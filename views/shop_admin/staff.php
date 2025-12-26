<?php
require_once '../../includes/db.php';
requireRole('shop_admin');
$shop_id = $_SESSION['shop_id'];

// Handle Add Staff
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_staff'])) {
    $name = $_POST['full_name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    
    // Check duplication
    $check = $db->query("SELECT id FROM users WHERE email = ? OR username = ?", [$email, $username], "ss");
    if ($check->get_result()->num_rows == 0) {
        $db->query("INSERT INTO users (shop_id, role, username, email, password_hash, full_name) VALUES (?, 'cashier', ?, ?, ?, ?)", 
            [$shop_id, $username, $email, $password, $name], "issss");
    } else {
        $error = "Email or Username already exists.";
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $db->query("DELETE FROM users WHERE id = ? AND shop_id = ? AND role = 'cashier'", [$_GET['delete'], $shop_id], "ii");
    redirect('staff.php');
}

$staff = $db->query("SELECT * FROM users WHERE shop_id = ? AND role = 'cashier' ORDER BY created_at DESC", [$shop_id], "i");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Staff</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="dashboard-layout">
        <?php include '../includes/sidebar.php'; ?>
        
        <div class="main-content">
            <h1>Staff Management</h1>
            <p style="color: var(--text-gray); margin-bottom: 2rem;">Create and manage Cashier accounts.</p>

            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem;">
                <!-- Add Form -->
                <div style="background: var(--bg-card); padding: 1.5rem; border-radius: 0.75rem; height: fit-content;">
                    <h3>Add New Cashier</h3>
                    <?php if(isset($error)): ?><p style="color: #ef4444;"><?php echo $error; ?></p><?php endif; ?>
                    <form method="POST">
                        <div class="form-group">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="full_name" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-input" required>
                        </div>
                        <button type="submit" name="add_staff" class="btn-primary">Create Account</button>
                    </form>
                </div>

                <!-- List -->
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Username</th>
                                <th>Created</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($user = $staff->get_result()->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <a href="staff.php?delete=<?php echo $user['id']; ?>" onclick="return confirm('Remove this user?')" style="color: #ef4444; text-decoration: none;">Remove</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
