<?php
require_once '../../models/SubscriptionPlan.php';
requireRole('super_admin');

$planModel = new SubscriptionPlan();
$plan = null;
$id = $_GET['id'] ?? null;

if ($id) {
    $plan = $planModel->get($id);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $max_sales = $_POST['max_sales'] === '' ? -1 : $_POST['max_sales'];

    if ($id) {
        $planModel->update($id, $name, $price, $max_sales);
        $message = "Plan updated successfully!";
    } else {
        $planModel->create($name, $price, $max_sales);
        $message = "Plan created successfully!";
    }
    
    redirect('plans.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $id ? 'Edit' : 'Create'; ?> Plan</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="dashboard-layout">
        <?php include '../includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div style="max-width: 600px; margin: 0 auto;">
                <h1><?php echo $id ? 'Edit' : 'Create New'; ?> Plan</h1>
                
                <form method="POST" class="auth-container" style="max-width: 100%; margin-top: 2rem;">
                    <div class="form-group">
                        <label class="form-label">Plan Name</label>
                        <input type="text" name="name" class="form-input" required value="<?php echo $plan['name'] ?? ''; ?>" placeholder="e.g. Enterprise Tier">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Price ($)</label>
                        <input type="number" step="0.01" name="price" class="form-input" required value="<?php echo $plan['price'] ?? '0.00'; ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Max Sales Order Limit (Per Month)</label>
                        <input type="number" name="max_sales" class="form-input" value="<?php echo isset($plan) && $plan['max_sales'] != -1 ? $plan['max_sales'] : ''; ?>" placeholder="Leave empty for Unlimited">
                        <small style="color: var(--text-gray);">Enter a number, or leave empty for Unlimited sales.</small>
                    </div>

                    <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                        <button type="submit" class="btn-primary">Save Plan</button>
                        <a href="plans.php" style="padding: 0.75rem; color: var(--text-gray); text-decoration: none;">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
