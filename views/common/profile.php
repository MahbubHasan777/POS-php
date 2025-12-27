<?php
require_once '../includes/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $user_id = $_SESSION['user_id'];

    if ($new_password !== $confirm_password) {
        $error = "New passwords do not match.";
    } else {
        // Verify current
        $res = $db->query("SELECT password_hash FROM users WHERE id = ?", [$user_id], "i");
        $user = $res->get_result()->fetch_assoc();

        if (password_verify($current_password, $user['password_hash'])) {
            $new_hash = password_hash($new_password, PASSWORD_BCRYPT);
            $db->query("UPDATE users SET password_hash = ? WHERE id = ?", [$new_hash, $user_id], "si");
            $message = "Password updated successfully.";
        } else {
            $error = "Incorrect current password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile Settings</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="dashboard-layout">
        <?php include '../includes/sidebar.php'; ?>
        
        <div class="main-content">
            <h1>Profile Settings</h1>
            
            <div style="background: var(--bg-card); padding: 2rem; border-radius: 0.5rem; max-width: 600px;">
                <h3 style="margin-bottom: 1.5rem;">Change Password</h3>
                
                <?php if($message): ?><div style="color: #34d399; margin-bottom: 1rem;"><?php echo $message; ?></div><?php endif; ?>
                <?php if($error): ?><div style="color: #f87171; margin-bottom: 1rem;"><?php echo $error; ?></div><?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label class="form-label">Current Password</label>
                        <input type="password" name="current_password" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">New Password</label>
                        <input type="password" name="new_password" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="confirm_password" class="form-input" required>
                    </div>
                    <button type="submit" class="btn-primary">Update Password</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
