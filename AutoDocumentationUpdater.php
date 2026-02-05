<?php
/**
 * Auto Documentation Update System
 * 
 * This system automatically updates the user guide documentation
 * whenever there are changes in the application.
 */

require_once __DIR__ . '/DocumentationUpdater.php';

class AutoDocumentationUpdater {
    private $documentationUpdater;
    private $updateLog = [];
    private $config;
    
    public function __construct() {
        $this->documentationUpdater = new DocumentationUpdater();
        $this->updateLog = [];
        $this->config = [
            'auto_update' => true,
            'monitor_database' => true,
            'monitor_files' => true,
            'monitor_api' => true,
            'monitor_forms' => true
        ];
    }
    
    /**
     * Auto update documentation
     */
    public function autoUpdate() {
        try {
            $this->updateLog[] = "Starting auto documentation update at " . date('Y-m-d H:i:s');
            
            if (!$this->config['auto_update']) {
                $this->updateLog[] = "Auto update is disabled";
                return ['success' => false, 'message' => 'Auto update is disabled'];
            }
            
            $changesDetected = false;
            
            // Monitor database changes
            if ($this->config['monitor_database']) {
                $dbChanges = $this->monitorDatabaseChanges();
                if (!empty($dbChanges)) {
                    $changesDetected = true;
                    $this->updateLog[] = "Database changes detected: " . count($dbChanges) . " tables";
                }
            }
            
            // Monitor file changes
            if ($this->config['monitor_files']) {
                $fileChanges = $this->monitorFileChanges();
                if (!empty($fileChanges)) {
                    $changesDetected = true;
                    $this->updateLog[] = "File changes detected: " . count($fileChanges) . " files";
                }
            }
            
            // Monitor API changes
            if ($this->config['monitor_api']) {
                $apiChanges = $this->monitorAPIChanges();
                if (!empty($apiChanges)) {
                    $changesDetected = true;
                    $this->updateLog[] = "API changes detected: " . count($apiChanges) . " endpoints";
                }
            }
            
            // Monitor form changes
            if ($this->config['monitor_forms']) {
                $formChanges = $this->monitorFormChanges();
                if (!empty($formChanges)) {
                    $changesDetected = true;
                    $this->updateLog[] = "Form changes detected: " . count($formChanges) . " forms";
                }
            }
            
            // Update documentation if changes detected
            if ($changesDetected) {
                $this->updateLog[] = "Changes detected, updating documentation";
                
                $result = $this->documentationUpdater->updateUserGuide();
                
                if ($result['success']) {
                    $this->updateLog[] = "Documentation updated successfully to version " . $result['version'];
                    $this->updateLog[] = "Update log: " . implode(', ', $result['log']);
                } else {
                    $this->updateLog[] = "Failed to update documentation: " . $result['message'];
                }
            } else {
                $this->updateLog[] = "No changes detected, documentation is up to date";
            }
            
            return [
                'success' => true,
                'message' => 'Auto documentation update completed',
                'changes_detected' => $changesDetected,
                'documentation_updated' => $changesDetected,
                'version' => $this->documentationUpdater->getVersion(),
                'log' => $this->updateLog
            ];
            
        } catch (Exception $e) {
            $this->updateLog[] = "Error in auto update: " . $e->getMessage();
            
            return [
                'success' => false,
                'message' => 'Error in auto update: ' . $e->getMessage(),
                'log' => $this->updateLog
            ];
        }
    }
    
    /**
     * Monitor database changes
     */
    private function monitorDatabaseChanges() {
        $changes = [];
        
        // Monitor key tables
        $tables = [
            'cooperatives',
            'cooperative_types',
            'cooperative_status_history',
            'cooperative_document_history',
            'rat_sessions',
            'modal_pokok_changes'
        ];
        
        foreach ($tables as $table) {
            $tableChange = $this->checkTableChange($table);
            if ($tableChange) {
                $changes[] = $tableChange;
            }
        }
        
        return $changes;
    }
    
    /**
     * Check table change
     */
    private function checkTableChange($tableName) {
        try {
            // Get current table structure
            $stmt = $this->app->getCoopDB()->query("DESCRIBE $tableName");
            $currentStructure = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get stored structure
            $storedStructure = $this->getStoredStructure($tableName);
            
            // Compare structures
            if ($storedStructure === null || $currentStructure !== $storedStructure) {
                $change = [
                    'table' => $tableName,
                    'type' => 'structure_change',
                    'timestamp' => date('Y-m-d H:i:s'),
                    'old_structure' => $storedStructure,
                    'new_structure' => $currentStructure
                ];
                
                // Store new structure
                $this->storeStructure($tableName, $currentStructure);
                
                return $change;
            }
        } catch (Exception $e) {
            $this->updateLog[] = "Error checking table {$tableName}: " . $e->getMessage();
            return null;
        }
        
        return null;
    }
    
