<?php
require_once '../includes/db.php';
// session_start(); // Handled in db.php

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$action = $_POST['action'] ?? '';
$shop_id = $_SESSION['shop_id'] ?? 0;

if ($action === 'add') {
    $id = $_POST['id'];
    $qty = 1;

    // Check stock
    $stmt = $db->query("SELECT * FROM products WHERE id = ? AND shop_id = ?", [$id, $shop_id], "ii");
    $product = $stmt->get_result()->fetch_assoc();

    if ($product && $product['stock_qty'] > 0) {
        $found = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id'] == $id) {
                if ($item['qty'] < $product['stock_qty']) {
                    $item['qty']++;
                }
                $found = true;
                break;
            }
        }
        if (!$found) {
            $_SESSION['cart'][] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['sell_price'],
                'qty' => 1,
                'max' => $product['stock_qty']
            ];
        }
    }
} elseif ($action === 'update_qty') {
    $id = $_POST['id'];
    $qty = (int)$_POST['qty'];
    
    foreach ($_SESSION['cart'] as $key => &$item) {
        if ($item['id'] == $id) {
            if ($qty <= 0) {
                unset($_SESSION['cart'][$key]);
            } else {
                if ($qty <= $item['max']) {
                    $item['qty'] = $qty;
                }
            }
            break;
        }
    }
    $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindex
} elseif ($action === 'remove') {
    $id = $_POST['id'];
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $id) {
            unset($_SESSION['cart'][$key]);
            break;
        }
    }
    $_SESSION['cart'] = array_values($_SESSION['cart']);
} elseif ($action === 'clear') {
    $_SESSION['cart'] = [];
} elseif ($action === 'hold') {
    $customer = $_POST['customer'] ?? 'Walk-in';
    if (!empty($_SESSION['cart'])) {
        $cart_json = json_encode($_SESSION['cart']);
        $cashier_id = $_SESSION['user_id'];
        $db->query("INSERT INTO held_orders (shop_id, cashier_id, items_json, customer_name) VALUES (?, ?, ?, ?)", 
                   [$shop_id, $cashier_id, $cart_json, $customer], "iiss");
        $_SESSION['cart'] = [];
    }
} elseif ($action === 'list_held') {
    $res = $db->query("SELECT * FROM held_orders WHERE shop_id = ? ORDER BY created_at DESC", [$shop_id], "i");
    $held = [];
    $result = $res->get_result();
    while($row = $result->fetch_assoc()) {
        $items = json_decode($row['items_json'], true);
        $row['items_count'] = is_array($items) ? count($items) : 0;
        $held[] = $row;
    }
    echo json_encode(['held' => $held]);
    exit;
} elseif ($action === 'recall') {
    $id = $_POST['id'];
    $res = $db->query("SELECT * FROM held_orders WHERE id = ? AND shop_id = ?", [$id, $shop_id], "ii");
    $result = $res->get_result();
    $order = $result->fetch_assoc();
    
    if ($order) {
        $recovered_cart = json_decode($order['items_json'], true);
        if (is_array($recovered_cart)) {
            $_SESSION['cart'] = $recovered_cart;
            $db->query("DELETE FROM held_orders WHERE id = ?", [$id], "i");
        }
    }
}

echo json_encode(['cart' => $_SESSION['cart']]);
?>
