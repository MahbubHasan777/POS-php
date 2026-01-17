<?php
require_once __DIR__ . '/../models/Order.php';

class ReportController {
    private $orderModel;

    public function __construct() {
        $this->orderModel = new Order();
    }

    public function getSalesReports($shop_id, $start_date, $end_date) {
        $orders = $this->orderModel->getReport($shop_id, $start_date, $end_date);
        
        $data = [];
        $total_revenue = 0;
        $total_profit = 0;

        while($row = $orders->fetch_assoc()) {
            $total_revenue += $row['grand_total'];
            
            $profit = $this->calculateOrderProfit($row['id']);
            $total_profit += $profit;

            $data[] = $row;
        }

        return [
            'orders' => $data,
            'total_revenue' => $total_revenue,
            'total_profit' => $total_profit
        ];
    }

    private function calculateOrderProfit($order_id) {
        $items = $this->orderModel->query(
            "SELECT oi.quantity, p.buy_price, p.sell_price 
             FROM order_items oi 
             JOIN products p ON oi.product_id = p.id 
             WHERE oi.order_id = ?", 
            [$order_id], 
            "i"
        );
        
        $profit = 0;
        while($item = $items->get_result()->fetch_assoc()) {
            $profit += ($item['sell_price'] - $item['buy_price']) * $item['quantity'];
        }
        return $profit;
    }
}
?>
