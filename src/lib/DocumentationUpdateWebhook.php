<?php
/**
 * Documentation Update Webhook
 * 
 * This webhook automatically updates the documentation
 * when changes are detected in the application.
 */

require_once __DIR__ . '/AutoDocumentationUpdater.php';

class DocumentationUpdateWebhook {
    private $autoUpdater;
    private $webhookLog = [];
    private $config;
    
    public function __construct() {
        $this->autoUpdater = new AutoDocumentationUpdater();
        $this->webhookLog = [];
        $this->config = [
            'webhook_enabled' => true,
            'webhook_url' => null, // Set this in configuration
            'webhook_secret' => null, // Set this in configuration
            'auto_update' => true
        ];
    }
    
    /**
     * Handle webhook request
     */
    public function handleWebhook($payload) {
        try {
            $this->webhookLog[] = "Webhook received: " . json_encode($payload);
            
            // Verify webhook secret if configured
            if ($this->config['webhook_secret'] && 
                (!isset($payload['secret']) || 
                $payload['secret'] !== $this->config['webhook_secret'])) {
                $this->webhookLog[] = "Invalid webhook secret";
                return [
                    'success' => false,
                    'message' => 'Invalid webhook secret'
                ];
            }
            
            // Verify webhook URL if configured
            if ($this->config['webhook_url'] && 
                (!isset($payload['url']) || 
                $payload['url'] !== $this->config['webhook_url'])) {
                $this->webhookLog[] = "Invalid webhook URL";
                return [
                    'success' => false,
                    'message' => 'Invalid webhook URL'
                ];
            }
            
            // Update documentation
            $result = $this->autoUpdater->autoUpdate();
            
            if ($result['success']) {
                $this->webhookLog[] = "Documentation updated via webhook to version " . $result['version'];
                $this->webhookLog[] = "Update log: " . implode(', ', $result['log']);
            } else {
                $this->webhookLog[] = "Failed to update documentation via webhook: " . $result['message'];
            }
            
            return [
                'success' => $result['success'],
                'message' => $result['message'],
                'version' => $result['version'],
                'log' => $this->webhookLog
            ];
            
        } catch (Exception $e) {
            $this->webhookLog[] = "Error handling webhook: " . $e->getMessage();
            
            return [
                'success' => false,
                'message' => 'Error handling webhook: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Set webhook configuration
     */
    public function setWebhookConfig($config) {
        $this->config = array_merge($this->config, $config);
    }
    
    /**
     * Get webhook log
     */
    public function getWebhookLog() {
        return $this->webhookLog;
    }
    
    /**
     * Clear webhook log
     */
    public function clearWebhookLog() {
        $this->webhookLog = [];
    }
    
    /**
     * Get webhook summary
     */
    public function getWebhookSummary() {
        return [
            'webhook_enabled' => $this->config['webhook_enabled'],
            'webhook_url' => $this->config['webhook_url'],
            'auto_update' => $this->config['auto_update'],
            'log' => $this->webhookLog
        ];
    }
}

// Usage example:
// $webhook = new DocumentationUpdateWebhook();
// 
// Set webhook configuration:
// $webhook->setWebhookConfig([
//     'webhook_enabled' => true,
//     'webhook_url' => 'https://your-server.com/webhook/documentation',
//     'webhook_config' => 'your-secret-key',
//     'auto_update' => true
// ]);
// 
// Handle webhook request:
// $payload = json_decode(file_get_contents('php://input'), true);
// $result = $webhook->handleWebhook($payload);
// 
// Get results:
// echo json_encode($result);
?>
