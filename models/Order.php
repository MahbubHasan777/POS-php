<?php
require_once 'Core.php';

class Order extends Core {
    
    public function create($shop_id, $cashier_id, $totals, $payment_method) {
        $this->query("INSERT INTO orders (shop_id, cashier_id, total_amount, tax_amount, grand_total, payment_method) VALUES (?, ?, ?, ?, ?, ?)",
            [$shop_id, $cashier_id, $totals['sub'], $totals['tax'], $totals['grand'], $payment_method], "iiddds");
        return $this->db->getLastId();
    }

    public function addItems($order_id, $items) {
        foreach($items as $item) {
            $line_total = $item['price'] * $item['qty'];
            $this->query("INSERT INTO order_items (order_id, product_id, quantity, unit_price, subtotal) VALUES (?, ?, ?, ?, ?)",
                [$order_id, $item['id'], $item['qty'], $item['price'], $line_total], "ididd");
        }
    }

    public function get($id, $shop_id) {
        $stmt = $this->query("SELECT * FROM orders WHERE id = ? AND shop_id = ?", [$id, $shop_id], "ii");
        return $stmt->get_result()->fetch_assoc();
    }

    public function getItems($order_id) {
        $stmt = $this->query("SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?", [$order_id], "i");
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getRecent($shop_id, $limit = 5) {
        $stmt = $this->query("SELECT orders.*, users.username as cashier_name FROM orders 
                             JOIN users ON orders.cashier_id = users.id 
                             WHERE orders.shop_id = ? 
                             ORDER BY created_at DESC LIMIT ?", [$shop_id, $limit], "ii");
        return $stmt->get_result();
    }

    public function getReport($shop_id, $start, $end) {
        $stmt = $this->query("SELECT * FROM orders WHERE shop_id = ? AND DATE(created_at) BETWEEN ? AND ? ORDER BY created_at DESC", 
            [$shop_id, $start, $end], "iss");
        return $stmt->get_result();
    }
    public function getReportWithProfit($shop_id, $start, $end) {
        $sql = "SELECT o.*, u.username as cashier_name,
                SUM((oi.unit_price - p.buy_price) * oi.quantity) as profit 
                FROM orders o 
                JOIN users u ON o.cashier_id = u.id 
                LEFT JOIN order_items oi ON o.id = oi.order_id 
                LEFT JOIN products p ON oi.product_id = p.id 
                WHERE o.shop_id = ? AND o.status != 'returned' AND DATE(o.created_at) BETWEEN ? AND ? 
                GROUP BY o.id 
                ORDER BY o.created_at DESC";
                
        $stmt = $this->query($sql, [$shop_id, $start, $end], "iss");
        return $stmt->get_result();
    }

    public function returnOrder($order_id, $shop_id) {
        $order = $this->get($order_id, $shop_id);
        if (!$order || $order['status'] === 'returned') return false;

        // 1. Update Status
        $this->query("UPDATE orders SET status = 'returned' WHERE id = ?", [$order_id], "i");

        // 2. Restore Stock
        $items = $this->getItems($order_id);
        foreach ($items as $item) {
            $this->query("UPDATE products SET stock_qty = stock_qty + ? WHERE id = ?", [$item['quantity'], $item['product_id']], "ii");
        }
        return true;
    }
    public function getBestSellingItem($shop_id, $start, $end) {
        $sql = "SELECT p.name, SUM(oi.quantity) as total_qty 
                FROM order_items oi 
                JOIN orders o ON oi.order_id = o.id 
                JOIN products p ON oi.product_id = p.id 
                WHERE o.shop_id = ? AND DATE(o.created_at) BETWEEN ? AND ? 
                GROUP BY p.id 
                ORDER BY total_qty DESC 
                LIMIT 1";
                
        $stmt = $this->query($sql, [$shop_id, $start, $end], "iss");
        $result = $stmt->get_result()->fetch_assoc();
        return $result ? $result : ['name' => 'None', 'total_qty' => 0];
    }
    public function getTopSellingItems($shop_id, $start, $end, $limit = 5) {
        $sql = "SELECT p.name, SUM(oi.quantity) as total_qty, SUM(oi.subtotal) as total_revenue
                FROM order_items oi 
                JOIN orders o ON oi.order_id = o.id 
                JOIN products p ON oi.product_id = p.id 
                WHERE o.shop_id = ? AND DATE(o.created_at) BETWEEN ? AND ? 
                GROUP BY p.id 
                ORDER BY total_qty DESC 
                LIMIT ?";
        $stmt = $this->query($sql, [$shop_id, $start, $end, $limit], "issi");
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getDailySales($shop_id, $start, $end) {
        $sql = "SELECT DATE(created_at) as date, SUM(grand_total) as revenue 
                FROM orders 
                WHERE shop_id = ? AND DATE(created_at) BETWEEN ? AND ? 
                GROUP BY DATE(created_at) 
                ORDER BY date ASC";
        $stmt = $this->query($sql, [$shop_id, $start, $end], "iss");
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>
