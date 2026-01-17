<?php
require_once '../../models/Shop.php';
requireRole('shop_admin');

$shopModel = new Shop();
$plans = $shopModel->query("SELECT * FROM subscription_plans WHERE price > 0")->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upgrade Subscription</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        .plan-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin-top: 2rem; }
        .plan-card { background: var(--bg-card); padding: 2rem; border-radius: 1rem; text-align: center; border: 1px solid rgba(255,255,255,0.1); transition: transform 0.2s; }
        .plan-card:hover { transform: translateY(-5px); border-color: var(--primary); }
        .price { font-size: 2.5rem; font-weight: 800; margin: 1rem 0; color: var(--primary); }
        .features { list-style: none; padding: 0; margin: 2rem 0; color: var(--text-gray); }
        .features li { margin-bottom: 0.5rem; }
    </style>
</head>
<body>
    <div class="dashboard-layout">
        <?php include '../includes/sidebar.php'; ?>
        
        <div class="main-content">
            <h1>Choose Your Plan</h1>
            <p style="color: var(--text-gray);">Upgrade your plan to increase sales limits and unlock features.</p>

            <div class="plan-grid">
                <?php while($plan = $plans->fetch_assoc()): ?>
                <div class="plan-card">
                    <h2 style="margin-bottom: 0.5rem;"><?php echo htmlspecialchars($plan['name']); ?></h2>
                    <div class="price">$<?php echo $plan['price']; ?></div>
                    <ul class="features">
                        <li>Sales Limit: <b><?php echo $plan['max_sales'] == -1 ? 'Unlimited' : $plan['max_sales']; ?></b> / month</li>
                        <li>24/7 Support</li>
                        <li>Admin Dashboard</li>
                    </ul>
                    <form action="ssl_payment.php" method="POST">
                        <input type="hidden" name="plan_id" value="<?php echo $plan['id']; ?>">
                        <input type="hidden" name="amount" value="<?php echo $plan['price']; ?>">
                        <button type="submit" class="btn-primary" style="width: 100%;">Choose Plan</button>
                    </form>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</body>
</html>
