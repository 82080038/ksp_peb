# Rencana Aplikasi Koperasi Terpadu

Ringkasan: Menyusun rancangan aplikasi koperasi dengan autentikasi multi-aktor, akuntansi, laporan, voting, SHU, dan kontrol akses berbasis tiga database terpisah (orang, alamat, aplikasi koperasi), terinspirasi dari DOKUMENTASI_POLRES.md.

## Tujuan & Prinsip
- Fokus fitur awal: register/login (anggota, pengurus, pengawas, koperasi), RBAC, akuntansi dasar, laporan inti, voting anggota, perhitungan/pembagian SHU.
- Arsitektur 3 database: identitas orang (people DB), alamat & geografi (address DB), domain koperasi (coop DB) dengan integritas referensial melalui service layer.
- Keamanan: hashing (bcrypt/argon2), RBAC granular, audit trail, approval flow untuk konflik kepentingan (pengurus bisa multi peran), pemisahan data sensitif.

## Cakupan Fitur
1) Autentikasi & Registrasi
- Registrasi pengguna (anggota, pengurus, pengawas, calon koperasi/tenant) + KYC dasar.
- Login/password + opsi MFA; session/JWT. Reset password & verifikasi email/WA.
- Multi-tenant koperasi (jika diperlukan): namespace per koperasi dalam coop DB.

2) Manajemen Pengguna & Role
- Role: Super Admin, Admin/Pengurus, Pengawas (read/approve), Anggota, Tamu/Calon Anggota.
- Multiple roles per user (pengurus juga investor/agen/pembeli). 
- RBAC berbasis permission set; audit log aktivitas; approval khusus jika konflik kepentingan.

3) Data Inti Anggota
- Profil anggota terhubung ke People DB; status keanggotaan; unggah dokumen; riwayat status.
- Pengurus & pengawas sebagai entitas terpisah di coop DB, referensi user & periode jabatan.

4) Akuntansi Koperasi
- COA, jurnal umum, buku besar, neraca saldo, laporan keuangan dasar (Neraca, Laba Rugi, Arus Kas).
- Integrasi modul simpanan, pinjaman, penjualan/SHU ke jurnal otomatis (posting terkontrol).

5) Simpanan & Pinjaman
- Jenis simpanan (pokok, wajib, sukarela); transaksi setoran/penarikan; saldo per anggota.
- Pengajuan pinjaman, approval berjenjang, penjadwalan angsuran, denda & bunga, pembayaran angsuran.

6) SHU & Distribusi
- Perhitungan SHU dari transaksi & partisipasi; konfigurasi proporsi sesuai AD/ART.
- Distribusi ke anggota & investor (jika ada); status pembayaran; bukti bayar.

7) Voting & Rapat
- Modul voting untuk rapat anggota (agenda, opsi, jatah suara per role/anggota, periode voting, hasil & audit).
- Notulen & keputusan rapat; daftar hadir.

8) Laporan
- Laporan anggota, simpanan, pinjaman, SHU, jurnal/GL, neraca, laba rugi, arus kas.
- Ekspor PDF/Excel; dashboard ringkas per role.

9) E-commerce/Penjualan (minimal untuk SHU & transaksi)
- Katalog produk/jasa koperasi, order dasar (anggota/umum), pembayaran manual/PG (rencana), stok minimal.
- Pencatatan penjualan agen/reseller dengan approval khusus jika agen adalah pengurus/pengawas.

10) Notifikasi & Pengingat
- Pengingat jatuh tempo angsuran/simpanan wajib; hasil voting; distribusi SHU.

## Arsitektur Teknis (tingkat tinggi)
- Frontend: HTML5/CSS/JS (Bootstrap/jQuery) atau opsi SPA ringan; PWA opsional.
- Backend: PHP (native atau framework ringan) + RESTful API v1 (JSON); session/JWT.
- Integrasi eksternal tahap lanjut: payment gateway (Midtrans/Xendit), ongkir (RajaOngkir), WA/SMS, email SMTP.

## Desain Database (3 DB terpisah)
1) People DB (identitas)
- users (id, nama, email, phone, password_hash, status, created_at)
- user_roles (user_id, role_id)
- roles, permissions, role_permissions
- identities (NIK, dokumen, tanggal_lahir, foto_path)

2) Address DB (alamat & geo)
- countries, provinces, cities, districts, villages
- addresses (id, line1, line2, postal_code, geo_lat, geo_lng)
- user_addresses (user_id -> addresses)

3) Coop DB (domain koperasi)
- anggota (id, user_id, status_keanggotaan, nomor_anggota)
- pengurus, pengawas (user_id, jabatan, periode)
- simpanan_types, simpanan_transactions
- pinjaman, pinjaman_angsuran
- chart_of_accounts, journal_entries, journal_entry_details, general_ledger
- shu_distributions, member_shu
- products, orders, order_details, agent_sales (minimal set)
- votes (agenda, opsi), vote_ballots (user_id, pilihan, timestamp)
- audit_logs, notifications, configs

Relasi lintas DB via service layer dengan kunci natural (user_uuid) dan caching referensi (materialized key tables) di coop DB bila perlu.

## Tahapan Implementasi
- Fase 0 (Persiapan): Finalisasi requirement & model data 3-DB; definisi role/permission; desain API; strategi deployment & backup.
- Fase 1 (MVP 8-10 minggu):
  - Auth (register/login), RBAC, manajemen user/anggota/pengurus/pengawas.
  - Simpanan & pinjaman dasar + posting jurnal otomatis minimal.
  - Akuntansi dasar (COA, jurnal, GL, laporan Neraca/Laba Rugi sederhana).
  - Voting rapat anggota sederhana; laporan inti; notifikasi dasar.
  - Struktur 3 DB & migrasi awal; audit log.
- Fase 2 (Operasional 8-10 minggu):
  - Penjualan/agen sederhana, integrasi payment gateway tahap awal.
  - Pengingat jatuh tempo, denda/bunga otomatis, dashboards role-based.
  - SHU kalkulasi & distribusi pembayaran; ekspor laporan; approval konflik kepentingan.
- Fase 3 (Lanjutan 6-8 minggu):
  - Integrasi ongkir/kurir, WA/SMS; PWA/mobile shell.
  - Penyempurnaan akuntansi (penyesuaian, depresiasi aset jika diperlukan), perpajakan dasar.
  - Konsinyasi/komisi agen lebih lengkap; BI ringan.

## Keluaran & Artefak
- Spesifikasi API v1 (auth, user/role, anggota, simpanan, pinjaman, jurnal, SHU, voting, laporan).
- Skema migrasi SQL untuk 3 DB.
- Matriks permission per role + approval rules (konflik kepentingan).
- Wireframe dashboard per role (anggota, pengurus, pengawas, admin koperasi).
- Rencana backup & monitoring (dump harian, log audit, health checks).

## Edge Case & Kebijakan Bisnis
- Anggota meninggal/keluar/dipecat: status anggota ditandai (inactive/terminated/deceased); blokir login; freeze transaksi baru; trigger workflow penyelesaian (tutup pinjaman, tarik simpanan, distribusi SHU pro-rata, pindah waris jika diatur); catat di audit log dan laporan khusus.
- Buku besar: semua peristiwa di atas diposting ke jurnal/GL (penutupan simpanan, pencadangan piutang macet, reverse accrual bunga, pengakuan kewajiban waris/pengembalian); gunakan nomor jurnal referensial per kasus.
- Telat bayar: scheduler hitung denda otomatis berdasar konfigurasi; reminder notifikasi; flag kolektibilitas; posting bunga/denda ke GL; block layanan tertentu bila melewati grace period; laporan aging.
- Juru bayar memotong gaji (payroll deduction): modul otorisasi potong gaji per anggota; unggah surat kuasa; batch posting simpanan pokok/wajib dan cicilan pinjaman; rekonsiliasi slip gaji; audit trail juru bayar; jurnal otomatis (debit piutang gaji/kas, kredit simpanan atau pokok pinjaman).
- Peminjam hampir pensiun: aturan usia/masa dinas pada engine eligibility; batas sisa tenor ≤ sisa masa kerja; kalkulasi maximum exposure; rambu approval khusus; risk flag di scoring dan laporan kredit.

## Modularisasi Jenis Koperasi
- Model tipe koperasi: tabel `coop_types` (mis. simpan pinjam, jasa, serba usaha, pemasaran, produsen); relasi koperasi-tenant ke tipe; memungkinkan multi-tipe per koperasi (many-to-many) untuk hybrid.
- Konfigurasi per tipe: template modul & permission bundle (contoh: tipe jasa aktifkan modul layanan/pelayanan, tipe serba usaha aktifkan katalog multi-divisi, tipe simpan pinjam wajibkan modul simpanan/pinjaman/SHU).
- Bundling produk/lini usaha: `business_units` per koperasi (mis. toko ritel, jasa logistik, layanan keuangan mikro); setiap unit punya COA segment, produk, dan workflow sendiri, namun konsolidasi ke GL & laporan gabungan.
- Aktivasi modul dinamis: feature flag per koperasi + per unit; migrasi data modular; form builder ringan untuk field khusus per tipe.
- Laporan & buku besar: segmentasi per tipe/ unit usaha (dimensi analitik di GL); laporan konsolidasi dan per-segmen; pembagian SHU bisa berbasis kontribusi per unit.

## API & Flow Utama (ringkas)
- Auth & User: `POST /auth/register` (people DB + coop DB link), `POST /auth/login` (JWT/session), `POST /auth/forgot/reset`, `POST /auth/mfa/verify`, `GET /me`, `POST /roles/assign` (RBAC + audit).
- Anggota/Pengurus/Pengawas: `GET/POST/PUT /anggota`, `POST /anggota/{id}/status` (aktif/nonaktif/keluar/meninggal/dipecat), `GET/POST pengurus`, `GET/POST pengawas` (periode jabatan). Flow: create user → map role → buat anggota → assign pengurus/pengawas jika perlu.
- Simpanan: `GET/POST /simpanan/types`, `POST /simpanan/transactions` (setor/tarik), `GET /simpanan/saldo/{anggota}`; flow posting jurnal otomatis (debit kas/bank, kredit simpanan).
- Pinjaman: `POST /pinjaman` (apply), `POST /pinjaman/{id}/approve|reject`, `POST /pinjaman/{id}/disburse`, `POST /pinjaman/{id}/angsuran` (bayar), `POST /pinjaman/{id}/denda` (telat), `POST /pinjaman/{id}/reschedule`; flow: scoring + approval berjenjang → pencairan → jadwal angsuran → pembayaran → aging/denda.
- SHU: `POST /shu/run` (per periode), `GET /shu/preview`, `POST /shu/distribute` (anggota & investor), `POST /shu/pay/{id}`; flow: tarik data laba bersih + kontribusi anggota → kalkulasi proporsi → jurnal distribusi → pembayaran.
- Voting/Rapat: `POST /rapat` (agenda), `POST /rapat/{id}/voting` (setup opsi & jatah suara), `POST /voting/{id}/ballot`, `GET /voting/{id}/result`; audit trail + kontrol waktu.
- Laporan & GL: `GET /reports/neraca|labarugi|aruskas|aging|simpanan|pinjaman|shu`, `GET /gl`, `POST /journal` (manual), `POST /journal/adjustment`; semua transaksi modul memicu posting otomatis ke journal/GL dengan reference_id & source_module.
- Notifikasi & Scheduler: cron/queue untuk jatuh tempo, denda otomatis, reminder voting, backup; `POST /notifications/send` (templat WA/SMS/email opsional).

## Flow Register & Alamat
- Kondisi awal tanpa koperasi: UI hanya menampilkan tab “Register Koperasi”; setelah koperasi pertama dibuat, tab “Register User” muncul berdampingan.
- Register koperasi: form wajib (nama koperasi, jenis/tipe, nomor badan hukum/akta, tanggal pendirian, NPWP, alamat legal, kontak resmi, logo, periode tahun buku, konfigurasi awal simpanan wajib/pokok, bunga pinjaman, denda telat, periode SHU). Setelah submit: buat record di coop DB, tenant config, default COA, role Super Admin pengusul.
- Register user (setelah ada koperasi): pilih koperasi (atau terikat tenant), isi identitas dasar (nama, email/HP), role awal (anggota/pengurus/pengawas/pegawai), verifikasi OTP/email; link ke people DB, lalu ke coop DB.
- Alamat combo hirarkis: dropdown berantai province → city/kabupaten → district/kecamatan → village/kelurahan; detail alamat (jalan, RT/RW, kode pos, koordinat opsional) baru aktif setelah village dipilih. API: `GET /geo/provinces`, `GET /geo/cities?province_id=`, `GET /geo/districts?city_id=`, `GET /geo/villages?district_id=`; simpan ke address DB dan referensi ke user/koperasi.
- Prefill alamat untuk personil yang daftar mandiri: combo alamat user mengisi otomatis sampai tingkat kabupaten; koperasi memakai referensi kabupaten yang sama, sementara detail bawah (kecamatan/kelurahan/jalan) diisi user.
- NIK wajib untuk setiap user (validasi unik & format) saat registrasi; untuk personil juga wajib NRP/NIP.
- Identitas wajib: nomor HP, email (unik), jenis kelamin, suku, agama, pekerjaan, status kawin; referensi ke tabel master (religions, ethnicities, occupations); verifikasi email/HP via OTP.
- Dokumen identitas: unggah foto/scan KTP dan foto diri (selfie) wajib; simpan path di `identities` atau `attachments` (ref_type=user); opsional verifikasi wajah (face match KTP) dan cek keabsahan NIK.

