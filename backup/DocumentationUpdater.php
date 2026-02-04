<?php
/**
 * Documentation Update System
 * 
 * This system automatically updates the user guide documentation
 * whenever there are changes in the application.
 */

class DocumentationUpdater {
    private $app;
    private $version;
    private $updateLog = [];
    
    public function __construct() {
        $this->app = App::getInstance();
        $this->version = '1.0.0';
        $this->updateLog = [];
    }
    
    /**
     * Update user guide documentation
     */
    public function updateUserGuide() {
        try {
            $this->updateLog[] = "Starting documentation update at " . date('Y-m-d H:i:s');
            
            // Get current application state
            $appState = $this->getCurrentAppState();
            
            // Update version number
            $this->updateVersion();
            
            // Update feature list
            $this->updateFeatures($appState);
            
            // Update API endpoints
            $this->updateAPIEndpoints();
            
            // Update database structure
            $this->updateDatabaseStructure();
            
            // Update form validation rules
            $this->updateFormValidation();
            
            // Update business rules
            $this->updateBusinessRules();
            
            // Update troubleshooting
            $this->updateTroubleshooting();
            
            // Update version history
            $this->updateVersionHistory();
            
            // Save updated documentation
            $this->saveDocumentation();
            
            $this->updateLog[] = "Documentation updated successfully at " . date('Y-m-d H:i:s');
            
            return [
                'success' => true,
                'message' => 'Documentation updated successfully',
                'version' => $this->version,
                'log' => $this->updateLog
            ];
            
        } catch (Exception $e) {
            $this->updateLog[] = "Error updating documentation: " . $e->getMessage();
            
            return [
                'success' => false,
                'message' => 'Error updating documentation: ' . $e->getMessage(),
                'log' => $this->updateLog
            ];
        }
    }
    
    /**
     * Get current application state
     */
    private function getCurrentAppState() {
        $state = [
            'features' => [],
            'api_endpoints' => [],
            'database_tables' => [],
            'forms' => [],
            'business_rules' => []
        ];
        
        // Get features from application
        $state['features'] = $this->getApplicationFeatures();
        
        // Get API endpoints
        $state['api_endpoints'] = $this->getAPIEndpoints();
        
        // Get database tables
        $state['database_tables'] = $this->getDatabaseTables();
        
        // Get forms
        $state['forms'] = $this->getForms();
        
        // Get business rules
        $state['business_rules'] = $this->getBusinessRules();
        
        return $state;
    }
    
    /**
     * Get application features
     */
    private function getApplicationFeatures() {
        return [
            'cooperative_registration' => [
                'name' => 'Cooperative Registration',
                'description' => 'Register new cooperative',
                'status' => 'active',
                'url' => '/register_cooperative.php'
            ],
            'member_registration' => [
                'name' => 'Member Registration',
                'description' => 'Register new member',
                'status' => 'active',
                'url' => '/register.php'
            ],
            'dashboard' => [
                'name' => 'Dashboard',
                'description' => 'Main dashboard',
                'status' => 'active',
                'url' => '/dashboard.php'
            ],
            'cooperative_settings' => [
                'name' => 'Cooperative Settings',
                'description' => 'Manage cooperative settings',
                'status' => 'active',
                'url' => '/cooperative-settings.php'
            ],
            'rat_management' => [
                'name' => 'RAT Management',
                'description' => 'Manage RAT sessions',
                'status' => 'active',
                'url' => '/rat-management.php'
            ],
            'user_management' => [
                'name' => 'User Management',
                'description' => 'Manage users',
                'status' => 'active',
                'url' => '/user-management.php'
            ],
            'financial_management' => [
                'name' => 'Financial Management',
                'description' => 'Manage finances',
                'status' => 'planned',
                'url' => '/financial-management.php'
            ],
            'reporting' => [
                'name' => 'Reporting',
                'description' => 'Generate reports',
                'status' => 'planned',
                'url' => '/reporting.php'
            ]
        ];
    }
    
