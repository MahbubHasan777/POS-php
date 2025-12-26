<?php
require_once '../../includes/db.php';
requireRole('shop_admin');
$shop_id = $_SESSION['shop_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $contact = $_POST['contact'];
    $db->query("INSERT INTO suppliers (shop_id, name, contact_info) VALUES (?, ?, ?)", [$shop_id, $name, $contact], "iss");
}

$suppliers = $db->query("SELECT * FROM suppliers WHERE shop_id = ?", [$shop_id], "i");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Suppliers</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="dashboard-layout">
        <?php include '../includes/sidebar.php'; ?>
        <div class="main-content">
            <h1>Suppliers</h1>
            
            <form method="POST" style="background: var(--bg-card); padding: 1.5rem; border-radius: 0.5rem; margin-bottom: 2rem; display: flex; gap: 1rem; align-items: flex-end;">
                <div style="flex: 1;">
                    <label class="form-label">Supplier Name</label>
                    <input type="text" name="name" class="form-input" required>
                </div>
                <div style="flex: 2;">
                    <label class="form-label">Contact Info (Phone/Address)</label>
                    <input type="text" name="contact" class="form-input" required>
                </div>
                <button type="submit" class="btn-primary" style="width: auto;">Add Supplier</button>
            </form>

            <div class="table-container">
                <table>
                    <thead><tr><th>Name</th><th>Contact Info</th></tr></thead>
                    <tbody>
                        <?php while($s = $suppliers->get_result()->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($s['name']); ?></td>
                            <td><?php echo htmlspecialchars($s['contact_info']); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
