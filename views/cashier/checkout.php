<?php
require_once '../../includes/db.php';
require_once '../../includes/notifications.php';
requireRole('cashier');

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) redirect('dashboard.php');

$subtotal = 0;
foreach($cart as $item) $subtotal += $item['price'] * $item['qty'];
$tax = $subtotal * 0.05;
$grand_total = $subtotal + $tax;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process Payment & Order
    $payment_method = $_POST['payment_method'];
    $amount_given = (float)$_POST['amount_given'];
    $change = $amount_given - $grand_total;
    
    // Save Order
    $stmt = $db->query("INSERT INTO orders (shop_id, cashier_id, total_amount, tax_amount, grand_total, payment_method) VALUES (?, ?, ?, ?, ?, ?)",
        [$_SESSION['shop_id'], $_SESSION['user_id'], $subtotal, $tax, $grand_total, $payment_method], "iiddds");
    $order_id = $db->getLastId();
    
    // Save Items & Update Stock
    foreach($cart as $item) {
        $line_total = $item['price'] * $item['qty'];
        $db->query("INSERT INTO order_items (order_id, product_id, quantity, unit_price, subtotal) VALUES (?, ?, ?, ?, ?)",
            [$order_id, $item['id'], $item['qty'], $item['price'], $line_total], "ididd");
            
        // Decrease Stock
        $db->query("UPDATE products SET stock_qty = stock_qty - ? WHERE id = ?", [$item['qty'], $item['id']], "ii");
        
        checkStock($item['id']);
    }
    
    // Clear Cart
    $_SESSION['cart'] = [];
    
    redirect("receipt.php?id=$order_id&change=$change");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div style="display: flex; justify-content: center; align-items: center; height: 100vh;">
        <div class="auth-container" style="max-width: 500px;">
            <h2 style="text-align: center; margin-bottom: 2rem;">Payment</h2>
            <div style="margin-bottom: 2rem; text-align: center;">
                <div style="color: var(--text-gray);">Total Amount Due</div>
                <div style="font-size: 3rem; font-weight: 800; color: #34d399;">$<?php echo number_format($grand_total, 2); ?></div>
            </div>

            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Payment Method</label>
                    <select name="payment_method" class="form-input">
                        <option value="Cash">Cash</option>
                        <option value="Card">Card</option>
                        <option value="Mobile">Mobile Banking</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Amount Given</label>
                    <input type="number" step="0.01" name="amount_given" class="form-input" required min="<?php echo $grand_total; ?>">
                </div>
                
                <div style="display: flex; gap: 1rem;">
                    <a href="dashboard.php" style="flex: 1; padding: 0.75rem; text-align: center; color: var(--text-gray); text-decoration: none;">Cancel</a>
                    <button type="submit" class="btn-primary" style="flex: 2;">Confirm Payment</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