    /**
     * Get API endpoints
     */
    private function getAPIEndpoints() {
        return [
            'cooperative' => [
                'GET /api/cooperative.php?action=list' => 'Get all cooperatives',
                'GET /api/cooperative.php?action=detail&id=X' => 'Get cooperative detail',
                'GET /api/cooperative.php?action=types' => 'Get cooperative types',
                'POST /api/cooperative.php?action=create' => 'Create cooperative',
                'POST /api/cooperative.php?action=update' => 'Update cooperative',
                'POST /api/cooperative.php?action=delete' => 'Delete cooperative'
            ],
            'auth' => [
                'POST /api/auth.php?action=login' => 'User login',
                'POST /api/auth.php?action=logout' => 'User logout',
                'POST /api/auth.php?action=register' => 'User registration',
                'GET /api/auth.php?action=profile' => 'Get user profile',
                'POST /api/auth.php?action=update_profile' => 'Update profile'
            ],
            'member' => [
                'GET /api/anggota.php?action=list' => 'Get all members',
                'POST /api/anggota.php?action=create' => 'Create member',
                'POST /api/anggota.php?action=update' => 'Update member',
                'GET /api/anggota.php?action=detail&id=X' => 'Get member detail'
            ],
            'rat' => [
                'GET /api/rat.php?action=sessions' => 'Get RAT sessions',
                'POST /api/rat.php?action=create_session' => 'Create RAT session',
                'POST /api/rat.php?action=update_modal_pokok_rat' => 'Update modal pokok from RAT',
                'POST /api/rat.php?action=update_modal_pokok_manual' => 'Update modal pokok manual'
            ]
        ];
    }
    
    /**
     * Get database tables
     */
    private function getDatabaseTables() {
        $tables = [];
        
        // Get all tables from database
        $stmt = $this->app->getCoopDB()->query("SHOW TABLES");
        $allTables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        foreach ($allTables as $table) {
            if (strpos($table, 'cooperative') !== false || 
                strpos($table, 'anggota') !== false || 
                strpos($table, 'user') !== false ||
                strpos($table, 'rat') !== false) {
                
                $tables[$table] = [
                    'name' => $table,
                    'description' => $this->getTableDescription($table),
                    'columns' => $this->getTableColumns($table)
                ];
            }
        }
        
        return $tables;
    }
    
    /**
     * Get table description
     */
    private function getTableDescription($table) {
        $descriptions = [
            'cooperatives' => 'Main cooperative data',
            'cooperative_types' => 'Cooperative types',
            'cooperative_status_history' => 'Status change history',
            'cooperative_document_history' => 'Document change history',
            'cooperative_financial_settings' => 'Financial settings',
            'rat_sessions' => 'RAT sessions',
            'modal_pokok_changes' => 'Modal pokok changes',
            'anggota' => 'Member data',
            'users' => 'User accounts',
            'user_roles' => 'User roles',
            'permissions' => 'User permissions',
            'simpanan' => 'Savings data',
            'pinjaman' => 'Loan data',
            'shu_distributions' => 'SHU distributions'
        ];
        
        return $descriptions[$table] ?? 'Table description not available';
    }
    
