<?php
require_once 'Core.php';

class SubscriptionPlan extends Core {
    
    public function getAll() {
        return $this->query("SELECT * FROM subscription_plans")->get_result();
    }

    public function get($id) {
        return $this->query("SELECT * FROM subscription_plans WHERE id = ?", [$id], "i")->get_result()->fetch_assoc();
    }

    public function create($name, $price, $max_sales) {
        $this->query("INSERT INTO subscription_plans (name, price, max_sales) VALUES (?, ?, ?)", 
                     [$name, $price, $max_sales], "sdi");
    }

    public function update($id, $name, $price, $max_sales) {
        $this->query("UPDATE subscription_plans SET name = ?, price = ?, max_sales = ? WHERE id = ?", 
                     [$name, $price, $max_sales, $id], "sdii");
    }

    public function delete($id) {
        // Only delete if no shops are using it, or handle constraints
        // For simplicity, we might simple error if used, or use cascading delete (dangerous)
        // Let's check first
        $check = $this->query("SELECT COUNT(*) as count FROM shops WHERE subscription_plan_id = ?", [$id], "i")->get_result()->fetch_assoc()['count'];
        if ($check > 0) {
            return false; // Cannot delete, in use
        }
        $this->query("DELETE FROM subscription_plans WHERE id = ?", [$id], "i");
        return true;
    }
}
?>
