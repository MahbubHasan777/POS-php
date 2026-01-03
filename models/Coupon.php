<?php
require_once 'Core.php';

class Coupon extends Core {
    public function getAll($shop_id) {
        return $this->query("SELECT * FROM coupons WHERE shop_id = ? ORDER BY expiry_date DESC", [$shop_id], "i")->get_result();
    }
    
    public function create($data) {
        $this->query("INSERT INTO coupons (shop_id, code, discount_type, discount_value, min_order_amount, max_discount_amount, expiry_date) VALUES (?, ?, ?, ?, ?, ?, ?)",
        [$data['shop_id'], $data['code'], $data['type'], $data['value'], $data['min'], $data['max'], $data['expiry']], "issddds");
    }
    public function getById($id) {
        return $this->query("SELECT * FROM coupons WHERE id = ?", [$id], "i")->get_result()->fetch_assoc();
    }

    public function update($id, $data) {
        $this->query("UPDATE coupons SET code = ?, discount_type = ?, discount_value = ?, min_order_amount = ?, max_discount_amount = ?, expiry_date = ? WHERE id = ?",
        [$data['code'], $data['type'], $data['value'], $data['min'], $data['max'], $data['expiry'], $id], "ssdddsi");
    }

    public function delete($id) {
        $this->query("DELETE FROM coupons WHERE id = ?", [$id], "i");
    }
}
?>
