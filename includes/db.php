<?php
require_once 'functions.php';
loadEnv(__DIR__ . '/../.env');

class Database {
    private $host;
    private $user;
    private $pass;
    private $dbname;
    public $conn;

    public function __construct() {
        $this->host = getenv('DB_HOST') ?: 'localhost';
        $this->user = getenv('DB_USER') ?: 'root';
        $this->pass = getenv('DB_PASS') ?: '';
        $this->dbname = getenv('DB_NAME') ?: 'pos_db';

        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->dbname);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
        $this->conn->set_charset("utf8");
    }

    public function query($sql, $params = [], $types = "") {
        $stmt = $this->conn->prepare($sql);
        if(!$stmt) {
             die("Query Error: " . $this->conn->error);
        }
        if (!empty($params)) {
             $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt;
    }
    
    public function getLastId() {
        return $this->conn->insert_id;
    }
}

$db = new Database();
session_start();
?>
