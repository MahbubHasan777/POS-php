<?php
require_once '../../includes/db.php';
requireRole('super_admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_plan'])) {
        $name = $_POST['name'];
        $price = $_POST['price'];
        $max_sales = $_POST['max_sales'];
        $db->query("INSERT INTO subscription_plans (name, price, max_sales) VALUES (?, ?, ?)", [$name, $price, $max_sales], "sdi");
    }
    if (isset($_POST['delete_plan'])) {
        $id = $_POST['id'];
        $db->query("DELETE FROM subscription_plans WHERE id = ?", [$id], "i");
    }
}

$plans = $db->query("SELECT * FROM subscription_plans");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Plans</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="dashboard-layout">
        <?php include '../includes/sidebar.php'; ?>
        
        <div class="main-content">
            <h1>Subscription Plans</h1>
            
            <div style="background: var(--bg-card); padding: 1.5rem; border-radius: 0.5rem; margin-bottom: 2rem;">
                <h3>Add New Plan</h3>
                <form method="POST" style="margin-top: 1rem; display: flex; gap: 1rem; list-style: none; align-items: flex-end;">
                    <div>
                        <label class="form-label">Plan Name</label>
                        <input type="text" name="name" class="form-input" required>
                    </div>
                    <div>
                        <label class="form-label">Price ($)</label>
                        <input type="number" step="0.01" name="price" class="form-input" required>
                    </div>
                    <div>
                        <label class="form-label">Max Sales (-1 for Unlimited)</label>
                        <input type="number" name="max_sales" class="form-input" required value="-1">
                    </div>
                    <button type="submit" name="add_plan" class="btn-primary" style="margin-bottom: 2px;">Create Plan</button>
                </form>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Plan Name</th>
                            <th>Price</th>
                            <th>Max Sales</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($plan = $plans->get_result()->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($plan['name']); ?></td>
                            <td>$<?php echo number_format($plan['price'], 2); ?></td>
                            <td><?php echo $plan['max_sales'] == -1 ? 'Unlimited' : $plan['max_sales']; ?></td>
                            <td>
                                <form method="POST" onsubmit="return confirm('Delete this plan?');">
                                    <input type="hidden" name="id" value="<?php echo $plan['id']; ?>">
                                    <button type="submit" name="delete_plan" style="color: #ef4444; background: none; border: none; cursor: pointer;">Delete</button>
                                </form>
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
