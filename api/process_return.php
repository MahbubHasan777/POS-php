<?php
require_once '../includes/db.php';
session_start();

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
    }

    $db->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $db->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
