<?php
// Konfigurasi Bahasa Indonesia untuk Aplikasi Koperasi
// File ini mengkonsolidasikan semua teks dan pesan dalam bahasa Indonesia

return [
    // General Messages
    'loading' => 'Memuat...',
    'error' => 'Terjadi kesalahan',
    'success' => 'Berhasil',
    'confirm' => 'Apakah Anda yakin?',
    'cancel' => 'Batal',
    'save' => 'Simpan',
    'delete' => 'Hapus',
    'edit' => 'Ubah',
    'view' => 'Lihat',
    'add' => 'Tambah',
    'search' => 'Cari',
    'filter' => 'Filter',
    'export' => 'Ekspor',
    'import' => 'Impor',
    'refresh' => 'Refresh',
    'close' => 'Tutup',
    'back' => 'Kembali',
    'next' => 'Lanjut',
    'previous' => 'Sebelumnya',
    
    // Navigation & Menu
    'dashboard' => 'Dashboard',
    'home' => 'Beranda',
    'members' => 'Anggota',
    'savings' => 'Simpanan',
    'loans' => 'Pinjaman',
    'accounting' => 'Akuntansi',
    'reports' => 'Laporan',
    'voting' => 'Voting',
    'profile' => 'Profil',
    'help' => 'Bantuan',
    'logout' => 'Logout',
    'login' => 'Login',
    'register' => 'Daftar',
    
    // Admin Menu
    'admin_tools' => 'Admin Tools',
    'user_management' => 'Manajemen User',
    'role_management' => 'Manajemen Role',
    'system_settings' => 'Pengaturan Sistem',
    'audit_log' => 'Audit Log',
    'backup_restore' => 'Backup & Restore',
    'notifications' => 'Notifikasi',
    'administration' => 'Administrasi',
    
    // User Menu
    'my_profile' => 'Profil Saya',
    'my_account' => 'Akun Saya',
    'change_password' => 'Ganti Password',
    
    // Form Labels
    'name' => 'Nama',
    'full_name' => 'Nama Lengkap',
    'email' => 'Email',
    'phone' => 'Phone',
    'address' => 'Alamat',
    'nik' => 'NIK',
    'birth_place' => 'Tempat Lahir',
    'birth_date' => 'Tanggal Lahir',
    'gender' => 'Jenis Kelamin',
    'male' => 'Laki-laki',
    'female' => 'Perempuan',
    'current_password' => 'Password Saat Ini',
    'new_password' => 'Password Baru',
    'confirm_password' => 'Konfirmasi Password Baru',
    'status' => 'Status',
    'registration_date' => 'Tanggal Daftar',
    
    // Status Values
    'active' => 'Aktif',
    'inactive' => 'Tidak Aktif',
    'blacklist' => 'Blacklist',
    'pending' => 'Menunggu',
    'approved' => 'Disetujui',
    'rejected' => 'Ditolak',
    
    // Messages & Notifications
    'login_success' => 'Login berhasil! Mengarahkan ke dashboard...',
    'login_failed' => 'Login gagal',
    'logout_success' => 'Logout berhasil',
    'register_success' => 'Pendaftaran berhasil! Mengarahkan ke halaman login...',
    'register_failed' => 'Pendaftaran gagal',
    'save_success' => 'Data berhasil disimpan',
    'save_failed' => 'Gagal menyimpan data',
    'update_success' => 'Data berhasil diperbarui',
    'update_failed' => 'Gagal memperbarui data',
    'delete_success' => 'Data berhasil dihapus',
    'delete_failed' => 'Gagal menghapus data',
    'load_failed' => 'Gagal memuat data',
    'export_success' => 'Data berhasil diekspor',
    'export_failed' => 'Gagal mengekspor data',
    'import_success' => 'Data berhasil diimpor',
    'import_failed' => 'Gagal mengimpor data',
    
    // Dashboard Messages
    'dashboard_refreshed' => 'Data dashboard diperbarui',
    'dashboard_exported' => 'Dashboard berhasil diekspor',
    'dashboard_error' => 'Kesalahan memperbarui dashboard',
    'welcome_message' => 'Selamat datang',
    'total_members' => 'Total Anggota',
    'total_savings' => 'Total Simpanan',
    'total_loans' => 'Total Pinjaman',
    'shu_this_year' => 'SHU Tahun Ini',
    'recent_activities' => 'Aktivitas Terkini',
    'system_status' => 'Status Sistem',
    'quick_actions' => 'Aksi Cepat',
    
    // Member Management
    'add_member' => 'Tambah Anggota',
    'edit_member' => 'Ubah Anggota',
    'delete_member' => 'Hapus Anggota',
    'view_member' => 'Lihat Anggota',
    'member_details' => 'Detail Anggota',
    'personal_info' => 'Informasi Pribadi',
    'membership_info' => 'Informasi Keanggotaan',
    'no_members_found' => 'Tidak ada data anggota',
    'member_added' => 'Anggota berhasil ditambah',
    'member_updated' => 'Anggota berhasil diperbarui',
    'member_deleted' => 'Anggota berhasil dihapus',
    'member_status_changed' => 'Status anggota berhasil diubah',
    'select_member_first' => 'Pilih anggota terlebih dahulu',
    
    // Bulk Actions
    'bulk_edit' => 'Edit Massal',
    'bulk_delete' => 'Hapus Massal',
    'bulk_toggle_status' => 'Ubah Status Massal',
    'bulk_actions' => 'Aksi Massal',
    'select_all' => 'Pilih Semua',
    'selected_count' => 'dipilih',
    'bulk_edit_coming_soon' => 'Fitur edit massal segera hadir',
    'bulk_delete_coming_soon' => 'Fitur hapus massal segera hadir',
    'bulk_toggle_coming_soon' => 'Fitur toggle status massal segera hadir',
    
    // Validation Messages
    'required_field' => 'Field ini wajib diisi',
    'invalid_email' => 'Format email tidak valid',
    'invalid_phone' => 'Format phone tidak valid',
    'min_length' => 'Minimal %d karakter',
    'max_length' => 'Maksimal %d karakter',
    'password_mismatch' => 'Password tidak cocok',
    'email_exists' => 'Email sudah terdaftar',
    'nik_exists' => 'NIK sudah terdaftar',
    
    // System Messages
    'session_expired' => 'Sesi telah berakhir, silakan login kembali',
    'access_denied' => 'Akses ditolak',
    'page_not_found' => 'Halaman tidak ditemukan',
    'server_error' => 'Kesalahan server',
    'network_error' => 'Kesalahan jaringan',
    'try_again' => 'Silakan coba lagi',
    
    // Tooltips
    'tooltip_edit' => 'Ubah',
    'tooltip_view' => 'Lihat',
    'tooltip_delete' => 'Hapus',
    'tooltip_toggle_status' => 'Ubah Status',
    'tooltip_refresh' => 'Refresh',
    'tooltip_export' => 'Ekspor',
    'tooltip_import' => 'Impor',
    'tooltip_search' => 'Cari',
    'tooltip_filter' => 'Filter',
    
    // Confirmation Messages
    'confirm_delete' => 'Apakah Anda yakin ingin menghapus data ini?',
    'confirm_logout' => 'Apakah Anda yakin ingin logout?',
    'confirm_delete_member' => 'Apakah Anda yakin ingin menghapus anggota ini?',
    'confirm_bulk_delete' => 'Apakah Anda yakin ingin menghapus data yang dipilih?',
    
    // Loading States
    'loading_data' => 'Memuat data...',
    'saving_data' => 'Menyimpan data...',
    'deleting_data' => 'Menghapus data...',
    'updating_data' => 'Memperbarui data...',
    'processing_request' => 'Memproses permintaan...',
    
    // Error Messages
    'error_loading_page' => 'Kesalahan memuat halaman',
    'error_loading_data' => 'Kesalahan memuat data',
    'error_saving_data' => 'Kesalahan menyimpan data',
    'error_deleting_data' => 'Kesalahan menghapus data',
    'error_updating_data' => 'Kesalahan memperbarui data',
    'ajax_error' => 'Kesalahan AJAX',
    
    // Success Messages
    'operation_success' => 'Operasi berhasil',
    'data_saved' => 'Data berhasil disimpan',
    'data_updated' => 'Data berhasil diperbarui',
    'data_deleted' => 'Data berhasil dihapus',
    'data_exported' => 'Data berhasil diekspor',
    'data_imported' => 'Data berhasil diimpor',
    
    // Info Messages
    'coming_soon' => 'Segera hadir',
    'under_development' => 'Dalam pengembangan',
    'feature_not_available' => 'Fitur tidak tersedia',
    
    // Date & Time
    'today' => 'Hari Ini',
    'yesterday' => 'Kemarin',
    'this_week' => 'Minggu Ini',
    'this_month' => 'Bulan Ini',
    'this_year' => 'Tahun Ini',
    'last_updated' => 'Terakhir Diperbarui',
    
    // File Operations
    'file_uploaded' => 'File berhasil diunggah',
    'file_upload_failed' => 'Gagal mengunggah file',
    'file_downloaded' => 'File berhasil diunduh',
    'file_download_failed' => 'Gagal mengunduh file',
    'invalid_file_type' => 'Tipe file tidak valid',
    'file_too_large' => 'File terlalu besar',
    
    // Search & Filter
    'search_placeholder' => 'Cari...',
    'filter_by_status' => 'Filter berdasarkan status',
    'filter_by_date' => 'Filter berdasarkan tanggal',
    'sort_by' => 'Urutkan berdasarkan',
    'sort_asc' => 'A-Z',
    'sort_desc' => 'Z-A',
    'clear_filters' => 'Hapus Filter',
    
    // Pagination
    'showing' => 'Menampilkan',
    'of' => 'dari',
    'entries' => 'data',
    'per_page' => 'per halaman',
    'first_page' => 'Halaman pertama',
    'last_page' => 'Halaman terakhir',
    
    // Tables
    'no_data_available' => 'Tidak ada data tersedia',
    'no_records_found' => 'Tidak ada record ditemukan',
    'empty_table' => 'Tabel kosong',
    
    // Forms
    'form_required' => 'Field dengan tanda * wajib diisi',
    'form_optional' => 'Field opsional',
    'form_submit' => 'Kirim',
    'form_reset' => 'Reset',
    'form_cancel' => 'Batal',
    
    // Modals
    'modal_close' => 'Tutup',
    'modal_save' => 'Simpan',
    'modal_cancel' => 'Batal',
    'modal_confirm' => 'Konfirmasi',
    
    // Alerts
    'alert_success' => 'Sukses',
    'alert_error' => 'Error',
    'alert_warning' => 'Peringatan',
    'alert_info' => 'Informasi',
    
    // Buttons
    'btn_add_new' => 'Tambah Baru',
    'btn_save_changes' => 'Simpan Perubahan',
    'btn_cancel' => 'Batal',
    'btn_close' => 'Tutup',
    'btn_ok' => 'OK',
    'btn_yes' => 'Ya',
    'btn_no' => 'Tidak',
    
    // Cooperative Specific
    'cooperative_name' => 'Nama Koperasi',
    'cooperative_address' => 'Alamat Koperasi',
    'cooperative_phone' => 'Telepon Koperasi',
    'cooperative_email' => 'Email Koperasi',
    'member_number' => 'Nomor Anggota',
    'member_since' => 'Anggota Sejak',
    'share_capital' => 'Modal Saham',
    'mandatory_savings' => 'Simpanan Wajib',
    'voluntary_savings' => 'Simpanan Sukarela',
    'loan_amount' => 'Jumlah Pinjaman',
    'loan_interest' => 'Bunga Pinjaman',
    'loan_term' => 'Jangka Waktu',
    'monthly_installment' => 'Angsuran Bulanan',
    
    // Reports
    'financial_report' => 'Laporan Keuangan',
    'member_report' => 'Laporan Anggota',
    'savings_report' => 'Laporan Simpanan',
    'loan_report' => 'Laporan Pinjaman',
    'transaction_report' => 'Laporan Transaksi',
    'generate_report' => 'Buat Laporan',
    'download_report' => 'Unduh Laporan',
    
    // Settings
    'general_settings' => 'Pengaturan Umum',
    'security_settings' => 'Pengaturan Keamanan',
    'notification_settings' => 'Pengaturan Notifikasi',
    'backup_settings' => 'Pengaturan Backup',
    'email_settings' => 'Pengaturan Email',
    'system_preferences' => 'Preferensi Sistem',
    
    // Security
    'strong_password' => 'Password harus kuat',
    'password_changed' => 'Password berhasil diubah',
    'password_change_failed' => 'Gagal mengubah password',
    'two_factor_enabled' => 'Two Factor Authentication diaktifkan',
    'two_factor_disabled' => 'Two Factor Authentication dinonaktifkan',
    
    // Notifications
    'notification_sent' => 'Notifikasi terkirim',
    'notification_failed' => 'Gagal mengirim notifikasi',
    'mark_as_read' => 'Tandai sebagai dibaca',
    'mark_as_unread' => 'Tandai sebagai belum dibaca',
    'delete_notification' => 'Hapus notifikasi',
    
    // Help & Support
    'user_guide' => 'Panduan Pengguna',
    'video_tutorial' => 'Video Tutorial',
    'faq' => 'FAQ',
    'contact_support' => 'Hubungi Support',
    'send_feedback' => 'Kirim Feedback',
    'report_issue' => 'Laporkan Masalah',
    
    // Maintenance
    'maintenance_mode' => 'Mode Pemeliharaan',
    'system_maintenance' => 'Sistem sedang dalam pemeliharaan',
    'back_soon' => 'Akan kembali segera',
    'contact_admin' => 'Hubungi administrator',
    
    // Emergency
    'emergency_contact' => 'Kontak Darurat',
    'report_emergency' => 'Laporkan Keadaan Darurat',
    'system_down' => 'Sistem Down',
    'emergency_procedures' => 'Prosedur Darurat'
];
