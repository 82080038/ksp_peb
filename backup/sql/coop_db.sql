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
-- Database: `coop_db`
--

DELIMITER $$
--
-- Prosedur
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `track_document_change` (IN `p_cooperative_id` INT, IN `p_document_type` ENUM('nomor_bh','nib','nik_koperasi','modal_pokok'), IN `p_old_value` VARCHAR(50), IN `p_new_value` VARCHAR(50), IN `p_old_decimal` DECIMAL(15,2), IN `p_new_decimal` DECIMAL(15,2), IN `p_user_id` INT, IN `p_reason` VARCHAR(255))  BEGIN
    INSERT INTO cooperative_document_history (
        cooperative_id, 
        document_type, 
        document_number_lama, 
        document_number_baru,
        document_value_lama,
        document_value_baru,
        tanggal_efektif, 
        change_reason, 
        user_id
    ) VALUES (
        p_cooperative_id, 
        p_document_type, 
        p_old_value, 
        p_new_value,
        p_old_decimal,
        p_new_decimal,
        CURDATE(), 
        p_reason, 
        p_user_id
    );
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `track_status_change` (IN `p_cooperative_id` INT, IN `p_status_lama` ENUM('belum_terdaftar','terdaftar','badan_hukum'), IN `p_status_baru` ENUM('belum_terdaftar','terdaftar','badan_hukum'), IN `p_user_id` INT, IN `p_reason` VARCHAR(255))  BEGIN
    INSERT INTO cooperative_status_history (
        cooperative_id, 
        status_sebelumnya, 
        status_baru, 
        tanggal_efektif, 
        change_reason, 
        user_id
    ) VALUES (
        p_cooperative_id, 
        p_status_lama, 
        p_status_baru, 
        CURDATE(), 
        p_reason, 
        p_user_id
    );
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_modal_pokok_from_rat` (IN `p_cooperative_id` INT, IN `p_tahun` INT, IN `p_modal_pokok_baru` DECIMAL(15,2), IN `p_alasan` TEXT, IN `p_user_id` INT)  BEGIN
    DECLARE v_modal_pokok_sebelum DECIMAL(15,2);
    DECLARE v_persentase_perubahan DECIMAL(5,2);
    
    
    SELECT modal_pokok INTO v_modal_pokok_sebelum
    FROM cooperatives
    WHERE id = p_cooperative_id;
    
    
    IF v_modal_pokok_sebelum > 0 THEN
        SET v_persentase_perubahan = ((p_modal_pokok_baru - v_modal_pokok_sebelum) / v_modal_pokok_sebelum) * 100;
    ELSE
        SET v_persentase_perubahan = 0.00;
    END IF;
    
    
    UPDATE cooperatives 
    SET modal_pokok = p_modal_pokok_baru, updated_at = CURRENT_TIMESTAMP()
    WHERE id = p_cooperative_id;
    
    
    UPDATE rat_sessions 
    SET 
        modal_pokok_sebelum = v_modal_pokok_sebelum,
        modal_pokok_setelah = p_modal_pokok_baru,
        persentase_perubahan = v_persentase_perubahan,
        status = 'completed',
        updated_at = CURRENT_TIMESTAMP()
    WHERE cooperative_id = p_cooperative_id AND tahun = p_tahun;
    
    
    INSERT INTO modal_pokok_changes (
        cooperative_id, modal_pokok_lama, modal_pokok_baru, persentase_perubahan,
        tanggal_efektif, perubahan_type, referensi_id, alasan_perubahan, user_id
    ) VALUES (
        p_cooperative_id, v_modal_pokok_sebelum, p_modal_pokok_baru, v_persentase_perubahan,
        CURDATE(), 'rat', LAST_INSERT_ID(), p_alasan, p_user_id
    );
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_modal_pokok_manual` (IN `p_cooperative_id` INT, IN `p_modal_pokok_baru` DECIMAL(15,2), IN `p_alasan` TEXT, IN `p_user_id` INT)  BEGIN
    DECLARE v_modal_pokok_sebelum DECIMAL(15,2);
    DECLARE v_persentase_perubahan DECIMAL(5,2);
    
    
    SELECT modal_pokok INTO v_modal_pokok_sebelum
    FROM cooperatives
    WHERE id = p_cooperative_id;
    
    
    IF v_modal_pokok_sebelum > 0 THEN
        SET v_persentase_perubahan = ((p_modal_pokok_baru - v_modal_pokok_sebelum) / v_modal_pokok_sebelum) * 100;
    ELSE
        SET v_persentase_perubahan = 0.00;
    END IF;
    
    
    UPDATE cooperatives 
    SET modal_pokok = p_modal_pokok_baru, updated_at = CURRENT_TIMESTAMP()
    WHERE id = p_cooperative_id;
    
    
    INSERT INTO modal_pokok_changes (
        cooperative_id, modal_pokok_lama, modal_pokok_baru, persentase_perubahan,
        tanggal_efektif, perubahan_type, referensi_id, alasan_perubahan, user_id
    ) VALUES (
        p_cooperative_id, v_modal_pokok_sebelum, p_modal_pokok_baru, v_persentase_perubahan,
        CURDATE(), 'manual', NULL, p_alasan, p_user_id
    );
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `agent_sales`
--

