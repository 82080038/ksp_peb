<?php
// API endpoint for members
require_once __DIR__ . '/../../bootstrap.php';

header('Content-Type: application/json');

$auth = new Auth();
if (!$auth->isLoggedIn() || !$auth->hasPermission('view_members')) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$member = new Member();
$action = $_GET['action'] ?? '';

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        switch ($action) {
            case 'list':
                $limit = intval($_GET['limit'] ?? 50);
                $offset = intval($_GET['offset'] ?? 0);
                $members = $member->getAllMembers($limit, $offset);
                echo json_encode(['success' => true, 'data' => $members]);
                break;
                
            case 'detail':
                $id = intval($_GET['id'] ?? 0);
                $memberData = $member->getMember($id);
                if ($memberData) {
                    // Get additional data
                    $memberData['savings'] = $member->getMemberSavings($id);
                    $memberData['loans'] = $member->getMemberLoans($id);
                    $memberData['savings_balance'] = $member->getMemberSavingsBalance($id);
                    echo json_encode(['success' => true, 'data' => $memberData]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Member not found']);
                }
                break;
                
            case 'search':
                $query = $_GET['q'] ?? '';
                $limit = intval($_GET['limit'] ?? 50);
                $results = $member->searchMembers($query, $limit);
                echo json_encode(['success' => true, 'data' => $results]);
                break;
                
            case 'count_by_status':
                $counts = $member->getMemberCountByStatus();
                echo json_encode(['success' => true, 'data' => $counts]);
                break;
                
            case 'count':
                $stmt = App::getInstance()->getCoopDB()->prepare("SELECT COUNT(*) as count FROM anggota");
                $stmt->execute();
                $result = $stmt->fetch();
                echo json_encode(['success' => true, 'count' => $result['count']]);
                break;
                
            default:
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }
        break;
        
    case 'POST':
        if (!$auth->hasPermission('manage_members')) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Forbidden']);
            break;
        }
        
        switch ($action) {
            case 'create':
                $data = json_decode(file_get_contents('php://input'), true);
                $result = $member->createMember($data['user_id'], $data['nomor_anggota'] ?? null);
                echo json_encode($result);
                break;
                
            case 'update_status':
                $data = json_decode(file_get_contents('php://input'), true);
                $result = $member->updateMemberStatus($data['member_id'], $data['status'], $data['reason'] ?? '');
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
