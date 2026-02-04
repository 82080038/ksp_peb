-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Waktu pembuatan: 05 Feb 2026 pada 04.09
-- Versi server: 10.6.23-MariaDB-0ubuntu0.22.04.1
-- Versi PHP: 8.1.2-1ubuntu2.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `people_db`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `addresses`
--

CREATE TABLE `addresses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `address_type` enum('home','work','other') DEFAULT 'home',
  `is_primary` tinyint(1) DEFAULT 0,
  `country` varchar(100) DEFAULT 'Indonesia',
  `province_id` int(11) DEFAULT NULL,
  `city_id` int(11) DEFAULT NULL,
  `village_id` int(11) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `street_address` varchar(255) DEFAULT NULL,
  `alamat_detil` varchar(255) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `cooperative_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Trigger `addresses`
--
DELIMITER $$
CREATE TRIGGER `trg_addresses_primary` BEFORE INSERT ON `addresses` FOR EACH ROW BEGIN
    IF NEW.is_primary THEN
        UPDATE addresses SET is_primary = FALSE WHERE user_id = NEW.user_id;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_addresses_primary_upd` BEFORE UPDATE ON `addresses` FOR EACH ROW BEGIN
    IF NEW.is_primary AND (OLD.is_primary IS NULL OR OLD.is_primary = FALSE) THEN
        UPDATE addresses SET is_primary = FALSE WHERE user_id = NEW.user_id;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `allowed_databases`
--

CREATE TABLE `allowed_databases` (
  `id` int(11) NOT NULL,
  `db_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `allowed` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `allowed_databases`
--

INSERT INTO `allowed_databases` (`id`, `db_name`, `description`, `allowed`, `created_at`) VALUES
(1, 'alamat_db', 'Database for Indonesian addresses: provinces, cities, villages', 1, '2026-02-03 14:13:06');

-- --------------------------------------------------------

--
-- Struktur dari tabel `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(50) DEFAULT NULL,
  `performed_by` int(11) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT current_timestamp(),
  `details` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `blood_types`
--

CREATE TABLE `blood_types` (
  `id` int(11) NOT NULL,
  `name` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `blood_types`
--

INSERT INTO `blood_types` (`id`, `name`) VALUES
(1, 'A'),
(6, 'A-'),
(5, 'A+'),
(3, 'AB'),
(10, 'AB-'),
(9, 'AB+'),
(2, 'B'),
(8, 'B-'),
(7, 'B+'),
(4, 'O'),
(12, 'O-'),
(11, 'O+');

-- --------------------------------------------------------

--
-- Struktur dari tabel `certifications`
--

CREATE TABLE `certifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `certification_name` varchar(255) NOT NULL,
  `issuing_organization` varchar(255) DEFAULT NULL,
  `date_issued` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `certificate_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `consents`
--

CREATE TABLE `consents` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `consent_type` varchar(100) DEFAULT NULL,
  `consented` tinyint(1) DEFAULT 0,
  `consent_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `purpose` text DEFAULT NULL,
  `policy_version` varchar(50) DEFAULT NULL,
  `revoked_at` timestamp NULL DEFAULT NULL,
  `revoked_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `contact_emails`
--

CREATE TABLE `contact_emails` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `type` enum('personal','work','other') DEFAULT 'personal',
  `is_primary` tinyint(1) DEFAULT 0,
  `email_hash` char(128) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ;

--
-- Trigger `contact_emails`
--
DELIMITER $$
CREATE TRIGGER `trg_contact_emails_primary` BEFORE INSERT ON `contact_emails` FOR EACH ROW BEGIN
    IF NEW.is_primary THEN
        UPDATE contact_emails SET is_primary = FALSE WHERE user_id = NEW.user_id;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_contact_emails_primary_upd` BEFORE UPDATE ON `contact_emails` FOR EACH ROW BEGIN
    IF NEW.is_primary AND (OLD.is_primary IS NULL OR OLD.is_primary = FALSE) THEN
        UPDATE contact_emails SET is_primary = FALSE WHERE user_id = NEW.user_id;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `contact_phones`
