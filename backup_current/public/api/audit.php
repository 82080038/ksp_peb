<?php
// API endpoint for audit logs
require_once __DIR__ . '/../../bootstrap.php';

header('Content-Type: application/json');

$auth = new Auth();
if (!$auth->isLoggedIn() || !$auth->hasPermission('view_audit')) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? '';

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        switch ($action) {
            case 'recent':
                $limit = intval($_GET['limit'] ?? 10);
                $offset = intval($_GET['offset'] ?? 0);

                try {
                    // Get recent audit logs with user information
                    $stmt = App::getInstance()->getCoopDB()->prepare("
                        SELECT
                            al.id,
                            al.action,
                            al.details,
                            al.created_at,
                            COALESCE(u.nama, 'System') as user_name,
                            u.email as user_email
                        FROM audit_logs al
                        LEFT JOIN people_db.users u ON al.user_id = u.id
                        ORDER BY al.created_at DESC
                        LIMIT ? OFFSET ?
                    ");
                    $stmt->execute([$limit, $offset]);
                    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Format the data for frontend
                    $activities = array_map(function($log) {
                        return [
                            'id' => $log['id'],
                            'action' => $log['action'],
                            'details' => $log['details'] ? json_decode($log['details'], true) : null,
                            'created_at' => $log['created_at'],
                            'user_name' => $log['user_name'],
                            'user_email' => $log['user_email']
                        ];
                    }, $logs);

                    echo json_encode([
                        'success' => true,
                        'activities' => $activities,
                        'count' => count($activities)
                    ]);
                } catch (Exception $e) {
                    error_log("Audit API Error: " . $e->getMessage());
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to load audit logs',
                        'error' => $e->getMessage()
                    ]);
                }
                break;

            case 'count':
                try {
                    $stmt = App::getInstance()->getCoopDB()->prepare("SELECT COUNT(*) as total FROM audit_logs");
                    $stmt->execute();
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);

                    echo json_encode([
                        'success' => true,
                        'total' => (int)$result['total']
                    ]);
                } catch (Exception $e) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to count audit logs'
                    ]);
                }
                break;

            default:
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }
        break;

    case 'POST':
        if (!$auth->hasPermission('admin_access')) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Forbidden']);
            break;
        }

        switch ($action) {
            case 'log':
                $data = json_decode(file_get_contents('php://input'), true);

                if (!$data || !isset($data['action'])) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
                    break;
                }

                try {
                    $currentUser = $auth->getCurrentUser();

                    $stmt = App::getInstance()->getCoopDB()->prepare("
                        INSERT INTO audit_logs (user_id, action, details, ip_address, user_agent)
                        VALUES (?, ?, ?, ?, ?)
                    ");

                    $details = isset($data['details']) ? json_encode($data['details']) : null;

                    $stmt->execute([
                        $currentUser['id'] ?? null,
                        $data['action'],
                        $details,
                        $_SERVER['REMOTE_ADDR'] ?? null,
                        $_SERVER['HTTP_USER_AGENT'] ?? null
                    ]);

                    echo json_encode([
                        'success' => true,
                        'message' => 'Audit log created',
                        'id' => App::getInstance()->getCoopDB()->lastInsertId()
                    ]);
                } catch (Exception $e) {
                    error_log("Audit log creation error: " . $e->getMessage());
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to create audit log'
                    ]);
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
