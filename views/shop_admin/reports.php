<?php
require_once '../../models/Order.php';
requireRole('shop_admin');
$shop_id = $_SESSION['shop_id'];
$orderModel = new Order();

// Date Filter
$start_date = $_GET['start'] ?? date('Y-m-01');
$end_date = $_GET['end'] ?? date('Y-m-d');

// Fetch Orders via Model
$orders = $orderModel->getReport($shop_id, $start_date, $end_date);

// Calculate Totals (Internal Logic here or could be in Model)
$total_revenue = 0;
$total_profit = 0;
$orders_data = [];

while($row = $orders->fetch_assoc()) {
    $orders_data[] = $row;
    $total_revenue += $row['grand_total'];
    // Profit Calculation - Ideally Model should handle complex logic but keeping here for now or adding helper method
    // Since we need to query items for each order to get profit, let's just do it.
    // Optimization: Join in the main query would be better.
    // For now, let's rely on Order model getting items if we want, or just raw query if specialized.
    // Let's stick to the previous logic but use Core wrapper via model if needed.
    // Actually, let's duplicate the logic but via Core to be safe/quick.
    $core = $orderModel; // It extends Core
    $items = $core->query("SELECT oi.quantity, p.buy_price, p.sell_price FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?", [$row['id']], "i");
    while($item = $items->get_result()->fetch_assoc()) {
        $profit = ($item['sell_price'] - $item['buy_price']) * $item['quantity'];
        $total_profit += $profit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reports & AI Insights</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="dashboard-layout">
        <?php include '../includes/sidebar.php'; ?>
        
        <div class="main-content">
            <h1>Sales Reports</h1>
            
            <form class="form-group" style="display: flex; gap: 1rem; align-items: flex-end; background: var(--bg-card); padding: 1rem; border-radius: 0.5rem; margin-bottom: 2rem;">
                <div>
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start" class="form-input" value="<?php echo $start_date; ?>">
                </div>
                <div>
                    <label class="form-label">End Date</label>
                    <input type="date" name="end" class="form-input" value="<?php echo $end_date; ?>">
                </div>
                <button type="submit" class="btn-primary" style="width: auto;">Filter</button>
            </form>

            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Revenue</h3>
                    <div class="stat-value">$<?php echo number_format($total_revenue, 2); ?></div>
                </div>
                <div class="stat-card">
                    <h3>Net Profit</h3>
                    <div class="stat-value" style="color: #34d399;">$<?php echo number_format($total_profit, 2); ?></div>
                </div>
                <div class="stat-card">
                    <h3>Orders Count</h3>
                    <div class="stat-value"><?php echo count($orders_data); ?></div>
                </div>
            </div>

            <!-- AI Section -->
            <div style="margin-top: 2rem; background: linear-gradient(135deg, rgba(79, 70, 229, 0.1) 0%, rgba(6, 182, 212, 0.1) 100%); padding: 1.5rem; border-radius: 1rem; border: 1px solid rgba(79, 70, 229, 0.2);">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h2 style="background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">AI Business Analyst</h2>
                        <p style="color: var(--text-gray);">Get insights and tips to increase your sales based on this report.</p>
                    </div>
                    <button id="askAiBtn" onclick="fetchAiInsights()" class="btn-primary" style="width: auto;">✨ Generate Insights with Gemini</button>
                </div>
                <div id="aiResult" style="margin-top: 1rem; white-space: pre-wrap; line-height: 1.6;"></div>
            </div>

            <h2 style="margin-top: 2rem;">Order History</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Cashier</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($orders_data as $row): ?>
                        <tr>
                            <td>#<?php echo $row['id']; ?></td>
                            <td><?php echo $row['created_at']; ?></td>
                            <td>User #<?php echo $row['cashier_id']; ?></td>
                            <td>$<?php echo $row['grand_total']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function fetchAiInsights() {
            const btn = document.getElementById('askAiBtn');
            const resultDiv = document.getElementById('aiResult');
            
            btn.disabled = true;
            btn.innerText = "Analyzing...";
            resultDiv.innerText = "Connecting to Gemini API...";

            const data = {
                revenue: <?php echo $total_revenue; ?>,
                profit: <?php echo $total_profit; ?>,
                orders: <?php echo count($orders_data); ?>,
                start: '<?php echo $start_date; ?>',
                end: '<?php echo $end_date; ?>'
            };

            fetch('../../api/ai_insight.php', {
                method: 'POST',
                body: JSON.stringify(data),
                headers: { 'Content-Type': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    resultDiv.innerText = data.insight;
                } else {
                    resultDiv.innerText = "Error: " + data.message;
                }
                btn.disabled = false;
                btn.innerText = "✨ Generate Insights with Gemini";
            });
        }
    </script>
</body>
</html>