--

CREATE TABLE `contact_phones` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `type` enum('mobile','home','work','other') DEFAULT 'mobile',
  `is_primary` tinyint(1) DEFAULT 0,
  `phone_hash` char(128) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ;

--
-- Trigger `contact_phones`
--
DELIMITER $$
CREATE TRIGGER `trg_contact_phones_primary` BEFORE INSERT ON `contact_phones` FOR EACH ROW BEGIN
    IF NEW.is_primary THEN
        UPDATE contact_phones SET is_primary = FALSE WHERE user_id = NEW.user_id;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_contact_phones_primary_upd` BEFORE UPDATE ON `contact_phones` FOR EACH ROW BEGIN
    IF NEW.is_primary AND (OLD.is_primary IS NULL OR OLD.is_primary = FALSE) THEN
        UPDATE contact_phones SET is_primary = FALSE WHERE user_id = NEW.user_id;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `data_subject_requests`
--

CREATE TABLE `data_subject_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `request_type` enum('access','rectification','erasure','portability','restriction','objection') NOT NULL,
  `status` enum('pending','in_progress','fulfilled','rejected') DEFAULT 'pending',
  `requested_at` timestamp NULL DEFAULT current_timestamp(),
  `fulfilled_at` timestamp NULL DEFAULT NULL,
  `details` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `education_records`
--

CREATE TABLE `education_records` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `institution` varchar(255) DEFAULT NULL,
  `degree` varchar(100) DEFAULT NULL,
  `field_of_study` varchar(100) DEFAULT NULL,
  `start_year` year(4) DEFAULT NULL,
  `end_year` year(4) DEFAULT NULL,
  `grade` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `emergency_contacts`
--

