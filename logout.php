<?php
require_once 'includes/db.php';
session_destroy();
redirect('views/login.php');
?>
