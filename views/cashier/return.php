<?php
require_once '../../includes/db.php';
requireRole('cashier');

$order = null;
$error = '';
$success = '';

if (isset($_GET['invoice_id'])) {
    $invoice_id = $_GET['invoice_id'];
    $stmt = $db->query("SELECT * FROM orders WHERE id = ? AND shop_id = ?", [$invoice_id, $_SESSION['shop_id']], "ii");
    $order_data = $stmt->get_result()->fetch_assoc();

    if ($order_data) {
        $stmt_items = $db->query("SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?", [$invoice_id], "i");
        $items = [];
        $items_res = $stmt_items->get_result();
        while ($row = $items_res->fetch_assoc()) {
            $items[] = $row;
        }
        $order = [
            'info' => $order_data,
            'items' => $items
        ];
    } else {
        $error = "Invoice ID not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Process Return</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="dashboard-layout">
        <div style="background: var(--bg-card); padding: 1rem; margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; max-width: 1200px; margin: 0 auto;">
                <h1 style="font-size: 1.5rem;">Process Return</h1>
                <a href="dashboard.php" class="btn-primary" style="text-decoration: none; background: var(--text-gray);">Back to POS</a>
            </div>
        </div>

        <div style="max-width: 800px; margin: 0 auto; padding: 0 1rem;">
            <div style="background: var(--bg-card); padding: 2rem; border-radius: 0.75rem; border: 1px solid rgba(255,255,255,0.05);">
                <form method="GET" style="display: flex; gap: 1rem; margin-bottom: 2rem;">
                    <input type="number" name="invoice_id" class="form-input" placeholder="Enter Invoice ID (Order #)" value="<?php echo $_GET['invoice_id'] ?? ''; ?>" required>
                    <button type="submit" class="btn-primary" style="width: auto;">Search</button>
                </form>

                <?php if ($error): ?>
                    <div style="padding: 1rem; background: rgba(239, 68, 68, 0.2); color: #f87171; border-radius: 0.5rem; margin-bottom: 1rem;">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <?php if ($order): ?>
                    <div style="margin-bottom: 2rem;">
                        <h3>Order #<?php echo $order['info']['id']; ?> Details</h3>
                        <p>Date: <?php echo $order['info']['created_at']; ?></p>
                        <p>Total: $<?php echo $order['info']['grand_total']; ?></p>
                    </div>

                    <h3>Select Items to Return</h3>
                    <form id="returnForm">
                        <input type="hidden" name="order_id" value="<?php echo $order['info']['id']; ?>">
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Qty Sold</th>
                                        <th>Return Qty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($order['items'] as $item): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                                        <td>$<?php echo $item['unit_price']; ?></td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td>
                                            <input type="number" name="items[<?php echo $item['product_id']; ?>]" 
                                                   class="form-input" style="width: 80px;" 
                                                   min="0" max="<?php echo $item['quantity']; ?>" value="0">
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <button type="button" onclick="submitReturn()" class="btn-primary" style="margin-top: 1rem;">Process Refund</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function submitReturn() {
            const form = document.getElementById('returnForm');
            const formData = new FormData(form);
            
            // Validate at least one item returned
            let hasItems = false;
            for(let [key, value] of formData.entries()) {
                if(key.startsWith('items') && parseInt(value) > 0) hasItems = true;
            }
            
            if(!hasItems) {
                alert("Please select at least one item to return.");
                return;
            }

            if(!confirm("Are you sure you want to process this return? Stock will be updated.")) return;

            fetch('../../api/process_return.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    alert("Return processed successfully!");
                    window.location.href = 'dashboard.php';
                } else {
                    alert("Error: " + data.message);
                }
            });
        }
    </script>
</body>
</html>