CREATE TABLE `agent_sales` (
  `id` int(11) NOT NULL,
  `agent_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `commission` decimal(15,2) NOT NULL,
  `approved` tinyint(1) DEFAULT 0,
  `approved_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `anggota`
--

CREATE TABLE `anggota` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status_keanggotaan` enum('active','inactive','suspended') DEFAULT 'active',
  `nomor_anggota` varchar(20) NOT NULL,
  `joined_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `table_name` varchar(50) NOT NULL,
  `record_id` int(11) NOT NULL,
  `field_name` varchar(50) NOT NULL,
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` enum('INSERT','UPDATE','DELETE') NOT NULL,
  `details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`details`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `chart_of_accounts`
--

CREATE TABLE `chart_of_accounts` (
  `id` int(11) NOT NULL,
  `cooperative_id` int(11) NOT NULL,
  `code` varchar(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` enum('asset','liability','equity','revenue','expense') NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `chart_of_accounts`
--

INSERT INTO `chart_of_accounts` (`id`, `cooperative_id`, `code`, `name`, `type`, `parent_id`, `is_active`, `created_at`) VALUES
(1, 0, '1000', 'Kas', 'asset', NULL, 1, '2026-02-03 14:13:20'),
(2, 0, '1100', 'Bank', 'asset', NULL, 1, '2026-02-03 14:13:20'),
(3, 0, '2000', 'Simpanan Anggota', 'liability', NULL, 1, '2026-02-03 14:13:20'),
(4, 0, '2100', 'Pinjaman Anggota', 'asset', NULL, 1, '2026-02-03 14:13:20'),
(5, 0, '3000', 'Modal', 'equity', NULL, 1, '2026-02-03 14:13:20'),
(6, 0, '4000', 'Pendapatan Bunga', 'revenue', NULL, 1, '2026-02-03 14:13:20'),
(7, 0, '5000', 'Beban Bunga', 'expense', NULL, 1, '2026-02-03 14:13:20'),
(8, 0, '5100', 'Beban Operasional', 'expense', NULL, 1, '2026-02-03 14:13:20'),
(10, 4, '1000', 'Kas', 'asset', NULL, 1, '2026-02-04 17:30:29'),
(11, 4, '1100', 'Bank', 'asset', NULL, 1, '2026-02-04 17:30:29'),
(12, 4, '2000', 'Simpanan Anggota', 'liability', NULL, 1, '2026-02-04 17:30:29'),
(13, 4, '2100', 'Pinjaman Anggota', 'asset', NULL, 1, '2026-02-04 17:30:29'),
(14, 4, '3000', 'Modal', 'equity', NULL, 1, '2026-02-04 17:30:29'),
(15, 4, '3100', 'Cadangan', 'equity', NULL, 1, '2026-02-04 17:30:29'),
(16, 4, '4000', 'Pendapatan Bunga', 'revenue', NULL, 1, '2026-02-04 17:30:29'),
(17, 4, '4100', 'Pendapatan Operasional', 'revenue', NULL, 1, '2026-02-04 17:30:29'),
(18, 4, '5000', 'Beban Bunga', 'expense', NULL, 1, '2026-02-04 17:30:29'),
(19, 4, '5100', 'Beban Operasional', 'expense', NULL, 1, '2026-02-04 17:30:29'),
(20, 4, '5200', 'Beban Administrasi', 'expense', NULL, 1, '2026-02-04 17:30:29');

-- --------------------------------------------------------

--
-- Struktur dari tabel `configs`
--

CREATE TABLE `configs` (
  `id` int(11) NOT NULL,
  `key_name` varchar(100) NOT NULL,
  `value` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `configs`
--

INSERT INTO `configs` (`id`, `key_name`, `value`, `description`, `updated_at`) VALUES
(1, 'coop_name', 'Koperasi Simpan Pinjam', 'Nama koperasi', '2026-02-03 14:13:20'),
(2, 'interest_rate_savings', '3.5', 'Suku bunga simpanan tahunan (%)', '2026-02-03 14:13:20'),
(3, 'interest_rate_loans', '12.0', 'Suku bunga pinjaman tahunan (%)', '2026-02-03 14:13:20'),
(4, 'penalty_rate', '2.0', 'Denda keterlambatan (%) per hari', '2026-02-03 14:13:20'),
(5, 'shu_distribution_ratio', '70', 'Persentase SHU untuk anggota (%)', '2026-02-03 14:13:20');

-- --------------------------------------------------------

--
-- Struktur dari tabel `cooperatives`
--

CREATE TABLE `cooperatives` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `jenis` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`jenis`)),
  `badan_hukum` varchar(255) DEFAULT NULL,
  `status_badan_hukum` enum('belum_terdaftar','terdaftar','badan_hukum') DEFAULT 'belum_terdaftar',
  `tanggal_status_terakhir` date DEFAULT NULL,
  `status_notes` text DEFAULT NULL,
  `tanggal_pendirian` date DEFAULT NULL,
  `npwp` varchar(50) DEFAULT NULL,
  `nomor_bh` varchar(50) DEFAULT NULL,
  `nib` varchar(20) DEFAULT NULL,
  `nik_koperasi` varchar(20) DEFAULT NULL,
  `modal_pokok` decimal(15,2) DEFAULT 0.00,
  `alamat_legal` text DEFAULT NULL,
  `kontak_resmi` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `province_id` int(11) DEFAULT NULL,
  `regency_id` int(11) DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `village_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `cooperatives`
--

INSERT INTO `cooperatives` (`id`, `nama`, `jenis`, `badan_hukum`, `status_badan_hukum`, `tanggal_status_terakhir`, `status_notes`, `tanggal_pendirian`, `npwp`, `nomor_bh`, `nib`, `nik_koperasi`, `modal_pokok`, `alamat_legal`, `kontak_resmi`, `logo`, `created_by`, `created_at`, `updated_at`, `province_id`, `regency_id`, `district_id`, `village_id`) VALUES
(4, 'KSP POLRES SAMOSIR', '\"KSP\"', 'terdaftar', 'belum_terdaftar', NULL, NULL, '2025-12-25', '3171011001900001', NULL, NULL, NULL, '0.00', 'Jl. Danau Toba No. 03', '081211223344', NULL, 5, '2026-02-04 17:30:29', '2026-02-04 17:30:29', 3, 40, 590, 10617);

-- --------------------------------------------------------

--
-- Struktur dari tabel `cooperative_document_history`
--

CREATE TABLE `cooperative_document_history` (
  `id` int(11) NOT NULL,
  `cooperative_id` int(11) NOT NULL,
  `document_type` enum('nomor_bh','nib','nik_koperasi','modal_pokok') NOT NULL,
  `document_number_lama` varchar(50) DEFAULT NULL,
  `document_number_baru` varchar(50) DEFAULT NULL,
  `document_value_lama` decimal(15,2) DEFAULT NULL,
  `document_value_baru` decimal(15,2) DEFAULT NULL,
  `tanggal_efektif` date NOT NULL,
  `change_reason` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `cooperative_financial_settings`
--

CREATE TABLE `cooperative_financial_settings` (
  `id` int(11) NOT NULL,
  `cooperative_id` int(11) NOT NULL,
  `tahun_buku` year(4) NOT NULL,
  `periode_mulai` date NOT NULL,
  `periode_akhir` date NOT NULL,
  `simpanan_pokok` decimal(15,2) DEFAULT 0.00,
  `simpanan_wajib` decimal(15,2) DEFAULT 0.00,
  `bunga_pinjaman` decimal(5,2) DEFAULT 12.00,
  `denda_telat` decimal(5,2) DEFAULT 2.00,
  `periode_shu` enum('yearly','semi_annual','quarterly') DEFAULT 'yearly',
  `status` enum('active','inactive','closed') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `cooperative_financial_settings`
--

INSERT INTO `cooperative_financial_settings` (`id`, `cooperative_id`, `tahun_buku`, `periode_mulai`, `periode_akhir`, `simpanan_pokok`, `simpanan_wajib`, `bunga_pinjaman`, `denda_telat`, `periode_shu`, `status`, `created_at`, `updated_at`, `created_by`) VALUES
(4, 4, 2026, '2026-01-01', '2026-12-31', '100000.00', '50000.00', '12.00', '2.00', 'yearly', 'active', '2026-02-04 17:30:29', '2026-02-04 17:30:29', 5);

-- --------------------------------------------------------

--
-- Struktur dari tabel `cooperative_status_history`
--

CREATE TABLE `cooperative_status_history` (
  `id` int(11) NOT NULL,
  `cooperative_id` int(11) NOT NULL,
  `status_sebelumnya` varchar(50) DEFAULT NULL,
  `status_baru` varchar(50) NOT NULL,
  `tanggal_efektif` date DEFAULT NULL,
  `dokumen_path` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `change_reason` varchar(255) DEFAULT NULL,
  `approval_status` enum('pending','approved','rejected') DEFAULT 'approved',
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `cooperative_types`
--

CREATE TABLE `cooperative_types` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `code` varchar(20) DEFAULT NULL,
  `category` enum('finansial','produksi','jasa','konsumsi','serba_usaha','karyawan') DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `cooperative_types`
--

INSERT INTO `cooperative_types` (`id`, `name`, `description`, `code`, `category`, `is_active`, `created_at`) VALUES
(1, 'Koperasi Simpan Pinjam (KSP)', 'Koperasi yang bergerak di bidang simpan pinjam untuk anggota, menyediakan layanan tabungan, kredit, dan jasa keuangan lainnya sesuai PP No. 7 Tahun 2021', 'KSP', 'finansial', 1, '2026-02-04 07:36:54'),
(2, 'Koperasi Konsumsi', 'Koperasi yang bergerak di bidang pemenuhan kebutuhan konsumsi anggota, menyediakan barang dan jasa kebutuhan sehari-hari sesuai PP No. 7 Tahun 2021', 'KK', 'konsumsi', 1, '2026-02-04 07:36:54'),
(3, 'Koperasi Produksi', 'Koperasi yang bergerak di bidang produksi barang/jasa anggota, mengelola pengolahan, pemasaran, dan distribusi hasil produksi sesuai PP No. 7 Tahun 2021', 'KP', 'produksi', 1, '2026-02-04 07:36:54'),
(4, 'Koperasi Pemasaran', 'Koperasi yang bergerak di bidang pemasaran hasil produksi anggota, menyediakan layanan distribusi, penjualan, dan ekspor sesuai PP No. 7 Tahun 2021', 'KPAS', 'produksi', 1, '2026-02-04 07:36:54'),
(5, 'Koperasi Jasa', 'Koperasi yang bergerak di bidang penyediaan jasa untuk anggota, seperti transportasi, komunikasi, konsultasi, dan jasa lainnya sesuai PP No. 7 Tahun 2021', 'KJ', 'jasa', 1, '2026-02-04 07:36:54'),
(6, 'Koperasi Serba Usaha (KSU)', 'Koperasi yang menjalankan berbagai jenis usaha kombinasi dari beberapa jenis koperasi dalam satu organisasi sesuai PP No. 7 Tahun 2021', 'KSU', 'serba_usaha', 1, '2026-02-04 07:36:54'),
(7, 'Koperasi Karyawan', 'Koperasi yang bergerak di bidang kesejahteraan karyawan perusahaan, menyediakan simpan pinjam, konsumsi, dan jasa untuk karyawan sesuai PP No. 7 Tahun 2021', 'KKAR', 'karyawan', 1, '2026-02-04 09:19:02'),
(8, 'Koperasi Pertanian', 'Koperasi yang bergerak di bidang pertanian, menyediakan sarana produksi, pengolahan hasil, dan pemasaran produk pertanian sesuai PP No. 7 Tahun 2021', 'KOPERTA', 'produksi', 1, '2026-02-04 09:20:25'),
(9, 'Koperasi Nelayan', 'Koperasi yang bergerak di bidang perikanan, menyediakan alat tangkap, pengolahan hasil, dan pemasaran hasil perikanan sesuai PP No. 7 Tahun 2021', 'KOPERNAL', 'produksi', 1, '2026-02-04 09:20:25'),
(10, 'Koperasi Peternakan', 'Koperasi yang bergerak di bidang peternakan, menyediakan pakan, pengolahan, dan pemasaran hasil peternakan sesuai PP No. 7 Tahun 2021', 'KOPERTAK', 'produksi', 1, '2026-02-04 09:20:25'),
(11, 'Koperasi Perdagangan', 'Koperasi yang bergerak di bidang perdagangan grosir dan eceran, menyediakan barang dagangan untuk anggota sesuai PP No. 7 Tahun 2021', 'KOPERDAG', 'konsumsi', 1, '2026-02-04 09:20:25'),
(12, 'Koperasi Pondok Pesantren', 'Koperasi yang bergerak di lingkungan pondok pesantren, menyediakan kebutuhan santri dan wali santri sesuai PP No. 7 Tahun 2021', 'KOPONTREN', 'serba_usaha', 1, '2026-02-04 09:20:25');

-- --------------------------------------------------------

--
-- Struktur dari tabel `general_ledger`
--

CREATE TABLE `general_ledger` (
  `id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `period` date NOT NULL,
  `beginning_balance` decimal(15,2) DEFAULT 0.00,
  `debit_total` decimal(15,2) DEFAULT 0.00,
  `credit_total` decimal(15,2) DEFAULT 0.00,
  `ending_balance` decimal(15,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `journal_entries`
--

CREATE TABLE `journal_entries` (
  `id` int(11) NOT NULL,
  `entry_date` date NOT NULL,
  `description` text NOT NULL,
  `reference_number` varchar(50) DEFAULT NULL,
  `status` enum('draft','posted') DEFAULT 'draft',
  `posted_by` int(11) DEFAULT NULL,
  `posted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `journal_entry_details`
--

CREATE TABLE `journal_entry_details` (
  `id` int(11) NOT NULL,
  `journal_entry_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `debit` decimal(15,2) DEFAULT 0.00,
  `credit` decimal(15,2) DEFAULT 0.00,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `member_shu`
--

CREATE TABLE `member_shu` (
  `id` int(11) NOT NULL,
  `anggota_id` int(11) NOT NULL,
  `shu_distribution_id` int(11) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `paid` tinyint(1) DEFAULT 0,
  `paid_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `modal_pokok_changes`
--

CREATE TABLE `modal_pokok_changes` (
  `id` int(11) NOT NULL,
  `cooperative_id` int(11) NOT NULL,
  `modal_pokok_lama` decimal(15,2) NOT NULL,
  `modal_pokok_baru` decimal(15,2) NOT NULL,
  `persentase_perubahan` decimal(5,2) NOT NULL,
  `tanggal_efektif` date NOT NULL,
  `perubahan_type` enum('manual','rat','other') NOT NULL,
  `referensi_id` int(11) DEFAULT NULL,
  `alasan_perubahan` text DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `type` enum('info','warning','error') DEFAULT 'info',
  `sent_at` timestamp NULL DEFAULT current_timestamp(),
  `read_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `order_date` timestamp NULL DEFAULT current_timestamp(),
  `total_amount` decimal(15,2) NOT NULL,
  `status` enum('pending','confirmed','shipped','delivered','cancelled') DEFAULT 'pending',
  `shipping_address` text DEFAULT NULL,
  `payment_status` enum('unpaid','paid','refunded') DEFAULT 'unpaid'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `order_details`
--

CREATE TABLE `order_details` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(15,2) NOT NULL,
  `total` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengawas`
--

CREATE TABLE `pengawas` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `jabatan` varchar(100) NOT NULL,
  `periode_start` date NOT NULL,
  `periode_end` date DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengurus`
--

CREATE TABLE `pengurus` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `jabatan` varchar(100) NOT NULL,
  `periode_start` date NOT NULL,
  `periode_end` date DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'view_users', 'View user list', '2026-02-03 14:13:20'),
(2, 'create_users', 'Create new users', '2026-02-03 14:13:20'),
(3, 'edit_users', 'Edit user information', '2026-02-03 14:13:20'),
(4, 'delete_users', 'Delete users', '2026-02-03 14:13:20'),
(5, 'view_members', 'View members', '2026-02-03 14:13:20'),
(6, 'manage_members', 'Manage member data', '2026-02-03 14:13:20'),
(7, 'view_savings', 'View savings transactions', '2026-02-03 14:13:20'),
(8, 'manage_savings', 'Manage savings', '2026-02-03 14:13:20'),
(9, 'view_loans', 'View loan applications', '2026-02-03 14:13:20'),
(10, 'manage_loans', 'Manage loans', '2026-02-03 14:13:20'),
(11, 'view_accounts', 'View chart of accounts', '2026-02-03 14:13:20'),
(12, 'manage_accounts', 'Manage accounting', '2026-02-03 14:13:20'),
(13, 'view_reports', 'View reports', '2026-02-03 14:13:20'),
(14, 'generate_reports', 'Generate financial reports', '2026-02-03 14:13:20'),
(15, 'vote', 'Participate in voting', '2026-02-03 14:13:20'),
(16, 'manage_votes', 'Manage voting sessions', '2026-02-03 14:13:20'),
(17, 'view_audit', 'View audit logs', '2026-02-03 14:13:20'),
(18, 'admin_access', 'Full administrative access', '2026-02-03 14:13:20');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pinjaman`
--

CREATE TABLE `pinjaman` (
  `id` int(11) NOT NULL,
  `anggota_id` int(11) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `interest_rate` decimal(5,2) NOT NULL,
  `term_months` int(11) NOT NULL,
  `status` enum('pending','approved','active','paid','rejected') DEFAULT 'pending',
  `approved_at` timestamp NULL DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `disbursed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pinjaman_angsuran`
--

CREATE TABLE `pinjaman_angsuran` (
  `id` int(11) NOT NULL,
  `pinjaman_id` int(11) NOT NULL,
  `installment_number` int(11) NOT NULL,
  `due_date` date NOT NULL,
  `principal_amount` decimal(15,2) NOT NULL,
  `interest_amount` decimal(15,2) NOT NULL,
  `total_amount` decimal(15,2) NOT NULL,
  `paid_amount` decimal(15,2) DEFAULT 0.00,
  `paid_at` timestamp NULL DEFAULT NULL,
  `status` enum('pending','paid','overdue') DEFAULT 'pending',
  `penalty` decimal(15,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(15,2) NOT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `category` varchar(100) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `rat_sessions`
--

CREATE TABLE `rat_sessions` (
  `id` int(11) NOT NULL,
  `cooperative_id` int(11) NOT NULL,
  `tahun` int(11) NOT NULL,
  `tanggal_rapat` date NOT NULL,
  `tempat` varchar(255) DEFAULT NULL,
  `agenda` text DEFAULT NULL,
  `status` enum('scheduled','in_progress','completed','cancelled') DEFAULT 'scheduled',
  `modal_pokok_sebelum` decimal(15,2) DEFAULT 0.00,
  `modal_pokok_setelah` decimal(15,2) DEFAULT 0.00,
  `persentase_perubahan` decimal(5,2) DEFAULT 0.00,
  `alasan_perubahan` text DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'super_admin', 'Super administrator with all access', '2026-02-03 14:13:20'),
(2, 'admin', 'Administrator/Pengurus', '2026-02-03 14:13:20'),
(3, 'pengawas', 'Pengawas with read/approve access', '2026-02-03 14:13:20'),
(4, 'anggota', 'Regular member', '2026-02-03 14:13:20'),
(5, 'calon_anggota', 'Prospective member', '2026-02-03 14:13:20');

-- --------------------------------------------------------

--
-- Struktur dari tabel `role_permissions`
--

CREATE TABLE `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `assigned_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `role_permissions`
--

INSERT INTO `role_permissions` (`role_id`, `permission_id`, `assigned_at`) VALUES
(2, 5, '2026-02-04 17:55:10'),
(2, 6, '2026-02-04 17:55:10'),
(2, 7, '2026-02-04 17:55:10'),
(2, 8, '2026-02-04 17:55:10'),
(2, 9, '2026-02-04 17:55:10'),
(2, 10, '2026-02-04 17:55:10'),
(2, 11, '2026-02-04 17:55:10'),
(2, 12, '2026-02-04 17:55:10'),
(2, 13, '2026-02-04 17:55:10'),
(2, 14, '2026-02-04 17:55:10'),
(2, 17, '2026-02-04 17:57:02');

-- --------------------------------------------------------

--
-- Struktur dari tabel `shu_distributions`
--

CREATE TABLE `shu_distributions` (
  `id` int(11) NOT NULL,
  `year` year(4) NOT NULL,
  `total_shu` decimal(15,2) NOT NULL,
  `distributed_at` timestamp NULL DEFAULT NULL,
  `status` enum('calculated','distributed') DEFAULT 'calculated',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `simpanan_transactions`
--

CREATE TABLE `simpanan_transactions` (
  `id` int(11) NOT NULL,
  `anggota_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `transaction_type` enum('deposit','withdraw') NOT NULL,
  `balance_after` decimal(15,2) NOT NULL,
  `transaction_date` timestamp NULL DEFAULT current_timestamp(),
  `description` text DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `simpanan_types`
--

CREATE TABLE `simpanan_types` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `interest_rate` decimal(5,2) DEFAULT 0.00,
  `minimum_balance` decimal(15,2) DEFAULT 0.00,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tenant_configs`
--

CREATE TABLE `tenant_configs` (
  `id` int(11) NOT NULL,
  `cooperative_id` int(11) NOT NULL,
  `active_modules` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`active_modules`)),
  `feature_flags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`feature_flags`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `tenant_configs`
--

INSERT INTO `tenant_configs` (`id`, `cooperative_id`, `active_modules`, `feature_flags`, `created_at`, `updated_at`) VALUES
(3, 4, '[]', '{\"multi_tenant\":true,\"modular\":true}', '2026-02-04 17:30:29', '2026-02-04 17:30:29');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `user_db_id` int(11) NOT NULL,
  `status` enum('active','inactive','pending') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `user_db_id`, `status`, `created_at`, `updated_at`) VALUES
(2, 'testuser', '$2y$10$dummyhash', 1, 'active', '2026-02-04 17:26:43', '2026-02-04 17:26:43'),
(5, '820800', '$2y$12$12ui9kSR2Sqh7mGRj2CHc.MyYEZBziRDuldj01y8aUMMJoWPfgUum', 1, 'active', '2026-02-04 17:30:29', '2026-02-04 17:30:29');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_roles`
--

CREATE TABLE `user_roles` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `assigned_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `user_roles`
--

INSERT INTO `user_roles` (`user_id`, `role_id`, `assigned_at`) VALUES
(5, 1, '2026-02-04 20:01:25'),
(5, 2, '2026-02-04 17:30:29');

-- --------------------------------------------------------

--
-- Struktur dari tabel `votes`
--

CREATE TABLE `votes` (
  `id` int(11) NOT NULL,
  `agenda` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `status` enum('draft','active','closed') DEFAULT 'draft',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `vote_ballots`
--

CREATE TABLE `vote_ballots` (
  `id` int(11) NOT NULL,
  `vote_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `choice` varchar(100) NOT NULL,
  `voted_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `v_cooperative_complete`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `v_cooperative_complete` (
`id` int(11)
,`nama` varchar(255)
,`jenis` longtext
,`badan_hukum` varchar(255)
,`tanggal_pendirian` date
,`npwp` varchar(50)
,`alamat_legal` text
,`kontak_resmi` varchar(255)
,`logo` varchar(255)
,`created_by` int(11)
,`created_at` timestamp
,`updated_at` timestamp
,`province_name` varchar(100)
,`regency_name` varchar(100)
,`district_name` varchar(100)
,`village_name` varchar(100)
,`admin_name` varchar(255)
,`admin_email` varchar(255)
,`admin_phone` varchar(20)
);

-- --------------------------------------------------------

--
-- Struktur untuk view `v_cooperative_complete`
--
DROP TABLE IF EXISTS `v_cooperative_complete`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_cooperative_complete`  AS SELECT `c`.`id` AS `id`, `c`.`nama` AS `nama`, `c`.`jenis` AS `jenis`, `c`.`badan_hukum` AS `badan_hukum`, `c`.`tanggal_pendirian` AS `tanggal_pendirian`, `c`.`npwp` AS `npwp`, `c`.`alamat_legal` AS `alamat_legal`, `c`.`kontak_resmi` AS `kontak_resmi`, `c`.`logo` AS `logo`, `c`.`created_by` AS `created_by`, `c`.`created_at` AS `created_at`, `c`.`updated_at` AS `updated_at`, `p`.`name` AS `province_name`, `r`.`name` AS `regency_name`, `d`.`name` AS `district_name`, `v`.`name` AS `village_name`, `u`.`nama` AS `admin_name`, `u`.`email` AS `admin_email`, `u`.`phone` AS `admin_phone` FROM ((((((`cooperatives` `c` left join `alamat_db`.`provinces` `p` on(`c`.`province_id` = `p`.`id`)) left join `alamat_db`.`regencies` `r` on(`c`.`regency_id` = `r`.`id`)) left join `alamat_db`.`districts` `d` on(`c`.`district_id` = `d`.`id`)) left join `alamat_db`.`villages` `v` on(`c`.`village_id` = `v`.`id`)) left join `users` `cu` on(`c`.`created_by` = `cu`.`id`)) left join `people_db`.`users` `u` on(`cu`.`user_db_id` = `u`.`id`)) ;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `agent_sales`
--
ALTER TABLE `agent_sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `agent_id` (`agent_id`),
  ADD KEY `approved_by` (`approved_by`);

--
-- Indeks untuk tabel `anggota`
--
ALTER TABLE `anggota`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nomor_anggota` (`nomor_anggota`),
  ADD KEY `idx_anggota_user` (`user_id`);

--
-- Indeks untuk tabel `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_table_record` (`table_name`,`record_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indeks untuk tabel `chart_of_accounts`
--
ALTER TABLE `chart_of_accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_cooperative_code` (`cooperative_id`,`code`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `idx_chart_cooperative` (`cooperative_id`);

--
-- Indeks untuk tabel `configs`
--
ALTER TABLE `configs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `key_name` (`key_name`);

--
-- Indeks untuk tabel `cooperatives`
--
ALTER TABLE `cooperatives`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_cooperative_province` (`province_id`),
  ADD KEY `idx_cooperative_regency` (`regency_id`),
  ADD KEY `idx_cooperative_district` (`district_id`),
  ADD KEY `idx_cooperative_village` (`village_id`),
  ADD KEY `idx_nomor_bh` (`nomor_bh`),
  ADD KEY `idx_nib` (`nib`),
  ADD KEY `idx_nik_koperasi` (`nik_koperasi`),
  ADD KEY `idx_status_badan_hukum` (`status_badan_hukum`),
  ADD KEY `idx_tanggal_status_terakhir` (`tanggal_status_terakhir`);

--
-- Indeks untuk tabel `cooperative_document_history`
--
ALTER TABLE `cooperative_document_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_cooperative_document` (`cooperative_id`,`document_type`),
  ADD KEY `idx_tanggal_efektif` (`tanggal_efektif`),
  ADD KEY `idx_document_type` (`document_type`);

--
-- Indeks untuk tabel `cooperative_financial_settings`
--
ALTER TABLE `cooperative_financial_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_cooperative_year` (`cooperative_id`,`tahun_buku`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_cooperative_year` (`cooperative_id`,`tahun_buku`),
  ADD KEY `idx_tahun_buku` (`tahun_buku`);

--
-- Indeks untuk tabel `cooperative_status_history`
--
ALTER TABLE `cooperative_status_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_cooperative_id` (`cooperative_id`),
  ADD KEY `idx_tanggal_efektif` (`tanggal_efektif`),
  ADD KEY `idx_approval_status` (`approval_status`);

--
-- Indeks untuk tabel `cooperative_types`
--
ALTER TABLE `cooperative_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indeks untuk tabel `general_ledger`
--
ALTER TABLE `general_ledger`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `account_id` (`account_id`,`period`);

--
-- Indeks untuk tabel `journal_entries`
--
ALTER TABLE `journal_entries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_journal_posted_by` (`posted_by`);

--
-- Indeks untuk tabel `journal_entry_details`
--
ALTER TABLE `journal_entry_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `journal_entry_id` (`journal_entry_id`),
  ADD KEY `account_id` (`account_id`);

--
-- Indeks untuk tabel `member_shu`
--
ALTER TABLE `member_shu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `anggota_id` (`anggota_id`),
  ADD KEY `shu_distribution_id` (`shu_distribution_id`);

--
-- Indeks untuk tabel `modal_pokok_changes`
--
ALTER TABLE `modal_pokok_changes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `referensi_id` (`referensi_id`),
  ADD KEY `idx_cooperative_date` (`cooperative_id`,`tanggal_efektif`),
  ADD KEY `idx_perubahan_type` (`perubahan_type`),
  ADD KEY `idx_tanggal_efektif` (`tanggal_efektif`);

--
-- Indeks untuk tabel `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_notifications_user_read` (`user_id`,`read_at`);

--
-- Indeks untuk tabel `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indeks untuk tabel `pengawas`
--
ALTER TABLE `pengawas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pengawas_user` (`user_id`);

--
-- Indeks untuk tabel `pengurus`
--
ALTER TABLE `pengurus`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pengurus_user` (`user_id`);

--
-- Indeks untuk tabel `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indeks untuk tabel `pinjaman`
--
ALTER TABLE `pinjaman`
  ADD PRIMARY KEY (`id`),
  ADD KEY `approved_by` (`approved_by`),
  ADD KEY `idx_pinjaman_anggota` (`anggota_id`),
  ADD KEY `idx_pinjaman_status` (`status`);

--
-- Indeks untuk tabel `pinjaman_angsuran`
--
ALTER TABLE `pinjaman_angsuran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pinjaman_id` (`pinjaman_id`);

--
-- Indeks untuk tabel `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `rat_sessions`
--
ALTER TABLE `rat_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `approved_by` (`approved_by`),
  ADD KEY `idx_cooperative_tahun` (`cooperative_id`,`tahun`),
  ADD KEY `idx_tanggal_rapat` (`tanggal_rapat`),
  ADD KEY `idx_status` (`status`);

--
-- Indeks untuk tabel `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indeks untuk tabel `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`role_id`,`permission_id`),
  ADD KEY `permission_id` (`permission_id`);

--
-- Indeks untuk tabel `shu_distributions`
--
ALTER TABLE `shu_distributions`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `simpanan_transactions`
--
ALTER TABLE `simpanan_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `type_id` (`type_id`),
  ADD KEY `approved_by` (`approved_by`),
  ADD KEY `idx_simpanan_anggota` (`anggota_id`),
  ADD KEY `idx_simpanan_date` (`transaction_date`);

--
-- Indeks untuk tabel `simpanan_types`
--
ALTER TABLE `simpanan_types`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tenant_configs`
--
ALTER TABLE `tenant_configs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cooperative_id` (`cooperative_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `idx_users_user_db_id` (`user_db_id`);

--
-- Indeks untuk tabel `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `role_id` (`role_id`);

--
-- Indeks untuk tabel `votes`
--
ALTER TABLE `votes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_votes_created_by` (`created_by`);

--
-- Indeks untuk tabel `vote_ballots`
--
ALTER TABLE `vote_ballots`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_vote_ballots_vote_user` (`vote_id`,`user_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `agent_sales`
--
ALTER TABLE `agent_sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `anggota`
--
ALTER TABLE `anggota`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `chart_of_accounts`
--
ALTER TABLE `chart_of_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT untuk tabel `configs`
--
ALTER TABLE `configs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `cooperatives`
--
ALTER TABLE `cooperatives`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `cooperative_document_history`
--
ALTER TABLE `cooperative_document_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `cooperative_financial_settings`
--
ALTER TABLE `cooperative_financial_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `cooperative_status_history`
--
ALTER TABLE `cooperative_status_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `cooperative_types`
--
ALTER TABLE `cooperative_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `general_ledger`
--
ALTER TABLE `general_ledger`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `journal_entries`
--
ALTER TABLE `journal_entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `journal_entry_details`
--
ALTER TABLE `journal_entry_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `member_shu`
--
ALTER TABLE `member_shu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `modal_pokok_changes`
--
ALTER TABLE `modal_pokok_changes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pengawas`
--
ALTER TABLE `pengawas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pengurus`
--
ALTER TABLE `pengurus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT untuk tabel `pinjaman`
--
ALTER TABLE `pinjaman`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pinjaman_angsuran`
--
ALTER TABLE `pinjaman_angsuran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `rat_sessions`
--
ALTER TABLE `rat_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `shu_distributions`
--
ALTER TABLE `shu_distributions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `simpanan_transactions`
--
ALTER TABLE `simpanan_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `simpanan_types`
--
ALTER TABLE `simpanan_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tenant_configs`
--
ALTER TABLE `tenant_configs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `votes`
--
ALTER TABLE `votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `vote_ballots`
--
ALTER TABLE `vote_ballots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `agent_sales`
--
ALTER TABLE `agent_sales`
  ADD CONSTRAINT `agent_sales_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `agent_sales_ibfk_2` FOREIGN KEY (`agent_id`) REFERENCES `anggota` (`id`),
  ADD CONSTRAINT `agent_sales_ibfk_3` FOREIGN KEY (`approved_by`) REFERENCES `people_db`.`users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `anggota`
--
ALTER TABLE `anggota`
  ADD CONSTRAINT `anggota_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `people_db`.`users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `people_db`.`users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `chart_of_accounts`
--
ALTER TABLE `chart_of_accounts`
  ADD CONSTRAINT `chart_of_accounts_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `chart_of_accounts` (`id`);

--
-- Ketidakleluasaan untuk tabel `cooperative_document_history`
--
ALTER TABLE `cooperative_document_history`
  ADD CONSTRAINT `cooperative_document_history_ibfk_1` FOREIGN KEY (`cooperative_id`) REFERENCES `cooperatives` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `cooperative_financial_settings`
--
ALTER TABLE `cooperative_financial_settings`
  ADD CONSTRAINT `cooperative_financial_settings_ibfk_1` FOREIGN KEY (`cooperative_id`) REFERENCES `cooperatives` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cooperative_financial_settings_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `general_ledger`
--
ALTER TABLE `general_ledger`
  ADD CONSTRAINT `general_ledger_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `chart_of_accounts` (`id`);

--
-- Ketidakleluasaan untuk tabel `journal_entries`
--
ALTER TABLE `journal_entries`
  ADD CONSTRAINT `fk_journal_posted_by` FOREIGN KEY (`posted_by`) REFERENCES `people_db`.`users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `journal_entry_details`
--
ALTER TABLE `journal_entry_details`
  ADD CONSTRAINT `journal_entry_details_ibfk_1` FOREIGN KEY (`journal_entry_id`) REFERENCES `journal_entries` (`id`),
  ADD CONSTRAINT `journal_entry_details_ibfk_2` FOREIGN KEY (`account_id`) REFERENCES `chart_of_accounts` (`id`);

--
-- Ketidakleluasaan untuk tabel `member_shu`
--
ALTER TABLE `member_shu`
  ADD CONSTRAINT `member_shu_ibfk_1` FOREIGN KEY (`anggota_id`) REFERENCES `anggota` (`id`),
  ADD CONSTRAINT `member_shu_ibfk_2` FOREIGN KEY (`shu_distribution_id`) REFERENCES `shu_distributions` (`id`);

--
-- Ketidakleluasaan untuk tabel `modal_pokok_changes`
--
ALTER TABLE `modal_pokok_changes`
  ADD CONSTRAINT `modal_pokok_changes_ibfk_1` FOREIGN KEY (`cooperative_id`) REFERENCES `cooperatives` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `modal_pokok_changes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `modal_pokok_changes_ibfk_3` FOREIGN KEY (`referensi_id`) REFERENCES `rat_sessions` (`id`);

--
-- Ketidakleluasaan untuk tabel `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `people_db`.`users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Ketidakleluasaan untuk tabel `pengawas`
--
ALTER TABLE `pengawas`
  ADD CONSTRAINT `pengawas_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `people_db`.`users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pengurus`
--
ALTER TABLE `pengurus`
  ADD CONSTRAINT `pengurus_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `people_db`.`users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pinjaman`
--
ALTER TABLE `pinjaman`
  ADD CONSTRAINT `pinjaman_ibfk_1` FOREIGN KEY (`anggota_id`) REFERENCES `anggota` (`id`),
  ADD CONSTRAINT `pinjaman_ibfk_2` FOREIGN KEY (`approved_by`) REFERENCES `people_db`.`users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `pinjaman_angsuran`
--
ALTER TABLE `pinjaman_angsuran`
  ADD CONSTRAINT `pinjaman_angsuran_ibfk_1` FOREIGN KEY (`pinjaman_id`) REFERENCES `pinjaman` (`id`);

--
-- Ketidakleluasaan untuk tabel `rat_sessions`
--
ALTER TABLE `rat_sessions`
  ADD CONSTRAINT `rat_sessions_ibfk_1` FOREIGN KEY (`cooperative_id`) REFERENCES `cooperatives` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rat_sessions_ibfk_2` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `simpanan_transactions`
--
ALTER TABLE `simpanan_transactions`
  ADD CONSTRAINT `simpanan_transactions_ibfk_1` FOREIGN KEY (`anggota_id`) REFERENCES `anggota` (`id`),
  ADD CONSTRAINT `simpanan_transactions_ibfk_2` FOREIGN KEY (`type_id`) REFERENCES `simpanan_types` (`id`),
  ADD CONSTRAINT `simpanan_transactions_ibfk_3` FOREIGN KEY (`approved_by`) REFERENCES `people_db`.`users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `tenant_configs`
--
ALTER TABLE `tenant_configs`
  ADD CONSTRAINT `tenant_configs_ibfk_1` FOREIGN KEY (`cooperative_id`) REFERENCES `cooperatives` (`id`);

--
-- Ketidakleluasaan untuk tabel `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `votes`
--
ALTER TABLE `votes`
  ADD CONSTRAINT `fk_votes_created_by` FOREIGN KEY (`created_by`) REFERENCES `people_db`.`users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `vote_ballots`
--
ALTER TABLE `vote_ballots`
  ADD CONSTRAINT `vote_ballots_ibfk_1` FOREIGN KEY (`vote_id`) REFERENCES `votes` (`id`),
  ADD CONSTRAINT `vote_ballots_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `people_db`.`users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
