/**
 * Form Helper - Utility functions for form validation and handling
 */

// Clear all error states from form
function clearFormErrors(formId) {
    const form = document.getElementById(formId);
    if (form) {
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    }
}

// Validate form fields with comprehensive error checking
function validateForm(formId, fieldRules) {
    const form = document.getElementById(formId);
    if (!form) return { isValid: false, errors: [] };

    clearFormErrors(formId);
    
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);
    const errors = [];

    // Check each field rule
    for (const [fieldName, rules] of Object.entries(fieldRules)) {
        const value = data[fieldName];
        const fieldElement = document.getElementById(rules.elementId || fieldName);
        
        // Required validation
        if (rules.required && (!value || (typeof value === 'string' && value.trim() === ''))) {
            errors.push({ 
                field: fieldName, 
                label: rules.label,
                elementId: rules.elementId || fieldName,
                message: `${rules.label} wajib diisi`
            });
            continue;
        }

        // Skip other validations if field is empty and not required
        if (!value || (typeof value === 'string' && value.trim() === '')) {
            continue;
        }

        // Email validation
        if (rules.type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
            errors.push({ 
                field: fieldName, 
                label: rules.label,
                elementId: rules.elementId || fieldName,
                message: `${rules.label} format tidak valid`
            });
        }

        // Phone validation (Indonesia format - with or without dashes)
        if (rules.type === 'phone' && !/^08[0-9-]{9,14}$/.test(value)) {
            errors.push({ 
                field: fieldName, 
                label: rules.label,
                elementId: rules.elementId || fieldName,
                message: `${rules.label} format tidak valid (contoh: 08123456789 atau 0812-3456-7890)`
            });
        }

        // Min length validation
        if (rules.minLength && value.length < rules.minLength) {
            errors.push({ 
                field: fieldName, 
                label: rules.label,
                elementId: rules.elementId || fieldName,
                message: `${rules.label} minimal ${rules.minLength} karakter`
            });
        }

        // Max length validation
        if (rules.maxLength && value.length > rules.maxLength) {
            errors.push({ 
                field: fieldName, 
                label: rules.label,
                elementId: rules.elementId || fieldName,
                message: `${rules.label} maksimal ${rules.maxLength} karakter`
            });
        }

        // Pattern validation
        if (rules.pattern && !new RegExp(rules.pattern).test(value)) {
            errors.push({ 
                field: fieldName, 
                label: rules.label,
                elementId: rules.elementId || fieldName,
                message: `${rules.label} format tidak valid`
            });
        }

        // Custom validation function
        if (rules.validate && typeof rules.validate === 'function') {
            const customResult = rules.validate(value);
            if (customResult !== true) {
                errors.push({ 
                    field: fieldName, 
                    label: rules.label,
                    elementId: rules.elementId || fieldName,
                    message: customResult || `${rules.label} tidak valid`
                });
            }
        }
    }

    return {
        isValid: errors.length === 0,
        errors: errors,
        data: data
    };
}

// Show form errors with highlighting and focus
function showFormErrors(errors) {
    // Highlight error fields
    errors.forEach(error => {
        const element = document.getElementById(error.elementId);
        if (element) {
            element.classList.add('is-invalid');
        }
    });

    // Focus first error field
    if (errors.length > 0) {
        const firstElement = document.getElementById(errors[0].elementId);
        if (firstElement) {
            firstElement.focus();
        }
    }

    // Return error message
    const errorLabels = errors.map(e => e.label).join(', ');
    return `Field berikut wajib diisi: ${errorLabels}`;
}

// Setup tab order for form fields
function setupTabOrder(fieldIds) {
    fieldIds.forEach((fieldId, index) => {
        const element = document.getElementById(fieldId);
        if (element) {
            element.setAttribute('tabindex', index + 1);
        }
    });
}

// Auto-focus to next field on select change
function setupAutoFocus(triggerId, targetId) {
    const triggerElement = document.getElementById(triggerId);
    const targetElement = document.getElementById(targetId);
    
    if (triggerElement && targetElement) {
        triggerElement.addEventListener('change', () => {
            if (triggerElement.value) {
                targetElement.focus();
            }
        });
    }
}

// Auto-uppercase field on blur
function setupAutoUppercase(fieldId) {
    const element = document.getElementById(fieldId);
    if (element) {
        element.addEventListener('blur', () => {
            if (element.value) {
                element.value = element.value.toUpperCase();
            }
        });
    }
}