## Jenis Koperasi Saat Register (Multi-tipe & Aktivasi Modul)
- Jenis yang dapat dipilih (multi-select): Simpan Pinjam, Konsumsi, Produksi, Pemasaran, Jasa, Serba Usaha (kombinasi/hibdrid diperbolehkan).
- Aktivasi modul otomatis per jenis (disimpan di `coop_type_modules`):
  - Simpan Pinjam: anggota, simpanan, pinjaman, SHU, akuntansi, voting.
  - Pemasaran/Konsumsi: e-commerce/produk, order, pengiriman/penjemputan, POS, agen (opsional), biaya operasional.
  - Produksi: BOM, work_orders, inventory movements, HPP produksi.
  - Jasa: service_catalog, service_orders, SLA/ticketing.
  - Serba Usaha: aktifkan beberapa jenis sekaligus via business_units.
- UI: checkbox multi-jenis saat register koperasi; setelah submit, backend menyalakan modul sesuai bundel dan membuat feature flags terkait.
- Setelah koperasi terdaftar, hanya admin/super admin yang boleh menambah/mengaktifkan modul jenis koperasi; modul dikelola terpisah dan akan terintegrasi ke aplikasi setelah diaktifkan (feature flags + provisioning schema/endpoint terkait).
- Aktivasi modul dicatat di audit log (oleh siapa, kapan, modul apa); seluruh transaksi modul tercatat dan dapat dilihat sebelum SHU dibagikan, sehingga kontribusi modul tetap terlapor walau belum ada distribusi SHU.

## Aturan Perkoperasian (ringkasan referensi UU 25/1992)
- Pendirian & keanggotaan: koperasi primer minimal 20 orang (WNI), tujuan memenuhi kebutuhan anggota; perubahan AD/ART & kebijakan utama via Rapat Anggota (RA/RAT).
- Rapat Anggota Tahunan (RAT): wajib minimal 1x setahun; pengurus menyusun laporan tahunan (neraca, laba rugi/SHU, rencana kerja) max 1 bulan sebelum RAT; keputusan RAT mengesahkan laporan & pembagian SHU.
- Pengurus/Pengawas: pengurus mengelola, pengawas mengawasi dan lapor ke RAT; pengawas boleh minta audit akuntan publik; hasil pengawasan rahasia pihak ketiga kecuali RAT/otoritas.
- Modal koperasi: simpanan pokok (sekali), simpanan wajib (berkala), cadangan, hibah; modal pinjaman (anggota, koperasi lain, bank/lembaga, obligasi/surat utang); perlu pencatatan terpisah.
- SHU: dibagikan sesuai kontribusi dan keputusan RAT; sebagian jadi cadangan; distribusi ke anggota/investor (jika ada) sesuai proporsi AD/ART.
- Sanksi & tata kelola: pengawas dapat beri teguran/pemberhentian sementara pengurus; sanksi anggota diatur AD/ART.

### Referensi Tambahan (Permenkop/Pajak/Konsumen)
- RAT & pelaporan: Permenkop menekankan RAT sebelum batas tahun buku berikut (umumnya 6 bulan), laporan tahunan paling lambat 1 bulan sebelum RAT; rencana kerja dan RAPB wajib disahkan RAT.
- Pelindungan konsumen: patuhi UU Perlindungan Konsumen (kejujuran produk, garansi, pengaduan); untuk e-commerce gunakan kebijakan retur/garansi tertulis dan SLA layanan.
- Perpajakan koperasi: 
  - PPh Badan: perlakuan umum; koperasi simpan pinjam yang hanya melayani anggota dapat memiliki perlakuan khusus (cek regulasi terbaru) namun tetap lapor SPT Tahunan.
  - PPN: wajib jika PKP (omzet > batas PKP); transaksi simpan pinjam umumnya non-PPN, tapi penjualan barang/jasa kena PPN jika PKP.
  - Potput/PPh 21/23: berlaku untuk pembayaran jasa/barang tertentu; perlu bukti potong.
  - Rekening & bukti: nomor faktur/efaktur (jika PKP), bukti potong pajak, dicatat di modul pajak.

### Detail PPN/PKP & e-Faktur
- Threshold PKP: omzet tahunan > batas PKP (cek regulasi terbaru, historis 4,8M/Th); jika PKP, wajib terbitkan e-Faktur untuk transaksi kena PPN.
- e-Faktur: store `efaktur_number`, `efaktur_pdf_path` di `attachments`; validasi nomor seri faktur unik; status faktur (draft, approved, canceled); endpoint `POST /pajak/efaktur` untuk generate/attach.
- Mapping PPN: tentukan kategori barang/jasa kena/tidak kena PPN; jika PKP dan transaksi Kena PPN, hitung PPN 11% (atau sesuai tarif berlaku), pisahkan akun PPN keluaran/masukan.
- Potput PPh: pembayaran jasa tertentu (mis. sewa, jasa profesional) perlu PPh 23/21/4(2); wajib bukti potong dan pencatatan di modul pajak.

### SOP Pengaduan Konsumen
- Kanal pengaduan: form/ticketing + kontak WA/email; SLA respons awal (mis. 1x24 jam) dan resolusi (mis. 3 hari kerja) configurable per koperasi.
- Alur: pengaduan masuk → kategorisasi (produk/pengiriman/pembayaran/layanan) → assign petugas → update status (open/in progress/resolved/closed) → notifikasi ke pelapor → opsional survei kepuasan.
- Dokumen: simpan bukti (foto/nota) di `attachments`; jika terkait retur/refund, link ke order/return.
- Pelaporan: dashboard KPI (jumlah tiket, SLA compliance, waktu respon/resolusi, NPS/CSAT opsional).

### Verifikasi & Sertifikasi Dokumen (barcode/QR)
- QR memuat URL `/verify/doc/{id}` + hash; backend validasi hash + status dokumen.
- Sertifikasi tambahan: tandatangani hash dengan kunci privat (opsional) → lampirkan signature; endpoint `GET /verify/doc/{id}` bisa mengembalikan `signature` dan `public_key_id` untuk verifikasi offline.
- Offline fallback: tampilkan hash + checksum di dokumen; pengguna dapat cek manual di halaman verifikasi dengan memasukkan hash.

### Tambahan Model/Tabel untuk Integrasi DB
- Pajak/e-Faktur: tabel `tax_invoices` (id, ref_type, ref_id, efaktur_number, efaktur_pdf_path, status, issued_at, canceled_at); kolom pajak di `sales_orders`/`supplier_invoices` (ppn_amount, pph_amount, tax_codes).
- Konsumen/Pengaduan: gunakan `tickets` dengan kolom tambahan SLA (`sla_response_hours`, `sla_resolve_hours`), `due_at`, `resolved_at`, `category`, `channel`, `attachments`; relasi ke order/return optional (ref_type/ref_id).
- Risk flag: tambahkan kolom `risk_flag`, `risk_reason` di `anggota`; tabel `anggota_risk_history` (id, anggota_id, risk_flag, reason, changed_by, created_at).
- Simpanan wajib/config: tabel `coop_configs` atau gunakan `config` dengan key (simpanan_wajib_amount, due_day, bunga_pinjaman_default, denda_default); simpanan_types sudah ada untuk jenis simpanan.
- Signature/Key: tabel `signing_keys` (id, public_key, issuer, valid_from, valid_to, status); tabel `doc_signatures` (id, doc_id, ref_type, signature, signing_key_id, hash, created_at).

### Skema Detail (tipe data ringkas)
- `tax_invoices`: id (bigint PK), ref_type (enum), ref_id (bigint), efaktur_number (varchar unique), efaktur_pdf_path (varchar), status (enum draft/approved/canceled), issued_at (datetime), canceled_at (datetime null), created_at/updated_at.
- `sales_orders`/`supplier_invoices`: ppn_amount (decimal 18,2), pph_amount (decimal 18,2), tax_codes (json/varchar).
- `tickets`: sla_response_hours (int), sla_resolve_hours (int), due_at (datetime), resolved_at (datetime), category (enum), channel (enum: web/wa/email), attachments (json), ref_type/ref_id (nullable), status (enum), created_at/updated_at.
- `anggota`: risk_flag (enum: normal/attention/blacklist), risk_reason (text). `anggota_risk_history`: id (bigint PK), anggota_id FK, risk_flag, reason (text), changed_by FK users, created_at.
- `config/coop_configs`: key (varchar), value (json/varchar), scope (coop_id bigint), updated_at; contoh key: simpanan_wajib_amount (decimal), due_day (tinyint), bunga_pinjaman_default (decimal 5,2), denda_default (decimal 5,2).
- `signing_keys`: id (bigint PK), public_key (text), issuer (varchar), valid_from/valid_to (datetime), status (enum active/revoked), created_at. `doc_signatures`: id (bigint PK), doc_id (bigint), ref_type (enum), signature (text), signing_key_id FK, hash (varchar), created_at.

