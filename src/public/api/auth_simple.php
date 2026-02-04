<?php
// Simple API endpoint untuk testing
header('Content-Type: application/json');

// User dummy untuk testing
$dummyUsers = [
    '820800' => [
        'password' => '820800', 
        'roles' => ['admin', 'super_admin']
    ],
    'admin' => [
        'password' => 'admin123', 
        'roles' => ['admin']
    ]
];

$action = $_GET['action'] ?? '';

switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        switch ($action) {
            case 'login':
                $data = json_decode(file_get_contents('php://input'), true);
                $username = $data['username'] ?? '';
                $password = $data['password'] ?? '';
                
                // Check dummy users
                if (isset($dummyUsers[$username]) && 
                    $dummyUsers[$username]['password'] === $password) {
                    echo json_encode([
                        'success' => true,
                        'user' => [
                            'id' => 1,
                            'nama' => 'User 820800',
                            'username' => $username,
                            'roles' => $dummyUsers[$username]['roles']
                        ]
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Username atau password salah'
                    ]);
                }
                break;
                
            default:
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }
        break;
        
    case 'GET':
        switch ($action) {
            case 'check':
                echo json_encode(['success' => true, 'logged_in' => false]);
                break;
                
            default:
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>