CREATE TABLE `emergency_contacts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `relation` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `employment_records`
--

CREATE TABLE `employment_records` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `company` varchar(255) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `industry` varchar(100) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `salary` decimal(15,2) DEFAULT NULL,
  `reason_for_leaving` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `ethnicities`
--

CREATE TABLE `ethnicities` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `ethnicities`
--

INSERT INTO `ethnicities` (`id`, `name`) VALUES
(3, 'Batak'),
(5, 'Betawi'),
(1, 'Jawa'),
(6, 'Lainnya'),
(4, 'Minangkabau'),
(2, 'Sunda');

-- --------------------------------------------------------

--
-- Struktur dari tabel `family_members`
--

CREATE TABLE `family_members` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `relation` enum('ayah','ibu','pasangan','anak','saudara') DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `occupation` varchar(100) DEFAULT NULL,
  `contact_info` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `financial_info`
--

CREATE TABLE `financial_info` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('income','assets','debts','investments','other') DEFAULT 'income',
  `amount` decimal(15,2) DEFAULT NULL,
  `currency` varchar(10) DEFAULT 'IDR',
  `description` text DEFAULT NULL,
  `date_recorded` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `genders`
--

CREATE TABLE `genders` (
  `id` int(11) NOT NULL,
  `name` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `genders`
--

INSERT INTO `genders` (`id`, `name`) VALUES
(1, 'laki-laki'),
(2, 'perempuan');

-- --------------------------------------------------------

--
-- Struktur dari tabel `health_records`
--

CREATE TABLE `health_records` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `record_type` enum('alergi','penyakit','obat','operasi','vaksinasi') DEFAULT NULL,
  `description` text DEFAULT NULL,
  `date_recorded` date DEFAULT NULL,
  `severity` varchar(50) DEFAULT NULL,
  `doctor` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `identities`
--

CREATE TABLE `identities` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `nik` varchar(20) DEFAULT NULL,
  `nomor_kk` varchar(20) DEFAULT NULL,
  `nama_lengkap` varchar(255) DEFAULT NULL,
  `tempat_lahir` varchar(100) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `status` enum('draft','complete','verified') DEFAULT 'draft',
  `gender_id` int(11) DEFAULT NULL,
  `marital_status_id` int(11) DEFAULT NULL,
  `religion_id` int(11) DEFAULT NULL,
  `ethnicity_id` int(11) DEFAULT NULL,
  `kewarganegaraan` varchar(100) DEFAULT 'Indonesia',
  `blood_type_id` int(11) DEFAULT NULL,
  `nomor_passport` varchar(20) DEFAULT NULL,
  `nomor_sim` varchar(20) DEFAULT NULL,
  `nik_encrypted` varbinary(512) DEFAULT NULL,
  `passport_encrypted` varbinary(512) DEFAULT NULL,
  `sim_encrypted` varbinary(512) DEFAULT NULL,
  `pep_flag` tinyint(1) DEFAULT 0,
  `pep_source` varchar(255) DEFAULT NULL,
  `risk_score` enum('low','medium','high') DEFAULT 'low',
  `risk_reason` text DEFAULT NULL,
  `kyc_last_review_at` timestamp NULL DEFAULT NULL,
  `kyc_next_review_at` timestamp NULL DEFAULT NULL,
  `kyc_completeness` tinyint(3) UNSIGNED DEFAULT NULL,
  `foto_path` varchar(255) DEFAULT NULL,
  `verified` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `identity_types`
--

CREATE TABLE `identity_types` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `required` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `identity_types`
--

INSERT INTO `identity_types` (`id`, `name`, `description`, `country`, `required`, `created_at`) VALUES
(1, 'KTP', 'Kartu Tanda Penduduk Indonesia', 'Indonesia', 1, '2026-02-03 14:12:57'),
(2, 'Passport', 'Passport (any country)', NULL, 0, '2026-02-03 14:12:57'),
(3, 'SIM', 'Surat Izin Mengemudi', 'Indonesia', 0, '2026-02-03 14:12:57');

-- --------------------------------------------------------

--
-- Struktur dari tabel `languages`
--

CREATE TABLE `languages` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `language` varchar(100) NOT NULL,
  `proficiency` enum('basic','conversational','fluent','native') DEFAULT 'conversational',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `life_events`
--

CREATE TABLE `life_events` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `event_type` enum('kelahiran','pernikahan','perceraian','kematian','pindah','lainnya') DEFAULT NULL,
  `event_date` date DEFAULT NULL,
  `description` text DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `related_person` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `marital_statuses`
--

CREATE TABLE `marital_statuses` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `marital_statuses`
--

INSERT INTO `marital_statuses` (`id`, `name`) VALUES
(1, 'belum_kawin'),
(3, 'cerai_hidup'),
(4, 'cerai_mati'),
(2, 'kawin');

-- --------------------------------------------------------

--
-- Struktur dari tabel `physical_attributes`
--

CREATE TABLE `physical_attributes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tinggi_badan` int(11) DEFAULT NULL,
  `berat_badan` decimal(5,2) DEFAULT NULL,
  `warna_rambut` varchar(50) DEFAULT NULL,
  `warna_kulit` varchar(50) DEFAULT NULL,
  `warna_mata` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `preferences`
--

CREATE TABLE `preferences` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `preference` varchar(255) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `reference_contacts`
--

CREATE TABLE `reference_contacts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `relation` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `religions`
--

CREATE TABLE `religions` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `religions`
--

INSERT INTO `religions` (`id`, `name`) VALUES
(5, 'Buddha'),
(4, 'Hindu'),
(1, 'Islam'),
(3, 'Katolik'),
(6, 'Konghucu'),
(2, 'Kristen'),
(7, 'Lainnya');

-- --------------------------------------------------------

--
-- Struktur dari tabel `skills`
--

CREATE TABLE `skills` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `skill_name` varchar(100) NOT NULL,
  `proficiency_level` enum('beginner','intermediate','advanced','expert') DEFAULT 'intermediate',
  `years_of_experience` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `social_profiles`
