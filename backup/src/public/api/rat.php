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
            case 'modal_pokok_history':
                if ($id <= 0) {
                    echo json_encode(['success' => false, 'message' => 'Invalid cooperative ID']);
                    exit;
                }
                
                $history = $cooperative->getModalPokokHistory($id);
                echo json_encode(['success' => true, 'data' => $history]);
                break;
                
            case 'rat_sessions':
                if ($id <= 0) {
                    echo json_encode(['success' => false, 'message' => 'Invalid cooperative ID']);
                    exit;
                }
                
                $sessions = $cooperative->getRATSessions($id);
                echo json_encode(['success' => true, 'data' => $sessions]);
                break;
                
            default:
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }
        break;
        
    case 'POST':
        switch ($action) {
            case 'update_modal_pokok_rat':
                if ($id <= 0) {
                    echo json_encode(['success' => false, 'message' => 'Invalid cooperative ID']);
                    exit;
                }
                
                $data = json_decode(file_get_contents('php://input'), true);
                $result = $cooperative->updateModalPokokFromRAT(
                    $id,
                    $data['tahun'],
                    $data['modal_pokok_baru'],
                    $data['alasan'],
                    $_SESSION['user_id']
                );
                echo json_encode($result);
                break;
                
            case 'update_modal_pokok_manual':
                if ($id <= 0) {
                    echo json_encode(['success' => false, 'message' => 'Invalid cooperative ID']);
                    exit;
                }
                
                $data = json_decode(file_get_contents('php://input'), true);
                $result = $cooperative->updateModalPokokManual(
                    $id,
                    $data['modal_pokok_baru'],
                    $data['alasan'],
                    $_SESSION['user_id']
                );
                echo json_encode($result);
                break;
                
            case 'create_rat_session':
                if ($id <= 0) {
                    echo json_encode(['success' => false, 'message' => 'Invalid cooperative ID']);
                    exit;
                }
                
                $data = json_decode(file_get_contents('php://input'), true);
                $result = $cooperative->createRATSession(
                    $id,
                    $data['tahun'],
                    $data['tanggal_rapat'],
                    $data['tempat'],
                    $data['agenda'],
                    $_SESSION['user_id']
                );
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
