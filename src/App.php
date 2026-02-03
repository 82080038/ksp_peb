<?php
// Core application class
class App {
    private static $instance = null;
    private $peopleDB;
    private $coopDB;
    private $addressDB;
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->initDatabase();
    }
    
    private function initDatabase() {
        $this->peopleDB = getPeopleDB();
        $this->coopDB = getCoopDB();
        $this->addressDB = getAddressDB();
    }
    
    public function getPeopleDB() {
        return $this->peopleDB;
    }
    
    public function getCoopDB() {
        return $this->coopDB;
    }
    
    public function getAddressDB() {
        return $this->addressDB;
    }
    
    public function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_name($_ENV['SESSION_NAME'] ?? 'ksp_session');
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