    /**
     * Monitor file changes
     */
    private function monitorFileChanges() {
        $changes = [];
        
        // Monitor key files
        $files = [
            __DIR__ . '/../src/public/api/cooperative.php',
            __DIR__ . '/../src/public/api/auth.php',
            __DIR__ . '/../src/public/api/anggota.php',
            __DIR__ . '/../src/public/api/rat.php',
            __DIR__ . '/../register_cooperative.php',
            __DIR__ . '/../register.php',
            __DIR__ . '/../src/public/dashboard/cooperative-settings.php',
            __DIR__ . '/../src/public/dashboard/rat-management.php',
            __DIR__ . '/../app/Cooperative.php',
            __DIR__ . '/../app/Auth.php'
        ];
        
        foreach ($files as $file) {
            $fileChange = $this->checkFileChange($file);
            if ($fileChange) {
                $changes[] = $fileChange;
            }
        }
        
        return $changes;
    }
    
    /**
     * Check file change
     */
    private function checkFileChange($filePath) {
        try {
            if (!file_exists($filePath)) {
                return null;
            }
            
            $currentModified = filemtime($filePath);
            $storedModified = $this->getStoredModified($filePath);
            
            if ($storedModified === null || $currentModified > $storedModified) {
                $change = [
                    'file' => basename($filePath),
                    'path' => $filePath,
                    'type' => 'file_change',
                    'timestamp' => date('Y-m-d H:i:s', $currentModified),
                    'old_modified' => $storedModified,
                    'new_modified' => $currentModified
                ];
                
                // Store new modified time
                $this->storeModified($filePath, $currentModified);
                
                return $change;
            }
        } catch (Exception $e) {
            $this->updateLog[] = "Error checking file {$filePath}: " . $e->getMessage();
            return null;
        }
        
        return null;
    }
    
    /**
     * Monitor API changes
     */
    private function monitorAPIChanges() {
        $changes = [];
        
        // Monitor API files
        $files = [
            __DIR__ . '/../src/public/api/cooperative.php',
            __DIR__ . '/../src/public/api/auth.php',
            __DIR__ . '/../src/public/api/anggota.php',
            __DIR__ . '/../src/public/api/rat.php'
        ];
        
        foreach ($files as $file) {
            $fileChange = $this->checkFileChange($file);
            if ($fileChange) {
                $changes[] = $fileChange;
            }
        }
        
        return $changes;
    }
    
    /**
     * Monitor form changes
     */
    private function monitorFormChanges() {
        $changes = [];
        
        // Monitor form files
        $files = [
            __DIR__ . '/../register_cooperative.php',
            __DIR__ . '/../register.php',
            __DIR__ . '/../src/public/dashboard/cooperative-settings.php',
            __DIR__ . '/../src/public/dashboard/rat-management.php'
        ];
        
        foreach ($files as $file) {
            $fileChange = $this->checkFileChange($file);
            if ($fileChange) {
                $changes[] = $fileChange;
            }
        }
        
        return $changes;
    }
    
    /**
     * Get stored structure
     */
    private function getStoredStructure($tableName) {
        $cacheFile = __DIR__ . '/../cache/structure_' . $tableName . '.json';
        
        if (file_exists($cacheFile)) {
            return json_decode(file_get_contents($cacheFile), true);
        }
        
        return null;
    }
    
    /**
     * Store structure
     */
    private function storeStructure($tableName, $structure) {
        $cacheFile = __DIR__ . '/../cache/structure_' . $tableName . '.json';
        
        $cacheDir = dirname($cacheFile);
        if (!file_exists($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }
        
        file_put_contents($cacheFile, json_encode($structure, JSON_PRETTY_PRINT));
    }
    
    /**
     * Get stored modified time
     */
    private function getStoredModified($filePath) {
        $cacheFile = __DIR__ . '/../cache/modified_' . md5($filePath) . '.json';
        
        if (file_exists($cacheFile)) {
            return json_decode(file_get_contents($cacheFile), true);
        }
        
        return null;
    }
    
    /**
     * Store modified time
     */
    private function storeModified($filePath, $modified) {
        $cacheFile = __DIR__ . '/../cache/modified_' . md5($filePath) . '.json';
        
        $cacheDir = dirname($cacheFile);
        if (!file_exists($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }
        
        file_put_contents($cacheFile, json_encode($modified));
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
    public function getCurrentVersion() {
        return $this->documentationUpdater->getVersion();
    }
    
    /**
     * Clear cache
     */
    public function clearCache() {
        $cacheDir = __DIR__ . '/../cache';
        
        if (file_exists($cacheDir)) {
            $files = glob($cacheDir . '/*');
            foreach ($files as $file) {
                unlink($file);
            }
        }
        
        $this->updateLog[] = "Cache cleared at " . date('Y-m-d H:i:s');
    }
    
    /**
     * Get change summary
     */
    public function getChangeSummary() {
        return [
            'last_check' => date('Y-m-d H:i:s'),
            'current_version' => $this->getCurrentVersion(),
            'config' => $this->config,
            'log' => $this->updateLog
        ];
    }
    
    /**
     * Set configuration
     */
    public function setConfig($config) {
        $this->config = array_merge($this->config, $config);
    }
    
    /**
     * Get configuration
     */
    public function getConfig() {
        return $this->config;
    }
}

// Usage example:
// $updater = new AutoDocumentationUpdater();
// $result = $updater->autoUpdate();
// echo json_encode($result);

// For automatic updates, you can call this:
// 1. After database migrations
// 2. After file deployments
// 3. After API changes
// 4. After form updates
// 5. On a schedule (e.g., daily at midnight)
?>