### Skema Inti Lengkap (tipe data ringkas)
- `users`: id BIGINT PK, name VARCHAR, email VARCHAR UNIQUE, phone VARCHAR UNIQUE, password_hash VARCHAR, status ENUM(active/inactive), created_at, updated_at.
- `identities`: id BIGINT PK, user_id FK, nik VARCHAR UNIQUE, tempat_lahir VARCHAR, tanggal_lahir DATE, agama_id FK, suku_id FK, pekerjaan_id FK, gender ENUM, foto_path VARCHAR, dokumen_path VARCHAR.
- `addresses`: id BIGINT PK, line1 VARCHAR, line2 VARCHAR, country/province/city/district/village IDs BIGINT, postal_code VARCHAR, lat/lng DECIMAL(10,6), created_at.
- `anggota`: id BIGINT PK, user_id FK, nomor_anggota VARCHAR UNIQUE, status_keanggotaan ENUM(draft/aktif/nonaktif/keluar/meninggal/dipecat), risk_flag ENUM(normal/attention/blacklist), risk_reason TEXT, alamat_id FK, created_at, updated_at.
- `anggota_status_history`: id BIGINT PK, anggota_id FK, status ENUM, reason TEXT, changed_by FK users, created_at.
- `pengurus`/`pengawas`: id BIGINT PK, user_id FK, jabatan VARCHAR, periode_mulai/akhir DATE, status ENUM(active/inactive), sk_path VARCHAR, created_at; history tabel pengurus_history (fields sama + changed_by, created_at).
- `roles`, `permissions`, `user_roles`, `role_permissions`: id BIGINT PK, name VARCHAR; join tables (user_id/role_id, role_id/permission_id) BIGINT FK.
- `simpanan_types`: id BIGINT PK, nama VARCHAR, jenis ENUM(pokok/wajib/sukarela), nominal DECIMAL(18,2) nullable, per_bulan BOOL, created_at.
- `simpanan_transactions`: id BIGINT PK, anggota_id FK, simpanan_type_id FK, jumlah DECIMAL(18,2), tanggal DATE, jenis_transaksi ENUM(setor/tarik), status ENUM(posted/void), created_at.
- `pinjaman`: id BIGINT PK, anggota_id FK, jumlah_pinjaman DECIMAL(18,2), bunga DECIMAL(5,2), jangka_waktu INT, tenor_satuan ENUM(bulan/hari), tujuan TEXT, status ENUM(draft/submitted/approved/rejected/disbursed/closed/writeoff), approved_by FK users nullable, created_at.
- `pinjaman_angsuran`: id BIGINT PK, pinjaman_id FK, angsuran_ke INT, jumlah DECIMAL(18,2), bunga DECIMAL(18,2), denda DECIMAL(18,2), tanggal_jatuh_tempo DATE, tanggal_bayar DATE NULL, status ENUM(pending/paid/overdue), created_at.
- `products`: id BIGINT PK, sku VARCHAR UNIQUE, nama VARCHAR, kategori_id FK, unit_id FK, harga_beli DECIMAL(18,2), harga_jual DECIMAL(18,2), batch_enabled BOOL, serial_enabled BOOL, expiry_enabled BOOL, status ENUM(active/inactive), created_at.
- `product_categories`: id BIGINT PK, nama VARCHAR, handling_type ENUM(perishable/durable), storage_requirements VARCHAR, shelf_life_days INT NULL.
- `product_units`: id BIGINT PK, nama VARCHAR, faktor DECIMAL(12,4), base_unit_id FK NULL.
- `inventory_locations`: id BIGINT PK, nama VARCHAR, jenis ENUM(gudang/toko), address_id FK, business_unit_id FK, status ENUM(active/inactive).
- `inventory_stocks`: id BIGINT PK, product_id FK, location_id FK, qty_on_hand DECIMAL(18,3), qty_reserved DECIMAL(18,3), updated_at.
- `inventory_movements`: id BIGINT PK, product_id FK, from_location_id FK NULL, to_location_id FK NULL, qty DECIMAL(18,3), tipe ENUM(in/out/transfer/adjust), reason VARCHAR, batch_no VARCHAR NULL, serial_no VARCHAR NULL, expiry_date DATE NULL, ref_type ENUM NULL, ref_id BIGINT NULL, created_by FK users, created_at.
- `purchase_orders`: id BIGINT PK, supplier_id FK, nomor_po VARCHAR UNIQUE, tanggal DATE, status ENUM(draft/approved/released/canceled), total DECIMAL(18,2), business_unit_id FK, created_at.
- `po_details`: id BIGINT PK, po_id FK, product_id FK, qty DECIMAL(18,3), harga DECIMAL(18,2), diskon DECIMAL(18,2) NULL, subtotal DECIMAL(18,2).
- `goods_receipts`: id BIGINT PK, po_id FK, nomor_gr VARCHAR UNIQUE, tanggal DATE, status ENUM(received/canceled), lokasi_id FK.
- `sales_orders`: id BIGINT PK, customer_id FK, nomor_so VARCHAR UNIQUE, tanggal DATE, status ENUM(draft/approved/fulfilled/paid/canceled), total DECIMAL(18,2), business_unit_id FK, lokasi_id_pengambilan FK NULL, metode_pembayaran ENUM(cash/bank_transfer/pg), ppn_amount DECIMAL(18,2) NULL, pph_amount DECIMAL(18,2) NULL.
- `sales_details`: id BIGINT PK, so_id FK, product_id FK, qty DECIMAL(18,3), harga DECIMAL(18,2), diskon DECIMAL(18,2) NULL, subtotal DECIMAL(18,2), batch_no VARCHAR NULL, expiry_date DATE NULL.
- `returns`: id BIGINT PK, ref_type ENUM(po/so), ref_id BIGINT, product_id FK, qty DECIMAL(18,3), reason TEXT, status ENUM(draft/received/approved/rejected), created_at.
- `agents`/`agent_sales`/`agent_commissions`: id BIGINT PK; agent_sales: nomor_transaksi VARCHAR, total_nilai DECIMAL, status_approval ENUM; commissions: total_komisi DECIMAL, status_pembayaran ENUM.
- `fixed_assets`: id BIGINT PK, kode_aset VARCHAR UNIQUE, nama_aset VARCHAR, kategori VARCHAR, nilai_perolehan DECIMAL(18,2), tanggal_perolehan DATE, metode_depresiasi ENUM(garis_lurus/saldo_menurun), umur_ekonomis INT, lokasi_id FK, assigned_to FK NULL, status ENUM(active/inactive), created_at.
- `asset_depreciations`: id BIGINT PK, asset_id FK, periode VARCHAR (YYYY-MM), nilai_depresiasi DECIMAL(18,2), nilai_buku_setelah DECIMAL(18,2), created_at.
- `asset_documents`: id BIGINT PK, asset_id FK, nama_file VARCHAR, path VARCHAR, tipe_dokumen VARCHAR, created_at.
- `journal_entries`: id BIGINT PK, tanggal DATE, nomor_jurnal VARCHAR UNIQUE, deskripsi TEXT, created_by FK users, created_at.
- `journal_entry_details`: id BIGINT PK, journal_entry_id FK, account_id FK, debit DECIMAL(18,2), kredit DECIMAL(18,2), created_at.
- `chart_of_accounts`: id BIGINT PK, kode_akun VARCHAR UNIQUE, nama_akun VARCHAR, kategori VARCHAR, parent_id FK NULL, saldo_awal DECIMAL(18,2), created_at.
- `general_ledger`: id BIGINT PK, account_id FK, tanggal DATE, debit DECIMAL(18,2), kredit DECIMAL(18,2), saldo DECIMAL(18,2), reference_type ENUM, reference_id BIGINT, created_at.
- `tax_invoices`: efaktur_number VARCHAR(50), efaktur_pdf_path VARCHAR(255), status ENUM.
- `tickets`: category VARCHAR(50) atau ENUM, channel ENUM, attachments JSON, status ENUM.
- `config`: id BIGINT PK, key VARCHAR, value JSON/VARCHAR, description TEXT, updated_at.
- `attachments`: id BIGINT PK, ref_type ENUM, ref_id BIGINT, file_path VARCHAR, caption VARCHAR, created_at.
- `signing_keys`: id BIGINT PK, public_key TEXT; `doc_signatures`: hash VARCHAR(191), signature TEXT.
- `pengurus`/`pengawas`: jabatan VARCHAR(100), status ENUM(active/inactive), sk_path VARCHAR(255).
- `risk_history`: reason TEXT, risk_flag ENUM(normal/attention/blacklist).
- `orders`/`order_details` (e-commerce): nomor_order VARCHAR(50-100), customer_type ENUM(member/non-member), status_order ENUM(pending/processed/shipped/completed/canceled), status_pembayaran ENUM(unpaid/paid/refund), alamat_pengiriman TEXT/VARCHAR(255), metode_pembayaran ENUM(cash/bank_transfer/pg/qris), qty DECIMAL(18,3), harga DECIMAL(18,2).
- `operational_costs`: kategori_biaya VARCHAR(100), deskripsi TEXT, jumlah DECIMAL(18,2), status approval ENUM.
- `warranties`/`warranty_claims`: periode_garansi VARCHAR(50), syarat_ketentuan TEXT, status klaim ENUM(open/in_review/approved/rejected/closed).
- `tickets` status ENUM(open/in_progress/resolved/closed); priority ENUM(low/medium/high/critical); channel ENUM(web/wa/email/offline).

### ERD Teks Ringkas
- People DB: users 1..1 identities; users 1..* user_roles -> roles; identities 1..* addresses (via user_addresses) jika multi alamat.
- Address DB: addresses terkait ke users, koperasi, inventory_locations (FK address_id).
- Coop DB: users -> anggota (1..1), anggota -> simpanan_transactions/pinjaman/pinjaman_angsuran; anggota -> risk_history; pengurus/pengawas mereferensi users.
- Produk/Inventory: products -> inventory_movements -> inventory_locations; sales_orders -> sales_details -> products; purchase_orders -> po_details -> products; returns -> products (via ref order/po).
- Keuangan: journal_entries -> journal_entry_details -> chart_of_accounts; general_ledger agregasi per akun; tax_invoices refer ke SO/Invoices; attachments/doc_signatures refer ke berbagai ref_type.
- Aset: fixed_assets -> asset_depreciations/asset_documents/asset_movements; assignments ke users/lokasi.

## Benchmark & Saran (konsolidasi praktik umum aplikasi koperasi)
- Mobile-first + PWA ringan untuk anggota/pengurus; offline queue untuk kasir/agen.
- Integrasi KYC pihak ketiga (dukcapil/e-KTP bila tersedia) untuk validasi NIK; face match opsional.
- Pembayaran & penagihan: payment gateway (QRIS/VA/e-wallet), auto-reconcile, reminder penagihan via WA/Email/SMS.
- Risk & scoring: plafon/DSCR, aging kolektibilitas, alert NPL; dashboard kredit.
- Pajak & kepatuhan: e-Faktur, SPT scheduler, pemisahan transaksi kena/tidak kena PPN; bukti potong otomatis.
- Keamanan: enkripsi at-rest kolom sensitif, TLS, rate limit, SOC/monitoring; opsi penerapan ISO 27001 best practice.
- Audit & SoD: role terpisah, audit trail lengkap, approval berjenjang; mode read-only untuk pengawas.
- Pelayanan anggota: tiket/SLA, retur/garansi jelas, notifikasi status; portal anggota menampilkan simpanan, pinjaman, SHU.
- Observabilitas: health check, APM/log/error rate; alert jika backup gagal atau job pajak/ekspor gagal.

### Panjang Kolom Per Tabel (guideline)
- `users`: name/email/phone VARCHAR(191), password_hash VARCHAR(191), status ENUM.
- `identities`: nik VARCHAR(32), nrp_nip VARCHAR(32), tempat_lahir VARCHAR(100), foto_path/dokumen_path VARCHAR(255), gender ENUM.
- `addresses`: line1/line2 VARCHAR(255), postal_code VARCHAR(20), lat/lng DECIMAL(10,6).
- `anggota`: nomor_anggota VARCHAR(50), risk_reason TEXT, status_keanggotaan/risk_flag ENUM.
- `roles/permissions`: name VARCHAR(100); join tables FK BIGINT.
- `simpanan_types`: nama VARCHAR(100), jenis ENUM, nominal DECIMAL(18,2).
- `simpanan_transactions`: jenis_transaksi ENUM, jumlah DECIMAL(18,2), tanggal DATE.
- `pinjaman`: tujuan TEXT, status ENUM, nomor_pinjam (jika ada) VARCHAR(50), bunga DECIMAL(5,2), jumlah_pinjaman DECIMAL(18,2).
- `pinjaman_angsuran`: status ENUM, angsuran_ke INT, jumlah/bunga/denda DECIMAL(18,2), tanggal DATE.
- `products`: sku VARCHAR(100), nama VARCHAR(191), harga_jual/beli DECIMAL(18,2), batch/serial/expiry flags BOOL.
- `product_categories`: nama VARCHAR(150), handling_type ENUM, storage_requirements VARCHAR(100).
- `inventory_locations`: nama VARCHAR(150), jenis ENUM, address_id FK.
- `inventory_movements`: reason VARCHAR(150), batch_no VARCHAR(100), serial_no VARCHAR(100), qty DECIMAL(18,3).
- `purchase_orders`/`sales_orders`: nomor_* VARCHAR(50-100), status ENUM, total DECIMAL(18,2), metode_pembayaran ENUM.
- `*_details`: qty DECIMAL(18,3), harga DECIMAL(18,2), diskon DECIMAL(18,2), subtotal DECIMAL(18,2).
- `returns`: reason TEXT, status ENUM.
- `agents/agent_sales/agent_commissions`: nomor_transaksi VARCHAR(100), status_approval ENUM, total DECIMAL(18,2).
- `fixed_assets`: kode_aset VARCHAR(100), nama_aset VARCHAR(191), kategori VARCHAR(100), metode_depresiasi ENUM, nilai_perolehan DECIMAL(18,2).
- `journal_entries`: nomor_jurnal VARCHAR(100), deskripsi TEXT; `journal_entry_details`: debit/kredit DECIMAL(18,2).
- `chart_of_accounts`: kode_akun VARCHAR(50), nama_akun VARCHAR(191), kategori VARCHAR(100).
- `tax_invoices`: efaktur_number VARCHAR(50), efaktur_pdf_path VARCHAR(255), status ENUM.
- `tickets`: category VARCHAR(50) atau ENUM, channel ENUM, attachments JSON, status ENUM.
- `config/coop_configs`: key VARCHAR(100), value JSON/VARCHAR(255), description TEXT.
- `attachments`: file_path VARCHAR(255), caption VARCHAR(255).
- `signing_keys`: issuer VARCHAR(100), public_key TEXT; `doc_signatures`: hash VARCHAR(191), signature TEXT.
- `pengurus`/`pengawas`: jabatan VARCHAR(100), status ENUM(active/inactive), sk_path VARCHAR(255).
- `risk_history`: reason TEXT, risk_flag ENUM(normal/attention/blacklist).
- `orders`/`order_details` (e-commerce): nomor_order VARCHAR(50-100), customer_type ENUM(member/non-member), status_order ENUM(pending/processed/shipped/completed/canceled), status_pembayaran ENUM(unpaid/paid/refund), alamat_pengiriman TEXT/VARCHAR(255), metode_pembayaran ENUM(cash/bank_transfer/pg/qris), qty DECIMAL(18,3), harga DECIMAL(18,2).
- `operational_costs`: kategori_biaya VARCHAR(100), deskripsi TEXT, jumlah DECIMAL(18,2), status approval ENUM.
- `warranties`/`warranty_claims`: periode_garansi VARCHAR(50), syarat_ketentuan TEXT, status klaim ENUM(open/in_review/approved/rejected/closed).
- `tickets` status ENUM(open/in_progress/resolved/closed); priority ENUM(low/medium/high/critical); channel ENUM(web/wa/email/offline).


### Ketahanan & Edge Case
- Anti-kerusakan data: CRC/checksum pada backup; migrasi dengan checksum & rollback; deteksi data korup via konsistensi (saldo vs GL, stok vs ledger).
- Rate limit & anti-abuse: batas OTP/login/registrasi, proteksi brute force, CAPTCHA opsional; monitoring anomali transaksi (nilai besar, jam aneh).
- Kehilangan kunci/secret: gunakan KMS dengan rotasi; prosedur recovery key (sealed, multi-person approval); simpan hash signature terpisah.
- Insider threat: audit log immutable (WORM) untuk aksi kritikal; dual control untuk pencairan besar/aktivasi modul; notifikasi pengawas untuk perubahan role/kunci.
- Kapasitas & failover: rencana scale (read replica, queue), health check & circuit breaker untuk layanan eksternal (OTP/PG/WA); retry idempotent.
- Legal/hold: mode freeze untuk sengketa/forensik (transaksi read-only, log tetap jalan); ekspor data forensik dengan hash.
- Chaos/DR drill: jadwal uji failover/restore dan simulasi kehilangan admin/koperasi nonaktif minimal 2x setahun.
- BCP/continuity: daftar kontak darurat (pengawas/otoritas/vendor), runbook pemulihan, dan channel komunikasi alternatif (email/WA/SMS) saat outage.

