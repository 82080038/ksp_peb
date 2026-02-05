<?php
require_once __DIR__ . '/../../../app/bootstrap.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

$app = new App();
$cooperative = new Cooperative();

$action = $_GET['action'] ?? '';
$id = intval($_GET['id'] ?? 0);

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        switch ($action) {
            case 'status_history':
                if ($id <= 0) {
                    echo json_encode(['success' => false, 'message' => 'Invalid cooperative ID']);
                    exit;
                }
                
                $history = $cooperative->getStatusHistory($id);
                echo json_encode(['success' => true, 'data' => $history]);
                break;
                
            case 'document_history':
                if ($id <= 0) {
                    echo json_encode(['success' => false, 'message' => 'Invalid cooperative ID']);
                    exit;
                }
                
                $history = $cooperative->getDocumentHistory($id);
                echo json_encode(['success' => true, 'data' => $history]);
                break;
                
            default:
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }
        break;
        
    case 'POST':
        switch ($action) {
            case 'update':
                if ($id <= 0) {
                    echo json_encode(['success' => false, 'message' => 'Invalid cooperative ID']);
                    exit;
                }
                
                $data = json_decode(file_get_contents('php://input'), true);
                $result = $cooperative->updateCooperative($id, $data);
                echo json_encode($result);
                break;
                
            case 'update_legal':
                if ($id <= 0) {
                    echo json_encode(['success' => false, 'message' => 'Invalid cooperative ID']);
                    exit;
                }
                
                $data = json_decode(file_get_contents('php://input'), true);
                $result = $cooperative->updateLegalInformation($id, $data);
                echo json_encode($result);
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
?>
