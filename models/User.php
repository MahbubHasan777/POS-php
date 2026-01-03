<?php
require_once 'Core.php';

class User extends Core {
    
    public function login($email, $password) {
        // Query now includes shop status if shop_id is present
        $sql = "SELECT u.*, s.status as shop_status 
                FROM users u 
                LEFT JOIN shops s ON u.shop_id = s.id 
                WHERE u.email = ?";
        
        $result = $this->query($sql, [$email], "s");
        $user = $result->get_result()->fetch_assoc();

        if ($user && password_verify($password, $user['password_hash'])) {
            // Check suspension logic
            if ($user['shop_id'] && $user['shop_status'] === 'suspended') {
                return 'suspended'; // Return specific status to handle in controller
            }
            return $user;
        }
        return false;
    }

    public function create($data) {
        // data: shop_id, role, username, email, password, full_name
        $hash = password_hash($data['password'], PASSWORD_BCRYPT);
        $this->query("INSERT INTO users (shop_id, role, username, email, password_hash, full_name) VALUES (?, ?, ?, ?, ?, ?)", 
            [$data['shop_id'], $data['role'], $data['username'], $data['email'], $hash, $data['full_name']], "issss");
        return $this->db->getLastId();
    }

    public function exists($email, $username) {
        $check = $this->query("SELECT id FROM users WHERE email = ? OR username = ?", [$email, $username], "ss");
        return $check->get_result()->num_rows > 0;
    }

    public function getShopStaff($shop_id) {
        $result = $this->query("SELECT * FROM users WHERE shop_id = ? AND role = 'cashier' ORDER BY created_at DESC", [$shop_id], "i");
        return $result->get_result();
    }

    public function delete($id, $shop_id) {
        $this->query("DELETE FROM users WHERE id = ? AND shop_id = ? AND role = 'cashier'", [$id, $shop_id], "ii");
    }
}
?>