### Checklist Robustness & Integrasi
- Integritas data: FK, unique key (NIK/NRP/NIP/email/phone/nomor dokumen), soft delete terkontrol, migrasi dengan rollback plan.
- Idempoten & transaksi: endpoint create/update kritikal bersifat idempoten (key unik), gunakan transaksi DB untuk jurnal/stock/angsuran.
- Konsistensi finansial: mapping COA wajib, validasi debit=kredit, rekonsiliasi stok vs ledger, aging vs GL.
- Health & monitoring: health check app+broker+DB, alert job gagal (backup, pajak, ekspor, notifikasi), APM/error rate.
- Feature flag & akses: aktivasi modul lewat flag, role/SoD ditegakkan; break-glass tercatat.
- Testing cepat: smoke test API inti (auth, simpanan, pinjaman, SO/PO, notifikasi) tiap deploy; checklist UAT (pajak, jurnal, backup-restore, akses, offline queue).

### Cacat/Kekurangan yang Perlu Diantisipasi
- Ketergantungan identitas manual: belum ada integrasi resmi ke Dukcapil/e-KTP; risiko data KYC palsu; mitigasi: dokumen wajib + face match opsional + screening manual.
- Pengiriman OTP biayanya bergantung provider; belum ada limit biaya bulanan; mitigasi: monitor biaya (low priority) dan pakai email default.
- Kafka/RabbitMQ self-host: perlu pemeliharaan; risiko downtime jika VM kecil tanpa HA; mitigasi: monitoring broker, fallback pending_notifications, opsi managed jika beban naik.
- Backup/DR: memerlukan disiplin uji restore; tanpa uji rutin, risiko backup korup tidak terdeteksi.
- Compliance pajak: regulasi PKP/PPN bisa berubah; perlu review berkala; e-faktur tetap perlu infrastruktur DJP.
- Observabilitas biaya & performa: belum ada batas alert biaya notifikasi/PG; belum ada target TPS formal untuk beban puncak; perlu penetapan saat go-live.

### Normalisasi & Master Data
- Pisahkan master referensi: agama (`religions`), suku (`ethnicities`), pekerjaan (`occupations`), pangkat (`ranks`), kategori produk (`product_categories`), unit (`product_units`), lokasi geo (provinsi/kota/kecamatan/kelurahan) di address DB.
- Hindari duplikasi identitas: `identities` refer ke master, enforce unik NIK/NRP/NIP/email/phone; gunakan constraints + index unik.
- Transaksi hanya simpan FK + nilai numerik; atribut dinamis bisa di `product_attributes` (key/value per kategori) untuk fleksibilitas tanpa denormalisasi tabel utama.
- Master dokumen: `attachments` refer ke tipe dokumen, `doc_signatures` untuk sertifikasi; jangan simpan blob besar di kolom utama (gunakan path/URL storage).
- Histori status (anggota, pengurus, pinjaman, tiket, risk) di tabel riwayat terpisah agar state sekarang tetap ter-normalisasi.

### SOP Enkripsi/Retensi/Backup
- Enkripsi: kolom sensitif (NIK/NRP, dokumen identitas, hash signature) dienkripsi at-rest; kunci dikelola terpisah (KMS) dengan rotasi; masking di log/audit.
- Retensi: log 90-180 hari; dokumen keuangan/pajak 5-7 tahun; KTP/selfie sesuai kebijakan privasi (mis. 2 tahun setelah nonaktif) dengan purge terjadwal.
- Backup: harian + mingguan, offsite; verifikasi checksum; uji restore triwulanan; dokumentasi RPO/RTO; alert jika backup gagal.

## Modul Pengurus (governance)
- Data & masa jabatan: tabel `pengurus` (user_id, jabatan, periode_mulai/akhir, SK_path, status); `pengurus_history` untuk riwayat.
- Role & approval: bundle permission khusus (otorisasi pinjaman, persetujuan pengeluaran, pengesahan SHU, setup voting). 
- Data status risiko: di tabel `anggota` tambahkan kolom `risk_flag` (normal/attention/blacklist) dan `risk_reason`; histori di `anggota_risk_history`.
- Pinjaman macet: gunakan aging & kolektibilitas; flag otomatis jika >30/60/90 hari; tampilkan di dashboard pengurus/pengawas (widget risk alerts) dan daftar khusus “Perhatian Khusus”.
- Blacklist: status khusus yang memblokir pengajuan baru; tampilkan badge di profil anggota; perlu approval pengurus/pengawas untuk set/unset, tercatat di audit log.
- UI: halaman “Risiko & Blacklist” dengan filter (kolektibilitas, risk_flag, jatuh tempo), tombol ekspor PDF/XLSX (pakai mekanisme print/export & barcode); notifikasi ke pengawas saat ada anggota masuk blacklist atau pinjaman masuk bucket >60 hari.

#### Payload/Flow Risiko
- GET /risiko/anggota?flag=blacklist|attention&bucket=60 : daftar anggota bermasalah (dengan total tunggakan, umur tunggakan, risk_reason).
- POST /risiko/anggota/{id}/flag : Body `{ "risk_flag":"blacklist", "risk_reason":"fraud", "notes":"..." }` → Response `{status:"ok"}`; wajib log audit + notifikasi pengawas.
- GET /pinjaman/at-risk?bucket=30 : ringkas pinjaman terlambat; gunakan untuk widget risk alerts.

#### UI Risiko & Blacklist (tabel + kartu)
- Filter: risk_flag (all/attention/blacklist), kolektibilitas (0-30/31-60/>60), koperasi/unit, kata kunci nama/NIK.
- Tabel desktop: kolom nama, nomor anggota, risk_flag, bucket, tunggakan, risk_reason, aksi (lihat detail/ubah flag).
- Kartu mobile: nama + badge risk_flag, tunggakan, bucket; tombol detail/ubah flag.
- Ekspor: tombol PDF/XLSX memakai mekanisme print/export (barcode). Notifikasi ke pengawas saat flag berubah ke blacklist.

### Pencetakan Bon/Faktur & Logging
  - Pengawas: view-only + approval tertentu, akses audit log & laporan pengawasan.
  - Kasir/Juru Bayar: penerimaan setoran, pembayaran angsuran, batch payroll deduction, penarikan, kas kecil.
  - Admin Sistem: manajemen role/permission, feature flag modul, konfigurasi tenant.

## Juru Bayar & Jurnal Otomatis
- Peran juru bayar: entitas (internal/instansi) yang memotong gaji anggota untuk simpanan pokok/wajib dan cicilan pinjaman.
- Flow: surat kuasa → daftar anggota terhubung juru bayar → upload batch potong gaji → validasi → posting transaksi.
- Jurnal otomatis:
  - Potong simpanan wajib/pokok: Dr Piutang gaji/Bank (jika dana masuk), Cr Simpanan wajib/pokok.
  - Potong cicilan pinjaman: Dr Piutang gaji/Bank, Cr Piutang pinjaman pokok; Cr Pendapatan bunga (porsi bunga), Cr Denda (jika ada).
  - Jika payroll deduction belum disetor ke koperasi: catat sebagai Piutang ke juru bayar; ketika setor, pindahkan ke Kas/Bank.
- Rekonsiliasi: modul untuk mencocokkan slip gaji/bukti transfer dengan batch payroll; status open/partial/closed.

## Kas/Bank & Dana Beredar
- Kas & Bank: gunakan COA dengan tipe kas/bank; tampilan ringkasan saldo kas fisik, saldo bank per rekening, kas kecil. Endpoint: `GET /cashbank/summary` dari GL real-time/near-real-time.
- Modal/SHU belum dibayar: laporan kewajiban pembayaran (SHU yang sudah diakui tapi belum dibayar). Endpoint: `GET /liability/shu-pending`.
- Piutang pinjaman beredar: total outstanding pokok, bunga terakru, aging. Endpoint: `GET /pinjaman/aging` + GL view.
- Simpanan terikat: saldo simpanan pokok/wajib/sukarela per anggota dan total. Endpoint: `GET /simpanan/summary`.
- Dashboard keuangan ringkas per role: pengurus/pengawas melihat kas-bank, piutang, simpanan, kewajiban SHU, biaya operasional, pendapatan bunga.

## UI & Wireframe per Role (mobile-first, jQuery/AJAX, DOM ID unik)
- Prinsip: mobile-first; desktop menambah kolom/panel; setiap widget punya ID unik (mis. `#dash-anggota-saldo`, `#dash-pengurus-approval-list`). AJAX (jQuery) untuk load data: `$.getJSON('/api/...')` → render DOM; skeleton/loading state sebelum data masuk.
- Anggota (mobile):
  - Header saldo simpanan & pinjaman ringkas (badge keterlambatan). ID: `#anggota-card-saldo`.
  - Shortcut: Simpanan, Pinjaman, Voting, Notifikasi. ID per tombol: `#btn-simpanan`, `#btn-pinjaman`, `#btn-voting`, `#btn-notif`.
  - List transaksi terakhir (infinite scroll). ID: `#anggota-transaksi-list`.
  - Banner pengumuman/voting aktif. ID: `#anggota-banner-voting`.
- Anggota (desktop tambahan): tabel riwayat lebih lebar, grafik mini saldo vs waktu.

- Pengurus (mobile):
  - KPI strip: kas+bank, piutang pinjaman, simpanan terikat, kewajiban SHU. ID: `#pengurus-kpi-*`.
  - Queue approval pinjaman/pengeluaran. ID: `#approval-queue`.
  - Agenda rapat/voting terdekat. ID: `#agenda-rapat`.
  - Notifikasi risiko (telat bayar, konflik kepentingan). ID: `#risk-alerts`.
- Pengurus (desktop tambahan): dua kolom—kiri KPI+chart, kanan queue approval + tabel aging; filter tanggal/koperasi/unit.

- Pengawas (mobile):
  - Ringkasan kas/bank, simpanan, piutang, kewajiban SHU (read-only). ID: `#pengawas-kpi-*`.
  - Audit log terbaru. ID: `#audit-log-list`.
  - Approval khusus (jika diizinkan). ID: `#pengawas-approval`.
  - Laporan pengawasan unduh PDF. ID: `#pengawas-report-download`.

- Kasir/Juru Bayar (mobile):
  - Widget kas fisik & setoran hari ini. ID: `#kasir-kas-harian`.
  - Form input setoran/angsuran cepat (scan kode anggota). ID: `#kasir-form-bayar`.
  - Upload batch payroll deduction. ID: `#kasir-payroll-upload`.
  - Status rekonsiliasi batch. ID: `#kasir-rekon-status`.
- Kasir (desktop tambahan): tabel batch, tombol ekspor, panel verifikasi bukti transfer.

- Admin Sistem (mobile):
  - Feature flag modul per koperasi/unit. ID: `#admin-feature-flags`.
  - Manajemen role/permission. ID: `#admin-roles`.
  - Monitoring API error rate/log singkat. ID: `#admin-api-monitor`.
- Admin (desktop tambahan): chart health check, daftar user terakhir, toggle modul massal.

### Pola AJAX/jQuery
- Setiap widget fetch via endpoint spesifik, cache lokal sessionStorage jika perlu untuk hemat data.
- Error state dengan toast/notif; retry ringan; indikator offline.
- Partial reload: gunakan ID unik per widget untuk replace innerHTML/append row; hindari full page reload.

## OpenAPI Mini (contoh payload/response utama)
- GET /cashbank/summary
  - Response 200: `{ "cash": 1500000, "petty_cash": 250000, "bank_accounts": [{"id":1,"name":"BCA Operasional","balance":12500000},{"id":2,"name":"Mandiri Giro","balance":8300000}] }`

- GET /pengurus/approval-queue
  - Query: `type=pinjaman|pengeluaran&status=pending`
  - Response 200: `{ "items": [ {"id":101,"type":"pinjaman","anggota":"Andi","jumlah":15000000,"tenor_bulan":12,"submitted_at":"2026-02-02T04:10:00Z","risk_flag":"late"} ], "next_cursor": "abc" }`

- GET /anggota/dashboard
  - Response 200: `{ "simpanan_saldo": {"pokok":1000000,"wajib":3000000,"sukarela":1500000}, "pinjaman": {"outstanding_pokok":5000000,"tunggakan":250000,"jatuh_tempo":"2026-02-28"}, "voting_aktif": [{"id":9,"judul":"RAT 2026","deadline":"2026-02-10"}], "transaksi_recent": [{"id":9001,"jenis":"setor_wajib","jumlah":250000,"tanggal":"2026-02-01"}] }`

- POST /kasir/payroll-upload (multipart)
  - Body: file CSV + `payroll_date`
  - Response 200: `{ "batch_id": 77, "rows": 120, "valid": 118, "invalid": 2, "status": "validating" }`

- GET /kasir/payroll-batch/{id}
  - Response 200: `{ "batch_id":77, "status":"open", "total_potong": 12500000, "settled": 0, "items": [{"anggota":"Budi","potong_wajib":200000,"potong_pinjaman":500000,"status":"pending"}] }`

- GET /pinjaman/aging
  - Response 200: `{ "total_pokok": 125000000, "total_bunga_terakru": 5500000, "buckets": [{"range":"0-30","pokok":80000000},{"range":"31-60","pokok":30000000},{"range":">60","pokok":15000000}] }`

