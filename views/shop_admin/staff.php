<?php
require_once '../../includes/db.php';
requireRole('shop_admin');
$shop_id = $_SESSION['shop_id'];

$edit_mode = false;
$staff_data = null;
$error = "";
$success = "";

// --- HANDLE DELETE ---
if (isset($_GET['delete'])) {
    $db->query("DELETE FROM users WHERE id = ? AND shop_id = ? AND role = 'cashier'", [$_GET['delete'], $shop_id], "ii");
    redirect('staff.php');
}

// --- HANDLE FORM SUBMIT ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['full_name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password']; // Can be empty on update
    
    if (isset($_POST['edit_id'])) {
        // UPDATE
        $id = $_POST['edit_id'];
        
        // Check duplication (excluding self)
        $check = $db->query("SELECT id FROM users WHERE (email = ? OR username = ?) AND id != ? AND shop_id = ?", [$email, $username, $id, $shop_id], "ssii");
        if ($check->get_result()->num_rows > 0) {
            $error = "Email or Username already taken by another staff.";
            $edit_mode = true;
            $staff_data = $_POST; // Preserve input
            $staff_data['id'] = $id;
        } else {
            if (!empty($password)) {
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $db->query("UPDATE users SET full_name=?, email=?, username=?, password_hash=? WHERE id=? AND shop_id=?", 
                    [$name, $email, $username, $hash, $id, $shop_id], "ssssii");
            } else {
                $db->query("UPDATE users SET full_name=?, email=?, username=? WHERE id=? AND shop_id=?", 
                    [$name, $email, $username, $id, $shop_id], "sssii");
            }
            $success = "Staff updated successfully.";
            // Clear edit mode
            $edit_mode = false;
            $staff_data = null;
        }
    } else {
        // CREATE
        $check = $db->query("SELECT id FROM users WHERE (email = ? OR username = ?) AND shop_id = ?", [$email, $username, $shop_id], "ssi");
        if ($check->get_result()->num_rows > 0) {
            $error = "Email or Username already exists.";
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $db->query("INSERT INTO users (shop_id, role, username, email, password_hash, full_name) VALUES (?, 'cashier', ?, ?, ?, ?)", 
                [$shop_id, $username, $email, $hash, $name], "issss");
            $success = "Staff created successfully.";
        }
    }
}

// --- HANDLE EDIT GET ---
if (isset($_GET['edit'])) {
    $stmt = $db->query("SELECT * FROM users WHERE id = ? AND shop_id = ? AND role = 'cashier'", [$_GET['edit'], $shop_id], "ii");
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $edit_mode = true;
        $staff_data = $res->fetch_assoc();
    }
}

$staff_list = $db->query("SELECT * FROM users WHERE shop_id = ? AND role = 'cashier' ORDER BY created_at DESC", [$shop_id], "i");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Staff</title>
    <link rel="stylesheet" href="../../assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="dashboard-layout">
        <?php include '../includes/sidebar.php'; ?>
        
        <div class="main-content">
            <h1>Staff Management</h1>
            <p style="color: var(--text-gray); margin-bottom: 2rem;">Create and manage Cashier accounts.</p>

            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem;">
                <!-- Form -->
                <div style="background: var(--bg-card); padding: 1.5rem; border-radius: 0.75rem; height: fit-content;">
                    <h3><?php echo $edit_mode ? 'Edit Cashier' : 'Add New Cashier'; ?></h3>
                    
                    <?php if($error): ?><div style="color: #ef4444; margin-bottom: 1rem;"><?php echo $error; ?></div><?php endif; ?>
                    <?php if($success): ?><div style="color: #34d399; margin-bottom: 1rem;"><?php echo $success; ?></div><?php endif; ?>

                    <form method="POST" action="staff.php">
                        <?php if($edit_mode): ?>
                            <input type="hidden" name="edit_id" value="<?php echo $staff_data['id']; ?>">
                        <?php endif; ?>

                        <div class="form-group">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="full_name" class="form-input" required value="<?php echo $staff_data['full_name'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-input" required value="<?php echo $staff_data['username'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-input" required value="<?php echo $staff_data['email'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label"><?php echo $edit_mode ? 'New Password (leave empty to keep)' : 'Password'; ?></label>
                            <input type="password" name="password" class="form-input" <?php echo $edit_mode ? '' : 'required'; ?>>
                        </div>
                        
                        <button type="submit" class="btn-primary"><?php echo $edit_mode ? 'Update Account' : 'Create Account'; ?></button>
                        
                        <?php if($edit_mode): ?>
                            <a href="staff.php" class="btn-primary" style="display: block; text-align: center; margin-top: 10px; background: transparent; border: 1px solid #666; width: 100%; box-sizing: border-box; text-decoration: none;">Cancel</a>
                        <?php endif; ?>
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
                            <?php 
                            $res = $staff_list->get_result();
                            if ($res->num_rows === 0): ?>
                                <tr><td colspan="4" style="text-align:center; padding: 2rem;">No staff members found.</td></tr>
                            <?php else:
                            while($user = $res->fetch_assoc()): 
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <a href="staff.php?edit=<?php echo $user['id']; ?>" style="color: var(--primary); margin-right: 10px; text-decoration: none;">Edit</a>
                                    <a href="staff.php?delete=<?php echo $user['id']; ?>" onclick="return confirm('Remove this user?')" style="color: #ef4444; text-decoration: none;">Remove</a>
                                </td>
                            </tr>
                            <?php endwhile; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
