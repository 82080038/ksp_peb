<?php
// API endpoint for authentication
require_once __DIR__ . '/../src/bootstrap.php';

header('Content-Type: application/json');

$auth = new Auth();
$action = $_GET['action'] ?? '';

switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        switch ($action) {
            case 'register':
                $data = json_decode(file_get_contents('php://input'), true);
                $result = $auth->register($data);
                echo json_encode($result);
                break;
                
            case 'login':
                $data = json_decode(file_get_contents('php://input'), true);
                $result = $auth->login($data['email'], $data['password']);
                echo json_encode($result);
                break;
                
            case 'logout':
                $result = $auth->logout();
                echo json_encode($result);
                break;
                
            case 'change_password':
                if (!$auth->isLoggedIn()) {
                    http_response_code(401);
                    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                    break;
                }
                
                $data = json_decode(file_get_contents('php://input'), true);
                $user = $auth->getCurrentUser();
                $result = $auth->changePassword($user['id'], $data['current_password'], $data['new_password']);
                echo json_encode($result);
                break;
                
            default:
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }
        break;
        
    case 'GET':
        switch ($action) {
            case 'me':
                if (!$auth->isLoggedIn()) {
                    http_response_code(401);
                    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                    break;
                }
                
                $user = $auth->getCurrentUser();
                echo json_encode(['success' => true, 'user' => $user]);
                break;
                
            case 'check':
                echo json_encode(['success' => true, 'logged_in' => $auth->isLoggedIn()]);
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
