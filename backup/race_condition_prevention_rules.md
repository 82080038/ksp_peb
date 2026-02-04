# Race Condition Prevention Rules - KSP Application

## 🎯 **Overview**

Dokumentasi aturan untuk mencegah **race condition** yang dapat menyebabkan error di aplikasi KSP. Race condition terjadi ketika multiple operations berjalan secara bersamaan dan mengakses shared resources yang sama.

---

## ⚠️ **Identified Race Conditions**

### **❌ 1. Event Listener Duplication (Fixed)**
**Location:** `register_cooperative.php`
```javascript
// ❌ PROBLEM: 2 event listener untuk elemen yang sama
1. FormHelper.setupJenisKoperasiDynamic() (line 291)
2. Manual event listener (line 307-318) ← CONFLICT!

// ✅ SOLUTION: Hapus manual event listener
// Hanya gunakan FormHelper.setupJenisKoperasiDynamic()
```

### **❌ 2. Async Operation Without Wait (Fixed)**
**Location:** `register_cooperative.php`
```javascript
// ❌ PROBLEM: Setup tanpa menunggu API response
loadCooperativeTypes(); // Async
FormHelper.setupJenisKoperasiDynamic(); // Jalan duluan!

// ✅ SOLUTION: Sequential execution dengan Promise
loadCooperativeTypes().then(() => {
    FormHelper.setupJoperasiDynamic('jenis_koperasi', 'nama_koperasi', null);
});
```

### **❌ 3. DOM Access Before Ready (Potential)**
**Location:** `register.php`, `current_script.txt`
```javascript
// ❌ PROBLEM: setTimeout untuk DOM ready
setTimeout(() => attachRegisterFormListener(), 100);

// ✅ SOLUTION: Gunakan DOMContentLoaded
document.addEventListener('DOMContentLoaded', function() {
    attachRegisterFormListener();
});
```

### **❌ 4. Multiple Async API Calls (Potential)**
**Location:** `register.php`, `dashboard.php`
```javascript
// ❌ PROBLEM: Multiple API calls tanpa coordination
loadProvinces();
loadCooperativeTypes();
loadStatistics();

// ✅ SOLUTION: Sequential atau Promise.all
Promise.all([
    loadProvinces(),
    loadCooperativeTypes(),
    loadStatistics()
]).then(() => {
    // Setup setelah semua selesai
});
```

---

## 🛡️ **Prevention Rules**

### **✅ Rule 1: Single Event Listener Pattern**
```javascript
// ❌ AVOID: Multiple event listeners
element.addEventListener('change', handler1);
element.addEventListener('change', handler2); ← CONFLICT!

// ✅ USE: Single event listener dengan centralized logic
element.addEventListener('change', function() {
    // Centralized logic
    handler1();
    handler2();
});
```

### **✅ Rule 2: Sequential Async Operations**
```javascript
// ❌ AVOID: Async tanpa wait
loadData();
setupEventListeners(); // Jalan duluan!

// ✅ USE: Sequential dengan Promise
loadData().then(() => {
    setupEventListeners();
});

// ATAU gunakan async/await
async function initialize() {
    await loadData();
    setupEventListeners();
}
```

### **✅ Rule 3: DOM Ready Validation**
```javascript
// ❌ AVOID: setTimeout untuk DOM ready
setTimeout(() => setupElements(), 100);

// ✅ USE: DOMContentLoaded event
document.addEventListener('DOMContentLoaded', function() {
    setupElements();
});

// ATAU check element existence
function setupElements() {
    const element = document.getElementById('myElement');
    if (!element) {
        console.warn('Element not found');
        return;
    }
    // Setup logic
}
```

### **✅ Rule 4: API Call Coordination**
```javascript
// ❌ AVOID: Multiple independent API calls
loadCooperativeTypes();
loadProvinces();
loadStatistics();

// ✅ USE: Coordinated API calls
async function initializeApp() {
    try {
        const [types, provinces, stats] = await Promise.all([
            loadCooperativeTypes(),
            loadProvinces(),
            loadStatistics()
        ]);
        
        // Setup setelah semua selesai
        setupEventListeners();
        setupUI();
    } catch (error) {
        console.error('Initialization failed:', error);
    }
}
```

### **✅ Rule 5: State Management**
```javascript
// ❌ AVOID: Global state tanpa protection
let isLoading = false;
function loadData() {
    isLoading = true; // Race condition possible
}

// ✅ USE: Protected state management
class AppState {
    constructor() {
        this.isLoading = false;
        this.isInitialized = false;
    }
    
    async loadData() {
        if (this.isLoading) return;
        
        this.isLoading = true;
        try {
            // Load data
        } finally {
            this.isLoading = false;
        }
    }
}
```

---

## 🔧 **Implementation Guidelines**

