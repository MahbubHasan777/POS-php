<?php
require_once '../models/Product.php';
requireRole('cashier');

header('Content-Type: application/json');

$query = $_GET['q'] ?? '';
$shop_id = $_SESSION['shop_id'];

// Collect Filters
$filters = [
    'category_id' => $_GET['category_id'] ?? null,
    'brand_id' => $_GET['brand_id'] ?? null,
    'sort' => $_GET['sort'] ?? null
];

$productModel = new Product();
$results = $productModel->searchForPos($shop_id, $query, $filters);

echo json_encode($results);
?>
