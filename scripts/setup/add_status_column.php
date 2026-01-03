<?php
require_once '../../includes/db.php';

try {
    // Check if column exists first
    $check = $db->query("SHOW COLUMNS FROM orders LIKE 'status'");
    if ($check->get_result()->num_rows == 0) {
        $db->query("ALTER TABLE orders ADD COLUMN status VARCHAR(20) DEFAULT 'completed'");
        echo "Column 'status' added successfully.";
    } else {
        echo "Column 'status' already exists.";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
