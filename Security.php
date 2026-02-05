<?php
// Security utilities for Koperasi App
class Security {
    
    // Generate CSRF token
    public static function generateCSRFToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    // Verify CSRF token
    public static function verifyCSRFToken($token) {
        if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            return false;
        }
        return true;
    }
    
    // Sanitize input
    public static function sanitize($input) {
        if (is_array($input)) {
            return array_map([self::class, 'sanitize'], $input);
        }
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
    
    // Validate phone number format
    public static function validatePhone($phone) {
        return preg_match('/^08[0-9]{8,12}$/', $phone);
    }
    
    // Validate email format
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    // Rate limiting
    public static function checkRateLimit($action, $limit = 5, $window = 300) {
        $key = $action . '_' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
        
        if (!isset($_SESSION['rate_limit'][$key])) {
            $_SESSION['rate_limit'][$key] = ['count' => 0, 'start' => time()];
        }
        
        $data = $_SESSION['rate_limit'][$key];
        
        // Reset window if expired
        if (time() - $data['start'] > $window) {
            $_SESSION['rate_limit'][$key] = ['count' => 1, 'start' => time()];
            return true;
        }
        
        // Check limit
        if ($data['count'] >= $limit) {
            return false;
        }
        
        $_SESSION['rate_limit'][$key]['count']++;
        return true;
    }
    
    // Password strength validation
    public static function validatePasswordStrength($password) {
        $length = strlen($password) >= 8;
        $uppercase = preg_match('/[A-Z]/', $password);
        $lowercase = preg_match('/[a-z]/', $password);
        $number = preg_match('/[0-9]/', $password);
        $special = preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password);
        
        return $length && $uppercase && $lowercase && $number && $special;
    }
    
    // XSS protection
    public static function escape($string) {
        return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
    
    // SQL injection protection (use prepared statements instead)
    public static function escapeIdentifier($identifier) {
        return preg_replace('/[^a-zA-Z0-9_]/', '', $identifier);
    }
}
