<?php
require_once __DIR__ . '/../models/Core.php';

class ShopAdminController {
    private $core;

    public function __construct() {
        $this->core = new Core();
    }

    public function getDashboardStats($shop_id) {
        $today = date('Y-m-d');
        
        // Sales
        $sales_stmt = $this->core->query("SELECT SUM(grand_total) as total FROM orders WHERE shop_id = ? AND DATE(created_at) = ?", [$shop_id, $today], "is");
        $todays_sales = $sales_stmt->get_result()->fetch_assoc()['total'] ?? 0.00;

        // Low Stock
        $low_stock_stmt = $this->core->query("SELECT COUNT(*) as count FROM products WHERE shop_id = ? AND stock_qty <= alert_threshold", [$shop_id], "i");
        $low_stock_count = $low_stock_stmt->get_result()->fetch_assoc()['count'];

        // Total Products
        $prod_stmt = $this->core->query("SELECT COUNT(*) as count FROM products WHERE shop_id = ?", [$shop_id], "i");
        $total_products = $prod_stmt->get_result()->fetch_assoc()['count'];

        return [
            'todays_sales' => $todays_sales,
            'low_stock_count' => $low_stock_count,
            'total_products' => $total_products
        ];
    }

    public function getRecentSales($shop_id, $limit = 5) {
        $limit = (int)$limit;
        $sql = "SELECT orders.*, users.username as cashier_name FROM orders 
                JOIN users ON orders.cashier_id = users.id 
                WHERE orders.shop_id = ? 
                ORDER BY created_at DESC LIMIT ?";
        
        return $this->core->query($sql, [$shop_id, $limit], "ii")->get_result();
    }
}
?>
