<?php
require_once 'includes/db.php';
header('Content-Type: application/json');

$response = [];

// 1. Describe Orders
$res = $db->query("SHOW COLUMNS FROM orders");
$response['orders_schema'] = $res->get_result()->fetch_all(MYSQLI_ASSOC);

// 2. Users Sample
$res = $db->query("SELECT id, username, full_name, role FROM users LIMIT 5");
$response['users'] = $res->get_result()->fetch_all(MYSQLI_ASSOC);

echo json_encode($response, JSON_PRETTY_PRINT);
?>
