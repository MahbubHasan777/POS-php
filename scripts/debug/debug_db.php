<?php
require_once '../../includes/db.php';

echo "=== USERS ===\n";
$users = $db->query("SELECT id, username, role, shop_id FROM users");
while($u = $users->get_result()->fetch_assoc()) {
    print_r($u);
}

echo "\n=== SHOPS ===\n";
$shops = $db->query("SELECT * FROM shops");
while($s = $shops->get_result()->fetch_assoc()) {
    print_r($s);
}

echo "\n=== PRODUCTS ===\n";
$products = $db->query("SELECT id, shop_id, name, category_id, brand_id FROM products");
while($p = $products->get_result()->fetch_assoc()) {
    print_r($p);
}
?>
