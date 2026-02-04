<?php
// API endpoint for cooperative financial settings
require_once __DIR__ . '/../../bootstrap.php';

header('Content-Type: application/json');

// Include CooperativeFinancialSettings class
require_once __DIR__ . '/../../../app/CooperativeFinancialSettings.php';

$financialSettings = new CooperativeFinancialSettings();
$action = $_GET['action'] ?? '';

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        switch ($action) {
            case 'current':
                $cooperativeId = intval($_GET['cooperative_id'] ?? 0);
                $tahun = $_GET['tahun'] ?? date('Y');
                
                if (!$cooperativeId) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Cooperative ID is required']);
                    break;
                }
                
                $settings = $financialSettings->getCurrentSettings($cooperativeId, $tahun);
                if ($settings) {
                    echo json_encode(['success' => true, 'data' => $settings]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Financial settings not found']);
                }
                break;
                
            case 'history':
                $cooperativeId = intval($_GET['cooperative_id'] ?? 0);
                
                if (!$cooperativeId) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Cooperative ID is required']);
                    break;
                }
                
                $history = $financialSettings->getSettingsHistory($cooperativeId);
                echo json_encode(['success' => true, 'data' => $history]);
                break;
                
            case 'active_year':
                $cooperativeId = intval($_GET['cooperative_id'] ?? 0);
                
                if (!$cooperativeId) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Cooperative ID is required']);
                    break;
                }
                
                $activeYear = $financialSettings->getActiveYear($cooperativeId);
                echo json_encode(['success' => true, 'data' => ['tahun_buku' => $activeYear]]);
                break;
                
            case 'all_settings':
                // Get all financial settings for all cooperatives (admin view)
                try {
                    $app = App::getInstance();
                    $coopDB = $app->getCoopDB();
                    
                    $stmt = $coopDB->prepare("
                        SELECT c.nama as nama_koperasi, c.id as cooperative_id, fs.* 
                        FROM cooperatives c
                        LEFT JOIN cooperative_financial_settings fs ON c.id = fs.cooperative_id
                        ORDER BY c.nama, fs.tahun_buku DESC
                    ");
                    $stmt->execute();
                    $result = $stmt->fetchAll();
                    
                    echo json_encode(['success' => true, 'data' => $result]);
                } catch (Exception $e) {
                    echo json_encode(['success' => false, 'message' => 'Failed to fetch settings: ' . $e->getMessage()]);
                }
                break;
                
            default:
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }
        break;
        
    case 'POST':
        switch ($action) {
            case 'save':
                $data = json_decode(file_get_contents('php://input'), true);
                $result = $financialSettings->setFinancialSettings($data);
                echo json_encode($result);
                break;
                
            case 'update':
                $data = json_decode(file_get_contents('php://input'), true);
                
                // Validate required fields for update
                $required = ['cooperative_id', 'tahun_buku'];
                foreach ($required as $field) {
                    if (empty($data[$field])) {
                        echo json_encode(['success' => false, 'message' => "Field $field is required"]);
                        break;
                    }
                }
                
                $result = $financialSettings->setFinancialSettings($data);
                echo json_encode($result);
                break;
                
            case 'close_year':
                $data = json_decode(file_get_contents('php://input'), true);
                $cooperativeId = intval($data['cooperative_id'] ?? 0);
                $tahun = $data['tahun'] ?? date('Y');
                
                if (!$cooperativeId) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Cooperative ID is required']);
                    break;
                }
                
                $success = $financialSettings->closeFinancialYear($cooperativeId, $tahun);
                if ($success) {
                    echo json_encode(['success' => true, 'message' => 'Financial year closed successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to close financial year']);
                }
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
