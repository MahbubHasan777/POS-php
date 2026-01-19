<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Shop - SaaS POS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
</head>

<body>
    <div class="auth-container" style="max-width: 500px;">
        <div class="logo">POS Master</div>
        <h2 style="text-align: center; margin-bottom: 2rem;">Register Your Shop</h2>
        
        <?php if(isset($_GET['error'])): ?>
            <div style="padding: 10px; background: rgba(239, 68, 68, 0.2); color: #f87171; border-radius: 5px; margin-bottom: 1rem;">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <div id="js-error-container" style="padding: 10px; background: rgba(239, 68, 68, 0.2); color: #f87171; border-radius: 5px; margin-bottom: 1rem; display: none;"></div>

        <form action="../controllers/auth_controller.php" method="POST" id="register-form">
            <input type="hidden" name="action" value="register_shop">

            <div class="form-group">
                <label class="form-label">Shop/Business Name</label>
                <input type="text" name="shop_name" class="form-input" required placeholder="My Awesome Store">
            </div>

            <div class="form-group">
                <label class="form-label">Owner Full Name</label>
                <input type="text" name="username" class="form-input" required placeholder="Mahbub Hasan">
            </div>

            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-input" required placeholder="owner@business.com">
            </div>

            <div class="form-group">
                <label class="form-label">Phone Number</label>
                <input type="tel" name="phone" class="form-input" required placeholder="+1234567890">
            </div>

            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-input" required placeholder="Create a strong password">
            </div>

            <button type="submit" class="btn-primary">Create Account</button>
        </form>
        
        <div class="auth-links">
            Already have an account? <a href="login.php" style="color: var(--primary);">Login here</a>
        </div>
    </div>
    <script src="../assets/js/register.js"></script>
</body>
</html>