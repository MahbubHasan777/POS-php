<?php
require_once '../models/Order.php';
require_once '../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) session_start();
requireRole('shop_admin'); // Ensure only admin can view

$orderModel = new Order();
$shop_id = $_SESSION['shop_id'];
$order_id = $_GET['id'] ?? 0;

$items = $orderModel->getItems($order_id);
// Security: Verify order belongs to shop (implicitly handled if getting items fails? No, better check)
$order = $orderModel->get($order_id, $shop_id);

if (!$order) {
    echo json_encode(['success' => false, 'message' => 'Order not found']);
    exit;
}

echo json_encode(['success' => true, 'items' => $items]);
?>
