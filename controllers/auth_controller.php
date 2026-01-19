<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Shop.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'login';
    $userModel = new User();
    $shopModel = new Shop();

    if ($action === 'login') {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $user = $userModel->login($email, $password);

        if ($user === 'suspended') {
            $error = "Your shop has been suspended. Please contact Super Admin.";
            include '../views/login.php';
        } elseif ($user) {
            if ($user['shop_id'] && $user['shop_status'] === 'suspended') {
                $error = "Your shop has been suspended. Please contact Super Admin.";
                include '../views/login.php';
            }
            // Check Subscription Limit
            // Limit check moved to POS Checkout
            else {
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
            }
        } else {
            $error = "Invalid email or password";
            include '../views/login.php';
        }
    } elseif ($action === 'register_shop') {
        $shopName = $_POST['shop_name'];
        $email = $_POST['email'];
        $username = $_POST['username'];
        $password = $_POST['password'];

        if ($userModel->exists($email, $username)) {
            die("Email or Username already exists");
        }

        $shopId = $shopModel->create($shopName);

        $userModel->create([
            'shop_id' => $shopId,
            'role' => 'shop_admin',
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'full_name' => $shopName
        ]);

        $_SESSION['success'] = "Shop registered! Please login.";
        redirect('../views/login.php');
    }
}


?>