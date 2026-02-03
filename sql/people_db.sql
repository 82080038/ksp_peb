-- Create People DB
/*
People Database (people_db)

This database stores comprehensive information about individuals (people) for a cooperative application.
It includes user accounts, personal identities (KYC), addresses, contacts, education, employment, health records, and more.
Enhanced with GDPR-compliant features for personal data management: consents, audit logs, and data subject requests.

Master tables for normalization: genders, marital_statuses, religions, ethnicities, blood_types.
References external database: alamat_db for Indonesian addresses (provinces, cities, villages).

Key Tables:
- users: Basic user authentication and status
- identities: KYC data with FK to master tables for gender, marital status, religion, ethnicity, blood type
- physical_attributes: Physical characteristics like height, weight, hair/eye/skin color
- addresses: User addresses with geo coordinates and references to alamat_db
- contact_emails, contact_phones: Multiple email and phone contacts
- skills, certifications, languages: Professional and competency info
- social_profiles, emergency_contacts, references: Social and emergency contacts
- education_records, employment_records: Educational and employment history
- life_events, health_records: Life events and health information
- preferences, financial_info: Personal preferences and financial details
- consents: User consents for data processing (GDPR compliance)
- audit_logs: Audit trail for access and modifications to personal data
- data_subject_requests: Handling GDPR rights requests (access, rectification, erasure)
- allowed_databases: Registry of external databases allowed to connect

Schema normalized to 3NF for data integrity and reduced redundancy.
*/
CREATE DATABASE IF NOT EXISTS people_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE people_db;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE,
    phone VARCHAR(20),
    password_hash VARCHAR(255) NOT NULL,
    status ENUM('active', 'inactive', 'pending') DEFAULT 'pending',
    preferred_channel ENUM('email', 'sms', 'wa', 'push') DEFAULT 'email',
    preferred_language VARCHAR(10) DEFAULT 'id',
    timezone VARCHAR(50) DEFAULT 'Asia/Jakarta',
    mfa_enabled BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Master tables
CREATE TABLE genders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(20) UNIQUE NOT NULL
);

CREATE TABLE marital_statuses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL
);

CREATE TABLE religions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL
);

CREATE TABLE ethnicities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL
);

CREATE TABLE blood_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(10) UNIQUE NOT NULL
);

-- Insert initial data
INSERT INTO genders (name) VALUES ('laki-laki'), ('perempuan');
INSERT INTO marital_statuses (name) VALUES ('belum_kawin'), ('kawin'), ('cerai_hidup'), ('cerai_mati');
INSERT INTO religions (name) VALUES ('Islam'), ('Kristen'), ('Katolik'), ('Hindu'), ('Buddha'), ('Konghucu'), ('Lainnya');
INSERT INTO ethnicities (name) VALUES ('Jawa'), ('Sunda'), ('Batak'), ('Minangkabau'), ('Betawi'), ('Lainnya');
INSERT INTO blood_types (name) VALUES ('A'), ('B'), ('AB'), ('O'), ('A+'), ('A-'), ('B+'), ('B-'), ('AB+'), ('AB-'), ('O+'), ('O-');

