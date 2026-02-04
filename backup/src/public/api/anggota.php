<?php
// Anggota API for AJAX requests
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../lib/IndonesianHelper.php';

$auth = new Auth();
if (!$auth->isLoggedIn() || (!$auth->hasPermission('view_members') && !$auth->hasPermission('admin_access'))) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => __('access_denied')]);
    exit;
}

$app = App::getInstance();
$coopDB = $app->getCoopDB();
$peopleDB = $app->getPeopleDB();

$action = $_GET['action'] ?? '';
$page = intval($_GET['page'] ?? 1);
$perPage = intval($_GET['per_page'] ?? 10);
$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? '';
$sort = $_GET['sort'] ?? 'created_at';

switch ($action) {
    case 'list':
        // Get members with pagination
        try {
            $offset = ($page - 1) * $perPage;
            
            // Build WHERE clause
            $whereClause = 'WHERE 1=1';
            $params = [];
            
            if (!empty($search)) {
                $whereClause .= ' AND (a.nama LIKE ? OR a.email LIKE ? OR a.phone LIKE ? OR a.nik LIKE ?)';
                $searchParam = '%' . $search . '%';
                $params = array_fill(0, 4, $searchParam);
            }
            
            if (!empty($status)) {
                $whereClause .= ' AND a.status = ?';
                $params[] = $status;
            }
            
            // Build ORDER BY clause
            $orderBy = 'ORDER BY ' . $sort;
            
            // Count total records
            $countSql = "SELECT COUNT(*) as total FROM anggota a $whereClause";
            $stmt = $coopDB->prepare($countSql);
            $stmt->execute($params);
            $total = $stmt->fetch()['total'];
            
            // Get members
            $sql = "SELECT a.*, u.nama, u.email, u.phone 
                    FROM anggota a 
                    LEFT JOIN people_db.users u ON a.user_id = u.id 
                    $whereClause 
                    $orderBy 
                    LIMIT ? OFFSET ?";
            $stmt = $coopDB->prepare($sql);
            $params[] = $perPage;
            $params[] = $offset;
            $stmt->execute($params);
            $members = $stmt->fetchAll();
            
            // Calculate statistics
            $statistics = [
                'total' => $total,
                'active' => 0,
                'inactive' => 0,
                'blacklist' => 0
            ];
            
            foreach ($members as $member) {
                if ($member['status'] === 'active') $statistics['active']++;
                elseif ($member['status'] === 'inactive') $statistics['inactive']++;
                elseif ($member['status'] === 'blacklist') $statistics['blacklist']++;
            }
            
            // Pagination info
            $pagination = [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => ceil($total / $perPage),
                'has_previous' => $page > 1,
                'has_next' => $page < ceil($total / $perPage)
            ];
            
            echo json_encode([
                'success' => true,
                'members' => $members,
                'statistics' => $statistics,
                'pagination' => $pagination
            ]);
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;
        
    case 'create':
        // Create new member
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Validate required fields
            $required = ['nama', 'nik', 'email', 'phone'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    echo json_encode(['success' => false, 'message' => "Field $field is required"]);
                    exit;
                }
            }
            
            // Check if NIK already exists
            $stmt = $coopDB->prepare("SELECT id FROM anggota WHERE nik = ?");
            $stmt->execute([$data['nik']]);
            if ($stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'NIK sudah terdaftar']);
                exit;
            }
            
            // Check if email already exists
            $stmt = $peopleDB->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$data['email']]);
            if ($stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Email sudah terdaftar']);
                exit;
            }
            
            // Create user in people_db first
            $stmt = $peopleDB->prepare("
                INSERT INTO users (nama, email, phone, status, created_at, updated_at) 
                VALUES (?, ?, ?, 'pending', NOW(), NOW())
            ");
            $stmt->execute([$data['nama'], $data['email'], $data['phone']]);
            $userId = $peopleDB->lastInsertId();
            
            // Create anggota record
            $stmt = $coopDB->prepare("
                INSERT INTO anggota (user_id, nik, nama, email, phone, alamat, tempat_lahir, tanggal_lahir, jenis_kelamin, status, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW(), NOW())
            ");
            $stmt->execute([
                $userId,
                $data['nik'],
                $data['nama'],
                $data['email'],
                $data['phone'],
                $data['alamat'] ?? '',
                $data['tempat_lahir'] ?? '',
                $data['tanggal_lahir'] ?? '',
                $data['jenis_kelamin'] ?? ''
            ]);
            $memberId = $coopDB->lastInsertId();
            
            echo json_encode(['success' => true, 'member_id' => $memberId, 'message' => 'Anggota berhasil ditambahkan']);
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;
        
    case 'get':
        // Get member details
        $memberId = intval($_GET['id']);
        
        try {
            $stmt = $coopDB->prepare("
                SELECT a.*, u.nama, u.email, u.phone 
                FROM anggota a 
                LEFT JOIN people_db.users u ON a.user_id = u.id 
                WHERE a.id = ?
            ");
            $stmt->execute([$memberId]);
            $member = $stmt->fetch();
            
            if ($member) {
                echo json_encode(['success' => true, 'member' => $member]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Anggota tidak ditemukan']);
            }
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;
        
    case 'update':
        // Update member
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $memberId = $data['member_id'];
            
            // Validate required fields
            $required = ['nama', 'nik', 'email', 'phone'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    echo json_encode(['success' => false, 'message' => "Field $field is required"]);
                    exit;
                }
            }
            
            // Update anggota record
            $stmt = $coopDB->prepare("
                UPDATE anggota 
                SET nama = ?, nik = ?, email = ?, phone = ?, alamat = ?, tempat_lahir = ?, tanggal_lahir = ?, jenis_kelamin = ?, updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([
                $data['nama'],
                $data['nik'],
                $data['email'],
                $data['phone'],
                $data['alamat'] ?? '',
                $data['tempat_lahir'] ?? '',
                $data['tanggal_lahir'] ?? '',
                $data['jenis_kelamin'] ?? '',
                $memberId
            ]);
            
            // Update user record
            $stmt = $peopleDB->prepare("
                UPDATE users 
                SET nama = ?, email = ?, phone = ?, updated_at = NOW()
                WHERE id = (SELECT user_id FROM anggota WHERE id = ?)
            ");
            $stmt->execute([
                $data['nama'],
                $data['email'],
                $data['phone'],
                $memberId
            ]);
            
            echo json_encode(['success' => true, 'message' => 'Data anggota berhasil diperbarui']);
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;
        
    case 'update_status':
        // Update member status
        try {
            $memberId = intval($_POST['member_id']);
            $newStatus = $_POST['status'];
            
            $stmt = $coopDB->prepare("UPDATE anggota SET status = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$newStatus, $memberId]);
            
            echo json_encode(['success' => true, 'message' => 'Status berhasil diubah']);
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;
        
    case 'delete':
        // Delete member
        $memberId = intval($_GET['id']);
        
        try {
            // Get user_id before deleting anggota
            $stmt = $coopDB->prepare("SELECT user_id FROM anggota WHERE id = ?");
            $stmt->execute([$memberId]);
            $member = $stmt->fetch();
            
            if ($member) {
                // Delete from people_db users
                $stmt = $peopleDB->prepare("DELETE FROM users WHERE id = ?");
                $stmt->execute([$member['user_id']]);
            }
            
            // Delete from anggota
            $stmt = $coopDB->prepare("DELETE FROM anggota WHERE id = ?");
            $stmt->execute([$memberId]);
            
            echo json_encode(['success' => true, 'message' => 'Anggota berhasil dihapus']);
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;
        
    case 'export':
        // Export members to CSV
        try {
            $stmt = $coopDB->prepare("
                SELECT a.*, u.nama, u.email, u.phone 
                FROM anggota a 
                LEFT JOIN people_db.users u ON a.user_id = u.id 
                ORDER BY a.created_at DESC
            ");
            $stmt->execute();
            $members = $stmt->fetchAll();
            
            // Set headers for CSV download
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="anggota_export_' . date('Y-m-d') . '.csv"');
            
            $output = fopen('php://output', 'w');
            
            // CSV header
            fputcsv($output, ['ID', 'NIK', 'Nama', 'Email', 'Phone', 'Alamat', 'Tempat Lahir', 'Tanggal Lahir', 'Jenis Kelamin', 'Status', 'Tanggal Daftar']);
            
            // CSV data
            foreach ($members as $member) {
                fputcsv($output, [
                    $member['id'],
                    $member['nik'],
                    $member['nama'] ?? '',
                    $member['email'] ?? '',
                    $member['phone'] ?? '',
                    $member['alamat'] ?? '',
                    $member['tempat_lahir'] ?? '',
                    $member['tanggal_lahir'] ?? '',
                    $member['jenis_kelamin'] ?? '',
                    $member['status'],
                    $member['created_at']
                ]);
            }
            
            fclose($output);
            exit;
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>
