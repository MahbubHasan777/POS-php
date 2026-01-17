<?php
require_once '../../models/Coupon.php';
requireRole('shop_admin');
$shop_id = $_SESSION['shop_id'];
$couponModel = new Coupon();

$editMode = false;
$editCoupon = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_coupon'])) {
        $couponModel->create([
            'shop_id' => $shop_id,
            'code' => $_POST['code'],
            'type' => $_POST['discount_type'],
            'value' => $_POST['discount_value'],
            'min' => $_POST['min_order_amount'],
            'max' => $_POST['max_discount_amount'],
            'expiry' => $_POST['expiry_date']
        ]);
        redirect('coupons.php');
    } elseif (isset($_POST['update_coupon'])) {
        $couponModel->update($_POST['id'], [
            'code' => $_POST['code'],
            'type' => $_POST['discount_type'],
            'value' => $_POST['discount_value'],
            'min' => $_POST['min_order_amount'],
            'max' => $_POST['max_discount_amount'],
            'expiry' => $_POST['expiry_date']
        ]);
        redirect('coupons.php');
    }
}

if (isset($_GET['edit'])) {
    $editMode = true;
    $editCoupon = $couponModel->getById($_GET['edit']);
}

$coupons = $couponModel->getAll($shop_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Coupons</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="dashboard-layout">
        <?php include '../includes/sidebar.php'; ?>
        <div class="main-content">
            <h1>Coupon Management</h1>
            
            <form method="POST" autocomplete="off" style="background: var(--bg-card); padding: 1.5rem; border-radius: 0.5rem; margin-bottom: 2rem; display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                <?php if($editMode): ?>
                    <input type="hidden" name="id" value="<?php echo $editCoupon['id']; ?>">
                <?php endif; ?>
                <div>
                    <label class="form-label">Code</label>
                    <input type="text" name="code" class="form-input" required placeholder="SUMMER25" value="<?php echo $editMode ? htmlspecialchars($editCoupon['code']) : ''; ?>">
                </div>
                <div>
                    <label class="form-label">Type</label>
                    <select name="discount_type" class="form-input">
                        <option value="fixed" <?php echo ($editMode && $editCoupon['discount_type'] == 'fixed') ? 'selected' : ''; ?>>Fixed Amount (Taka)</option>
                        <option value="percent" <?php echo ($editMode && $editCoupon['discount_type'] == 'percent') ? 'selected' : ''; ?>>Percentage (%)</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Value</label>
                    <input type="number" step="0.01" name="discount_value" class="form-input" required value="<?php echo $editMode ? $editCoupon['discount_value'] : ''; ?>">
                </div>
                <div>
                    <label class="form-label">Min Order Amount</label>
                    <input type="number" step="0.01" name="min_order_amount" class="form-input" value="<?php echo $editMode ? $editCoupon['min_order_amount'] : '0'; ?>">
                </div>
                <div>
                    <label class="form-label">Max Discount (for %)</label>
                    <input type="number" step="0.01" name="max_discount_amount" class="form-input" value="<?php echo $editMode ? $editCoupon['max_discount_amount'] : '0'; ?>">
                </div>
                <div>
                    <label class="form-label">Expiry Date</label>
                    <input type="date" name="expiry_date" class="form-input" required value="<?php echo $editMode ? $editCoupon['expiry_date'] : ''; ?>">
                </div>
                <div style="grid-column: span 3; display: flex; gap: 1rem;">
                    <button type="submit" name="<?php echo $editMode ? 'update_coupon' : 'create_coupon'; ?>" class="btn-primary" style="width: auto;">
                        <?php echo $editMode ? 'Update Coupon' : 'Create Coupon'; ?>
                    </button>
                    <?php if($editMode): ?>
                        <a href="coupons.php" style="padding: 0.75rem; color: var(--text-gray); text-decoration: none;">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Discount</th>
                            <th>Min Order</th>
                            <th>Expiry</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($c = $coupons->fetch_assoc()): ?>
                        <tr>
                            <td><span style="font-family: monospace; background: rgba(255,255,255,0.1); padding: 0.2rem 0.5rem; border-radius: 4px;"><?php echo htmlspecialchars($c['code']); ?></span></td>
                            <td>
                                <?php echo $c['discount_type'] == 'fixed' ? '$'.$c['discount_value'] : $c['discount_value'].'%'; ?>
                                <?php if($c['discount_type'] == 'percent' && $c['max_discount_amount'] > 0) echo " (Max $".$c['max_discount_amount'].")"; ?>
                            </td>
                            <td>$<?php echo $c['min_order_amount']; ?></td>
                            <td><?php echo $c['expiry_date']; ?></td>
                            <td>
                                <?php 
                                    if(strtotime($c['expiry_date']) < time()) echo "<span style='color: #ef4444'>Expired</span>";
                                    else echo "<span style='color: #34d399'>Active</span>";
                                ?>
                            </td>
                            <td>
                                <a href="coupons.php?edit=<?php echo $c['id']; ?>" style="color: var(--primary); text-decoration: none; margin-right: 0.5rem;">Edit</a>
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
