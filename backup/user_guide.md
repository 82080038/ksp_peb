# 📚 Panduan Pengguna Aplikasi KSP (Koperasi Simpan Pinjam)

## 🎯 **Overview**

Panduan ini akan terupdate secara dinamis seiring dengan pengembangan aplikasi KSP. Versi terakhir: **v1.0.0** (2026-02-04)

---

## 📋 **Daftar Isi (Update Terakhir: 2026-02-04)**

### **🚀 Quick Start**
- [Login ke Aplikasi](#-login-ke-aplikasi)
- [Registrasi Anggota](#-registrasi-anggota)
- [Registrasi Koperasi](#-registrasi-koperasi)
- [Dashboard Overview](#-dashboard-overview)

### **🏢 Manajemen Koperasi**
- [Cooperative Settings](#-cooperative-settings)
- [RAT Management](#-rat-management)
- [Legal Documents](#-legal-documents)
- [Status Tracking](#-status-tracking)

### **👥 Manajemen Anggota**
- [Registrasi Anggota](#-registrasi-anggota)
- [Data Anggota](#-data-anggota)
- [SHU & Simpanan](#-shu--simpanan)
- [Pinjaman](#-pinjaman)

### **📊 Keuangan & Laporan**
- [Dashboard Keuangan](#-dashboard-keuangan)
- [Jurnal & Ledger](#-jurnal--ledger)
- [Laporan Keuangan](#-laporan-keuangan)
- [Audit Trail](#-audit-trail)

### **⚙️ Administrasi**
- [User Management](#-user-management)
- [Role & Permissions](#-role--permissions)
- [System Settings](#-system-settings)
- [Backup & Restore](#-backup--restore)

---

## 🚀 **Quick Start**

### **🔐 Login ke Aplikasi**

1. **Buka Browser:** Akses `http://localhost/ksp_peb/login.php`
2. **Masukkan Kredensial:**
   - **Username:** Username yang terdaftar
   - **Password:** Password yang terdaftar
3. **Klik Login:** Masuk ke dashboard

**📝 Login Credentials:**
- **Default Admin:** `admin` / `admin123`
- **Default Anggota:** `user` / `user123`

---

### **👤 Registrasi Anggota**

1. **Akses:** `http://localhost/ksp_peb/register.php`
2. **Pilih Lokasi:**
   - **Provinsi** → Pilih provinsi
   - **Kabupaten/Kota** → Pilih kabupaten/kota
   - **Kecamatan** → Pilih kecamatan
   - **Desa/Kelurahan** → Pilih desa/kelurahan
3. **Pilih Koperasi:** Pilih koperasi yang tersedia
4. **Isi Form Anggota:**
   - **Nama Lengkap** (wajib)
   - **Nomor HP** (wajib, format: 08xxxxxxxxxx)
   - **Email** (wajib, format email valid)
   - **Alamat Lengkap** (wajib)
   - **Username** (wajib, min 3 karakter)
   - **Password** (wajib, min 6 karakter)
   - **Konfirmasi Password** (wajib)
5. **Klik "Daftar Sekarang"**

**📋 Field Validation:**
- **Nomor HP:** Harus dimulai dengan 08, 10-13 digit
- **Email:** Format email valid
- **Username:** Unik, alphanumeric dan underscore
- **Password:** Min 6 karakter, case-sensitive

---

### **🏢 Registrasi Koperasi**

1. **Akses:** `http://localhost/ksp_peb/register_cooperative.php`
2. **Isi Form Koperasi:**

#### **📍 Lokasi Koperasi**
- **Provinsi** → Pilih provinsi
- **Kabupaten/Kota** → Pilih kabupaten/kota
- **Kecamatan** → Pilih kecamatan
- **Desa/Kelurahan** → Pilih desa/kelurahan

#### **📝 Informasi Dasar**
- **Jenis Koperasi** → Pilih dari 12 jenis koperasi
- **Nama Koperasi** → Nama resmi koperasi
- **Status Badan Hukum** → Pilih status
- **Tanggal Pendirian** → Tanggal akta pendirian
- **NPWP** → 16 digit NPWP (PMK 112/2022)
- **Kontak Resmi** → Nomor telepon kantor

#### **👤 Informasi Administrator**
- **Nama Lengkap** → Nama admin koperasi
- **Username** → Username untuk login
- **Email** → Email admin koperasi
- **Nomor HP** → Nomor HP admin
- **Password** → Password admin

3. **Klik "Daftarkan Koperasi"**

**📋 Jenis Koperasi Tersedia:**
- **KJ** - Koperasi Jasa
- **KK** - Koperasi Konsumsi
- **KKAR** - Koperasi Karyawan
- **KOPERDAG** - Koperasi Perdagangan
- **KOPERNAL** - Koperasi Nelayan
- **KOPERTA** - Koperasi Pertanian
- **KOPERTAK** - Koperasi Peternakan
- **KOPONTREN** - Koperasi Pondok Pesantren
- **KP** - Koperasi Produksi
- **KPAS** - Koperasi Pemasaran
- **KSP** - Koperasi Simpan Pinjam
- **KSU** - Koperasi Serba Usaha

---

## 🏢 **Dashboard Overview**

### **📊 Dashboard Utama**

Akses: `http://localhost/ksp_peb/dashboard.php`

#### **📈 Statistics Cards**
- **Total Anggota:** Jumlah anggota aktif
- **Total Simpanan:** Total simpanan anggota
- **Total Pinjaman:** Total pinjaman aktif
- **SHU Tersedia:** Total SHU yang bisa dibagikan

#### **📋 Quick Actions**
- **Registrasi Anggota:** Tambah anggota baru
- **Pinjaman Baru:** Ajukan pinjaman baru
- **Laporan:** Lihat laporan keuangan
- **Settings:** Konfigurasi sistem

#### **📅 Recent Activities**
- **Registrasi anggota terbaru**
- **Pinjaman yang diajukan**
- **Simpanan yang masuk**
- **Status perubahan sistem**

---

## 🏢 **Manajemen Koperasi**

### **⚙️ Cooperative Settings**

Akses: Dashboard → Cooperative Settings

#### **📝 Informasi Dasar**
- **Nama Koperasi:** Edit nama koperasi
- **Jenis Koperasi:** Ubah jenis koperasi
- **Status Badan Hukum:** Update status
- **Tanggal Pendirian:** Edit tanggal pendirian
- **NPWP:** Update NPWP
- **Kontak Resmi:** Update kontak

#### **📜 Informasi Legal**
- **Nomor Badan Hukum:** Nomor SABH
- **NIB:** Nomor Induk Berusaha
- **NIK Koperasi:** NIK koperasi
- **Modal Pokok:** Modal pokok koperasi
- **Catatan Status:** Catatan tambahan

#### **📋 Riwayat Perubahan**
- **Status History:** Riwayat perubahan status
- **Document History:** Riwayat perubahan dokumen
- **User Attribution:** Siapa yang melakukan perubahan
- **Timestamp:** Waktu perubahan

---

### **📊 RAT Management**

Akses: Dashboard → RAT Management

#### **📈 Current Modal Pokok**
- **Nilai Saat Ini:** Modal pokok terakhir
- **Sumber Perubahan:** Dari RAT atau manual
- **Tanggal Update:** Kapan terakhir diubah

#### **📅 Sesi RAT**
- **Tahun:** Tahun pelaksanaan RAT
- **Tanggal Rapat:** Tanggal pelaksanaan
- **Tempat:** Lokasi rapat
- **Status:** scheduled → in_progress → completed
- **Modal Pokok Sebelum:** Modal pokok sebelum RAT
- **Modal Pokok Sesudah:** Modal pokok setelah RAT
- **% Perubahan:** Persentase perubahan

#### **🔄 Quick Actions**
- **Buat Sesi RAT Baru:** Jadwalkan RAT baru
- **Update Modal Pokok Manual:** Update manual
- **Refresh Data:** Refresh data terbaru

#### **📋 Riwayat Perubahan Modal Pokok**
- **Tanggal:** Tanggal perubahan
- **Modal Pokok Lama:** Nilai sebelum perubahan
- **Modal Pokok Baru:** Nilai setelah perubahan
- **% Perubahan:** Persentase perubahan
- **Sumber Perubahan:** RAT atau manual
- **Alasan:** Alasan perubahan
- **User:** User yang melakukan perubahan

---

## 📊 **Keuangan & Laporan**

### **💰 Dashboard Keuangan**

#### **📈 Financial Overview**
- **Total Simpanan:** Total semua simpanan
- **Total Pinjaman:** Total semua pinjaman
- **Total SHU:** Total SHU tersedia
- **Rasio Keuangan:** Rasio simpanan vs pinjaman

#### **📅 Grafik Per Bulan**
- **Simpanan Masuk:** Grafik simpanan per bulan
- **Pinjaman Cair:** Grafik pinjaman per bulan
- **SHU Dibagikan:** Grafik SHU per bulan
- **Profit/Loss:** Grafik profit/loss per bulan

---

### **📋 Laporan Keuangan**

#### **📊 Laporan Bulanan**
- **Laporan Simpanan:** Detail simpanan per bulan
- **Laporan Pinjaman:** Detail pinjaman per bulan
- **Laporan SHU:** Detail SHU per bulan
- **Laporan Laba Rugi:** Laba rugi per bulan

#### **📈 Laporan Tahunan**
- **Laporan Tahunan:** Ringkasan tahunan
- **Laporan SHU Tahunan:** SHU per tahun
- **Laporan Pertumbuhan:** Pertumbuhan anggota

---

## 👥 **Manajemen Anggota**

### **📝 Data Anggota**

#### **👤 Informasi Pribadi**
- **Nama Lengkap:** Nama lengkap anggota
- **Nomor HP:** Nomor telepon anggota
- **Email:** Email anggota
- **Alamat:** Alamat lengkap anggota
- **Status:** Status keanggotaan

#### **💰 Keuangan Anggota**
- **Simpanan Pokok:** Jumlah simpanan pokok
- **Simpanan Wajib:** Jumlah simpanan wajib
- **Total Simpanan:** Total simpanan anggota
- **Pinjaman Aktif:** Pinjaman yang sedang berjalan
- **SHU Tersedia:** SHU yang bisa diambil

---

### **💎 SHU & Simpanan**

#### **📊 Jenis Simpanan**
- **Simpanan Pokok:** Simpanan wajib pokok
- **Simpanan Wajib:** Simpanan wajib bulanan
- **Simpanan Sukarela:** Simpanan sukarela
- **Simpanan Berjangka:** Simpanan berjangka

#### **💰 SHU (Sisa Hasil Usaha)**
- **Perhitungan SHU:** Berdasarkan tahun buku
- **Pembagian SHU:** Sesuai dengan keputusan RAT
- **Penarikan SHU:** Anggota bisa tarik SHU
- **History SHU:** Riwayat SHU anggota

---

### **🏦 Pinjaman**

#### **📋 Jenis Pinjaman**
- **Pinjaman Produktif:** Untuk kegiatan usaha
- **Pinjaman Konsumtif:** Untuk kebutuhan konsumsi
- **Pinjaman Darurat:** Untuk kebutuhan darurat
- **Pinjaman Angsuran:** Dengan cicilan bulanan

#### **💰 Detail Pinjaman**
- **Jumlah Pinjaman:** Nominal pinjaman
- **Bunga:** Suku bunga pinjaman
- **Tenor:** Lama pinjaman
- **Angsuran:** Cicilan bulanan
- **Status:** Status pinjaman

---

## ⚙️ **Administrasi**

### **👥 User Management**

#### **📋 Role System**
- **Admin:** Akses penuh ke semua fitur
- **Pengurus:** Akses ke fitur manajemen
- **Pengawas:** Akses ke fitur pengawasan
- **Anggota:** Akses ke fitur anggota

#### **🔐 User Creation**
- **Username:** Unique username
- **Password:** Hashed password
- **Email:** Email valid
- **Role:** Role assignment
- **Status:** Active/inactive/pending

---

### **🔧 Role & Permissions**

#### **📊 Permission Matrix**
- **Admin:** Semua permissions
- **Pengurus:** Manajemen anggota, keuangan
- **Pengawas:** View dan audit
- **Anggota:** View data sendiri

#### **🔑 Access Control**
- **Menu Access:** Berdasarkan role
- **Action Access:** Berdasarkan permission
- **Data Access:** Berdasarkan ownership

---

## 📋 **Form Validation Rules**

### **📝 General Validation**

#### **✅ Required Fields**
- **Semua field dengan asterisk (*)** wajib diisi
- **Tidak boleh kosong** untuk field wajib
- **Format sesuai** untuk field dengan format khusus

#### **📱 Format Validation**
- **Nomor HP:** `08xxxxxxxxxx` (10-13 digit)
- **NPWP:** `3201234567890001` (16 digit)
- **Email:** Standard email format
- **Username:** Alphanumeric + underscore, min 3 karakter
- **Password:** Min 6 karakter, case-sensitive

#### **🔢 Numeric Validation**
- **Modal Pokok:** Angka positif, max 15 digit
- **Simpanan:** Angka positif, max 15 digit
- **Pinjaman:** Angka positif, max 15 digit
- **Bunga:** Desimal, max 5 digit

---

### **📝 Form-Specific Rules**

#### **🏢 Register Cooperative**
```html
✅ jenis_koperasi: Required, harus dipilih dari dropdown
✅ nama_koperasi: Required, min 3 karakter, max 255
✅ badan_hukum: Required, enum('belum_terdaftar','terdaftar','badan_hukum')
✅ tanggal_pendirian: Required, valid date format
✅ npwp: Optional, 16 digit jika diisi
✅ kontak_resmi: Required, format 08xxxxxxxxxx
✅ admin_nama: Required, min 3 karakter
✅ admin_username: Required, unique, min 3 karakter
✅ admin_email: Required, valid email format
✅ admin_phone: Required, format 08xxxxxxxxxx
✅ admin_password: Required, min 6 karakter
```

#### **👤 Register Member**
```html
✅ member_name: Required, min 3 karakter
✅ member_phone: Required, format 08xxxxxxxxxx
✅ member_email: Required, valid email format
✅ member_address: Required, min 10 karakter
✅ username: Required, unique, min 3 karakter
✅ password: Required, min 6 karakter
✅ confirm_password: Required, sama dengan password
```

---

## 🗄️ **Database Structure**

### **📊 Core Tables**

#### **🏢 cooperatives**
```sql
CREATE TABLE cooperatives (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(255) NOT NULL,
    jenis LONGTEXT DEFAULT NULL CHECK (json_valid(jenis)),
    badan_hukum VARCHAR(255) DEFAULT NULL,
    status_badan_hukum ENUM('belum_terdaftar','terdaftar','badan_hukum') DEFAULT 'belum_terdaftar',
    tanggal_pendirian DATE DEFAULT NULL,
    npwp VARCHAR(50) DEFAULT NULL,
    nomor_bh VARCHAR(50) DEFAULT NULL,
    nib VARCHAR(20) DEFAULT NULL,
    nik_koperasi VARCHAR(20) DEFAULT NULL,
    modal_pokok DECIMAL(15,2) DEFAULT 0.00,
    alamat_legal TEXT DEFAULT NULL,
    kontak_resmi VARCHAR(255) DEFAULT NULL,
    created_by INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
    province_id INT DEFAULT NULL,
    regency_id INT DEFAULT NULL,
    district_id INT DEFAULT NULL,
    village_id INT DEFAULT NULL
);
```

#### **👥 users**
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    user_db_id INT NOT NULL,
    status ENUM('active','inactive','pending') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP()
);
```

#### **👤 anggota**
```sql
CREATE TABLE anggota (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(255) NOT NULL,
    no_hp VARCHAR(20) NOT NULL,
    email VARCHAR(255) NOT NULL,
    alamat TEXT NOT NULL,
    tanggal_daftar DATE NOT NULL,
    status ENUM('active','inactive','blacklist') DEFAULT 'active',
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP()
);
```

---

### **📋 Supporting Tables**

#### **📊 cooperative_types**
```sql
CREATE TABLE cooperative_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(10) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP()
);
```

#### **📊 cooperative_status_history**
```sql
CREATE TABLE cooperative_status_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cooperative_id INT NOT NULL,
    status_sebelumnya VARCHAR(50) DEFAULT NULL,
    status_baru VARCHAR(50) NOT NULL,
    tanggal_efektif DATE NOT NULL,
    change_reason VARCHAR(255),
    approval_status ENUM('pending','approved','rejected') DEFAULT 'approved',
    approved_by INT,
    approved_at TIMESTAMP NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP()
);
```

#### **📊 cooperative_document_history**
```sql
CREATE TABLE cooperative_document_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cooperative_id INT NOT NULL,
    document_type ENUM('nomor_bh','nib','nik_koperasi','modal_pokok') NOT NULL,
    document_number_lama VARCHAR(50),
    document_number_baru VARCHAR(50),
    document_value_lama DECIMAL(15,2),
    document_value_baru DECIMAL(15,2),
    tanggal_efektif DATE NOT NULL,
    change_reason TEXT,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP()
);
```

---

## 🔄 **API Endpoints**

### **📊 Cooperative API**

#### **🔍 GET Endpoints**
```php
GET /api/cooperative.php?action=list                    // Get all cooperatives
GET /api/cooperative.php?action=detail&id=X           // Get cooperative detail
GET /api/cooperative.php?action=types                   // Get cooperative types
GET /api/cooperative.php?action=villages&district_id=X // Get villages
GET /api/cooperative.php?action=create                  // Create cooperative (POST)
```

#### **📝 POST Endpoints**
```php
POST /api/cooperative.php?action=create                   // Create new cooperative
POST /api/cooperative.php?action=update                   // Update cooperative
POST /api/cooperative.php?action=delete                   // Delete cooperative
```

---

### **👥 User API**

#### **🔍 GET Endpoints**
```php
GET /api/auth.php?action=login                         // User login
GET /api/auth.php?action=logout                        // User logout
GET /api/auth.php?action=profile                       // User profile
GET /api/auth.php?action=check                         // Check auth status
```

#### **📝 POST Endpoints**
```php
POST /api/auth.php?action=login                         // User login
POST /api/auth.php?action=register                      // User registration
POST /api/auth.php?action=update_profile                 // Update profile
POST /api/auth.php?action=change_password               // Change password
```

---

## 📝 **Business Rules**

### **🏢 Cooperative Rules**

#### **📋 Jenis Koperasi**
- **12 Jenis Koperasi:** Berdasarkan PP No. 7/2021
- **KSP:** Koperasi Simpan Pinjam
- **KK:** Koperasi Konsumsi
- **KP:** Koperasi Produksi
- **KSU:** Koperasi Simpan Pinjam
- **DLL:** Jenis lain sesuai regulasi

#### **📜 Legal Requirements**
- **Badan Hukum:** Status sesuai UU No. 25/1992 & UU No. 6/2023
- **NPWP:** Format 16 digit sesuai PMK 112/2022
- **SABH:** Surat Akta Badan Hukum
- **NIB:** Nomor Induk Berusaha
- **NIK:** Nomor Induk Koperasi

#### **💰 Modal Pokok**
- **Minimum:** Rp 1.000.000
- **Update:** Hanya bisa diubah lewat RAT
- **Tracking:** Semua perubahan tercatat
- **Approval:** Perlu persetujuan admin

---

### **👥 Member Rules**

#### **📋 Keanggotaan**
- **Usia Minimum:** 17 tahun
- **Syarat:** Mengisi formulir pendaftaran
- **Approval:** Perlu persetujuan pengurus
- **Status:** Active/inactive/blacklist

#### **💰 Simpanan**
- **Simpanan Pokok:** Minimal Rp 100.000
- **Simpanan Wajib:** Minimal Rp 50.000/bulan
- **Bunga:** Sesuai keputusan rapat
- **Penarikan:** Sesuai ketentuan

#### **🏦 Pinjaman**
- **Plafon:** Maksimal 10x simpanan
- **Bunga:** Sesuai keputusan rapat
- **Tenor:** Maksimal 60 bulan
- **Jaminan:** Sesuai ketentuan

---

## 📊 **Reporting & Analytics**

### **📈 Financial Reports**

#### **📊 Laporan Bulanan**
- **Laporan Simpanan:** Detail simpanan per bulan
- **Laporan Pinjaman:** Detail pinjaman per bulan
- **Laporan SHU:** Detail SHU per bulan
- **Laporan Laba Rugi:** Laba rugi per bulan

#### **📈 Laporan Tahunan**
- **Laporan Tahunan:** Ringkasan tahunan
- **Laporan SHU Tahunan:** SHU per tahun
- **Laporan Pertumbuhan:** Pertumbuhan anggota
- **Laporan Keuangan:** Keuangan tahunan

---

### **📋 Compliance Reports**

#### **🔍 Audit Trail**
- **User Activity:** Log aktivitas user
- **Data Changes:** Log perubahan data
- **Financial Transactions:** Log transaksi keuangan
- **System Changes:** Log perubahan sistem

#### **📊 Regulatory Reports**
- **Laporan RAT:** Laporan RAT tahunan
- **Laporan Keuangan:** Laporan keuangan tahunan
- **Laporan Anggota:** Laporan anggota tahunan
- **Laporan Compliance:** Laporan compliance

---

## 🔧 **Troubleshooting**

### **🚨 Common Issues**

#### **🔐 Login Issues**
- **Username tidak ditemukan:** Cek username yang benar
- **Password salah:** Reset password
- **Akun tidak aktif:** Hubungi admin
- **Session expired:** Login kembali

#### **📝 Form Issues**
- **Validation error:** Periksa format input
- **Required field:** Isi semua field wajib
- **Network error:** Cek koneksi internet
- **Server error:** Hubungi admin

#### **💰 Financial Issues**
- **Saldo tidak muncul:** Tunggu proses 1x24 jam
- **Pinjaman ditolak:** Cek kelayakan
- **SHU tidak bisa diambil:** Cek periode SHU
- **Bunga tidak sesuai:** Cek keputusan rapat

---

### **📞 Support**

#### **📞 Kontak Support**
- **Admin:** admin@ksp.com
- **Support:** support@ksp.com
- **Hotline:** 08123456789
- **WhatsApp:** 08123456789

#### **📝 Help Desk**
- **Jam Operasional:** Senin-Jumat, 08:00-17:00
- **Response Time:** 1x24 jam
- **Priority:** Critical > High > Medium > Low
- **Escalation:** Admin > Manager > Director

---

## 📅 **Version History**

### **📋 Update Log**

#### **v1.0.0 (2026-02-04)**
- ✅ Initial release
- ✅ Cooperative registration
- ✅ Member registration
- ✅ Dashboard overview
- ✅ Basic financial features
- ✅ User management
- ✅ RAT management
- ✅ Document tracking

#### **🔄 Planned Updates**
- 📅 v1.1.0 - Advanced reporting
- 📅 v1.2.0 - Mobile app
- 📅 v1.3.0 - API integration
- 📅 v1.4.0 - Advanced analytics
- 📅 v1.5.0 - Multi-branch

---

## 📚 **Appendix**

### **📖 Glossary**
- **KSP:** Koperasi Simpan Pinjam
- **SHU:** Sisa Hasil Usaha
- **RAT:** Rapat Anggota Tahunan
- **SABH:** Surat Akta Badan Hukum
- **NIB:** Nomor Induk Berusaha
- **NPWP:** Nomor Pokok Wajib Pajak

### **📞 Quick Reference**
- **Login:** `http://localhost/ksp_peb/login.php`
- **Register Member:** `http://localhost/ksp_peb/register.php`
- **Register Cooperative:** `http://localhost/ksp_peb/register_cooperative.php`
- **Dashboard:** `http://localhost/ksp_peb/dashboard.php`

### **🔧 Technical Specifications**
- **Backend:** PHP 8.0+
- **Database:** MySQL 8.0+
- **Frontend:** Bootstrap 5.1.3
- **API:** RESTful API
- **Authentication:** Session-based

---

## 📝 **Notes**

### **📌 Important Notes**
- **Data Backup:** Backup data secara teratur
- **Password Security:** Gunakan password yang kuat
- **User Training:** Training pengguna secara berkala
- **Compliance:** Patuh pada regulasi yang berlaku
- **Security:** Jaga kerahasiaan data

### **⚠️ Disclaimer**
- **Data Accuracy:** User bertanggung jawab atas data yang dimasukkan
- **System Availability:** System tidak selalu tersedia 24/7
- **Data Loss:** Backup data secara teratur untuk menghindari kehilangan data
- **Security:** User bertanggung jawab atas keamanan akun masing-masing

---

## 🔄 **Update Information**

### **📅 Update Frequency**
- **Daily:** Bug fixes dan minor updates
- **Weekly:** New features dan improvements
- **Monthly:** Major updates dan new modules
- **Quarterly:** System maintenance dan optimization

### **📢 Update Notifications**
- **Email:** Notifikasi update via email
- **Dashboard:** Notifikasi di dashboard
- **SMS:** Notifikasi penting via SMS
- **In-App:** Notifikasi dalam aplikasi

---

## 🎯 **Contact Information**

### **📞 Development Team**
- **Lead Developer:** [Nama Lead Developer]
- **Backend Developer:** [Nama Backend Developer]
- **Frontend Developer:** [Nama Frontend Developer]
- **Database Admin:** [Nama Database Admin]

### **📧 Support Team**
- **System Admin:** [Nama System Admin]
- **User Support:** [Nama User Support]
- **Technical Support:** [Nama Technical Support]
- **Business Analyst:** [Nama Business Analyst]

---

## 📝 **Last Updated**

**Tanggal:** 2026-02-04
**Waktu:** 17:46 WIB
**Version:** v1.0.0
**Update:** Initial release dengan fitur lengkap

---

**📚 Panduan ini akan terupdate secara otomatis seiring dengan pengembangan aplikasi. Check kembali untuk versi terbaru!** 🚀
