<?php
require_once '../includes/db.php';
requireRole('cashier');

header('Content-Type: application/json');

$query = $_GET['q'] ?? '';
$shop_id = $_SESSION['shop_id'];

// Search by Name or ID
if (strlen($query) > 0) {
    $sql = "SELECT id, name, sell_price, stock_qty, image FROM products 
            WHERE shop_id = ? AND (name LIKE ? OR id = ?) AND stock_qty > 0 LIMIT 10";
    $term = "%$query%";
    $stmt = $db->query($sql, [$shop_id, $term, $query], "iss");
} else {
    // Default: Show recent products
    $sql = "SELECT id, name, sell_price, stock_qty, image FROM products 
            WHERE shop_id = ? AND stock_qty > 0 ORDER BY id DESC LIMIT 20";
    $stmt = $db->query($sql, [$shop_id], "i");
}

$results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

echo json_encode($results);
?>
