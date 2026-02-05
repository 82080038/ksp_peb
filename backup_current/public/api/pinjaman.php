<?php
// API endpoint for loans
require_once __DIR__ . '/../../bootstrap.php';

header('Content-Type: application/json');

$auth = new Auth();
if (!$auth->isLoggedIn() || !$auth->hasPermission('view_loans')) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? '';
$cooperativeId = $_SESSION['cooperative_id'] ?? null;

if (!$cooperativeId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Cooperative ID missing']);
    exit;
}

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        switch ($action) {
            case 'summary':
                // Return dummy summary data
                echo json_encode([
                    'success' => true,
                    'total_amount' => 0,
                    'active_count' => 0,
                    'paid_count' => 0,
                    'overdue_count' => 0
                ]);
                break;
                
            case 'list':
                // Return dummy list data
                echo json_encode([
                    'success' => true,
                    'loans' => []
                ]);
                break;
                
            default:
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }
        break;
        
    case 'POST':
        switch ($action) {
            case 'apply':
                // Dummy response for loan application
                echo json_encode([
                    'success' => true,
                    'message' => 'Loan application submitted'
                ]);
                break;
                
            default:
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