-- Identities table (KYC data)
CREATE TABLE identities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nik VARCHAR(20) UNIQUE,
    nomor_kk VARCHAR(20),
    nama_lengkap VARCHAR(255),
    tempat_lahir VARCHAR(100),
    tanggal_lahir DATE,
    status ENUM('draft', 'complete', 'verified') DEFAULT 'draft',
    gender_id INT,
    marital_status_id INT,
    religion_id INT,
    ethnicity_id INT,
    kewarganegaraan VARCHAR(100) DEFAULT 'Indonesia',
    blood_type_id INT,
    nomor_passport VARCHAR(20),
    nomor_sim VARCHAR(20),
    nik_encrypted VARBINARY(512),
    passport_encrypted VARBINARY(512),
    sim_encrypted VARBINARY(512),
    pep_flag BOOLEAN DEFAULT FALSE,
    pep_source VARCHAR(255),
    risk_score ENUM('low', 'medium', 'high') DEFAULT 'low',
    risk_reason TEXT,
    kyc_last_review_at TIMESTAMP NULL,
    kyc_next_review_at TIMESTAMP NULL,
    kyc_completeness TINYINT UNSIGNED,
    foto_path VARCHAR(255),
    verified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (gender_id) REFERENCES genders(id),
    FOREIGN KEY (marital_status_id) REFERENCES marital_statuses(id),
    FOREIGN KEY (religion_id) REFERENCES religions(id),
    FOREIGN KEY (ethnicity_id) REFERENCES ethnicities(id),
    FOREIGN KEY (blood_type_id) REFERENCES blood_types(id),
    CHECK (nik IS NULL OR nik REGEXP '^[0-9]{16}$'),
    CHECK (nomor_kk IS NULL OR nomor_kk REGEXP '^[0-9]{16}$'),
    CHECK (nomor_passport IS NULL OR nomor_passport REGEXP '^[A-Za-z0-9]{5,20}$'),
    CHECK (nomor_sim IS NULL OR nomor_sim REGEXP '^[A-Za-z0-9]{5,20}$')
);

CREATE INDEX idx_identities_user ON identities(user_id);
CREATE INDEX idx_identities_nomor_kk ON identities(nomor_kk);
CREATE INDEX idx_identities_pep ON identities(pep_flag);
CREATE INDEX idx_identities_risk ON identities(risk_score);
CREATE INDEX idx_identities_kyc_next_review ON identities(kyc_next_review_at);

-- Physical attributes
CREATE TABLE physical_attributes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    tinggi_badan INT,  -- in cm
    berat_badan DECIMAL(5,2),  -- in kg
    warna_rambut VARCHAR(50),
    warna_kulit VARCHAR(50),
    warna_mata VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Identity types
CREATE TABLE identity_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL,  -- e.g., KTP, Passport, SIM
    description TEXT,
    country VARCHAR(100),
    required BOOLEAN DEFAULT FALSE,  -- if mandatory for certain roles
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Seed common identity types
INSERT INTO identity_types (name, description, country, required) VALUES
('KTP', 'Kartu Tanda Penduduk Indonesia', 'Indonesia', TRUE),
('Passport', 'Passport (any country)', NULL, FALSE),
('SIM', 'Surat Izin Mengemudi', 'Indonesia', FALSE);

-- User identity documents
CREATE TABLE user_identity_documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    identity_type_id INT NOT NULL,
    document_number VARCHAR(50) UNIQUE,
    issued_date DATE,
    expiry_date DATE,
    issuing_authority VARCHAR(255),
    document_path VARCHAR(255),  -- path to uploaded document
    verified BOOLEAN DEFAULT FALSE,
    verified_by INT,  -- if verified by someone
    verified_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (identity_type_id) REFERENCES identity_types(id) ON DELETE CASCADE
);

-- Family members
CREATE TABLE family_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    relation ENUM('ayah', 'ibu', 'pasangan', 'anak', 'saudara'),
    name VARCHAR(255),
    date_of_birth DATE,
    occupation VARCHAR(100),
    contact_info VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Education records
CREATE TABLE education_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    institution VARCHAR(255),
    degree VARCHAR(100),
    field_of_study VARCHAR(100),
    start_year YEAR,
    end_year YEAR,
    grade VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE INDEX idx_education_institution ON education_records(institution);

-- Employment records
CREATE TABLE employment_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    company VARCHAR(255),
    position VARCHAR(100),
    industry VARCHAR(100),
    start_date DATE,
    end_date DATE,
    salary DECIMAL(15,2),
    reason_for_leaving TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE INDEX idx_employment_company ON employment_records(company);

-- Life events
CREATE TABLE life_events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    event_type ENUM('kelahiran', 'pernikahan', 'perceraian', 'kematian', 'pindah', 'lainnya'),
    event_date DATE,
    description TEXT,
    location VARCHAR(255),
    related_person VARCHAR(255),  -- e.g., spouse name for marriage
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Health records
CREATE TABLE health_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    record_type ENUM('alergi', 'penyakit', 'obat', 'operasi', 'vaksinasi'),
    description TEXT,
    date_recorded DATE,
    severity VARCHAR(50),
    doctor VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Addresses
