<?php
// Core application class
class App {
    private static $instance = null;
    private $peopleDB;
    private $coopDB;
    private $addressDB;
    private $config;
    private $db;
    private $coopDb;
    private $addressDb;
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // Load configuration from environment
        $this->config = [
            'db_host' => Environment::get('DB_HOST', 'localhost'),
            'db_user' => Environment::get('DB_USER', 'root'),
            'db_pass' => Environment::get('DB_PASS', 'root'),
            'db_name' => Environment::get('DB_NAME', 'coop_db')
        ];
        $this->initDatabase();
    }
    
    private function initDatabase() {
        $this->peopleDB = $this->getPeopleDB();
        $this->coopDB = $this->getCoopDB();
        $this->addressDB = $this->getAddressDB();
    }
    
    public function getPeopleDB() {
        if (!$this->db) {
            $this->db = new PDO(
                "mysql:host={$this->config['db_host']};dbname=people_db",
                $this->config['db_user'],
                $this->config['db_pass'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        }
        return $this->db;
    }
    
    public function getAddressDB() {
        if (!$this->addressDb) {
            $this->addressDb = new PDO(
                "mysql:host={$this->config['db_host']};dbname=alamat_db",
                $this->config['db_user'],
                $this->config['db_pass'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        }
        return $this->addressDb;
    }
    
    public function getDB() {
        return $this->getPeopleDB();
    }
    
    public function getCoopDB() {
        if (!$this->coopDb) {
            $this->coopDb = new PDO(
                "mysql:host={$this->config['db_host']};dbname=coop_db",
                $this->config['db_user'],
                $this->config['db_pass'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        }
        return $this->coopDb;
    }
    
    public function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_name(Environment::get('SESSION_NAME', 'ksp_session'));
            
            // Configure session cookie
            $secure = Environment::get('APP_ENV') === 'production' && 
                     (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on');
            
            session_set_cookie_params([
                'lifetime' => intval(Environment::get('SESSION_LIFETIME', 1440)) * 60,
                'path' => Environment::get('SESSION_PATH', '/'),
                'secure' => $secure,
                'httponly' => filter_var(Environment::get('SESSION_HTTPONLY', 'true'), FILTER_VALIDATE_BOOLEAN),
                'samesite' => Environment::get('SESSION_SAMESITE', 'Strict')
            ]);
            session_start();
        }
    }
    
    public function getConfig($key, $default = null) {
        try {
            $stmt = $this->coopDB->prepare("SELECT value FROM configs WHERE key_name = ?");
            $stmt->execute([$key]);
            $result = $stmt->fetch();
            return $result ? $result['value'] : $default;
        } catch (Exception $e) {
            return $default;
        }
    }
    
    public function setConfig($key, $value, $description = '') {
        try {
            $stmt = $this->coopDB->prepare("
                INSERT INTO configs (key_name, value, description) 
                VALUES (?, ?, ?) 
                ON DUPLICATE KEY UPDATE value = ?, description = ?, updated_at = CURRENT_TIMESTAMP
            ");
            return $stmt->execute([$key, $value, $description, $value, $description]);
        } catch (Exception $e) {
            return false;
        }
    }
}
