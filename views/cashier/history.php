<?php
require_once '../../includes/db.php';
requireRole('cashier');

$cashier_id = $_SESSION['user_id'];
$shop_id = $_SESSION['shop_id'];

$sales = $db->query("SELECT * FROM orders WHERE shop_id = ? AND cashier_id = ? ORDER BY created_at DESC LIMIT 50", 
                    [$shop_id, $cashier_id], "ii");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales History</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="dashboard-layout">
        <?php include '../includes/sidebar.php'; ?>
        
        <div class="main-content">
            <h1>My Sales History</h1>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Time</th>
                            <th>Payment Method</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $res = $sales->get_result();
                        while($order = $res->fetch_assoc()): 
                        ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td><?php echo date('M d, h:i A', strtotime($order['created_at'])); ?></td>
                            <td><?php echo ucfirst($order['payment_method']); ?></td>
                            <td>$<?php echo number_format($order['grand_total'], 2); ?></td>
                            <td>
                                <a href="receipt.php?order_id=<?php echo $order['id']; ?>" target="_blank" style="color: var(--primary);">View Receipt</a>
                                <a href="return.php?invoice_id=<?php echo $order['id']; ?>" style="color: var(--secondary); margin-left: 1rem;">Return</a>
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
