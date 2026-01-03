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
            [$data['shop_id'], $data['role'], $data['username'], $data['email'], $hash, $data['full_name']], "isssss");
        return $this->db->getLastId();
    }

    public function exists($email, $username, $exclude_id = null) {
        $sql = "SELECT id FROM users WHERE (email = ? OR username = ?)";
        $params = [$email, $username];
        $types = "ss";

        if ($exclude_id) {
            $sql .= " AND id != ?";
            $params[] = $exclude_id;
            $types .= "i";
        }

        $check = $this->query($sql, $params, $types);
        return $check->get_result()->num_rows > 0;
    }

    public function getShopStaff($shop_id) {
        $result = $this->query("SELECT * FROM users WHERE shop_id = ? AND role = 'cashier' ORDER BY created_at DESC", [$shop_id], "i");
        return $result->get_result();
    }

    public function delete($id, $shop_id) {
        // Check for dependencies (Foreign Key Constraints)
        $check = $this->query("SELECT id FROM orders WHERE cashier_id = ?", [$id], "i");
        if ($check->get_result()->num_rows > 0) {
            return "Cannot delete cashier: This user has processed sales. Deleting them would corrupt sales history. Consider changing their password instead.";
        }

        $this->query("DELETE FROM users WHERE id = ? AND shop_id = ? AND role = 'cashier'", [$id, $shop_id], "ii");
        return true;
    }
    public function getById($id, $shop_id) {
        return $this->query("SELECT * FROM users WHERE id = ? AND shop_id = ?", [$id, $shop_id], "ii")->get_result()->fetch_assoc();
    }

    public function update($id, $shop_id, $data) {
        $sql = "UPDATE users SET username = ?, email = ?, full_name = ? WHERE id = ? AND shop_id = ?";
        $params = [$data['username'], $data['email'], $data['full_name'], $id, $shop_id];
        $types = "sssii";
        
        if (!empty($data['password'])) {
            $hash = password_hash($data['password'], PASSWORD_BCRYPT);
            $sql = "UPDATE users SET username = ?, email = ?, full_name = ?, password_hash = ? WHERE id = ? AND shop_id = ?";
            $params = [$data['username'], $data['email'], $data['full_name'], $hash, $id, $shop_id];
            $types = "ssssii";
        }
        
        $this->query($sql, $params, $types);
    }
}
?>
