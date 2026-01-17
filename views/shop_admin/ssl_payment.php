<?php
require_once '../../models/Shop.php';
requireRole('shop_admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('payment.php');
}

$plan_id = $_POST['plan_id'];
$amount = $_POST['amount'];
$shop_id = $_SESSION['shop_id'];

if (isset($_POST['confirm_payment'])) {
    $shopModel = new Shop();
    $shopModel->renewSubscription($shop_id, $plan_id, $amount, 'SSLCommerz Sandbox');
    
    $_SESSION['success'] = "Payment Successful! Plan Upgraded.";
    redirect('dashboard.php');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pay with SSLCommerz (Sandbox)</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div style="max-width: 500px; margin: 4rem auto; background: white; padding: 2rem; border-radius: 1rem; color: #333; text-align: center;">
        <h2 style="color: #333; margin-bottom: 1rem;">SSLCommerz Secure Payment</h2>
        <div style="background: #f3f4f6; padding: 1rem; border-radius: 0.5rem; margin-bottom: 2rem;">
            <p><strong>Merchant:</strong> POS Master</p>
            <p><strong>Amount:</strong> $<?php echo $amount; ?></p>
        </div>
        
        <form method="POST">
            <input type="hidden" name="plan_id" value="<?php echo $plan_id; ?>">
            <input type="hidden" name="amount" value="<?php echo $amount; ?>">
            
            <div style="margin-bottom: 1rem;">
                <label style="display:block; text-align:left; margin-bottom:0.5rem; font-weight:600;">Card Number (Test)</label>
                <input type="text" value="4444 4444 4444 4444" style="width:100%; padding:0.75rem; border:1px solid #ddd; border-radius:0.5rem;" readonly>
            </div>
            
            <button type="submit" name="confirm_payment" class="btn-primary" style="background: #007bff; width: 100%;">Pay Now</button>
            <a href="payment.php" style="display:block; margin-top:1rem; color: #666;">Cancel</a>
        </form>
        
        <p style="margin-top: 2rem; font-size: 0.8rem; color: #999;">This is a sandbox environment. No real money is charged.</p>
    </div>
</body>
</html>
