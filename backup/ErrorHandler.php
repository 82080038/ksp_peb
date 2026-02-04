<?php
// Enhanced Error Handler untuk Aplikasi Koperasi
class ErrorHandler {
    private static $instance = null;
    private static $errors = [];
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public static function logError($error, $context = '', $type = 'error') {
        self::getInstance()->addError($error, $context, $type);
    }
    
    public static function addError($error, $context = '', $type = 'error') {
        self::getInstance()->addError($error, $context, $type);
    }
    
    public static function getErrors() {
        return self::getInstance()->errors;
    }
    
    public static function clearErrors() {
        self::getInstance()->errors = [];
    }
    
    public static function getErrorSummary() {
        $errors = self::getInstance()->errors;
        $summary = [];
        
        foreach ($errors as $error) {
            $summary[] = array_merge($summary, [
                'error' => $error['error'],
                'context' => $error['context'],
                'type' => $error['type'],
                'timestamp' => $error['timestamp'] ?? date('Y-m-d H:i:s')
            ]);
        }
        
        return $summary;
    }
    
    public static function hasErrors() {
        return !empty(self::getInstance()->errors);
    }
    
    public static function getErrorCount() {
        return count(self::getInstance()->errors);
    }
    
    public static function getRecentErrors($limit = 10) {
        return array_slice(self::getInstance()->errors, -$limit);
    }
    
    public static function clearOldErrors() {
        $errors = self::getInstance()->errors;
        $oldCount = count($errors);
        self::$errors = array_slice($errors, $oldCount);
    }
    
    public static function getErrorStats() {
        $errors = self::getInstance()->errors;
        $stats = [
            'total' => count($errors),
            'recent' => array_slice($errors, -5),
            'by_type' => array_count_values(array_column($errors, 'type')),
            'by_context' => array_count_values(array_column($errors, 'context'))
        ];
        
        return $stats;
    }
    
    public static function exportErrors() {
        $errors = self::getInstance()->errors;
        $filename = __DIR__ . '/logs/errors_' . date('Y-m-d') . '.json';
        
        $data = [
            'timestamp' => date('Y-m-d H:i:s'),
            'errors' => $errors,
            'total' => count($errors),
            'recent' => array_slice($errors, -5)
        ];
        
        file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT));
        return $filename;
    }
    
    public static function displayErrors() {
        $errors = self::getRecentErrors();
        if (empty($errors)) {
            echo "✅ Tidak ada error yang perlu ditampilkan";
            return;
        }
        
        echo "<h3>🔍 Error Log Terdeteksi:</h3>";
        echo "<table class='table table-striped'>";
        echo "<thead><tr><th>Waktu</th><th>Context</th><th>Tipe</th><th>Pesan</th></tr></thead>";
        
        foreach ($errors as $error) {
            echo "<tr>";
            echo "<td>" . ($error['timestamp'] ?? 'N/A') . "</td>";
            echo "<td>" . ($error['context'] ?? 'N/A') . "</td>";
            echo "<td>" . ($error['type'] ?? 'N/A') . "</td>";
            echo "<td>" . ($error['error'] ?? 'N/A') . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
    }
}

// Auto-cleanup old errors (setiap 1 jam)
register_shutdown_function(function() {
    ErrorHandler::clearOldErrors();
});

// Initialize error handler
ErrorHandler::init();
