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
}
?>
