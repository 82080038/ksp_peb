<?php
// Environment loader for Koperasi App
class Environment {
    private static $loaded = false;
    private static $vars = [];
    
    public static function load($path = null) {
        if (self::$loaded) return;
        
        $path = $path ?: __DIR__ . '/../.env';
        
        if (!file_exists($path)) {
            throw new Exception("Environment file not found: $path");
        }
        
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                // Remove quotes if present
                $value = trim($value, '"\'');
                
                self::$vars[$key] = $value;
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
        }
        
        self::$loaded = true;
    }
    
    public static function get($key, $default = null) {
        self::load();
        return self::$vars[$key] ?? $default;
    }
    
    public static function set($key, $value) {
        self::$vars[$key] = $value;
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}

// Auto-load environment
Environment::load();
