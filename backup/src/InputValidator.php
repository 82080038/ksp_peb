<?php
// Input validation and sanitization utility
class InputValidator {
    
    public static function get($key, $default = null, $type = 'string', $sanitize = true) {
        $value = $_GET[$key] ?? $_POST[$key] ?? $default;
        
        if ($sanitize) {
            $value = Security::sanitize($value);
        }
        
        return self::validateType($value, $type, $default);
    }
    
    public static function post($key, $default = null, $type = 'string', $sanitize = true) {
        $value = $_POST[$key] ?? $default;
        
        if ($sanitize) {
            $value = Security::sanitize($value);
        }
        
        return self::validateType($value, $type, $default);
    }
    
    public static function getJSON() {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }
        
        return array_map([Security::class, 'sanitize'], $data);
    }
    
    private static function validateType($value, $type, $default) {
        switch ($type) {
            case 'int':
            case 'integer':
                return is_numeric($value) ? intval($value) : $default;
            case 'float':
            case 'double':
                return is_numeric($value) ? floatval($value) : $default;
            case 'bool':
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'email':
                return Security::validateEmail($value) ? $value : $default;
            case 'phone':
                return Security::validatePhone($value) ? $value : $default;
            case 'alpha':
                return ctype_alpha($value) ? $value : $default;
            case 'alnum':
                return ctype_alnum($value) ? $value : $default;
            case 'string':
            default:
                return is_string($value) ? $value : $default;
        }
    }
    
    public static function validateRequired($fields, $data = null) {
        $data = $data ?? $_POST;
        $missing = [];
        
        foreach ($fields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $missing[] = $field;
            }
        }
        
        return $missing;
    }
    
    public static function validateMinLength($value, $minLength) {
        return strlen($value) >= $minLength;
    }
    
    public static function validateMaxLength($value, $maxLength) {
        return strlen($value) <= $maxLength;
    }
    
    public static function validateDate($value, $format = 'Y-m-d') {
        $date = DateTime::createFromFormat($format, $value);
        return $date && $date->format($format) === $value;
    }
    
    public static function validateNumericRange($value, $min = null, $max = null) {
        if (!is_numeric($value)) {
            return false;
        }
        
        $num = floatval($value);
        
        if ($min !== null && $num < $min) {
            return false;
        }
        
        if ($max !== null && $num > $max) {
            return false;
        }
        
        return true;
    }
}