### **✅ 1. Initialization Pattern**
```javascript
// Standard initialization pattern
class ComponentManager {
    constructor() {
        this.isInitialized = false;
        this.elements = {};
    }
    
    async initialize() {
        if (this.isInitialized) return;
        
        try {
            await this.loadDependencies();
            this.cacheElements();
            this.attachEventListeners();
            this.isInitialized = true;
        } catch (error) {
            console.error('Initialization failed:', error);
        }
    }
    
    cacheElements() {
        this.elements.jenisSelect = document.getElementById('jenis_koperasi');
        this.elements.namaInput = document.getElementById('nama_koperasi');
        
        // Validate elements
        Object.entries(this.elements).forEach(([key, element]) => {
            if (!element) {
                throw new Error(`Element ${key} not found`);
            }
        });
    }
    
    attachEventListeners() {
        // Single event listener pattern
        this.elements.jenisSelect.addEventListener('change', (e) => {
            this.handleJenisChange(e);
        });
    }
}
```

### **✅ 2. API Call Pattern**
```javascript
// Safe API call pattern
class APIService {
    constructor() {
        this.cache = new Map();
        this.pendingRequests = new Map();
    }
    
    async fetchData(endpoint, useCache = true) {
        // Check cache
        if (useCache && this.cache.has(endpoint)) {
            return this.cache.get(endpoint);
        }
        
        // Check pending request
        if (this.pendingRequests.has(endpoint)) {
            return this.pendingRequests.get(endpoint);
        }
        
        // Make request
        const promise = this.makeRequest(endpoint);
        this.pendingRequests.set(endpoint, promise);
        
        try {
            const data = await promise;
            this.cache.set(endpoint, data);
            return data;
        } finally {
            this.pendingRequests.delete(endpoint);
        }
    }
    
    async makeRequest(endpoint) {
        const response = await fetch(endpoint);
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    }
}
```

### **✅ 3. Event Listener Pattern**
```javascript
// Safe event listener pattern
class EventManager {
    constructor() {
        this.listeners = new Map();
    }
    
    addListener(element, event, handler, options = {}) {
        // Remove existing listener
        if (this.listeners.has(element)) {
            const existing = this.listeners.get(element);
            element.removeEventListener(event, existing.handler);
        }
        
        // Add new listener
        element.addEventListener(event, handler, options);
        this.listeners.set(element, { event, handler, options });
    }
    
    removeListener(element) {
        if (this.listeners.has(element)) {
            const { event, handler } = this.listeners.get(element);
            element.removeEventListener(event, handler);
            this.listeners.delete(element);
        }
    }
    
    cleanup() {
        this.listeners.forEach((listener, element) => {
            this.removeListener(element);
        });
    }
}
```

---

## 📋 **Checklist for Race Condition Prevention**

### **✅ Development Checklist:**
- [ ] **Single Event Listener:** Hanya 1 event listener per elemen
- [ ] **Sequential Initialization:** Async operations dengan proper wait
- [ ] **DOM Validation:** Check element existence sebelum access
- [ ] **State Protection:** Protected state management
- [ ] **API Coordination:** Coordinated API calls
- [ ] **Error Handling:** Proper error handling untuk async operations
- [ ] **Cleanup:** Proper cleanup untuk event listeners

### **✅ Code Review Checklist:**
- [ ] **No Duplicate Listeners:** Tidak ada event listener duplikat
- [ ] **Proper Async Pattern:** Gunakan Promise.all atau sequential
- [ ] **DOM Ready Check:** Element existence validation
- [ ] **State Management:** Protected state access
- [ ] **API Call Safety:** Request deduplication dan caching
- [ ] **Error Boundaries:** Proper error handling
- [ ] **Memory Leaks:** Event listener cleanup

### **✅ Testing Checklist:**
- [ ] **Race Condition Testing:** Test concurrent operations
- [ ] **Async Flow Testing:** Test async operation sequences
- [ ] **DOM Timing Testing:** Test DOM access timing
- [ ] **State Consistency:** Test state consistency
- [ ] **Error Recovery:** Test error recovery scenarios
- [ ] **Performance Testing:** Test performance under load

---

## 🚨 **Common Race Condition Scenarios**

### **❌ Scenario 1: Form Setup Race**
```javascript
// PROBLEM: Form setup sebelum data loaded
loadFormData();
setupFormValidation(); // Jalan duluan!
attachEventListeners();

// SOLUTION: Sequential setup
async function initializeForm() {
    await loadFormData();
    setupFormValidation();
    attachEventListeners();
}
```

### **❌ Scenario 2: Dropdown Population Race**
```javascript
// PROBLEM: Dropdown setup sebelum options loaded
loadCooperativeTypes();
setupJenisKoperasiDynamic(); // Jalan duluan!

// SOLUTION: Wait untuk options
loadCooperativeTypes().then(() => {
    setupJenisKoperasiDynamic();
});
```

