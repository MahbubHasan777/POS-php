<?php
require_once 'includes/db.php';

$sql = "ALTER TABLE shops ADD COLUMN cycle_start_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP";

if ($db->query($sql)) {
    echo "Column cycle_start_date added to shops table.";
    
    // Initialize existing shops to have cycle start date as their creation date if needed
    $db->query("UPDATE shops SET cycle_start_date = created_at");
} else {
    echo "Error adding column (maybe it already exists).";
}
?>