CREATE TABLE addresses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    address_type ENUM('home', 'work', 'other') DEFAULT 'home',
    is_primary BOOLEAN DEFAULT FALSE,
    country VARCHAR(100) DEFAULT 'Indonesia',
    province_id INT,  -- references alamat_db.provinces.id
    city_id INT,  -- references alamat_db.cities.id
    village_id INT,  -- references alamat_db.villages.id
    postal_code VARCHAR(20),
    street_address VARCHAR(255),
    alamat_detil VARCHAR(255),  -- detailed address
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE INDEX idx_addresses_region ON addresses(province_id, city_id, village_id);

-- Contact emails
CREATE TABLE contact_emails (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    email VARCHAR(255) NOT NULL,
    type ENUM('personal', 'work', 'other') DEFAULT 'personal',
    is_primary BOOLEAN DEFAULT FALSE,
    email_hash CHAR(128),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CHECK (email REGEXP '^[^@\\s]+@[^@\\s]+\\.[^@\\s]+$')
);

CREATE UNIQUE INDEX uq_contact_emails_user_email ON contact_emails(user_id, email);
CREATE INDEX idx_contact_emails_hash ON contact_emails(email_hash);
CREATE INDEX idx_contact_emails_primary ON contact_emails(user_id, is_primary);

-- Contact phones
CREATE TABLE contact_phones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    phone VARCHAR(20) NOT NULL,
    type ENUM('mobile', 'home', 'work', 'other') DEFAULT 'mobile',
    is_primary BOOLEAN DEFAULT FALSE,
    phone_hash CHAR(128),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CHECK (phone REGEXP '^\\+?[0-9]{8,15}$')
);

CREATE UNIQUE INDEX uq_contact_phones_user_phone ON contact_phones(user_id, phone);
CREATE INDEX idx_contact_phones_hash ON contact_phones(phone_hash);
CREATE INDEX idx_contact_phones_primary ON contact_phones(user_id, is_primary);

-- Skills
CREATE TABLE skills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    skill_name VARCHAR(100) NOT NULL,
    proficiency_level ENUM('beginner', 'intermediate', 'advanced', 'expert') DEFAULT 'intermediate',
    years_of_experience INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Certifications
CREATE TABLE certifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    certification_name VARCHAR(255) NOT NULL,
    issuing_organization VARCHAR(255),
    date_issued DATE,
    expiry_date DATE,
    certificate_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Languages
CREATE TABLE languages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    language VARCHAR(100) NOT NULL,
    proficiency ENUM('basic', 'conversational', 'fluent', 'native') DEFAULT 'conversational',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Social profiles
CREATE TABLE social_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    platform VARCHAR(50) NOT NULL,
    url VARCHAR(255),
    username VARCHAR(100),
    followers_count INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE INDEX idx_social_platform_user ON social_profiles(platform, username);

-- Emergency contacts
CREATE TABLE emergency_contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    relation VARCHAR(100),
    phone VARCHAR(20),
    email VARCHAR(255),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Reference contacts (professional/personal references)
CREATE TABLE reference_contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    relation VARCHAR(100),
    phone VARCHAR(20),
    email VARCHAR(255),
    company VARCHAR(255),
    position VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Travel history
CREATE TABLE travel_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    country VARCHAR(100),
    purpose VARCHAR(100),
    date_from DATE,
    date_to DATE,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Preferences
CREATE TABLE preferences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category VARCHAR(50),
    preference VARCHAR(255),
    details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Financial info
