#!/bin/bash

# KSP Documentation Update Cron Job
# This script automatically updates the user guide documentation
# whenever changes are detected in the application

# Set the correct path to your KSP installation
KSP_PATH="/var/www/html/ksp_peb"
PHP_BIN="/usr/bin/php"

# Log file for documentation updates
LOG_FILE="$KSP_PATH/logs/documentation_update.log"

# Create log directory if it doesn't exist
mkdir -p "$KSP_PATH/logs"

# Function to log messages
log_message() {
    echo "[$timestamp] $1" >> "$LOG_FILE"
}

# Function to update documentation
update_documentation() {
    log_message "Starting documentation update"
    
    cd "$KSP_PATH"
    
    # Run the auto documentation updater
    $PHP_BIN -r "
        require_once 'src/lib/AutoDocumentationUpdater.php';
        \$updater = new AutoDocumentationUpdater();
        \$result = \$updater->autoUpdate();
        
        if (\$result['success']) {
            log_message "Documentation updated successfully to version {\$result['version']}"
        } else {
            log_message "Failed to update documentation: {\$result['message']}"
        }
        
        log_message "Documentation update completed"
}

# Run the update
update_documentation

# Log completion
log_message "Documentation update cron job completed at $(date)"
