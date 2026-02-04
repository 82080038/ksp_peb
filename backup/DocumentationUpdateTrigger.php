<?php
/**
 * Documentation Update Trigger
 * 
 * This system triggers documentation updates when changes are detected
 * in the application.
 */

require_once __DIR__ . '/AutoDocumentationUpdater.php';

class DocumentationUpdateTrigger {
    private $autoUpdater;
    private $triggerLog = [];
    
    public function __construct() {
        $this->autoUpdater = new AutoDocumentationUpdater();
        $this->triggerLog = [];
    }
    
    /**
     * Trigger documentation update after database migration
     */
    public function triggerAfterMigration() {
        try {
            $this->triggerLog[] = "Triggering documentation update after database migration";
            
            $result = $this->autoUpdater->autoUpdate();
            
            if ($result['success']) {
                $this->triggerLog[] = "Documentation updated successfully after migration";
                $this->triggerLog[] = "New version: " . $result['version'];
            } else {
                $this->triggerLog[] = "Failed to update documentation after migration: " . $result['message'];
            }
            
            return $result;
            
        } catch (Exception $e) {
            $this->triggerLog[] = "Error triggering documentation update after migration: " . $e->getMessage();
            
            return [
                'success' => false,
                'message' => 'Error triggering documentation update after migration: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Trigger documentation update after file deployment
     */
    public function triggerAfterDeployment() {
        try {
            $this->triggerLog[] = "Triggering documentation update after file deployment";
            
            $result = $this->autoUpdater->autoUpdate();
            
            if ($result['success']) {
                $this->triggerLog[] = "Documentation updated successfully after deployment";
                $this->triggerLog[] = "New version: " . $result['version'];
            } else {
                $this->triggerLog[] = "Failed to update documentation after deployment: " . $result['message'];
            }
            
            return $result;
            
        } catch (Exception $e) {
            $this->triggerLog[] = "Error triggering documentation update after deployment: " . $e->getMessage();
            
            return [
                'success' => false,
                'message' => 'Error triggering documentation update after deployment: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Trigger documentation update after API changes
     */
    public function triggerAfterAPIChanges() {
        try {
            $this->triggerLog[] = "Triggering documentation update after API changes";
            
            $result = $this->autoUpdater->autoUpdate();
            
            if ($result['success']) {
                $this->triggerLog[] = "Documentation updated successfully after API changes";
                $this->triggerLog[] = "New version: " . $result['version'];
            } else {
                $this->triggerLog[] = "Failed to update documentation after API changes: " . $result['message'];
            }
            
            return $result;
            
        } catch (Exception $e) {
            $triggerLog[] = "Error triggering documentation update after API changes: " . $e->getMessage();
            
            return [
                'success' => false,
                'message' => 'Error triggering documentation update after API changes: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Trigger documentation update after form changes
     */
    public function triggerAfterFormChanges() {
        try {
            $this->triggerLog[] = "Triggering documentation update after form changes";
            
            $result = $this->autoUpdater->autoUpdate();
            
            if ($result['success']) {
                $this->triggerLog[] = "Documentation updated successfully after form changes";
                $this->triggerLog[] = "New version: " . $result['version'];
            } else {
                $this->triggerLog[] = "Failed to update documentation after form changes: " . $result['message'];
            }
            
            return $result;
            
        } catch (Exception $e) {
            $triggerLog[] = "Error triggering documentation update after form changes: " . $e->getMessage();
            
            return [
                'success' => false,
                'message' => 'Error triggering documentation update after form changes: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Trigger documentation update on schedule
     */
    public function triggerOnSchedule($schedule = 'daily') {
        try {
            $this->triggerLog[] = "Triggering documentation update on schedule: {$schedule}";
            
            $result = $this->autoUpdater->autoUpdate();
            
            if ($result['success']) {
                $this->triggerLog[] = "Documentation updated successfully on schedule: {$schedule}";
                $this->triggerLog[] = "New version: " . $result['version'];
            } else {
                $this->triggerLog[] = "Failed to update documentation on schedule: {$schedule}";
            }
            
            return $result;
            
        } catch (exception $e) {
            $triggerLog[] = "Error triggering documentation update on schedule: {$schedule}: " . $e->getMessage();
            
            return [
                'success' => false,
                'message' => 'Error triggering documentation update on schedule: {$schedule}: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Get trigger log
     */
    public function getTriggerLog() {
        return $this->triggerLog;
    }
    
    /**
     * Clear trigger log
     */
    public function clearTriggerLog() {
        $this->triggerLog = [];
    }
    
    /**
     * Get update summary
     */
    public function getUpdateSummary() {
        return [
            'last_trigger' => date('Y-m-d H:i:s'),
            'current_version' => $this->autoUpdater->getVersion(),
            'log' => $this->triggerLog
        ];
    }
}

// Usage examples:
// $trigger = new DocumentationUpdateTrigger();
// 
// After database migration:
// $trigger->triggerAfterMigration();
// 
// After file deployment:
// $trigger->triggerAfterDeployment();
// 
// After API changes:
// $trigger->triggerAfterAPIChanges();
// 
// After form changes:
// $trigger->triggerAfterFormChanges();
// 
// On schedule (daily):
// $trigger->triggerOnSchedule('daily');
// 
// Get results:
// echo json_encode($trigger->getUpdateSummary());
?>
