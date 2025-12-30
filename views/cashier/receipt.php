<?php
require_once '../../models/Order.php';
requireRole('cashier');

$order_id = $_GET['id'];
$change = $_GET['change'];
$orderModel = new Order();

$order = $orderModel->get($order_id, $_SESSION['shop_id']);
if(!$order) die("Order not found");

$items = $orderModel->getItems($order_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt #<?php echo $order_id; ?></title>
    <style>
        body { font-family: monospace; padding: 20px; max-width: 300px; margin: 0 auto; }
        .header { text-align: center; border-bottom: 1px dashed black; padding-bottom: 10px; margin-bottom: 10px; }
        .item { display: flex; justify-content: space-between; margin-bottom: 5px; }
        .total { border-top: 1px dashed black; padding-top: 10px; margin-top: 10px; }
        .btn { display: block; width: 100%; padding: 10px; background: black; color: white; text-align: center; text-decoration: none; margin-top: 20px; border-radius: 5px; }
        @media print { .btn { display: none; } }
    </style>
</head>
<body>
    <div class="header">
        <h2>POS System</h2>
        <p>Order #<?php echo $order_id; ?></p>
        <p><?php echo $order['created_at']; ?></p>
    </div>
    
    <div>
        <?php foreach($items as $item): ?>
        <div class="item">
            <span><?php echo $item['name']; ?> x<?php echo $item['quantity']; ?></span>
            <span>$<?php echo number_format($item['subtotal'], 2); ?></span>
        </div>
        <?php endforeach; ?>
    </div>
    
    <div class="total">
        <div class="item">
            <span>Subtotal</span>
            <span>$<?php echo number_format($order['total_amount'], 2); ?></span>
        </div>
        <div class="item">
            <span>Tax</span>
            <span>$<?php echo number_format($order['tax_amount'], 2); ?></span>
        </div>
        <div class="item" style="font-weight: bold; font-size: 1.2em;">
            <span>TOTAL</span>
            <span>$<?php echo number_format($order['grand_total'], 2); ?></span>
        </div>
        <div class="item">
            <span>Paid (<?php echo $order['payment_method']; ?>)</span>
            <span>Change: $<?php echo number_format($change, 2); ?></span>
        </div>
    </div>
    
    <button onclick="window.print()" class="btn">Print Receipt</button>
    <a href="dashboard.php" class="btn" style="background: transparent; color: black; border: 1px solid black;">New Sale</a>
</body>
</html>
