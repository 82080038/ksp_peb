// Reusable helpers for formatting numbers and currency (Indonesian locale)
// Usage:
//   formatRupiah(1500000) => "Rp 1.500.000"
//   formatNumber(12345.67) => "12.345,67"
// Options:
//   formatRupiah(value, { maximumFractionDigits: 2, minimumFractionDigits: 0 })
//   formatNumber(value, { minimumFractionDigits: 0, maximumFractionDigits: 2 })

function formatRupiah(value, options = {}) {
  const opts = {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
    ...options,
  };
  const number = Number(value) || 0;
  return new Intl.NumberFormat('id-ID', opts).format(number);
}

function formatNumber(value, options = {}) {
  const opts = {
    minimumFractionDigits: 0,
    maximumFractionDigits: 2,
    ...options,
  };
  const number = Number(value) || 0;
  return new Intl.NumberFormat('id-ID', opts).format(number);
}

// Export to global scope for easy reuse
window.formatRupiah = formatRupiah;
window.formatNumber = formatNumber;
