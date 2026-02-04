# NPWP Format Analysis & Recommendations

## 📊 **NPWP Format Analysis**

### **🇮🇩 Standard NPWP Format:**
```
01.234.567.8-012.000
```

**Breakdown:**
- `01.234.567.8` - Nomor Wajib Pajak (8 digit)
- `012` - Kode KPP/Posisi (3 digit)  
- `000` - Serial Number (3 digit)

### **📋 **Format Variations yang Umum:**
1. **With Dots:** `01.234.567.8-012.000` (16 digit)
2. **Without Dots:** `012345678012000` (15 digit)
3. **With Hyphen:** `01.234.567.8-012.000` (16 digit)
4. **Mixed:** `01.234567.8-012.000` (16 digit)

---

## 🎯 **Rekomendasi Penyimpanan Database**

### **✅ Opsi 1: Clean Numbers (RECOMMENDED)**
**Database:** `012345678012000` (15 digit, tanpa formatting)
**Keuntungan:**
- ✅ **Universal** - Bisa digunakan untuk semua format
- ✅ **Validation Mudah** - Regex sederhana `/^[0-9]{15}$/`
- ✅ **API Friendly** - Mudah parsing dan processing
- ✅ **Storage Efficient** - Tidak ada karakter tambahan
- ✅ **International Standard** - Nomor murni

**Frontend Display:** `01.234.567.8-012.000`

### **⚠️ Opsi 2: With Dots (NOT RECOMMENDED)**
**Database:** `01.234.567.8-012.000` (16 digit, dengan titik)
**Kerugan:**
- ❌ **Validation Complex** - Regex rumit `/^\d{2}\.\d{3}\.\d{3}\.\d-\d{3}\.\d{3}$/`
- ❌ **Storage Inefficient** - Karakter tambahan tidak perlu
- ❌ **API Issues** - Parsing lebih sulit
- ❌ **International** - Titik tidak universal separator

---

## 🛠️ **Implementasi Suggested**

### **1. Frontend Formatting:**
```javascript
// NPWP formatting function
function formatNPWP(npwp) {
    if (!npwp) return '';
    
    // Remove all non-digits
    let clean = npwp.replace(/[^0-9]/g, '');
    
    // Format as 01.234.567.8-012.000
    if (clean.length === 15) {
        return clean.slice(0, 2) + '.' + 
               clean.slice(2, 5) + '.' + 
               clean.slice(5, 9) + '.' + 
               clean.slice(9, 12) + '-' + 
               clean.slice(12, 15) + '.' + 
               clean.slice(15);
    }
    
    return clean;
}

// Input formatting
document.getElementById('npwp').addEventListener('input', (e) => {
    let value = e.target.value.replace(/[^0-9]/g, '').slice(0, 15);
    e.target.value = formatNPWP(value);
});
```

### **2. Backend Storage:**
```php
// Clean NPWP before storage
$npwpClean = preg_replace('/[^0-9]/', '', $data['npwp']);

// Validation
if (!preg_match('/^[0-9]{15}$/', $npwpClean)) {
    return ['success' => false, 'message' => 'Format NPWP tidak valid'];
}

// Store clean number in database
$stmt->execute([$npwpClean]);
```

### **3. Display Formatting:**
```php
// Format for display
function formatNPWPDisplay($npwp) {
    if (strlen($npwp) === 15) {
        return substr($npwp, 0, 2) . '.' . 
               substr($npwp, 2, 3) . '.' . 
               substr($npwp, 5, 4) . '.' . 
               substr($npwp, 9, 3) . '-' . 
               substr($npwp, 12, 3) . '.' . 
               substr($npwp, 15);
    }
    return $npwp;
}
```

---

## 🎯 **Final Recommendation**

### **✅ STORAGE FORMAT: Clean Numbers**
```
Database: 012345678012000
Frontend: 01.234.567.8-012.000
```

### **📋 Alasan:**
1. **Simplicity** - Hanya angka, mudah di-handle
2. **Flexibility** - Bisa format ke tampilan apapun
3. **Performance** - Storage lebih efisien
4. **Validation** - Regex sangat sederhana
5. **Standard** - Nomor murni adalah standar global
6. **Future-Proof** - Mudah adaptasi perubahan format

### **🔄 Data Flow:**
```
Input → 012345678012000
  ↓
Frontend → 01.234.567.8-012.000 (tampilan)
  ↓
API → 012345678012000 (clean)
  ↓
Database → 012345678012000 (storage)
  ↓
Display → 01.234.567.8-012.000 (formatted)
```

---

## 🚀 **Implementation Priority**

1. **High Priority:** Clean numbers di database
2. **Medium Priority:** Frontend formatting
3. **Low Priority:** Backend display formatting

**Rekomendasi: Simpan NPWP sebagai clean numbers di database, format hanya untuk tampilan!** 🎯
