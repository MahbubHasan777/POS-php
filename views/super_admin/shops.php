<?php
require_once '../../models/Shop.php';
requireRole('super_admin');
$shopModel = new Shop();

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['suspend_shop'])) {
        $shopModel->updateStatus($_POST['shop_id'], 'suspended');
    }
    if (isset($_POST['activate_shop'])) {
        $shopModel->updateStatus($_POST['shop_id'], 'active');
    }
}

// Filter Logic
$filter_status = $_GET['status'] ?? null;
$shops = $shopModel->getAll($filter_status);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Shops - Super Admin</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="dashboard-layout">
        <?php include '../includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <h1>Shop Management</h1>
                <div>
                    <a href="shops.php" class="btn-primary" style="text-decoration:none; background: <?php echo $filter_status == '' ? 'var(--primary-dark)' : 'var(--bg-card)'; ?>">All</a>
                    <a href="shops.php?status=active" class="btn-primary" style="text-decoration:none; background: <?php echo $filter_status == 'active' ? 'var(--primary-dark)' : 'var(--bg-card)'; ?>">Active</a>
                    <a href="shops.php?status=suspended" class="btn-primary" style="text-decoration:none; background: <?php echo $filter_status == 'suspended' ? 'var(--primary-dark)' : 'var(--bg-card)'; ?>">Suspended</a>
                </div>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Shop Name</th>
                            <th>Owner Email</th>
                            <th>Plan</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        while($shop = $shops->fetch_assoc()): 
                        ?>
                        <tr>
                            <td>#<?php echo $shop['id']; ?></td>
                            <td><?php echo htmlspecialchars($shop['name']); ?></td>
                            <td><?php echo htmlspecialchars($shop['owner_email']); ?></td>
                            <td><?php echo htmlspecialchars($shop['plan_name']); ?></td>
                            <td>
                                <span style="padding: 0.25rem 0.5rem; border-radius: 0.25rem; background: <?php echo $shop['status']=='active'?'rgba(16, 185, 129, 0.2)':'rgba(239, 68, 68, 0.2)'; ?>; color: <?php echo $shop['status']=='active'?'#34d399':'#f87171'; ?>">
                                    <?php echo ucfirst($shop['status']); ?>
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.5rem; align-items: center;">
                                    <form method="POST" style="margin:0;">
                                        <input type="hidden" name="shop_id" value="<?php echo $shop['id']; ?>">
                                        <?php if($shop['status'] === 'active'): ?>
                                            <button type="submit" name="suspend_shop" class="btn-primary" style="background: #ef4444; padding: 0.25rem 0.5rem; font-size: 0.875rem;">
                                                Suspend
                                            </button>
                                        <?php else: ?>
                                            <button type="submit" name="activate_shop" class="btn-primary" style="background: #10b981; padding: 0.25rem 0.5rem; font-size: 0.875rem;">
                                                Activate
                                            </button>
                                        <?php endif; ?>
                                    </form>
                                    <a href="shop_history.php?shop_id=<?php echo $shop['id']; ?>" class="btn-primary" style="background: none; border: 1px solid var(--secondary); color: white; padding: 0.25rem 0.5rem; font-size: 0.875rem; text-decoration: none; display: inline-block;">History</a>
                                </div>
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
