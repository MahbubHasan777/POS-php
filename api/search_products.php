<?php
require_once '../includes/db.php';
requireRole('cashier');

header('Content-Type: application/json');

$query = $_GET['q'] ?? '';
$shop_id = $_SESSION['shop_id'];

if (strlen($query) < 1) {
    echo json_encode([]);
    exit;
}

// Search by Name or ID
$sql = "SELECT id, name, sell_price, stock_qty, image FROM products 
        WHERE shop_id = ? AND (name LIKE ? OR id = ?) AND stock_qty > 0 LIMIT 10";
$term = "%$query%";
$stmt = $db->query($sql, [$shop_id, $term, $query], "iss");
$results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

echo json_encode($results);
?>
