<?php
require_once 'includes/db.php';

$sql = "CREATE TABLE IF NOT EXISTS subscription_payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    shop_id INT NOT NULL,
    plan_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_status ENUM('paid', 'pending', 'failed') DEFAULT 'pending',
    payment_method VARCHAR(50), 
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES subscription_plans(id)
)";

if ($db->query($sql)) {
    echo "Table subscription_payments created successfully.";
    
    // Insert dummy data if empty
    $check = $db->query("SELECT * FROM subscription_payments");
    if ($check->get_result()->num_rows == 0) {
        $db->query("INSERT INTO subscription_payments (shop_id, plan_id, amount, payment_status, payment_method) VALUES (1, 1, 0.00, 'paid', 'system_init')");
        echo " Dummy data inserted.";
    }
} else {
    echo "Error creating table.";
}
?>
