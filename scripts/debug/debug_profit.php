<?php
require_once '../../includes/db.php';
header('Content-Type: application/json');

$response = [];

// 1. Check Products
$res = $db->query("SELECT id, name, buy_price, sell_price FROM products LIMIT 5");
$response['products'] = $res->get_result()->fetch_all(MYSQLI_ASSOC);

// 2. Check Order Items
$res = $db->query("SELECT * FROM order_items LIMIT 5");
$response['order_items'] = $res->get_result()->fetch_all(MYSQLI_ASSOC);

// 3. Profit Query Test
$shop_id = $_SESSION['shop_id'] ?? 2; 
if(isset($_GET['shop_id'])) $shop_id = $_GET['shop_id'];

$sql = "SELECT o.id, 
        SUM((oi.unit_price - p.buy_price) * oi.quantity) as profit,
        o.total_amount
        FROM orders o 
        LEFT JOIN order_items oi ON o.id = oi.order_id 
        LEFT JOIN products p ON oi.product_id = p.id 
        WHERE o.shop_id = ? 
        GROUP BY o.id 
        ORDER BY o.created_at DESC 
        LIMIT 5";

$stmt = $db->query($sql, [$shop_id], "i");
$response['profit_query'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

echo json_encode($response, JSON_PRETTY_PRINT);
?>
