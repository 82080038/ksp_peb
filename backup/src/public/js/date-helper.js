// Reusable date input initializer
// Features:
// - Display input dd/mm/yyyy (manual typing allowed)
// - Auto-format digits: 01012022 -> 01/01/2022
// - Hidden ISO field (yyyy-mm-dd) kept in sync
// - Native date picker opened via trigger button
// Params: { displayId, hiddenId, pickerId, triggerId }
function initDateInput(config) {
  const displayId = config.displayId;
  const hiddenId = config.hiddenId;
  const pickerId = config.pickerId;
  const triggerId = config.triggerId;
  
  // Safety check for parameters
  if (!displayId || !hiddenId || !pickerId || !triggerId) {
    return;
  }
  
  const displayEl = document.getElementById(displayId);
  const hiddenEl = document.getElementById(hiddenId);
  const pickerEl = document.getElementById(pickerId);
  const triggerEl = document.getElementById(triggerId);
  
  if (!displayEl || !hiddenEl || !pickerEl || !triggerEl) {
    return;
  }

  // Open native picker only via trigger
  const openPicker = () => {
    // Method 1: Try showPicker() API (modern browsers)
    if (typeof pickerEl.showPicker === 'function') {
      try {
        pickerEl.showPicker();
      } catch (e) {
        fallbackPicker();
      }
    } else {
      fallbackPicker();
    }
  };
  
  const fallbackPicker = () => {
    // Method 2: Position picker near the display field and trigger it
    const displayRect = displayEl.getBoundingClientRect();
    
    // Position the picker near the display field
    pickerEl.style.position = 'fixed';
    pickerEl.style.top = (displayRect.bottom + window.scrollY) + 'px';
    pickerEl.style.left = (displayRect.left + window.scrollX) + 'px';
    pickerEl.style.opacity = '1';
    pickerEl.style.pointerEvents = 'auto';
    pickerEl.style.zIndex = '9999';
    pickerEl.style.width = displayEl.offsetWidth + 'px';
    
    // Focus and click
    pickerEl.focus();
    pickerEl.click();
    
    // Method 3: If still not working, create a temporary visible date input
    if (document.activeElement !== pickerEl) {
      const tempPicker = document.createElement('input');
      tempPicker.type = 'date';
      tempPicker.style.position = 'fixed';
      tempPicker.style.top = (displayRect.bottom + window.scrollY) + 'px';
      tempPicker.style.left = (displayRect.left + window.scrollX) + 'px';
      tempPicker.style.zIndex = '9999';
      tempPicker.style.width = displayEl.offsetWidth + 'px';
      tempPicker.style.border = '1px solid #ced4da';
      tempPicker.style.borderRadius = '0.375rem';
      tempPicker.style.padding = '0.375rem 0.75rem';
      tempPicker.style.fontSize = '1rem';
      document.body.appendChild(tempPicker);
      
      tempPicker.addEventListener('change', (e) => {
        pickerEl.value = e.target.value;
        pickerEl.dispatchEvent(new Event('change'));
        document.body.removeChild(tempPicker);
        // Hide the original picker
        pickerEl.style.opacity = '0';
        pickerEl.style.pointerEvents = 'none';
        pickerEl.style.zIndex = '-1';
      });
      
      tempPicker.addEventListener('blur', () => {
        setTimeout(() => {
          if (document.body.contains(tempPicker)) {
            document.body.removeChild(tempPicker);
          }
          pickerEl.style.opacity = '0';
          pickerEl.style.pointerEvents = 'none';
          pickerEl.style.zIndex = '-1';
        }, 200);
      });
      
      tempPicker.focus();
      tempPicker.click();
    }
  };
  
  triggerEl.addEventListener('click', openPicker);
  
  // Hide picker when clicking outside
  document.addEventListener('click', (e) => {
    if (!pickerEl.contains(e.target) && !triggerEl.contains(e.target) && !displayEl.contains(e.target)) {
      pickerEl.style.opacity = '0';
      pickerEl.style.pointerEvents = 'none';
      pickerEl.style.zIndex = '-1';
    }
  });
  
  // Hide picker on escape key
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      pickerEl.style.opacity = '0';
      pickerEl.style.pointerEvents = 'none';
      pickerEl.style.zIndex = '-1';
    }
  });

  const isoToDisplay = (isoDate) => {
    if (!isoDate) return '';
    const [y, m, d] = isoDate.split('-');
    if (!y || !m || !d) return '';
    return `${d}/${m}/${y}`;
  };

  const digitsToDisplay = (digits) => {
    if (!digits) return '';
    if (digits.length <= 2) return digits;
    if (digits.length <= 4) return `${digits.slice(0, 2)}/${digits.slice(2)}`;
    if (digits.length <= 8) return `${digits.slice(0, 2)}/${digits.slice(2, 4)}/${digits.slice(4, 8)}`;
    return `${digits.slice(0, 2)}/${digits.slice(2, 4)}/${digits.slice(4, 8)}`;
  };

  // Sync picker -> display + hidden
  pickerEl.addEventListener('change', (e) => {
    const value = e.target.value; // yyyy-mm-dd
    hiddenEl.value = value;
    displayEl.value = isoToDisplay(value);
    
    // Hide picker after selection
    setTimeout(() => {
      pickerEl.style.opacity = '0';
      pickerEl.style.pointerEvents = 'none';
      pickerEl.style.zIndex = '-1';
    }, 100);
  });

  // Add input validation for better UX - only numbers allowed
  displayEl.addEventListener('keydown', (e) => {
    // Allow backspace, delete, tab, escape, enter, arrow keys
    if ([8, 9, 27, 13, 37, 38, 39, 40].includes(e.keyCode)) {
      return;
    }
    
    // Allow numbers only (0-9) and numpad numbers (96-105)
    if ((e.keyCode < 48 || e.keyCode > 57) && (e.keyCode < 96 || e.keyCode > 105)) {
      e.preventDefault();
    }
  });

  // Add input event for real-time masking
  displayEl.addEventListener('input', (e) => {
    let value = e.target.value;
    
    // Remove all non-digit characters
    let digits = value.replace(/\D/g, '').slice(0, 8);
    
    // Apply mask: dd/mm/yyyy
    let maskedValue = '';
    if (digits.length > 0) {
      maskedValue += digits.slice(0, 2);
      if (digits.length > 2) {
        maskedValue += '/' + digits.slice(2, 4);
        if (digits.length > 4) {
          maskedValue += '/' + digits.slice(4, 8);
        }
      }
    }
    
    // Update display with masked value
    displayEl.value = maskedValue;
    
    // Update hidden field with ISO format (yyyy-mm-dd)
    let iso = '';
    if (digits.length === 8) {
      const day = digits.slice(0, 2);
      const month = digits.slice(2, 4);
      const year = digits.slice(4, 8);
      iso = `${year}-${month}-${day}`;
    }
    hiddenEl.value = iso;
    pickerEl.value = iso;
  });

  // Handle paste events with masking
  displayEl.addEventListener('paste', (e) => {
    e.preventDefault();
    const pastedText = (e.clipboardData || window.clipboardData).getData('text');
    let digits = pastedText.replace(/\D/g, '').slice(0, 8);
    
    // Apply mask: dd/mm/yyyy
    let maskedValue = '';
    if (digits.length > 0) {
      maskedValue += digits.slice(0, 2);
      if (digits.length > 2) {
        maskedValue += '/' + digits.slice(2, 4);
        if (digits.length > 4) {
          maskedValue += '/' + digits.slice(4, 8);
        }
      }
    }
    
    displayEl.value = maskedValue;
    
    // Update hidden field with ISO format (yyyy-mm-dd)
    let iso = '';
    if (digits.length === 8) {
      const day = digits.slice(0, 2);
      const month = digits.slice(2, 4);
      const year = digits.slice(4, 8);
      iso = `${year}-${month}-${day}`;
    }
    hiddenEl.value = iso;
    pickerEl.value = iso;
  });

  // Handle blur events for validation
  displayEl.addEventListener('blur', (e) => {
    const value = e.target.value;
    const digits = value.replace(/\D/g, '');
    
    // Validate date format
    if (digits.length === 8) {
      const day = parseInt(digits.slice(0, 2));
      const month = parseInt(digits.slice(2, 4));
      const year = parseInt(digits.slice(4, 8));
      
      // Basic validation
      if (day > 31 || month > 12 || year < 1900 || year > 2100) {
        // Clear invalid input
        displayEl.value = '';
        hiddenEl.value = '';
        pickerEl.value = '';
        
        // Show user-friendly error without console logging
        if (window.avoidNextError) {
          window.avoidNextError.logWarning('Format tanggal tidak valid. Gunakan format dd/mm/yyyy');
        }
      } else {
        // Additional validation for month-specific days
        const monthDays = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        const isLeapYear = (year % 4 === 0 && year % 100 !== 0) || (year % 400 === 0);
        const febDays = isLeapYear ? 29 : 28;
        
        if (month === 2 && day > febDays) {
          displayEl.value = '';
          hiddenEl.value = '';
          pickerEl.value = '';
          
          if (window.avoidNextError) {
            window.avoidNextError.logWarning('Tanggal Februari tidak valid untuk tahun ini');
          }
        } else if (day > monthDays[month - 1]) {
          displayEl.value = '';
          hiddenEl.value = '';
          pickerEl.value = '';
          
          if (window.avoidNextError) {
            window.avoidNextError.logWarning('Hari tidak valid untuk bulan ini');
          }
        }
      }
    } else if (digits.length > 0 && digits.length < 8) {
      // Partial input - show gentle reminder
      if (window.avoidNextError) {
        window.avoidNextError.logWarning('Tanggal belum lengkap. Masukkan 8 digit angka (ddmmyyyy)');
      }
    }
  });

  // Initialize if picker has value (e.g., autofill)
  if (pickerEl.value) {
    const event = new Event('change');
    pickerEl.dispatchEvent(event);
  }
}

// Export to global scope for easy reuse
window.initDateInput = initDateInput;
