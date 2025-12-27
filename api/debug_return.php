<?php
// Mock Session
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'cashier'; 
$_SESSION['shop_id'] = 1;

// Mock POST Data
$_POST['order_id'] = 1; 
$_POST['items'] = [1 => 1]; 

// Include the API in the same dir
require 'process_return.php';
?>
