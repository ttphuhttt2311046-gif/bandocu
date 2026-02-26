-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Feb 26, 2026 at 04:53 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bandocumoinhat`
--

-- --------------------------------------------------------

--
-- Table structure for table `banner`
--

DROP TABLE IF EXISTS `banner`;
CREATE TABLE IF NOT EXISTS `banner` (
  `id` int NOT NULL AUTO_INCREMENT,
  `hinh` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trangthai` tinyint DEFAULT '1',
  `thu_tu` int DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `banner`
--

INSERT INTO `banner` (`id`, `hinh`, `link`, `trangthai`, `thu_tu`) VALUES
(5, 'assets/uploads/banner/1769862417_Máy chạy bộ Rovera.png', 'http://localhost/webbandocudoan2/product.php?id=30', 1, 0),
(6, 'assets/uploads/banner/1769862893_bếp 4 h.png', 'http://localhost/webbandocudoan2/product.php?id=26', 1, 1),
(7, 'assets/uploads/banner/1769955249_nubia.png', 'http://localhost/webbandocudoan2/product.php?id=12', 1, 3);

-- --------------------------------------------------------

--
-- Table structure for table `chitietdonhang`
--

DROP TABLE IF EXISTS `chitietdonhang`;
CREATE TABLE IF NOT EXISTS `chitietdonhang` (
  `maChiTiet` int NOT NULL AUTO_INCREMENT,
  `maDonHang` int NOT NULL,
  `maSanPham` int NOT NULL,
  `maNguoiBan` int DEFAULT NULL,
  `soLuong` int DEFAULT '1',
  `donGia` decimal(15,2) DEFAULT '0.00',
  `thanhTien` decimal(15,2) GENERATED ALWAYS AS ((`soLuong` * `donGia`)) STORED,
  PRIMARY KEY (`maChiTiet`),
  KEY `fk_chitietdonhang_donhang` (`maDonHang`),
  KEY `fk_chitietdonhang_sanpham` (`maSanPham`),
  KEY `fk_chitietdonhang_nguoiBan` (`maNguoiBan`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chitietdonhang`
--

INSERT INTO `chitietdonhang` (`maChiTiet`, `maDonHang`, `maSanPham`, `maNguoiBan`, `soLuong`, `donGia`) VALUES
(13, 22, 15, 3, 2, 840000.00),
(14, 23, 18, 3, 1, 500000.00),
(15, 24, 61, 7, 1, 1000000000.00),
(16, 25, 60, 7, 1, 10000000.00),
(17, 26, 58, 7, 2, 7000000.00),
(18, 27, 59, 7, 1, 6500000.00),
(19, 28, 58, 7, 2, 7000000.00),
(20, 29, 11, 3, 3, 4000000.00),
(21, 30, 59, 7, 1, 6500000.00),
(22, 31, 30, 3, 2, 50000000.00),
(23, 32, 57, 7, 2, 15000000.00),
(24, 33, 101, 14, 1, 499000000.00),
(25, 33, 102, 14, 1, 9900000000.00),
(26, 33, 84, 12, 1, 3200000.00),
(27, 34, 101, 14, 1, 499000000.00),
(28, 35, 101, 14, 1, 499000000.00),
(29, 36, 101, 14, 2, 499000000.00),
(30, 37, 101, 14, 1, 499000000.00),
(31, 38, 102, 14, 1, 9900000000.00),
(32, 39, 103, 14, 1, 3999000000.00),
(33, 40, 7, 2, 1, 7000000.00),
(34, 41, 98, 13, 2, 20000000.00),
(35, 41, 99, 14, 1, 85000000.00),
(36, 41, 101, 14, 1, 499000000.00),
(37, 41, 102, 14, 1, 9900000000.00),
(38, 42, 103, 14, 1, 3999000000.00),
(39, 45, 101, 14, 1, 499000000.00),
(40, 46, 101, 14, 1, 499000000.00),
(41, 47, 101, 14, 1, 499000000.00),
(42, 48, 101, 14, 1, 499000000.00),
(43, 49, 101, 14, 1, 499000000.00),
(44, 50, 101, 14, 1, 499000000.00),
(45, 51, 101, 14, 1, 499000000.00),
(46, 52, 84, 12, 1, 3200000.00),
(47, 57, 107, 17, 1, 146250000.00),
(48, 58, 44, 2, 1, 2625000.00);

-- --------------------------------------------------------

--
-- Table structure for table `counter`
--

DROP TABLE IF EXISTS `counter`;
CREATE TABLE IF NOT EXISTS `counter` (
  `id` int NOT NULL AUTO_INCREMENT,
  `total` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `counter`
--

INSERT INTO `counter` (`id`, `total`) VALUES
(1, 98);

-- --------------------------------------------------------

--
-- Table structure for table `danhgia`
--

DROP TABLE IF EXISTS `danhgia`;
CREATE TABLE IF NOT EXISTS `danhgia` (
  `id` int NOT NULL AUTO_INCREMENT,
  `maSanPham` int NOT NULL,
  `maNguoiDung` int NOT NULL,
  `soSao` tinyint NOT NULL,
  `binhLuan` text COLLATE utf8mb4_unicode_ci,
  `ngayDanhGia` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_sanpham_nguoidung` (`maSanPham`,`maNguoiDung`),
  KEY `fk_danhgia_nguoidung` (`maNguoiDung`)
) ;

--
-- Dumping data for table `danhgia`
--

INSERT INTO `danhgia` (`id`, `maSanPham`, `maNguoiDung`, `soSao`, `binhLuan`, `ngayDanhGia`) VALUES
(2, 101, 9, 3, 'sản phẩm cũng được', '2026-02-12 23:11:35');

-- --------------------------------------------------------

--
-- Table structure for table `danhmuc`
--

DROP TABLE IF EXISTS `danhmuc`;
CREATE TABLE IF NOT EXISTS `danhmuc` (
  `maDanhMuc` int NOT NULL AUTO_INCREMENT,
  `tenDanhMuc` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `moTa` text COLLATE utf8mb4_unicode_ci,
  `trangThai` tinyint(1) DEFAULT '0',
  `thu_tu` int DEFAULT '0',
  PRIMARY KEY (`maDanhMuc`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `danhmuc`
--

INSERT INTO `danhmuc` (`maDanhMuc`, `tenDanhMuc`, `moTa`, `trangThai`, `thu_tu`) VALUES
(1, 'Điện Thoại', NULL, 0, 1),
(2, 'Thiết Bị Gia Dụng', NULL, 0, 0),
(3, 'Điện Máy', NULL, 1, 5),
(4, 'Khác', NULL, 1, 6),
(5, 'PC', NULL, 0, 3),
(6, 'Thiết Bị Điện Tử', NULL, 0, 2),
(7, 'Đồng hồ', NULL, 1, 4),
(8, 'Xe Ôtô', '', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `donhang`
--

DROP TABLE IF EXISTS `donhang`;
CREATE TABLE IF NOT EXISTS `donhang` (
  `maDonHang` int NOT NULL AUTO_INCREMENT,
  `ngayDat` datetime DEFAULT CURRENT_TIMESTAMP,
  `tongTien` decimal(15,2) DEFAULT '0.00',
  `trangThai` enum('Đang xử lý','Đã thanh toán','Đã hủy','Hoàn thành') COLLATE utf8mb4_unicode_ci DEFAULT 'Đang xử lý',
  `maNguoiMua` int DEFAULT NULL,
  `maGiaoDichVNPAY` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`maDonHang`),
  KEY `fk_donhang_nguoiMua` (`maNguoiMua`)
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `donhang`
--

INSERT INTO `donhang` (`maDonHang`, `ngayDat`, `tongTien`, `trangThai`, `maNguoiMua`, `maGiaoDichVNPAY`) VALUES
(22, '2025-11-10 00:00:00', 1680000.00, 'Hoàn thành', 1, NULL),
(23, '2025-11-10 00:00:00', 500000.00, 'Hoàn thành', 1, NULL),
(24, '2025-11-11 00:00:00', 1000000000.00, 'Hoàn thành', 1, NULL),
(25, '2025-11-11 00:00:00', 10000000.00, 'Hoàn thành', 1, NULL),
(26, '2025-11-11 00:00:00', 14000000.00, 'Hoàn thành', 7, NULL),
(27, '2025-11-15 00:00:00', 6500000.00, 'Hoàn thành', 1, NULL),
(28, '2025-11-15 00:00:00', 14000000.00, 'Hoàn thành', 1, NULL),
(29, '2025-11-15 00:00:00', 12000000.00, 'Hoàn thành', 1, NULL),
(30, '2025-11-16 00:00:00', 6500000.00, 'Hoàn thành', 1, NULL),
(31, '2025-11-17 00:00:00', 100000000.00, 'Hoàn thành', 1, NULL),
(32, '2026-01-15 00:00:00', 30000000.00, 'Hoàn thành', 11, NULL),
(33, '2026-01-18 00:00:00', 10402200000.00, 'Hoàn thành', 11, NULL),
(34, '2026-01-18 00:00:00', 499000000.00, 'Hoàn thành', 11, NULL),
(35, '2026-01-18 00:00:00', 499000000.00, 'Hoàn thành', 11, NULL),
(36, '2026-01-18 00:00:00', 998000000.00, 'Hoàn thành', 14, NULL),
(37, '2026-01-18 00:00:00', 499000000.00, 'Hoàn thành', 14, NULL),
(38, '2026-01-23 00:00:00', 9900000000.00, 'Hoàn thành', 1, NULL),
(39, '2026-01-23 00:00:00', 3999000000.00, 'Hoàn thành', 2, NULL),
(40, '2026-01-23 00:00:00', 7000000.00, 'Hoàn thành', 2, NULL),
(41, '2026-01-23 00:00:00', 10524000000.00, 'Hoàn thành', 16, NULL),
(42, '2026-01-23 00:00:00', 3999000000.00, 'Hoàn thành', 2, NULL),
(43, '2026-02-06 00:00:00', 3999000000.00, 'Hoàn thành', 1, NULL),
(44, '2026-02-06 00:00:00', 3999000000.00, 'Hoàn thành', 1, NULL),
(45, '2026-02-12 22:29:07', 499000000.00, 'Hoàn thành', 9, NULL),
(46, '2026-02-12 22:31:52', 499000000.00, 'Đang xử lý', 9, NULL),
(47, '2026-02-12 22:31:55', 499000000.00, 'Đang xử lý', 9, NULL),
(48, '2026-02-12 22:32:01', 499000000.00, 'Đang xử lý', 9, NULL),
(49, '2026-02-12 22:32:05', 499000000.00, 'Đang xử lý', 9, NULL),
(50, '2026-02-12 22:33:50', 499000000.00, '', 9, NULL),
(51, '2026-02-12 22:36:15', 499000000.00, 'Đang xử lý', 9, NULL),
(52, '2026-02-13 09:32:05', 3200000.00, '', 1, NULL),
(53, '2026-02-26 11:15:30', 146250000.00, 'Đang xử lý', 1, NULL),
(54, '2026-02-26 11:15:52', 146250000.00, 'Đang xử lý', 1, NULL),
(55, '2026-02-26 11:16:00', 146250000.00, 'Đang xử lý', 1, NULL),
(56, '2026-02-26 11:16:22', 146250000.00, 'Đang xử lý', 1, NULL),
(57, '2026-02-26 11:20:44', 146250000.00, 'Đang xử lý', 1, NULL),
(58, '2026-02-26 11:24:59', 2625000.00, 'Đang xử lý', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `duyet_settings`
--

DROP TABLE IF EXISTS `duyet_settings`;
CREATE TABLE IF NOT EXISTS `duyet_settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `auto_review` tinyint(1) DEFAULT '0',
  `banned_words` text COLLATE utf8mb4_unicode_ci,
  `warning_message` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'Sản phẩm chứa từ bị cấm!',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `duyet_settings`
--

INSERT INTO `duyet_settings` (`id`, `auto_review`, `banned_words`, `warning_message`, `updated_at`) VALUES
(1, 1, 'đồi trụy,mới 100%,ma túy,bóng cười,lựu đạn thật,súng thật', 'Sản phẩm chứa từ bị cấm!', '2025-11-16 06:47:44');

-- --------------------------------------------------------

--
-- Table structure for table `nhantin`
--

DROP TABLE IF EXISTS `nhantin`;
CREATE TABLE IF NOT EXISTS `nhantin` (
  `maThongBao` int NOT NULL AUTO_INCREMENT,
  `noiDung` text COLLATE utf8mb4_unicode_ci,
  `maNguoiGui` int DEFAULT NULL,
  `maNguoiNhan` int DEFAULT NULL,
  `ngayGui` datetime DEFAULT CURRENT_TIMESTAMP,
  `phanHoi` text COLLATE utf8mb4_unicode_ci,
  `trangThai` enum('chua_xem','da_xem') COLLATE utf8mb4_unicode_ci DEFAULT 'chua_xem',
  `thoiGian` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`maThongBao`),
  KEY `fk_thongbao_nguoiGui` (`maNguoiGui`),
  KEY `fk_thongbao_nguoiNhan` (`maNguoiNhan`)
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nhantin`
--

INSERT INTO `nhantin` (`maThongBao`, `noiDung`, `maNguoiGui`, `maNguoiNhan`, `ngayGui`, `phanHoi`, `trangThai`, `thoiGian`) VALUES
(29, 'Bạn có đơn hàng mới #23', 1, 3, '2025-11-10 09:04:01', NULL, 'da_xem', '2025-11-09 19:04:01'),
(30, '\r\n    📦 <b>NguyenVan</b> vừa đặt <b>1</b> sản phẩm <b>“Ssd 512GB”</b><br>\r\n    💰 Đơn giá: 500.000₫<br>\r\n    🧮 Tổng: <b>500.000₫</b><br>\r\n    🧾 Mã đơn: #23\r\n    ', 1, 3, '2025-11-10 09:04:01', NULL, 'da_xem', '2025-11-09 19:04:01'),
(31, 'xin chào', 1, 2, '2025-11-11 10:09:43', '[1] [03:09] xin chào', 'da_xem', '2025-11-10 20:09:43'),
(32, 'xin chào', 1, 7, '2025-11-11 10:25:38', '[1] [03:10] xin chào\n[1] [03:25] chào', 'da_xem', '2025-11-10 20:10:54'),
(33, 'Bạn có đơn hàng mới #24', 1, 7, '2025-11-11 10:26:07', NULL, 'da_xem', '2025-11-10 20:26:07'),
(34, '\r\n    📦 <b>NguyenVan</b> vừa đặt <b>1</b> sản phẩm <b>“BMW X5”</b><br>\r\n    💰 Đơn giá: 1.000.000.000₫<br>\r\n    🧮 Tổng: <b>1.000.000.000₫</b><br>\r\n    🧾 Mã đơn: #24\r\n    ', 1, 7, '2025-11-11 10:26:07', NULL, 'da_xem', '2025-11-10 20:26:07'),
(35, 'Bạn có đơn hàng mới #25', 1, 7, '2025-11-11 10:26:57', NULL, 'chua_xem', '2025-11-10 20:26:57'),
(36, '\r\n    📦 <b>NguyenVan</b> vừa đặt <b>1</b> sản phẩm <b>“VIVO V60”</b><br>\r\n    💰 Đơn giá: 10.000.000₫<br>\r\n    🧮 Tổng: <b>10.000.000₫</b><br>\r\n    🧾 Mã đơn: #25\r\n    ', 1, 7, '2025-11-11 10:26:57', NULL, 'chua_xem', '2025-11-10 20:26:57'),
(37, 'Bạn có đơn hàng mới #26', 7, 7, '2025-11-11 10:32:58', NULL, 'chua_xem', '2025-11-10 20:32:58'),
(38, '\r\n    📦 <b>Phu</b> vừa đặt <b>2</b> sản phẩm <b>“Samsung Galaxy A17 5G”</b><br>\r\n    💰 Đơn giá: 7.000.000₫<br>\r\n    🧮 Tổng: <b>14.000.000₫</b><br>\r\n    🧾 Mã đơn: #26\r\n    ', 7, 7, '2025-11-11 10:32:58', NULL, 'chua_xem', '2025-11-10 20:32:58'),
(39, 'Bạn có đơn hàng mới #27', 1, 7, '2025-11-15 16:55:59', NULL, 'chua_xem', '2025-11-15 02:55:59'),
(40, '\r\n    📦 <b>NguyenVan</b> vừa đặt <b>1</b> sản phẩm <b>“Realme C85 Pro”</b><br>\r\n    💰 Đơn giá: 6.500.000₫<br>\r\n    🧮 Tổng: <b>6.500.000₫</b><br>\r\n    🧾 Mã đơn: #27\r\n    ', 1, 7, '2025-11-15 16:55:59', NULL, 'chua_xem', '2025-11-15 02:55:59'),
(41, 'Bạn có đơn hàng mới #28', 1, 7, '2025-11-15 17:02:42', NULL, 'chua_xem', '2025-11-15 03:02:42'),
(42, '\r\n            📦 <b>NguyenVan</b> vừa đặt <b>2</b> sản phẩm <b>“Samsung Galaxy A17 5G”</b><br>\r\n            💰 Đơn giá: 7.000.000₫<br>\r\n            🧮 Tổng: <b>14.000.000₫</b><br>\r\n            🧾 Mã đơn: #28\r\n            ', 1, 7, '2025-11-15 17:02:42', NULL, 'chua_xem', '2025-11-15 03:02:42'),
(43, 'Bạn có đơn hàng mới #29', 1, 3, '2025-11-15 18:29:23', NULL, 'da_xem', '2025-11-15 04:29:23'),
(44, '\r\n            📦 <b>NguyenVan</b> vừa đặt <b>3</b> sản phẩm <b>“Máy ảnh Canon FTb”</b><br>\r\n            💰 Đơn giá: 4.000.000₫<br>\r\n            🧮 Tổng: <b>12.000.000₫</b><br>\r\n            🧾 Mã đơn: #29\r\n            ', 1, 3, '2025-11-15 18:29:23', NULL, 'da_xem', '2025-11-15 04:29:23'),
(45, 'Bạn có đơn hàng mới #30', 1, 7, '2025-11-16 20:40:00', NULL, 'chua_xem', '2025-11-16 06:40:00'),
(46, '\r\n            📦 <b>NguyenVan</b> vừa đặt <b>1</b> sản phẩm <b>“Realme C85 Pro”</b><br>\r\n            💰 Đơn giá: 6.500.000₫<br>\r\n            🧮 Tổng: <b>6.500.000₫</b><br>\r\n            🧾 Mã đơn: #30\r\n            ', 1, 7, '2025-11-16 20:40:00', NULL, 'chua_xem', '2025-11-16 06:40:00'),
(47, 'Bạn có đơn hàng mới #31', 1, 3, '2025-11-17 07:32:43', NULL, 'da_xem', '2025-11-16 17:32:43'),
(48, '\r\n            📦 <b>NguyenVan</b> vừa đặt <b>2</b> sản phẩm <b>“iphone 17”</b><br>\r\n            💰 Đơn giá: 50.000.000₫<br>\r\n            🧮 Tổng: <b>100.000.000₫</b><br>\r\n            🧾 Mã đơn: #31\r\n            ', 1, 3, '2025-11-17 07:32:43', NULL, 'da_xem', '2025-11-16 17:32:43'),
(49, 'Bạn có đơn hàng mới #32', 11, 7, '2026-01-15 18:17:06', NULL, 'chua_xem', '2026-01-15 04:17:06'),
(50, '\r\n            📦 <b>Phan Đặng Đức Nguyên</b> vừa đặt <b>2</b> sản phẩm <b>“Samsung Galaxy S25 FE”</b><br>\r\n            💰 Đơn giá: 15.000.000₫<br>\r\n            🧮 Tổng: <b>30.000.000₫</b><br>\r\n            🧾 Mã đơn: #32\r\n            ', 11, 7, '2026-01-15 18:17:06', NULL, 'chua_xem', '2026-01-15 04:17:06'),
(51, 'Bạn có đơn hàng mới #33', 11, 14, '2026-01-18 11:41:45', NULL, 'chua_xem', '2026-01-17 21:41:45'),
(52, '\r\n            📦 <b>Phan Đặng Đức Nguyên</b> vừa đặt <b>1</b> sản phẩm <b>“BMW S1000RR 2018, XE ĐẸP ODO 22K”</b><br>\r\n            💰 Đơn giá: 499.000.000₫<br>\r\n            🧮 Tổng: <b>499.000.000₫</b><br>\r\n            🧾 Mã đơn: #33\r\n            ', 11, 14, '2026-01-18 11:41:45', NULL, 'chua_xem', '2026-01-17 21:41:45'),
(53, 'Bạn có đơn hàng mới #33', 11, 14, '2026-01-18 11:41:45', NULL, 'chua_xem', '2026-01-17 21:41:45'),
(54, '\r\n            📦 <b>Phan Đặng Đức Nguyên</b> vừa đặt <b>1</b> sản phẩm <b>“Mercedes G63 AMG 2022”</b><br>\r\n            💰 Đơn giá: 9.900.000.000₫<br>\r\n            🧮 Tổng: <b>9.900.000.000₫</b><br>\r\n            🧾 Mã đơn: #33\r\n            ', 11, 14, '2026-01-18 11:41:45', NULL, 'chua_xem', '2026-01-17 21:41:45'),
(55, 'Bạn có đơn hàng mới #33', 11, 12, '2026-01-18 11:41:45', NULL, 'chua_xem', '2026-01-17 21:41:45'),
(56, '\r\n            📦 <b>Phan Đặng Đức Nguyên</b> vừa đặt <b>1</b> sản phẩm <b>“Đồng hồ Rado Golden Horse Nâu”</b><br>\r\n            💰 Đơn giá: 3.200.000₫<br>\r\n            🧮 Tổng: <b>3.200.000₫</b><br>\r\n            🧾 Mã đơn: #33\r\n            ', 11, 12, '2026-01-18 11:41:45', NULL, 'chua_xem', '2026-01-17 21:41:45'),
(57, 'Bạn có đơn hàng mới #34', 11, 14, '2026-01-18 12:31:01', NULL, 'chua_xem', '2026-01-17 22:31:01'),
(58, '\r\n            📦 <b>Phan Đặng Đức Nguyên</b> vừa đặt <b>1</b> sản phẩm <b>“BMW S1000RR 2018, XE ĐẸP ODO 22K”</b><br>\r\n            💰 Đơn giá: 499.000.000₫<br>\r\n            🧮 Tổng: <b>499.000.000₫</b><br>\r\n            🧾 Mã đơn: #34\r\n            ', 11, 14, '2026-01-18 12:31:01', NULL, 'chua_xem', '2026-01-17 22:31:01'),
(59, 'Bạn có đơn hàng mới #35', 11, 14, '2026-01-18 12:31:19', NULL, 'chua_xem', '2026-01-17 22:31:19'),
(60, '\r\n            📦 <b>Phan Đặng Đức Nguyên</b> vừa đặt <b>1</b> sản phẩm <b>“BMW S1000RR 2018, XE ĐẸP ODO 22K”</b><br>\r\n            💰 Đơn giá: 499.000.000₫<br>\r\n            🧮 Tổng: <b>499.000.000₫</b><br>\r\n            🧾 Mã đơn: #35\r\n            ', 11, 14, '2026-01-18 12:31:19', NULL, 'chua_xem', '2026-01-17 22:31:19'),
(61, 'Bạn có đơn hàng mới #36', 14, 14, '2026-01-18 12:32:49', NULL, 'chua_xem', '2026-01-17 22:32:49'),
(62, '\r\n            📦 <b>Quang Thuận</b> vừa đặt <b>2</b> sản phẩm <b>“BMW S1000RR 2018, XE ĐẸP ODO 22K”</b><br>\r\n            💰 Đơn giá: 499.000.000₫<br>\r\n            🧮 Tổng: <b>998.000.000₫</b><br>\r\n            🧾 Mã đơn: #36\r\n            ', 14, 14, '2026-01-18 12:32:49', NULL, 'chua_xem', '2026-01-17 22:32:49'),
(63, 'Bạn có đơn hàng mới #37', 14, 14, '2026-01-18 12:38:18', NULL, 'chua_xem', '2026-01-17 22:38:18'),
(64, '\r\n            📦 <b>Quang Thuận</b> vừa đặt <b>1</b> sản phẩm <b>“BMW S1000RR 2018, XE ĐẸP ODO 22K”</b><br>\r\n            💰 Đơn giá: 499.000.000₫<br>\r\n            🧮 Tổng: <b>499.000.000₫</b><br>\r\n            🧾 Mã đơn: #37\r\n            ', 14, 14, '2026-01-18 12:38:18', NULL, 'chua_xem', '2026-01-17 22:38:18'),
(65, 'Bạn có đơn hàng mới #41', 16, 13, '2026-01-23 21:19:27', NULL, 'chua_xem', '2026-01-23 07:19:27'),
(66, '\r\n            📦 <b>test1@22</b> vừa đặt <b>2</b> sản phẩm <b>“Xe đạp địa hình Bianchi Magma 29.2”</b><br>\r\n            💰 Đơn giá: 20.000.000₫<br>\r\n            🧮 Tổng: <b>40.000.000₫</b><br>\r\n            🧾 Mã đơn: #41\r\n            ', 16, 13, '2026-01-23 21:19:27', NULL, 'chua_xem', '2026-01-23 07:19:27'),
(67, 'Bạn có đơn hàng mới #41', 16, 14, '2026-01-23 21:19:27', NULL, 'chua_xem', '2026-01-23 07:19:27'),
(68, '\r\n            📦 <b>test1@22</b> vừa đặt <b>1</b> sản phẩm <b>“Kawasaki Zx25R 2022 abs.”</b><br>\r\n            💰 Đơn giá: 85.000.000₫<br>\r\n            🧮 Tổng: <b>85.000.000₫</b><br>\r\n            🧾 Mã đơn: #41\r\n            ', 16, 14, '2026-01-23 21:19:27', NULL, 'chua_xem', '2026-01-23 07:19:27'),
(69, 'Bạn có đơn hàng mới #41', 16, 14, '2026-01-23 21:19:27', NULL, 'chua_xem', '2026-01-23 07:19:27'),
(70, '\r\n            📦 <b>test1@22</b> vừa đặt <b>1</b> sản phẩm <b>“BMW S1000RR 2018, XE ĐẸP ODO 22K”</b><br>\r\n            💰 Đơn giá: 499.000.000₫<br>\r\n            🧮 Tổng: <b>499.000.000₫</b><br>\r\n            🧾 Mã đơn: #41\r\n            ', 16, 14, '2026-01-23 21:19:27', NULL, 'chua_xem', '2026-01-23 07:19:27'),
(71, 'Bạn có đơn hàng mới #41', 16, 14, '2026-01-23 21:19:27', NULL, 'chua_xem', '2026-01-23 07:19:27'),
(72, '\r\n            📦 <b>test1@22</b> vừa đặt <b>1</b> sản phẩm <b>“Mercedes G63 AMG 2022”</b><br>\r\n            💰 Đơn giá: 9.900.000.000₫<br>\r\n            🧮 Tổng: <b>9.900.000.000₫</b><br>\r\n            🧾 Mã đơn: #41\r\n            ', 16, 14, '2026-01-23 21:19:27', NULL, 'chua_xem', '2026-01-23 07:19:27'),
(73, 'Bạn có đơn hàng mới #42', 2, 14, '2026-01-23 21:29:48', NULL, 'chua_xem', '2026-01-23 07:29:48'),
(74, '\r\n            📦 <b>Nguyen</b> vừa đặt <b>1</b> sản phẩm <b>“Nissan 370Z Nismo”</b><br>\r\n            💰 Đơn giá: 3.999.000.000₫<br>\r\n            🧮 Tổng: <b>3.999.000.000₫</b><br>\r\n            🧾 Mã đơn: #42\r\n            ', 2, 14, '2026-01-23 21:29:48', NULL, 'chua_xem', '2026-01-23 07:29:48');

-- --------------------------------------------------------

--
-- Table structure for table `sanpham`
--

DROP TABLE IF EXISTS `sanpham`;
CREATE TABLE IF NOT EXISTS `sanpham` (
  `maSanPham` int NOT NULL AUTO_INCREMENT,
  `tenSanPham` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `moTa` text COLLATE utf8mb4_unicode_ci,
  `gia` decimal(15,2) DEFAULT '0.00',
  `soLuong` int DEFAULT '0',
  `tinhTrang` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hinhAnh` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `maNguoiBan` int DEFAULT NULL,
  `hinhAnh1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hinhAnh2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hinhAnh3` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hinhanh4` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hinhanh5` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hinhanh6` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `maDanhMuc` int DEFAULT NULL,
  `trangThai` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 = hiện, 0 = ẩn',
  `duyetTrangThai` tinyint(1) DEFAULT '0',
  `giamGia` int DEFAULT '0',
  `thamGiaSuKien` tinyint(1) DEFAULT '1',
  `giaGoc` decimal(15,2) DEFAULT NULL,
  PRIMARY KEY (`maSanPham`),
  KEY `fk_sanpham_nguoiBan` (`maNguoiBan`),
  KEY `fk_sanpham_danhmuc` (`maDanhMuc`)
) ENGINE=InnoDB AUTO_INCREMENT=109 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sanpham`
--

INSERT INTO `sanpham` (`maSanPham`, `tenSanPham`, `moTa`, `gia`, `soLuong`, `tinhTrang`, `video`, `hinhAnh`, `maNguoiBan`, `hinhAnh1`, `hinhAnh2`, `hinhAnh3`, `hinhanh4`, `hinhanh5`, `hinhanh6`, `maDanhMuc`, `trangThai`, `duyetTrangThai`, `giamGia`, `thamGiaSuKien`, `giaGoc`) VALUES
(1, 'Airpods Max Apple', 'Airport', 12900000.00, 1, 'Còn hàng', NULL, '1761191772_Airpods_Max_Apple.png', 2, '1761652958_1_23_1.jpg', '1761652958_2_23_2.jpg', '1761652958_3_23_3.jpg', NULL, NULL, NULL, 4, 1, 1, 0, 0, NULL),
(2, 'Cặp củ loa dài rộng 18CM', 'Loa rộng 18cm cũ', 245000.00, 3, 'Còn hàng', NULL, '1761191817_C___p_c____loa_d__i_r___ng_18CM.png', 2, '1761654123_1_21_1.jpg', '1761654123_2_21_2.jpg', '1761654123_3_21_3.jpg', '0', NULL, NULL, 4, 1, 1, 0, 0, NULL),
(4, 'Dàn karaoke MK Acoustic', 'Dàn karaoke MK Acoustic nghe ổn', 26000000.00, 3, 'Còn hàng', NULL, '1761191894_D__n_karaoke_MK_Acoustic.png', 2, '1761653947_1_19_1.jpg', '1761653947_2_19_2.jpg', '1761653947_3_19_3.jpg', '0', NULL, NULL, 3, 1, 1, 0, 0, NULL),
(5, 'Dell Latitude 7400', 'Máy dùng 98%', 12000000.00, 4, 'Còn hàng', NULL, '1761191919_Dell_Latitude_7400.png', 2, '1761653922_1_18_1.jpg', '1761653922_2_18_2.jpg', '1761653922_3_18_3.jpg', '0', NULL, NULL, 5, 1, 1, 0, 0, NULL),
(6, 'Imac 2015 i5', 'Dùng 99% không lỗi', 12500000.00, 3, 'Còn hàng', NULL, '1761191961_Imac_2015_i5.png', 2, '1761653894_1_17_1.jpg', '1761653894_2_17_2.jpg', '1761653894_3_17_3.jpg', '0', NULL, NULL, 6, 1, 1, 0, 0, NULL),
(7, 'Ipad pro M1', 'Máy mua được 2 năm ai cần nhắn em nha', 7000000.00, 3, 'Còn hàng', NULL, '1761192015_Ipad_pro_M1.png', 2, '1761653863_1_16_1.jpg', '1761653863_2_16_2.jpg', NULL, '0', NULL, NULL, 6, 1, 1, 0, 0, NULL),
(9, 'Laptop Gaming Acer Intro', 'Lap sài được 5 năm nhưng giờ không có nhu cầu dùng nữa ai cần inbox em ạ!', 8000000.00, 2, 'Còn hàng', NULL, '1761192113_Laptop_Gaming_Acer_Intro.png', 2, '1761653748_1_12_1.jpg', '1761653748_2_12_2.jpg', NULL, '0', NULL, NULL, 5, 1, 1, 0, 0, NULL),
(10, 'Macbook pro 16', 'Máy zin chính hãng', 9050000.00, 3, 'Còn hàng', NULL, '1761192170_Macbook_pro_16.png', 2, '1761653714_1_18_1.jpg', '1761653714_2_18_2.jpg', '1761653714_3_18_3.jpg', '0', NULL, NULL, 5, 1, 1, 0, 0, NULL),
(11, 'Máy ảnh Canon FTb', 'Máy ảnh sắc nét mình không dùng nữa', 4000000.00, 3, 'Còn hàng', NULL, '1761192225_M__y____nh_Canon_FTb.png', 3, '1761654396_1_10_1.jpg', '1761654396_2_10_2.jpg', '1761654396_3_10_3.jpg', '0', NULL, NULL, 6, 1, 1, 0, 1, NULL),
(12, 'Nubia Red Magic 9 Pro', 'chiến game siêu mượt', 12000000.00, 2, 'Còn hàng', NULL, '1761192265_Nubia_Red_Magic_9_Pro.png', 3, '1761654372_1_9_1.jpg', '1761654372_2_9_2.jpg', '1761654372_3_9_3.jpg', '0', NULL, NULL, 1, 1, 1, 70, 1, NULL),
(13, 'Ống kính 7Artisans 12mm', 'Ống kính mới không xước len', 700000.00, 2, 'Còn hàng', NULL, '1761192297____ng_k__nh_7Artisans_12mm.png', 3, '1761654353_1_8_1.jpg', '1761654353_2_8_2.jpg', '1761654353_3_8_3.jpg', '0', NULL, NULL, 4, 1, 1, 0, 1, NULL),
(15, 'Phụ kiện zin apple samsung', 'Phụ kiện apple samsung các loại cần nhắn em', 840000.00, 6, 'Còn hàng', NULL, '1761192366_Ph____ki___n_zin_apple_samsung.png', 3, '1761654312_1_6_1.jpg', '1761654312_2_6_2.jpg', '1761654312_3_6_3.jpg', '0', NULL, NULL, 4, 1, 1, 0, 1, NULL),
(16, 'Vivo X200 5G 12/256gb camera đẹp\r\n', 'Về lại em vivo giá tốt cho ae đam mê chụp ảnh\r\nVivo X200 5g 12/256gb 98\r\nMáy kèm ốp \r\nChip dimen 9400 siêu mạnh mẽ\r\nPin silicon 5800mah trâu ,Sạc nhanh 90w\r\nCamera zeiss AI chất lượng cực tốt', 20000000.00, 5, 'Còn hàng', NULL, 'vivo4.0.jpg', 3, 'vivo4.1.jpg', 'vivo4.2.jpg', 'vivo4.3.jpg', '0', NULL, NULL, 1, 1, 1, 0, 1, NULL),
(17, 'Samsung tab a7 lite', 'Máy cũ dùng được', 7300000.00, 4, 'Còn hàng', NULL, '1761192436_Samsung_tab_a7_lite.png', 3, '1761654250_1_4_1.jpg', '1761654250_2_4_2.jpg', '1761654250_3_4_3.jpg', '0', NULL, NULL, 6, 1, 1, 0, 1, NULL),
(18, 'Ssd 512GB', 'ổ ssd 512GB chính hãng', 500000.00, 2, 'Còn hàng', NULL, '1761192472_Ssd_512GB.png', 3, '1761654231_1_3_1.jpg', '1761654231_2_3_2.jpg', '1761654231_3_3_3.jpg', '0', NULL, NULL, 4, 1, 1, 0, 1, NULL),
(19, 'Tản corsiar H150i Pro', 'Tản nhiệt máy tính mới', 1000000.00, 3, 'Còn hàng', NULL, '1761192494_T___n_corsiar_H150i_Pro.png', 3, '1761654212_1_2_1.jpg', '1761654212_2_2_2.jpg', NULL, '0', NULL, NULL, 4, 1, 1, 0, 1, NULL),
(20, 'thinkpad x1 gen 8', 'Máy cũ như mới', 1900000.00, 0, 'Hết hàng', NULL, '1761192519_thinkpad_x1_gen_8.png', 3, '1761654190_1_1_1.jpg', '1761654190_2_1_2.jpg', '1761654190_3_1_3.jpg', '0', NULL, NULL, 6, 1, 1, 0, 1, NULL),
(26, 'Bếp 4 họng', 'Bếp 4 họng lửa sẳn hàng\r\nGiao lắp kv cần thơ', 123000.00, 1, 'Còn hàng', NULL, '1761991761_B___p_4_h___ng.png', 3, NULL, NULL, NULL, '0', NULL, NULL, 2, 1, 1, 30, 1, NULL),
(27, 'Máy lạnh Pannasonic 1,5HP Inverter mới 85%', 'Máy lạnh Pannasonic 1,5HP Inverter mới 85% máy hoạt động êm tiết kiệm điện ', 7000000.00, 20, 'Còn hàng', NULL, 'mlanh3.1.jpg', 3, 'mlanh3.0.jpg', NULL, NULL, '0', NULL, NULL, 2, 1, 1, 0, 1, NULL),
(28, 'Máy tính để bàn Cooler Master Đen', 'CPU Cooler Master màu đen, hiệu năng ổn định, phù hợp cho máy tính để bàn.\r\n- Thiết kế tản nhiệt tốt, giúp máy hoạt động mát mẻ.\r\n- Dễ dàng lắp đặt, tương thích nhiều loại mainboard.\r\n- Giá tốt.', 18000000.00, 3, 'Còn hàng', NULL, 'thungpc.jpg', 3, 'thungpc.jpg', NULL, NULL, '0', NULL, NULL, 5, 1, 1, 0, 1, NULL),
(29, 'Galaxy S24 Ultra 256Gb Chính Hãng Vn Zin Nét', 'S24 Ultra 256Gb\r\nBản Chính hãng VN\r\nRam 12/512Gb\r\n\r\n+ 2 sim, có ghi âm cuộc gọi mặc định hãng\r\n+ Đẹp 99%\r\n+ Zin nguyên áp…', 1990000.00, 70, 'Còn hàng', NULL, '253.0.jpg', 2, '253.1.jpg', '253.2.jpg', '253.3.jpg', NULL, NULL, NULL, 1, 1, 1, 0, 0, NULL),
(30, 'Máy chạy bộ Rovera', 'Máy chạy bộ Rovera đã qua sử dụng, màu xám đen. \r\n - Thiết kế gấp gọn tiện lợi, tiết kiệm không gian. \r\n - Phù hợp tập luyện tại nhà, cải thiện sức khỏe.\r\nGia đình chuyển nhà cần thang lí', 2800000.00, 8, 'Còn hàng', NULL, 'chay1.0.jpg', 3, 'chay1.3.jpg', 'chay1.2.jpg', 'chay1.4.jpg', '0', NULL, NULL, 4, 1, 1, 20, 1, NULL),
(31, 'Honor X50 128GB ', 'Thanh lý máy cũ honor zin như hư camera', 2000000.00, 3, 'Còn hàng', NULL, 'honor3.0.jpg', 7, 'honor1.0.jpg', 'honor1.1.jpg', NULL, '0', NULL, NULL, 1, 1, 1, 0, 1, NULL),
(32, 'Vợt pickleball franklin c45 paristodd ', 'Mình cần bán vợt franklin c45 của paris todd màu cực đẹp.vợt mới mua đánh đc vài trận còn rất mới. Vợt giành cho ace nào thích tấn công đứng lưới tay nhanh.bản 13.25', 2500000.00, 4, 'Còn hàng', NULL, 'vot2.0.jpg', 7, 'vot2.1.jpg', 'vot2.2.jpg', NULL, '0', NULL, NULL, 4, 1, 1, 0, 1, NULL),
(33, 'Đàn guitar gỗ màu nâu', 'Đàn guitar gỗ màu nâu, âm thanh ấm áp, phù hợp cho người mới tập chơi hoặc biểu diễn. \r\n - Chất liệu gỗ bền đẹp. \r\n - Thiết kế tinh tế, dễ cầm. \r\n - Âm thanh vang, rõ.', 12000000.00, 200, 'Còn hàng', NULL, 'dan1.0.jpg', 7, 'dan1.1.jpg', 'dan1.2.jpg', 'dan1.4.jpg', '0', NULL, NULL, 4, 1, 1, 0, 1, NULL),
(34, 'Macbook Pro Rentina + Tuochbar.', 'Bán Macbook Pro Rentina 13\"Tuoch Bar MNQF2, vỏ nhôm nguyên khối, thiết kế siêu mỏng gọn nhẹ 1.37kg, Hình thức máy đẹp, nguyên bản, mọi chức năng hoạt động tốt, Cấu hình cao Core I5, Ram 8Gb, Ổ SSD 256Gb, màn 13.3 Rentina 2K+ siêu nét phím có Tuoch Bar, Vân tay + đèn Led, máy chạy siêu nhanh giải trí và đồ hoạ  \r\nCấu hình máy:  \r\n- Core I5-2.9Ghz upto 3.9Ghz\r\n- Ram 8Gb DDRAM 4\r\n- ổ cứng SSD 256Gb chạy siêu nhanh mượt + tha hồ lưu trữ\r\n- Vga Intel Iris \r\n- Bàn phím có Tuoch Bar, Vân tay + đèn Led phím rất thuận tiện làm việc về đêm  \r\n- Màn 13.3\" Rentina 2560x1600 siêu nét phân giải 2K+  \r\n-Cổng kết nối Type C\r\n- pin tốt Tầm 3 đến 5h ', 79000000.00, 48, 'Còn hàng', NULL, 'book1.0.jpg', 2, 'book1.3.jpg', 'book1.2.jpg', 'book1.1.jpg', '0', NULL, NULL, 5, 1, 1, 0, 0, NULL),
(35, 'Laptop HP5', 'HP 430g6 i5/8th/Ram 8/SSD 256/13.3\"HD\r\nVỏ nhôm sáng đẹp, mỏng nhẹ thời trang\r\nPin ok\r\nMáy chạy ngon full chức năng', 5000000.00, 15, 'Còn hàng', NULL, 'lap5.0.jpg', 7, 'lap5.1.jpg', 'lap5.2.jpg', NULL, '0', NULL, NULL, 5, 1, 1, 0, 1, NULL),
(36, 'Watch Ultra 2024 LTE chính hãng', 'Chính hãng ssvn\r\nFullbox đẹp 99%,nguyên zin\r\nmọi chức năng hoạt động hoàn hảo\r\nLTE ghép esim\r\nPin tốt 3 ngày\r\nSẵn 2 màu:trắng,cam', 7000000.00, 50, 'Còn hàng', NULL, 'dongho2.0.jpg', 2, 'dongho2.1.jpg', 'dongho2.2.jpg', 'dongho2.3.jpg', '0', NULL, NULL, 7, 1, 1, 0, 0, NULL),
(37, 'Máy lọc không khí IRIS OHYAMA RHF-404-W', 'Máy lọc không khí bù ẩm có kèm sưởi ấm IRIS OHYAMA ớRHF-404-W màu trắng, sản xuất năm 2020.đẹp xuất sắc \r\n - Thiết kế nhỏ gọn, phù hợp nhiều không gian.\r\n - Công suất 360/372W, hoạt động hiệu quả.ion diệt khuẩn giúp \r\n  không khí trong lành, bảo vệ sức khỏe.', 5000000.00, 19, 'Còn hàng', NULL, 'mayloc1.0.jpg', 7, 'mayloc1.4.jpg', 'mayloc1.3.jpg', 'mayloc1.1.jpg', '0', NULL, NULL, 2, 1, 1, 0, 1, NULL),
(38, 'Bếp từ\r\n', 'Model mới nhất bếp nhập nguyên chiếc  Thái Lan Canzy Cz PUJ588PLUS serial 8. Model Nâng cấp Sx năm 2024 . \r\nBo viền có sôi liu riu đều ko bùng chàn ngắt quãng khi nấu.  \r\nMặt kính chống xước , mâm mạch nâng cấp hiện đại ', 12000000.00, 30, 'Còn hàng', NULL, 'bep3.0.jpg', 7, 'bep3.1.jpg', 'bep3.2.jpg', 'bep3.3.jpg', '0', NULL, NULL, 2, 1, 1, 0, 1, NULL),
(39, 'Lò vi sóng Electrolux 23l có nướng', 'hình thức đẹp . hoạt động tốt mọi chức năng \r\nBảo hành 2 tháng\r\n\r\n', 550000.00, 39, 'Còn hàng', NULL, 'lo2.0.jpg', 7, 'lo2.2.jpg', 'lo2.1.jpg', NULL, '0', NULL, NULL, 2, 1, 1, 0, 1, NULL),
(40, 'Iphone 13 ProMax 128gb vàng.\r\n', 'Mình đổi máy cần bán lại cây 13 Pro Max 128gb màu Gold Sa Mạc đang dùng cực tốt. Phiên bản quốc tế chính hãng Apple. Còn bảo hành gần 6 tháng hoàn toàn yên tâm. Máy mình mua đập hộp tới giờ chưa sửa chữa. Tình trạng không một lỗi nhỏ nào, máy xài kỹ đã dán cường lực 2 mặt xài ốp lưng nên còn đẹp 99% Pin mình xài 2 ngày, phụ kiện zin theo máy còn đầy đủ không thiếu gì sạc cáp tai nghe hộp trùng imei hoá đơn.', 12000000.00, 10, 'Còn hàng', NULL, 'ip6.0.jpg', 7, 'ip5.1.jpg', 'ip5.3.jpg', 'ip5.2.jpg', '0', NULL, NULL, 1, 1, 1, 0, 1, NULL),
(41, 'MOTO GPX 150cc - FULL KIỂNG.', 'Cần thanh lý xe MOTO GPX DEMON 150GR FULL KIỂNG\r\n💥- Đăng ký 2018 - Chất xe còn rất mới \r\n❤️‍🔥ĐỒ CHƠI FULL XE NHƯ HÌNH NHA ANH EM\r\n                XE ĐẸP - SỬ DỤNG NGAY\r\n✅Máy móc zin êm . Ko một tiếng động lạ \r\n💁‍♂️- Xe chạy tốt  tiết kiệm nhiên liệu\r\n💁‍♂️-Dàn chân cứng cáp ,sạch sẽ rất đẹp ,lốp dày cui mới\r\n💁‍♂️-Dàn áo còn rất đẹp leng keng \r\n💁‍♂️-Giây tờ đầy đủ.Giao cavet và hoá đơn cửa hàng bán ra\r\n🤝-Hỗ trợ rút gốc sang tên ', 30000000.00, 60, 'Còn hàng', NULL, 'xemoi3.0.jpg', 7, 'xemoi3.3.jpg', 'xemoi3.2.jpg', 'xemoi3.1.jpg', '0', NULL, NULL, 4, 1, 1, 0, 1, NULL),
(42, 'tai nghe i37', '🎧 Tai Nghe i37 - Bass Cực Lớn, Giá Cực Sốc!\r\nTên sản phẩm: Tai nghe i37 (Có vẻ là tai nghe over-ear cao cấp tương tự AirPods Max)\r\n\r\nGiá bán: 300.000 VND (Mức giá không tưởng!)\r\n\r\nĐiểm nhấn: \"nghe tốt bass lớn\" – Tận hưởng âm nhạc sống động với âm trầm (bass) mạnh mẽ, bùng nổ. Thiết kế sang trọng, đệm tai êm ái, đeo lâu không đau.\r\n\r\nTình trạng: Còn hàng | Số lượng còn: 19 chiếc.\r\n\r\nMục đích: Giải trí đỉnh cao, nghe nhạc, xem phim, chơi game.', 300000.00, 19, 'Còn hàng', NULL, '1762138808_23_1.jpg', 7, '', '', NULL, '0', NULL, NULL, 4, 1, 1, 0, 1, NULL),
(43, 'Z1000 xe chính chủ, xe zin êm.', 'Z1000 date 2012 xe chính chủ, máy móc zin êm. Có giao lưu trao đổi\r\n\"AN TÂM KHI MUA XE TẠI CHIẾN MOTOR\r\n✅ Bảo hàng 06 tháng + giấy tờ đầy đủ\r\n✅ Công chứng ủy quyền hoặc sang tên toàn quốc\r\n✅ Máy móc zin, xe ngoại hình đẹp\r\n✅ Bán trả góp - trả trước xx', 99900000.00, 1, 'Còn hàng', NULL, 'xe3.0.jpg', 7, 'xe3.1.jpg', 'xe3.4.jpg', 'xe3.2.jpg', '0', NULL, NULL, 4, 1, 1, 0, 1, NULL),
(44, 'Máy ảnh Canon 450D Đen Đã sử dụng', 'Bộ Canon 450D còn đẹp, sài ngon, đầy đủ phụ kiện.', 3500000.00, 1, 'Còn hàng', NULL, 'mayanh2.0.jpg', 2, 'mayanh2.1.jpg', 'mayanh2.3.jpg', 'mayanh2,4.jpg', '0', NULL, NULL, 4, 1, 1, 0, 0, NULL),
(45, 'Apple iPad Pro M1 11 inch 128GB.', 'IPad Pro M1 11inh 128gb wifi hàng chính hãng VN mua Minh Tuấn Mobile\r\nNgoại hình máy còn đẹp 98%\r\nPin 89%\r\nMàn hình ko trầy sướt dán cường lực từ đầu, màn hình bị đè có 1 đường pixel như hình mình có chụp, ko loang hay lỗi gì\r\nMáy nguyên zin chưa sửa chữa, khuyến khích dẫn thợ thầy check test', 99000000.00, 3, 'Còn hàng', NULL, 'apple2.0.jpg', 7, 'apple2.1.jpg', 'apple2.2.jpg', 'apple2.3.jpg', '0', NULL, NULL, 1, 1, 1, 0, 1, NULL),
(46, 'Xe đạp đua', 'Full chính hãng sườn cacbon đời cao dây âm sườn size s group điện 2x12 bánh roval chính hãng cl50 vỏ ruột rời ', 3200000.00, 5, 'Còn hàng', NULL, 'xedap1.0.jpg', 7, 'xedap1.1.jpg', 'xedap1.3.jpg', 'xedap1.2.jpg', '0', NULL, NULL, 4, 1, 1, 0, 1, NULL),
(47, 'Quái Thú Pc AK56', 'Chào mừng các game thủ và tín đồ công nghệ! Đây chính là Quái Thú Pc AK56 - chiếc PC mạnh nhất thế giới (PC mạnh nhất TG) mà bạn hằng mơ ước!\r\nGiá cực sốc: 1.000.000.000 VND (Một tỷ đồng chẵn). Đầu tư một lần, trải nghiệm đỉnh cao vĩnh viễn!\r\nTình trạng: Còn hàng – Cơ hội sở hữu có hạn!\r\nĐiểm nhấn: Không chỉ là một cỗ máy, đây là một tác phẩm nghệ thuật với hệ thống ánh sáng RGB rực rỡ, bàn phím cơ và màn hình kép siêu rộng, mang lại trải nghiệm chơi game và làm việc không đối thủ. Tận hưởng mọi tựa game AAA ở mức cài đặt cao nhất mà không hề giật lag!', 1000000000.00, 2, 'Còn hàng', NULL, '1762140959_Hinh-anh-dan-PC-khung.jpg', 7, NULL, NULL, NULL, '0', NULL, NULL, 6, 1, 1, 0, 1, NULL),
(48, 'Máy Tính Văn phòng v500', 'Mô tả nổi bật:\r\n\r\nMục đích sử dụng: Tuy là máy văn phòng nhưng có khả năng chiến game nét nên nét (ám chỉ khả năng chơi game tốt).\r\n\r\nĐặc điểm: Gọn nhẹ nhàng, cấu lưu trữ cá nhân.\r\n\r\nLưu trữ: Sử dụng NVMe PCIe 4.0 256 GB.\r\n\r\nThiết kế: Gọn, hiện đại.\r\n\r\nCổng kết nối: Có hỗ trợ HDMI 1.4, ...', 20000000.00, 20, 'Còn hàng', NULL, '1762141140_vi-vn-asus-v500mv-i3-1315u-8gb-256gb-slide-1.jpg', 7, '', NULL, NULL, '0', NULL, NULL, 6, 1, 1, 0, 1, NULL),
(49, 'VinFast VF8 2025.', 'BÁN VINFAST VF8 PLUS - XE ĐẸP NHƯ MỚI\r\n\r\nOdo: 15.000 km (Xe đi rất ít).\r\n\r\nPin: Gói thuê pin cố định (không giới hạn km), tiết kiệm chi phí.\r\n\r\nTình trạng: Xe nguyên bản, sơn zin 100%. Đã cập nhật phần mềm mới nhất cực mượt, không lỗi vặt.\r\n\r\nBảo hành: Còn bảo hành chính hãng đến 10 năm (yên tâm sử dụng).', 850000000.00, 100, 'Còn hàng', NULL, 'vf82.0.jpg', 7, 'vf82.3.jpg', 'vf82.1.jpg', 'vf82.4.jpg', '0', NULL, NULL, 4, 1, 1, 0, 1, NULL),
(50, 'Mec c200', 'CẦN BÁN MERCEDES C200 - XE ĐẸP, GIÁ CỰC TỐT\r\n\r\nĐời xe: 2020.\r\n\r\nTình trạng: Xe gia đình đi kỹ, bảo dưỡng full lịch trình, nội ngoại thất còn rất mới.\r\n\r\nVận hành: Máy êm, lái cực bốc, cách âm hoàn hảo đúng chất Mer.\r\n\r\nCam kết: Không đâm đụng, không ngập nước, bao check hãng toàn quốc.', 1400000000.00, 1, 'Còn hàng', NULL, 'mec2.0.jpg', 7, 'mec2.2.jpg', 'mec2.1.jpg', NULL, '0', NULL, NULL, 4, 1, 1, 0, 1, NULL),
(51, 'Thanh lý máy lạnh LG 2HP', 'Tình trạng: Máy cũ nhưng làm lạnh siêu nhanh, thổi gió cực mạnh.\r\n\r\nCông suất: 2HP (phù hợp phòng khách hoặc phòng lớn 20-30m²).\r\n\r\nƯu điểm: Máy LG chạy bền, dễ sử dụng, bao test tại chỗ.', 4000000.00, 4, 'Còn hàng', NULL, 'maylanh3.0.jpg', 7, 'maylanh3.1.jpg', 'maylanh3.2.jpg', 'maylanh3.3.jpg', '0', NULL, NULL, 2, 1, 1, 0, 1, NULL),
(52, 'Tủ lạnh Toshiba Inverter 311 lít', 'Tủ lạnh Toshiba Inverter 311 lít\r\n- Siêu thị bán : 13.090.000\r\n- Em bán : 9.000.000\r\n- Hàng mới, full thùng , bảo hành chính hãng\r\n- Hàng có sẵn ', 8000000.00, 29, 'Còn hàng', NULL, 'tulanh4.0.jpg', 7, 'tulanh4.1.jpg', 'tulanh4.3.jpg', NULL, '0', NULL, NULL, 2, 1, 1, 0, 1, NULL),
(53, 'Tủ lạnh AQUA 93 lít Xám', 'tủ lạnh AQUA 93lit zin đẹp tiết kiệm điện có bảo hành miễn phí vận chuyển tủ chạy êm lạnh nhanh', 4000000.00, 3, 'Còn hàng', NULL, 'tulanh3.0.jpg', 7, 'tulanh3.2.jpg', 'tulanh3.1.jpg', 'tulanh3.3.jpg', '0', NULL, NULL, 2, 1, 1, 0, 1, NULL),
(54, 'Tủ lạnh Hitachi Side by Side Đen', 'Tủ lạnh Hitachi Side by Side mặt gương, 3 cánh\r\nCông nghệ Inverter tiết kiệm điện. \r\n - Thiết kế sang trọng, dung tích lớn. \r\n - Giữ thực phẩm tươi ngon lâu hơn. \r\n - Hoạt động êm ái, bền bỉ.', 10000000.00, 39, 'Còn hàng', NULL, 'tulanh1.2.jpg', 7, 'tulanh2.jpg', 'tulanh1.jpg', 'tulanh3.jpg', '0', NULL, NULL, 2, 1, 1, 0, 1, NULL),
(55, 'Máy Giặt HITACHI', 'THANH LÝ HITACHI 8kg - GIÁ RẺ\r\n\r\nMáy cũ theo thời gian nhưng máy móc bao bền, chạy khỏe.\r\n\r\nGiặt sạch, vắt khô, mọi chức năng bình thường.\r\n\r\nPhù hợp cho ai cần máy dùng chống cháy, sinh viên, thợ thuyền.', 25000000.00, 18, 'Còn hàng', NULL, 'maygiat2.1.1.jpg', 7, 'maygiat2.2.jpg', 'maygiat2.3.jpg', NULL, '0', NULL, NULL, 2, 1, 1, 0, 1, NULL),
(56, 'Máy Giặt PANASONIC', 'MÁY GIẶT PANASONIC – GIẶT SẠCH NHƯ TAY MẸ 🧺 Nhà lên đời máy sấy nên dư ra em Panasonic 9kg. Máy dùng cực giữ gìn, lồng giặt lúc nào cũng thơm tho.\r\n\r\nƯu điểm: Chạy êm ru, không rung lắc, siêu tiết kiệm điện nước.\r\n\r\nCông nghệ: Có Econavi và kháng khuẩn Blue Ag+ (đồ trẻ em giặt cực yên tâm).\r\n\r\nTình trạng: Nguyên zin, chưa một lần đụng chạm sửa chữa.', 30000000.00, 19, 'Còn hàng', NULL, '1762142891_panasonic-82-kg-na-f82y01drv1-700x467.jpg', 7, '1762520507_1_panasonic-82-kg-na-f82y01drv2-700x467.jpg', 'maygiat3.jpg', 'maygiat2.jpg', '0', NULL, NULL, 2, 1, 1, 0, 1, NULL),
(57, 'Samsung Galaxy S25 FE', 'S25 FE – CẤU HÌNH S25 NHƯNG GIÁ \"HỌC SINH\" Cần pass em S25 FE đẹp như mới. Máy dành cho ai thích chip mạnh mà ngại bỏ tiền mua bản Ultra.\r\n\r\nSức mạnh: RAM 8GB/256GB, cân mọi loại game không nóng máy.\r\n\r\nCamera: Chụp đêm đỉnh cao, nét từng sợi tóc.\r\n\r\nNgoại hình: Màu xanh cực sang.', 15000000.00, 25, 'Còn hàng', NULL, '1762143162_samsung-galaxy-s25-fe-blue-5-638938966702603121-750x500.jpg', 7, '1762520350_1_samsung-galaxy-s25-fe-blue-4-638938966708861699-750x500.jpg', NULL, 'samsung1.jpg', '0', NULL, NULL, 1, 1, 1, 0, 1, NULL),
(58, 'Samsung Galaxy A17 5G', 'SAMSUNG A17 5G – ĐẸP KENG, GIÁ HẠT DẺ\r\n\r\nMáy đẹp như đập hộp, không một vết xước.\r\n\r\nMàn AMOLED 120Hz lướt mượt như người yêu cũ trở mặt.\r\n\r\nPin trâu bò, 5G chạy xé gió.\r\n\r\nRAM 8GB / ROM 128GB – Đa nhiệm không độ trễ.', 7000000.00, 8, 'Còn hàng', NULL, '1762143269_samsung-galaxy-a17-5g-gray-1-638925131547875229-750x500.jpg', 7, '1762520231_1_samsung-galaxy-a17-5g-gray-4-638925131528503776-750x500.jpg', NULL, NULL, '0', NULL, NULL, 1, 1, 1, 0, 1, NULL),
(59, 'Oppo reno 14 pro 5G', 'Máy chính chủ sử dụng, ngoại hình như mới, mọi chức năng hoàn hảo.\r\n\r\nCấu hình: RAM 12GB / ROM 256GB (Chạy cực mượt, lưu trữ thoải mái).\r\n\r\nCamera: Chụp chân dung AI siêu đẹp, chống rung tốt.\r\n\r\nPin: Sạc siêu nhanh SuperVOOC, pin cực bền.\r\n\r\nTình trạng: Nguyên zin, chưa sửa chữa, màn hình không trầy xước.', 10000000.00, 31, 'Còn hàng', NULL, 'oppo14.jpg', 7, 'oppo141.jpg', 'oppo142.jpg', NULL, '0', NULL, NULL, 1, 1, 1, 0, 1, NULL),
(60, 'VIVO V60 5G', 'Máy như mới, chính chủ dùng kỹ, cực kỳ mượt mà.\r\n\r\nCấu hình: RAM 12GB (+ mở rộng), Bộ nhớ 256GB (tha hồ lưu ảnh/video).\r\n\r\nPin: 6500mAh cực trâu + Sạc nhanh 90W.\r\n\r\nCamera: ZEISS chụp ảnh cực nét.\r\n\r\nTình trạng: Đẹp 99%, zin 100%, không lỗi lầm.', 12000000.00, 20, 'Còn hàng', NULL, '1762143563_7f1aa11dec84a0c3134a834df83fa518.png', 7, 'vivoy60.jpg', 'vivoy601.jpg', NULL, '0', NULL, NULL, 1, 1, 1, 0, 1, NULL),
(61, 'BMW X5 2015', 'Mình cần nhượng lại chiếc BMW X5 đời 2015, xe gia đình sử dụng kỹ, bảo dưỡng định kỳ đúng chuẩn. Dòng này chạy cực đầm, cách âm tuyệt vời và ngoại hình vẫn rất thời thượng.\r\n\r\nTình trạng: Xe nguyên bản, không đâm đụng, không ngập nước (bao check hãng/gara toàn quốc).\r\n\r\nNội thất: Ghế da cao cấp còn mới, hệ thống âm thanh cực hay, điều hòa mát sâu.\r\n\r\nNgoại thất: Màu sơn còn bóng đẹp, đăng kiểm dài hạn.\r\n\r\nVận hành: Động cơ mạnh mẽ, hộp số mượt mà, cảm giác lái đúng chất BMW.', 1000000000.00, 0, 'Hết hàng', NULL, 'bwm1.jpg', 7, 'bwm.jpg', 'bwm1.jpg', NULL, '0', NULL, NULL, 4, 1, 1, 0, 1, NULL),
(73, 'iPhone XR độ 17 Pro quốc tế', 'Loại máy: iPhone XR độ 17 Pro màu cam\r\nMàu: cam\r\nBộ nhớ: 64G\r\nBản : Quốc tế\r\nChức năng: full , face ID tốt\r\nDung lượng pin: 82\r\nVỏ hộp và phụ kiện: không có\r\nTình trạng: máy zin hết chỉ độ vỏ\r\nBảo hành: 1 tuần', 5000000.00, 30, 'Còn hàng', NULL, 'ip172.0.jpg', 12, 'ip172.1.jpg', 'ip172.2.jpg', 'ip172.3.jpg', '0', NULL, NULL, 1, 1, 1, 0, 1, NULL),
(74, 'Máy tính PC văn phòng, học tập, đọc báo đủ bộ.', 'Thanh lý bộ máy tính để bàn Lenovo, đầy đủ trọn bộ.\r\n - Cấu hình: Intel Pentium G3420, RAM 4GB DDR3, SSD 120GB.\r\n - Card đồ họa tích hợp Intel HD Graphics.\r\n - Màn hình độ phân giải 1680 x 1050.\r\n - Phù hợp cho công việc văn phòng, học tập.\r\nĐầy đủ trọn bộ, bao gồm chuột, bàn phím, card wifi, về chỉ cắm điện là dùng \r\nHệ điều hành Windows 10, bao test trong ngày, kể cả thợ.\r\ncam kết chưa sửa chữa.', 2500000.00, 3, 'Còn hàng', NULL, 'pc2.0.jpg', 12, 'pc2.1.jpg', 'pc2.2.jpg', 'pc2.3.jpg', '0', NULL, NULL, 5, 1, 1, 0, 1, NULL),
(76, 'Philips 206V6', 'Em cần bán bộ máy tính như trên 2 màn 1 màn 19inch và 1 màn 22inch i5 card 730 máy dùng ổn định \r\nmàn hình bên phải có bị lỗi em bán nhanh fix mạnh ai nhiệt tình ạ', 2900000.00, 1, '0', NULL, 'mt3.0.jpg', 12, 'mt3.3.jpg', 'mt3.2.jpg', 'mt3.1.jpg', '0', NULL, NULL, 5, 1, 1, 0, 1, NULL),
(77, 'Loa Bluetooth PHICOMM R1 Infinity Harman', '-Mình cần bán loa để bàn Phicomm R1 âm thanh chất lượng, đèn led phát sáng tuỳ chỉnh nháy theo nhạc.\r\n', 400000.00, 50, 'Còn hàng', NULL, 'loa1.0.jpg', 12, 'loa1.2.jpg', 'loa1.1jpg.jpg', NULL, '0', NULL, NULL, 6, 1, 1, 0, 1, NULL),
(78, 'Soundbar Yamaha ysp 2200', 'Loa soundbar Yamaha YSP-2200 màu đen, thiết kế mỏng gọn, chất liệu kim loại bền bỉ.\r\n - Âm thanh vòm sống động, chân thực.\r\n - Kết nối đa dạng, dễ dàng lắp đặt.\r\n - Phù hợp cho không gian giải trí gia đình.', 4900000.00, 99, '0', NULL, 'so1.0.jpg', 12, 'so1.2.jpg', 'so1.3.jpg', NULL, '0', NULL, NULL, 6, 1, 1, 0, 1, NULL),
(79, 'Tai nghe Sony WH-CH520 Trắng', 'Tai nghe Bluetooth chụp tai Sony WH-CH520 hàng chính hãng \r\n- Tai Nghe Sony WH-CH520 với mức giá hạt rẻ \r\n- Kết nối Bluetooth 5.2, tuỳ chỉnh EQ qua app Sony Headphones Connect', 990000.00, 7, 'Còn hàng', NULL, 'tai3.0.jpg', 12, 'tai3.3.jpg', 'tai3.1.jpg', 'tai3.2.jpg', '0', NULL, NULL, 6, 1, 1, 0, 1, NULL),
(80, 'Airpods Pro 2 Type C Fullbox Còn Bảo Hành', 'AirPods Pro 2 TypeC Fullbox BH apple Care+ 31.8.2026\r\n- Airpods pro 2 TypeC siêu đẹp\r\n- Fullbox đầy đủ pk zin theo tai nghe: dây cáp Typec, 3 núm cao su phụ zin\r\n- Tất cả zin và còn đẹp', 2500000.00, 20, 'Còn hàng', NULL, 'nghe2.0.jpg', 12, 'nghe2.3.jpg', 'nghe2.2.jpg', 'nghe2.1.jpg', '0', NULL, NULL, 6, 1, 1, 0, 1, NULL),
(81, 'Máy ảnh Canon 750D Đen', 'Máy ảnh Canon 750D kèm kit ngoại hình máy đẹp.\r\nTình trạng chức năng, phím nút hoạt động hoàn hảo.\r\nMàn hình sáng đẹp có cảm ứng, xoay lật, có wifi .Chụp đẹp, dễ dùng người mới chơi \r\nPhụ kiện máy đi kèm đủ', 8550000.00, 40, 'Còn hàng', NULL, 'anh4.0.jpg', 12, 'anh4.4.jpg', 'anh4.1.jpg', NULL, '0', NULL, NULL, 4, 1, 1, 0, 1, NULL),
(82, 'Card đồ họa Gigabyte RTX 3060 12GB ', 'Gigabyte rtx 3060 12gb còn bảo hành t7/27 vẫn còn mới nhiệt mát ko lỗi lầm gì', 5000000.00, 3, 'Còn hàng', NULL, 'bo1.0.jpg', 12, 'bo1.3.jpg', 'bo1.1.jpg', 'bo1.2.jpg', '0', NULL, NULL, 6, 1, 1, 0, 1, NULL),
(83, 'Máy sấy Electrolux 8,5kg đời mới 98%', 'Em về mẫu máy sấy Electrolux 8,5kg thông hơi đời mới siêu lướt, date 2023 màu đen nhám đẹp long lanh chưa 1 vết sơn xước, zin nguyên bản. \r\nDòng đời mới có cấp nước tạo ẩm, tăng cường chống nhăn tốt hơn, sấy nhanh khô diệt khuẩn, hút bụi sơ vải sạch sẽ.', 7000000.00, 20, 'Còn hàng', NULL, 's1.0.jpg', 12, 's1.1.jpg', 's1.3.jpg', NULL, '0', NULL, NULL, 2, 1, 1, 0, 1, NULL),
(84, 'Đồng hồ Rado Golden Horse Nâu', 'Rado 30 Jewels thuỵ sỹ\r\nSize 35 máy cơ Automatic\r\nZin nguyên củ', 3200000.00, 39, 'Còn hàng', NULL, 'dong2.0.jpg', 12, 'dong2.4.jpg', 'dong2.3.jpg', 'dong2.1.jpg', '0', NULL, NULL, 7, 1, 1, 0, 1, NULL),
(85, 'Đồng hồ Maurice Lacroix Pontos.', 'Maurice Lacroix Pontos PT6178\r\n- Máy caliber ML112,trữ cót 46h\r\n- Kính saphire nguyên khối \r\n- Kim cọc siêu nét \r\n- Kháng nước 50m\r\n', 25000000.00, 10, 'Còn hàng', NULL, 'dh3.0.jpg', 12, 'dh3.1.jpg', 'dh3.3.jpg', 'dh3.4.jpg', '0', NULL, NULL, 7, 1, 1, 0, 1, NULL),
(86, 'Đồng hồ để bàn Jaeger Vàng', 'Như một khối vàng\r\nCao 45cm, ngang 25cm\r\nMáy điện tử của Pháp chạy Pin đại từ những thập niên 80.', 1500000.00, 3, 'Còn hàng', NULL, 'vang1.0.jpg', 13, 'vang1.2.jpg', 'vang1.4.jpg', 'vang1.3.jpg', '0', NULL, NULL, 7, 1, 1, 0, 1, NULL),
(87, 'Panasonic inverter 280l cấp đông.', 'Em về mẫu Panasonic inverter dung tích tổng 280l (255l) đời mới sạch đẹp như mới, khay kệ đầy đủ sạch đẹp, nội thất roăng trắng sáng.', 6990000.00, 99, 'Còn hàng', NULL, 'l2.0.jpg', 13, 'l2.2.jpg', 'l2.1.jpg', NULL, '0', NULL, NULL, 2, 1, 1, 0, 1, NULL),
(88, 'Ford Laser 2003', 'xe em bảo dưỡng định kì đầy đủ, bác nào mua về la chạy luôn thôi ạ.\r\nRiêng đồ chơi ko thiếu gì, Màn Android 4G xịn xò, nghe nhạc đỉnh cao, camera hành trình, camera lùi nét căng cũng  hơn chục triệu, giàn lốp cũng mới 1chục. đủ 2 chìa khóa. Sedan rộng rãi, đi cực thích. Nói chung ngon hế.', 4999000.00, 2, 'Còn hàng', NULL, 'ban3.0.jpg', 13, 'ban3.3.jpg', 'ban3.1.jpg', 'ban3.4.jpg', '0', NULL, NULL, 4, 1, 1, 0, 1, NULL),
(89, 'Volvo XC90 T6 Inscription', 'Đời xe:2021\r\n\r\nOdo: 600000 km\r\n\r\nMàu: Đen / Nội thất [Màu ghế]\r\n\r\nTình trạng: Xe đẹp xuất sắc, sơn zin cực nhiều. Full lịch sử bảo dưỡng hãng.\r\n\r\nOption nổi bật: Loa Bowers & Wilkins, Ghế Massage/Làm mát, Camera 360, Phanh khoảng cách, Treo khí nén cực êm.\r\n\r\n✅ Cam kết không đâm đụng, không ngập nước, máy móc nguyên bản. Bao check hãng toàn quốc.', 999000000.00, 4, 'Còn hàng', NULL, 'xe4.0.jpg', 13, 'xe4.1.jpg', 'xe4.2.jpg', 'xe4.3.jpg', '0', NULL, NULL, 4, 1, 1, 0, 1, NULL),
(90, 'Yamaha R15V3 2019 máy zin biển số 67', 'Yamaha R15V3 2019 biển số 67\r\nGiấy tờ đầy đủ , giao cccd chủ \r\nMáy móc bao zin êm\r\nDàn áo , chânn sạch đẹp\r\nXe lên được vài món đồ chơi kiểng như hình\r\nMọi chức năng hoạt động tốt', 29900000.00, 20, 'Còn hàng', NULL, 'r152.0.jpg', 13, 'r152.1.jpg', 'r152.2.jpg', 'r152.3.jpg', '0', NULL, NULL, 4, 1, 1, 0, 1, NULL),
(91, 'Xe điện VinFast VF5 Plus 2025 Xám', 'VF5 Plus 2025 xám xi măng\r\nOdo: 3v3 km\r\nXe còn bảo hiểm thân vỏ', 400000000.00, 30, 'Còn hàng', NULL, 'vf3.0.jpg', 13, 'vf3.2.jpg', 'vf3.3.jpg', 'vf3.1.jpg', '0', NULL, NULL, 4, 1, 1, 0, 1, NULL),
(92, 'Suzuki GSX 150r 2020', '𝗡𝗵𝗮̣̂𝗻 𝗚𝗶𝗮𝗼 𝗟𝘂̛𝘂 - 𝗠𝘂𝗮 𝗕𝗮́𝗻 - Đ𝗼̂̉𝗶 𝗖𝗮́𝗰 𝗫𝗲 𝗟𝗲̂𝗻 Đ𝗼̛̀𝗶 𝗚𝗶𝗮́ 𝗧𝗼̂́𝘁\r\n✔ Bao Check test 1 đổi 1 Miễn Phí Trong 1 Tuần \r\n✔ Hỗ trợ trả góp đến 45tr chỉ cần CCCD\r\n✔ Hỗ trợ trả góp 0% cho thẻ tín dụng\r\n✔ Hỗ trợ trả góp cho cả ae nợ chú ý, xấu\r\n✔ Bảo hành động cơ xe 2 năm', 29000000.00, 99, 'Còn hàng', NULL, 'su1.0.jpg', 13, 'su1.1.jpg', 'su1.3.jpg', 'su1.2.jpg', '0', NULL, NULL, 4, 1, 1, 0, 1, NULL),
(93, 'KIA k2700 xe đẹp sx2014i', 'KIA K2700 SX 2014 xe đẹp. có màn hình cam hanh trình cam lùi. thùng sàn inox. lốp đẹp. khám phí dài. điều hòa mát. xe chất lượng các bác hợp việc đến xem xe nhé', 179000000.00, 49, '0', NULL, 'xetai1.0jpg.jpg', 13, 'xetai1.1.jpg', 'xetai1.3.jpg', 'xetai1.2.jpg', '0', NULL, NULL, 4, 1, 1, 0, 1, NULL),
(94, 'Đầu kéo HOWO 420, đời 2021.', 'Bán đầu kéo HOWO đời 2021,cầu dầu, máy 420, xe nguyên zin, hồ sơ rút sãn.', 700000000.00, 2, 'Còn hàng', NULL, 'keo1.0.jpg', 13, 'keo1.1.jpg', 'keo1.3.jpg', 'keo1.4.jpg', '0', NULL, NULL, 4, 1, 1, 0, 1, NULL),
(96, 'Xe máy điện Vespa màu Xám lịch sự - bền bỉ', 'Xe còn khá mới anh em giúp em', 17990000.00, 99, 'Còn hàng', NULL, 'xedien1.0.jpg', 13, 'xedien1.1.jpg', 'xedien1.3.jpg', NULL, '0', NULL, NULL, 4, 1, 1, 0, 1, NULL),
(97, 'Xe đạp trợ lực Nhật Bản 20 inch', 'xe đạp trợ lưc nhật bãi pin mới bh 12tháng bánh 20inch khung nhôm full tính năng', 10000000.00, 20, 'Còn hàng', NULL, 'dap2.0.jpg', 13, 'dap2.2.jpg', 'dap2.3.jpg', 'dap2.1.jpg', '0', NULL, NULL, 4, 1, 1, 0, 1, NULL),
(98, 'Xe đạp địa hình Bianchi Magma 29.2', 'Pass xe đạp Bianchi magma 29.2 chính hãng hình thức 98% đẹp ít sử dụng size 29 inch phù hợp ng chiều cao m7 -m85 đi đẹp xe khung nhôm aluminum siêu nhẹ vành size 29 phuộc suntour êm mọi địa hình bộ chuyển động shimano alivio 2x9,phanh đĩa thuỷ lực,lốp kenda chính hãng xe chính hãng của Ý phù hợp cho ng mới bắt đầu và đẹp xe thường xuyên xe khung siêu bền và lâu dài', 20000000.00, 38, 'Còn hàng', NULL, 'd4.0.jpg', 13, 'd4.1.jpg', 'd4.2.jpg', NULL, '0', NULL, NULL, 4, 1, 1, 0, 1, NULL),
(99, 'Kawasaki Zx25R 2022 abs.', '✔️ Màu đen đỏ ngầu chất sport, đời form đại diện Sport của nhà Kawasaki \r\n✔️ Động cơ 250cc , năng động , Công nghệ fun xăng điện tử, phanh đĩa to 2 kênh ABS, máy êm cực mượt, đầm xe. xe đã lên đôi Lốp to 190 siêu to\r\n✔️ Date 2022 abs  biển số 29 odo 11000 . Bảo hành đầy đủ 12 tháng\r\n✔️ Bảo hành Cam kết xe rất mới nguyên bản tuyệt đối\r\n✔️ Cam kết xe không tai nạn, không ngập nước', 85000000.00, 9, 'Còn hàng', 'kawa400.mp4', 'ka1.0.jpg', 14, 'ka1.1.jpg', 'ka1.2.jpg', 'ka1.3.jpg', NULL, NULL, NULL, 4, 0, 1, 0, 1, NULL),
(100, 'BMW 2015 320i', 'can bán bmw 320i máy mới B48 xe màu trắng xe còn nguyên bản', 500000000.00, 10, 'Còn hàng', 'bwm320i.mp4', '320i1.0.jpg', 14, '320i1.1.jpg', '320i1.2.jpg', '320i1.3.jpg', NULL, NULL, NULL, 4, 1, 1, 0, 1, NULL),
(101, 'BMW S1000RR 2018, XE ĐẸP ODO 22K', 'BMW S1000RR – hàng chơi khỏi bàn\r\n\r\n• ĐK lần đầu 2018\r\n• Odo 22.000km – siêu lướt - biển Sg \r\n• Màu xanh HP – gắp bạc nhìn là mê\r\n\r\n🔧 Đồ chơi đã lên sẵn:\r\n• Pô Austin Racing slip-on real\r\n• Chống đổ T-Rex\r\n• Bảo vệ lốc GB Racing\r\n• Chắn gió\r\n• Ốp sườn carbon\r\n• Ốp dè con carbon\r\n• Pát tăng sên\r\n• Cặp vỏ Pirelli Rosso còn dày cui', 499000000.00, 2, 'Còn hàng', 'ca2.0.mp4', 'ca2.0.jpg', 14, 'ca2.4.jpg', 'ca2.5.jpg', 'ca2.3.jpg', NULL, NULL, NULL, 4, 1, 1, 0, 1, NULL),
(102, 'Mercedes G63 AMG 2022', 'Mercedes G63 AMG 2022 siêu lướt 5.000km như xe thùng.\r\n- Màu ngọc lục bảo ghế đen.\r\n- Bản nhập chính hãng mới đăng ký 2025, bảo hành dài hạn tới 2028.', 9900000000.00, 0, 'Hết hàng', 'g632.0.mp4', 'g631.0.jpg', 14, 'g631.1.jpg', 'g631.2.jpg', 'g631.3.jpg', NULL, NULL, NULL, 4, 1, 1, 0, 1, NULL),
(103, 'Nissan 370Z Nismo', '💥💥 Nissan #370z_Nismo sx 2020 độc bản siêu hiếm lướt nhẹ đúng 4.567 km căng đét như xe mới trong hãng 🔥🔥\r\n1 viên duy 1’ tại thị trường\r\n✅𝐌𝐚̀𝐮 𝐱𝐞: Đen - nội thất đỏ\r\n✅𝐒𝐚̉𝐧 𝐱𝐮𝐚̂́𝐭: 2020\r\n©️ 𝑂𝑑𝑜 𝑐ℎ𝑢𝑎̂̉𝑛: 4.567 km - mới như xe trong hãng thiếu mỗi cái thùng 🔥\r\n👌𝐓𝐢̀𝐧𝐡 𝐭𝐫𝐚̣𝐧𝐠 𝐱𝐞: Xe cá nhân 1 chủ siêu lướt 4.567 km tiết kiệm ngay 3🧄\r\n', 3999000000.00, 0, 'Hết hàng', 'nissan2.0.mp4', 'nissan2.0.jpg', 14, 'nissan2.1.jpg', 'nisson2.3.jpg', 'nisson2.4.jpg', 'nisson2.2.jpg', NULL, NULL, 4, 1, 1, 0, 1, NULL),
(105, 'KIA MORNING SI 2017 SỐ SÀN', 'Tại thị trường Việt Nam, KIA Morning được THACO lắp ráp và phân phối với nhiều phiên bản, từ tiêu chuẩn cho đến các bản cá tính như GT-Line và X-Line. Bước sang năm 2026, KIA Morning không còn đơn thuần là một chiếc xe “che mưa che nắng” giá rẻ. Nó đã trở thành một phụ kiện thời trang di động, phản ánh phong cách sống năng động của chủ nhân trẻ tuổi.\r\n\r\nĐiểm nhấn của thế hệ này chính là ngôn ngữ thiết kế “Opposites United” (Sự thống nhất của các mặt đối lập), mang lại diện mạo sắc sảo và hiện đại vượt tầm phân khúc. Khách hàng tại Việt Nam hiện nay không chỉ quan tâm đến giá rẻ mà còn đòi hỏi cao về trải nghiệm lái và hàm lượng công nghệ an toàn – những thứ mà KIA Morning 2026 đang thực hiện rất tốt.', 215000000.00, 50, 'Còn hàng', NULL, '1770960186_xe1.jpg', 17, '1770960232_1_xe1.2.jpg', '1770960232_2_xe1.3.jpg', '1770960232_3_1770960165_xe1.4.jpg', NULL, NULL, NULL, 8, 1, 1, 0, 1, NULL),
(106, 'MITSUBISHI XPANDER 2020 BẢN ECO', 'Trong bối cảnh thị trường xe ô tô tại Việt Nam ngày càng sôi động, Mitsubishi Xpander luôn giữ vững vị thế là một trong những mẫu MPV 7 chỗ được ưa chuộng nhất. Đặc biệt, Mitsubishi Xpander 2020 vẫn là một lựa chọn hấp dẫn đối với nhiều gia đình và doanh nghiệp vận tải nhờ sự kết hợp hài hòa giữa thiết kế hiện đại, không gian rộng rãi và khả năng vận hành bền bỉ. Phiên bản 2020 này không chỉ kế thừa những tinh hoa của thế hệ trước mà còn có những cải tiến nhất định, đáp ứng tốt nhu cầu di chuyển đa dạng trên nhiều địa hình khác nhau tại Việt Nam.\r\n\r\nVới mức giá xe cũ phải chăng hơn so với xe mới, Xpander 2020 trở thành tâm điểm tìm kiếm của những ai muốn sở hữu một chiếc xe đa dụng, tiết kiệm nhiên liệu mà vẫn đảm bảo đầy đủ tiện nghi và an toàn. Bài viết này sẽ đi sâu phân tích những đặc điểm nổi bật, giá cả trên thị trường xe cũ, cũng như so sánh Xpander 2020 với các phiên bản khác để giúp bạn đưa ra quyết định mua sắm thông minh nhất.\r\n\r\nTóm tắt nhanh: Mitsubishi Xpander 2020 là mẫu MPV 7 chỗ lý tưởng cho gia đình Việt, nổi bật với thiết kế năng động, nội thất rộng rãi và khả năng vận hành ổn định. Xe cung cấp giá trị tốt trên thị trường xe cũ, là lựa chọn kinh tế nhưng vẫn đầy đủ tiện nghi và an toàn cho nhu cầu di chuyển hàng ngày.\r\n\r\nMitsubishi Xpander 2020: Tổng quan và điểm nhấn hấp dẫn\r\nMitsubishi Xpander 2020 tiếp tục khẳng định vị thế của mình trong phân khúc MPV lai SUV nhờ sự kết hợp độc đáo giữa vẻ ngoài mạnh mẽ và không gian nội thất linh hoạt. Dù đã ra mắt được vài năm, phiên bản này vẫn giữ được sức hút nhờ những giá trị cốt lõi mà nó mang lại cho người dùng Việt.', 425000000.00, 30, 'Còn hàng', NULL, '1770960789_xe2.2.png', 17, '1770960804_1_xe2.3.png', '1770960846_2_xe2.4.png', NULL, NULL, NULL, NULL, 8, 1, 1, 5, 1, NULL),
(107, 'CHEVROLET CAPTIVA LTZ 2015', 'Băng lướt qua những con đường tuyệt đẹp giữa mùa vàng cao nguyên đang ngập tràn miền Tây Bắc, Chevrolet Captiva LTZ 2015, chiếc xe thể thao đa dụng 7 chỗ này đã để lại những trải nghiệm khó quên đối với chúng tôi trong suốt hành trình bởi sự tiện dụng, an toàn và khả năng vận hành nổi bật.\r\nĐường cao tốc vốn đã vắng, đi buổi tối lại càng thưa người hơn, thế nên cứ vút chân ga theo đúng tốc độ cho phép mà “phóng”. Băng lướt trong đêm tối nhưng vẫn vô cùng tự tin bởi khả năng vận hành của Captiva LTZ 2015 thực sự ấn tượng và tạo cảm giác rất an toàn. Xe chạy êm, đầm chắc và hệ thống đèn cốt/pha cung cấp dải sáng và cường độ sáng rất tốt.\r\n\r\nTrên mẫu xe Captiva LTZ 2015 có một chức năng giúp người lái tiết kiệm nhiên liệu là vận hành xe ở chế độ ECO, khi đó động cơ chỉ hoạt động ở dải vòng tua thấp nhằm mục đích tiết kiệm nhiên liệu. Nhưng khi chuyển sang chế độ hoạt động bình thường, xe tăng tốc mạnh mẽ hơn hẳn.', 195000000.00, 25, 'Còn hàng', NULL, '1770961273_xe3.jpg', 17, '1770961288_1_xe3.3.png', '1770961304_2_xe3.1.png', NULL, NULL, NULL, NULL, 8, 1, 1, 0, 1, NULL),
(108, 'FORD ECOSPORT 2017 TITANIUM', 'Xe Ford EcoSport hoàn toàn mới tại thị trường Việt Nam năm 2017 mang đến cho khách hàng sự đa dạng về màu sắc, giúp người mua dễ dàng tìm được lựa chọn phù hợp với sở thích và phong thủy cá nhân. Tổng cộng có 8 màu sơn ngoại thất để khách hàng lựa chọn, bao gồm các tông màu phổ biến và một số màu cá tính hơn. Các màu sắc có sẵn bao gồm: Màu nâu hổ phách, Trắng kim cương, Bạc Ánh kim, Ghi ánh thép, Đỏ sao hỏa, Xanh dương, Đỏ ngọc Ruby, và Đen Panther. Sự phong phú về màu sắc là một lợi thế của mẫu xe này, đáp ứng thị hiếu đa dạng của người tiêu dùng.\r\n\r\nCác phiên bản của Ford EcoSport 2017 tại Việt Nam bao gồm Ford EcoSport số sàn MT (Trend), Ford EcoSport Titanium tự động và Ford EcoSport Black Edition tự động. Mỗi phiên bản có sự khác biệt nhất định về trang bị nội thất, tiện nghi và một số chi tiết ngoại thất (như phiên bản Black Edition với gói trang bị thể thao), mang đến các tùy chọn khác nhau về mức giá và tính năng, phù hợp với nhu cầu và ngân sách đa dạng của khách hàng.', 570000000.00, 99, 'Còn hàng', NULL, '1770963484_xe4.jpg', 17, '1770963498_1_xe4.1.png', '1770963512_2_xe4.3.png', NULL, NULL, NULL, NULL, 8, 1, 1, 0, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sukien_khuyenmai`
--

DROP TABLE IF EXISTS `sukien_khuyenmai`;
CREATE TABLE IF NOT EXISTS `sukien_khuyenmai` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tenSuKien` varchar(255) NOT NULL,
  `mucGiamGia` int DEFAULT '0',
  `ngayBatDau` datetime NOT NULL,
  `ngayKetThuc` datetime NOT NULL,
  `trangThai` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sukien_khuyenmai`
--

INSERT INTO `sukien_khuyenmai` (`id`, `tenSuKien`, `mucGiamGia`, `ngayBatDau`, `ngayKetThuc`, `trangThai`) VALUES
(1, 'Nay tết giảm nhé ae', 30, '2026-02-13 18:49:00', '2026-02-13 18:51:00', 1),
(2, 'Nay tết giảm nhé ae', 30, '2026-02-13 18:51:00', '2026-02-13 18:52:00', 1),
(3, 'Nay tết nhé ae', 30, '2026-02-13 18:52:00', '2026-02-13 18:53:00', 1),
(4, 'Nay tết nhé ae', 40, '2026-02-13 18:55:00', '2026-02-13 18:56:00', 1),
(5, 'tết nhé ae', 28, '2026-02-13 19:00:00', '2026-02-13 19:01:00', 1),
(6, 'TẾT TẾT TếT AAA', 90, '2026-02-13 19:15:00', '2026-02-13 19:30:00', 1),
(7, 'TẾT TẾT giảm 10%', 10, '2026-02-13 19:33:00', '2026-02-13 19:40:00', 1),
(8, '123 TếT tết tết', 10, '2026-02-13 19:44:00', '2026-02-13 19:50:00', 1),
(9, 'Nay tết giảm nhé ae', 60, '2026-02-13 19:55:00', '2026-02-13 20:00:00', 1),
(10, 'ABC', 50, '2026-02-13 20:01:00', '2026-02-13 20:30:00', 1),
(11, 'TẾT TẾT tết tết đên r', 79, '2026-02-13 20:38:00', '2026-02-13 21:00:00', 1),
(12, 'TẾT 3000', 90, '2026-02-13 21:02:00', '2026-02-13 21:14:00', 1),
(13, 'Năm mới sale', 90, '2026-02-13 21:34:00', '2026-02-13 21:38:00', 1),
(14, 'Tết 2027', 25, '2026-02-26 11:15:00', '2026-02-26 11:30:00', 1),
(15, 'Sale tháng 2', 10, '2026-02-26 11:50:00', '2026-02-26 11:59:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `taikhoan`
--

DROP TABLE IF EXISTS `taikhoan`;
CREATE TABLE IF NOT EXISTS `taikhoan` (
  `maTaiKhoan` int NOT NULL AUTO_INCREMENT,
  `tenNguoiDung` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tenDangNhap` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `matKhau` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `soDienThoai` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `diaChi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vaitro` enum('admin','seller','buyer') COLLATE utf8mb4_unicode_ci NOT NULL,
  `trangThai` tinyint(1) NOT NULL DEFAULT '1',
  `shopThamGiaSuKien` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`maTaiKhoan`),
  UNIQUE KEY `uk_tenDangNhap` (`tenDangNhap`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `taikhoan`
--

INSERT INTO `taikhoan` (`maTaiKhoan`, `tenNguoiDung`, `tenDangNhap`, `matKhau`, `soDienThoai`, `diaChi`, `vaitro`, `trangThai`, `shopThamGiaSuKien`) VALUES
(1, 'NguyenVan', 'mua@gmail.com', '$2y$10$PLu0.yaNMnEU.wKHFOGCTeRMoQeN.a5kfV7evgmDwQULghcyISkPK', '0321412388', 'Cần Thơ', 'buyer', 1, 1),
(2, 'Nguyen', 'ban@gmail.com', '$2y$10$BddX7nPddqP1GZTFUc3Ld.QoPOxFcDJ4dVJbCC7W0W3Mr42z.QKx6', '0351268532', 'An Giang', 'seller', 1, 0),
(3, 'Nhi', 'ban1@gmail.com', '$2y$10$Tafvxk3n/PWRA3nHJ5eYHOl1n98SPccDOOeQwYv1PzQ/TDUDL7Cwi', '0842560721', 'Vĩnh Long', 'seller', 1, 1),
(7, 'Phu', 'nguoiban@gmail.com', '$2y$10$965nPYkyY3G1M6RzfHySM.91svOclqMNJ25FtYlLwviHRu0Rg7Q3.', '115', 'cam', 'seller', 1, 1),
(8, 'Lan', 'nguoimua@gmail.com', '$2y$10$Rdm4Vi1aGyTePAmg2YpXZ.su2fVKnwG5LyBtRASszt.GQQHfh06XC', '09672345124', '', 'buyer', 1, 1),
(9, 'Diep', 'admin1@gmail.com', '$2y$10$oBewHwmtQaujcuFX.wrrce.hzG0VSyOCG9cjQcjxNiDe.uhgGIoYG', '', '', 'admin', 1, 1),
(10, 'nhan', 'nhan@gmail.com', '$2y$10$t8SUXuSLp1fMKy9FRrlqZOFd7icdZ4UUhG1GtQzQUhhUi.4N1Lx0y', '', '', 'buyer', 0, 1),
(11, 'Phan Đặng Đức Nguyên', 'ducnguyen01072005@gmail.com', '$2y$10$SeX2xXl6ud/GM58sWwbSZO/7MUuQoVxLTPQnPesACDRlssf7rFtIy', '0866645161', 'Huyện Chợ Mới Tỉnh An Giang', 'buyer', 1, 1),
(12, 'Anh Tuấn', 'Atuan@gmail.com', '$2y$10$m4PdrNeyXY.ljv7gK1PyYu2H9JWGv.QsqCRnLII.hr0HIjQ6klztW', '01247473825', 'Châu Đốc An Giang', 'seller', 1, 1),
(13, 'Nguyễn Đức Lương', 'Aluong@gmail.com', '$2y$10$utCXOP8bFCLKvO9Wrr2PaOLxIugumFF.uUOi4uA2qAfxWNBIuPZqO', '0989898988', 'Phong Điền Cần Thơ', 'seller', 1, 1),
(14, 'Quang Thuận', 'thuan@gmail.com', '$2y$10$rvpyrrS5TYgJPS14bZykuu.3AiZHuUWLSGOMDfixzuDUteg0CDr9i', '0767676543', 'Ô Môn Cần Thơ', 'seller', 1, 1),
(15, 'test1@11', 'test1@11', '$2y$10$aiDtE5RPvPzKD71jP4ukpORzXOEb5Q1OMHyqNN/XgWae.cBIDfXTu', '0939011666', 'ấp 7', 'seller', 1, 1),
(16, 'test1@22', 'test1@22', '$2y$10$wbx3ry3pnF8/6hRC9SjWJugJrGTXMEA5md7xkYtohvDnTxUZlvMv.', '0763992600', 'ấp 7', 'seller', 1, 1),
(17, 'Auto77', 'ban@1mail.com', '$2y$10$qNlV43SK4JP/86tlV73e5e1byh5A4NzM.SWczBvmcvjVJ07yLKv.G', '0978752279', 'Hà TâyXuân Lộc-Hà Bắc', 'seller', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `thanhtoan`
--

DROP TABLE IF EXISTS `thanhtoan`;
CREATE TABLE IF NOT EXISTS `thanhtoan` (
  `maThanhToan` int NOT NULL AUTO_INCREMENT,
  `maDonHang` int NOT NULL,
  `phuongThuc` enum('cod','vnpay','fake_vnpay') COLLATE utf8mb4_unicode_ci NOT NULL,
  `soTien` double NOT NULL,
  `trangThai` enum('pending','paid','failed','success') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `ngayThanhToan` datetime DEFAULT CURRENT_TIMESTAMP,
  `nganHang` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `maGiaoDich` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`maThanhToan`),
  KEY `fk_thanhtoan_donhang` (`maDonHang`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `thanhtoan`
--

INSERT INTO `thanhtoan` (`maThanhToan`, `maDonHang`, `phuongThuc`, `soTien`, `trangThai`, `ngayThanhToan`, `nganHang`, `maGiaoDich`) VALUES
(1, 50, 'fake_vnpay', 499000000, 'paid', '2026-02-12 22:33:50', NULL, NULL),
(2, 50, 'vnpay', 499000000, 'success', '2026-02-12 22:35:47', 'BIDV', 'VNPAY1770910547'),
(3, 51, 'cod', 499000000, 'pending', '2026-02-12 22:36:15', NULL, NULL),
(4, 52, 'fake_vnpay', 3200000, 'paid', '2026-02-13 09:32:05', NULL, NULL),
(5, 52, 'vnpay', 3200000, 'success', '2026-02-13 09:32:13', 'ACB', 'VNPAY1770949933'),
(6, 57, 'cod', 146250000, 'pending', '2026-02-26 11:20:44', NULL, NULL),
(7, 58, 'cod', 2625000, 'pending', '2026-02-26 11:24:59', NULL, NULL);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chitietdonhang`
--
ALTER TABLE `chitietdonhang`
  ADD CONSTRAINT `fk_chitietdonhang_donhang` FOREIGN KEY (`maDonHang`) REFERENCES `donhang` (`maDonHang`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_chitietdonhang_nguoiBan` FOREIGN KEY (`maNguoiBan`) REFERENCES `taikhoan` (`maTaiKhoan`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_chitietdonhang_sanpham` FOREIGN KEY (`maSanPham`) REFERENCES `sanpham` (`maSanPham`) ON DELETE CASCADE;

--
-- Constraints for table `danhgia`
--
ALTER TABLE `danhgia`
  ADD CONSTRAINT `fk_danhgia_nguoidung` FOREIGN KEY (`maNguoiDung`) REFERENCES `taikhoan` (`maTaiKhoan`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_danhgia_sanpham` FOREIGN KEY (`maSanPham`) REFERENCES `sanpham` (`maSanPham`) ON DELETE CASCADE;

--
-- Constraints for table `donhang`
--
ALTER TABLE `donhang`
  ADD CONSTRAINT `fk_donhang_nguoiMua` FOREIGN KEY (`maNguoiMua`) REFERENCES `taikhoan` (`maTaiKhoan`) ON DELETE CASCADE;

--
-- Constraints for table `nhantin`
--
ALTER TABLE `nhantin`
  ADD CONSTRAINT `fk_thongbao_nguoiGui` FOREIGN KEY (`maNguoiGui`) REFERENCES `taikhoan` (`maTaiKhoan`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_thongbao_nguoiNhan` FOREIGN KEY (`maNguoiNhan`) REFERENCES `taikhoan` (`maTaiKhoan`) ON DELETE CASCADE;

--
-- Constraints for table `sanpham`
--
ALTER TABLE `sanpham`
  ADD CONSTRAINT `fk_sanpham_danhmuc` FOREIGN KEY (`maDanhMuc`) REFERENCES `danhmuc` (`maDanhMuc`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_sanpham_nguoiBan` FOREIGN KEY (`maNguoiBan`) REFERENCES `taikhoan` (`maTaiKhoan`) ON DELETE CASCADE;

--
-- Constraints for table `thanhtoan`
--
ALTER TABLE `thanhtoan`
  ADD CONSTRAINT `fk_thanhtoan_donhang` FOREIGN KEY (`maDonHang`) REFERENCES `donhang` (`maDonHang`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
