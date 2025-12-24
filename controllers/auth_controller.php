<?php
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'login';

    if ($action === 'login') {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $result = $db->query("SELECT * FROM users WHERE email = ?", [$email], "s");
        $user = $result->get_result()->fetch_assoc();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['shop_id'] = $user['shop_id'];
            $_SESSION['username'] = $user['username'];

            if ($user['role'] === 'super_admin') {
                redirect('../views/super_admin/dashboard.php');
            } elseif ($user['role'] === 'shop_admin') {
                redirect('../views/shop_admin/dashboard.php');
            } else {
                redirect('../views/cashier/dashboard.php');
            }
        } else {
            $error = "Invalid email or password";
            include '../views/login.php';
        }
    } 
    elseif ($action === 'register_shop') {
        // Simple registration logic for demo
        $shopName = $_POST['shop_name'];
        $email = $_POST['email'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $phone = $_POST['phone'];
        
        // Check if email exists
        $check = $db->query("SELECT id FROM users WHERE email = ?", [$email], "s");
        if ($check->get_result()->num_rows > 0) {
            die("Email already exists");
        }

        // Create Shop
        // Default to plan 1 (Free)
        $db->query("INSERT INTO shops (name, subscription_plan_id) VALUES (?, 1)", [$shopName], "s");
        $shopId = $db->getLastId();

        // Create Shop Admin
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $db->query("INSERT INTO users (shop_id, role, username, email, password_hash, full_name) VALUES (?, 'shop_admin', ?, ?, ?, ?)", 
            [$shopId, $username, $email, $hash, $shopName], "issss");

        $_SESSION['success'] = "Shop registered! Please login.";
        redirect('../views/login.php');
    }
}
?>
