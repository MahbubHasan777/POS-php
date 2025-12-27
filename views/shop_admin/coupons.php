<?php
require_once '../../includes/db.php';
requireRole('shop_admin');
$shop_id = $_SESSION['shop_id'];

$edit_mode = false;
$coupon_data = null;

// --- HANDLE DELETE ---
if (isset($_POST['delete_id'])) {
    $db->query("DELETE FROM coupons WHERE id = ? AND shop_id = ?", [$_POST['delete_id'], $shop_id], "ii");
    redirect('coupons.php');
}

// --- HANDLE FORM SUBMIT ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_coupon'])) {
    $code = $_POST['code'];
    $type = $_POST['discount_type'];
    $value = $_POST['discount_value'];
    $min = $_POST['min_order_amount'];
    $max = $_POST['max_discount_amount'];
    $expiry = $_POST['expiry_date'];
    
    if (!empty($_POST['edit_id'])) {
        // Update
        $db->query("UPDATE coupons SET code=?, discount_type=?, discount_value=?, min_order_amount=?, max_discount_amount=?, expiry_date=? WHERE id=? AND shop_id=?",
            [$code, $type, $value, $min, $max, $expiry, $_POST['edit_id'], $shop_id], "sidddsii");
    } else {
        // Create
        $db->query("INSERT INTO coupons (shop_id, code, discount_type, discount_value, min_order_amount, max_discount_amount, expiry_date) VALUES (?, ?, ?, ?, ?, ?, ?)",
            [$shop_id, $code, $type, $value, $min, $max, $expiry], "issddds");
    }
    redirect('coupons.php');
}

// --- HANDLE EDIT GET ---
if (isset($_GET['edit'])) {
    $stmt = $db->query("SELECT * FROM coupons WHERE id = ? AND shop_id = ?", [$_GET['edit'], $shop_id], "ii");
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $edit_mode = true;
        $coupon_data = $res->fetch_assoc();
    }
}

$coupons = $db->query("SELECT * FROM coupons WHERE shop_id = ? ORDER BY expiry_date DESC", [$shop_id], "i");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Coupons</title>
    <link rel="stylesheet" href="../../assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="dashboard-layout">
        <?php include '../includes/sidebar.php'; ?>
        <div class="main-content">
            <h1><?php echo $edit_mode ? 'Edit Coupon' : 'Coupon Management'; ?></h1>
            
            <form method="POST" style="background: var(--bg-card); padding: 1.5rem; border-radius: 0.5rem; margin-bottom: 2rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                
                <?php if($edit_mode): ?>
                    <input type="hidden" name="edit_id" value="<?php echo $coupon_data['id']; ?>">
                <?php endif; ?>

                <div>
                    <label class="form-label">Code</label>
                    <input type="text" name="code" class="form-input" required placeholder="SUMMER25" value="<?php echo $coupon_data['code'] ?? ''; ?>">
                </div>
                <div>
                    <label class="form-label">Type</label>
                    <select name="discount_type" class="form-input">
                        <option value="fixed" <?php echo ($coupon_data['discount_type'] ?? '') == 'fixed' ? 'selected' : ''; ?>>Fixed Amount (Taka)</option>
                        <option value="percent" <?php echo ($coupon_data['discount_type'] ?? '') == 'percent' ? 'selected' : ''; ?>>Percentage (%)</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Value</label>
                    <input type="number" step="0.01" name="discount_value" class="form-input" required value="<?php echo $coupon_data['discount_value'] ?? ''; ?>">
                </div>
                <div>
                    <label class="form-label">Min Order Amount</label>
                    <input type="number" step="0.01" name="min_order_amount" class="form-input" value="<?php echo $coupon_data['min_order_amount'] ?? '0'; ?>">
                </div>
                <div>
                    <label class="form-label">Max Discount (for %)</label>
                    <input type="number" step="0.01" name="max_discount_amount" class="form-input" value="<?php echo $coupon_data['max_discount_amount'] ?? '0'; ?>">
                </div>
                <div>
                    <label class="form-label">Expiry Date</label>
                    <input type="date" name="expiry_date" class="form-input" required value="<?php echo $coupon_data['expiry_date'] ?? ''; ?>">
                </div>
                <div style="grid-column: 1 / -1; display: flex; gap: 10px;">
                    <button type="submit" name="save_coupon" class="btn-primary" style="width: auto;">
                        <?php echo $edit_mode ? 'Update Coupon' : 'Create Coupon'; ?>
                    </button>
                    <?php if($edit_mode): ?>
                        <a href="coupons.php" class="btn-primary" style="background: transparent; border: 1px solid #666; width: auto; text-decoration: none; text-align: center;">Cancel</a>
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
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $res = $coupons->get_result();
                        if ($res->num_rows == 0): ?>
                            <tr><td colspan="6" style="text-align:center; padding: 20px;">No coupons found.</td></tr>
                        <?php else:
                        while($c = $res->fetch_assoc()): 
                        ?>
                        <tr>
                            <td><span style="font-family: monospace; background: rgba(255,255,255,0.1); padding: 0.2rem 0.5rem; border-radius: 4px;"><?php echo htmlspecialchars($c['code']); ?></span></td>
                            <td>
                                <?php echo $c['discount_type'] == 'fixed' ? 'Taka '.$c['discount_value'] : $c['discount_value'].'%'; ?>
                                <?php if($c['discount_type'] == 'percent' && $c['max_discount_amount'] > 0) echo " (Max Taka ".$c['max_discount_amount'].")"; ?>
                            </td>
                            <td>Taka <?php echo $c['min_order_amount']; ?></td>
                            <td><?php echo $c['expiry_date']; ?></td>
                            <td>
                                <?php 
                                    if(strtotime($c['expiry_date']) < time()) echo "<span style='color: #ef4444'>Expired</span>";
                                    else echo "<span style='color: #34d399'>Active</span>";
                                ?>
                            </td>
                            <td>
                                <a href="coupons.php?edit=<?php echo $c['id']; ?>" style="color: var(--primary); margin-right: 10px; text-decoration: none;">Edit</a>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this coupon?');">
                                    <input type="hidden" name="delete_id" value="<?php echo $c['id']; ?>">
                                    <button type="submit" style="color: #ef4444; background: none; border: none; cursor: pointer;">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