--

CREATE TABLE `social_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `platform` varchar(50) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `followers_count` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `travel_history`
--

CREATE TABLE `travel_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `country` varchar(100) DEFAULT NULL,
  `purpose` varchar(100) DEFAULT NULL,
  `date_from` date DEFAULT NULL,
  `date_to` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `status` enum('active','inactive','pending') DEFAULT 'pending',
  `preferred_channel` enum('email','sms','wa','push') DEFAULT 'email',
  `preferred_language` varchar(10) DEFAULT 'id',
  `timezone` varchar(50) DEFAULT 'Asia/Jakarta',
  `mfa_enabled` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `phone`, `password_hash`, `status`, `preferred_channel`, `preferred_language`, `timezone`, `mfa_enabled`, `created_at`, `updated_at`) VALUES
(1, 'ADMIN PALING BAIK DI DUNIA', '82080038@koperasi.com', '081910457868', '$2y$10$N.UC2TY6FU5wt70.9cfHvOeCia0FRj.tGZYmsu57jK/a9il9UPP2e', 'active', 'email', 'id', 'Asia/Jakarta', 0, '2026-02-04 17:27:52', '2026-02-04 20:02:03');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_identity_documents`
--

CREATE TABLE `user_identity_documents` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `identity_type_id` int(11) NOT NULL,
  `document_number` varchar(50) DEFAULT NULL,
  `issued_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `issuing_authority` varchar(255) DEFAULT NULL,
  `document_path` varchar(255) DEFAULT NULL,
  `verified` tinyint(1) DEFAULT 0,
  `verified_by` int(11) DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `v_identities_masked`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `v_identities_masked` (
`id` int(11)
,`user_id` int(11)
,`nik_masked` varchar(16)
,`kk_masked` varchar(16)
,`passport_masked` varchar(6)
,`sim_masked` varchar(6)
,`status` enum('draft','complete','verified')
,`verified` tinyint(1)
,`pep_flag` tinyint(1)
,`risk_score` enum('low','medium','high')
,`kyc_last_review_at` timestamp
,`kyc_next_review_at` timestamp
);

-- --------------------------------------------------------

--
-- Struktur untuk view `v_identities_masked`
--
DROP TABLE IF EXISTS `v_identities_masked`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_identities_masked`  AS SELECT `identities`.`id` AS `id`, `identities`.`user_id` AS `user_id`, concat(repeat('X',12),right(`identities`.`nik`,4)) AS `nik_masked`, concat(repeat('X',12),right(`identities`.`nomor_kk`,4)) AS `kk_masked`, concat('***',right(`identities`.`nomor_passport`,3)) AS `passport_masked`, concat('***',right(`identities`.`nomor_sim`,3)) AS `sim_masked`, `identities`.`status` AS `status`, `identities`.`verified` AS `verified`, `identities`.`pep_flag` AS `pep_flag`, `identities`.`risk_score` AS `risk_score`, `identities`.`kyc_last_review_at` AS `kyc_last_review_at`, `identities`.`kyc_next_review_at` AS `kyc_next_review_at` FROM `identities` ;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_addresses_region` (`province_id`,`city_id`,`village_id`),
  ADD KEY `idx_address_cooperative` (`cooperative_id`);

--
-- Indeks untuk tabel `allowed_databases`
--
ALTER TABLE `allowed_databases`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `db_name` (`db_name`);

