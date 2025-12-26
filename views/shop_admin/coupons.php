<?php
require_once '../../includes/db.php';
requireRole('shop_admin');
$shop_id = $_SESSION['shop_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = $_POST['code'];
    $type = $_POST['discount_type'];
    $value = $_POST['discount_value'];
    $min = $_POST['min_order_amount'];
    $max = $_POST['max_discount_amount'];
    $expiry = $_POST['expiry_date'];

    $db->query("INSERT INTO coupons (shop_id, code, discount_type, discount_value, min_order_amount, max_discount_amount, expiry_date) VALUES (?, ?, ?, ?, ?, ?, ?)",
        [$shop_id, $code, $type, $value, $min, $max, $expiry], "issddds");
}

$coupons = $db->query("SELECT * FROM coupons WHERE shop_id = ? ORDER BY expiry_date DESC", [$shop_id], "i");
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
            
            <form method="POST" style="background: var(--bg-card); padding: 1.5rem; border-radius: 0.5rem; margin-bottom: 2rem; display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                <div>
                    <label class="form-label">Code</label>
                    <input type="text" name="code" class="form-input" required placeholder="SUMMER25">
                </div>
                <div>
                    <label class="form-label">Type</label>
                    <select name="discount_type" class="form-input">
                        <option value="fixed">Fixed Amount (Taka)</option>
                        <option value="percent">Percentage (%)</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Value</label>
                    <input type="number" step="0.01" name="discount_value" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Min Order Amount</label>
                    <input type="number" step="0.01" name="min_order_amount" class="form-input" value="0">
                </div>
                <div>
                    <label class="form-label">Max Discount (for %)</label>
                    <input type="number" step="0.01" name="max_discount_amount" class="form-input" value="0">
                </div>
                <div>
                    <label class="form-label">Expiry Date</label>
                    <input type="date" name="expiry_date" class="form-input" required>
                </div>
                <div style="grid-column: span 3;">
                    <button type="submit" class="btn-primary" style="width: auto;">Create Coupon</button>
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
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($c = $coupons->get_result()->fetch_assoc()): ?>
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
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
