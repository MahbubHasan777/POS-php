<?php
require_once __DIR__ . '/../models/Shop.php';
require_once __DIR__ . '/../models/User.php';

class SuperAdminController {
    private $shopModel;
    private $userModel;

    public function __construct() {
        $this->shopModel = new Shop();
        $this->userModel = new User();
    }

    public function getDashboardStats() {
        $stats = $this->shopModel->getStats();
        // Ad-hoc query for user count, encapsulated here
        $users_count = $this->shopModel->query("SELECT COUNT(*) as count FROM users")->get_result()->fetch_assoc()['count'];
        
        return [
            'shops_count' => $stats['shops'],
            'revenue' => $stats['revenue'],
            'users_count' => $users_count
        ];
    }

    public function getRecentShops() {
        return $this->shopModel->query("SELECT shops.*, subscription_plans.name as plan_name 
                                      FROM shops 
                                      JOIN subscription_plans ON shops.subscription_plan_id = subscription_plans.id 
                                      ORDER BY created_at DESC LIMIT 5");
    }

    public function getAllShops($status = null) {
        return $this->shopModel->getAll($status);
    }

    public function updateShopStatus($shop_id, $status) {
        return $this->shopModel->updateStatus($shop_id, $status);
    }
}
?>
