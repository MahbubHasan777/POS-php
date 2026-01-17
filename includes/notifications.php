<?php
function createNotification($user_id, $title, $message) {
    global $db;
    $db->query("INSERT INTO notifications (user_id, title, message) VALUES (?, ?, ?)", [$user_id, $title, $message], "iss");
}

function checkStock($product_id) {
    global $db;
    $stmt = $db->query("SELECT p.*, s.id as shop_id FROM products p JOIN shops s ON p.shop_id = s.id WHERE p.id = ?", [$product_id], "i");
    $product = $stmt->get_result()->fetch_assoc();

    if ($product && $product['stock_qty'] <= $product['alert_threshold']) {
        $shop_id = $product['shop_id'];
        $stmt_admin = $db->query("SELECT id FROM users WHERE shop_id = ? AND role = 'shop_admin' LIMIT 1", [$shop_id], "i");
        $admin = $stmt_admin->get_result()->fetch_assoc();
        
        if ($admin) {
            $title = "Low Stock Alert: " . $product['name'];
            $msg = "The stock for {$product['name']} is low ({$product['stock_qty']} left). Please restock.";
            
            createNotification($admin['id'], $title, $msg);
        }
    }
}
?>
