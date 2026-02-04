// �️ Avoid Next Error - Real-time Error Prevention System

class AvoidNextError {
    constructor() {
        this.errors = [];
        this.metrics = {
            total: 0,
            byType: {},
            byFile: {},
            recent: [],
            critical: 0
        };
        this.config = {
            maxErrors: 50,
            enableAutoRecovery: true,
            enableLogging: true,
            enableReporting: true
        };
        this.init();
    }

    init() {
        // Prevent connection errors by wrapping initialization in try-catch
        try {
            this.setupGlobalHandlers();
            this.setupFormValidation();
            this.setupPerformanceMonitoring();
            this.startMaintenanceScheduler();
            this.loadStoredErrors();
            
            // Validate label for attributes on load with delay
            setTimeout(() => {
                try {
                    this.validateLabelForAttributes();
                } catch (e) {
                    // Silent fail to prevent connection errors
                }
            }, 1000);
        } catch (e) {
            // Silent initialization error handling
            this.handleError({
                type: 'initialization',
                message: 'Error prevention system initialization failed',
                error: e.message,
                timestamp: new Date().toISOString()
            });
        }
    }

    setupGlobalHandlers() {
        // Global error handler with connection error prevention
        window.addEventListener('error', (event) => {
            // Prevent connection errors from being logged
            if (event.message && event.message.includes('Could not establish connection')) {
                return; // Silent ignore connection errors
            }
            
            const error = {
                type: 'javascript',
                message: event.message,
                filename: event.filename,
                lineno: event.lineno,
                colno: event.colno,
                stack: event.error && event.error.stack,
                timestamp: new Date().toISOString(),
                userAgent: navigator.userAgent,
                url: window.location.href
            };
            
            this.handleError(error);
        });

        // Promise rejection handler with connection error prevention
        window.addEventListener('unhandledrejection', (event) => {
            // Prevent connection errors from being logged
            if (event.reason && event.reason.message && event.reason.message.includes('Could not establish connection')) {
                event.preventDefault(); // Silent ignore connection errors
                return;
            }
            
            const error = {
                type: 'promise',
                message: event.reason && event.reason.message || 'Unhandled Promise Rejection',
                stack: event.reason && event.reason.stack,
                timestamp: new Date().toISOString(),
                url: window.location.href
            };
            
            this.handleError(error);
            event.preventDefault();
        });

        // Network status handlers with silent operation
        window.addEventListener('online', () => {
            // Silent online status - no logging
        });

        window.addEventListener('offline', () => {
            // Silent offline status - no logging
        });
    }

    setupFormValidation() {
        // Override FormHelper with error detection
        if (window.FormHelper) {
            const originalValidateForm = window.FormHelper.validateForm;
            
            window.FormHelper.validateForm = (formId, fieldRules) => {
                try {
                    const validation = originalValidateForm.call(window.FormHelper, formId, fieldRules);
                    this.detectFormErrors(formId, validation, fieldRules);
                    this.validateLabelForAttributes(formId);
                    return validation;
                } catch (error) {
                    this.handleError({
                        type: 'form_validation',
                        formId: formId,
                        message: error.message,
                        stack: error.stack,
                        timestamp: new Date().toISOString()
                    });
                    return { isValid: false, errors: { general: 'Validation system error' } };
                }
            };
        }
    }

    validateLabelForAttributes(formId = null) {
        const scope = formId ? document.getElementById(formId) : document;
        const labels = scope.querySelectorAll('label[for]');
        const issues = [];
        
        labels.forEach(label => {
            const forId = label.getAttribute('for');
            const element = document.getElementById(forId);
            
            if (!element) {
                issues.push({
                    type: 'label_for_missing_element',
                    formId: formId || 'global',
                    label: label.textContent.trim(),
                    forId: forId,
                    timestamp: new Date().toISOString()
                });
            } else if (element.type === 'hidden' && element.id !== forId) {
                // Check if there's a visible element with similar ID
                const visibleElement = scope.querySelector(`[id*="${forId}"]:not([type="hidden"])`);
                
                if (visibleElement) {
                    issues.push({
                        type: 'label_for_hidden_element',
                        formId: formId || 'global',
                        label: label.textContent.trim(),
                        forId: forId,
                        actualElementId: element.id,
                        suggestedId: visibleElement.id,
                        timestamp: new Date().toISOString()
                    });
                }
            }
        });
        
        if (issues.length > 0) {
            issues.forEach(issue => {
                this.handleError({
                    type: 'accessibility',
                    message: `Label for "${issue.label}" points to "${issue.forId}" but element not found or hidden`,
                    details: issue,
                    timestamp: new Date().toISOString()
                });
            });
            
            // Show user-friendly alert
            this.showLabelForWarning(issues);
        }
        
        return issues;
    }

