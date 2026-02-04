-- Create cooperatives table if not exists
CREATE TABLE IF NOT EXISTS cooperatives (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(255) NOT NULL,
    jenis JSON NOT NULL,
    badan_hukum VARCHAR(100) NOT NULL,
    tanggal_pendirian DATE NOT NULL,
    npwp VARCHAR(20),
    alamat_legal TEXT,
    kontak_resmi VARCHAR(20) NOT NULL,
    logo VARCHAR(255),
    periode_tahun_buku VARCHAR(20) DEFAULT 'calendar',
    simpanan_pokok DECIMAL(15,2) DEFAULT 0,
    simpanan_wajib DECIMAL(15,2) DEFAULT 0,
    bunga_pinjaman DECIMAL(5,2) DEFAULT 12,
    denda_telat DECIMAL(5,2) DEFAULT 2,
    periode_shu VARCHAR(20) DEFAULT 'yearly',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_cooperatives_nama (nama),
    INDEX idx_cooperatives_created_by (created_by)
);