- GET /voting/{id}/result
  - Response 200: `{ "id":9,"judul":"RAT 2026","total_pemilih":320,"sudah_memilih":250,"opsi":[{"label":"Setuju","suara":180},{"label":"Tidak","suara":50},{"label":"Abstain","suara":20}],"deadline":"2026-02-10T23:59:00Z" }`

- GET /simpanan/summary
  - Response 200: `{ "total_pokok": 125000000, "total_wajib": 210000000, "total_sukarela": 50000000, "per_anggota": [{"anggota_id":11,"nama":"Andi","pokok":1000000,"wajib":3000000,"sukarela":500000}] }`

- POST /simpanan/transactions
  - Body: `{ "anggota_id":11, "simpanan_type_id":2, "jumlah":250000, "jenis":"setor", "metode":"bank_transfer", "referensi":"TRX123" }`
  - Response 201: `{ "id":9002, "status":"posted", "journal_ref":"JRN-2026-0021" }`

- POST /pinjaman/{id}/angsuran
  - Body: `{ "jumlah":750000, "bunga":250000, "denda":50000, "metode":"cash", "kasir_id":7 }`
  - Response 201: `{ "id":8801, "status":"posted", "journal_ref":"JRN-2026-0030" }`

## Layout Grid/Stack (mobile → tablet → desktop)
- Mobile: stack vertikal; kartu KPI 1 kolom, list scroll; tombol CTA sticky di bawah bila perlu.
- Tablet: 2 kolom; KPI di grid 2xN; list approval berdampingan dengan ringkasan.
- Desktop: 3 kolom bila lebar cukup; panel kiri navigasi, tengah konten utama (approval/list), kanan KPI/alert. Gunakan breakpoint Bootstrap: `col-12` (mobile), `col-md-6`, `col-lg-4/8`.
- Komponen tabel di mobile: ubah ke kartu dengan key-value; di desktop: tabel penuh dengan sorting/filter.
- Navigasi tab: gunakan ID unik per tab (`#tab-anggota`, `#tab-pengurus`) dan AJAX load konten per tab.

## Contoh Markup Dashboard (Anggota, mobile-first)
```html
<div id="dash-anggota" class="container py-3">
  <div id="anggota-card-saldo" class="card mb-2">
    <div class="card-body">
      <div class="d-flex justify-content-between">
        <div>Simpanan</div><div id="saldo-simpanan">-</div>
      </div>
      <div class="d-flex justify-content-between mt-1">
        <div>Pinjaman</div><div id="saldo-pinjaman">-</div>
      </div>
      <small id="badge-tilang" class="text-danger d-none">Terlambat bayar</small>
    </div>
  </div>

  <div class="d-grid gap-2 mb-2" id="anggota-shortcuts">
    <button id="btn-simpanan" class="btn btn-primary">Simpanan</button>
    <button id="btn-pinjaman" class="btn btn-outline-primary">Pinjaman</button>
    <button id="btn-voting" class="btn btn-outline-primary">Voting</button>
    <button id="btn-notif" class="btn btn-outline-primary">Notifikasi</button>
  </div>

  <div id="anggota-banner-voting" class="alert alert-info d-none">Voting aktif...</div>

  <div id="anggota-transaksi-list" class="list-group">
    <!-- items append via AJAX -->
  </div>

  <button id="load-more-tx" class="btn btn-light w-100 mt-2">Muat lagi</button>
</div>

<script>
$(function(){
  $.getJSON('/api/v1/anggota/dashboard', function(data){
    $('#saldo-simpanan').text('Rp ' + data.simpanan_saldo.wajib.toLocaleString());
    $('#saldo-pinjaman').text('Rp ' + data.pinjaman.outstanding_pokok.toLocaleString());
    if(data.pinjaman.tunggakan > 0) $('#badge-tilang').removeClass('d-none');
    if(data.voting_aktif.length) $('#anggota-banner-voting').removeClass('d-none');
    data.transaksi_recent.forEach(function(tx){
      $('#anggota-transaksi-list').append(`<div class="list-group-item" id="tx-${tx.id}">${tx.jenis} - Rp ${tx.jumlah.toLocaleString()}</div>`);
    });
  });
});
</script>
```

#### Flow Print Async (ringkas)
1) FE panggil `/print/{type}` (invoice/return/angsuran/laporan) → respon `{job_id}` atau `{url}`.
2) Jika `job_id`, FE polling `/api/v1/jobs/{job_id}` hingga `status=done` → dapat `download_url`.
3) FE buka PDF (auto-print) dan tampilkan toast sukses; jika `status=error`, tampilkan toast gagal + tombol retry.

#### Format Audit Log (lengkap)
- Fields: `user_id`, `action` (create/update/delete/approve/print/generate_barcode), `table_name`, `record_id`, `old_values`, `new_values`, `ref_type`, `ref_id`, `ip_address`, `user_agent`, `created_at`, `trace_id` (opsional untuk korelasi), `module` (kasir/pinjaman/inventory). 
- Simpan JSON old/new (terbatas bidang sensitif dienkripsi atau disensor); catat event print & generate barcode.

#### Tombol Ekspor (Pengurus)
```html
<div class="card mt-3" id="export-keuangan">
  <div class="card-header">Ekspor Keuangan</div>
  <div class="card-body d-flex gap-2">
    <button id="btn-exp-neraca" class="btn btn-outline-primary">Unduh Neraca</button>
    <button id="btn-exp-lr" class="btn btn-outline-primary">Unduh Laba Rugi</button>
  </div>
</div>

<script>
const coopId = 1, buId = 2;
$('#btn-exp-neraca').on('click', () => window.location = `/api/v1/reports/keuangan?type=neraca&period=2026-01&format=pdf&coop_id=${coopId}&business_unit_id=${buId}`);
$('#btn-exp-lr').on('click', () => window.location = `/api/v1/reports/keuangan?type=labarugi&period=2026-01&format=pdf&coop_id=${coopId}&business_unit_id=${buId}`);
</script>
```

#### Tombol Ekspor Voting & Aging (Pengurus)
```html
<div class="card mt-3" id="export-voting-aging">
  <div class="card-header">Ekspor Voting & Aging</div>
  <div class="card-body d-flex flex-wrap gap-2 align-items-center">
    <select id="sel-coop" class="form-select form-select-sm" style="width:auto">
      <option value="1">Koperasi A</option>
    </select>
    <select id="sel-bu" class="form-select form-select-sm" style="width:auto">
      <option value="2">Unit Ritel</option>
    </select>
    <button id="btn-exp-voting" class="btn btn-outline-primary">Unduh Voting</button>
    <button id="btn-exp-aging" class="btn btn-outline-primary">Unduh Aging</button>
  </div>
</div>

<script>
$('#btn-exp-voting').on('click', () => {
  const coopId = $('#sel-coop').val();
  const buId = $('#sel-bu').val();
  window.location = `/api/v1/voting/9/export?format=pdf&coop_id=${coopId}&business_unit_id=${buId}`;
});

$('#btn-exp-aging').on('click', () => {
  const coopId = $('#sel-coop').val();
  const buId = $('#sel-bu').val();
  window.location = `/api/v1/reports/pinjaman?periode=2026-01&type=aging&format=pdf&coop_id=${coopId}&business_unit_id=${buId}`;
});

// Dropdown dinamis koperasi & unit
$.getJSON('/api/v1/coops', res => {
  res.items?.forEach(c => $('#sel-coop, #sel-coop-kasir').append(`<option value="${c.id}">${c.name}</option>`));
});

$('#sel-coop, #sel-coop-kasir').on('change', function(){
  const coopId = $(this).val();
  const target = $(this).is('#sel-coop') ? '#sel-bu' : '#sel-bu-kasir';
  $(target).empty();
  $.getJSON(`/api/v1/business-units?coop_id=${coopId}`, res => {
    res.items?.forEach(b => $(target).append(`<option value="${b.id}">${b.name}</option>`));
  });
});

// Handler loading/disabled untuk ekspor
function bindLoading(btnId, urlBuilder){
  $(btnId).on('click', function(){
    const $btn = $(this);
    const prev = $btn.text();
    $btn.prop('disabled', true).text('Memproses...');
    const job = urlBuilder();
    if(job.async){
      // polling status hingga siap unduh
      const poll = setInterval(() => {
        $.getJSON(`/api/v1/jobs/${job.job_id}`, st => {
          if(st.status === 'done'){ clearInterval(poll); $btn.prop('disabled', false).text(prev); window.location = st.download_url; }
          else if(st.status === 'error'){ clearInterval(poll); $btn.prop('disabled', false).text('Gagal'); showToast('Gagal memproses ekspor', 'error'); }
          else { $btn.text(`Menyiapkan... ${st.progress || 0}%`); }
        });
      }, 1500);
    } else {
      window.location = job.url;
      setTimeout(() => $btn.prop('disabled', false).text(prev), 2000); // reset sederhana
    }
  });
}

// urlBuilder dapat mengembalikan {url} (sinkron) atau {async:true, job_id, download_url?} untuk kasus generate lambat
bindLoading('#btn-exp-voting', () => ({ url: `/api/v1/voting/9/export?format=pdf&coop_id=${$('#sel-coop').val()}&business_unit_id=${$('#sel-bu').val()}` }));
bindLoading('#btn-exp-aging', () => ({ url: `/api/v1/reports/pinjaman?periode=2026-01&type=aging&format=pdf&coop_id=${$('#sel-coop').val()}&business_unit_id=${$('#sel-bu').val()}` }));
bindLoading('#btn-exp-neraca', () => ({ url: `/api/v1/reports/keuangan?type=neraca&period=2026-01&format=pdf&coop_id=${$('#sel-coop').val()}&business_unit_id=${$('#sel-bu').val()}` }));
bindLoading('#btn-exp-lr', () => ({ url: `/api/v1/reports/keuangan?type=labarugi&period=2026-01&format=pdf&coop_id=${$('#sel-coop').val()}&business_unit_id=${$('#sel-bu').val()}` }));
bindLoading('#btn-exp-batch', () => ({ url: `/api/v1/kasir/payroll-batch/export?coop_id=${$('#sel-coop-kasir').val()}&business_unit_id=${$('#sel-bu-kasir').val()}&format=xlsx` }));
bindLoading('#btn-exp-simpanan', () => ({ url: `/api/v1/reports/simpanan?periode=2026-01&format=xlsx&scope=total&coop_id=${$('#sel-coop-kasir').val()}&business_unit_id=${$('#sel-bu-kasir').val()}` }));

// Helper toast sederhana (bahasa Indonesia)
function showToast(msg, type='info'){
  const cls = type === 'error' ? 'bg-danger' : (type === 'success' ? 'bg-success' : 'bg-primary');
  const $t = $(`<div class="toast align-items-center text-white ${cls} border-0" role="alert" aria-live="assertive" aria-atomic="true" style="position:fixed; top:1rem; right:1rem; z-index:1055;">`)
    .append(`<div class="d-flex"><div class="toast-body">${msg}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>`);
  $('body').append($t);
  const toast = new bootstrap.Toast($t[0]);
  toast.show();
  setTimeout(() => $t.remove(), 4000);
}

// Catatan lokaliasi Indonesia: gunakan Intl
const fmtIDR = n => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(n || 0);
const fmtDateID = d => new Intl.DateTimeFormat('id-ID', { dateStyle: 'medium' }).format(new Date(d));
</script>
```

## Model Orang & Keanggotaan
- Dua jenis utama: (1) Personil kepolisian/TNI/PNS/PHL; (2) Pihak eksternal (suplier, pembeli, distributor, mitra, investor, agen, umum).
- Master data identitas (people DB): tabel referensi `religions`, `ethnicities`, `occupations`; tabel `identities` (user_id, nik, nama, agama_id, suku_id, pekerjaan_id, tempat/tanggal lahir, jenis_kelamin, foto, dokumen); tabel alamat terpisah (address DB) sudah ada.
- Personil aparat: field khusus `nrp_nip`, `pangkat`, `jabatan_struktural`, `kesatuan`. Validasi format NRP/NIP dan daftar pangkat (lookup table `ranks`).
- Profil role bisnis: `party_types` untuk suplier/pembeli/distributor/mitra; relasi many-to-many ke user (`user_party_types`).
- Status keanggotaan: enum/status pada tabel `anggota` (draft, aktif, nonaktif, keluar, meninggal, dipecat); riwayat status di `anggota_status_history` (status, alasan, tanggal, handled_by).
- UI register orang: pilihan tipe orang (personil/umum), jika personil muncul field NRP/NIP, pangkat, jabatan; tetap wajib isi identitas master dan alamat.

## Database Tambahan: Kantor Kepolisian
- Tambah DB ke-4: police_offices DB, terpisah dari people/address/coop DB.
- Tabel utama: `police_offices` (id, nama_kantor, tipe/satker, kode_satker, alamat_id, kontak, geo_lat, geo_lng), `office_hierarchy` (parent-child), `office_contacts` (kontak per fungsi), lookup `office_types`.
- Relasi ke address DB: `alamat_id` refer ke tabel addresses di address DB (foreign key via service layer).
- Penggunaan di coop DB: tabel `coop_office_links` untuk mengaitkan koperasi dengan kantor induk atau cabang; referensi alamat kantor dapat dipakai sebagai alamat koperasi default.
- API: `GET /police-offices` (filter tipe/region), `GET /police-offices/{id}`, `GET /police-offices/{id}/children`.