CREATE TABLE financial_info (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type ENUM('income', 'assets', 'debts', 'investments', 'other') DEFAULT 'income',
    amount DECIMAL(15,2),
    currency VARCHAR(10) DEFAULT 'IDR',
    description TEXT,
    date_recorded DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Consents
CREATE TABLE consents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    consent_type VARCHAR(100),
    consented BOOLEAN DEFAULT FALSE,
    consent_date DATE,
    expiry_date DATE,
    purpose TEXT,
    policy_version VARCHAR(50),
    revoked_at TIMESTAMP NULL,
    revoked_reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Audit logs
CREATE TABLE audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action VARCHAR(50),
    performed_by INT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    details TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (performed_by) REFERENCES users(id) ON DELETE SET NULL
);

CREATE INDEX idx_audit_logs_user_time ON audit_logs(user_id, timestamp);

-- Data subject requests
CREATE TABLE data_subject_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    request_type ENUM('access', 'rectification', 'erasure', 'portability', 'restriction', 'objection') NOT NULL,
    status ENUM('pending', 'in_progress', 'fulfilled', 'rejected') DEFAULT 'pending',
    requested_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fulfilled_at TIMESTAMP NULL,
    details TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE INDEX idx_dsr_user_status ON data_subject_requests(user_id, status);

-- Allowed external databases
CREATE TABLE allowed_databases (
    id INT AUTO_INCREMENT PRIMARY KEY,
    db_name VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    allowed BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert initial allowed database
INSERT INTO allowed_databases (db_name, description) VALUES ('alamat_db', 'Database for Indonesian addresses: provinces, cities, villages');

-- Triggers to enforce single primary per user
DELIMITER $$

CREATE TRIGGER trg_addresses_primary BEFORE INSERT ON addresses
FOR EACH ROW
BEGIN
    IF NEW.is_primary THEN
        UPDATE addresses SET is_primary = FALSE WHERE user_id = NEW.user_id;
    END IF;
END$$

CREATE TRIGGER trg_addresses_primary_upd BEFORE UPDATE ON addresses
FOR EACH ROW
BEGIN
    IF NEW.is_primary AND (OLD.is_primary IS NULL OR OLD.is_primary = FALSE) THEN
        UPDATE addresses SET is_primary = FALSE WHERE user_id = NEW.user_id;
    END IF;
END$$

CREATE TRIGGER trg_contact_emails_primary BEFORE INSERT ON contact_emails
FOR EACH ROW
BEGIN
    IF NEW.is_primary THEN
        UPDATE contact_emails SET is_primary = FALSE WHERE user_id = NEW.user_id;
    END IF;
END$$

CREATE TRIGGER trg_contact_emails_primary_upd BEFORE UPDATE ON contact_emails
FOR EACH ROW
BEGIN
    IF NEW.is_primary AND (OLD.is_primary IS NULL OR OLD.is_primary = FALSE) THEN
        UPDATE contact_emails SET is_primary = FALSE WHERE user_id = NEW.user_id;
    END IF;
END$$

CREATE TRIGGER trg_contact_phones_primary BEFORE INSERT ON contact_phones
FOR EACH ROW
BEGIN
    IF NEW.is_primary THEN
        UPDATE contact_phones SET is_primary = FALSE WHERE user_id = NEW.user_id;
    END IF;
END$$

CREATE TRIGGER trg_contact_phones_primary_upd BEFORE UPDATE ON contact_phones
FOR EACH ROW
BEGIN
    IF NEW.is_primary AND (OLD.is_primary IS NULL OR OLD.is_primary = FALSE) THEN
        UPDATE contact_phones SET is_primary = FALSE WHERE user_id = NEW.user_id;
    END IF;
END$$

DELIMITER ;

-- Masked view for limited read access (hides most of nik/kk/passport/sim)
CREATE OR REPLACE VIEW v_identities_masked AS
SELECT
    id,
    user_id,
    CONCAT(REPEAT('X',12), RIGHT(nik,4)) AS nik_masked,
    CONCAT(REPEAT('X',12), RIGHT(nomor_kk,4)) AS kk_masked,
    CONCAT('***', RIGHT(nomor_passport,3)) AS passport_masked,
    CONCAT('***', RIGHT(nomor_sim,3)) AS sim_masked,
    status,
    verified,
    pep_flag,
    risk_score,
    kyc_last_review_at,
    kyc_next_review_at
FROM identities;
