<?php
require_once '../../includes/db.php';
requireRole('super_admin');

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['suspend_shop'])) {
        $shopStartId = $_POST['shop_id'];
        $db->query("UPDATE shops SET status = 'suspended' WHERE id = ?", [$shopStartId], "i");
    }
    if (isset($_POST['activate_shop'])) {
        $shopStartId = $_POST['shop_id'];
        $db->query("UPDATE shops SET status = 'active' WHERE id = ?", [$shopStartId], "i");
    }
}

// Filter Logic
$filter_status = $_GET['status'] ?? '';
$sql = "SELECT shops.*, subscription_plans.name as plan_name, users.email as owner_email 
        FROM shops 
        JOIN subscription_plans ON shops.subscription_plan_id = subscription_plans.id 
        LEFT JOIN users ON users.shop_id = shops.id AND users.role = 'shop_admin'";

if ($filter_status) {
    if ($filter_status === 'suspended') {
        $sql .= " WHERE shops.status = 'suspended'";
    } else {
        $sql .= " WHERE shops.status = 'active'";
    }
}

$shops = $db->query($sql);
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
                        <?php while($shop = $shops->get_result()->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $shop['id']; ?></td>
                            <td><?php echo htmlspecialchars($shop['name']); ?></td>
                            <td><?php echo htmlspecialchars($shop['owner_email']); ?></td>
                            <td><?php echo htmlspecialchars($shop['plan_name']); ?></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="shop_id" value="<?php echo $shop['id']; ?>">
                                    <?php if($shop['status'] === 'active'): ?>
                                        <button type="submit" name="suspend_shop" style="background:none; border:none; cursor:pointer; color: #f87171;">
                                            Suspend
                                        </button>
                                    <?php else: ?>
                                        <button type="submit" name="activate_shop" style="background:none; border:none; cursor:pointer; color: #34d399;">
                                            Activate
                                        </button>
                                    <?php endif; ?>
                                </form>
                            </td>
                            <td>
                                <button onclick="alert('Viewing payment history for ID <?php echo $shop['id']; ?>')" style="background: none; border: 1px solid var(--secondary); color: white; padding: 0.25rem 0.5rem; border-radius: 0.25rem; cursor: pointer;">History</button>
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
