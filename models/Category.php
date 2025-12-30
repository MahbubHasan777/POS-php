<?php
require_once 'Core.php';

class Category extends Core {
    public function getAll($shop_id) {
        return $this->query("SELECT * FROM categories WHERE shop_id = ? ORDER BY id DESC", [$shop_id], "i")->get_result();
    }
    
    public function create($shop_id, $name) {
        $this->query("INSERT INTO categories (shop_id, name) VALUES (?, ?)", [$shop_id, $name], "is");
    }
    
    public function delete($id, $shop_id) {
        $this->query("DELETE FROM categories WHERE id = ? AND shop_id = ?", [$id, $shop_id], "ii");
    }
}

class Brand extends Core {
    public function getAll($shop_id) {
        return $this->query("SELECT * FROM brands WHERE shop_id = ? ORDER BY id DESC", [$shop_id], "i")->get_result();
    }
    
    public function create($shop_id, $name) {
        $this->query("INSERT INTO brands (shop_id, name) VALUES (?, ?)", [$shop_id, $name], "is");
    }
    
    public function delete($id, $shop_id) {
        $this->query("DELETE FROM brands WHERE id = ? AND shop_id = ?", [$id, $shop_id], "ii");
    }
}
?>
