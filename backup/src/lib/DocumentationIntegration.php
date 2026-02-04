<?php
/**
 * Documentation Integration
 * 
 * This file integrates the documentation update system
 * with the main application.
 */

require_once __DIR__ . '/AutoDocumentationUpdater.php';
require_once __DIR__ . '/DocumentationUpdateTrigger.php';

class DocumentationIntegration {
    private $autoUpdater;
    private $trigger;
    private $integrationLog = [];
    
    public function __construct() {
        $this->autoUpdater = new AutoDocumentationUpdater();
        $this->trigger = new DocumentationUpdateTrigger();
        $this->integrationLog = [];
    }
    
    /**
     * Initialize documentation integration
     */
    public function initialize() {
        try {
            $this->integrationLog[] = "Initializing documentation integration";
            
            // Check if auto update is enabled
            $config = $this->trigger->getConfig();
            if (!$config['auto_update']) {
                $this->integrationLog[] = "Auto update is disabled";
                return false;
            }
            
            $this->integrationLog[] = "Documentation integration initialized successfully";
            return true;
            
        } catch (Exception $e) {
            $this->integrationLog[] = "Error initializing documentation integration: " . $e->getMessage();
            return false;
        }
    }
    
    /**
     * Trigger documentation update after cooperative creation
     */
    public function afterCooperativeCreated($cooperativeData) {
        try {
            $this->integrationLog[] = "Triggering documentation update after cooperative creation";
            
            $result = $this->trigger->triggerAfterFormChanges();
            
            if ($result['success']) {
                $this->integrationLog[] = "Documentation updated after cooperative creation";
                $this->integrationLog[] = "New version: " . $result['version'];
                
                // Log cooperative creation details
                $this->integrationLog[] = "Cooperative created: " . $cooperativeData['nama'];
                $this->integrationLog[] = "Jenis: " . (is_array($cooperativeData['jenis']) ? $cooperativeData['jenis']['name'] : $cooperativeData['jenis']);
            } else {
                $this->integrationLog[] = "Failed to update documentation after cooperative creation: " . $result['message'];
            }
            
            return $result;
            
        } catch (Exception $e) {
            $this->integrationLog[] = "Error triggering documentation update after cooperative creation: " . $e->getMessage();
            
            return [
                'success' => false,
                'message' => 'Error triggering documentation update after cooperative creation: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Trigger documentation update after member registration
     */
    public function afterMemberRegistered($memberData) {
        try {
            $this->integrationLog[] = "Triggering documentation update after member registration";
            
            $result = $this->trigger->triggerAfterFormChanges();
            
            if ($result['success']) {
                $this->integrationLog[] = "Documentation updated after member registration";
                $this->integrationLog[] = "New version: " . $result['version'];
                
                // Log member registration details
                $this->integrationLog[] = "Member registered: " . $memberData['nama'];
                $this->integrationLog[] = "Phone: " . $memberData['no_hp'];
            } else {
                $this->integrationLog[] = "Failed to update documentation after member registration: " . $result['message'];
            }
            
            return $result;
            
        } catch (Exception $e) {
            $this->integrationLog[] = "Error triggering documentation update after member registration: " . $e->getMessage();
            
            return [
                'success' => false,
                'message' => 'Error triggering documentation update after member registration: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Trigger documentation update after financial settings update
     */
    public function afterFinancialSettingsUpdate($financialData) {
        try {
            $this->integrationLog[] = "Triggering documentation update after financial settings update";
            
            $result = $this->trigger->triggerAfterFormChanges();
            
            if ($result['success']) {
                $this->integrationLog[] = "Documentation updated after financial settings update";
                $this->integrationLog[] = "New version: " . $result['version'];
            } else {
                $this->integrationLog[] = "Failed to update documentation after financial settings update: " . $result['message'];
            }
            
            return $result;
            
        } catch (Exception $e) {
            $this->integrationLog[] = "Error triggering documentation update after financial settings update: " . $e->getMessage();
            
            return [
                'success' => false,
                'message' => 'Error triggering documentation update after financial settings update: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Trigger documentation update after legal document update
     */
    public function afterLegalDocumentUpdate($documentData) {
        try {
            $this->integrationLog[] = "Triggering documentation update after legal document update";
            
            $result = $this->trigger->triggerAfterFormChanges();
            
            if ($result['success']) {
                $this->integrationLog[] = "Documentation updated after legal document update";
                $this->integrationLog[] = "New version: " . $result['version'];
                
                // Log document update details
                $this->integrationLog[] = "Document type: " . $documentData['document_type'];
                $this->integrationLog[] = "Document number: " . ($documentData['document_number'] ?? 'N/A');
            } else {
                $this->integrationLog[] = "Failed to update documentation after legal document update: " . $result['message'];
            }
            
            return $result;
            
        } catch (Exception $e) {
            $this->integrationLog[] = "Error triggering documentation update after legal document update: " . $e->getMessage();
            
            return [
                'success' => false,
                'message' => 'Error triggering documentation update after legal document update: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Trigger documentation update after RAT session
     */
    public function afterRATSession($ratData) {
        try {
            $this->integrationLog[] = "Triggering documentation update after RAT session";
            
            $result = $this->trigger->triggerAfterFormChanges();
            
            if ($result['success']) {
                $this->integrationLog[] = "Documentation updated after RAT session";
                $this->integrationLog[] = "New version: " . $result['version'];
                
                // Log RAT session details
                $this->integrationLog[] = "RAT tahun: " . $ratData['tahun'];
                $this->integrationLog[] = "Modal pokok: Rp " . number_format($ratData['modal_pokok_setelah']);
            } else {
                $this->integrationLog[] = "Failed to update documentation after RAT session: " . $result['message'];
            }
            
            return $result;
            
        } catch (Exception $e) {
            $this->integrationLog[] = "Error triggering documentation update after RAT session: " . $e->getMessage();
            
            return [
                'success' => false,
                'message' => 'Error triggering documentation update after RAT session: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Trigger documentation update on schedule
     */
    public function triggerScheduledUpdate($schedule = 'daily') {
        try {
            $this->integrationLog[] = "Triggering scheduled documentation update: {$schedule}";
            
            $result = $this->trigger->triggerOnSchedule($schedule);
            
            if ($result['success']) {
                $this->integrationLog[] = "Documentation updated on schedule: {$schedule}";
                $this->integrationLog[] = "New version: " . $result['version'];
            } else {
                $this->integrationLog[] = "Failed to update documentation on schedule: {$schedule}: " . $result['message'];
            }
            
            return $result;
            
        } catch (Exception $e) {
            $this->integrationLog[] = "Error triggering scheduled documentation update: {$schedule}: " . $e->getMessage();
            
            return [
                'success' => false,
                'message' => 'Error triggering scheduled documentation update: {$schedule}: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Get integration log
     */
    public function getIntegrationLog() {
        return $this->integrationLog;
    }
    
    /**
     * Clear integration log
     */
    public function clearIntegrationLog() {
        $this->integrationLog = [];
    }
    
    /**
     * Get integration summary
     */
    public function getIntegrationSummary() {
        return [
            'last_trigger' => date('Y-m-d H:i:s'),
            'current_version' => $this->autoUpdater->getVersion(),
            'trigger_log' => $this->trigger->getTriggerLog(),
            'integration_log' => $this->integrationLog
        ];
    }
}

// Usage examples:
// $integration = new DocumentationIntegration();
// 
// Initialize integration:
// $integration->initialize();
// 
// After cooperative creation:
// $integration->afterCooperativeCreated($cooperativeData);
// 
// After member registration:
// $integration->afterMemberRegistered($memberData);
// 
// After financial settings update:
// $integration->afterFinancialSettingsUpdate($financialData);
// 
// After legal document update:
// $integration->afterLegalDocumentUpdate($documentData);
// 
// After RAT session:
// $integration->afterRATSession($ratData);
// 
// On schedule:
// $integration->triggerScheduledUpdate('daily');
// 
// Get results:
// echo json_encode($integration->getIntegrationSummary());
?>
