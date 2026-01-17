<?php
require_once __DIR__ . '/../includes/db.php';
error_reporting(E_ALL); 
ini_set('display_errors', 0);
header('Content-Type: application/json');

function logDebug($msg) {
    file_put_contents(__DIR__ . '/debug_return.log', date('Y-m-d H:i:s') . " - " . $msg . "\n", FILE_APPEND);
}

logDebug("Script Started");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'cashier') {
    logDebug("Unauthorized access attempt: " . print_r($_SESSION, true));
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$shop_id = $_SESSION['shop_id'];
$order_id = $_POST['order_id'];
$items = $_POST['items'] ?? [];

if (!$order_id || empty($items)) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}


try {
    $db->conn->begin_transaction();
    $total_refund = 0;
    foreach ($items as $prod_id => $qty) {
        $qty = (int)$qty;
        if ($qty <= 0) continue;

        $stmt = $db->query("SELECT * FROM order_items WHERE order_id = ? AND product_id = ?", [$order_id, $prod_id], "ii");
        $order_item = $stmt->get_result()->fetch_assoc();

        if (!$order_item) {
            throw new Exception("Product $prod_id not found in order");
        }

        if ($qty > $order_item['quantity']) {
             throw new Exception("Return quantity can be greater than sold quantity");
        }

        
        $refund_amount = $order_item['unit_price'] * $qty;
        $db->query("INSERT INTO returns (shop_id, order_id, product_id, quantity, refund_amount, reason) VALUES (?, ?, ?, ?, ?, 'Customer Return')", 
                   [$shop_id, $order_id, $prod_id, $qty, $refund_amount], "iiiid");

        $db->query("UPDATE products SET stock_qty = stock_qty + ? WHERE id = ?", [$qty, $prod_id], "ii");

        $new_item_qty = $order_item['quantity'] - $qty;
        $new_item_subtotal = $order_item['subtotal'] - $refund_amount;
        $db->query("UPDATE order_items SET quantity = ?, subtotal = ? WHERE id = ?", 
                   [$new_item_qty, $new_item_subtotal, $order_item['id']], "idi");

        $total_refund += $refund_amount;
    }

    if(isset($total_refund) && $total_refund > 0) {
        $db->query("UPDATE orders SET total_amount = total_amount - ?, grand_total = grand_total - ? WHERE id = ?", 
                   [$total_refund, $total_refund, $order_id], "ddi");
    }

    $db->conn->commit();
    logDebug("Transaction Committed");
    $resp = json_encode(['success' => true]);
    if ($resp === false) logDebug("JSON Encode Error: " . json_last_error_msg());
    echo $resp;
} catch (Throwable $e) {
    if (isset($db->conn)) $db->conn->rollback();
    logDebug("Exception/Error: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