## Modul Koperasi Dagang & Inventori
- Tidak perlu DB terpisah: simpan di coop DB, gunakan dimensi `business_unit` untuk segmentasi unit usaha dagang. Jika skala besar, opsional micro-DB khusus inventory, tapi default satu DB coop cukup.
- Tabel tambahan (coop DB): `products` (barang dagang), `product_categories`, `product_units`, `inventory_locations` (gudang/toko), `inventory_stocks`, `inventory_movements` (in/out/transfer/adjustment), `purchase_orders` + `po_details`, `goods_receipts`, `sales_orders` + `sales_details`, `returns`, `supplier_invoices`, `operational_costs` terkait pengiriman/penjemputan.
- Inventaris (aset tetap): `fixed_assets`, `asset_assignments` (aset ke orang/lokasi), `asset_maintenance`, `asset_documents` (surat/nota), `asset_movements`, `asset_depreciations` (opsional jika diaktifkan).
- Operasional pendukung: link biaya-orang-barang-lokasi-dokumen via tabel referensi `operational_costs` (foreign key ke order/asset/movement), `attachments` (surat/nota/bukti) dengan reference_type/id.
- API dagang utama: 
  - Produk & stok: `GET/POST /products`, `GET /inventory/stock?location_id=...`, `POST /inventory/move` (transfer), `POST /inventory/adjust`.
  - Pembelian: `POST /purchase-orders`, `POST /goods-receipts`, `POST /supplier-invoices`.
  - Penjualan: `POST /sales-orders`, `POST /sales-orders/{id}/fulfill`, `POST /returns`.
  - Inventaris aset: `POST /assets`, `POST /assets/{id}/assign`, `POST /assets/{id}/maintenance`, `POST /assets/{id}/move`, `POST /assets/{id}/attach`.
- Jurnal otomatis: 
  - Pembelian: Dr Persediaan / Cr Hutang Usaha; saat bayar: Dr Hutang / Cr Kas/Bank.
  - Penjualan: Dr Piutang/Kas / Cr Penjualan; Dr HPP / Cr Persediaan; retur membalik sebagian.
  - Inventaris aset: Dr Aset Tetap / Cr Kas/Hutang; depresiasi: Dr Beban Penyusutan / Cr Akumulasi.
- Laporan: stok per lokasi, kartu persediaan, HPP, aging hutang/piutang dagang, aset & penyusutan, biaya operasional per order/lokasi/unit usaha.

### Skema Tabel Kunci (ringkas)
- `products` (id, sku, nama, kategori_id, unit_id, harga_beli, harga_jual, is_asset_flag, status, created_at).
- Kolom lanjutan untuk kontrol stok ketat: `batch_enabled` bool, `serial_enabled` bool, `expiry_enabled` bool; jika aktif maka movement/detail wajib mengisi `batch_no`, `serial_no`, `expiry_date`.
- `product_units` (id, nama, konversi_ke_unit_dasar, faktor).
- `inventory_locations` (id, nama, jenis (gudang/toko), address_id, business_unit_id).
- `inventory_stocks` (id, product_id, location_id, qty_on_hand, qty_reserved, qty_available(calc)).
- `inventory_movements` (id, product_id, from_location_id, to_location_id, qty, tipe (in/out/transfer/adjust), reason, ref_type, ref_id, created_by, created_at).
- `purchase_orders` (id, supplier_id, nomor_po, tanggal, status, total, business_unit_id).
- `po_details` (id, po_id, product_id, qty, harga, diskon, subtotal).
- `goods_receipts` (id, po_id, nomor_gr, tanggal, status, lokasi_id).
- `sales_orders` (id, nomor_so, customer_id, tanggal, status, total, business_unit_id, lokasi_id_pengambilan/pengiriman).
- `sales_details` (id, so_id, product_id, qty, harga, diskon, subtotal).
- `returns` (id, ref_type(po/so), ref_id, product_id, qty, reason, status).
- `fixed_assets` (id, kode_aset, nama_aset, kategori, nilai_perolehan, tanggal_perolehan, lokasi_id, assigned_to, status, umur_ekonomis, metode_depresiasi).
- `asset_documents` (id, asset_id, nama_file, path, tipe_dokumen (nota, surat, foto))).
- `operational_costs` (id, kategori_biaya, deskripsi, jumlah, ref_type (so/po/asset/movement), ref_id, lokasi_id, dibebankan_ke, approved_by).
- `attachments` (id, ref_type, ref_id, file_path, caption).

### Master Jenis Barang
- `product_categories` dengan tipe barang (pangan/sayur/buah, elektronik, jasa, dll); field `handling_type` (perishable/durable), `storage_requirements` (chiller/ambient), `shelf_life_days`.
- `product_attributes` (attr key/value per kategori) untuk fleksibilitas, mis. elektronik: watt, voltase; pangan: grade, kemasan, tanggal panen.

#### Lookup & UI Produk (perishable vs durable)
- Dropdown kategori barang (`product_categories`) dengan field handling_type (perishable/durable) dan storage_requirements.
- Jika kategori perishable dipilih: tampilkan field batch/expiry wajib; form hint untuk storage (chiller/ambient), shelf life.
- Jika kategori durable/elektronik: opsional serial_no per item; attr watt/voltase; warranty period (opsional) bisa disimpan di `product_attributes`.

### Contoh Payload API (ringkas)
- POST /inventory/move (transfer)
  - Body: `{ "product_id":1, "from_location_id":10, "to_location_id":12, "qty":50, "batch_no":"BCH-001", "expiry_date":"2026-06-01", "reason":"transfer_gudang", "ref_type":"so", "ref_id":123 }`
  - Response 201: `{ "movement_id": 9001, "status": "posted" }`

- POST /inventory/adjust
  - Body: `{ "product_id":1, "location_id":10, "qty_delta":-2, "reason":"shrinkage", "batch_no":"BCH-001", "serial_no":null, "expiry_date":"2026-06-01" }`
  - Response 201: `{ "movement_id": 9002, "status": "posted" }`

- POST /returns (contoh untuk sales return dengan serial)
  - Body: `{ "ref_type":"so", "ref_id":1101, "items": [{"product_id":2, "qty":1, "serial_no":"SN-XYZ-123", "reason":"rusak kemasan"}] }`
  - Response 201: `{ "return_id":1201, "status":"received" }`

- POST /purchase-orders
  - Body: `{ "supplier_id":5, "business_unit_id":2, "items": [{"product_id":1,"qty":100,"harga":15000,"batch_no":"BCH-PO-01","expiry_date":"2026-06-01"}] }`
  - Response 201: `{ "po_id": 701, "status": "draft" }`

- POST /goods-receipts
  - Body: `{ "po_id":701, "location_id":10, "items": [{"product_id":1,"qty":100,"batch_no":"BCH-PO-01","expiry_date":"2026-06-01"}] }`
  - Response 201: `{ "gr_id": 801, "status": "received" }`

- POST /sales-orders
  - Body: `{ "customer_id":20, "business_unit_id":2, "location_id":12, "items": [{"product_id":1,"qty":3,"harga":25000,"batch_no":"BCH-PO-01"}], "diskon":0 }`
  - Response 201: `{ "so_id": 1101, "status": "draft" }`

- POST /sales-orders/{id}/fulfill
  - Body: `{ "items": [{"product_id":1,"qty":3,"batch_no":"BCH-PO-01"}], "ship_from_location_id":12 }`
  - Response 200: `{ "status": "fulfilled", "movement_ids": [9101] }`

- POST /assets
  - Body: `{ "kode_aset":"AST-001", "nama_aset":"Laptop Kasir", "kategori":"elektronik", "nilai_perolehan":12000000, "tanggal_perolehan":"2026-01-10", "lokasi_id":12, "assigned_to":7, "umur_ekonomis":36, "metode_depresiasi":"garis_lurus" }`
  - Response 201: `{ "asset_id":501 }`

- POST /assets/{id}/maintenance
  - Body: `{ "deskripsi":"Servis keyboard", "biaya":250000, "tanggal":"2026-02-01", "vendor":"Service Center" }`
  - Response 201: `{ "maintenance_id":601, "status":"recorded" }`

### Pencetakan Bon/Faktur & Logging
- Bon/faktur otomatis: setelah kasir/admin menyimpan transaksi (SO/angsuran/simpanan/pembayaran), backend menghasilkan PDF (templat faktur) dan menyimpan metadata di `attachments` (ref_type/ref_id, file_path); frontend membuka print dialog atau download otomatis.
- Endpoint contoh: `POST /print/invoice` dengan `ref_type` (so/pembayaran) → return `{url, job_id?}`; jika async, gunakan polling job seperti handler ekspor.
- Logging wajib: gunakan `audit_logs` (user_id, action, table_name, record_id, old_values, new_values, ip, user_agent, timestamp); panggil di setiap create/update/delete/approval; untuk transaksi kasir, log juga event print.

Contoh endpoint print lain:
- `POST /print/return` body `{ "return_id":1201 }` → `{ url: "https://.../return-1201.pdf", job_id?: "JOB-1" }`
- `POST /print/angsuran` body `{ "angsuran_id":8801 }` → `{ url: "https://.../angsuran-8801.pdf", job_id?: "JOB-2" }`

Format audit log (JSON contoh):
```json
{
  "user_id": 7,
  "action": "update",
  "table_name": "sales_orders",
  "record_id": 1101,
  "old_values": {"status": "draft"},
  "new_values": {"status": "fulfilled"},
  "ip_address": "192.168.1.10",
  "user_agent": "Mozilla/5.0",
  "ref_type": "so",
  "ref_id": 1101,
  "created_at": "2026-02-03T09:38:00Z"
}
```

### Retry & Toast Khusus Print + Barcode
- Retry print: jika status job `error`, tampilkan toast error dan sediakan tombol “Coba Lagi” yang memanggil kembali endpoint print; batasi retry (mis. 3x) dan log percobaan gagal.
- Toast khusus: sukses → hijau “PDF siap, membuka…”; gagal → merah “Gagal mencetak, coba lagi atau hubungi admin”. Untuk retry, tombol di toast bisa memicu ulang binding.
- Barcode wajib di semua dokumen terbitan (bon/faktur/return/angsuran/laporan): backend menyematkan barcode/QR berisi URL verifikasi atau payload hash; metadata simpan di `attachments` dan `audit_logs` (action: generate_barcode). Frontend hanya menampilkan PDF; validasi keaslian via scan barcode yang memanggil endpoint verifikasi (`GET /verify/doc/{id}`) yang merespons status valid dan checksum.

#### Verifikasi Barcode
- Endpoint: `GET /verify/doc/{id}` → Response `{status:"valid", ref_type:"so", ref_id:1101, hash:"...", issued_at:"2026-02-03T09:38:00Z"}` atau `{status:"invalid"}`.
- QR/barcode berisi URL verifikasi; FE/Scanner cukup buka URL untuk validasi.

### Helper Perhitungan Otomatis
- Plafon pinjaman: helper menghitung maksimum berdasarkan (gaji/pendapatan, tenor, usia pensiun, risk_flag, DSCR). Param configurable per koperasi. API: `POST /calc/plafon` body `{gaji, tenor_bulan, usia, risk_flag}` → `{plafon_maks, dscr}`.
- Denda & bunga telat: scheduler + helper `POST /calc/denda` body `{outstanding, hari_telat, rate_denda, bunga_per_bulan}` → `{denda, bunga_terakru}`; dipakai untuk posting otomatis dan reminder.
- SHU: `POST /calc/shu` body `{laba_bersih, konfigurasi_proporsi, kontribusi_anggota[]}` → `{total_shu, distribusi_per_anggota[]}`; simpan draft sebelum distribusi.
- Akuntansi otomatis: rule engine memetakan event → jurnal (COA debit/kredit, nilai). Contoh event: simpanan/setor/penarikan, pinjaman/cair/angsuran/denda, penjualan/pembelian/retur, aset/penyusutan. API: `POST /journal/auto` body `{event_type, ref_type, ref_id}` → hasil jurnal + posting ke GL.
- Validasi ganda: hasil helper ditampilkan di UI (preview) sebelum posting, dengan tombol “setujui” untuk pengurus bila melewati threshold.

### Validasi Tambahan
- Semua nominal > 0; tanggal tidak di masa lalu untuk disburse/angsuran (kecuali backdate khusus dengan approval).
- Plafon pinjaman: tidak boleh melebihi hasil helper plafon; blok jika usia+tenor melewati masa pensiun.
- Batch/expiry: jika produk perishable dan expiry_enabled, tanggal kadaluarsa harus > tanggal transaksi; batch_no wajib unik per lokasi jika aturan diaktifkan.
- Serial: jika serial_enabled, qty per baris = 1 atau harus sertakan daftar serial; validasi tidak boleh duplikat.
- Return: qty retur ≤ qty terpenuhi; tidak boleh retur untuk status draft/cancel.
- Write-off pinjaman: butuh approval pengawas; wajib jurnal cadangan atau koreksi piutang.

