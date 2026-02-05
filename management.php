<?php
// API endpoint for management (pengurus & pengawas)
require_once __DIR__ . '/../../bootstrap.php';

header('Content-Type: application/json');

$auth = new Auth();
if (!$auth->isLoggedIn() || !$auth->hasPermission('admin_access')) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$management = new Management();
$type = $_GET['type'] ?? ''; // pengurus or pengawas
$action = $_GET['action'] ?? '';

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        switch ($type) {
            case 'pengurus':
                switch ($action) {
                    case 'list':
                        $pengurusList = $management->getAllPengurus();
                        echo json_encode(['success' => true, 'data' => $pengurusList]);
                        break;
                        
                    case 'detail':
                        $id = intval($_GET['id'] ?? 0);
                        $pengurus = $management->getPengurus($id);
                        if ($pengurus) {
                            echo json_encode(['success' => true, 'data' => $pengurus]);
                        } else {
                            echo json_encode(['success' => false, 'message' => 'Pengurus not found']);
                        }
                        break;
                        
                    case 'count':
                        $count = $management->getActivePengurusCount();
                        echo json_encode(['success' => true, 'count' => $count]);
                        break;
                        
                    default:
                        $pengurusList = $management->getAllPengurus();
                        echo json_encode(['success' => true, 'data' => $pengurusList]);
                }
                break;
                
            case 'pengawas':
                switch ($action) {
                    case 'list':
                        $pengawasList = $management->getAllPengawas();
                        echo json_encode(['success' => true, 'data' => $pengawasList]);
                        break;
                        
                    case 'detail':
                        $id = intval($_GET['id'] ?? 0);
                        $pengawas = $management->getPengawas($id);
                        if ($pengawas) {
                            echo json_encode(['success' => true, 'data' => $pengawas]);
                        } else {
                            echo json_encode(['success' => false, 'message' => 'Pengawas not found']);
                        }
                        break;
                        
                    case 'count':
                        $count = $management->getActivePengawasCount();
                        echo json_encode(['success' => true, 'count' => $count]);
                        break;
                        
                    default:
                        $pengawasList = $management->getAllPengawas();
                        echo json_encode(['success' => true, 'data' => $pengawasList]);
                }
                break;
                
            default:
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid type. Use pengurus or pengawas']);
        }
        break;
        
    case 'POST':
        switch ($type) {
            case 'pengurus':
                $data = json_decode(file_get_contents('php://input'), true);
                $result = $management->createPengurus(
                    $data['user_id'],
                    $data['jabatan'],
                    $data['periode_start'],
                    $data['periode_end'] ?? null
                );
                echo json_encode($result);
                break;
                
            case 'pengawas':
                $data = json_decode(file_get_contents('php://input'), true);
                $result = $management->createPengawas(
                    $data['user_id'],
                    $data['jabatan'],
                    $data['periode_start'],
                    $data['periode_end'] ?? null
                );
                echo json_encode($result);
                break;
                
            default:
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid type. Use pengurus or pengawas']);
        }
        break;
        
    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        $id = intval($_GET['id'] ?? 0);
        
        switch ($type) {
            case 'pengurus':
                $result = $management->updatePengurus(
                    $id,
                    $data['jabatan'],
                    $data['periode_start'],
                    $data['periode_end'] ?? null
                );
                echo json_encode($result);
                break;
                
            case 'pengawas':
                $result = $management->updatePengawas(
                    $id,
                    $data['jabatan'],
                    $data['periode_start'],
                    $data['periode_end'] ?? null
                );
                echo json_encode($result);
                break;
                
            default:
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid type. Use pengurus or pengawas']);
        }
        break;
        
    case 'PATCH':
        $data = json_decode(file_get_contents('php://input'), true);
        $id = intval($_GET['id'] ?? 0);
        
        switch ($type) {
            case 'pengurus':
                if (isset($data['status'])) {
                    $result = $management->updatePengurusStatus($id, $data['status']);
                    echo json_encode($result);
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Missing status field']);
                }
                break;
                
            case 'pengawas':
                if (isset($data['status'])) {
                    $result = $management->updatePengawasStatus($id, $data['status']);
                    echo json_encode($result);
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Missing status field']);
                }
                break;
                
            default:
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid type. Use pengurus or pengawas']);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
