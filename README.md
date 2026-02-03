# Aplikasi Koperasi

Aplikasi manajemen koperasi modern dengan fitur lengkap untuk mengelola simpanan, pinjaman, dan operasional koperasi.

## Fitur Utama

- Manajemen anggota koperasi
- Simpanan dan penarikan dana
- Pengajuan dan pengelolaan pinjaman
- Laporan keuangan
- Notifikasi otomatis
- Multi-role (anggota, pengurus, pengawas)

## Persyaratan Sistem

- Python 3.8+
- PostgreSQL 12+ atau SQLite3
- Kafka/RabbitMQ (untuk notifikasi)

## Instalasi

1. Clone repositori ini
2. Buat virtual environment:
   ```bash
   python -m venv venv
   source venv/bin/activate  # Linux/Mac
   # atau
   .\venv\Scripts\activate  # Windows
   ```
3. Install dependencies:
   ```bash
   pip install -r requirements.txt
   ```
4. Salin file konfigurasi:
   ```bash
   cp config/.env.example config/.env
   ```
5. Sesuaikan pengaturan di `config/.env` sesuai kebutuhan

## Menjalankan Aplikasi

```bash
# Mode pengembangan
export FLASK_APP=src/app.py
export FLASK_ENV=development
flask run
```

## Struktur Proyek

```
.
├── config/           # File konfigurasi
├── logs/             # File log aplikasi
├── src/              # Kode sumber aplikasi
├── temp/             # File sementara
├── tests/            # Test cases
├── .env.example      # Contoh file environment
├── requirements.txt  # Dependensi Python
└── README.md         # Dokumentasi
```

## Kontribusi

1. Fork repositori ini
2. Buat branch fitur (`git checkout -b fitur/namafitur`)
3. Commit perubahan (`git commit -am 'Menambahkan fitur'`)
4. Push ke branch (`git push origin fitur/namafitur`)
5. Buat Pull Request

## Lisensi

MIT
