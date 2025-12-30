<?php
require_once '../../models/Core.php'; 
// Ideally we'd have a ShopController or ReportController to aggregate stats. 
// For now, using direct queries via Core or specialized Model methods makes sense.
// Let's assume we use Core for ad-hoc dashboard stats to keep it clean if specific model methods don't exist yet.
requireSameRole: require_once '../../includes/functions.php'; // Ensure functions loaded
requireRole('shop_admin');
$shop_id = $_SESSION['shop_id'];
$core = new Core();

// Today's Sales
$today = date('Y-m-d');
$sales_stmt = $core->query("SELECT SUM(grand_total) as total FROM orders WHERE shop_id = ? AND DATE(created_at) = ?", [$shop_id, $today], "is");
$todays_sales = $sales_stmt->get_result()->fetch_assoc()['total'] ?? 0.00;

// Low Stock Count
$low_stock_stmt = $core->query("SELECT COUNT(*) as count FROM products WHERE shop_id = ? AND stock_qty <= alert_threshold", [$shop_id], "i");
$low_stock_count = $low_stock_stmt->get_result()->fetch_assoc()['count'];

// Total Products
$prod_stmt = $core->query("SELECT COUNT(*) as count FROM products WHERE shop_id = ?", [$shop_id], "i");
$total_products = $prod_stmt->get_result()->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shop Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="dashboard-layout">
        <?php include '../includes/sidebar.php'; ?>
        
        <div class="main-content">
            <h1>Shop Overview</h1>
            <p style="color: var(--text-gray); margin-bottom: 2rem;">Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?></p>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Today's Sales</h3>
                    <div class="stat-value">$<?php echo number_format($todays_sales, 2); ?></div>
                </div>
                <div class="stat-card">
                    <h3 style="color: #f87171;">Low Stock Alerts</h3>
                    <div class="stat-value" style="color: #f87171;"><?php echo $low_stock_count; ?></div>
                    <?php if($low_stock_count > 0): ?>
                        <a href="products.php?filter=low_stock" style="font-size: 0.875rem; color: var(--text-gray);">View Items &rarr;</a>
                    <?php endif; ?>
                </div>
                <div class="stat-card">
                    <h3>Total Products</h3>
                    <div class="stat-value"><?php echo $total_products; ?></div>
                </div>
            </div>

            <h2 style="margin-top: 2rem;">Recent Sales</h2>
            <div class="table-container">
                 <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Time</th>
                            <th>Total</th>
                            <th>Payment</th>
                            <th>Cashier</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        // Execute query
                        $recent_orders = $core->query("SELECT orders.*, users.username as cashier_name FROM orders 
                                                     JOIN users ON orders.cashier_id = users.id 
                                                     WHERE orders.shop_id = ? 
                                                     ORDER BY created_at DESC LIMIT 5", [$shop_id], "i");
                        
                        $res = $recent_orders->get_result(); // Fetch result set once immediately

                        if ($res && $res->num_rows > 0):
                            while($order = $res->fetch_assoc()):
                        ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td><?php echo date('H:i', strtotime($order['created_at'])); ?></td>
                            <td>$<?php echo number_format($order['grand_total'], 2); ?></td>
                            <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
                            <td><?php echo htmlspecialchars($order['cashier_name']); ?></td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr><td colspan="5" style="text-align:center;">No recent sales found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                 </table>
            </div>
        </div>
    </div>
</body>
</html>
