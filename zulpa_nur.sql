-- ======================================================
-- DATABASE: zulpa_nur
-- Sistem Penjualan Boneka PD ZULPA NUR
-- ======================================================

CREATE DATABASE IF NOT EXISTS zulpa_nur;
USE zulpa_nur;

-- ======================================================
-- TABEL: boneka
-- ======================================================
DROP TABLE IF EXISTS `boneka`;
CREATE TABLE `boneka` (
  `id_boneka` int(11) NOT NULL AUTO_INCREMENT,
  `kode_boneka` varchar(50) DEFAULT NULL,
  `nama_boneka` varchar(100) NOT NULL,
  `stok` int(11) DEFAULT 0,
  `harga` int(11) NOT NULL,
  `gambar` varchar(10) DEFAULT '🧸',
  PRIMARY KEY (`id_boneka`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `boneka` (`id_boneka`, `kode_boneka`, `nama_boneka`, `stok`, `harga`, `gambar`) VALUES
(1, 'BD-001', 'Teddy Bear Pink', 50, 75000, '🧸'),
(2, 'BD-002', 'Boneka Kelinci Putih', 45, 68000, '🐰'),
(3, 'BD-003', 'Boneka Panda Gendut', 30, 95000, '🐼'),
(4, 'BD-004', 'Boneka Dino Hijau', 25, 85000, '🦕'),
(5, 'BD-005', 'Boneka Kucing Oren', 60, 60000, '🐱'),
(6, 'BD-006', 'Boneka Beruang Coklat', 40, 70000, '🐻'),
(7, 'BD-007', 'Boneka Monyet Lucu', 35, 55000, '🐵');

-- ======================================================
-- TABEL: customer
-- ======================================================
DROP TABLE IF EXISTS `customer`;
CREATE TABLE `customer` (
  `id_customer` int(11) NOT NULL AUTO_INCREMENT,
  `nama_customer` varchar(100) NOT NULL,
  `alamat` text DEFAULT NULL,
  `no_telepon` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id_customer`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `customer` (`id_customer`, `nama_customer`, `alamat`, `no_telepon`) VALUES
(1, 'MASNAH (INOY - CASH)', 'KP. CAKUING NO.39 RT.004/RW.004 KEL. JATISARI, KEC. JATIASIH BEKASI', '08123456789'),
(2, 'BUDI SANTOSO', 'Jl. Merdeka No.10, Jakarta', '08987654321'),
(3, 'RINA WATI', 'Jl. Diponegoro No.5, Bandung', '087812345678'),
(4, 'ANDI PRATAMA', 'Jl. Sudirman No.20, Surabaya', '085712345678');

-- ======================================================
-- TABEL: petugas
-- ======================================================
DROP TABLE IF EXISTS `petugas`;
CREATE TABLE `petugas` (
  `id_petugas` int(11) NOT NULL AUTO_INCREMENT,
  `nama_petugas` varchar(100) NOT NULL,
  `jabatan` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_petugas`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `petugas` (`id_petugas`, `nama_petugas`, `jabatan`) VALUES
(1, 'Admin', 'Owner'),
(2, 'Siti', 'Kasir'),
(3, 'Budi', 'Produksi');

-- ======================================================
-- TABEL: nota
-- ======================================================
DROP TABLE IF EXISTS `nota`;
CREATE TABLE `nota` (
  `no_nota` int(11) NOT NULL AUTO_INCREMENT,
  `tanggal` date NOT NULL,
  `total` int(11) DEFAULT 0,
  `id_customer` int(11) DEFAULT NULL,
  `id_petugas` int(11) DEFAULT NULL,
  PRIMARY KEY (`no_nota`),
  KEY `id_customer` (`id_customer`),
  KEY `id_petugas` (`id_petugas`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `nota` (`no_nota`, `tanggal`, `total`, `id_customer`, `id_petugas`) VALUES
(1, '2024-01-15', 150000, 1, 1),
(2, '2024-01-20', 136000, 2, 2),
(3, '2024-02-10', 285000, 3, 1);

-- ======================================================
-- TABEL: detail_nota
-- ======================================================
DROP TABLE IF EXISTS `detail_nota`;
CREATE TABLE `detail_nota` (
  `id_detail` int(11) NOT NULL AUTO_INCREMENT,
  `no_nota` int(11) DEFAULT NULL,
  `id_boneka` int(11) DEFAULT NULL,
  `jumlah` int(11) NOT NULL,
  `harga` int(11) NOT NULL,
  `subtotal` int(11) NOT NULL,
  PRIMARY KEY (`id_detail`),
  KEY `no_nota` (`no_nota`),
  KEY `id_boneka` (`id_boneka`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `detail_nota` (`id_detail`, `no_nota`, `id_boneka`, `jumlah`, `harga`, `subtotal`) VALUES
(1, 1, 1, 2, 75000, 150000),
(2, 2, 2, 2, 68000, 136000),
(3, 3, 3, 3, 95000, 285000);

-- ======================================================
-- TAMPILAN DATA
-- ======================================================
SELECT '=== DATA BONEKA ===' as '';
SELECT id_boneka, kode_boneka, nama_boneka, stok, harga, gambar FROM boneka;

SELECT '=== DATA CUSTOMER ===' as '';
SELECT id_customer, nama_customer, alamat, no_telepon FROM customer;

SELECT '=== DATA PETUGAS ===' as '';
SELECT id_petugas, nama_petugas, jabatan FROM petugas;

SELECT '=== DATA NOTA ===' as '';
SELECT n.*, c.nama_customer, p.nama_petugas 
FROM nota n 
JOIN customer c ON n.id_customer = c.id_customer 
JOIN petugas p ON n.id_petugas = p.id_petugas;

SELECT '=== DATA DETAIL NOTA ===' as '';
SELECT dn.*, b.kode_boneka, b.nama_boneka, b.gambar
FROM detail_nota dn 
JOIN boneka b ON dn.id_boneka = b.id_boneka;