// Setup focus dropdown behavior for select elements
function setupFocusDropdown(selectId) {
    const selectElement = document.getElementById(selectId);
    if (!selectElement) return;
    
    // Add focus event to show dropdown when data is loaded
    selectElement.addEventListener('focus', function() {
        // Only show dropdown if there are options beyond the placeholder
        if (this.options.length > 1) {
            this.size = this.options.length > 10 ? 10 : this.options.length;
            this.setAttribute('size', this.size);
        }
    });
    
    // Add blur event to restore single line
    selectElement.addEventListener('blur', function() {
        this.removeAttribute('size');
        this.size = 1;
    });
    
    // Add change event to restore single line after selection
    selectElement.addEventListener('change', function() {
        this.removeAttribute('size');
        this.size = 1;
    });
}

// Convert string to Title Case
function toTitleCase(str) {
    return str.replace(/\w\S*/g, function(txt){
        return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
    });
}

// Auto-titlecase field on blur
function setupAutoTitleCase(fieldId) {
    const element = document.getElementById(fieldId);
    if (element && typeof toTitleCase === 'function') {
        element.addEventListener('blur', () => {
            if (element.value) {
                element.value = toTitleCase(element.value);
            }
        });
    }
}

// Copy field value on blur if target is empty
function setupFieldCopy(sourceId, targetId, validationFn = null) {
    const sourceElement = document.getElementById(sourceId);
    const targetElement = document.getElementById(targetId);
    
    if (sourceElement && targetElement) {
        sourceElement.addEventListener('blur', () => {
            if (!targetElement.value && sourceElement.value) {
                // Apply validation if provided
                if (validationFn) {
                    if (validationFn(sourceElement.value)) {
                        targetElement.value = sourceElement.value;
                    }
                } else {
                    targetElement.value = sourceElement.value;
                }
            }
        });
    }
}

// Dynamic label update
function setupDynamicLabel(triggerId, targetLabelId, prefix = '', suffix = '') {
    const triggerElement = document.getElementById(triggerId);
    const labelElement = document.querySelector(`label[for="${targetLabelId}"]`);
    
    if (triggerElement && labelElement) {
        const originalText = labelElement.textContent.replace(/\s*\*$/, '');
        
        triggerElement.addEventListener('change', () => {
            if (triggerElement.value) {
                const selectedText = triggerElement.options[triggerElement.selectedIndex].text;
                labelElement.textContent = `${prefix}${originalText}${selectedText}${suffix} *`;
            } else {
                labelElement.textContent = `${originalText} *`;
            }
        });
    }
}

