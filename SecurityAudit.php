<?php
// Security audit and monitoring utility
class SecurityAudit {
    
    public static function performFullAudit() {
        $results = [
            'timestamp' => date('Y-m-d H:i:s'),
            'checks' => []
        ];
        
        // Check environment security
        $results['checks']['environment'] = self::auditEnvironment();
        
        // Check file permissions
        $results['checks']['permissions'] = self::auditFilePermissions();
        
        // Check database security
        $results['checks']['database'] = self::auditDatabaseSecurity();
        
        // Check session security
        $results['checks']['session'] = self::auditSessionSecurity();
        
        // Check input validation
        $results['checks']['input'] = self::auditInputValidation();
        
        // Check XSS protection
        $results['checks']['xss'] = self::auditXSSProtection();
        
        // Check CSRF protection
        $results['checks']['csrf'] = self::auditCSRFProtection();
        
        // Check SQL injection protection
        $results['checks']['sql'] = self::auditSQLInjectionProtection();
        
        // Calculate overall security score
        $results['score'] = self::calculateSecurityScore($results['checks']);
        
        return $results;
    }
    
    private static function auditEnvironment() {
        $issues = [];
        
        // Check debug mode
        if (Environment::get('APP_DEBUG') === 'true') {
            $issues[] = 'Debug mode is enabled in production';
        }
        
        // Check secure keys
        if (Environment::get('APP_KEY') === 'base64:randomlyGeneratedKeyHere') {
            $issues[] = 'Default APP_KEY is being used';
        }
        
        if (Environment::get('JWT_SECRET') === 'your_jwt_secret_key_here') {
            $issues[] = 'Default JWT_SECRET is being used';
        }
        
        // Check database credentials
        if (Environment::get('DB_USER') === 'root') {
            $issues[] = 'Using root database user is not recommended';
        }
        
        return [
            'status' => empty($issues) ? 'pass' : 'fail',
            'issues' => $issues
        ];
    }
    
    private static function auditFilePermissions() {
        $issues = [];
        $baseDir = __DIR__ . '/..';
        
        // Check sensitive file permissions
        $sensitiveFiles = [
            '.env',
            'config/db.php',
            'src/App.php'
        ];
        
        foreach ($sensitiveFiles as $file) {
            $filePath = $baseDir . '/' . $file;
            if (file_exists($filePath)) {
                $perms = fileperms($filePath);
                if ($perms & 0x004) { // World readable
                    $issues[] = "File $file is world readable";
                }
            }
        }
        
        return [
            'status' => empty($issues) ? 'pass' : 'fail',
            'issues' => $issues
        ];
    }
    
    private static function auditDatabaseSecurity() {
        $issues = [];
        
        try {
            $pdo = PerformanceOptimizer::getOptimizedConnection();
            
            // Check for plain text passwords
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users WHERE password_hash NOT LIKE '$2%'");
            $stmt->execute();
            $result = $stmt->fetch();
            
            if ($result['count'] > 0) {
                $issues[] = $result['count'] . ' users have non-hashed passwords';
            }
            
            // Check for default admin accounts
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users WHERE email IN ('admin@example.com', 'admin@test.com')");
            $stmt->execute();
            $result = $stmt->fetch();
            
            if ($result['count'] > 0) {
                $issues[] = 'Default admin accounts detected';
            }
            
        } catch (Exception $e) {
            $issues[] = 'Could not connect to database for security audit';
        }
        
        return [
            'status' => empty($issues) ? 'pass' : 'fail',
            'issues' => $issues
        ];
    }
    
    private static function auditSessionSecurity() {
        $issues = [];
        
        // Check session configuration
        if (Environment::get('SESSION_SECURE') === 'false' && 
            Environment::get('APP_ENV') === 'production') {
            $issues[] = 'Session secure flag is disabled in production';
        }
        
        if (Environment::get('SESSION_HTTPONLY') !== 'true') {
            $issues[] = 'Session HTTPOnly flag is disabled';
        }
        
        if (Environment::get('SESSION_SAMESITE') !== 'Strict') {
            $issues[] = 'Session SameSite is not set to Strict';
        }
        
        return [
            'status' => empty($issues) ? 'pass' : 'fail',
            'issues' => $issues
        ];
    }
    
    private static function auditInputValidation() {
        $issues = [];
        
        // Check if InputValidator is being used
        if (!class_exists('InputValidator')) {
            $issues[] = 'InputValidator class not found';
        }
        
        // Check for direct $_GET/$_POST usage in API files
        $apiDir = __DIR__ . '/../src/public/api';
        if (is_dir($apiDir)) {
            $files = glob($apiDir . '/*.php');
            foreach ($files as $file) {
                $content = file_get_contents($file);
                if (preg_match('/\$_GET\[|\$_POST\[|\$_REQUEST\[/', $content)) {
                    $issues[] = 'Direct superglobal usage detected in ' . basename($file);
                }
            }
        }
        
        return [
            'status' => empty($issues) ? 'pass' : 'fail',
            'issues' => $issues
        ];
    }
    
    private static function auditXSSProtection() {
        $issues = [];
        
        // Check for XSS headers
        if (!headers_sent()) {
            // Headers will be checked during actual request
        }
        
        // Check for direct echo without escaping
        $viewsDir = __DIR__ . '/../app/views';
        if (is_dir($viewsDir)) {
            $files = glob($viewsDir . '/*.php');
            foreach ($files as $file) {
                $content = file_get_contents($file);
                if (preg_match('/echo\s+\$[^h]/', $content)) {
                    $issues[] = 'Potential XSS vulnerability in ' . basename($file);
                }
            }
        }
        
        return [
            'status' => empty($issues) ? 'pass' : 'fail',
            'issues' => $issues
        ];
    }
    
    private static function auditCSRFProtection() {
        $issues = [];
        
        // Check if Security class has CSRF methods
        if (!class_exists('Security') || 
            !method_exists('Security', 'generateCSRFToken')) {
            $issues[] = 'CSRF protection not implemented';
        }
        
        return [
            'status' => empty($issues) ? 'pass' : 'fail',
            'issues' => $issues
        ];
    }
    
    private static function auditSQLInjectionProtection() {
        $issues = [];
        
        // Check for prepared statement usage
        $appDir = __DIR__ . '/../app';
        if (is_dir($appDir)) {
            $files = glob($appDir . '/*.php');
            foreach ($files as $file) {
                $content = file_get_contents($file);
                if (preg_match('/query\s*\(/', $content) && 
                    !preg_match('/prepare\s*\(/', $content)) {
                    $issues[] = 'Potential SQL injection vulnerability in ' . basename($file);
                }
            }
        }
        
        return [
            'status' => empty($issues) ? 'pass' : 'fail',
            'issues' => $issues
        ];
    }
    
    private static function calculateSecurityScore($checks) {
        $totalChecks = count($checks);
        $passedChecks = 0;
        
        foreach ($checks as $check) {
            if ($check['status'] === 'pass') {
                $passedChecks++;
            }
        }
        
        return [
            'score' => round(($passedChecks / $totalChecks) * 100, 2),
            'passed' => $passedChecks,
            'total' => $totalChecks
        ];
    }
    
    public static function logSecurityEvent($event, $details = []) {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'event' => $event,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'user_id' => $_SESSION['user_id'] ?? null,
            'details' => $details
        ];
        
        $logFile = __DIR__ . '/../logs/security.log';
        file_put_contents($logFile, json_encode($logEntry) . "\n", FILE_APPEND | LOCK_EX);
    }
}