--
-- Indeks untuk tabel `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `performed_by` (`performed_by`),
  ADD KEY `idx_audit_logs_user_time` (`user_id`,`timestamp`);

--
-- Indeks untuk tabel `blood_types`
--
ALTER TABLE `blood_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indeks untuk tabel `certifications`
--
ALTER TABLE `certifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `consents`
--
ALTER TABLE `consents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `contact_emails`
--
ALTER TABLE `contact_emails`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_contact_emails_user_email` (`user_id`,`email`),
  ADD KEY `idx_contact_emails_hash` (`email_hash`),
  ADD KEY `idx_contact_emails_primary` (`user_id`,`is_primary`);

--
-- Indeks untuk tabel `contact_phones`
--
ALTER TABLE `contact_phones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_contact_phones_user_phone` (`user_id`,`phone`),
  ADD KEY `idx_contact_phones_hash` (`phone_hash`),
  ADD KEY `idx_contact_phones_primary` (`user_id`,`is_primary`);

--
-- Indeks untuk tabel `data_subject_requests`
--
ALTER TABLE `data_subject_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_dsr_user_status` (`user_id`,`status`);

--
-- Indeks untuk tabel `education_records`
--
ALTER TABLE `education_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_education_institution` (`institution`);

--
-- Indeks untuk tabel `emergency_contacts`
--
ALTER TABLE `emergency_contacts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `employment_records`
--
ALTER TABLE `employment_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_employment_company` (`company`);

--
-- Indeks untuk tabel `ethnicities`
--
ALTER TABLE `ethnicities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indeks untuk tabel `family_members`
--
ALTER TABLE `family_members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `financial_info`
--
ALTER TABLE `financial_info`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `genders`
--
ALTER TABLE `genders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indeks untuk tabel `health_records`
--
ALTER TABLE `health_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `identities`
--
ALTER TABLE `identities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nik` (`nik`),
  ADD KEY `gender_id` (`gender_id`),
  ADD KEY `marital_status_id` (`marital_status_id`),
  ADD KEY `religion_id` (`religion_id`),
  ADD KEY `ethnicity_id` (`ethnicity_id`),
  ADD KEY `blood_type_id` (`blood_type_id`),
  ADD KEY `idx_identities_user` (`user_id`),
  ADD KEY `idx_identities_nomor_kk` (`nomor_kk`),
  ADD KEY `idx_identities_pep` (`pep_flag`),
  ADD KEY `idx_identities_risk` (`risk_score`),
  ADD KEY `idx_identities_kyc_next_review` (`kyc_next_review_at`);

