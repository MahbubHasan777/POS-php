<?php
require_once 'Core.php';

class Product extends Core {
    
    public function getAll($shop_id, $search = '', $category_id = null) {
        $sql = "SELECT p.*, c.name as cat_name, b.name as brand_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN brands b ON p.brand_id = b.id 
                WHERE p.shop_id = ?";
        $params = [$shop_id];
        $types = "i";

        if ($search) {
            $sql .= " AND (p.name LIKE ? OR p.id = ?)";
            $params[] = "%$search%";
            $params[] = $search;
            $types .= "ss";
        }
        if ($category_id) {
            $sql .= " AND p.category_id = ?";
            $params[] = $category_id;
            $types .= "i";
        }

        $sql .= " ORDER BY p.id DESC";
        return $this->query($sql, $params, $types)->get_result();
    }

    public function get($id, $shop_id) {
        $result = $this->query("SELECT * FROM products WHERE id = ? AND shop_id = ?", [$id, $shop_id], "ii");
        return $result->get_result()->fetch_assoc();
    }

    public function save($data, $shop_id, $id = null) {
        if ($id) {
            $this->query("UPDATE products SET name=?, category_id=?, brand_id=?, buy_price=?, sell_price=?, stock_qty=?, alert_threshold=?, image=? WHERE id=? AND shop_id=?", 
                [$data['name'], $data['category_id'], $data['brand_id'], $data['buy_price'], $data['sell_price'], $data['stock_qty'], $data['alert_threshold'], $data['image'], $id, $shop_id], "siidddissi");
        } else {
             $this->query("INSERT INTO products (shop_id, name, category_id, brand_id, buy_price, sell_price, stock_qty, alert_threshold, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [$shop_id, $data['name'], $data['category_id'], $data['brand_id'], $data['buy_price'], $data['sell_price'], $data['stock_qty'], $data['alert_threshold'], $data['image']], "isiidddis");
        }
    }

    public function searchForPos($shop_id, $query, $filters = []) {
        $sql = "SELECT id, name, sell_price, stock_qty, image FROM products WHERE shop_id = ? AND stock_qty > 0";
        $params = [$shop_id];
        $types = "i";

        if (!empty($query)) {
            $sql .= " AND (name LIKE ? OR id = ?)";
            $params[] = "%$query%";
            $params[] = $query;
            $types .= "ss";
        }

        if (!empty($filters['category_id'])) {
            $sql .= " AND category_id = ?";
            $params[] = $filters['category_id'];
            $types .= "i";
        }

        if (!empty($filters['brand_id'])) {
            $sql .= " AND brand_id = ?";
            $params[] = $filters['brand_id'];
            $types .= "i";
        }

        if (!empty($filters['sort'])) {
            switch ($filters['sort']) {
                case 'price_asc': $sql .= " ORDER BY sell_price ASC"; break;
                case 'price_desc': $sql .= " ORDER BY sell_price DESC"; break;
                case 'name_asc': $sql .= " ORDER BY name ASC"; break;
                default: $sql .= " ORDER BY id DESC";
            }
        } else {
            $sql .= " ORDER BY id DESC";
        }

        $sql .= " LIMIT 20";
        return $this->query($sql, $params, $types)->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    public function decreaseStock($id, $qty) {
        $this->query("UPDATE products SET stock_qty = stock_qty - ? WHERE id = ?", [$qty, $id], "ii");
    }
}
?>