// Phone number formatting (only numbers, max length, with dashes for display)
function setupPhoneFormatting(fieldId, maxLength = 14) {
    const element = document.getElementById(fieldId);
    if (element) {
        // Add keydown event for input validation
        element.addEventListener('keydown', (e) => {
            // Allow backspace, delete, tab, escape, enter, arrow keys, and dash
            if ([8, 9, 27, 13, 37, 38, 39, 40, 189].includes(e.keyCode)) {
                return;
            }
            
            // Allow numbers only (0-9) and numpad numbers (96-105)
            if ((e.keyCode < 48 || e.keyCode > 57) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });
        
        element.addEventListener('input', () => {
            // Remove non-digits and limit length
            let value = element.value.replace(/[^0-9]/g, '').slice(0, maxLength);
            
            // Format as 0857-1122-3344
            if (value.length >= 4) {
                if (value.length <= 8) {
                    // Format: 0857-1122
                    value = value.slice(0, 4) + '-' + value.slice(4);
                } else {
                    // Format: 0857-1122-3344
                    value = value.slice(0, 4) + '-' + value.slice(4, 8) + '-' + value.slice(8, 12);
                }
            }
            
            element.value = value;
        });

        // Format on blur to ensure proper format
        element.addEventListener('blur', () => {
            let value = element.value.replace(/[^0-9]/g, '').slice(0, maxLength);
            
            // Format as 0857-1122-3344
            if (value.length >= 4) {
                if (value.length <= 8) {
                    // Format: 0857-1122
                    value = value.slice(0, 4) + '-' + value.slice(4);
                } else {
                    // Format: 0857-1122-3344
                    value = value.slice(0, 4) + '-' + value.slice(4, 8) + '-' + value.slice(8, 12);
                }
            }
            
            element.value = value;
        });
    }
}

// Function to remove dashes from phone number (for database storage)
function cleanPhoneNumber(phoneNumber) {
    return phoneNumber ? phoneNumber.replace(/-/g, '') : '';
}

// Function to format phone number for display
function formatPhoneNumber(phoneNumber) {
    if (!phoneNumber) return '';
    
    let value = phoneNumber.replace(/[^0-9]/g, '').slice(0, 14);
    
    // Format as 0857-1122-3344
    if (value.length >= 4) {
        if (value.length <= 8) {
            // Format: 0857-1122
            value = value.slice(0, 4) + '-' + value.slice(4);
        } else {
            // Format: 0857-1122-3344
            value = value.slice(0, 4) + '-' + value.slice(4, 8) + '-' + value.slice(8, 12);
        }
    }
    
    return value;
}

// NPWP 16 digit formatting (PMK No. 112/2022 standard)
function setupNPWPFormatting(fieldId) {
    const element = document.getElementById(fieldId);
    if (element) {
        // Add hidden field for clean value
        const hiddenField = document.createElement('input');
        hiddenField.type = 'hidden';
        hiddenField.id = fieldId + '_clean';
        hiddenField.name = fieldId + '_clean';
        element.parentNode.appendChild(hiddenField);
        
        // Add keydown event for input validation
        element.addEventListener('keydown', (e) => {
            // Allow backspace, delete, tab, escape, enter, arrow keys, and dash
            if ([8, 9, 27, 13, 37, 38, 39, 40, 189].includes(e.keyCode)) {
                return;
            }
            
            // Allow numbers only (0-9) and numpad numbers (96-105)
            if ((e.keyCode < 48 || e.keyCode > 57) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });
        
        element.addEventListener('input', () => {
            // Remove all non-digits and limit to 16 digits
            let digits = element.value.replace(/[^0-9]/g, '').slice(0, 16);
            
            // Apply masking: XXXX-XXXX-XXXX-XXXX
            let maskedValue = '';
            if (digits.length > 0) {
                maskedValue += digits.slice(0, 4);
                if (digits.length > 4) {
                    maskedValue += '-' + digits.slice(4, 8);
                    if (digits.length > 8) {
                        maskedValue += '-' + digits.slice(8, 12);
                        if (digits.length > 12) {
                            maskedValue += '-' + digits.slice(12, 16);
                        }
                    }
                }
            }
            
            element.value = maskedValue;
            hiddenField.value = digits; // Store clean value in hidden field
        });
        
        element.addEventListener('blur', () => {
            // Ensure masked format on blur
            let digits = element.value.replace(/[^0-9]/g, '').slice(0, 16);
            
            // Apply masking: XXXX-XXXX-XXXX-XXXX
            let maskedValue = '';
            if (digits.length > 0) {
                maskedValue += digits.slice(0, 4);
                if (digits.length > 4) {
                    maskedValue += '-' + digits.slice(4, 8);
                    if (digits.length > 8) {
                        maskedValue += '-' + digits.slice(8, 12);
                        if (digits.length > 12) {
                            maskedValue += '-' + digits.slice(12, 16);
                        }
                    }
                }
            }
            
            element.value = maskedValue;
            hiddenField.value = digits; // Store clean value in hidden field
        });
    }
}

// Function to clean NPWP (remove all non-digits)
function cleanNPWP(npwp) {
    return npwp ? npwp.replace(/[^0-9]/g, '') : '';
}

// Function to format NPWP for display (16 digit standard)
function formatNPWP(npwp) {
    if (!npwp) return '';
    
    let value = npwp.replace(/[^0-9]/g, '').slice(0, 16);
    
    // Format 16 digit as XXXXXXXXXXXXXXXXXX
    if (value.length === 16) {
        return value;
    } else if (value.length === 15) {
        // Legacy 15 digit format: XX.XXX.XXX.X-XXX.XXX
        return value.slice(0, 2) + '.' + 
               value.slice(2, 5) + '.' + 
               value.slice(5, 9) + '.' + 
               value.slice(9, 12) + '-' + 
               value.slice(12, 15) + '.' + 
               value.slice(15);
    }
    
    return value;
}

// Function to setup currency formatting (Rupiah)
function setupCurrencyFormatting(fieldId) {
    const element = document.getElementById(fieldId);
    if (element) {
        element.addEventListener('input', () => {
            // Remove all non-digits and convert to number
            let value = element.value.replace(/[^0-9]/g, '');
            
            // Convert to number and format as Rupiah
            if (value) {
                const numValue = parseInt(value);
                // Format with thousand separators
                value = numValue.toLocaleString('id-ID');
            } else {
                value = '';
            }
            
            element.value = value;
        });
        
        element.addEventListener('blur', () => {
            // Ensure proper Rupiah format on blur
            let value = element.value.replace(/[^0-9]/g, '');
            
            if (value) {
                const numValue = parseInt(value);
                // Format as Rupiah with currency symbol
                value = 'Rp ' + numValue.toLocaleString('id-ID');
            } else {
                value = '';
            }
            
            element.value = value;
        });
        
        element.addEventListener('focus', () => {
            // Remove currency symbol on focus for easy editing
            let value = element.value.replace(/[^0-9]/g, '');
            if (value) {
                value = parseInt(value).toLocaleString('id-ID');
            }
            element.value = value;
        });
    }
}

// Function to clean currency value (remove formatting for database)
function cleanCurrency(currency) {
    return currency ? currency.replace(/[^0-9]/g, '') : '0';
}

// Function to format currency for display
function formatCurrency(amount) {
    if (!amount) return 'Rp 0';
    const numValue = parseInt(amount);
    return 'Rp ' + numValue.toLocaleString('id-ID');
}

// Function to setup jenis koperasi dynamic behavior
function setupJenisKoperasiDynamic(jenisSelectId, namaInputId, legalSectionId) {
    const jenisSelect = document.getElementById(jenisSelectId);
    const namaInput = document.getElementById(namaInputId);
    const namaLabel = document.querySelector(`label[for="${namaInputId}"]`);
    const legalSection = legalSectionId ? document.getElementById(legalSectionId) : null;
    
    if (!jenisSelect || !namaInput || !namaLabel) {
        console.warn('setupJenisKoperasiDynamic: Required elements not found');
        return;
    }
    
    // Function to update nama koperasi based on jenis koperasi
    function updateNamaKoperasi() {
        const selectedOption = jenisSelect.options[jenisSelect.selectedIndex];
        const jenisCode = selectedOption.value;
        const jenisName = selectedOption.textContent;
        const jenisCategory = selectedOption.dataset.category || '';
        
        // Update label with selected jenis and category
        if (jenisCode) {
            namaLabel.innerHTML = `Nama Koperasi <small class="text-muted">(${jenisName})</small> *`;
            
            // Reset nama_koperasi input
            namaInput.value = '';
            
            // Set input value to jenis code with space (KSP, KK, KP, etc.)
            namaInput.value = jenisCode + ' ';
            
            // Focus on nama koperasi input
            namaInput.focus();
            
            // Position cursor at the end of the text
            namaInput.setSelectionRange(namaInput.value.length, namaInput.value.length);
            
            // Update legal info section (if exists)
            if (legalSection) {
                updateLegalInfo();
            }
            
            console.log('Jenis Koperasi Selected:', {
                code: jenisCode,
                name: jenisName,
                category: jenisCategory,
                inputPrefix: jenisCode + ' ',
                labelWithCategory: `Nama Koperasi (${jenisName}) *`
            });
        } else {
            namaLabel.innerHTML = 'Nama Koperasi *';
            namaInput.value = '';
            if (legalSection) {
                updateLegalInfo();
            }
        }
    }
    
    // Function to update legal info section (if exists)
    function updateLegalInfo() {
        if (!legalSection) return;
        
        const namaKoperasi = namaInput.value;
        
        if (legalSection && namaKoperasi) {
            legalSection.innerHTML = `Informasi Legal <small class="text-muted">(${namaKoperasi})</small>`;
        } else if (legalSection) {
            legalSection.innerHTML = 'Informasi Legal';
        }
    }
    
    // Event listeners
    jenisSelect.addEventListener('change', updateNamaKoperasi);
    if (legalSection) {
        namaInput.addEventListener('input', updateLegalInfo);
        namaInput.addEventListener('blur', updateLegalInfo);
    }
    
    // Return functions for external access
    return {
        updateNamaKoperasi,
        updateLegalInfo
    };
}

// Username formatting (lowercase, alphanumeric, underscore, dot)
function setupUsernameFormatting(fieldId, maxLength = 20) {
    const element = document.getElementById(fieldId);
    if (element) {
        element.addEventListener('input', () => {
            element.value = element.value.toLowerCase()
                .replace(/[^a-z0-9_.]/g, '')
                .slice(0, maxLength);
        });
    }
}

// Show alert message
function showAlert(type, message, containerId = 'alert-container') {
    const alertContainer = document.getElementById(containerId);
    if (!alertContainer) return;

    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
    alertContainer.appendChild(alertDiv);

    // Auto dismiss after 5 seconds
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}

// Setup ENTER key navigation for form fields
function setupEnterKeyNavigation(formId, submitButtonId = null) {
    const form = document.getElementById(formId);
    if (!form) return;

    // Get all focusable elements in tab order
    const focusableElements = form.querySelectorAll(
        'input:not([disabled]):not([readonly]), ' +
        'select:not([disabled]):not([readonly]), ' +
        'textarea:not([disabled]):not([readonly]), ' +
        'button:not([disabled]), ' +
        '[tabindex]:not([disabled]):not([readonly])'
    );

    // Sort by tabindex, but also consider elements without explicit tabindex
    const sortedElements = Array.from(focusableElements).sort((a, b) => {
        const aIndex = parseInt(a.getAttribute('tabindex')) || 0;
        const bIndex = parseInt(b.getAttribute('tabindex')) || 0;
        
        // If both have tabindex, sort by tabindex
        if (aIndex > 0 && bIndex > 0) {
            return aIndex - bIndex;
        }
        
        // If only one has tabindex, prioritize the one with tabindex
        if (aIndex > 0) return -1;
        if (bIndex > 0) return 1;
        
        // If neither has tabindex, maintain DOM order
        return 0;
    });

    form.addEventListener('keydown', function(e) {
        // Handle ENTER key
        if (e.key === 'Enter' && !e.shiftKey) {
            const activeElement = document.activeElement;
            
            // If focus is on submit button, allow normal submit
            if (submitButtonId && activeElement.id === submitButtonId) {
                return; // Allow normal submit behavior
            }

            // If focus is on textarea, allow normal behavior (new line)
            if (activeElement.tagName === 'TEXTAREA') {
                return; // Allow normal textarea behavior
            }

            // Prevent default form submission
            e.preventDefault();

            // Find current element index
            const currentIndex = sortedElements.indexOf(activeElement);
            
            if (currentIndex !== -1) {
                // Move to next element
                let nextIndex = currentIndex + 1;
                
                // Skip disabled or readonly elements
                while (nextIndex < sortedElements.length) {
                    const nextElement = sortedElements[nextIndex];
                    if (!nextElement.disabled && !nextElement.readOnly) {
                        nextElement.focus();
                        
                        // If it's a select element, open the dropdown
                        if (nextElement.tagName === 'SELECT') {
                            // Trigger click to open dropdown
                            setTimeout(() => nextElement.click(), 0);
                        }
                        
                        break;
                    }
                    nextIndex++;
                }
                
                // If we've reached the end, focus on submit button if exists
                if (nextIndex >= sortedElements.length && submitButtonId) {
                    const submitButton = document.getElementById(submitButtonId);
                    if (submitButton && !submitButton.disabled) {
                        submitButton.focus();
                    }
                }
            }
        }
    });
}

// Enhanced form validation with ENTER key navigation
function setupFormWithEnterNavigation(formId, fieldRules, submitButtonId = null) {
    // Setup ENTER key navigation
    setupEnterKeyNavigation(formId, submitButtonId);
    
    // Return validation function for use in form submit
    return function() {
        return validateForm(formId, fieldRules);
    };
}

// Reset all fields in a form except specified exceptions
function resetFormFields(formId, exceptions = []) {
    const form = document.getElementById(formId);
    if (!form) return;
    
    // Get all input, select, and textarea elements
    const fields = form.querySelectorAll('input, select, textarea');
    
    fields.forEach(field => {
        // Skip if field is in exceptions array
        if (exceptions.includes(field.id)) {
            return;
        }
        
        // Reset based on field type
        if (field.type === 'checkbox' || field.type === 'radio') {
            field.checked = false;
        } else if (field.type === 'file') {
            field.value = '';
        } else {
            field.value = '';
        }
        
        // Remove validation states
        field.classList.remove('is-invalid', 'is-valid');
    });
}

// Reset all fields except location data (specialized function)
function resetAllFieldsExceptLocation(formId = 'cooperativeRegisterForm') {
    const locationFields = ['province', 'regency', 'district']; // Fields to keep
    resetFormFields(formId, locationFields);
}

// Export all functions
window.FormHelper = {
    clearFormErrors,
    validateForm,
    showFormErrors,
    setupTabOrder,
    setupAutoFocus,
    setupAutoUppercase,
    setupAutoTitleCase,
    setupFieldCopy,
    setupDynamicLabel,
    setupPhoneFormatting,
    setupUsernameFormatting,
    setupNPWPFormatting,
    setupCurrencyFormatting,
    setupJenisKoperasiDynamic,
    cleanPhoneNumber,
    formatPhoneNumber,
    cleanNPWP,
    formatNPWP,
    cleanCurrency,
    formatCurrency,
    toTitleCase,
    setupFocusDropdown,
    showAlert,
    setupEnterKeyNavigation,
    setupFormWithEnterNavigation,
    resetFormFields,
    resetAllFieldsExceptLocation
};

// Export functions globally for backward compatibility
window.toTitleCase = toTitleCase;
window.setupFocusDropdown = setupFocusDropdown;
