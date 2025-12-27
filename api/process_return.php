<?php
require_once __DIR__ . '/../includes/db.php';
// session_start(); // Handled in db.php
error_reporting(0);
header('Content-Type: application/json');

// Basic auth check inline for API
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'cashier') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$shop_id = $_SESSION['shop_id'];
$order_id = $_POST['order_id'];
$items = $_POST['items'] ?? []; // format: [product_id => qty]

if (!$order_id || empty($items)) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

$db->begin_transaction();

try {
    $total_refund = 0;
    foreach ($items as $prod_id => $qty) {
        $qty = (int)$qty;
        if ($qty <= 0) continue;

        // Verify item was in order
        $stmt = $db->query("SELECT * FROM order_items WHERE order_id = ? AND product_id = ?", [$order_id, $prod_id], "ii");
        $order_item = $stmt->get_result()->fetch_assoc();

        if (!$order_item) {
            throw new Exception("Product $prod_id not found in order");
        }

        if ($qty > $order_item['quantity']) {
             throw new Exception("Return quantity can be greater than sold quantity");
        }

        // Check if already returned? (Skipping for now as per simple requirement, but ideally should check)
        
        $refund_amount = $order_item['unit_price'] * $qty;

        // Insert Return
        $db->query("INSERT INTO returns (shop_id, order_id, product_id, quantity, refund_amount, reason) VALUES (?, ?, ?, ?, ?, 'Customer Return')", 
                   [$shop_id, $order_id, $prod_id, $qty, $refund_amount], "iiiid");

        // Update Stock (Increase)
        $db->query("UPDATE products SET stock_qty = stock_qty + ? WHERE id = ?", [$qty, $prod_id], "ii");

        // Update Order Item (Reduce Qty & Subtotal)
        $new_item_qty = $order_item['quantity'] - $qty;
        $new_item_subtotal = $order_item['subtotal'] - $refund_amount;
        $db->query("UPDATE order_items SET quantity = ?, subtotal = ? WHERE id = ?", 
                   [$new_item_qty, $new_item_subtotal, $order_item['id']], "idi");

        // Update Order (Reduce Total & Grand Total)
        // We do this inside loop, but efficient way is to sum up and do once. 
        // For simplicity/safety in transaction, we do it iteratively or sum up.
        // Let's sum up to avoid multiple writes to same order row.
        $total_refund += $refund_amount;
    }

    if(isset($total_refund) && $total_refund > 0) {
        $db->query("UPDATE orders SET total_amount = total_amount - ?, grand_total = grand_total - ? WHERE id = ?", 
                   [$total_refund, $total_refund, $order_id], "ddi");
    }

    $db->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $db->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
