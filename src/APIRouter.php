<?php
// API Router for handling all API requests
class APIRouter {
    private $routes = [];
    
    public function __construct() {
        $this->loadRoutes();
    }
    
    private function loadRoutes() {
        // API Routes configuration
        $this->routes = [
            'GET' => [
                '/api/auth' => 'auth.php',
                '/api/anggota' => 'anggota.php',
                '/api/management' => 'management.php',
                '/api/cooperative' => 'cooperative.php',
                '/api/cooperative-settings' => 'cooperative-settings.php',
                '/api/cooperative-financial' => 'cooperative-financial.php',
                '/api/rat' => 'rat.php',
                '/api/simpanan' => 'simpanan.php',
                '/api/pinjaman' => 'pinjaman.php',
                '/api/akuntansi' => 'akuntansi.php',
                '/api/audit' => 'audit.php',
                '/api/shu' => 'shu.php'
            ],
            'POST' => [
                '/api/auth' => 'auth.php',
                '/api/anggota' => 'anggota.php',
                '/api/management' => 'management.php',
                '/api/cooperative' => 'cooperative.php',
                '/api/cooperative-settings' => 'cooperative-settings.php',
                '/api/cooperative-financial' => 'cooperative-financial.php',
                '/api/rat' => 'rat.php',
                '/api/simpanan' => 'simpanan.php',
                '/api/pinjaman' => 'pinjaman.php',
                '/api/akuntansi' => 'akuntansi.php',
                '/api/audit' => 'audit.php',
                '/api/shu' => 'shu.php'
            ],
            'PUT' => [
                '/api/management' => 'management.php',
                '/api/cooperative-settings' => 'cooperative-settings.php',
                '/api/cooperative-financial' => 'cooperative-financial.php',
                '/api/rat' => 'rat.php',
                '/api/simpanan' => 'simpanan.php',
                '/api/pinjaman' => 'pinjaman.php',
                '/api/akuntansi' => 'akuntansi.php'
            ],
            'PATCH' => [
                '/api/management' => 'management.php',
                '/api/cooperative-settings' => 'cooperative-settings.php',
                '/api/cooperative-financial' => 'cooperative-financial.php',
                '/api/rat' => 'rat.php',
                '/api/simpanan' => 'simpanan.php',
                '/api/pinjaman' => 'pinjaman.php',
                '/api/akuntansi' => 'akuntansi.php'
            ],
            'DELETE' => [
                '/api/management' => 'management.php',
                '/api/cooperative-settings' => 'cooperative-settings.php',
                '/api/cooperative-financial' => 'cooperative-financial.php',
                '/api/rat' => 'rat.php',
                '/api/simpanan' => 'simpanan.php',
                '/api/pinjaman' => 'pinjaman.php',
                '/api/akuntansi' => 'akuntansi.php'
            ]
        ];
    }
    
    public function route($method, $uri) {
        $method = strtoupper($method);
        
        if (!isset($this->routes[$method])) {
            $this->send404();
            return;
        }
        
        // Remove query string from URI
        $uri = parse_url($uri, PHP_URL_PATH);
        
        if (!isset($this->routes[$method][$uri])) {
            $this->send404();
            return;
        }
        
        $file = $this->routes[$method][$uri];
        $filePath = __DIR__ . "/api/{$file}";
        
        if (!file_exists($filePath)) {
            $this->send404();
            return;
        }
        
        // Set proper headers
        header('Content-Type: application/json');
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('X-XSS-Protection: 1; mode=block');
        
        // Include the API file
        include $filePath;
    }
    
    private function send404() {
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'API endpoint not found'
        ]);
    }
}