### Validasi Tambahan (perdalam)
- Rate bunga: batas atas configurable per koperasi (mis. <= 3%/bulan); tolak jika melebihi; simpan rate efektif tahunan (APR).
- Denda: batas harian dan total; tidak boleh melebihi pokok tertunggak.
- Rekening bank: format VA/rekening diverifikasi (regex + daftar bank); jika payout ke pihak ketiga, wajib nama pemilik dan bukti.
- Dokumen wajib: pengajuan pinjaman harus ada bukti penghasilan/NRP kartu (personil); write-off perlu lampiran berita acara; retur perlu foto bukti barang.
- Anti duplikasi identitas: cek NIK/NRP/NIP/email/phone unik sebelum create; konflik harus diresolusi manual.
- Cutoff backdate: backdate transaksi keuangan hanya dalam window tertentu (mis. 7 hari) dengan role khusus; di luar itu perlu approval pengawas.
- Penomoran unik: nomor_anggota, nomor_pinjam, nomor_SO/PO harus unik per koperasi; gunakan prefix koperasi + tahun + sequence.
- Pembayaran: metode bank_transfer wajib bukti/nomor referensi; kas harus dicatat oleh kasir dengan shift/kas_box.
- Jurnal otomatis: block jika COA hilang/mapping tidak lengkap; validasi debit= kredit sebelum posting; jika gagal, simpan sebagai draft.

### CRUD Detail & Validasi (field wajib)
- Anggota: wajib `nama`, `email/phone`, `nik`, `alamat`, `nomor_anggota`; jika personil: `nrp_nip`, `pangkat`, `jabatan`. Validasi unik: email, phone, nik, nomor_anggota.
- Pengurus/Pengawas: wajib `user_id`, `jabatan`, `periode_mulai`, `periode_akhir`; perlu dokumen SK (file) jika ada; validasi tidak overlap periode untuk jabatan sama.
- Simpanan: jenis wajib `nama_simpanan`, `jenis` (pokok/wajib/sukarela), aturan nominal/periodik; transaksi wajib `anggota_id`, `simpanan_type_id`, `jumlah`, `tanggal`, `jenis_transaksi` (setor/tarik). Validasi saldo cukup untuk tarik; audit void/unpost.
- Pinjaman: wajib `anggota_id`, `jumlah_pinjaman`, `bunga`, `jangka_waktu`, `tujuan`; cek plafon helper; status transisi: draft→submitted→approved/rejected→disbursed→closed/writeoff. Angsuran: `pinjaman_id`, `angsuran_ke`, `jumlah`, `tanggal_bayar`; validasi sisa saldo pokok.
- Produk: wajib `sku`, `nama`, `kategori_id`, `unit_id`, `harga_jual`; jika batch/serial/expiry enabled, enforce input di movement/detail. Validasi SKU unik.
- Inventory movement: wajib `product_id`, `qty`, `tipe` (in/out/transfer/adjust); jika transfer: `from_location_id` & `to_location_id`; jika adjust: reason; jika batch/serial/expiry enabled: wajib isi; qty>0.
- PO/SO: wajib header `nomor` (unik), `supplier_id`/`customer_id`, `tanggal`, `business_unit_id`; detail wajib `product_id`, `qty>0`, `harga`; status flow sesuai approval; retur hanya pada status fulfill/received.
- Aset: wajib `kode_aset` (unik), `nama_aset`, `kategori`, `nilai_perolehan`, `tanggal_perolehan`, `lokasi_id`; jika depresiasi aktif: `umur_ekonomis`, `metode`. Assign/move perlu target lokasi/orang; maintenance wajib deskripsi & tanggal.
- Risiko/Blacklist: set flag wajib `risk_flag`, `risk_reason`; log audit; hanya peran tertentu (pengurus/pengawas) yang boleh ubah.
- Attachments/Print: wajib `ref_type`, `ref_id`, `file_path`; tidak dihapus kecuali admin; log audit.

### Payload Tambahan
- POST /config/simpanan-wajib
  - Body: `{ "jumlah_wajib_bulanan":250000, "tanggal_jatuh_tempo":10 }`
  - Response: `{ "status":"ok" }`

- POST /returns/{id}/approve
  - Body: `{ "approved_by":7, "catatan":"OK" }`
  - Response: `{ "status":"approved" }`

- POST /pinjaman/{id}/writeoff
  - Body: `{ "alasan":"gagal bayar", "approved_by":9, "lampiran_id":7001 }`
  - Response: `{ "status":"written_off", "journal_ref":"JRN-999" }`

### Payload Verifikasi Barcode
- `GET /verify/doc/{id}` → `{ "status": "valid", "ref_type": "so", "ref_id": 1101, "hash": "abc123", "issued_at": "2026-02-03T09:38:00Z" }` atau `{ "status": "invalid" }`.
- Jika hash mismatch atau dokumen kedaluwarsa, kembalikan `status:"invalid"` + alasan.

### Payload Tambahan (CRUD utama)
- POST /auth/register
  - Body: `{ "nama":"Budi", "email":"budi@mail.com", "phone":"0812...", "password":"***", "tipe_orang":"personil", "nrp_nip":"12345", "pangkat":"Aiptu" }`
  - Response: `{ "user_id": 21, "status":"pending_verification" }`

- POST /anggota
  - Body: `{ "user_id":21, "nomor_anggota":"KOP-2026-0001", "status_keanggotaan":"aktif", "alamat_id":501 }`
  - Response: `{ "anggota_id": 11 }`

- POST /simpanan/transactions
  - Body: `{ "anggota_id":11, "simpanan_type_id":2, "jumlah":250000, "jenis_transaksi":"setor", "tanggal":"2026-02-03" }`
  - Response: `{ "id":91001, "status":"posted" }`

- POST /pinjaman
  - Body: `{ "anggota_id":11, "jumlah_pinjaman":15000000, "bunga":1.2, "jangka_waktu":12, "tenor_satuan":"bulan", "tujuan":"Konsumtif" }`
  - Response: `{ "pinjaman_id":301, "status":"submitted" }`

- POST /pinjaman/{id}/approve
  - Body: `{ "approved_by":7, "catatan":"OK" }`
  - Response: `{ "status":"approved" }`

- POST /pinjaman/{id}/disburse
  - Body: `{ "tanggal":"2026-02-05", "jumlah":15000000, "rekening_tujuan":"BCA-xxx" }`
  - Response: `{ "status":"disbursed", "journal_ref":"JRN-001" }`

- POST /pinjaman/{id}/angsuran
  - Body: `{ "angsuran_ke":1, "jumlah":1250000, "bunga":150000, "denda":0, "tanggal_bayar":"2026-03-05", "metode":"bank_transfer" }`
  - Response: `{ "id":9901, "status":"posted" }`

- POST /products
  - Body: `{ "sku":"PRD-001", "nama":"Beras 5kg", "kategori_id":2, "unit_id":1, "harga_jual":60000, "batch_enabled":true, "expiry_enabled":true }`
  - Response: `{ "product_id":401 }`

- POST /sales-orders
  - Body: `{ "customer_id":20, "items":[{"product_id":401,"qty":2,"harga":60000,"batch_no":"BCH-01","expiry_date":"2026-08-01"}], "metode_pembayaran":"cash" }`
  - Response: `{ "so_id":1301, "status":"draft" }`

- POST /returns
  - Body: `{ "ref_type":"so", "ref_id":1301, "items":[{"product_id":401,"qty":1,"batch_no":"BCH-01","reason":"rusak"}] }`
  - Response: `{ "return_id":1401, "status":"received" }`

- POST /config/simpanan-wajib
  - Body: `{ "jumlah_wajib_bulanan":250000, "tanggal_jatuh_tempo":10 }`
  - Response: `{ "status":"ok" }`

- POST /returns/{id}/approve
  - Body: `{ "approved_by":7, "catatan":"OK" }`
  - Response: `{ "status":"approved" }`

- POST /pinjaman/{id}/writeoff
  - Body: `{ "alasan":"gagal bayar", "approved_by":9, "lampiran_id":7001 }`
  - Response: `{ "status":"written_off", "journal_ref":"JRN-999" }`

- POST /config/simpanan-types
  - Body: `{ "nama":"Simpanan Wajib", "jenis":"wajib", "nominal":250000, "per_bulan":true }`
  - Response: `{ "simpanan_type_id":5 }`

- POST /journal/auto
  - Body: `{ "event_type":"pinjaman_disburse", "ref_type":"pinjaman", "ref_id":301 }`
  - Response: `{ "journal_entry_id":8001, "status":"posted" }`

- POST /inventory/adjust
  - Body: `{ "product_id":1, "location_id":10, "qty_delta":-2, "reason":"shrinkage", "batch_no":"BCH-001", "serial_no":null, "expiry_date":"2026-06-01" }`
  - Response 201: `{ "movement_id": 9002, "status": "posted" }`

- POST /inventory/move
  - Body: `{ "product_id":1, "from_location_id":10, "to_location_id":12, "qty":50, "batch_no":"BCH-001", "expiry_date":"2026-06-01", "reason":"transfer_gudang", "ref_type":"so", "ref_id":123 }`
  - Response 201: `{ "movement_id": 9001, "status": "posted" }`

- POST /assets/{id}/assign
  - Body: `{ "assigned_to":7, "lokasi_id":12, "tanggal":"2026-02-10" }`
  - Response: `{ "status":"assigned" }`

- POST /assets/{id}/move
  - Body: `{ "from_lokasi_id":12, "to_lokasi_id":15, "tanggal":"2026-03-01" }`
  - Response: `{ "status":"moved" }`

### UI Risiko & Blacklist (siap pakai)
```html
<div class="container py-3" id="risiko-page">
  <div class="d-flex flex-wrap gap-2 mb-2">
    <select id="f-flag" class="form-select form-select-sm" style="width:auto">
      <option value="">Semua flag</option>
      <option value="attention">Perlu perhatian</option>
      <option value="blacklist">Blacklist</option>
    </select>
    <select id="f-bucket" class="form-select form-select-sm" style="width:auto">
      <option value="">Semua bucket</option>
      <option value="30">0-30</option>
      <option value="60">31-60</option>
      <option value="90">>60</option>
    </select>
    <input id="f-q" class="form-control form-control-sm" placeholder="Cari nama/NIK" style="width:200px" />
    <button id="btn-exp-risiko" class="btn btn-outline-primary btn-sm">Ekspor PDF</button>
  </div>

  <div class="d-none d-lg-block">
    <table class="table table-sm" id="tbl-risiko">
      <thead><tr><th>Nama</th><th>No Anggota</th><th>Flag</th><th>Bucket</th><th>Tunggakan</th><th>Alasan</th><th>Aksi</th></tr></thead>
      <tbody></tbody>
    </table>
  </div>
  <div class="d-lg-none" id="card-risiko"></div>
</div>

<script>
function loadRisiko(){
  const p = $.param({flag:$('#f-flag').val(), bucket:$('#f-bucket').val(), q:$('#f-q').val()});
  $.getJSON('/api/v1/risiko/anggota?'+p, res => {
    const $tb = $('#tbl-risiko tbody').empty();
    const $card = $('#card-risiko').empty();
    res.items?.forEach(it => {
      const row = `<tr><td>${it.nama}</td><td>${it.nomor_anggota}</td><td>${it.risk_flag}</td><td>${it.bucket}</td><td>${fmtIDR(it.tunggakan||0)}</td><td>${it.risk_reason||''}</td><td><button class="btn btn-sm btn-outline-primary" data-id="${it.id}">Detail</button></td></tr>`;
      $tb.append(row);
      $card.append(`<div class="card mb-2"><div class="card-body"><div class="d-flex justify-content-between"><div>${it.nama}</div><span class="badge bg-${it.risk_flag==='blacklist'?'danger':'warning'}">${it.risk_flag}</span></div><div>No: ${it.nomor_anggota}</div><div>Bucket: ${it.bucket}</div><div>Tunggakan: ${fmtIDR(it.tunggakan||0)}</div><small>${it.risk_reason||''}</small></div></div>`);
    });
  });
}
$('#f-flag,#f-bucket').on('change', loadRisiko);
$('#f-q').on('input', _.debounce(loadRisiko,300));
$('#btn-exp-risiko').on('click', () => window.location = '/api/v1/risiko/anggota/export?format=pdf');
loadRisiko();
</script>
```

### CRUD & Model Ringkas per Modul
- Anggota & Identitas: Create (register user+anggota), Read (list/filter by status/risk), Update (profil/status/risk_flag/role), Delete (soft delete/disable); tabel `users`, `identities`, `anggota`, `anggota_status_history`, `user_roles`, `addresses`.
- Pengurus/Pengawas: Create (assign user+periode), Read, Update (perpanjang/akhiri), Delete (nonaktifkan); tabel `pengurus`, `pengawas`, `pengurus_history`.
- Simpanan: Create jenis (`simpanan_types`), Create transaksi (`simpanan_transactions` setor/tarik); Update hanya void/unpost dengan audit; Read saldo/riwayat; tidak ada hard delete (soft flag).
- Pinjaman: Create pengajuan (`pinjaman`), Update status (approve/reject/disburse), Create jadwal (`pinjaman_angsuran`), Create pembayaran angsuran, Update restruktur/reschedule, Write-off (status + jurnal); Read aging/riwayat; Delete tidak diperbolehkan (gunakan cancel sebelum approve).
- Produk/Inventory: CRUD `products`, `product_categories`, `product_units`, `inventory_locations`; Create/Read `inventory_movements` (transfer/adjust), `inventory_stocks`; Update stok hanya via movement/adjust; Delete produk hanya jika tidak ada transaksi (atau set inactive).
- Penjualan (SO): Create `sales_orders` + `sales_details`; Update status (draft→approved→fulfilled→paid); Return via `returns`; Delete hanya draft.
- Pembelian (PO): Create `purchase_orders` + `po_details`; Update status (draft→approved→released); GR (`goods_receipts`), Invoice (`supplier_invoices`); Delete hanya draft/cancel sebelum GR.
- Aset: CRUD `fixed_assets`; Move/Assign (`asset_movements`/assign), Maintenance (`asset_maintenance`), Documents (`asset_documents`), Depreciation (`asset_depreciations`); Delete tidak diizinkan jika sudah ada jurnal (set inactive).
- Risiko/Blacklist: set/unset `risk_flag`, riwayat di `anggota_risk_history`; Read daftar at-risk/blacklist; Delete tidak berlaku (audit semua perubahan).
- Attachments/Print: Create attachment per dokumen cetak; Read/download; Delete tidak dianjurkan (arsip permanen) kecuali admin dengan audit.

