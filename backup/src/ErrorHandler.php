<?php
// Error handling and logging utility
class ErrorHandler {
    private static $logFile = null;
    
    public static function init() {
        self::$logFile = __DIR__ . '/../logs/error.log';
        
        // Create logs directory if not exists
        $logDir = dirname(self::$logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        // Set custom error handler
        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
        register_shutdown_function([self::class, 'handleShutdown']);
    }
    
    public static function handleError($severity, $message, $file, $line) {
        if (!(error_reporting() & $severity)) {
            return false;
        }
        
        self::log([
            'type' => 'Error',
            'severity' => $severity,
            'message' => $message,
            'file' => $file,
            'line' => $line,
            'trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)
        ]);
        
        if (Environment::get('APP_DEBUG') === 'true') {
            echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; padding: 10px; margin: 10px; border-radius: 4px;'>";
            echo "<strong>Error:</strong> $message in $file on line $line";
            echo "</div>";
        }
        
        return true;
    }
    
    public static function handleException($exception) {
        self::log([
            'type' => 'Exception',
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString()
        ]);
        
        if (Environment::get('APP_DEBUG') === 'true') {
            echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; padding: 10px; margin: 10px; border-radius: 4px;'>";
            echo "<strong>Exception:</strong> " . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine();
            echo "<pre>" . $exception->getTraceAsString() . "</pre>";
            echo "</div>";
        } else {
            echo "<h1>500 Internal Server Error</h1>";
            echo "<p>Something went wrong. Please try again later.</p>";
        }
        
        exit;
    }
    
    public static function handleShutdown() {
        $error = error_get_last();
        if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            self::log([
                'type' => 'Fatal Error',
                'message' => $error['message'],
                'file' => $error['file'],
                'line' => $error['line']
            ]);
            
            if (Environment::get('APP_DEBUG') !== 'true') {
                echo "<h1>500 Internal Server Error</h1>";
                echo "<p>Something went wrong. Please try again later.</p>";
            }
        }
    }
    
    private static function log($data) {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'url' => $_SERVER['REQUEST_URI'] ?? 'unknown',
            'method' => $_SERVER['REQUEST_METHOD'] ?? 'unknown',
            'data' => $data
        ];
        
        $logMessage = json_encode($logEntry) . "\n";
        file_put_contents(self::$logFile, $logMessage, FILE_APPEND | LOCK_EX);
    }
    
    public static function logActivity($action, $details = []) {
        self::log([
            'type' => 'Activity',
            'action' => $action,
            'details' => $details,
            'user_id' => $_SESSION['user_id'] ?? null
        ]);
    }
}

// Initialize error handler
ErrorHandler::init();
