<?php
require_once 'Core.php';

class Shop extends Core {
    
    public function create($name, $plan_id = 1) {
        $this->query("INSERT INTO shops (name, subscription_plan_id) VALUES (?, ?)", [$name, $plan_id], "si");
        return $this->db->getLastId();
    }

    public function getAll($status = null) {
        $sql = "SELECT shops.*, subscription_plans.name as plan_name, users.email as owner_email 
                FROM shops 
                JOIN subscription_plans ON shops.subscription_plan_id = subscription_plans.id 
                LEFT JOIN users ON users.shop_id = shops.id AND users.role = 'shop_admin'";
        
        if ($status) {
            $sql .= " WHERE shops.status = ?";
            return $this->query($sql, [$status], "s")->get_result();
        }
        
        return $this->query($sql)->get_result();
    }

    public function updateStatus($id, $status) {
        $this->query("UPDATE shops SET status = ? WHERE id = ?", [$status, $id], "si");
    }

    public function getPaymentHistory($shop_id) {
        $sql = "SELECT sp.*, p.name as plan_name 
                FROM subscription_payments sp 
                JOIN subscription_plans p ON sp.plan_id = p.id 
                WHERE sp.shop_id = ? 
                ORDER BY sp.payment_date DESC";
        return $this->query($sql, [$shop_id], "i")->get_result();
    }

    public function getStats($id = null) {
        if ($id) {
            // Specific Shop Stats
            // Intentionally left basic for now
             return []; 
        } else {
            // Global Stats
            $shops = $this->query("SELECT COUNT(*) as count FROM shops")->get_result()->fetch_assoc()['count'];
            $revenue = $this->query("SELECT SUM(price) as total FROM subscription_plans JOIN shops ON shops.subscription_plan_id = subscription_plans.id")->get_result()->fetch_assoc()['total'] ?? 0;
            return ['shops' => $shops, 'revenue' => $revenue];
        }
    }
}
?>
