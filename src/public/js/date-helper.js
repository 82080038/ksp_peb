// Reusable date input initializer
// Features:
// - Display input dd/mm/yyyy (manual typing allowed)
// - Auto-format digits: 01012022 -> 01/01/2022
// - Hidden ISO field (yyyy-mm-dd) kept in sync
// - Native date picker opened via trigger button
// Params: { displayId, hiddenId, pickerId, triggerId }
function initDateInput({ displayId, hiddenId, pickerId, triggerId }) {
  const displayEl = document.getElementById(displayId);
  const hiddenEl = document.getElementById(hiddenId);
  const pickerEl = document.getElementById(pickerId);
  const triggerEl = document.getElementById(triggerId);
  if (!displayEl || !hiddenEl || !pickerEl || !triggerEl) return;

  // Open native picker only via trigger
  const openPicker = () => pickerEl.showPicker?.();
  triggerEl.addEventListener('click', openPicker);

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
    return `${digits.slice(0, 2)}/${digits.slice(2, 4)}/${digits.slice(4, 8)}`;
  };

  // Sync picker -> display + hidden
  pickerEl.addEventListener('change', (e) => {
    const value = e.target.value; // yyyy-mm-dd
    hiddenEl.value = value;
    displayEl.value = isoToDisplay(value);
  });

  // Manual typing with live formatting
  displayEl.addEventListener('input', (e) => {
    const digits = e.target.value.replace(/\D/g, '').slice(0, 8);
    const formatted = digitsToDisplay(digits);
    displayEl.value = formatted;
    const iso = digits.length === 8 ? `${digits.slice(4, 8)}-${digits.slice(2, 4)}-${digits.slice(0, 2)}` : '';
    hiddenEl.value = iso;
  });

  // Initialize if picker has value (e.g., autofill)
  if (pickerEl.value) {
    const event = new Event('change');
    pickerEl.dispatchEvent(event);
  }
}

// Export to global scope for easy reuse
window.initDateInput = initDateInput;
