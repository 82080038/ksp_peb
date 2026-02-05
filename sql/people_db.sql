-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Waktu pembuatan: 06 Feb 2026 pada 06.00
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Struktur dari tabel `occupations`
--

CREATE TABLE `occupations` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `requires_rank` tinyint(1) NOT NULL DEFAULT 0,
  `category` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `occupations`
--

INSERT INTO `occupations` (`id`, `name`, `is_active`, `created_at`, `requires_rank`, `category`) VALUES
(1, 'BELUM/TIDAK BEKERJA', 1, '2026-02-04 22:50:36', 0, 'Lainnya'),
(2, 'MENGURUS RUMAH TANGGA', 1, '2026-02-04 22:50:36', 0, 'Lainnya'),
(3, 'PELAJAR/MAHASISWA', 1, '2026-02-04 22:50:36', 0, 'Lainnya'),
(4, 'PENSIUNAN', 1, '2026-02-04 22:50:36', 0, 'Lainnya'),
(5, 'PEGAWAI NEGERI SIPIL', 1, '2026-02-04 22:50:36', 1, 'Pemerintah'),
(6, 'TENTARA NASIONAL INDONESIA', 1, '2026-02-04 22:50:36', 1, 'Pemerintah'),
(7, 'KEPOLISIAN RI', 1, '2026-02-04 22:50:36', 1, 'Pemerintah'),
(8, 'PERDAGANGAN', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(9, 'PETANI/PEKEBUN', 1, '2026-02-04 22:50:36', 0, 'Pertanian'),
(10, 'PETERNAK', 1, '2026-02-04 22:50:36', 0, 'Pertanian'),
(11, 'NELAYAN/PERIKANAN', 1, '2026-02-04 22:50:36', 0, 'Pertanian'),
(12, 'INDUSTRI', 1, '2026-02-04 22:50:36', 0, 'Industri'),
(13, 'KONSTRUKSI', 1, '2026-02-04 22:50:36', 0, 'Industri'),
(14, 'TRANSPORTASI', 1, '2026-02-04 22:50:36', 0, 'Industri'),
(15, 'KARYAWAN SWASTA', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(16, 'KARYAWAN BUMN', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(17, 'KARYAWAN BUMD', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(18, 'KARYAWAN HONORER', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(19, 'BURUH HARIAN LEPAS', 1, '2026-02-04 22:50:36', 0, 'Pertanian'),
(20, 'BURUH TANI/PERKEBUNAN', 1, '2026-02-04 22:50:36', 0, 'Pertanian'),
(21, 'BURUH NELAYAN/PERIKANAN', 1, '2026-02-04 22:50:36', 0, 'Pertanian'),
(22, 'BURUH PETERNAKAN', 1, '2026-02-04 22:50:36', 0, 'Pertanian'),
(23, 'TUKANG CUKUR', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(24, 'TUKANG LISTRIK', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(25, 'TUKANG BATU', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(26, 'TUKANG KAYU', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(27, 'TUKANG SOL SEPATU', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(28, 'TUKANG LAS/PANDAI BESI', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(29, 'TUKANG JAHIT', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(30, 'TUKANG GIGI', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(31, 'PENATA RIAS', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(32, 'PENATA BUSANA', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(33, 'PENATA RAMBUT', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(34, 'MEKANIK', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(35, 'SENIMAN', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(36, 'TABIB', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(37, 'PARAJI', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(38, 'PERANCANG BUSANA', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(39, 'PENTERJEMAH', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(40, 'IMAM MASJID', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(41, 'PENDETA', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(42, 'PASTOR', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(43, 'WARTAWAN', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(44, 'USTADZ/MUBALIGH', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(45, 'JURU MASAK', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(46, 'PROMOTOR ACARA', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(47, 'ANGGOTA DPR-RI', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(48, 'ANGGOTA DPD', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(49, 'ANGGOTA BPK', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(50, 'PRESIDEN', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(51, 'WAKIL PRESIDEN', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(52, 'ANGGOTA MAHKAMAH KONSTITUSI', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(53, 'ANGGOTA KABINET/MENTERI', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(54, 'DUTA BESAR', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(55, 'GUBERNUR', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(56, 'WAKIL GUBERNUR', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(57, 'BUPATI', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(58, 'WAKIL BUPATI', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(59, 'WALIKOTA', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(60, 'WAKIL WALIKOTA', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(61, 'ANGGOTA DPRD PROVINSI', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(62, 'ANGGOTA DPRD KABUPATEN/KOTA', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(63, 'DOSEN', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(64, 'GURU', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(65, 'PILOT', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(66, 'PENGACARA', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(67, 'NOTARIS', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(68, 'ARSITEK', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(69, 'AKUNTAN', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(70, 'KONSULTAN', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(71, 'DOKTER', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(72, 'BIDAN', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(73, 'PERAWAT', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(74, 'APOTEKER', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(75, 'PSIKOLOG/PSIKIATER', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(76, 'PENYULUH PERTANIAN', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(77, 'PENYULUH PERIKANAN', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(78, 'PENYULUH KEHUTANAN', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(79, 'PARAMEDIS', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(80, 'PENELITI', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(81, 'SOPIR', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(82, 'PIALANG', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(83, 'PARANORMAL', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(84, 'PEDAGANG', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(85, 'PERANGKAT DESA', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(86, 'KEPALA DESA', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(87, 'BIARAWATI', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(88, 'WIRASWASTA', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(89, 'ANGGOTA LEMBAGA TINGGI LAINNYA', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(90, 'ARTIS', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(91, 'ATLET', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(92, 'MANAJER', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(93, 'TENAGA AHLI', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(94, 'KEPALA WILAYAH', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(95, 'DOSEN (NON PNS)', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(96, 'GURU (NON PNS)', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(97, 'SWASTA (LAINNYA)', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(98, 'TUKANG JAHIT (NON PNS)', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(99, 'LAINNYA', 1, '2026-02-04 22:50:36', 0, 'Perdagangan/Jasa'),
(100, 'ASN/PNS', 1, '2026-02-04 22:50:36', 1, 'Pemerintah'),
(101, 'POLSUSPAS', 1, '2026-02-04 22:50:36', 1, 'Pemerintah'),
(102, 'BEA CUKAI', 1, '2026-02-04 22:50:36', 1, 'Pemerintah'),
(103, 'SATPOL PP', 1, '2026-02-04 22:50:36', 1, 'Pemerintah');

-- --------------------------------------------------------

--
-- Struktur dari tabel `occupation_ranks`
--

CREATE TABLE `occupation_ranks` (
  `id` int(11) NOT NULL,
  `occupation_id` int(11) NOT NULL,
  `rank_name` varchar(100) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `sort_order` int(11) DEFAULT 0,
  `golongan` varchar(100) DEFAULT NULL,
  `tingkatan` varchar(100) DEFAULT NULL,
  `nama_pangkat` varchar(150) DEFAULT NULL,
  `singkatan` varchar(50) DEFAULT NULL,
  `lambang` varchar(150) DEFAULT NULL,
  `pendidikan_min` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `occupation_ranks`
--

INSERT INTO `occupation_ranks` (`id`, `occupation_id`, `rank_name`, `is_active`, `created_at`, `sort_order`, `golongan`, `tingkatan`, `nama_pangkat`, `singkatan`, `lambang`, `pendidikan_min`) VALUES
(560, 100, 'I/A', 1, '2026-02-05 05:50:36', 1, 'I (JURU)', 'I/A', 'Juru Muda', 'I/A', '', 'SD / Sederajat'),
(561, 5, 'I/A', 1, '2026-02-05 05:50:36', 1, 'I (JURU)', 'I/A', 'Juru Muda', 'I/A', '', 'SD / Sederajat'),
(562, 100, 'I/B', 1, '2026-02-05 05:50:36', 2, 'I (JURU)', 'I/B', 'Juru Muda Tingkat I', 'I/B', '', '-'),
(563, 5, 'I/B', 1, '2026-02-05 05:50:36', 2, 'I (JURU)', 'I/B', 'Juru Muda Tingkat I', 'I/B', '', '-'),
(564, 100, 'I/C', 1, '2026-02-05 05:50:36', 3, 'I (JURU)', 'I/C', 'Juru', 'I/C', '', 'SMP / Sederajat'),
(565, 5, 'I/C', 1, '2026-02-05 05:50:36', 3, 'I (JURU)', 'I/C', 'Juru', 'I/C', '', 'SMP / Sederajat'),
(566, 100, 'I/D', 1, '2026-02-05 05:50:36', 4, 'I (JURU)', 'I/D', 'Juru Tingkat I', 'I/D', '', '-'),
(567, 5, 'I/D', 1, '2026-02-05 05:50:36', 4, 'I (JURU)', 'I/D', 'Juru Tingkat I', 'I/D', '', '-'),
(568, 100, 'II/A', 1, '2026-02-05 05:50:36', 5, 'II (PENGATUR)', 'II/A', 'Pengatur Muda', 'II/A', '', 'SMA / SMK / Sederajat'),
(569, 5, 'II/A', 1, '2026-02-05 05:50:36', 5, 'II (PENGATUR)', 'II/A', 'Pengatur Muda', 'II/A', '', 'SMA / SMK / Sederajat'),
(570, 100, 'II/B', 1, '2026-02-05 05:50:36', 6, 'II (PENGATUR)', 'II/B', 'Pengatur Muda Tingkat I', 'II/B', '', 'D2'),
(571, 5, 'II/B', 1, '2026-02-05 05:50:36', 6, 'II (PENGATUR)', 'II/B', 'Pengatur Muda Tingkat I', 'II/B', '', 'D2'),
(572, 100, 'II/C', 1, '2026-02-05 05:50:36', 7, 'II (PENGATUR)', 'II/C', 'Pengatur', 'II/C', '', 'D3'),
(573, 5, 'II/C', 1, '2026-02-05 05:50:36', 7, 'II (PENGATUR)', 'II/C', 'Pengatur', 'II/C', '', 'D3'),
(574, 100, 'II/D', 1, '2026-02-05 05:50:36', 8, 'II (PENGATUR)', 'II/D', 'Pengatur Tingkat I', 'II/D', '', '-'),
(575, 5, 'II/D', 1, '2026-02-05 05:50:36', 8, 'II (PENGATUR)', 'II/D', 'Pengatur Tingkat I', 'II/D', '', '-'),
(576, 100, 'III/A', 1, '2026-02-05 05:50:36', 9, 'III (PENATA)', 'III/A', 'Penata Muda', 'III/A', '', 'S1 / D4'),
(577, 5, 'III/A', 1, '2026-02-05 05:50:36', 9, 'III (PENATA)', 'III/A', 'Penata Muda', 'III/A', '', 'S1 / D4'),
(578, 100, 'III/B', 1, '2026-02-05 05:50:36', 10, 'III (PENATA)', 'III/B', 'Penata Muda Tingkat I', 'III/B', '', 'S2'),
(579, 5, 'III/B', 1, '2026-02-05 05:50:36', 10, 'III (PENATA)', 'III/B', 'Penata Muda Tingkat I', 'III/B', '', 'S2'),
(580, 100, 'III/C', 1, '2026-02-05 05:50:36', 11, 'III (PENATA)', 'III/C', 'Penata', 'III/C', '', 'S3'),
(581, 5, 'III/C', 1, '2026-02-05 05:50:36', 11, 'III (PENATA)', 'III/C', 'Penata', 'III/C', '', 'S3'),
(582, 100, 'III/D', 1, '2026-02-05 05:50:36', 12, 'III (PENATA)', 'III/D', 'Penata Tingkat I', 'III/D', '', '-'),
(583, 5, 'III/D', 1, '2026-02-05 05:50:36', 12, 'III (PENATA)', 'III/D', 'Penata Tingkat I', 'III/D', '', '-'),
(584, 100, 'IV/A', 1, '2026-02-05 05:50:36', 13, 'IV (PEMBINA)', 'IV/A', 'Pembina', 'IV/A', '', '-'),
(585, 5, 'IV/A', 1, '2026-02-05 05:50:36', 13, 'IV (PEMBINA)', 'IV/A', 'Pembina', 'IV/A', '', '-'),
(586, 100, 'IV/B', 1, '2026-02-05 05:50:36', 14, 'IV (PEMBINA)', 'IV/B', 'Pembina Tingkat I', 'IV/B', '', '-'),
(587, 5, 'IV/B', 1, '2026-02-05 05:50:36', 14, 'IV (PEMBINA)', 'IV/B', 'Pembina Tingkat I', 'IV/B', '', '-'),
(588, 100, 'IV/C', 1, '2026-02-05 05:50:36', 15, 'IV (PEMBINA)', 'IV/C', 'Pembina Utama Muda', 'IV/C', '', '-'),
(589, 5, 'IV/C', 1, '2026-02-05 05:50:36', 15, 'IV (PEMBINA)', 'IV/C', 'Pembina Utama Muda', 'IV/C', '', '-'),
(590, 100, 'IV/D', 1, '2026-02-05 05:50:36', 16, 'IV (PEMBINA)', 'IV/D', 'Pembina Utama Madya', 'IV/D', '', '-'),
(591, 5, 'IV/D', 1, '2026-02-05 05:50:36', 16, 'IV (PEMBINA)', 'IV/D', 'Pembina Utama Madya', 'IV/D', '', '-'),
(592, 100, 'IV/E', 1, '2026-02-05 05:50:36', 17, 'IV (PEMBINA)', 'IV/E', 'Pembina Utama', 'IV/E', '', '-'),
(593, 5, 'IV/E', 1, '2026-02-05 05:50:36', 17, 'IV (PEMBINA)', 'IV/E', 'Pembina Utama', 'IV/E', '', '-'),
(623, 7, 'BHARADA', 1, '2026-02-05 05:50:36', 1, 'TAMTAMA', 'TAMTAMA', 'Bhayangkara Dua', 'BHARADA', '1 Balok Merah Miring', NULL),
(624, 7, 'BHARATU', 1, '2026-02-05 05:50:36', 2, 'TAMTAMA', 'TAMTAMA', 'Bhayangkara Satu', 'BHARATU', '2 Balok Merah Miring', NULL),
(625, 7, 'BHARAKA', 1, '2026-02-05 05:50:36', 3, 'TAMTAMA', 'TAMTAMA', 'Bhayangkara Kepala', 'BHARAKA', '3 Balok Merah Miring', NULL),
(626, 7, 'ABRIPDA', 1, '2026-02-05 05:50:36', 4, 'TAMTAMA', 'TAMTAMA', 'Ajun Brigadir Polisi Dua', 'ABRIPDA', '1 Balok Merah V', NULL),
(627, 7, 'ABRIPTU', 1, '2026-02-05 05:50:36', 5, 'TAMTAMA', 'TAMTAMA', 'Ajun Brigadir Polisi Satu', 'ABRIPTU', '2 Balok Merah V', NULL),
(628, 7, 'ABRIP', 1, '2026-02-05 05:50:36', 6, 'TAMTAMA', 'TAMTAMA', 'Ajun Brigadir Polisi', 'ABRIP', '3 Balok Merah V', NULL),
(629, 7, 'BRIPDA', 1, '2026-02-05 05:50:36', 7, 'BINTARA', 'BINTARA', 'Brigadir Polisi Dua', 'BRIPDA', '1 Balok Perak V', NULL),
(630, 7, 'BRIPTU', 1, '2026-02-05 05:50:36', 8, 'BINTARA', 'BINTARA', 'Brigadir Polisi Satu', 'BRIPTU', '2 Balok Perak V', NULL),
(631, 7, 'BRIGPOL', 1, '2026-02-05 05:50:36', 9, 'BINTARA', 'BINTARA', 'Brigadir Polisi', 'BRIGPOL', '3 Balok Perak V', NULL),
(632, 7, 'BRIPKA', 1, '2026-02-05 05:50:36', 10, 'BINTARA', 'BINTARA', 'Brigadir Polisi Kepala', 'BRIPKA', '4 Balok Perak V', NULL),
(633, 7, 'AIPDA', 1, '2026-02-05 05:50:36', 11, 'BINTARA TINGGI', 'BINTARA TINGGI', 'Ajun Inspektur Polisi Dua', 'AIPDA', '1 Balok Perak Bergelombang', NULL),
(634, 7, 'AIPTU', 1, '2026-02-05 05:50:36', 12, 'BINTARA TINGGI', 'BINTARA TINGGI', 'Ajun Inspektur Polisi Satu', 'AIPTU', '2 Balok Perak Bergelombang', NULL),
(635, 7, 'IPDA', 1, '2026-02-05 05:50:36', 13, 'PERWIRA PERTAMA', 'PAMA', 'Inspektur Polisi Dua', 'IPDA', '1 Balok Emas', NULL),
(636, 7, 'IPTU', 1, '2026-02-05 05:50:36', 14, 'PERWIRA PERTAMA', 'PAMA', 'Inspektur Polisi Satu', 'IPTU', '2 Balok Emas', NULL),
(637, 7, 'AKP', 1, '2026-02-05 05:50:36', 15, 'PERWIRA PERTAMA', 'PAMA', 'Ajun Komisaris Polisi', 'AKP', '3 Balok Emas', NULL),
(638, 7, 'KOMPOL', 1, '2026-02-05 05:50:36', 16, 'PERWIRA MENENGAH', 'PAMEN', 'Komisaris Polisi', 'KOMPOL', '1 Melati Emas', NULL),
(639, 7, 'AKBP', 1, '2026-02-05 05:50:36', 17, 'PERWIRA MENENGAH', 'PAMEN', 'Ajun Komisaris Besar Polisi', 'AKBP', '2 Melati Emas', NULL),
(640, 7, 'KOMBES POL', 1, '2026-02-05 05:50:36', 18, 'PERWIRA MENENGAH', 'PAMEN', 'Komisaris Besar Polisi', 'KOMBES POL', '3 Melati Emas', NULL),
(641, 7, 'BRIGJEN POL', 1, '2026-02-05 05:50:36', 19, 'PERWIRA TINGGI', 'PATI', 'Brigadir Jenderal Polisi', 'BRIGJEN POL', '1 Bintang Emas', NULL),
(642, 7, 'IRJEN POL', 1, '2026-02-05 05:50:36', 20, 'PERWIRA TINGGI', 'PATI', 'Inspektur Jenderal Polisi', 'IRJEN POL', '2 Bintang Emas', NULL),
(643, 7, 'KOMJEN POL', 1, '2026-02-05 05:50:36', 21, 'PERWIRA TINGGI', 'PATI', 'Komisaris Jenderal Polisi', 'KOMJEN POL', '3 Bintang Emas', NULL),
(644, 7, 'JEND POL', 1, '2026-02-05 05:50:36', 22, 'PERWIRA TINGGI', 'PATI', 'Jenderal Polisi', 'JEND POL', '4 Bintang Emas', NULL),
(654, 6, 'PRADA', 1, '2026-02-05 05:50:36', 1, 'TAMTAMA', 'PRAJURIT/KELASI', 'Prajurit Dua (Prada)', 'PRADA', '', NULL),
(655, 6, 'PRATU', 1, '2026-02-05 05:50:36', 2, 'TAMTAMA', 'PRAJURIT/KELASI', 'Prajurit Satu (Pratu)', 'PRATU', '', NULL),
(656, 6, 'PRAKA', 1, '2026-02-05 05:50:36', 3, 'TAMTAMA', 'PRAJURIT/KELASI', 'Prajurit Kepala (Praka)', 'PRAKA', '', NULL),
(657, 6, 'KOPDA', 1, '2026-02-05 05:50:36', 4, 'TAMTAMA', 'KEPALA', 'Kopral Dua (Kopda)', 'KOPDA', '', NULL),
(658, 6, 'KOPTU', 1, '2026-02-05 05:50:36', 5, 'TAMTAMA', 'KEPALA', 'Kopral Satu (Koptu)', 'KOPTU', '', NULL),
(659, 6, 'KOPKA', 1, '2026-02-05 05:50:36', 6, 'TAMTAMA', 'KEPALA', 'Kopral Kepala (Kopka)', 'KOPKA', '', NULL),
(660, 6, 'SERDA', 1, '2026-02-05 05:50:36', 7, 'BINTARA', 'BINTARA', 'Sersan Dua (Serda)', 'SERDA', '', NULL),
(661, 6, 'SERTU', 1, '2026-02-05 05:50:36', 8, 'BINTARA', 'BINTARA', 'Sersan Satu (Sertu)', 'SERTU', '', NULL),
(662, 6, 'SERKA', 1, '2026-02-05 05:50:36', 9, 'BINTARA', 'BINTARA', 'Sersan Kepala (Serka)', 'SERKA', '', NULL),
(663, 6, 'SERMA', 1, '2026-02-05 05:50:36', 10, 'BINTARA', 'BINTARA', 'Sersan Mayor (Serma)', 'SERMA', '', NULL),
(664, 6, 'PELDA', 1, '2026-02-05 05:50:36', 11, 'BINTARA', 'BINTARA TINGGI', 'Pembantu Letnan Dua (Pelda)', 'PELDA', '', NULL),
(665, 6, 'PELTU', 1, '2026-02-05 05:50:36', 12, 'BINTARA', 'BINTARA TINGGI', 'Pembantu Letnan Satu (Peltu)', 'PELTU', '', NULL),
(666, 6, 'LETDA', 1, '2026-02-05 05:50:36', 13, 'PERWIRA', 'PERWIRA PERTAMA (PAMA)', 'Letnan Dua (Letda)', 'LETDA', '', NULL),
(667, 6, 'LETTU', 1, '2026-02-05 05:50:36', 14, 'PERWIRA', 'PERWIRA PERTAMA (PAMA)', 'Letnan Satu (Lettu)', 'LETTU', '', NULL),
(668, 6, 'KAPT', 1, '2026-02-05 05:50:36', 15, 'PERWIRA', 'PERWIRA PERTAMA (PAMA)', 'Kapten', 'KAPT', '', NULL),
(669, 6, 'MAYOR', 1, '2026-02-05 05:50:36', 16, 'PERWIRA', 'PERWIRA MENENGAH (PAMEN)', 'Mayor', 'MAYOR', '', NULL),
(670, 6, 'LETKOL', 1, '2026-02-05 05:50:36', 17, 'PERWIRA', 'PERWIRA MENENGAH (PAMEN)', 'Letnan Kolonel (Letkol)', 'LETKOL', '', NULL),
(671, 6, 'KOL', 1, '2026-02-05 05:50:36', 18, 'PERWIRA', 'PERWIRA MENENGAH (PAMEN)', 'Kolonel', 'KOL', '', NULL),
(672, 6, 'BRIGJEN', 1, '2026-02-05 05:50:36', 19, 'PERWIRA', 'PERWIRA TINGGI (PATI)', 'Brigadir Jenderal TNI (Brigjen)', 'BRIGJEN', '', NULL),
(673, 6, 'MAYJEN', 1, '2026-02-05 05:50:36', 20, 'PERWIRA', 'PERWIRA TINGGI (PATI)', 'Mayor Jenderal TNI (Mayjen)', 'MAYJEN', '', NULL),
(674, 6, 'LETJEN', 1, '2026-02-05 05:50:36', 21, 'PERWIRA', 'PERWIRA TINGGI (PATI)', 'Letnan Jenderal TNI (Letjen)', 'LETJEN', '', NULL),
(675, 6, 'JEND TNI', 1, '2026-02-05 05:50:36', 22, 'PERWIRA', 'PERWIRA TINGGI (PATI)', 'Jenderal TNI', 'JEND TNI', '', NULL),
(676, 6, 'LAKSMA', 1, '2026-02-05 05:50:36', 23, 'PERWIRA', 'PERWIRA TINGGI (PATI)', 'Laksamana Pertama TNI (Laksma)', 'LAKSMA', '', NULL),
(677, 6, 'LAKSDA', 1, '2026-02-05 05:50:36', 24, 'PERWIRA', 'PERWIRA TINGGI (PATI)', 'Laksamana Muda TNI (Laksda)', 'LAKSDA', '', NULL),
(678, 6, 'LAKSDYA', 1, '2026-02-05 05:50:36', 25, 'PERWIRA', 'PERWIRA TINGGI (PATI)', 'Laksamana Madya TNI (Laksdya)', 'LAKSDYA', '', NULL),
(679, 6, 'LAKSAMANA TNI', 1, '2026-02-05 05:50:36', 26, 'PERWIRA', 'PERWIRA TINGGI (PATI)', 'Laksamana TNI', 'LAKSAMANA TNI', '', NULL),
(680, 6, 'MARSMA', 1, '2026-02-05 05:50:36', 27, 'PERWIRA', 'PERWIRA TINGGI (PATI)', 'Marsekal Pertama TNI (Marsma)', 'MARSMA', '', NULL),
(681, 6, 'MARSDA', 1, '2026-02-05 05:50:36', 28, 'PERWIRA', 'PERWIRA TINGGI (PATI)', 'Marsekal Muda TNI (Marsda)', 'MARSDA', '', NULL),
(682, 6, 'MARSDYA', 1, '2026-02-05 05:50:36', 29, 'PERWIRA', 'PERWIRA TINGGI (PATI)', 'Marsekal Madya TNI (Marsdya)', 'MARSDYA', '', NULL),
(683, 6, 'MARSEKAL TNI', 1, '2026-02-05 05:50:36', 30, 'PERWIRA', 'PERWIRA TINGGI (PATI)', 'Marsekal TNI', 'MARSEKAL TNI', '', NULL),
(685, 101, 'I/A', 1, '2026-02-05 05:50:36', 1, 'I (JURU)', 'I/A', 'Juru (1 Garis Miring Putih)', 'I/A', '1 Garis Miring Putih', NULL),
(686, 101, 'I/B', 1, '2026-02-05 05:50:36', 2, 'I (JURU)', 'I/B', 'Juru (2 Garis Miring Putih)', 'I/B', '2 Garis Miring Putih', NULL),
(687, 101, 'I/C', 1, '2026-02-05 05:50:36', 3, 'I (JURU)', 'I/C', 'Juru (3 Garis Miring Putih)', 'I/C', '3 Garis Miring Putih', NULL),
(688, 101, 'I/D', 1, '2026-02-05 05:50:36', 4, 'I (JURU)', 'I/D', 'Juru (4 Garis Miring Putih)', 'I/D', '4 Garis Miring Putih', NULL),
(689, 101, 'II/A', 1, '2026-02-05 05:50:36', 5, 'II (PENGATUR)', 'II/A', 'Pengatur Muda (1 Balok Putih)', 'II/A', '1 Balok Putih', NULL),
(690, 101, 'II/B', 1, '2026-02-05 05:50:36', 6, 'II (PENGATUR)', 'II/B', 'Pengatur Muda (2 Balok Putih)', 'II/B', '2 Balok Putih', NULL),
(691, 101, 'II/C', 1, '2026-02-05 05:50:36', 7, 'II (PENGATUR)', 'II/C', 'Pengatur (3 Balok Putih)', 'II/C', '3 Balok Putih', NULL),
(692, 101, 'II/D', 1, '2026-02-05 05:50:36', 8, 'II (PENGATUR)', 'II/D', 'Pengatur (4 Balok Putih)', 'II/D', '4 Balok Putih', NULL),
(693, 101, 'III/A', 1, '2026-02-05 05:50:36', 9, 'III (PENATA)', 'III/A', 'Penata Muda (1 Balok Kuning)', 'III/A', '1 Balok Kuning', NULL),
(694, 101, 'III/B', 1, '2026-02-05 05:50:36', 10, 'III (PENATA)', 'III/B', 'Penata Muda (2 Balok Kuning)', 'III/B', '2 Balok Kuning', NULL),
(695, 101, 'III/C', 1, '2026-02-05 05:50:36', 11, 'III (PENATA)', 'III/C', 'Penata (3 Balok Kuning)', 'III/C', '3 Balok Kuning', NULL),
(696, 101, 'III/D', 1, '2026-02-05 05:50:36', 12, 'III (PENATA)', 'III/D', 'Penata (4 Balok Kuning)', 'III/D', '4 Balok Kuning', NULL),
(697, 101, 'IV/A', 1, '2026-02-05 05:50:36', 13, 'IV (PEMBINA)', 'IV/A', 'Pembina (Bintang Emas)', 'IV/A', 'Bintang Emas', NULL),
(698, 101, 'IV/B', 1, '2026-02-05 05:50:36', 14, 'IV (PEMBINA)', 'IV/B', 'Pembina Tingkat I (Bintang Emas)', 'IV/B', 'Bintang Emas', NULL),
(699, 101, 'IV/C', 1, '2026-02-05 05:50:36', 15, 'IV (PEMBINA)', 'IV/C', 'Pembina Utama Muda (Bintang Emas)', 'IV/C', 'Bintang Emas', NULL),
(700, 101, 'IV/D', 1, '2026-02-05 05:50:36', 16, 'IV (PEMBINA)', 'IV/D', 'Pembina Utama Madya (Bintang Emas)', 'IV/D', 'Bintang Emas', NULL),
(701, 101, 'IV/E', 1, '2026-02-05 05:50:36', 17, 'IV (PEMBINA)', 'IV/E', 'Pembina Utama (Bintang Emas)', 'IV/E', 'Bintang Emas', NULL),
(716, 102, 'I/A', 1, '2026-02-05 05:50:36', 1, 'I (JURU)', 'I/A', 'Juru (1 Garis Miring Perak)', 'I/A', '1 Garis Miring Perak', NULL),
(717, 102, 'I/B', 1, '2026-02-05 05:50:36', 2, 'I (JURU)', 'I/B', 'Juru (2 Garis Miring Perak)', 'I/B', '2 Garis Miring Perak', NULL),
(718, 102, 'I/C', 1, '2026-02-05 05:50:36', 3, 'I (JURU)', 'I/C', 'Juru (3 Garis Miring Perak)', 'I/C', '3 Garis Miring Perak', NULL),
(719, 102, 'I/D', 1, '2026-02-05 05:50:36', 4, 'I (JURU)', 'I/D', 'Juru (4 Garis Miring Perak)', 'I/D', '4 Garis Miring Perak', NULL),
(720, 102, 'II/A', 1, '2026-02-05 05:50:36', 5, 'II (PENGATUR)', 'II/A', 'Pengatur Muda (1 Balok Perak)', 'II/A', '1 Balok Perak', NULL),
(721, 102, 'II/B', 1, '2026-02-05 05:50:36', 6, 'II (PENGATUR)', 'II/B', 'Pengatur Muda (2 Balok Perak)', 'II/B', '2 Balok Perak', NULL),
(722, 102, 'II/C', 1, '2026-02-05 05:50:36', 7, 'II (PENGATUR)', 'II/C', 'Pengatur (3 Balok Perak)', 'II/C', '3 Balok Perak', NULL),
(723, 102, 'II/D', 1, '2026-02-05 05:50:36', 8, 'II (PENGATUR)', 'II/D', 'Pengatur (4 Balok Perak)', 'II/D', '4 Balok Perak', NULL),
(724, 102, 'III/A', 1, '2026-02-05 05:50:36', 9, 'III (PENATA)', 'III/A', 'Penata Muda (1 Balok Emas)', 'III/A', '1 Balok Emas', NULL),
(725, 102, 'III/B', 1, '2026-02-05 05:50:36', 10, 'III (PENATA)', 'III/B', 'Penata Muda (2 Balok Emas)', 'III/B', '2 Balok Emas', NULL),
(726, 102, 'III/C', 1, '2026-02-05 05:50:36', 11, 'III (PENATA)', 'III/C', 'Penata (3 Balok Emas)', 'III/C', '3 Balok Emas', NULL),
(727, 102, 'III/D', 1, '2026-02-05 05:50:36', 12, 'III (PENATA)', 'III/D', 'Penata (4 Balok Emas)', 'III/D', '4 Balok Emas', NULL),
(728, 102, 'IV/A', 1, '2026-02-05 05:50:36', 13, 'IV (PEMBINA)', 'IV/A', 'Pembina (Bintang)', 'IV/A', 'Bintang', NULL),
(729, 102, 'IV/B', 1, '2026-02-05 05:50:36', 14, 'IV (PEMBINA)', 'IV/B', 'Pembina Tingkat I (Bintang)', 'IV/B', 'Bintang', NULL),
(730, 102, 'IV/C', 1, '2026-02-05 05:50:36', 15, 'IV (PEMBINA)', 'IV/C', 'Pembina Utama Muda (Bintang)', 'IV/C', 'Bintang', NULL),
(731, 102, 'IV/D', 1, '2026-02-05 05:50:36', 16, 'IV (PEMBINA)', 'IV/D', 'Pembina Utama Madya (Bintang)', 'IV/D', 'Bintang', NULL),
(732, 102, 'IV/E', 1, '2026-02-05 05:50:36', 17, 'IV (PEMBINA)', 'IV/E', 'Pembina Utama (Bintang)', 'IV/E', 'Bintang', NULL),
(747, 103, 'I/A', 1, '2026-02-05 05:50:36', 1, 'I (JURU)', 'I/A', 'Juru (1 Garis Miring Perak)', 'I/A', '1 Garis Miring Perak', NULL),
(748, 103, 'I/B', 1, '2026-02-05 05:50:36', 2, 'I (JURU)', 'I/B', 'Juru (2 Garis Miring Perak)', 'I/B', '2 Garis Miring Perak', NULL),
(749, 103, 'I/C', 1, '2026-02-05 05:50:36', 3, 'I (JURU)', 'I/C', 'Juru (3 Garis Miring Perak)', 'I/C', '3 Garis Miring Perak', NULL),
(750, 103, 'I/D', 1, '2026-02-05 05:50:36', 4, 'I (JURU)', 'I/D', 'Juru (4 Garis Miring Perak)', 'I/D', '4 Garis Miring Perak', NULL),
(751, 103, 'II/A', 1, '2026-02-05 05:50:36', 5, 'II (PENGATUR)', 'II/A', 'Pengatur Muda (1 Balok Perak)', 'II/A', '1 Balok Perak', NULL),
(752, 103, 'II/B', 1, '2026-02-05 05:50:36', 6, 'II (PENGATUR)', 'II/B', 'Pengatur Muda (2 Balok Perak)', 'II/B', '2 Balok Perak', NULL),
(753, 103, 'II/C', 1, '2026-02-05 05:50:36', 7, 'II (PENGATUR)', 'II/C', 'Pengatur (3 Balok Perak)', 'II/C', '3 Balok Perak', NULL),
(754, 103, 'II/D', 1, '2026-02-05 05:50:36', 8, 'II (PENGATUR)', 'II/D', 'Pengatur (4 Balok Perak)', 'II/D', '4 Balok Perak', NULL),
(755, 103, 'III/A', 1, '2026-02-05 05:50:36', 9, 'III (PENATA)', 'III/A', 'Penata Muda (1 Balok Emas)', 'III/A', '1 Balok Emas', NULL),
(756, 103, 'III/B', 1, '2026-02-05 05:50:36', 10, 'III (PENATA)', 'III/B', 'Penata Muda (2 Balok Emas)', 'III/B', '2 Balok Emas', NULL),
(757, 103, 'III/C', 1, '2026-02-05 05:50:36', 11, 'III (PENATA)', 'III/C', 'Penata (3 Balok Emas)', 'III/C', '3 Balok Emas', NULL),
(758, 103, 'III/D', 1, '2026-02-05 05:50:36', 12, 'III (PENATA)', 'III/D', 'Penata (4 Balok Emas)', 'III/D', '4 Balok Emas', NULL),
(759, 103, 'IV/A', 1, '2026-02-05 05:50:36', 13, 'IV (PEMBINA)', 'IV/A', 'Pembina (Bintang)', 'IV/A', 'Bintang', NULL),
(760, 103, 'IV/B', 1, '2026-02-05 05:50:36', 14, 'IV (PEMBINA)', 'IV/B', 'Pembina Tingkat I (Bintang)', 'IV/B', 'Bintang', NULL),
(761, 103, 'IV/C', 1, '2026-02-05 05:50:36', 15, 'IV (PEMBINA)', 'IV/C', 'Pembina Utama Muda (Bintang)', 'IV/C', 'Bintang', NULL),
(762, 103, 'IV/D', 1, '2026-02-05 05:50:36', 16, 'IV (PEMBINA)', 'IV/D', 'Pembina Utama Madya (Bintang)', 'IV/D', 'Bintang', NULL),
(763, 103, 'IV/E', 1, '2026-02-05 05:50:36', 17, 'IV (PEMBINA)', 'IV/E', 'Pembina Utama (Bintang)', 'IV/E', 'Bintang', NULL);

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
  `status` enum('active','inactive','pending') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `phone`, `password_hash`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin 820800', 'admin@koperasi.com', '081234567890', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'active', '2026-02-05 18:17:50', '2026-02-05 18:17:50');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
