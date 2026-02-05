// String helpers for casing and cleanup
// toTitleCase: capitalize each word, preserving spaces
// toCamelCase: remove spaces/non-alphanum, camelCase result
// normalizeWhitespace: trim and collapse multiple spaces
// terbilangID: number to Indonesian words (simplified, max billions)
// abbreviateWords: take first letters of each word (uppercase)
// nickname: first word, title-cased
// truncateText: limit length with ellipsis
// slugify: convert to URL-friendly slug
// debounce: limit function execution frequency

function normalizeWhitespace(str = '') {
  return str.replace(/\s+/g, ' ').trim();
}

function toTitleCase(str = '') {
  const clean = normalizeWhitespace(str.toLowerCase());
  return clean.replace(/\b\w/g, (c) => c.toUpperCase());
}

function toCamelCase(str = '') {
  const clean = normalizeWhitespace(str.toLowerCase());
  return clean
    .split(' ')
    .map((word, idx) => (idx === 0 ? word : word.charAt(0).toUpperCase() + word.slice(1)))
    .join('');
}

// Number to Indonesian words (simple, up to billions)
function terbilangID(n) {
  n = Math.floor(Number(n) || 0);
  const satuan = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];
  const spell = (x) => {
    if (x < 12) return satuan[x];
    if (x < 20) return spell(x - 10) + ' belas';
    if (x < 100) return spell(Math.floor(x / 10)) + ' puluh ' + spell(x % 10);
    if (x < 200) return 'seratus ' + spell(x - 100);
    if (x < 1000) return spell(Math.floor(x / 100)) + ' ratus ' + spell(x % 100);
    if (x < 2000) return 'seribu ' + spell(x - 1000);
    if (x < 1000000) return spell(Math.floor(x / 1000)) + ' ribu ' + spell(x % 1000);
    if (x < 1000000000) return spell(Math.floor(x / 1000000)) + ' juta ' + spell(x % 1000000);
    return spell(Math.floor(x / 1000000000)) + ' miliar ' + spell(x % 1000000000);
  };
  return normalizeWhitespace(spell(n)).trim();
}

// Abbreviate words to initials (uppercase)
function abbreviateWords(str = '') {
  const clean = normalizeWhitespace(str);
  if (!clean) return '';
  return clean.split(' ').map(w => w.charAt(0).toUpperCase()).join('');
}

// Nickname: first word, title-cased
function nickname(str = '') {
  const clean = normalizeWhitespace(str);
  if (!clean) return '';
  const first = clean.split(' ')[0];
  return toTitleCase(first);
}

// Truncate with ellipsis if exceeds length
function truncateText(str = '', maxLength = 50) {
  const s = str || '';
  if (s.length <= maxLength) return s;
  return s.slice(0, Math.max(0, maxLength - 3)) + '...';
}

// Slugify (URL-friendly)
function slugify(str = '') {
  return normalizeWhitespace(str)
    .toLowerCase()
    .replace(/[^a-z0-9\s-]/g, '')
    .replace(/\s+/g, '-');
}

// Debounce helper
function debounce(fn, delay = 300) {
  let t;
  return (...args) => {
    clearTimeout(t);
    t = setTimeout(() => fn.apply(null, args), delay);
  };
}

// Export globally for reuse
window.normalizeWhitespace = normalizeWhitespace;
window.toTitleCase = toTitleCase;
window.toCamelCase = toCamelCase;
window.terbilangID = terbilangID;
window.abbreviateWords = abbreviateWords;
window.nickname = nickname;
window.truncateText = truncateText;
window.slugify = slugify;
window.debounce = debounce;
