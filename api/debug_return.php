<?php
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'cashier'; 
$_SESSION['shop_id'] = 1;

$_POST['order_id'] = 1; 
$_POST['items'] = [1 => 1]; 

require 'process_return.php';
?>
