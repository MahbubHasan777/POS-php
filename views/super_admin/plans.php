<?php
require_once '../../models/SubscriptionPlan.php';
requireRole('super_admin');

$planModel = new SubscriptionPlan();

// Handle Delete
if (isset($_POST['delete_id'])) {
    if ($planModel->delete($_POST['delete_id'])) {
        $success = "Plan deleted successfully.";
    } else {
        $error = "Cannot delete plan. It is currently assigned to one or more shops.";
    }
}

$plans = $planModel->getAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Subscription Plans - Super Admin</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>
    <div class="dashboard-layout">
        <?php include '../includes/sidebar.php'; ?>

        <div class="main-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <h1>Subscription Plans</h1>
                <a href="plan_form.php" class="btn-primary" style="text-decoration: none; width: auto;">+ Create New
                    Plan</a>
            </div>

            <?php if (isset($error)): ?>
                <div
                    style="background: #fef2f2; color: #b91c1c; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem; border: 1px solid #fecaca;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($success)): ?>
                <div
                    style="background: #ecfdf5; color: #047857; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem; border: 1px solid #a7f3d0;">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Plan Name</th>
                            <th>Price</th>
                            <th>Max Sales / Mo</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($plan = $plans->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $plan['id']; ?></td>
                                <td><?php echo htmlspecialchars($plan['name']); ?></td>
                                <td>৳<?php echo number_format($plan['price'], 2); ?></td>
                                <td><?php echo $plan['max_sales'] == -1 ? "Unlimited" : $plan['max_sales']; ?></td>
                                <td>
                                    <a href="plan_form.php?id=<?php echo $plan['id']; ?>"
                                        style="color: var(--primary); margin-right: 1rem; text-decoration: none;">Edit</a>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?');">
                                        <input type="hidden" name="delete_id" value="<?php echo $plan['id']; ?>">
                                        <button type="submit"
                                            style="background: none; border: none; color: #ef4444; cursor: pointer;">Delete</button>
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