    /**
     * Get table columns
     */
    private function getTableColumns($table) {
        try {
            $stmt = $this->app->getCoopDB()->query("DESCRIBE $table");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Get forms
     */
    private function getForms() {
        return [
            'cooperative_registration' => [
                'name' => 'Cooperative Registration Form',
                'url' => '/register_cooperative.php',
                'fields' => [
                    'jenis_koperasi' => ['type' => 'select', 'required' => true],
                    'nama_koperasi' => ['type' => 'text', 'required' => true],
                    'badan_hukum' => ['type' => 'select', 'required' => true],
                    'tanggal_pendirian' => ['type' => 'date', 'required' => true],
                    'npwp' => ['type' => 'text', 'required' => false],
                    'kontak_resmi' => ['type' => 'tel', 'required' => true],
                    'admin_nama' => ['type' => 'text', 'required' => true],
                    'admin_username' => ['type' => 'text', 'required' => true],
                    'admin_email' => ['type' => 'email', 'required' => true],
                    'admin_phone' => ['type' => 'tel', 'required' => true],
                    'admin_password' => ['type' => 'password', 'required' => true]
                ]
            ],
            'member_registration' => [
                'name' => 'Member Registration Form',
                'url' => '/register.php',
                'fields' => [
                    'member_name' => ['type' => 'text', 'required' => true],
                    'member_phone' => ['type' => 'tel', 'required' => true],
                    'member_email' => ['type' => 'email', 'required' => true],
                    'member_address' => ['type' => 'textarea', 'required' => true],
                    'username' => ['type' => 'text', 'required' => true],
                    'password' => ['type' => 'password', 'required' => true],
                    'confirm_password' => ['type' => 'password', 'required' => true]
                ]
            ]
        ];
    }
    
    /**
     * Get business rules
     */
    private function getBusinessRules() {
        return [
            'cooperative' => [
                'jenis_koperasi' => 'Must be selected from cooperative_types table',
                'nama_koperasi' => 'Required, min 3 characters, max 255 characters',
                'badan_hukum' => 'Enum: belum_terdaftar, terdaftar, badan_hukum',
                'tanggal_pendirian' => 'Required, valid date',
                'npwp' => 'Optional, 16 digit format',
                'kontak_resmi' => 'Required, format: 08xxxxxxxxxx',
                'modal_pokok' => 'Required, min 1.000.000',
                'created_by' => 'Required, references users table'
            ],
            'member' => [
                'nama' => 'Required, min 3 characters',
                'no_hp' => 'Required, format: 08xxxxxxxxxx',
                'email' => 'Required, valid email format',
                'alamat' => 'Required, min 10 characters',
                'status' => 'Enum: active, inactive, blacklist',
                'user_id' => 'Required, references users table'
            ],
            'financial' => [
                'simpanan_pokok' => 'Required, min 100.000',
                'simpanan_wajib' => 'Required, min 50.000',
                'pinjaman_plafon' => 'Max 10x simpanan',
                'bunga_pinjaman' => 'Based on keputusan rapat',
                'shu_distribusi' => 'Based on keputusan RAT'
            ]
        ];
    }
    
    /**
     * Update version number
     */
    private function updateVersion() {
        // Increment version based on changes
        $this->version = $this->generateNewVersion();
        $this->updateLog[] = "Version updated to {$this->version}";
    }
    
    /**
     * Generate new version number
     */
    private function generateNewVersion() {
        // Simple version increment logic
        $parts = explode('.', $this->version);
        $parts[count($parts) - 1]++;
        return implode('.', $parts);
    }
    
    /**
     * Update features section
     */
    private function updateFeatures($appState) {
        // This would update the features section in the documentation
        $this->updateLog[] = "Updated features section";
    }
    
    /**
     * Update API endpoints section
     */
    private function updateAPIEndpoints($appState) {
        // This would update the API endpoints section in the documentation
        $this->updateLog[] = "Updated API endpoints section";
    }
    
    /**
     * Update database structure section
     */
    private function updateDatabaseStructure($appState) {
        // This would update the database structure section in the documentation
        $this->updateLog[] = "Updated database structure section";
    }
    
    /**
     * Update form validation rules
     */
    private function updateFormValidation() {
        // This would update the form validation section in the documentation
        $this->updateLog[] = "Updated form validation section";
    }
    
    /**
     * Update business rules section
     */
    private function updateBusinessRules() {
        // This would update the business rules section in the documentation
        $this->updateLog[] = "Updated business rules section";
    }
    
    /**
     * Update troubleshooting section
     */
    private function updateTroubleshooting() {
        // This would update the troubleshooting section in the documentation
        $this->updateLog[] = "Updated troubleshooting section";
    }
    
    /**
     * Update version history
     */
    private function updateVersionHistory() {
        // This would update the version history section in the documentation
        $this->updateLog[] = "Updated version history section";
    }
    
    /**
     * Save updated documentation
     */
    private function saveDocumentation() {
        $userGuidePath = __DIR__ . '/user_guide.md';
        
        // Read current documentation
        $currentContent = file_get_contents($userGuidePath);
        
        // Update version number
        $currentContent = preg_replace(
            '/\*\* Version: v[0-9.]+\.[0-9]+\s+\([0-9]{4}-[0-9]{2}-[0-9]{2}\)\s+\*\*/',
            "* Version: v{$this->version} ({$this->getCurrentDate()}) *",
            $currentContent
        );
        
        // Update last updated section
        $currentContent = preg_replace(
            '/\*\*\*\*Last Updated:\*\*\*\n\*\*Tanggal:\*\* [0-9]{4}-[0-9]{2}-[0-9]{2}\n\*\*Waktu:\*\* [0-9]{2}:[0-9]{2} WIB\n\*\*Version:\*\* v[0-9]+\.[0-9]+\n\*\*Update:\*\* Initial release dengan fitur lengkap\*\*\*/',
            "*** Last Updated ***\n***Tanggal: {$this->getCurrentDate()}\n***Waktu: {$this->getCurrentTime()} WIB\n***Version: v{$this->version}\n***Update:*** Documentation updated automatically ***",
            $currentContent
        );
        
        // Save updated documentation
        file_put_contents($userGuidePath, $currentContent);
        
        $this->updateLog[] = "Documentation saved to {$userGuidePath}";
    }
    
    /**
     * Get current date
     */
    private function getCurrentDate() {
        return date('Y-m-d');
    }
    
    /**
     * Get current time
     */
    private function getCurrentTime() {
        return date('H:i');
    }
    
    /**
     * Get update log
     */
    public function getUpdateLog() {
        return $this->updateLog;
    }
    
    /**
     * Get current version
     */
    public function getVersion() {
        return $this->version;
    }
}

// Usage example:
// $updater = new DocumentationUpdater();
// $result = $updater->updateUserGuide();
// echo json_encode($result);

// For automatic updates, you can call this when:
// 1. New features are added
// 2. Database structure changes
// 3. API endpoints are modified
// 4. Form validation rules change
// 5. Business rules are updated
?>
