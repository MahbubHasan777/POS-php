<?php
// Debug View to check Session and DB content
require_once 'includes/db.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Simple CSS
echo '<style>body{font-family:sans-serif; background:#f0f0f0; padding:20px;} pre{background:#fff; padding:15px; border:1px solid #ccc; overflow:auto;} h2{margin-top:20px;}</style>';

echo "<h1>Debug Dashboard</h1>";

// 1. Check Session
echo "<h2>1. Session Data</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

$shop_id = $_SESSION['shop_id'] ?? 0;

// 2. Check Database Connection
echo "<h2>2. DB Data for Shop ID: $shop_id</h2>";

// Count Products
$stmt = $db->query("SELECT count(*) as c FROM products WHERE shop_id = ?", [$shop_id], "i");
$count = $stmt->get_result()->fetch_assoc()['c'];
echo "<p><strong>Products Count in Query:</strong> $count</p>";

// List Data
echo "<h3>Categories Table (Raw Data)</h3>";
echo "<pre>";
$cats = $db->query("SELECT * FROM categories");
$res = $cats->get_result();
if($res->num_rows == 0) echo "Table is empty!";
while($r = $res->fetch_assoc()) { print_r($r); }
echo "</pre>";

echo "<h3>Brands Table (Raw Data)</h3>";
echo "<pre>";
$brands = $db->query("SELECT * FROM brands");
$res = $brands->get_result();
if($res->num_rows == 0) echo "Table is empty!";
while($r = $res->fetch_assoc()) { print_r($r); }
echo "</pre>";

echo "<h3>Products Table (Raw Data)</h3>";
echo "<pre>";
$prods = $db->query("SELECT * FROM products");
$res = $prods->get_result();
if($res->num_rows == 0) echo "Table is empty!";
while($r = $res->fetch_assoc()) { print_r($r); }
echo "</pre>";
?>
