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
            } else {
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
        $shopName = trim($_POST['shop_name']);
        $email = trim($_POST['email']);
        $username = trim($_POST['username']);
        $password = $_POST['password'];
        $phone = trim($_POST['phone']);

        if (empty($shopName) || strlen($shopName) < 3) {
            header("Location: ../views/register.php?error=" . urlencode("Shop name must be at least 3 characters and cannot be empty or just spaces"));
            exit;
        }

        if (empty($username)) {
            header("Location: ../views/register.php?error=" . urlencode("Owner name cannot be empty or just spaces"));
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header("Location: ../views/register.php?error=" . urlencode("Invalid email address"));
            exit;
        }

        if (empty($phone) || strlen($phone) < 10) {
            header("Location: ../views/register.php?error=" . urlencode("Phone must be at least 10 digits"));
            exit;
        }

        if (strlen($password) < 6) {
            header("Location: ../views/register.php?error=" . urlencode("Password must be at least 6 characters"));
            exit;
        }

        if ($userModel->exists($email, $username)) {
            header("Location: ../views/register.php?error=" . urlencode("Email or Username already exists"));
            exit;
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