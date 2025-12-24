<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SaaS POS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
</head>
<body>
    <div class="auth-container">
        <div class="logo">POS Master</div>
        <?php if(isset($error)): ?>
            <div style="color: #ef4444; margin-bottom: 1rem; text-align: center;"><?php echo $error; ?></div>
        <?php endif; ?>
        <form action="../controllers/auth_controller.php" method="POST">
            <input type="hidden" name="action" value="login">
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-input" required placeholder="admin@example.com">
            </div>
            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-input" required placeholder="••••••••">
            </div>
            <button type="submit" class="btn-primary">Sign In</button>
        </form>
        <div class="auth-links">
            <a href="register.php">Create New Shop Account</a>
            <br><br>
            <a href="forgot_password.php" style="color: var(--text-gray);">Forgot Password?</a>
        </div>
    </div>
</body>
</html>
