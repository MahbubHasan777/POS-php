<?php
require_once __DIR__ . '/functions.php';
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

        try {
            $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->dbname);
            
            if ($this->conn->connect_error) {
                throw new Exception($this->conn->connect_error);
            }
            $this->conn->set_charset("utf8");
        } catch (Exception $e) {
            die("<div style='font-family: sans-serif; text-align: center; padding: 50px;'>
                <h1 style='color: #ef4444;'>Database Connection Failed</h1>
                <p>Could not connect to the database. Error: " . $e->getMessage() . "</p>
                <div style='background: #f3f4f6; padding: 20px; display: inline-block; border-radius: 8px; text-align: left;'>
                    <strong>Possible Solutions:</strong>
                    <ul>
                        <li>Is your <strong>XAMPP MySQL</strong> server running?</li>
                        <li>Check if port 3306 is available.</li>
                        <li>Verify credentials in <code>.env</code> file.</li>
                    </ul>
                </div>
            </div>");
        }
    }

    public function reconnect() {
        $this->conn->close();
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->dbname);
        if ($this->conn->connect_error) {
            die("Re-connection failed: " . $this->conn->connect_error);
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
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
