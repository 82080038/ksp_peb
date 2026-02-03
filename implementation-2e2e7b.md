# Implementasi Robustness & Notifikasi (Rencana Singkat)
Ringkasan: Rencana eksekusi bertahap untuk implementasi face match opsional, notifikasi broker dengan scheduler, uji restore rutin, review pajak berkala, dan observabilitas biaya/performa.

## Scope
- Face match helper lokal (tanpa Dukcapil) untuk KYC opsional.
- Notifikasi via email default, broker self-host dengan scheduler pemeliharaan.
- Helper uji restore rutin ke non-prod + checksum.
- Scheduler review pajak PKP/PPN berkala.
- Observabilitas biaya/performa saat go-live.

## Langkah
1) Face Match (opsional)
   - Pilih library (InsightFace/FaceNet/OpenCV) dan tetapkan threshold (>=0.6);
   - Endpoint/worker simpan skor & keputusan; audit log untuk override manual;
   - Tambah flag config `kyc.face_match_enabled` dan threshold di config table.

2) Notifikasi
   - Set email sebagai kanal default OTP; SMS/WA hanya bila diminta/urgent;
   - Konfigurasi broker: 1-2 partisi per event, batch.size 16-32KB, linger 5-10ms;
   - Scheduler mingguan broker: rolling restart, cleanup log/segment, disk check, alert lag/disk;
   - Fallback `pending_notifications` jika broker down.

3) Backup & Restore
   - Script terjadwal restore sampel backup ke env non-prod; checksum hasil;
   - Alarm jika gagal; log hasil restore.

4) Review Pajak
   - Job bulanan cek konfigurasi PKP/PPN/e-faktur; update config tarif/threshold;
   - Kirim alert jika ada perubahan regulasi.

5) Observabilitas Go-Live
   - Tetapkan target TPS puncak, error rate, dan budget biaya notifikasi/PG;
   - Buat alert jika melampaui batas; tambahkan dashboard APM + biaya.

## Deliverable
- Config & kode helper face match opsional + audit logging.
- Pipeline notifikasi dengan scheduler pemeliharaan broker.
- Job uji restore otomatis dan laporan hasil.
- Job review pajak berkala + alert.
- Dashboard/alert observabilitas biaya & performa.
