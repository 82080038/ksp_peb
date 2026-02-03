<?php
// API endpoint for cooperatives
require_once __DIR__ . '/../../bootstrap.php';

header('Content-Type: application/json');

$cooperative = new Cooperative();
$action = $_GET['action'] ?? '';

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        switch ($action) {
            case 'list':
                $cooperatives = $cooperative->getAllCooperatives();
                echo json_encode(['success' => true, 'data' => $cooperatives]);
                break;
                
            case 'detail':
                $id = intval($_GET['id'] ?? 0);
                $coop = $cooperative->getCooperative($id);
                if ($coop) {
                    echo json_encode(['success' => true, 'data' => $coop]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Cooperative not found']);
                }
                break;
                
            case 'has_cooperatives':
                $hasCoops = $cooperative->hasCooperatives();
                echo json_encode(['success' => true, 'has_cooperatives' => $hasCoops]);
                break;
                
            case 'provinces':
                $provinces = $cooperative->getProvinces();
                echo json_encode(['success' => true, 'data' => $provinces]);
                break;
                
            case 'cities':
                $provinceId = intval($_GET['province_id'] ?? 0);
                $cities = $cooperative->getCities($provinceId);
                echo json_encode(['success' => true, 'data' => $cities]);
                break;
                
            case 'districts':
                $cityId = intval($_GET['city_id'] ?? 0);
                $districts = $cooperative->getDistricts($cityId);
                echo json_encode(['success' => true, 'data' => $districts]);
                break;
                
            case 'villages':
                $districtId = intval($_GET['district_id'] ?? 0);
                $villages = $cooperative->getVillages($districtId);
                echo json_encode(['success' => true, 'data' => $villages]);
                break;
                
            default:
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }
        break;
        
    case 'POST':
        switch ($action) {
            case 'create':
                $data = json_decode(file_get_contents('php://input'), true);
                $result = $cooperative->createCooperative($data);
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
