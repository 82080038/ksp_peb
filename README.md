# Aplikasi Koperasi Simpan Pinjam Terpadu (KSP-Peb)

Aplikasi manajemen koperasi modern dengan fitur lengkap untuk mengelola simpanan, pinjaman, dan operasional koperasi.

## Fitur Utama

- Manajemen anggota koperasi
- Simpanan dan penarikan dana
- Pengajuan dan pengelolaan pinjaman
- Laporan keuangan
- Notifikasi otomatis
- Multi-role (anggota, pengurus, pengawas)
- Voting anggota
- Distribusi SHU
- E-commerce dasar

## Persyaratan Sistem

- PHP 7.4 atau lebih tinggi
- MySQL 5.7+ atau MariaDB 10.0+
- Apache dengan mod_rewrite (atau web server lain yang mendukung PHP)
- phpMyAdmin untuk manajemen database (opsional)

## Instalasi

1. Clone repositori ini:
   ```bash
   git clone https://github.com/82080038/ksp_peb.git
   cd ksp_peb
   ```

2. Salin file konfigurasi environment:
   ```bash
   cp config/.env.example config/.env
   ```

3. Edit file `config/.env` dan sesuaikan pengaturan database:
   ```
   DB_HOST=localhost
   DB_USER=root
   DB_PASS=your_password
   DB_NAME_PEOPLE=people_db
   DB_NAME_COOP=coop_db
   DB_NAME_ADDRESS=alamat_db
   ```

4. Buat database:
   - Buka phpMyAdmin di http://localhost/phpmyadmin
   - Jalankan file `sql/people_db.sql` untuk membuat database People DB
   - Jalankan file `sql/coop_db.sql` untuk membuat database Coop DB
   - Database Address DB (alamat_db) sudah ada

5. Pastikan Apache dikonfigurasi untuk melayani direktori `src/public` sebagai root dokumen, atau gunakan built-in PHP server untuk development.

## Menjalankan Aplikasi

### Menggunakan Apache
Konfigurasi virtual host Apache untuk menunjuk ke `src/public` sebagai DocumentRoot.

### Menggunakan Built-in PHP Server (Development)
```bash
cd src/public
php -S localhost:8000
```
Kemudian akses http://localhost:8000 di browser.

## Struktur Proyek

```
ksp_peb/
├── config/           # File konfigurasi
├── logs/             # File log aplikasi (jika ada)
├── src/              # Kode sumber aplikasi
│   ├── bootstrap.php # Bootstrap autoload
│   └── public/       # Public files (index.php, assets)
├── sql/              # SQL scripts untuk database
├── app/              # Classes dan views aplikasi
├── temp/             # File sementara
├── tests/            # Test cases (jika ada)
├── .env.example      # Contoh file environment
└── README.md         # Dokumentasi
```

## Pengembangan

- Error reporting aktif selama development (lihat config/init.php)
- Gunakan prepared statements untuk query database
- Implementasi JWT/session yang aman untuk autentikasi

## API Dokumentasi

API belum diimplementasi. Rencana: RESTful API v1 untuk autentikasi, user management, simpanan, pinjaman, dll.

## Kontribusi

1. Fork repositori ini
2. Buat branch fitur (`git checkout -b fitur/namafitur`)
3. Commit perubahan (`git commit -am 'Menambahkan fitur'`)
4. Push ke branch (`git push origin fitur/namafitur`)
5. Buat Pull Request

## Lisensi

MIT
