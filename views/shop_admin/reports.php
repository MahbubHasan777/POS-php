<?php
require_once '../../models/Order.php';
requireRole('shop_admin');
$shop_id = $_SESSION['shop_id'];
$orderModel = new Order();

$start_date = $_GET['start'] ?? date('Y-m-01');
$end_date = $_GET['end'] ?? date('Y-m-d');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['return_order'])) {
    $orderModel->returnOrder($_POST['order_id'], $shop_id);
    $success = "Order #{$_POST['order_id']} returned and stock restored."; // Simple feedback
}

$orders = $orderModel->getReportWithProfit($shop_id, $start_date, $end_date);

$total_revenue = 0;
$total_profit = 0;
$orders_data = [];

while($row = $orders->fetch_assoc()) {
    $orders_data[] = $row;
    $total_revenue += $row['grand_total'];
    $total_profit += $row['profit'] ?? 0;
}

$best_seller = $orderModel->getBestSellingItem($shop_id, $start_date, $end_date);
$top_items = $orderModel->getTopSellingItems($shop_id, $start_date, $end_date, 5);
$daily_sales = $orderModel->getDailySales($shop_id, date('Y-m-d', strtotime('-7 days')), date('Y-m-d'));
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
                <div class="stat-card">
                    <h3>Best Selling Item</h3>
                    <div class="stat-value" style="font-size: 1.5rem; color: var(--primary);"><?php echo htmlspecialchars($best_seller['name']); ?></div>
                    <small><?php echo $best_seller['total_qty']; ?> sold</small>
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

            <div class="table-container">
                <?php if(isset($success)): ?>
                    <div style="background: rgba(52,211,153,0.1); color: #34d399; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Cashier</th>
                            <th>Total</th>
                            <th>Profit</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($orders_data as $row): ?>
                        <tr>
                            <td>#<?php echo $row['id']; ?></td>
                            <td><?php echo date('M d, H:i', strtotime($row['created_at'])); ?></td>
                            <td><?php echo htmlspecialchars($row['cashier_name']); ?></td>
                            <td>$<?php echo $row['grand_total']; ?></td>
                            <td style="color: #34d399;">$<?php echo number_format($row['profit'] ?? 0, 2); ?></td>
                            <td>
                                <button onclick="showDetails(<?php echo $row['id']; ?>)" class="btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem; width: auto;">Details</button>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Return this order? Stock will be restored.');">
                                    <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="return_order" style="color: #ef4444; background: none; border: none; cursor: pointer; margin-left: 0.5rem;">Return</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Details Modal -->
    <div id="detailsModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); justify-content: center; align-items: center;">
        <div style="background: var(--bg-card); padding: 2rem; border-radius: 1rem; width: 500px; max-width: 90%;">
            <h2 style="margin-top: 0;">Order Details</h2>
            <div id="modalContent">Loading...</div>
            <button onclick="document.getElementById('detailsModal').style.display='none'" class="btn-primary" style="margin-top: 1rem;">Close</button>
        </div>
    </div>

    <script>
        function ajaxRequest(url, options = {}) {
            return new Promise((resolve, reject) => {
                const xhr = new XMLHttpRequest();
                const method = options.method || 'GET';
                xhr.open(method, url);
                
                if (options.headers) {
                    for (let key in options.headers) {
                        xhr.setRequestHeader(key, options.headers[key]);
                    }
                }

                xhr.onload = () => {
                    resolve({
                        ok: xhr.status >= 200 && xhr.status < 300,
                        status: xhr.status,
                        json: () => Promise.resolve(JSON.parse(xhr.responseText)),
                        text: () => Promise.resolve(xhr.responseText)
                    });
                };
                xhr.onerror = () => reject(new Error('Network Error'));
                
                xhr.send(options.body || null);
            });
        }

        function showDetails(id) {
            const modal = document.getElementById('detailsModal');
            const content = document.getElementById('modalContent');
            modal.style.display = 'flex';
            content.innerHTML = 'Loading...';
            
            ajaxRequest('../../api/get_order_details.php?id=' + id)
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    let html = '<table style="width:100%; text-align: left;"><thead><tr><th>Product</th><th>Qty</th><th>Price</th></tr></thead><tbody>';
                    data.items.forEach(item => {
                        html += `<tr>
                            <td>${item.name}</td>
                            <td>${item.quantity}</td>
                            <td>$${item.unit_price}</td>
                        </tr>`;
                    });
                    html += '</tbody></table>';
                    content.innerHTML = html;
                } else {
                    content.innerHTML = 'Error loading details.';
                }
            });
        }
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
                end: '<?php echo $end_date; ?>',
                best_seller: '<?php echo addslashes($best_seller['name']); ?>',
                best_seller_qty: <?php echo $best_seller['total_qty']; ?>,
                top_items: <?php echo json_encode($top_items); ?>,
                daily_sales: <?php echo json_encode($daily_sales); ?>
            };

            ajaxRequest('../../api/ai_insight.php', {
                method: 'POST',
                body: JSON.stringify(data),
                headers: { 'Content-Type': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    resultDiv.innerHTML = data.insight;
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
