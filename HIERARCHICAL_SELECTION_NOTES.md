# Catatan Implementasi Hierarki Instansi → Pekerjaan → Golongan → Pangkat

## Ringkasan Perubahan
- **dashboard.php**
  - Modal "Tambah Anggota" kini memiliki dropdown **Jenis Instansi**, **Pekerjaan**, **Golongan Pangkat**, dan **Pangkat**.
  - Logika cascading berbasis JavaScript ditambahkan (placeholder data statis; siap diganti API backend) untuk memuat opsi berurutan dan menonaktifkan field ketika tidak relevan.
  - Reset otomatis setiap kali modal dibuka agar opsi dimuat ulang dan field pangkat/golongan disembunyikan saat tidak diperlukan.
- **anggota.php**
  - Form Tambah Anggota di dashboard/anggota (public dashboard view) telah disiapkan dengan pola serupa (dropdown Jenis Instansi, Pekerjaan, Golongan Pangkat, Pangkat) dan fungsi JS dengan data statis placeholder.

## Alur Cascading
1) **Jenis Instansi** dipilih → mengisi **Pekerjaan** sesuai instansi.
2) **Pekerjaan** dipilih → jika `hasRank=true`, tampilkan **Golongan Pangkat**; jika tidak, sembunyikan golongan/pangkat dan pakai teks "Tidak memerlukan pangkat".
3) **Golongan Pangkat** dipilih → mengisi **Pangkat** sesuai golongan.

## Catatan Integrasi Backend
- Data di JS saat ini **statis placeholder**. Ganti ke API referensi (mis. `references.php?action=institution_types|occupations|rank_groups|ranks`) dan map-kan:
  - Instansi → occupations
  - Occupation → rank_groups
  - Rank_group → ranks (gunakan singkatan/abbr untuk tampilan pangkat jika ada)
- Pastikan endpoint mengembalikan `has_rank` untuk memutuskan apakah field pangkat perlu ditampilkan.

## Lokasi Kode Utama
- Modal & form: `dashboard.php` (modalAddMember) — bagian form dan dropdown baru ada di sekitar blok modal Tambah Anggota.
- JS cascading: `dashboard.php` sebelum penutup `</body>` — blok script dengan konstanta `INSTANSI_OPTIONS`, `PEKERJAAN_BY_INSTANSI`, `GOLONGAN_BY_PEKERJAAN`, `PANGKAT_BY_GOLONGAN`, serta fungsi `loadInstansi`, `loadPekerjaan`, `loadGolongan`, `loadPangkat`, `reset*` dan hook `shown.bs.modal`.
- Implementasi pola serupa di `src/public/dashboard/anggota.php` (frontend anggota) dengan fungsi dan dropdown `add_institution_type_*`, `add_occupation_*`, `add_rank_group_*`, `add_rank_*`.

## Langkah Lanjutan yang Disarankan
- Sambungkan dropdown ke API backend yang membaca tabel referensi instansi/pekerjaan/golongan/pangkat.
- Validasi server-side: tolak submission jika pekerjaan memerlukan pangkat tetapi pangkat/golongan kosong.
- Urutkan opsi sesuai `sort_order` dari DB dan gunakan singkatan (abbr) untuk label pangkat.
- Tambahkan handling loading/error (spinner/notifikasi) saat fetch API.
