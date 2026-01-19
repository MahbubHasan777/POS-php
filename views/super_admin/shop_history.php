<?php
require_once '../../models/Shop.php';
requireRole('super_admin');

if (!isset($_GET['shop_id'])) {
    redirect('shops.php');
}

$shop_id = $_GET['shop_id'];
$shopModel = new Shop();
$history = $shopModel->getPaymentHistory($shop_id);

// Get shop basic info if needed, or query again
// For title
$shop_name = "Shop #" . $shop_id;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Payment History - Super Admin</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>
    <div class="dashboard-layout">
        <?php include '../includes/sidebar.php'; ?>

        <div class="main-content">
            <div style="margin-bottom: 2rem;">
                <a href="shops.php" style="color: var(--text-gray); text-decoration: none;">&larr; Back to Shops</a>
            </div>
            <h1>Payment History for <?php echo htmlspecialchars($shop_name); ?></h1>

            <div class="table-container" style="margin-top: 2rem;">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Plan</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Method</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($history->num_rows > 0): ?>
                            <?php while ($row = $history->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo date('M d, Y H:i', strtotime($row['payment_date'])); ?></td>
                                    <td><?php echo htmlspecialchars($row['plan_name']); ?></td>
                                    <td>৳<?php echo number_format($row['amount'], 2); ?></td>
                                    <td>
                                        <span
                                            style="padding: 0.25rem 0.5rem; border-radius: 0.25rem; background: <?php echo $row['payment_status'] == 'paid' ? 'rgba(16, 185, 129, 0.2)' : 'rgba(239, 68, 68, 0.2)'; ?>; color: <?php echo $row['payment_status'] == 'paid' ? '#34d399' : '#f87171'; ?>">
                                            <?php echo ucfirst($row['payment_status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-gray); padding: 2rem;">No
                                    payment history found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>