    showLabelForWarning(issues) {
        if (issues.length === 0) return;
        
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-warning alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
        alertDiv.style.zIndex = '9999';
        alertDiv.style.maxWidth = '600px';
        alertDiv.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <div>
                    <strong>⚠️ Accessibility Warning!</strong> 
                    ${issues.length} label(s) have incorrect 'for' attributes. 
                    This may affect form autofill and screen reader accessibility.
                    <div class="small mt-1">
                        <strong>Issues:</strong>
                        <ul class="mb-0">
                            ${issues.map(issue => `<li>Label "${issue.label}" → Element "${issue.forId}" (${issue.type})</li>`).join('')}
                        </ul>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="this.parentElement.parentElement.parentElement.remove()">Dismiss</button>
                </div>
            </div>
        `;
        
        document.body.appendChild(alertDiv);
        
        // Auto-remove after 15 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.parentNode.removeChild(alertDiv);
            }
        }, 15000);
    }

    setupPerformanceMonitoring() {
        // Monitor slow operations without console logging
        const originalFetch = window.fetch;
        
        window.fetch = async (...args) => {
            const start = performance.now();
            const url = args[0];
            
            try {
                const response = await originalFetch(...args);
                const duration = performance.now() - start;
                
                if (duration > 3000) {
                    // Silent slow API warning - no console logging
                    this.handleError({
                        type: 'performance',
                        message: `Slow API call: ${url} took ${duration.toFixed(2)}ms`,
                        url: url,
                        duration: duration,
                        timestamp: new Date().toISOString()
                    });
                }
                
                return response;
            } catch (error) {
                const duration = performance.now() - start;
                this.handleError({
                    type: 'network',
                    url: url,
                    message: error.message,
                    duration: duration,
                    timestamp: new Date().toISOString()
                });
                throw error;
            }
        };
    }

    detectFormErrors(formId, validation, fieldRules) {
        const form = document.getElementById(formId);
        
        // Check for missing elements
        Object.keys(fieldRules).forEach(fieldName => {
            const rule = fieldRules[fieldName];
            const element = document.getElementById(rule.elementId);
            
            if (!element) {
                this.handleError({
                    type: 'missing_element',
                    formId: formId,
                    fieldName: fieldName,
                    elementId: rule.elementId,
                    timestamp: new Date().toISOString()
                });
            }
        });

        // Check for validation state consistency
        if (form) {
            const invalidElements = form.querySelectorAll('.is-invalid');
            const errorMessages = form.querySelectorAll('.invalid-feedback, .error-message');
            
            if (invalidElements.length > 0 && errorMessages.length === 0) {
                this.handleError({
                    type: 'validation_state_mismatch',
                    formId: formId,
                    invalidCount: invalidElements.length,
                    errorMessageCount: errorMessages.length,
                    timestamp: new Date().toISOString()
                });
            }
        }
    }

    handleError(error) {
        // Store error
        this.errors.push(error);
        this.updateMetrics();
        
        // Auto-recovery attempts
        if (this.config.enableAutoRecovery) {
            this.attemptRecovery(error);
        }
        
        // Store in localStorage
        this.storeErrors();
        
        // Log to console (development only)
        if (this.config.enableLogging) {
            console.error('HIdari RE Error:', error);
        }
    }

    attemptRecovery(error) {
        const recoveryStrategies = {
            'Cannot access': () => {
                this.logWarning('Initialization error detected, reloading page...');
                setTimeout(() => window.location.reload(), 1000);
            },
            'SyntaxError': () => {
                this.logError('Critical syntax error - page reload required');
                setTimeout(() => window.location.reload(), 2000);
            },
            'NetworkError': () => {
                this.showNetworkErrorAlert();
            },
            'fetch': () => {
                this.showNetworkErrorAlert();
            },
            '400': () => {
                this.showAPIErrorAlert();
            }
        };

        // Find matching recovery strategy
        for (const [pattern, strategy] of Object.entries(recoveryStrategies)) {
            if (error.message.includes(pattern)) {
                strategy();
                return;
            }
        }
    }

    updateMetrics() {
        this.metrics.total = this.errors.length;
        
        // Group by type
        this.errors.forEach(error => {
            const type = this.categorizeError(error);
            this.metrics.byType[type] = (this.metrics.byType[type] || 0) + 1;
            this.metrics.byFile[error.filename || 'unknown'] = (this.metrics.byFile[error.filename || 'unknown'] || 0) + 1;
        });
        
        // Get recent errors (last 24 hours)
        const yesterday = new Date(Date.now() - 24 * 60 * 60 * 1000);
        this.metrics.recent = this.errors.filter(error => 
            new Date(error.timestamp) > yesterday
        );
        
        // Count critical errors
        this.metrics.critical = (this.metrics.byType['syntax'] || 0) + 
                              (this.metrics.byType['reference'] || 0) + 
                              (this.metrics.byType['missing_element'] || 0);
    }

    categorizeError(error) {
        if (error.message.includes('SyntaxError')) return 'syntax';
        if (error.message.includes('ReferenceError')) return 'reference';
        if (error.message.includes('TypeError')) return 'type';
        if (error.message.includes('NetworkError') || error.type === 'network') return 'network';
        if (error.type === 'form_validation') return 'validation';
        if (error.type === 'missing_element') return 'missing_element';
        if (error.type === 'validation_state_mismatch') return 'ui_inconsistency';
        return 'other';
    }

    storeErrors() {
        try {
            // Keep only recent errors
            const recentErrors = this.errors.slice(-this.config.maxErrors);
            localStorage.setItem('hidari_re_errors', JSON.stringify(recentErrors));
        } catch (e) {
            console.warn('Could not store errors:', e);
        }
    }

    loadStoredErrors() {
        try {
            const stored = localStorage.getItem('hidari_re_errors');
            if (stored) {
                this.errors = JSON.parse(stored);
                this.updateMetrics();
            }
        } catch (e) {
            console.warn('Could not load stored errors:', e);
        }
    }

    startMaintenanceScheduler() {
        // Clear old errors daily
        setInterval(() => {
            this.clearOldErrors();
        }, 24 * 60 * 60 * 1000);

        // Health check hourly
        setInterval(() => {
            this.performHealthCheck();
        }, 60 * 60 * 1000);
    }

    clearOldErrors() {
        const oneWeekAgo = new Date(Date.now() - 7 * 24 * 60 * 60 * 1000);
        this.errors = this.errors.filter(error => 
            new Date(error.timestamp) > oneWeekAgo
        );
        this.storeErrors();
        this.updateMetrics();
    }

    async performHealthCheck() {
        const health = {
            errors: this.metrics.total,
            critical: this.metrics.critical,
            recent: this.metrics.recent.length,
            storage: this.checkStorage(),
            performance: this.checkPerformance()
        };

        if (health.critical > 0) {
            this.logWarning(`Health check failed: ${health.critical} critical errors`);
        }
    }

    checkStorage() {
        try {
            localStorage.setItem('health_check', 'test');
            localStorage.removeItem('health_check');
            return 'ok';
        } catch (e) {
            return 'error';
        }
    }

    checkPerformance() {
        const metrics = performance.getEntriesByType('navigation');
        if (metrics.length > 0) {
            const loadTime = metrics[0].loadEventEnd - metrics[0].loadEventStart;
            return loadTime < 3000 ? 'good' : 'slow';
        }
        return 'unknown';
    }

    // UI Helper Methods
    showNetworkErrorAlert() {
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-warning alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
        alertDiv.style.zIndex = '9999';
        alertDiv.innerHTML = `
            <strong>Koneksi Bermasalah!</strong> Tidak dapat terhubung ke server. Silakan periksa koneksi internet Anda.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alertDiv);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.parentNode.removeChild(alertDiv);
            }
        }, 5000);
    }

    showAPIErrorAlert() {
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
        alertDiv.style.zIndex = '9999';
        alertDiv.innerHTML = `
            <strong>Error Server!</strong> Terjadi masalah dengan server. Silakan coba lagi nanti.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alertDiv);
        
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.parentNode.removeChild(alertDiv);
            }
        }, 5000);
    }

    // Logging Methods
    logError(message) {
        // Silent error logging - no console output to prevent connection errors
        this.handleError({
            type: 'system',
            message: message,
            timestamp: new Date().toISOString()
        });
    }

    logWarning(message) {
        // Silent warning logging - no console output to prevent connection errors
        this.handleError({
            type: 'warning',
            message: message,
            timestamp: new Date().toISOString()
        });
    }

    logInfo(message) {
        // Silent info logging - no console output to prevent connection errors
        this.handleError({
            type: 'info',
            message: message,
            timestamp: new Date().toISOString()
        });
    }

    // Public API Methods
    getErrorSummary() {
        const summary = {
            total: this.metrics.total,
            critical: this.metrics.critical,
            recent: this.metrics.recent.length,
            byType: this.metrics.byType,
            byFile: this.metrics.byFile,
            recommendations: this.generateRecommendations(),
            accessibility: {
                labelForIssues: this.metrics.byType['accessibility'] || 0,
                lastValidation: this.getLastLabelValidation()
            }
        };
        
        return summary;
    }

    getLastLabelValidation() {
        const errors = this.errors.filter(error => error.type.startsWith('label_for'));
        if (errors.length === 0) return null;
        
        const latest = errors[errors.length - 1];
        return {
            timestamp: latest.timestamp,
            count: errors.length,
            lastIssue: latest.message
        };
    }

    generateRecommendations() {
        const recommendations = [];
        
        if (this.metrics.byType['syntax'] > 0) {
            recommendations.push({
                priority: 'high',
                type: 'syntax',
                message: `${this.metrics.byType['syntax']} syntax error(s) detected`,
                action: 'Review code and run linter immediately'
            });
        }
        
        if (this.metrics.byType['reference'] > 0) {
            recommendations.push({
                priority: 'high',
                type: 'reference',
                message: `${this.metrics.byType['reference']} reference error(s) detected`,
                action: 'Check variable declarations and initialization order'
            });
        }
        
        if (this.metrics.byType['network'] > 5) {
            recommendations.push({
                priority: 'medium',
                type: 'network',
                message: `${this.metrics.byType['network']} network error(s) detected`,
                action: 'Check API endpoints and network connectivity'
            });
        }
        
        if (this.metrics.byType['accessibility'] > 0) {
            recommendations.push({
                priority: 'medium',
                type: 'accessibility',
                message: `${this.metrics.byType['accessibility']} accessibility issue(s) detected`,
                action: 'Review label-element relationships and fix label for attributes'
            });
        }
        
        return recommendations;
    }

    clearErrors() {
        this.errors = [];
        this.metrics = {
            total: 0,
            byType: {},
            byFile: {},
            recent: [],
            critical: 0
        };
        this.storeErrors();
        this.logInfo('All errors cleared');
    }

    // Export to global scope
    static getInstance() {
        if (!window.avoidNextError) {
            window.avoidNextError = new AvoidNextError();
        }
        return window.avoidNextError;
    }
}

// Auto-initialize with error prevention
document.addEventListener('DOMContentLoaded', () => {
    try {
        window.avoidNextError = AvoidNextError.getInstance();
    } catch (e) {
        // Silent initialization failure - prevent connection errors
        // No console logging to prevent connection errors
    }
});

// Additional prevention for browser extension connection errors
window.addEventListener('error', (event) => {
    // Prevent all connection errors from browser extensions
    if (event.message && (
        event.message.includes('Could not establish connection') ||
        event.message.includes('Receiving end does not exist') ||
        event.message.includes('Extension context invalidated') ||
        event.message.includes('chrome.runtime') ||
        event.message.includes('browser.runtime')
    )) {
        event.preventDefault();
        event.stopPropagation();
        return false;
    }
});

// Prevent unhandled promise rejections from connection errors
window.addEventListener('unhandledrejection', (event) => {
    if (event.reason && (
        (event.reason.message && event.reason.message.includes('Could not establish connection')) ||
        (event.reason.message && event.reason.message.includes('Receiving end does not exist')) ||
        (event.reason.message && event.reason.message.includes('Extension context invalidated')) ||
        (event.reason.message && event.reason.message.includes('chrome.runtime')) ||
        (event.reason.message && event.reason.message.includes('browser.runtime'))
    )) {
        event.preventDefault();
        event.stopPropagation();
        return false;
    }
});

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = AvoidNextError;
}
