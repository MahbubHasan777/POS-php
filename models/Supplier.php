<?php
require_once 'Core.php';

class Supplier extends Core {
    public function getAll($shop_id) {
        return $this->query("SELECT * FROM suppliers WHERE shop_id = ?", [$shop_id], "i")->get_result();
    }
    
    public function create($shop_id, $name, $contact) {
        $this->query("INSERT INTO suppliers (shop_id, name, contact_info) VALUES (?, ?, ?)", [$shop_id, $name, $contact], "iss");
    }
}
?>