### Kartu & Nomor Anggota
- Setiap anggota memiliki nomor unik `nomor_anggota` (format: prefix koperasi + tahun + sequence). Disimpan di tabel `anggota` dan muncul di semua dokumen transaksi/print.
- Opsi kartu anggota: generate kartu digital (PDF dengan barcode nomor anggota) dan cetak fisik; endpoint `POST /anggota/{id}/card` → simpan ke `attachments` + barcode/QR untuk verifikasi anggota.

### Monitoring Blacklist & At-Risk
```html
<div class="card mt-3" id="export-kasir">
  <div class="card-header">Ekspor</div>
  <div class="card-body d-flex flex-wrap gap-2">
    <select id="sel-coop-kasir" class="form-select form-select-sm" style="width:auto">
      <option value="1">Koperasi A</option>
    </select>
    <select id="sel-bu-kasir" class="form-select form-select-sm" style="width:auto">
      <option value="2">Unit Ritel</option>
    </select>
    <button id="btn-exp-batch" class="btn btn-outline-primary">Unduh Batch Payroll</button>
    <button id="btn-exp-simpanan" class="btn btn-outline-primary">Unduh Laporan Simpanan</button>
  </div>
</div>

<script>
$('#btn-exp-batch').on('click', () => {
  const coopId = $('#sel-coop-kasir').val();
  const buId = $('#sel-bu-kasir').val();
  window.location = `/api/v1/kasir/payroll-batch/export?coop_id=${coopId}&business_unit_id=${buId}&format=xlsx`;
});

$('#btn-exp-simpanan').on('click', () => {
  const coopId = $('#sel-coop-kasir').val();
  const buId = $('#sel-bu-kasir').val();
  window.location = `/api/v1/reports/simpanan?periode=2026-01&format=xlsx&scope=total&coop_id=${coopId}&business_unit_id=${buId}`;
});
</script>
```

### Variasi Desktop (layout multi kolom)
- Pengurus desktop: gunakan grid 3 kolom (`col-lg-4`) untuk KPI kiri, approval tengah (`col-lg-4`), agenda + risk kanan (`col-lg-4`). Tambah tabel aging pinjaman di bawah approval (full width `col-12`).
- Kasir desktop: dua kolom (`col-lg-6`); kiri: kas harian + form setoran/angsuran; kanan: upload payroll + status batch. Tabel batch bisa full width di bawah (row baru) bila > md.

#### Contoh Tabel Aging (Pengurus desktop)
```html
<div class="card mt-3" id="table-aging">
  <div class="card-header">Aging Pinjaman</div>
  <div class="table-responsive">
    <table class="table table-sm mb-0">
      <thead><tr><th>Range</th><th>Pokok</th><th>%</th></tr></thead>
      <tbody id="aging-body"></tbody>
    </table>
  </div>
</div>

<script>
$.getJSON('/api/v1/pinjaman/aging?coop_id=1&business_unit_id=2', d => {
  const total = d.total_pokok || 1;
  d.buckets.forEach(b => {
    const pct = ((b.pokok/total)*100).toFixed(1);
    $('#aging-body').append(`<tr><td>${b.range}</td><td>${b.pokok.toLocaleString()}</td><td>${pct}%</td></tr>`);
  });
});
</script>
```

#### Contoh Tabel Batch Payroll (Kasir desktop)
```html
<div class="card mt-3" id="table-batch">
  <div class="card-header">Batch Payroll</div>
  <div class="table-responsive">
    <table class="table table-sm mb-0">
      <thead><tr><th>ID</th><th>Status</th><th>Total Potong</th><th>Valid</th></tr></thead>
      <tbody id="batch-body"></tbody>
    </table>
  </div>
</div>

<script>
$.getJSON('/api/v1/kasir/payroll-batch?coop_id=1&business_unit_id=2&limit=20', res => {
  res.items?.forEach(b => {
    $('#batch-body').append(`<tr><td>${b.id}</td><td>${b.status}</td><td>${(b.total_potong||0).toLocaleString()}</td><td>${b.valid||'-'}</td></tr>`);
  });
});
</script>
```

## Contoh Markup Dashboard (Pengurus, mobile-first)
```html
<div id="dash-pengurus" class="container py-3">
  <div class="row g-2" id="pengurus-kpi-row">
    <div class="col-12 col-md-6">
      <div id="pengurus-kpi-cash" class="card"><div class="card-body">Kas+Bank: <span id="kpi-cash-val">-</span></div></div>
    </div>
    <div class="col-12 col-md-6">
      <div id="pengurus-kpi-loan" class="card"><div class="card-body">Piutang: <span id="kpi-loan-val">-</span></div></div>
    </div>
    <div class="col-12 col-md-6">
      <div id="pengurus-kpi-simpanan" class="card"><div class="card-body">Simpanan: <span id="kpi-simpanan-val">-</span></div></div>
    </div>
    <div class="col-12 col-md-6">
      <div id="pengurus-kpi-shu" class="card"><div class="card-body">SHU Terutang: <span id="kpi-shu-val">-</span></div></div>
    </div>
  </div>

  <div id="approval-queue" class="card mt-3">
    <div class="card-header">Approval Pending</div>
    <div class="list-group list-group-flush" id="approval-list"></div>
  </div>

  <div id="agenda-rapat" class="card mt-3">
    <div class="card-header">Agenda Rapat/Voting</div>
    <ul class="list-group list-group-flush" id="agenda-list"></ul>
  </div>

  <div id="risk-alerts" class="alert alert-warning mt-3 d-none">Risiko: ...</div>
</div>

<script>
$(function(){
  $.getJSON('/api/v1/cashbank/summary', d => $('#kpi-cash-val').text('Rp ' + d.cash.toLocaleString()));
  $.getJSON('/api/v1/pinjaman/aging', d => $('#kpi-loan-val').text('Rp ' + d.total_pokok.toLocaleString()));
  $.getJSON('/api/v1/simpanan/summary', d => $('#kpi-simpanan-val').text('Rp ' + d.total_wajib.toLocaleString()));
  $.getJSON('/api/v1/liability/shu-pending', d => $('#kpi-shu-val').text('Rp ' + d.total.toLocaleString()));

  $.getJSON('/api/v1/pengurus/approval-queue?status=pending', res => {
    res.items.forEach(item => {
      $('#approval-list').append(`<div class="list-group-item" id="approval-${item.id}">${item.type} - ${item.anggota || item.deskripsi} - Rp ${item.jumlah?.toLocaleString?.() || '-'} <button class="btn btn-sm btn-success float-end" data-id="${item.id}">Approve</button></div>`);
    });
  });

  $.getJSON('/api/v1/rapat/upcoming', res => {
    res.items.forEach(r => $('#agenda-list').append(`<li class="list-group-item" id="agenda-${r.id}">${r.judul} - ${r.tanggal}</li>`));
  });
});
</script>
```

## Contoh Markup Dashboard (Kasir/Juru Bayar, mobile-first)
```html
<div id="dash-kasir" class="container py-3">
  <div class="card mb-2" id="kasir-kas-harian"><div class="card-body">Kas hari ini: <span id="kas-harian-val">-</span></div></div>

  <div class="card mb-2">
    <div class="card-header">Setoran / Angsuran</div>
    <div class="card-body">
      <input id="kasir-member-code" class="form-control mb-2" placeholder="Kode/Nama Anggota" />
      <input id="kasir-amount" class="form-control mb-2" placeholder="Jumlah" />
      <button id="kasir-form-bayar" class="btn btn-primary w-100">Catat Pembayaran</button>
    </div>
  </div>

  <div class="card mb-2" id="kasir-payroll-card">
    <div class="card-header">Upload Payroll Deduction</div>
    <div class="card-body">
      <input type="file" id="kasir-payroll-upload" class="form-control" />
      <button id="btn-upload-payroll" class="btn btn-outline-primary w-100 mt-2">Upload</button>
    </div>
  </div>

  <div class="card" id="kasir-rekon-status">
    <div class="card-header">Status Batch</div>
    <div class="list-group list-group-flush" id="batch-list"></div>
  </div>
</div>

<script>
$(function(){
  $.getJSON('/api/v1/cashbank/summary', d => $('#kas-harian-val').text('Rp ' + (d.cash || 0).toLocaleString()));

  $('#btn-upload-payroll').on('click', function(){
    const formData = new FormData();
    formData.append('file', $('#kasir-payroll-upload')[0].files[0]);
    $.ajax({url:'/api/v1/kasir/payroll-upload', method:'POST', data:formData, processData:false, contentType:false})
      .done(res => $('#batch-list').prepend(`<div class="list-group-item" id="batch-${res.batch_id}">Batch ${res.batch_id} (${res.status})</div>`));
  });

  $.getJSON('/api/v1/kasir/payroll-batch?limit=5', res => {
    res.items?.forEach(b => $('#batch-list').append(`<div class="list-group-item" id="batch-${b.id}">Batch ${b.id} - ${b.status}</div>`));
  });
});
</script>
```

## Payload Tambahan (Voting & Ekspor)
- POST /voting/{id}/ballot
  - Body: `{ "pilihan": "Setuju" }`
  - Response 201: `{ "status": "recorded", "timestamp": "2026-02-03T01:02:00Z" }`

- GET /voting/{id}/export
  - Query: `format=pdf|xlsx&coop_id=...&business_unit_id=...`
  - Response 200: file stream (Content-Type sesuai format); metadata JSON opsional `{ "ready": true, "url": "https://.../voting-9.pdf" }`

- GET /reports/keuangan
  - Query: `periode=2026-01&format=pdf|xlsx&type=neraca|labarugi|aruskas&coop_id=...&business_unit_id=...`
  - Response 200: file stream atau `{ "ready": true, "url": "https://.../neraca-2026-01.pdf" }`

- GET /reports/simpanan
  - Query: `periode=2026-01&format=pdf|xlsx&scope=per-anggota|total&coop_id=...&business_unit_id=...`
  - Response 200: file stream atau `{ "ready": true, "url": "https://.../simpanan-2026-01.xlsx" }`

- GET /reports/pinjaman
  - Query: `periode=2026-01&format=pdf|xlsx&type=aging|kolektibilitas|angsuran&coop_id=...&business_unit_id=...`
  - Response 200: file stream atau `{ "ready": true, "url": "https://.../pinjaman-aging-2026-01.pdf" }`

## Template Modul per Tipe & Dampak Skema
- Tabel konfigurasi: `coop_types`, `coop_type_modules` (module_code, required/optional), `coop_business_units` (per koperasi), `business_unit_modules`, `feature_flags`.
- Tipe Simpan Pinjam (baseline): wajib `anggota`, `simpanan`, `pinjaman`, `shu`, `akuntansi`, `voting`; opsi `agen penjualan` jika ada usaha ritel; DB: aktifkan tabel simpanan/pinjaman/SHU; API: endpoint pinjaman/simpanan selalu tersedia.
- Tipe Jasa: modul `service_catalog`, `service_orders`, SLA/ticketing; DB: tabel services, service_orders, sla; API: `POST /services`, `POST /service-orders`, laporan pendapatan jasa; COA segment untuk pendapatan jasa.
- Tipe Serba Usaha: multi-unit ritel + jasa; DB: `business_units` wajib, produk per unit, penjualan per unit; API: `X-Business-Unit` header/param; GL memakai dimensi unit untuk segmentasi laporan.
- Tipe Produksi/Produsen: modul `bill_of_materials`, `work_orders`, `inventory`; DB: bom, wo, inventory movements; API: `POST /bom`, `POST /work-orders`, `POST /inventory/move`; GL: akun WIP, persediaan.
- Tipe Pemasaran: modul `suppliers`, `purchase_orders`, `partners`, `contracts`; DB: suppliers, PO, contracts; API: `POST /suppliers`, `POST /po`, `POST /contracts`; GL: HPP, hutang usaha.
- Dampak API/feature toggle: middleware cek `feature_flags` per koperasi/unit; jika modul off, endpoint return 403/404; dokumentasi OpenAPI ditag per modul untuk filtering.
- Migrasi modular: setiap modul punya skrip migrasi terpisah; aktivasi modul menjalankan batch migrasi terkait; rollback per modul.
