<?php
require_once __DIR__ . '/../includes/db.php'; // Ensure DB and Session are started

class Core {
    protected $db;
    protected $conn;

    public function __construct() {
        global $db; 
        $this->db = $db;
        $this->conn = $this->db->conn;
    }


    public function query($sql, $params = [], $types = "") {
        if (!$this->db->conn->ping()) {
            $this->db->reconnect();
        }
        return $this->db->query($sql, $params, $types);
    }
}
?>