### **❌ Scenario 3: Event Listener Race**
```javascript
// PROBLEM: Multiple listeners untuk same event
element.addEventListener('change', handler1);
element.addEventListener('change', handler2); // Conflict!

// SOLUTION: Single listener dengan centralized logic
element.addEventListener('change', function(e) {
    handler1(e);
    handler2(e);
});
```

---

## 🎯 **Best Practices Summary**

### **✅ 1. Initialization Order:**
1. **DOM Ready** → Wait untuk DOM
2. **Load Dependencies** → API calls, data loading
3. **Cache Elements** → Cache DOM elements
4. **Setup Listeners** → Attach event listeners
5. **Initialize State** → Set initial state

### **✅ 2. Async Operation Pattern:**
```javascript
// Standard async pattern
async function initialize() {
    try {
        // Load dependencies
        await loadDependencies();
        
        // Setup UI
        setupUI();
        
        // Attach listeners
        attachEventListeners();
        
        // Initialize state
        initializeState();
    } catch (error) {
        handleError(error);
    }
}
```

### **✅ 3. Error Handling:**
```javascript
// Proper error handling
try {
    await initialize();
} catch (error) {
    console.error('Initialization failed:', error);
    // Fallback or error UI
    showErrorState();
}
```

---

## 🔍 **Monitoring & Debugging**

### **✅ Race Condition Detection:**
```javascript
// Debug logging untuk race condition detection
class RaceConditionDetector {
    constructor() {
        this.operations = new Map();
    }
    
    startOperation(name) {
        if (this.operations.has(name)) {
            console.warn(`Race condition detected: ${name} already running`);
        }
        this.operations.set(name, Date.now());
    }
    
    endOperation(name) {
        if (this.operations.has(name)) {
            const duration = Date.now() - this.operations.get(name);
            console.log(`Operation ${name} completed in ${duration}ms`);
            this.operations.delete(name);
        }
    }
}
```

### **✅ Performance Monitoring:**
```javascript
// Performance monitoring
class PerformanceMonitor {
    constructor() {
        this.metrics = new Map();
    }
    
    measureAsync(name, asyncFn) {
        const start = performance.now();
        return asyncFn().then(result => {
            const duration = performance.now() - start;
            this.metrics.set(name, duration);
            console.log(`${name} took ${duration}ms`);
            return result;
        });
    }
}
```

---

## 📝 **Implementation Template**

### **✅ Safe Component Template:**
```javascript
class SafeComponent {
    constructor(elementId) {
        this.elementId = elementId;
        this.element = null;
        this.isInitialized = false;
        this.eventListeners = new Map();
    }
    
    async initialize() {
        if (this.isInitialized) return;
        
        try {
            await this.waitForElement();
            await this.loadData();
            this.setupEventListeners();
            this.isInitialized = true;
        } catch (error) {
            console.error(`Failed to initialize ${this.elementId}:`, error);
        }
    }
    
    async waitForElement() {
        return new Promise((resolve, reject) => {
            const checkElement = () => {
                this.element = document.getElementById(this.elementId);
                if (this.element) {
                    resolve();
                } else if (document.readyState === 'complete') {
                    reject(new Error(`Element ${this.elementId} not found`));
                } else {
                    setTimeout(checkElement, 50);
                }
            };
            checkElement();
        });
    }
    
    setupEventListeners() {
        // Single event listener pattern
        this.addEventListener('change', this.handleChange.bind(this));
    }
    
    addEventListener(event, handler) {
        if (this.eventListeners.has(event)) {
            this.element.removeEventListener(event, this.eventListeners.get(event));
        }
        this.element.addEventListener(event, handler);
        this.eventListeners.set(event, handler);
    }
    
    cleanup() {
        this.eventListeners.forEach((handler, event) => {
            this.element.removeEventListener(event, handler);
        });
        this.eventListeners.clear();
        this.isInitialized = false;
    }
}
```

---

## 🎉 **Conclusion**

**✅ Race Condition Prevention:**
- **Single Source of Truth:** Hanya 1 event listener per elemen
- **Sequential Execution:** Async operations dengan proper wait
- **State Protection:** Protected state management
- **Error Handling:** Proper error handling dan recovery
- **Performance Monitoring:** Monitor dan detect race conditions

**✅ Implementation Benefits:**
- **Reliable:** Aplikasi yang lebih reliable
- **Predictable:** Behavior yang lebih predictable
- **Maintainable:** Code yang lebih mudah di-maintain
- **Debuggable:** Mudah untuk debug dan troubleshoot

**✅ Development Workflow:**
- **Prevention:** Prevent race conditions during development
- **Detection:** Detect race conditions during testing
- **Monitoring:** Monitor race conditions in production
- **Resolution:** Quick resolution of race condition issues

**🚀 Dengan mengikuti aturan ini, aplikasi KSP akan bebas dari race condition yang menyebabkan error!**
