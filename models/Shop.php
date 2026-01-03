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

    public function checkSubscriptionLimit($shop_id) {
        $shop = $this->query("SELECT s.cycle_start_date, p.max_sales, s.rollover_sales 
                              FROM shops s 
                              JOIN subscription_plans p ON s.subscription_plan_id = p.id 
                              WHERE s.id = ?", [$shop_id], "i")->get_result()->fetch_assoc();
        
        if ($shop['max_sales'] == -1) return true; // Unlimited

        $count = $this->query("SELECT COUNT(*) as sales FROM orders WHERE shop_id = ? AND created_at >= ?", 
                              [$shop_id, $shop['cycle_start_date']], "is")->get_result()->fetch_assoc()['sales'];

        $effective_limit = $shop['max_sales'] + $shop['rollover_sales'];
        
        return $count < $effective_limit;
    }

    public function renewSubscription($shop_id, $plan_id, $amount, $method) {
        // 1. Calculate Rollover from current state
        $current = $this->query("SELECT s.cycle_start_date, p.max_sales, s.rollover_sales 
                                 FROM shops s 
                                 JOIN subscription_plans p ON s.subscription_plan_id = p.id 
                                 WHERE s.id = ?", [$shop_id], "i")->get_result()->fetch_assoc();
        
        $sales_done = $this->query("SELECT COUNT(*) as count FROM orders WHERE shop_id = ? AND created_at >= ?", 
                                   [$shop_id, $current['cycle_start_date']], "is")->get_result()->fetch_assoc()['count'];
        
        $rollover = 0;
        if ($current['max_sales'] != -1) {
            $current_limit = $current['max_sales'] + $current['rollover_sales'];
            $remaining = max(0, $current_limit - $sales_done);
            $rollover = $remaining;
        }

        // 2. Record Payment
        $this->query("INSERT INTO subscription_payments (shop_id, plan_id, amount, payment_status, payment_method) VALUES (?, ?, ?, 'paid', ?)", 
                     [$shop_id, $plan_id, $amount, $method], "iids");
        
        // 3. Update Shop Plan, Rollover, and Reset Cycle
        $this->query("UPDATE shops SET subscription_plan_id = ?, cycle_start_date = CURRENT_TIMESTAMP, rollover_sales = ?, status = 'active' WHERE id = ?", 
                     [$plan_id, $rollover, $shop_id], "iii");
    }

    public function getStats($id = null) {
        if ($id) {
            // Specific Shop Stats for Dashboard
            $shop = $this->query("SELECT s.cycle_start_date, p.max_sales, s.rollover_sales, p.name as plan_name 
                                  FROM shops s 
                                  JOIN subscription_plans p ON s.subscription_plan_id = p.id 
                                  WHERE s.id = ?", [$id], "i")->get_result()->fetch_assoc();
            
            $sales_count = $this->query("SELECT COUNT(*) as count FROM orders WHERE shop_id = ? AND created_at >= ?", 
                                        [$id, $shop['cycle_start_date']], "is")->get_result()->fetch_assoc()['count'];
            
            $total_limit = ($shop['max_sales'] == -1) ? -1 : ($shop['max_sales'] + $shop['rollover_sales']);

            return [
                'plan_name' => $shop['plan_name'],
                'max_sales' => $total_limit, // Return effective limit for display
                'base_limit' => $shop['max_sales'],
                'rollover' => $shop['rollover_sales'],
                'current_sales' => $sales_count
            ];
        } else {
            // Global Stats for Super Admin
            $shops = $this->query("SELECT COUNT(*) as count FROM shops")->get_result()->fetch_assoc()['count'];
            // Fix: Calculate revenue from actual payments
            $revenue = $this->query("SELECT SUM(amount) as total FROM subscription_payments WHERE payment_status = 'paid'")->get_result()->fetch_assoc()['total'] ?? 0;
            return ['shops' => $shops, 'revenue' => $revenue];
        }
    }
}
?>
