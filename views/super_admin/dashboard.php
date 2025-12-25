<?php
require_once '../../includes/db.php';
requireRole('super_admin');

// Fetch Stats
$shops_count = $db->query("SELECT COUNT(*) as count FROM shops")->get_result()->fetch_assoc()['count'];
$total_revenue = $db->query("SELECT SUM(price) as total FROM subscription_plans 
                            JOIN shops ON shops.subscription_plan_id = subscription_plans.id")->get_result()->fetch_assoc()['total'] ?? 0;
// Assuming revenue comes from subscriptions for Super Admin. 
// If revenue means percentage of sales, we'd calculate differently. 
// For SaaS, usually it's sub fees.
// Let's also count total users.
$users_count = $db->query("SELECT COUNT(*) as count FROM users")->get_result()->fetch_assoc()['count'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Super Admin Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="dashboard-layout">
        <?php include '../includes/sidebar.php'; ?>
        
        <div class="main-content">
            <h1 style="margin-bottom: 2rem;">Platform Overview</h1>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Active Shops</h3>
                    <div class="stat-value"><?php echo $shops_count; ?></div>
                </div>
                <div class="stat-card">
                    <h3>Total Revenue (Subscriptions)</h3>
                    <div class="stat-value">$<?php echo number_format($total_revenue, 2); ?></div>
                </div>
                <div class="stat-card">
                    <h3>Total Users</h3>
                    <div class="stat-value"><?php echo $users_count; ?></div>
                </div>
            </div>

            <h2 style="margin-bottom: 1rem; margin-top: 2rem;">Recent Shop Registrations</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Shop Name</th>
                            <th>Plan</th>
                            <th>Status</th>
                            <th>Joined Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $recent_shops = $db->query("SELECT shops.*, subscription_plans.name as plan_name 
                                                    FROM shops 
                                                    JOIN subscription_plans ON shops.subscription_plan_id = subscription_plans.id 
                                                    ORDER BY created_at DESC LIMIT 5");
                        while($shop = $recent_shops->get_result()->fetch_assoc()):
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($shop['name']); ?></td>
                            <td><?php echo htmlspecialchars($shop['plan_name']); ?></td>
                            <td>
                                <span style="padding: 0.25rem 0.5rem; border-radius: 0.25rem; background: <?php echo $shop['status']=='active'?'rgba(16, 185, 129, 0.2)':'rgba(239, 68, 68, 0.2)'; ?>; color: <?php echo $shop['status']=='active'?'#34d399':'#f87171'; ?>">
                                    <?php echo ucfirst($shop['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($shop['created_at'])); ?></td>
                            <td>
                                <a href="shops.php?edit=<?php echo $shop['id']; ?>" style="color: var(--primary); text-decoration: none;">Manage</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
