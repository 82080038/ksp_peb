<?php
// Dashboard API for AJAX requests
require_once __DIR__ . '/../../bootstrap.php';

$auth = new Auth();
if (!$auth->isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$app = App::getInstance();
$coopDB = $app->getCoopDB();

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'stats':
        // Get dashboard statistics
        try {
            $stats = [];
            
            // Total Anggota
            $stmt = $coopDB->prepare("SELECT COUNT(*) as total FROM anggota WHERE status = 'active'");
            $stmt->execute();
            $stats['totalAnggota'] = $stmt->fetch()['total'];
            
            // Total Simpanan
            $stmt = $coopDB->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM simpanan_transactions WHERE transaction_type = 'deposit'");
            $stmt->execute();
            $stats['totalSimpanan'] = number_format($stmt->fetch()['total'], 0, ',', '.');
            
            // Total Pinjaman
            $stmt = $coopDB->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM pinjaman WHERE status = 'active'");
            $stmt->execute();
            $stats['totalPinjaman'] = number_format($stmt->fetch()['total'], 0, ',', '.');
            
            // SHU Tahun Ini
            $stmt = $coopDB->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM shu_distributions WHERE YEAR(created_at) = YEAR(CURRENT_DATE)");
            $stmt->execute();
            $stats['shuTahunIni'] = number_format($stmt->fetch()['total'], 0, ',', '.');
            
            echo json_encode(['success' => true, 'stats' => $stats]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;
        
    case 'recent_activities':
        // Get recent activities
        try {
            $stmt = $coopDB->prepare("
                SELECT al.action, al.details, al.created_at, u.nama as user_name 
                FROM audit_logs al 
                LEFT JOIN users u ON al.user_id = u.id 
                ORDER BY al.created_at DESC 
                LIMIT 10
            ");
            $stmt->execute();
            $activities = $stmt->fetchAll();
            
            echo json_encode(['success' => true, 'activities' => $activities]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;
        
    case 'user_info':
        // Get current user info
        try {
            $user = $auth->getCurrentUser();
            echo json_encode(['success' => true, 'user' => $user]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>
