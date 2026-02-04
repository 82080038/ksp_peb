-- Create cooperative financial settings table for yearly data
CREATE TABLE IF NOT EXISTS cooperative_financial_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cooperative_id INT NOT NULL,
    tahun_buku YEAR NOT NULL,
    periode_mulai DATE NOT NULL,
    periode_akhir DATE NOT NULL,
    simpanan_pokok DECIMAL(15,2) DEFAULT 0,
    simpanan_wajib DECIMAL(15,2) DEFAULT 0,
    bunga_pinjaman DECIMAL(5,2) DEFAULT 12,
    denda_telat DECIMAL(5,2) DEFAULT 2,
    periode_shu ENUM('yearly', 'semi_annual', 'quarterly') DEFAULT 'yearly',
    status ENUM('active', 'inactive', 'closed') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT,
    FOREIGN KEY (cooperative_id) REFERENCES cooperatives(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    UNIQUE KEY unique_cooperative_year (cooperative_id, tahun_buku),
    INDEX idx_cooperative_year (cooperative_id, tahun_buku),
    INDEX idx_tahun_buku (tahun_buku)
);

-- Modify cooperatives table to remove yearly columns
ALTER TABLE cooperatives 
DROP COLUMN IF EXISTS periode_tahun_buku,
DROP COLUMN IF EXISTS simpanan_pokok,
DROP COLUMN IF EXISTS simpanan_wajib,
DROP COLUMN IF EXISTS bunga_pinjaman,
DROP COLUMN IF EXISTS denda_telat,
DROP COLUMN IF EXISTS periode_shu;

-- Insert default financial settings for existing cooperatives
INSERT INTO cooperative_financial_settings (
    cooperative_id, 
    tahun_buku, 
    periode_mulai, 
    periode_akhir,
    simpanan_pokok,
    simpanan_wajib,
    bunga_pinjaman,
    denda_telat,
    periode_shu,
    created_by
)
SELECT 
    id,
    YEAR(CURRENT_DATE) as tahun_buku,
    CONCAT(YEAR(CURRENT_DATE), '-01-01') as periode_mulai,
    CONCAT(YEAR(CURRENT_DATE), '-12-31') as periode_akhir,
    100000 as simpanan_pokok,
    50000 as simpanan_wajib,
    12.00 as bunga_pinjaman,
    2.00 as denda_telat,
    'yearly' as periode_shu,
    created_by
FROM cooperatives
WHERE id NOT IN (
    SELECT cooperative_id FROM cooperative_financial_settings 
    WHERE tahun_buku = YEAR(CURRENT_DATE)
);
