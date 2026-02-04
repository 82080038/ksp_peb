-- Fix Database Structure Issues

-- 1. Remove redundant user_addresses table
DROP TABLE IF EXISTS alamat_db.user_addresses;

-- 2. Add cooperative location links
ALTER TABLE coop_db.cooperatives 
ADD COLUMN province_id INT NULL,
ADD COLUMN regency_id INT NULL,
ADD COLUMN district_id INT NULL,
ADD COLUMN village_id INT NULL,
ADD INDEX idx_cooperative_province (province_id),
ADD INDEX idx_cooperative_regency (regency_id),
ADD INDEX idx_cooperative_district (district_id),
ADD INDEX idx_cooperative_village (village_id);

-- Add foreign keys (optional - can be added later if needed)
-- ALTER TABLE coop_db.cooperatives 
-- ADD CONSTRAINT fk_coop_province FOREIGN KEY (province_id) REFERENCES alamat_db.provinces(id),
-- ADD CONSTRAINT fk_coop_regency FOREIGN KEY (regency_id) REFERENCES alamat_db.regencies(id),
-- ADD CONSTRAINT fk_coop_district FOREIGN KEY (district_id) REFERENCES alamat_db.districts(id),
-- ADD CONSTRAINT fk_coop_village FOREIGN KEY (village_id) REFERENCES alamat_db.villages(id);

-- 3. Add cooperative_id to people_db.addresses for cooperative addresses
ALTER TABLE people_db.addresses 
ADD COLUMN cooperative_id INT NULL,
ADD INDEX idx_address_cooperative (cooperative_id);

-- Add foreign key (optional)
-- ALTER TABLE people_db.addresses 
-- ADD CONSTRAINT fk_address_cooperative FOREIGN KEY (cooperative_id) REFERENCES coop_db.cooperatives(id);

-- 4. Update existing cooperative records with location data from registration
-- This will be handled by the registration process

-- 5. Create view for complete cooperative information
CREATE OR REPLACE VIEW v_cooperative_complete AS
SELECT 
    c.id,
    c.nama,
    c.jenis,
    c.badan_hukum,
    c.tanggal_pendirian,
    c.npwp,
    c.alamat_legal,
    c.kontak_resmi,
    c.logo,
    c.created_by,
    c.created_at,
    c.updated_at,
    p.name as province_name,
    r.name as regency_name,
    d.name as district_name,
    v.name as village_name,
    u.nama as admin_name,
    u.email as admin_email,
    u.phone as admin_phone
FROM coop_db.cooperatives c
LEFT JOIN alamat_db.provinces p ON c.province_id = p.id
LEFT JOIN alamat_db.regencies r ON c.regency_id = r.id
LEFT JOIN alamat_db.districts d ON c.district_id = d.id
LEFT JOIN alamat_db.villages v ON c.village_id = v.id
LEFT JOIN coop_db.users cu ON c.created_by = cu.id
LEFT JOIN people_db.users u ON cu.user_db_id = u.id;
