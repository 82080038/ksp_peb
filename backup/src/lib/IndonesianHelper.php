<?php
// Helper Functions untuk Bahasa Indonesia
// Fungsi-fungsi untuk konsolidasi bahasa Indonesia di seluruh aplikasi

class IndonesianHelper {
    private static $translations = null;
    
    /**
     * Inisialisasi translations
     */
    private static function initTranslations() {
        if (self::$translations === null) {
            self::$translations = require __DIR__ . '/indonesian.php';
        }
    }
    
    /**
     * Mendapatkan teks dalam bahasa Indonesia
     */
    public static function get($key, $default = null) {
        self::initTranslations();
        
        $keys = explode('.', $key);
        $value = self::$translations;
        
        foreach ($keys as $k) {
            if (isset($value[$k])) {
                $value = $value[$k];
            } else {
                return $default ?: $key;
            }
        }
        
        return $value;
    }
    
    /**
     * Mendapatkan pesan sukses
     */
    public static function success($key, $params = []) {
        $message = self::get($key);
        
        if (!empty($params)) {
            foreach ($params as $placeholder => $value) {
                $message = str_replace('{' . $placeholder . '}', $value, $message);
            }
        }
        
        return $message;
    }
    
    /**
     * Mendapatkan pesan error
     */
    public static function error($key, $params = []) {
        $message = self::get($key);
        
        if (!empty($params)) {
            foreach ($params as $placeholder => $value) {
                $message = str_replace('{' . $placeholder . '}', $value, $message);
            }
        }
        
        return $message;
    }
    
    /**
     * Mendapatkan pesan konfirmasi
     */
    public static function confirm($key, $params = []) {
        $message = self::get($key);
        
        if (!empty($params)) {
            foreach ($params as $placeholder => $value) {
                $message = str_replace('{' . $placeholder . '}', $value, $message);
            }
        }
        
        return $message;
    }
    
    /**
     * Mendapatkan label form
     */
    public static function label($key) {
        return self::get($key);
    }
    
    /**
     * Mendapatkan tooltip
     */
    public static function tooltip($key) {
        return self::get('tooltip_' . $key);
    }
    
    /**
     * Mendapatkan pesan validasi
     */
    public static function validation($key, $params = []) {
        $message = self::get($key);
        
        if (!empty($params)) {
            foreach ($params as $placeholder => $value) {
                $message = str_replace('{' . $placeholder . '}', $value, $message);
            }
        }
        
        return $message;
    }
    
    /**
     * Mendapatkan pesan loading
     */
    public static function loading($key) {
        return self::get($key);
    }
    
    /**
     * Format pesan dengan parameter
     */
    public static function format($key, $params = []) {
        $message = self::get($key);
        
        if (!empty($params)) {
            foreach ($params as $placeholder => $value) {
                $message = str_replace('{' . $placeholder . '}', $value, $message);
            }
        }
        
        return $message;
    }
    
    /**
     * Mendapatkan semua translations
     */
    public static function getAll() {
        self::initTranslations();
        return self::$translations;
    }
    
    /**
     * Generate JavaScript translations
     */
    public static function generateJSTranslations() {
        self::initTranslations();
        
        $js = "// Indonesian Translations\n";
        $js .= "window.Indonesian = {\n";
        
        foreach (self::$translations as $key => $value) {
            $js .= "    '{$key}': " . json_encode($value) . ",\n";
        }
        
        $js = rtrim($js, ",\n") . "\n};\n";
        
        return $js;
    }
}

// Shortcut functions
if (!function_exists('__')) {
    function __($key, $default = null) {
        return IndonesianHelper::get($key, $default);
    }
}

if (!function_exists('success_msg')) {
    function success_msg($key, $params = []) {
        return IndonesianHelper::success($key, $params);
    }
}

if (!function_exists('error_msg')) {
    function error_msg($key, $params = []) {
        return IndonesianHelper::error($key, $params);
    }
}

if (!function_exists('confirm_msg')) {
    function confirm_msg($key, $params = []) {
        return IndonesianHelper::confirm($key, $params);
    }
}

if (!function_exists('label')) {
    function label($key) {
        return IndonesianHelper::label($key);
    }
}

if (!function_exists('tooltip')) {
    function tooltip($key) {
        return IndonesianHelper::tooltip($key);
    }
}

if (!function_exists('loading_msg')) {
    function loading_msg($key) {
        return IndonesianHelper::loading($key);
    }
}
