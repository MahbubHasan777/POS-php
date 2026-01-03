<?php
// Mock Session
session_start();
$_SESSION['user_id'] = 1; // Assuming 1 is a cashier
$_SESSION['role'] = 'cashier'; 
$_SESSION['shop_id'] = 1;

// Mock POST Data
$_POST['order_id'] = 1; // Assuming order 1 exists
$_POST['items'] = [1 => 1]; // Assuming product 1 exists in order 1

// Include the API
require '../../api/process_return.php';
?>
