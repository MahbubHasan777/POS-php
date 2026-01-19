<?php
require_once '../../includes/db.php';
requireRole('cashier');

$invoice_id = $_GET['invoice_id'] ?? '';
$order_data = null;
$error_msg = '';

if ($invoice_id) {
    $stmt = $db->query("SELECT * FROM orders WHERE id = ? AND shop_id = ?", [$invoice_id, $_SESSION['shop_id']], "ii");
    $order_info = $stmt->get_result()->fetch_assoc();

    if ($order_info) {
        $stmt_items = $db->query("SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?", [$invoice_id], "i");
        $items_res = $stmt_items->get_result();
        $items = [];
        while ($row = $items_res->fetch_assoc()) {
            $items[] = $row;
        }
        $order_data = ['info' => $order_info, 'items' => $items];
    } else {
        $error_msg = "Invoice #$invoice_id not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Return Items</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        .container {
            max-width: 800px;
            margin: 2rem auto;
            background: var(--bg-card);
            padding: 2rem;
            border-radius: 8px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .table th,
        .table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .error {
            color: #ef4444;
            background: rgba(239, 68, 68, 0.1);
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="header">
            <h2>Process Return</h2>
            <a href="dashboard.php" class="btn-primary" style="text-decoration: none; background: #4b5563;">Back to
                POS</a>
        </div>

        <form method="GET" style="display: flex; gap: 1rem; margin-bottom: 2rem;">
            <input type="number" name="invoice_id" class="form-input" placeholder="Invoice ID"
                value="<?php echo htmlspecialchars($invoice_id); ?>" required>
            <button type="submit" class="btn-primary" style="width: auto;">Search</button>
        </form>

        <?php if ($error_msg): ?>
            <div class="error"><?php echo $error_msg; ?></div>
        <?php endif; ?>

        <?php if ($order_data): ?>
            <div style="margin-bottom: 1.5rem; padding: 1rem; background: rgba(255,255,255,0.05); border-radius: 4px;">
                <strong>Order #<?php echo $order_data['info']['id']; ?></strong><br>
                Date: <?php echo $order_data['info']['created_at']; ?><br>
                Grand Total: ৳<?php echo number_format($order_data['info']['grand_total'], 2); ?>
            </div>

            <form id="refundForm">
                <input type="hidden" name="order_id" value="<?php echo $order_data['info']['id']; ?>">

                <table class="table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Sold Price</th>
                            <th>Qty Sold</th>
                            <th>Return Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($order_data['items'] as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['name']); ?></td>
                                <td>৳<?php echo number_format($item['unit_price'], 2); ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td>
                                    <input type="number" name="items[<?php echo $item['product_id']; ?>]" class="form-input"
                                        style="width: 80px;" min="0" max="<?php echo $item['quantity']; ?>" value="0">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div style="margin-top: 2rem; text-align: right;">
                    <button type="button" onclick="processRefund()" class="btn-primary" style="background: #ef4444;">Confirm
                        Refund</button>
                </div>
            </form>
        <?php endif; ?>
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

        async function processRefund() {
            const form = document.getElementById('refundForm');
            const formData = new FormData(form);

            let valid = false;
            for (let [k, v] of formData.entries()) {
                if (k.startsWith('items') && parseInt(v) > 0) valid = true;
            }

            if (!valid) {
                alert("Please select at least one item to return.");
                return;
            }

            if (!confirm("Are you sure? This will update stock and order totals.")) return;

            try {
                const res = await ajaxRequest('../../api/process_return.php', {
                    method: 'POST',
                    body: formData
                });

                const rawText = await res.text();
                console.log("Raw Response:", rawText);

                try {
                    const data = JSON.parse(rawText);
                    if (data.success) {
                        alert("Refund Processed Successfully!");
                        window.location.href = 'dashboard.php';
                    } else {
                        alert("Error: " + data.message);
                    }
                } catch (e) {
                    console.error("JSON Parse Error:", e);
                    alert("Server Error (Check Console for details): " + rawText.substring(0, 100));
                }

            } catch (err) {
                console.error("Fetch Error:", err);
                alert("Network Error");
            }
        }
    </script>

</body>

</html>