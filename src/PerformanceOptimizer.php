<?php
// Performance optimization utilities
class PerformanceOptimizer {
    
    // Database connection pooling
    private static $connections = [];
    private static $maxConnections = 10;
    
    public static function getOptimizedConnection($type = 'default') {
        $key = $type . '_' . md5(serialize([
            Environment::get('DB_HOST'),
            Environment::get('DB_NAME'),
            $type
        ]));
        
        if (!isset(self::$connections[$key]) || 
            count(self::$connections) >= self::$maxConnections) {
            
            $pdo = new PDO(
                "mysql:host=" . Environment::get('DB_HOST') . 
                ";dbname=" . ($type === 'people' ? 'people_db' : 
                              ($type === 'address' ? 'alamat_db' : Environment::get('DB_NAME'))) . 
                ";charset=utf8mb4",
                Environment::get('DB_USER'),
                Environment::get('DB_PASS'),
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_PERSISTENT => true,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => false
                ]
            );
            
            self::$connections[$key] = $pdo;
        }
        
        return self::$connections[$key];
    }
    
    // Query caching
    private static $cache = [];
    private static $cacheEnabled = true;
    
    public static function cacheQuery($key, $callback, $ttl = 300) {
        if (!self::$cacheEnabled) {
            return $callback();
        }
        
        $cacheKey = md5($key);
        
        if (isset(self::$cache[$cacheKey]) && 
            (time() - self::$cache[$cacheKey]['timestamp']) < $ttl) {
            return self::$cache[$cacheKey]['data'];
        }
        
        $data = $callback();
        
        self::$cache[$cacheKey] = [
            'data' => $data,
            'timestamp' => time()
        ];
        
        return $data;
    }
    
    public static function clearCache($pattern = null) {
        if ($pattern) {
            foreach (self::$cache as $key => $value) {
                if (strpos($key, $pattern) !== false) {
                    unset(self::$cache[$key]);
                }
            }
        } else {
            self::$cache = [];
        }
    }
    
    // Output buffering and compression
    public static function enableOutputCompression() {
        if (!ob_start('ob_gzhandler')) {
            ob_start();
        }
    }
    
    public static function setCacheHeaders($ttl = 3600) {
        $expires = gmdate('D, d M Y H:i:s', time() + $ttl) . ' GMT';
        header("Expires: $expires");
        header("Cache-Control: public, max-age=$ttl");
        header("Pragma: cache");
    }
    
    // Memory optimization
    public static function optimizeMemory() {
        // Clean up session data
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        
        // Force garbage collection
        if (function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }
    }
    
    // Database query optimization
    public static function optimizeQuery($sql, $params = []) {
        // Remove unnecessary whitespace
        $sql = preg_replace('/\s+/', ' ', $sql);
        
        // Add EXPLAIN for debugging in development
        if (Environment::get('APP_DEBUG') === 'true') {
            error_log("Query: " . $sql);
            error_log("Params: " . json_encode($params));
        }
        
        return [$sql, $params];
    }
    
    // Batch processing for large datasets
    public static function processBatch($callback, $data, $batchSize = 100) {
        $results = [];
        $batches = array_chunk($data, $batchSize);
        
        foreach ($batches as $batch) {
            $batchResults = $callback($batch);
            $results = array_merge($results, $batchResults);
            
            // Free memory between batches
            self::optimizeMemory();
        }
        
        return $results;
    }
    
    // Enable/disable features based on performance
    public static function enableCaching($enable = true) {
        self::$cacheEnabled = $enable;
    }
    
    public static function getPerformanceStats() {
        return [
            'memory_usage' => memory_get_usage(true),
            'memory_peak' => memory_get_peak_usage(true),
            'connections' => count(self::$connections),
            'cache_size' => count(self::$cache),
            'execution_time' => microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']
        ];
    }
}
