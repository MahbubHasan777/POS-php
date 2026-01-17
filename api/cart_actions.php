<?php
require_once '../models/Product.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../includes/functions.php';

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

elseif ($action === 'apply_voucher') {
    require_once '../models/Coupon.php';
    $couponModel = new Coupon();
    $code = $_POST['code'];
    
    $coupon = $couponModel->query("SELECT * FROM coupons WHERE shop_id = ? AND code = ? AND expiry_date >= CURDATE()", [$shop_id, $code], "is")->get_result()->fetch_assoc();
    
    $cart_total = 0;
    foreach ($_SESSION['cart'] as $item) $cart_total += $item['price'] * $item['qty'];

    if (!$coupon) {
        echo json_encode(['success' => false, 'message' => 'Invalid or Expired Coupon']);
        exit;
    }
    
    if ($cart_total < $coupon['min_order_amount']) {
        echo json_encode(['success' => false, 'message' => "Order must be at least $" . $coupon['min_order_amount']]);
        exit;
    }

    $discount_amount = 0;
    if ($coupon['discount_type'] === 'fixed') {
        $discount_amount = $coupon['discount_value'];
    } else {
        $discount_amount = ($cart_total * $coupon['discount_value']) / 100;
        if ($coupon['max_discount_amount'] > 0) {
            $discount_amount = min($discount_amount, $coupon['max_discount_amount']);
        }
    }
    
    $_SESSION['discount'] = [
        'code' => $code,
        'amount' => $discount_amount
    ];
    
    echo json_encode([
        'success' => true,
        'discount' => $_SESSION['discount'],
        'cart' => $_SESSION['cart']
    ]);
    exit;

} elseif ($action === 'remove_voucher') {
    unset($_SESSION['discount']);
    echo json_encode([
        'success' => true,
        'discount' => null,
        'cart' => $_SESSION['cart']
    ]);
    exit;

} elseif ($action === 'hold') {
    if (!isset($_SESSION['held_orders'])) $_SESSION['held_orders'] = [];
    
    $cart = $_SESSION['cart'];
    if (empty($cart)) {
        echo json_encode(['success' => false, 'message' => 'Cart is empty', 'cart' => []]);
        exit;
    }

    $customer = $_POST['customer'] ?? 'Walk-in';
    $held_id = time(); // Simple ID based on timestamp
    
    $_SESSION['held_orders'][] = [
        'id' => $held_id,
        'customer_name' => $customer,
        'cart' => $cart,
        'items_count' => count($cart),
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    $_SESSION['cart'] = []; // Clear current cart
    echo json_encode(['success' => true, 'cart' => []]);
    exit;

} elseif ($action === 'list_held') {
    $held = $_SESSION['held_orders'] ?? [];
    echo json_encode(['held' => array_values($held)]);
    exit;

} elseif ($action === 'recall') {
    $id = $_POST['id'];
    $held_orders = $_SESSION['held_orders'] ?? [];
    $found = false;
    
    foreach ($held_orders as $key => $order) {
        if ($order['id'] == $id) {
            $_SESSION['cart'] = $order['cart']; 
            unset($_SESSION['held_orders'][$key]);
            $found = true;
            break;
        }
    }

    $_SESSION['held_orders'] = array_values($_SESSION['held_orders']);
    
    echo json_encode([
        'success' => $found, 
        'cart' => $_SESSION['cart'],
        'discount' => $_SESSION['discount'] ?? null
    ]);
    exit;
}

$discount = $_SESSION['discount'] ?? null;
echo json_encode(['cart' => $_SESSION['cart'], 'discount' => $discount]);
?>
