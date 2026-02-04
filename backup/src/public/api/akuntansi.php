<?php
// API endpoint for accounting
require_once __DIR__ . '/../../bootstrap.php';

header('Content-Type: application/json');

$auth = new Auth();
if (!$auth->isLoggedIn() || !$auth->hasPermission('view_accounts')) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$accounting = new Accounting();
$action = $_GET['action'] ?? '';

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        switch ($action) {
            case 'chart_of_accounts':
                $parentId = isset($_GET['parent_id']) ? intval($_GET['parent_id']) : null;
                $accounts = $accounting->getChartOfAccounts($parentId);
                echo json_encode(['success' => true, 'data' => $accounts]);
                break;
                
            case 'account':
                $id = intval($_GET['id'] ?? 0);
                $account = $accounting->getAccount($id);
                if ($account) {
                    echo json_encode(['success' => true, 'data' => $account]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Account not found']);
                }
                break;
                
            case 'journal_entries':
                $startDate = $_GET['start_date'] ?? null;
                $endDate = $_GET['end_date'] ?? null;
                $limit = intval($_GET['limit'] ?? 50);
                $offset = intval($_GET['offset'] ?? 0);
                $entries = $accounting->getJournalEntries($startDate, $endDate, $limit, $offset);
                echo json_encode(['success' => true, 'data' => $entries]);
                break;
                
            case 'journals':
                $limit = intval($_GET['limit'] ?? 20);
                $entries = $accounting->getJournalEntries(null, null, $limit, 0);
                echo json_encode(['success' => true, 'journals' => $entries]);
                break;
                
            case 'journal_entry':
                $id = intval($_GET['id'] ?? 0);
                $entry = $accounting->getJournalEntry($id);
                if ($entry) {
                    echo json_encode(['success' => true, 'data' => $entry]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Journal entry not found']);
                }
                break;
                
            case 'trial_balance':
                $asOfDate = $_GET['as_of_date'] ?? date('Y-m-d');
                $trialBalance = $accounting->getTrialBalance($asOfDate);
                if ($trialBalance) {
                    echo json_encode(['success' => true, 'data' => $trialBalance]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to generate trial balance']);
                }
                break;
                
            default:
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }
        break;
        
    case 'POST':
        if (!$auth->hasPermission('manage_accounts')) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Forbidden']);
            break;
        }
        
        switch ($action) {
            case 'create_account':
                $data = json_decode(file_get_contents('php://input'), true);
                $result = $accounting->createAccount(
                    $data['code'],
                    $data['name'],
                    $data['type'],
                    $data['parent_id'] ?? null
                );
                echo json_encode($result);
                break;
                
            case 'create_journal_entry':
                $data = json_decode(file_get_contents('php://input'), true);
                $result = $accounting->createJournalEntry(
                    $data['date'],
                    $data['description'],
                    $data['details'],
                    $data['reference_number'] ?? null
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
