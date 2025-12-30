<?php
require_once '../models/Product.php';
// Session start is usually handled in db.php but let's be safe
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../includes/functions.php'; // Ensure helpers

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$action = $_POST['action'] ?? '';
$shop_id = $_SESSION['shop_id'] ?? 0;
$productModel = new Product();

if ($action === 'add') {
    $id = $_POST['id'];
    $product = $productModel->get($id, $shop_id);

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
    $_SESSION['cart'] = array_values($_SESSION['cart']); 
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
}

echo json_encode(['cart' => $_SESSION['cart']]);
?>