--
-- Indeks untuk tabel `identity_types`
--
ALTER TABLE `identity_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indeks untuk tabel `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `life_events`
--
ALTER TABLE `life_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `marital_statuses`
--
ALTER TABLE `marital_statuses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indeks untuk tabel `physical_attributes`
--
ALTER TABLE `physical_attributes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `preferences`
--
ALTER TABLE `preferences`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `reference_contacts`
--
ALTER TABLE `reference_contacts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `religions`
--
ALTER TABLE `religions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indeks untuk tabel `skills`
--
ALTER TABLE `skills`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `social_profiles`
--
ALTER TABLE `social_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_social_platform_user` (`platform`,`username`);

--
-- Indeks untuk tabel `travel_history`
--
ALTER TABLE `travel_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeks untuk tabel `user_identity_documents`
--
ALTER TABLE `user_identity_documents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `document_number` (`document_number`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `identity_type_id` (`identity_type_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `allowed_databases`
--
ALTER TABLE `allowed_databases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `blood_types`
--
ALTER TABLE `blood_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `certifications`
--
ALTER TABLE `certifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `consents`
--
ALTER TABLE `consents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `contact_emails`
--
ALTER TABLE `contact_emails`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `contact_phones`
--
ALTER TABLE `contact_phones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `data_subject_requests`
--
ALTER TABLE `data_subject_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `education_records`
--
ALTER TABLE `education_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `emergency_contacts`
--
ALTER TABLE `emergency_contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `employment_records`
--
ALTER TABLE `employment_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `ethnicities`
--
ALTER TABLE `ethnicities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `family_members`
--
ALTER TABLE `family_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `financial_info`
--
ALTER TABLE `financial_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `genders`
--
ALTER TABLE `genders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `health_records`
--
ALTER TABLE `health_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `identities`
--
ALTER TABLE `identities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `identity_types`
--
ALTER TABLE `identity_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `languages`
--
ALTER TABLE `languages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `life_events`
--
ALTER TABLE `life_events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `marital_statuses`
--
ALTER TABLE `marital_statuses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `physical_attributes`
--
ALTER TABLE `physical_attributes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `preferences`
--
ALTER TABLE `preferences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `reference_contacts`
--
ALTER TABLE `reference_contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `religions`
--
ALTER TABLE `religions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `skills`
--
ALTER TABLE `skills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `social_profiles`
--
ALTER TABLE `social_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `travel_history`
--
ALTER TABLE `travel_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `user_identity_documents`
--
ALTER TABLE `user_identity_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `addresses`
--
ALTER TABLE `addresses`
  ADD CONSTRAINT `addresses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `audit_logs_ibfk_2` FOREIGN KEY (`performed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `certifications`
--
ALTER TABLE `certifications`
  ADD CONSTRAINT `certifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `consents`
--
ALTER TABLE `consents`
  ADD CONSTRAINT `consents_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `contact_emails`
--
ALTER TABLE `contact_emails`
  ADD CONSTRAINT `contact_emails_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `contact_phones`
--
ALTER TABLE `contact_phones`
  ADD CONSTRAINT `contact_phones_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `data_subject_requests`
--
ALTER TABLE `data_subject_requests`
  ADD CONSTRAINT `data_subject_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `education_records`
--
ALTER TABLE `education_records`
  ADD CONSTRAINT `education_records_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `emergency_contacts`
--
ALTER TABLE `emergency_contacts`
  ADD CONSTRAINT `emergency_contacts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `employment_records`
--
ALTER TABLE `employment_records`
  ADD CONSTRAINT `employment_records_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `family_members`
--
ALTER TABLE `family_members`
  ADD CONSTRAINT `family_members_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `financial_info`
--
ALTER TABLE `financial_info`
  ADD CONSTRAINT `financial_info_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `health_records`
--
ALTER TABLE `health_records`
  ADD CONSTRAINT `health_records_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `identities`
--
ALTER TABLE `identities`
  ADD CONSTRAINT `identities_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `identities_ibfk_2` FOREIGN KEY (`gender_id`) REFERENCES `genders` (`id`),
  ADD CONSTRAINT `identities_ibfk_3` FOREIGN KEY (`marital_status_id`) REFERENCES `marital_statuses` (`id`),
  ADD CONSTRAINT `identities_ibfk_4` FOREIGN KEY (`religion_id`) REFERENCES `religions` (`id`),
  ADD CONSTRAINT `identities_ibfk_5` FOREIGN KEY (`ethnicity_id`) REFERENCES `ethnicities` (`id`),
  ADD CONSTRAINT `identities_ibfk_6` FOREIGN KEY (`blood_type_id`) REFERENCES `blood_types` (`id`);

--
-- Ketidakleluasaan untuk tabel `languages`
--
ALTER TABLE `languages`
  ADD CONSTRAINT `languages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `life_events`
--
ALTER TABLE `life_events`
  ADD CONSTRAINT `life_events_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `physical_attributes`
--
ALTER TABLE `physical_attributes`
  ADD CONSTRAINT `physical_attributes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `preferences`
--
ALTER TABLE `preferences`
  ADD CONSTRAINT `preferences_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `reference_contacts`
--
ALTER TABLE `reference_contacts`
  ADD CONSTRAINT `reference_contacts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `skills`
--
ALTER TABLE `skills`
  ADD CONSTRAINT `skills_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `social_profiles`
--
ALTER TABLE `social_profiles`
  ADD CONSTRAINT `social_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `travel_history`
--
ALTER TABLE `travel_history`
  ADD CONSTRAINT `travel_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `user_identity_documents`
--
ALTER TABLE `user_identity_documents`
  ADD CONSTRAINT `user_identity_documents_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_identity_documents_ibfk_2` FOREIGN KEY (`identity_type_id`) REFERENCES `identity_types` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
