-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 04, 2026 at 02:30 PM
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
-- Database: `bandocu`
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `banner`
--

INSERT INTO `banner` (`id`, `hinh`, `link`, `trangthai`, `thu_tu`) VALUES
(5, 'assets/uploads/banner/1769862417_Máy chạy bộ Rovera.png', 'http://localhost/webbandocudoan2/product.php?id=30', 1, 0),
(6, 'assets/uploads/banner/1769862893_bếp 4 h.png', 'http://localhost/webbandocudoan2/product.php?id=26', 1, 1),
(7, 'assets/uploads/banner/1769955249_nubia.png', 'http://localhost/webbandocudoan2/product.php?id=12', 1, 3),
(8, 'assets/uploads/banner/1772695839_banner1.jpg', '', 1, 0),
(9, 'assets/uploads/banner/1772695849_banner3.jpg', '', 1, 0),
(10, 'assets/uploads/banner/1772695857_banner2.png', '', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `baocao_shop`
--

DROP TABLE IF EXISTS `baocao_shop`;
CREATE TABLE IF NOT EXISTS `baocao_shop` (
  `maBaoCao` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `loaiBaoCao` enum('shop','sanpham') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'shop',
  `maShop` int NOT NULL,
  `maSanPham` int DEFAULT NULL,
  `maNguoiBaoCao` int NOT NULL,
  `tenShop` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tenNguoiBaoCao` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lyDo` enum('hang_gia','lua_dao','hang_cam','gia_mao','gian_lan_spam','khac') COLLATE utf8mb4_unicode_ci NOT NULL,
  `lyDoKhac` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `moTa` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `bangChung` longtext COLLATE utf8mb4_unicode_ci,
  `trangThai` enum('cho_xu_ly','dang_xu_ly','da_xac_minh','tu_choi','da_dong') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cho_xu_ly',
  `ghiChuAdmin` text COLLATE utf8mb4_unicode_ci,
  `ngayBaoCao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ngayXuLy` datetime DEFAULT NULL,
  PRIMARY KEY (`maBaoCao`),
  KEY `maShop` (`maShop`),
  KEY `maNguoiBaoCao` (`maNguoiBaoCao`),
  KEY `trangThai` (`trangThai`),
  KEY `idx_baocao_sanpham` (`maSanPham`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `baocao_shop`
--

INSERT INTO `baocao_shop` (`maBaoCao`, `loaiBaoCao`, `maShop`, `maSanPham`, `maNguoiBaoCao`, `tenShop`, `tenNguoiBaoCao`, `lyDo`, `lyDoKhac`, `moTa`, `bangChung`, `trangThai`, `ghiChuAdmin`, `ngayBaoCao`, `ngayXuLy`) VALUES
(1, 'shop', 2, NULL, 1, 'Nguyenđây', 'NguyenVan', 'hang_gia', '', 'Khứa này lừa đảo bán đồ giả không mua', '[{\"tenGoc\":\"uc.jpg\",\"tenLuu\":\"c3e8e317388d5401b3aa247fc9c471a0.jpg\",\"duongDan\":\"assets/uploads/bao_cao_shop/c3e8e317388d5401b3aa247fc9c471a0.jpg\",\"loai\":\"image\",\"mime\":\"image/jpeg\",\"kichThuoc\":167690}]', 'da_xac_minh', NULL, '2026-09-03 20:31:42', '2026-09-03 22:32:27'),
(2, 'shop', 2, NULL, 1, 'Nguyenđây', 'NguyenVan', 'gian_lan_spam', '', 'Spam tùm lum từa lưa chán vãi ò', '[{\"tenGoc\":\"1759202121573.jpeg\",\"tenLuu\":\"c437b6c1f9c0e310803f5cf6d2c4ce80.jpg\",\"duongDan\":\"assets/uploads/bao_cao_shop/c437b6c1f9c0e310803f5cf6d2c4ce80.jpg\",\"loai\":\"image\",\"mime\":\"image/jpeg\",\"kichThuoc\":1174573},{\"tenGoc\":\"1759202122006.jpeg\",\"tenLuu\":\"2a994cc0fb7b0d27a5b5b7957f6c8df3.jpg\",\"duongDan\":\"assets/uploads/bao_cao_shop/2a994cc0fb7b0d27a5b5b7957f6c8df3.jpg\",\"loai\":\"image\",\"mime\":\"image/jpeg\",\"kichThuoc\":1130595},{\"tenGoc\":\"1759202119846.jpeg\",\"tenLuu\":\"a059a77aea2233fbaaef2b4c55447720.jpg\",\"duongDan\":\"assets/uploads/bao_cao_shop/a059a77aea2233fbaaef2b4c55447720.jpg\",\"loai\":\"image\",\"mime\":\"image/jpeg\",\"kichThuoc\":1360354},{\"tenGoc\":\"1759202122586.jpeg\",\"tenLuu\":\"241498df1859d56b3925c12ebc448b93.jpg\",\"duongDan\":\"assets/uploads/bao_cao_shop/241498df1859d56b3925c12ebc448b93.jpg\",\"loai\":\"image\",\"mime\":\"image/jpeg\",\"kichThuoc\":1330449}]', 'da_xac_minh', NULL, '2026-09-03 21:51:38', '2026-09-03 21:53:22'),
(3, 'shop', 2, NULL, 1, 'Nguyenđây', 'NguyenVan', 'khac', 'Test lí do khác', 'vậy vậy đó hahahâhaaaâ', '[{\"tenGoc\":\"Untitled.png\",\"tenLuu\":\"01419d6ad473ea84abc611ccfdac6766.png\",\"duongDan\":\"assets/uploads/bao_cao_shop/01419d6ad473ea84abc611ccfdac6766.png\",\"loai\":\"image\",\"mime\":\"image/png\",\"kichThuoc\":2127889}]', 'da_xac_minh', NULL, '2026-09-03 22:34:00', '2026-09-03 22:34:08'),
(4, 'sanpham', 14, 101, 9, 'Quang Thuận', 'Diep', 'hang_gia', '', 'xe không khởi động máy được', '[{\"tenGoc\":\"1759202122006.jpeg\",\"tenLuu\":\"1d213829298e32320a285bf9b3f12886.jpg\",\"duongDan\":\"assets/uploads/bao_cao_san_pham/1d213829298e32320a285bf9b3f12886.jpg\",\"loai\":\"image\",\"mime\":\"image/jpeg\",\"kichThuoc\":1130595},{\"tenGoc\":\"1759202119846.jpeg\",\"tenLuu\":\"60db8e3a6071b47d8814bb5c69cb2e0a.jpg\",\"duongDan\":\"assets/uploads/bao_cao_san_pham/60db8e3a6071b47d8814bb5c69cb2e0a.jpg\",\"loai\":\"image\",\"mime\":\"image/jpeg\",\"kichThuoc\":1360354}]', 'dang_xu_ly', NULL, '2026-09-04 21:25:50', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `chitietdonhang`
--

DROP TABLE IF EXISTS `chitietdonhang`;
CREATE TABLE IF NOT EXISTS `chitietdonhang` (
  `maChiTiet` int NOT NULL AUTO_INCREMENT,
  `maDonHang` int NOT NULL,
  `maDonHang_Shop` int DEFAULT NULL,
  `maSanPham` int NOT NULL,
  `maNguoiBan` int DEFAULT NULL,
  `soLuong` int DEFAULT '1',
  `donGia` decimal(15,2) DEFAULT '0.00',
  `thanhTien` decimal(15,2) DEFAULT NULL,
  PRIMARY KEY (`maChiTiet`),
  KEY `fk_chitietdonhang_donhang` (`maDonHang`),
  KEY `fk_chitietdonhang_sanpham` (`maSanPham`),
  KEY `fk_chitietdonhang_nguoiBan` (`maNguoiBan`)
) ENGINE=InnoDB AUTO_INCREMENT=116 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chitietdonhang`
--

INSERT INTO `chitietdonhang` (`maChiTiet`, `maDonHang`, `maDonHang_Shop`, `maSanPham`, `maNguoiBan`, `soLuong`, `donGia`, `thanhTien`) VALUES
(85, 90, NULL, 86, 13, 2, 1500000.00, 3000000.00),
(86, 91, NULL, 92, 13, 1, 29000000.00, 29000000.00),
(89, 94, NULL, 87, 13, 1, 6990000.00, 6990000.00),
(90, 95, NULL, 96, 13, 1, 17990000.00, 17990000.00),
(91, 96, NULL, 94, 13, 1, 700000000.00, 700000000.00),
(96, 100, NULL, 109, 2, 1, 15000000.00, 15000000.00),
(97, 101, NULL, 98, 13, 1, 20000000.00, 20000000.00),
(98, 102, NULL, 109, 2, 1, 15000000.00, 15000000.00),
(99, 103, NULL, 109, 2, 1, 15000000.00, 15000000.00),
(100, 104, NULL, 101, 14, 1, 499000000.00, 499000000.00),
(101, 107, NULL, 96, 13, 1, 17990000.00, 17990000.00),
(105, 111, NULL, 101, 14, 1, 499000000.00, 499000000.00),
(107, 112, NULL, 100, 14, 1, 500000000.00, 500000000.00),
(109, 114, NULL, 99, 14, 1, 85000000.00, 85000000.00),
(111, 116, NULL, 101, 14, 2, 499000000.00, 998000000.00),
(112, 117, NULL, 92, 13, 2, 29000000.00, 58000000.00),
(113, 118, NULL, 100, 14, 1, 500000000.00, 500000000.00),
(114, 119, 88, 113, 2, 1, 49000000.00, 49000000.00);

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
(1, 322);

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
  `binhLuan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ngayDanhGia` datetime DEFAULT CURRENT_TIMESTAMP,
  `traLoiNguoiBan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ngayTraLoi` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_sanpham_nguoidung` (`maSanPham`,`maNguoiDung`),
  KEY `fk_danhgia_nguoidung` (`maNguoiDung`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `danhgia`
--

INSERT INTO `danhgia` (`id`, `maSanPham`, `maNguoiDung`, `soSao`, `binhLuan`, `ngayDanhGia`, `traLoiNguoiBan`, `ngayTraLoi`) VALUES
(2, 101, 9, 3, 'sản phẩm cũng được', '2026-02-12 23:11:35', 'Cảm ơn bn đã đánh giá sản phẩm của chúng tôi', '2026-06-19 22:50:48'),
(7, 109, 20, 5, 'Sản Phẩm xử dụng tốt cảm ơn shop!chúc shop buôn mai bán đắt', '2026-04-01 21:27:19', 'Cảm ơn bạn đã tin tưởng sản phẩm bên mình', '2026-04-01 21:30:00'),
(8, 101, 10002, 5, 'xe đẹp bóng như mới cho 5 sao', '2026-06-19 22:55:01', NULL, NULL),
(10, 99, 10006, 5, 'Xe chạy ok', '2026-07-14 21:31:17', 'Cảm ơn bạn đã ủng hộ shop ạ', '2026-07-14 21:31:49'),
(11, 113, 10000, 0, 'XE Đẹp chạy êm lắm shop', '2026-08-08 15:01:15', 'cảm ơn bạn chúc bạn đi ngon dùng bền', '2026-08-08 15:04:54');

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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `danhmuc`
--

INSERT INTO `danhmuc` (`maDanhMuc`, `tenDanhMuc`, `moTa`, `trangThai`, `thu_tu`) VALUES
(1, 'Điện Thoại', NULL, 0, 1),
(2, 'Thiết Bị Gia Dụng', NULL, 0, 2),
(3, 'Điện Máy', NULL, 0, 3),
(4, 'Khác', NULL, 0, 4),
(5, 'PC', NULL, 0, 5),
(6, 'Thiết Bị Điện Tử', NULL, 0, 6),
(7, 'Đồng hồ', NULL, 0, 7),
(8, 'Xe Ôtô', '', 0, 8),
(9, 'Sách', '', 0, 9),
(10, 'Xe Máy', NULL, 0, 10),
(11, 'Xe Đạp', NULL, 0, 11),
(12, 'Máy Ảnh', NULL, 0, 12),
(13, 'Sấy Dẻo', '', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `donhang`
--

DROP TABLE IF EXISTS `donhang`;
CREATE TABLE IF NOT EXISTS `donhang` (
  `maDonHang` int NOT NULL AUTO_INCREMENT,
  `ngayDat` datetime DEFAULT CURRENT_TIMESTAMP,
  `tongTien` decimal(15,2) DEFAULT '0.00',
  `trangThai` tinyint DEFAULT NULL,
  `maNguoiMua` int DEFAULT NULL,
  `maGiaoDichVNPAY` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`maDonHang`),
  KEY `fk_donhang_nguoiMua` (`maNguoiMua`)
) ENGINE=InnoDB AUTO_INCREMENT=120 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `donhang`
--

INSERT INTO `donhang` (`maDonHang`, `ngayDat`, `tongTien`, `trangThai`, `maNguoiMua`, `maGiaoDichVNPAY`) VALUES
(80, '2026-03-06 11:13:28', 570000000.00, 3, 1, NULL),
(81, '2026-03-06 11:13:40', 403750000.00, 2, 1, NULL),
(82, '2026-03-06 11:13:48', 570000000.00, 2, 1, NULL),
(83, '2026-03-06 11:20:21', 195000000.00, 2, 18, NULL),
(84, '2026-03-06 11:20:30', 215000000.00, 2, 18, NULL),
(85, '2026-03-06 15:00:49', 195000000.00, 2, 1, NULL),
(86, '2026-03-06 15:12:48', 195000000.00, 3, 1, NULL),
(87, '2026-03-13 11:29:26', 403750000.00, 2, 2, NULL),
(88, '2026-03-14 13:38:16', 195000000.00, 2, 1, NULL),
(89, '2026-03-23 15:24:45', 215000000.00, 0, 1, NULL),
(90, '2026-03-23 15:33:55', 3000000.00, 0, 1, NULL),
(91, '2026-03-23 15:40:39', 29000000.00, 0, 1, NULL),
(92, '2026-03-23 15:52:09', 425000000.00, 0, 1, NULL),
(93, '2026-03-23 15:52:32', 215000000.00, 0, 1, NULL),
(94, '2026-03-23 15:55:24', 6990000.00, 0, 1, NULL),
(95, '2026-03-23 16:02:30', 17990000.00, 0, 1, NULL),
(96, '2026-03-23 16:02:39', 700000000.00, 0, 1, NULL),
(97, '2026-03-27 09:40:16', 215000000.00, 2, 1, NULL),
(98, '2026-03-27 14:11:27', 620000000.00, 2, 1, NULL),
(99, '2026-03-27 14:43:41', 215000000.00, 0, 1, NULL),
(100, '2026-04-01 20:59:41', 15000000.00, 2, 1, NULL),
(101, '2026-04-01 21:18:22', 20000000.00, 3, 20, NULL),
(102, '2026-04-01 21:19:51', 15000000.00, 2, 20, NULL),
(103, '2026-06-10 15:43:10', 15000000.00, 2, 10000, NULL),
(104, '2026-06-19 22:52:16', 499000000.00, 2, 10002, NULL),
(107, '2026-07-13 19:14:19', 17990000.00, 3, 10002, NULL),
(108, '2026-07-13 19:40:49', 89900000.00, 2, 10002, NULL),
(109, '2026-07-14 20:27:15', 89900000.00, 2, 10002, NULL),
(111, '2026-07-14 21:09:19', 588900000.00, 2, 10002, NULL),
(112, '2026-07-14 21:12:41', 500000000.00, 2, 10006, NULL),
(113, '2026-07-14 21:19:47', 179800000.00, 2, 10006, NULL),
(114, '2026-07-14 21:30:29', 85000000.00, 2, 10006, NULL),
(115, '2026-07-15 23:03:29', 179800000.00, 2, 10006, NULL),
(116, '2026-07-15 23:04:58', 998000000.00, 2, 10006, NULL),
(117, '2026-08-07 17:05:57', 58000000.00, 3, 10000, NULL),
(118, '2026-08-07 17:07:29', 500000000.00, 0, 10000, NULL),
(119, '2026-08-08 14:35:25', 49000000.00, 2, 10000, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `donhang_shop`
--

DROP TABLE IF EXISTS `donhang_shop`;
CREATE TABLE IF NOT EXISTS `donhang_shop` (
  `maDonHang_Shop` int NOT NULL AUTO_INCREMENT,
  `maDonHang` int DEFAULT NULL,
  `maNguoiMua` int DEFAULT NULL,
  `maNguoiBan` int DEFAULT NULL,
  `tongTien` decimal(15,2) DEFAULT NULL,
  `trangThai` int DEFAULT '0',
  `ngayTao` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`maDonHang_Shop`),
  KEY `maDonHang` (`maDonHang`),
  KEY `maNguoiMua` (`maNguoiMua`),
  KEY `maNguoiBan` (`maNguoiBan`)
) ENGINE=InnoDB AUTO_INCREMENT=89 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donhang_shop`
--

INSERT INTO `donhang_shop` (`maDonHang_Shop`, `maDonHang`, `maNguoiMua`, `maNguoiBan`, `tongTien`, `trangThai`, `ngayTao`) VALUES
(63, 82, 1, NULL, 570000000.00, 0, '2026-03-06 11:14:52'),
(64, 81, 1, NULL, 403750000.00, 0, '2026-03-06 11:14:53'),
(65, 84, 18, NULL, 215000000.00, 0, '2026-03-06 11:20:57'),
(66, 83, 18, NULL, 195000000.00, 0, '2026-03-06 11:20:58'),
(67, 85, 1, NULL, 195000000.00, 0, '2026-03-06 15:01:35'),
(68, 87, 2, NULL, 403750000.00, 0, '2026-03-13 11:29:50'),
(69, 88, 1, NULL, 195000000.00, 0, '2026-03-14 13:39:14'),
(70, 97, 1, NULL, 215000000.00, 0, '2026-03-27 09:43:37'),
(71, 98, 1, NULL, 620000000.00, 0, '2026-03-27 14:11:39'),
(72, 100, 1, NULL, 15000000.00, 0, '2026-04-01 21:00:28'),
(73, 102, 20, NULL, 15000000.00, 0, '2026-04-01 21:24:13'),
(74, 103, 10000, NULL, 15000000.00, 0, '2026-06-10 15:43:35'),
(75, 104, 10002, NULL, 499000000.00, 0, '2026-06-19 22:53:09'),
(76, 108, 10002, NULL, 89900000.00, 0, '2026-07-13 19:41:15'),
(77, 109, 10002, NULL, 89900000.00, 0, '2026-07-14 20:27:26'),
(78, 111, 10002, NULL, 588900000.00, 0, '2026-07-14 21:09:58'),
(79, 112, 10006, NULL, 500000000.00, 0, '2026-07-14 21:13:10'),
(80, 113, 10006, NULL, 179800000.00, 0, '2026-07-14 21:20:35'),
(81, 114, 10006, NULL, 85000000.00, 0, '2026-07-14 21:30:48'),
(82, 115, 10006, NULL, 179800000.00, 0, '2026-07-15 23:03:58'),
(83, 116, 10006, NULL, 998000000.00, 0, '2026-07-15 23:05:12'),
(88, 119, 10000, 2, 49000000.00, 0, '2026-08-08 14:53:34');

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
  `soLanBaoCaoShopTheoDoi` int NOT NULL DEFAULT '3',
  `soLanBaoCaoSanPhamTheoDoi` int NOT NULL DEFAULT '3',
  `report_watch_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `thongBaoViPham` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `duyet_settings`
--

INSERT INTO `duyet_settings` (`id`, `auto_review`, `banned_words`, `warning_message`, `updated_at`, `soLanBaoCaoShopTheoDoi`, `soLanBaoCaoSanPhamTheoDoi`, `report_watch_enabled`, `thongBaoViPham`) VALUES
(1, 1, 'đồi trụy,mới 100%,ma túy,bóng cười,lựu đạn thật,súng thật', 'Sản phẩm chứa từ bị cấm!', '2025-11-16 06:47:44', 3, 3, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `giohang`
--

DROP TABLE IF EXISTS `giohang`;
CREATE TABLE IF NOT EXISTS `giohang` (
  `id` int NOT NULL AUTO_INCREMENT,
  `maNguoiDung` int NOT NULL,
  `maSanPham` int NOT NULL,
  `soLuong` int DEFAULT '1',
  `ngayThem` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_cart` (`maNguoiDung`,`maSanPham`),
  KEY `maSanPham` (`maSanPham`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `giohang`
--

INSERT INTO `giohang` (`id`, `maNguoiDung`, `maSanPham`, `soLuong`, `ngayThem`) VALUES
(29, 20, 100, 1, '2026-04-01 21:16:31'),
(30, 20, 99, 1, '2026-04-01 21:18:39'),
(31, 20, 96, 1, '2026-04-01 21:18:41');

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
) ENGINE=InnoDB AUTO_INCREMENT=230 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(61, 'Bạn có đơn hàng mới #36', 14, 14, '2026-01-18 12:32:49', NULL, 'da_xem', '2026-01-17 22:32:49'),
(62, '\r\n            📦 <b>Quang Thuận</b> vừa đặt <b>2</b> sản phẩm <b>“BMW S1000RR 2018, XE ĐẸP ODO 22K”</b><br>\r\n            💰 Đơn giá: 499.000.000₫<br>\r\n            🧮 Tổng: <b>998.000.000₫</b><br>\r\n            🧾 Mã đơn: #36\r\n            ', 14, 14, '2026-01-18 12:32:49', NULL, 'da_xem', '2026-01-17 22:32:49'),
(63, 'Bạn có đơn hàng mới #37', 14, 14, '2026-01-18 12:38:18', NULL, 'da_xem', '2026-01-17 22:38:18'),
(64, '\r\n            📦 <b>Quang Thuận</b> vừa đặt <b>1</b> sản phẩm <b>“BMW S1000RR 2018, XE ĐẸP ODO 22K”</b><br>\r\n            💰 Đơn giá: 499.000.000₫<br>\r\n            🧮 Tổng: <b>499.000.000₫</b><br>\r\n            🧾 Mã đơn: #37\r\n            ', 14, 14, '2026-01-18 12:38:18', NULL, 'da_xem', '2026-01-17 22:38:18'),
(65, 'Bạn có đơn hàng mới #41', 16, 13, '2026-01-23 21:19:27', NULL, 'chua_xem', '2026-01-23 07:19:27'),
(66, '\r\n            📦 <b>test1@22</b> vừa đặt <b>2</b> sản phẩm <b>“Xe đạp địa hình Bianchi Magma 29.2”</b><br>\r\n            💰 Đơn giá: 20.000.000₫<br>\r\n            🧮 Tổng: <b>40.000.000₫</b><br>\r\n            🧾 Mã đơn: #41\r\n            ', 16, 13, '2026-01-23 21:19:27', NULL, 'chua_xem', '2026-01-23 07:19:27'),
(67, 'Bạn có đơn hàng mới #41', 16, 14, '2026-01-23 21:19:27', NULL, 'da_xem', '2026-01-23 07:19:27'),
(68, '\r\n            📦 <b>test1@22</b> vừa đặt <b>1</b> sản phẩm <b>“Kawasaki Zx25R 2022 abs.”</b><br>\r\n            💰 Đơn giá: 85.000.000₫<br>\r\n            🧮 Tổng: <b>85.000.000₫</b><br>\r\n            🧾 Mã đơn: #41\r\n            ', 16, 14, '2026-01-23 21:19:27', NULL, 'da_xem', '2026-01-23 07:19:27'),
(69, 'Bạn có đơn hàng mới #41', 16, 14, '2026-01-23 21:19:27', NULL, 'da_xem', '2026-01-23 07:19:27'),
(70, '\r\n            📦 <b>test1@22</b> vừa đặt <b>1</b> sản phẩm <b>“BMW S1000RR 2018, XE ĐẸP ODO 22K”</b><br>\r\n            💰 Đơn giá: 499.000.000₫<br>\r\n            🧮 Tổng: <b>499.000.000₫</b><br>\r\n            🧾 Mã đơn: #41\r\n            ', 16, 14, '2026-01-23 21:19:27', NULL, 'da_xem', '2026-01-23 07:19:27'),
(71, 'Bạn có đơn hàng mới #41', 16, 14, '2026-01-23 21:19:27', NULL, 'da_xem', '2026-01-23 07:19:27'),
(72, '\r\n            📦 <b>test1@22</b> vừa đặt <b>1</b> sản phẩm <b>“Mercedes G63 AMG 2022”</b><br>\r\n            💰 Đơn giá: 9.900.000.000₫<br>\r\n            🧮 Tổng: <b>9.900.000.000₫</b><br>\r\n            🧾 Mã đơn: #41\r\n            ', 16, 14, '2026-01-23 21:19:27', NULL, 'da_xem', '2026-01-23 07:19:27'),
(73, 'Bạn có đơn hàng mới #42', 2, 14, '2026-01-23 21:29:48', NULL, 'da_xem', '2026-01-23 07:29:48'),
(74, '\r\n            📦 <b>Nguyen</b> vừa đặt <b>1</b> sản phẩm <b>“Nissan 370Z Nismo”</b><br>\r\n            💰 Đơn giá: 3.999.000.000₫<br>\r\n            🧮 Tổng: <b>3.999.000.000₫</b><br>\r\n            🧾 Mã đơn: #42\r\n            ', 2, 14, '2026-01-23 21:29:48', NULL, 'da_xem', '2026-01-23 07:29:48'),
(75, 'Sản phẩm mã 33 qua tháng 1 có giảm giá k shop', 20, 2, '2026-04-01 21:28:49', NULL, 'da_xem', '2026-04-01 14:28:49'),
(76, 'bạn chờ shop check nha', 2, 20, '2026-04-01 21:31:05', NULL, 'da_xem', '2026-04-01 14:31:05'),
(81, 'tiệm mình có máy ảnh không', 2, 9999, '2026-05-09 08:09:38', NULL, 'chua_xem', '2026-05-09 01:09:38'),
(82, 'Dạ chào bạn, hiện tại bên tiệm mình chưa có sẵn các dòng máy ảnh chuyên dụng ạ. 📸\n\nTuy nhiên, nếu bạn đang cần thiết bị để lưu trữ hoặc chỉnh sửa hình ảnh chuyên nghiệp thì mình đang có sẵn **iMac 2015** và **Laptop Dell Latitude 7400** cấu hình rất ổn định, ngoại hình mới 98-99% luôn ạ.\n\nKhông biết mình đang tìm máy ảnh để làm nghề hay chụp giải trí để em tư vấn thêm các món đồ công nghệ phù hợp cho mình nhé? 😊', 9999, 2, '2026-05-09 08:09:43', NULL, 'da_xem', '2026-05-09 01:09:43'),
(83, 'tôi thấy có con canon eos mà', 2, 9999, '2026-05-09 08:10:13', NULL, 'chua_xem', '2026-05-09 01:10:13'),
(84, 'Dạ, thành thật xin lỗi bạn về sự nhầm lẫn này ạ! 🙏 \n\nCó thể mẫu Canon EOS bạn thấy là hàng vừa mới \"bay\" hoặc là tin đăng cũ nên hiện tại thực tế ở tiệm em đã hết dòng máy ảnh đó rồi ạ. Tiếc quá, mong bạn thông cảm giúp em nhé!\n\nNếu mình đang cần thiết bị có màn hình chuẩn, sắc nét để đổ file từ máy ảnh sang chỉnh sửa hậu kỳ chuyên nghiệp thì con **iMac 2015** bên em đang sẵn hàng (mới 99%), làm ảnh là cực kỳ chuẩn bài luôn đó ạ.\n\nKhông biết mình đang tìm dòng Canon cụ thể nào để nếu có hàng về lại, em nhắn cho mình ngay nhé? 😊', 9999, 2, '2026-05-09 08:10:21', NULL, 'da_xem', '2026-05-09 01:10:21'),
(85, 'có xe máy điện không', 2, 9999, '2026-05-09 08:10:51', NULL, 'chua_xem', '2026-05-09 01:10:51'),
(86, 'Dạ có luôn ạ! May quá bên em vừa về một chiếc **Xe máy điện Vespa màu Xám** cực kỳ sang trọng và lịch sự luôn ạ. 🛵\n\nXe ngoại hình còn rất mới, chạy bền bỉ và êm ái, rất hợp để mình đi làm hay đi dạo phố. Giá em nó hiện tại là **17.990.000đ** ạ.\n\nKhông biết mình muốn qua cửa hàng xem xe trực tiếp hay cần em gửi thêm hình ảnh/video chi tiết của xe qua tin nhắn cho mình xem trước ạ? 😊', 9999, 2, '2026-05-09 08:10:56', NULL, 'da_xem', '2026-05-09 01:10:56'),
(87, 'xe oto điện có không', 2, 9999, '2026-05-09 08:11:23', NULL, 'chua_xem', '2026-05-09 01:11:23'),
(88, 'Dạ, hiện tại ô tô điện thì bên em chưa có hàng rồi ạ. Tiếc quá, mong mình thông cảm giúp em nhé! 🙏\n\nBên em hiện đang chủ yếu sẵn các dòng đồ công nghệ và âm thanh cao cấp. Nếu mình thích cảm giác trải nghiệm sang xịn mịn thì em đang có sẵn **Airpods Max** (12.900.000đ) hoặc bộ **Dàn karaoke MK Acoustic** nghe cực phê luôn đó ạ. 🎶\n\nKhông biết ngoài xe điện ra, mình có đang quan tâm thêm món đồ công nghệ hay thiết bị âm thanh nào khác để em tư vấn cho mình không ạ? 😊', 9999, 2, '2026-05-09 08:11:31', NULL, 'da_xem', '2026-05-09 01:11:31'),
(89, 'có vf5 không', 2, 9999, '2026-05-09 08:11:48', NULL, 'chua_xem', '2026-05-09 01:11:48'),
(90, 'Dạ có luôn ạ! Em vừa kiểm tra lại kho, may quá bên em đang sẵn đúng một chiếc **VinFast VF5 Plus đời 2025** màu Xám xi măng cực kỳ thời thượng luôn đây ạ. 🚗✨\n\nThông tin chi tiết để mình tham khảo:\n- **Odo:** 33.000 km.\n- **Tình trạng:** Xe còn rất mới, đặc biệt là đã có sẵn **bảo hiểm thân vỏ** nên mình yên tâm sử dụng luôn.\n- **Giá bán:** 400.000.000đ ạ.\n\nKhông biết mình có muốn em gửi thêm hình ảnh chi tiết của xe hay muốn hẹn lịch qua cửa hàng xem xe trực tiếp không ạ? 😊', 9999, 2, '2026-05-09 08:11:54', NULL, 'da_xem', '2026-05-09 01:11:54'),
(91, 'tôi tên phú', 1, 9999, '2026-05-22 08:36:28', NULL, 'chua_xem', '2026-05-22 01:36:28'),
(92, 'Dạ, em chào anh Phú ạ! Rất vui được hỗ trợ anh ngày hôm nay. 🥰\n\nHiện tại bên em đang sẵn có các dòng máy tính làm việc (Dell Latitude, iMac), tai nghe cao cấp Airpods Max hay cả dàn loa/karaoke giải trí cực chất nữa ạ. \n\nKhông biết anh Phú đang quan tâm hoặc cần tìm sản phẩm phục vụ cho nhu cầu công việc hay giải trí gia đình để em tư vấn chi tiết nhất cho mình ạ? 🌟', 9999, 1, '2026-05-22 08:36:33', NULL, 'da_xem', '2026-05-22 01:36:33'),
(93, 'tôi muốn mua xe', 1, 9999, '2026-05-22 08:37:01', NULL, 'chua_xem', '2026-05-22 01:37:01'),
(94, 'Dạ anh Phú ơi, hiện tại bên em lại chưa kinh doanh các dòng xe rồi ạ. 😅 Bên em đang chuyên về các thiết bị công nghệ phục vụ công việc và âm thanh giải trí thôi ạ. \n\nNếu anh đang chuẩn bị sắm xe để di chuyển, một chiếc tai nghe cao cấp **Airpods Max (12.900.000đ)** với khả năng chống ồn vượt trội sẽ là người bạn đồng hành cực kỳ tuyệt vời cho anh trên mọi cung đường đó ạ. 🎧\n\nKhông biết ngoài xe ra, anh Phú có muốn tham khảo thêm các dòng laptop làm việc hay loa kéo, dàn karaoke giải trí nào bên em để em tư vấn chi tiết cho mình không ạ? 🥰', 9999, 1, '2026-05-22 08:37:08', NULL, 'da_xem', '2026-05-22 01:37:08'),
(95, 'tôi muốn mua xe', 1, 9999, '2026-05-22 08:42:36', NULL, 'chua_xem', '2026-05-22 01:42:36'),
(96, 'Dạ anh Phú ơi, em rất tiếc là hiện tại bên em thực sự chưa kinh doanh các dòng xe rồi ạ. 🥺 \n\nHay là mình sắm trước một chiếc laptop **Dell Latitude 7400 (12.000.000đ)** hiệu năng mượt mà để hỗ trợ công việc, giúp mình mau chóng \"rinh\" xe xịn về nha anh? Hoặc một chiếc tai nghe **Airpods Max (12.900.000đ)** để đeo khi ngồi xe di chuyển cũng cực kỳ phong cách và tiện lợi luôn ạ. 🎧\n\nAnh Phú thấy sao ạ, em hỗ trợ tư vấn chi tiết sản phẩm nào cho mình nhé? 🥰', 9999, 1, '2026-05-22 08:42:44', NULL, 'da_xem', '2026-05-22 01:42:44'),
(97, 'tôi muốn mua xe bên bạn có xe nào k', 1, 9999, '2026-05-22 08:43:10', NULL, 'chua_xem', '2026-05-22 01:43:10'),
(98, 'Dạ anh Phú ơi, em đã lùng sục khắp các kho bên em rồi nhưng thực sự là bên em hoàn toàn không có dòng xe nào hết ạ. 😭 Cửa hàng em chỉ chuyên các thiết bị công nghệ và âm thanh thôi nè. \n\nHay là thay vì mua xe, mình rinh tạm **Dàn karaoke MK Acoustic (26.000.000đ)** âm thanh cực đỉnh về hát giải trí tại gia, hoặc chiếc **Imac 2015 (12.500.000đ)** màn hình lớn siêu đẹp để làm việc cho năng suất nha anh? 💻🎤\n\nAnh Phú thấy chiếc nào \"hợp gu\" mình hơn để em tư vấn chi tiết và hỗ trợ lên đơn cho mình luôn ạ? 🥰', 9999, 1, '2026-05-22 08:43:17', NULL, 'da_xem', '2026-05-22 01:43:17'),
(99, 'có xe đạp không', 1, 9999, '2026-05-22 08:45:08', NULL, 'chua_xem', '2026-05-22 01:45:08'),
(100, 'Dạ có chứ anh Phú ơi! May quá là may, bên em vừa về được vài mẫu xe đạp cực xịn sò để phục vụ anh đây ạ! 😍 \n\nEm gửi anh 3 lựa chọn siêu chất lượng này, anh xem \"hợp gu\" chiếc nào nhé:\n\n1. **Xe đạp đua (3.200.000đ):** Sườn carbon đời cao siêu nhẹ, dây âm sườn thẩm mỹ, group điện 2x12 mượt mà cho anh đam mê tốc độ. 🚴‍♂️\n2. **Xe đạp trợ lực Nhật Bản 20 inch (10.000.000đ):** Khung nhôm bền bỉ, pin mới tinh bảo hành 12 tháng, đạp nhẹ tênh, cực kỳ tiện lợi để đi dạo phố. 🇯🇵\n3. **Xe đạp địa hình Bianchi Magma 29.2 (20.000.000đ):** Thương hiệu Ý đẳng cấp (mới 98%), phuộc êm ái thách thức mọi địa hình, phù hợp cho anh Phú nếu cao từ 1m7 - 1m85 muốn rèn luyện sức khỏe bền bỉ ạ. \n\nAnh Phú đang tìm dáng xe thể thao cá tính hay xe trợ lực đi dạo nhẹ nhàng để em hỗ trợ tư vấn chi tiết và lên đơn cho mình luôn nha anh? 🥰', 9999, 1, '2026-05-22 08:45:15', NULL, 'da_xem', '2026-05-22 01:45:15'),
(101, 'có xe ôtô không', 1, 9999, '2026-05-22 08:45:39', NULL, 'chua_xem', '2026-05-22 01:45:39'),
(102, 'Dạ anh Phú ơi, xe ô tô thực tế để lăn bánh trên đường thì bên em lại không có mất rồi ạ. 🚗😭 Tiếc quá đi thôi! \n\nHay là thay vì sắm xe bốn bánh, anh Phú tham khảo thử chiếc tai nghe đỉnh cao **Airpods Max Apple (12.900.000đ)** với công nghệ chống ồn chủ động siêu đỉnh, mang lại không gian âm nhạc yên tĩnh và sang xịn mịn không khác gì đang ngồi trong cabin xe hơi hạng sang nha anh? 😉\n\nHoặc bên em đang có sẵn chiếc laptop **Dell Latitude 7400 (12.000.000đ)** ngoại hình đẹp 98%, cấu hình mượt mà và cực kỳ gọn nhẹ để anh tiện mang theo đi làm việc, đi công tác cực kỳ phong cách đó ạ. 💻\n\nAnh Phú thấy chiếc nào \"hợp gu\" với mình hơn để em tư vấn chi tiết và chốt đơn cho mình luôn nha anh? 🥰', 9999, 1, '2026-05-22 08:45:49', NULL, 'da_xem', '2026-05-22 01:45:49'),
(103, 'có xe máy không', 1, 9999, '2026-05-22 08:46:07', NULL, 'chua_xem', '2026-05-22 01:46:07'),
(104, 'Dạ có luôn anh Phú ơi! Đúng ngay tủ của bên em rồi ạ! 😍\n\nBên em đang có sẵn một chiếc **Xe máy điện Vespa màu Xám** cực kỳ lịch sự và bền bỉ. Xe ngoại hình còn rất là mới, động cơ chạy êm ru, vừa sang trọng lại vừa tiết kiệm chi phí đi lại cực tốt luôn anh nha.\n\n💰 Giá của em nó là **17.990.000đ** ạ.\n\nAnh Phú có muốn em gửi hình ảnh thực tế hoặc video cận cảnh của chiếc Vespa này để mình xem thử và chốt đơn luôn không anh? 🥰', 9999, 1, '2026-05-22 08:46:13', NULL, 'da_xem', '2026-05-22 01:46:13'),
(105, 'có máy tính không bạn', 2, 9999, '2026-06-02 20:50:21', NULL, 'chua_xem', '2026-06-02 13:50:21'),
(106, 'Dạ có luôn ạ! Bên em đang sẵn một bộ **Máy tính để bàn Cooler Master màu Đen** cực chất, hiệu năng mượt mà và hoạt động rất ổn định luôn đây ạ. 💻✨\n\nThông tin chi tiết của máy để mình tham khảo:\n- **Thiết kế & Tản nhiệt:** Tông màu đen cá tính, hệ thống tản nhiệt cực tốt giúp máy luôn hoạt động mát mẻ và bền bỉ.\n- **Tính năng:** Dễ dàng nâng cấp, tương thích tốt với nhiều loại linh kiện.\n- **Giá bán:** 18.000.000đ ạ.\n\nKhông biết nhu cầu chính của mình là mua máy để làm việc, chơi game hay làm đồ họa để em tư vấn chi tiết hơn cho mình cấu hình máy ạ? 😊', 9999, 2, '2026-06-02 20:50:27', NULL, 'da_xem', '2026-06-02 13:50:27'),
(107, 'có xe bmw không', 2, 9999, '2026-06-02 20:51:29', NULL, 'chua_xem', '2026-06-02 13:51:29'),
(108, 'Dạ có luôn ạ! Nhắc đến BMW là bên em đang sẵn ngay một \"siêu phẩm cá mập\" **BMW S1000RR đời 2018** cực kỳ hầm hố và chất chơi luôn đây ạ! 🏍️🔥\n\nThông tin chi tiết của em nó để mình tham khảo:\n- **Tình trạng:** Đăng ký lần đầu 2018, Odo 22.000 km (xe đẹp, biển Sài Gòn).\n- **Ngoại hình:** Phối màu xanh HP kết hợp gắp bạc nhìn là mê ngay. \n- **Đồ chơi sẵn có:** Xe đã lên sẵn rất nhiều đồ hiệu như Pô Austin Racing slip-on real (nổ cực uy lực), chống đổ T-Rex, bảo vệ lốc GB Racing, dàn ốp carbon và cặp vỏ Pirelli Rosso còn dày cui.\n- **Giá bán:** 499.000.000đ ạ.\n\nKhông biết mình đang tìm dòng xe phân khối lớn này hay đang có nhu cầu tìm dòng ô tô BMW ạ? Nếu là em \"cá mập\" này thì mình có muốn em gửi clip test tiếng pô hay hẹn lịch qua cửa hàng xem xe trực tiếp không ạ? 😊', 9999, 2, '2026-06-02 20:51:37', NULL, 'da_xem', '2026-06-02 13:51:37'),
(109, 'ô tô bmw', 2, 9999, '2026-06-02 20:51:56', NULL, 'chua_xem', '2026-06-02 13:51:56'),
(110, 'Dạ, hiện tại bên em lại chưa có sẵn dòng ô tô BMW rồi ạ, tiếc quá luôn! 🥺 Đợt này bên em chủ yếu đang sẵn dòng mô tô phân khối lớn BMW S1000RR và các sản phẩm công nghệ, đồ điện tử thôi ạ.\n\nLúc nãy mình có hỏi về máy tính, không biết mình có muốn tham khảo thêm bộ PC Cooler Master hoặc chiếc laptop Dell Latitude 7400 (nhỏ gọn, tiện di chuyển) bên em đang có sẵn không ạ? Để em gửi thông tin chi tiết tư vấn cho mình nhé! 😊', 9999, 2, '2026-06-02 20:52:03', NULL, 'da_xem', '2026-06-02 13:52:03'),
(111, 'bmw', 2, 9999, '2026-06-02 20:52:22', NULL, 'chua_xem', '2026-06-02 13:52:22'),
(112, 'Dạ em xin lỗi anh/chị nhiều ạ! Em vừa cập nhật lại chính xác với bộ phận kho thì bên em hiện đang sẵn 2 chiếc ô tô BMW cực đẹp, đúng ý mình luôn đây ạ! 🚗🔥\n\nEm gửi thông tin nhanh để mình tham khảo nhé:\n\n1. **BMW 320i (đời 2015) - Giá: 500.000.000đ**\n- Ngoại thất màu trắng sang trọng, xe nguyên bản cực kỳ giữ gìn.\n- Đã sở hữu khối động cơ mới B48 chạy cực bốc, mượt mà và tiết kiệm nhiên liệu.\n\n2. **BMW X5 (đời 2015) - Giá: 1.000.000.000đ**\n- Dòng SUV gầm cao, xe gia đình sử dụng kỹ, bảo dưỡng định kỳ chuẩn chỉ. \n- Ngoại thất bóng đẹp, nội thất da cao cấp. Xe chạy cực đầm, cách âm tuyệt vời. \n- Bên em cam kết xe nguyên bản, không đâm đụng/ngập nước (bao check hãng/gara toàn quốc).\n\nKhông biết mình đang nhắm tới chiếc sedan 320i thời thượng hay dòng SUV X5 rộng rãi cho gia đình ạ? Để em gửi hình ảnh chi tiết và video quay xe cho mình tham khảo nhé! 😊', 9999, 2, '2026-06-02 20:52:31', NULL, 'da_xem', '2026-06-02 13:52:31'),
(113, 'có xe tải không', 2, 9999, '2026-06-10 08:56:47', NULL, 'chua_xem', '2026-06-10 01:56:47'),
(114, 'Dạ, tiếc quá hiện tại bên em lại không có dòng xe tải rồi anh/chị ơi! 🥺 Đợt này mảng ô tô bên em chỉ chuyên dòng xe du lịch và xe gia đình thôi ạ (hiện đang sẵn 2 chiếc BMW 320i và BMW X5 cực đẹp đó ạ).\n\nKhông biết mình đang cần tìm xe tải để phục vụ công việc chở hàng hay sao ạ? Nếu mình có nhu cầu tham khảo thêm xe đi lại gia đình hoặc các sản phẩm công nghệ bên em thì nhắn em hỗ trợ mình ngay nhé! 😊', 9999, 2, '2026-06-10 08:56:53', NULL, 'da_xem', '2026-06-10 01:56:53'),
(115, 'còn xe đầu kéo', 2, 9999, '2026-06-10 08:57:37', NULL, 'chua_xem', '2026-06-10 01:57:37'),
(116, 'Dạ tiếc quá, bên em lại không có dòng xe đầu kéo rồi anh/chị ơi! 🥺 Hiện tại mảng xe bên em chỉ chuyên dòng xe du lịch và xe gia đình thôi ạ. \n\nKhông biết ngoài xe phục vụ công việc vận tải ra, mình có đang quan tâm đến chiếc BMW nào để đi lại gia đình, hay có nhu cầu sắm đồ công nghệ, loa đài giải trí không để em hỗ trợ tư vấn cho mình nhé ạ? 😊', 9999, 2, '2026-06-10 08:57:43', NULL, 'da_xem', '2026-06-10 01:57:43'),
(117, 'Đầu kéo', 2, 9999, '2026-06-10 08:58:09', NULL, 'chua_xem', '2026-06-10 01:58:09'),
(118, 'Dạ em xin lỗi anh/chị nhiều ạ! 🥺 Em vừa kiểm tra lại gấp với bên kho thì may quá, bên em vừa cập nhật lại danh sách xe và đang có sẵn đúng một chiếc đầu kéo cực chất để phục vụ anh/chị đây ạ:\n\n🚚 **Đầu kéo HOWO 420 (Đời 2021)**\n* **Tình trạng:** Xe nguyên zin, cầu dầu, máy 420 chạy cực bốc và khỏe.\n* **Pháp lý:** Hồ sơ đã rút sẵn, sẵn sàng sang tên bấm biển ngay.\n* **Giá cực tốt:** 700.000.000đ.\n\nAnh/chị có tiện kết bạn Zalo để em gửi hình ảnh và video thực tế của xe qua cho mình tham khảo ngay không ạ? 😊', 9999, 2, '2026-06-10 08:58:17', NULL, 'da_xem', '2026-06-10 01:58:17'),
(119, 'có đồng hồ không', 2, 9999, '2026-06-10 09:05:35', NULL, 'chua_xem', '2026-06-10 02:05:35'),
(120, 'Dạ có luôn anh/chị ơi! Bên em đang sẵn vài mẫu đồng hồ hàng tuyển cực chất, từ đeo tay Thụy Sỹ đến đồng hồ cổ để bàn luôn ạ:\n\n⌚ **Đồng hồ đeo tay (Chuẩn Thụy Sỹ):**\n* **Rado Golden Horse Nâu (Cơ Automatic):** Size 35 cổ điển, 30 Jewels Thụy Sỹ, zin nguyên củ. Lên tay cực kỳ sang trọng. Giá: **3.200.000đ**.\n* **Maurice Lacroix Pontos (PT6178):** Dòng cao cấp, máy cơ ML112 (trữ cót 46h), kính sapphire nguyên khối chống xước, kháng nước 50m. Giá: **25.000.000đ**.\n\n🕰️ **Đồng hồ để bàn:**\n* **Jaeger Vàng (Pháp - Thập niên 80):** Thiết kế như một khối vàng rực rỡ (Cao 45cm, Ngang 25cm), máy điện tử chạy pin đại cực bền, thích hợp trưng bày phòng khách hoặc bàn làm việc. Giá: **1.500.000đ**.\n\nKhông biết anh/chị đang quan tâm đến dòng đeo tay lịch lãm hay mẫu để bàn độc lạ để em gửi hình ảnh và video thực tế qua Zalo cho mình xem thử nhé ạ? 😊', 9999, 2, '2026-06-10 09:06:24', NULL, 'da_xem', '2026-06-10 02:06:24'),
(121, 'đeo tay á bạn', 2, 9999, '2026-06-10 09:08:09', NULL, 'chua_xem', '2026-06-10 02:08:09'),
(122, 'Dạ em tiếc quá anh ơi! 🥺 Hai mẫu đồng hồ đeo tay Rado và Maurice Lacroix em vừa giới thiệu bên trên vừa có khách nhanh tay chốt mất tiêu rồi ạ. Hiện tại kho bên em đang tạm hết dòng đồng hồ đeo tay mất rồi. \n\nNếu anh thích các món đồ công nghệ đeo thời thượng và đẳng cấp, bên em đang có sẵn chiếc tai nghe chụp tai **Airpods Max Apple** (màu cực sang, giá 12.900.000đ) đang rất hot đó ạ. \n\nHay là anh cứ kết bạn Zalo với em nhé, để đợt tới có mẫu đồng hồ đeo tay nào hàng tuyển về thêm là em chủ động gửi hình qua cho anh tham khảo liền được không ạ? 😊', 9999, 2, '2026-06-10 09:08:19', NULL, 'da_xem', '2026-06-10 02:08:19'),
(123, 'có ôtô không', 2, 9999, '2026-06-10 10:16:21', NULL, 'chua_xem', '2026-06-10 03:16:21'),
(124, 'Lỗi API: This model is currently experiencing high demand. Spikes in demand are usually temporary. Please try again later.', 9999, 2, '2026-06-10 10:16:23', NULL, 'da_xem', '2026-06-10 03:16:23'),
(125, 'có xe máy không', 2, 9999, '2026-06-10 10:18:16', NULL, 'chua_xem', '2026-06-10 03:18:16'),
(126, 'Dạ AI đang bận xử lý nhiều yêu cầu, anh/chị vui lòng thử lại sau ít phút ạ 😊', 9999, 2, '2026-06-10 10:18:22', NULL, 'da_xem', '2026-06-10 03:18:22'),
(127, 'có xe tải không', 2, 9999, '2026-06-10 10:34:43', NULL, 'chua_xem', '2026-06-10 03:34:43'),
(128, 'Dạ hiện tại bên shop em chưa có xe tải rồi ạ. Hiện bên em đang chuyên các dòng sản phẩm công nghệ như điện thoại, laptop, iPad và máy ảnh chính hãng giá cực tốt ạ. \n\nTiêu biểu có em **Vivo X200 5G (12/256GB)** cấu hình siêu mạnh mẽ, pin silicon 5800mah cực trâu và có sạc nhanh 90W rất tiện lợi khi di chuyển, giá chỉ 20.000.000đ thôi ạ.\n\nAnh/chị có muốn tham khảo thêm chi tiết về chiếc Vivo này hay dòng máy nào khác bên em không ạ? 😊', 9999, 2, '2026-06-10 10:34:52', NULL, 'da_xem', '2026-06-10 03:34:52'),
(129, 'xe ô tô có không', 2, 9999, '2026-06-10 10:35:11', NULL, 'chua_xem', '2026-06-10 03:35:11'),
(130, 'Dạ, hiện tại bên em chưa có xe ô tô rồi ạ. Bên em vẫn chuyên các dòng sản phẩm công nghệ chính hãng giá tốt thôi nè. 😊\n\nTiêu biểu hôm nay bên em đang sẵn chiếc **iPad Pro M1** cấu hình siêu mạnh (máy mua được 2 năm) với giá cực kỳ \"hạt dẻ\" chỉ **7.000.000đ** thôi ạ. Dòng này anh/chị mang theo để giải trí, xem bản đồ hay làm việc di động là vô cùng tiện lợi luôn!\n\nAnh/chị có muốn em gửi thêm hình ảnh thực tế để chốt em iPad Pro M1 này không ạ? 🥰', 9999, 2, '2026-06-10 10:35:19', NULL, 'da_xem', '2026-06-10 03:35:19'),
(131, 'có ô tô không', 1, 9999, '2026-06-10 15:13:47', NULL, 'chua_xem', '2026-06-10 08:13:47'),
(132, 'Dạ AI đang bận xử lý nhiều yêu cầu, anh/chị vui lòng thử lại sau ít phút ạ 😊', 9999, 1, '2026-06-10 15:14:13', NULL, 'da_xem', '2026-06-10 08:14:13'),
(133, 'có ô tô không', 1, 9999, '2026-06-10 15:14:13', NULL, 'chua_xem', '2026-06-10 08:14:13'),
(134, 'Dạ AI đang bận xử lý nhiều yêu cầu, anh/chị vui lòng thử lại sau ít phút ạ 😊', 9999, 1, '2026-06-10 15:14:15', NULL, 'da_xem', '2026-06-10 08:14:15'),
(135, 'có ô tô không', 1, 9999, '2026-06-10 15:14:15', NULL, 'chua_xem', '2026-06-10 08:14:15'),
(136, 'Dạ AI đang bận xử lý nhiều yêu cầu, anh/chị vui lòng thử lại sau ít phút ạ 😊', 9999, 1, '2026-06-10 15:14:16', NULL, 'da_xem', '2026-06-10 08:14:16'),
(137, 'có ô tô không', 1, 9999, '2026-06-10 15:14:16', NULL, 'chua_xem', '2026-06-10 08:14:16'),
(138, 'Dạ AI đang bận xử lý nhiều yêu cầu, anh/chị vui lòng thử lại sau ít phút ạ 😊', 9999, 1, '2026-06-10 15:14:18', NULL, 'da_xem', '2026-06-10 08:14:18'),
(139, 'có ô tô không', 1, 9999, '2026-06-10 15:14:18', NULL, 'chua_xem', '2026-06-10 08:14:18'),
(140, 'Dạ AI đang bận xử lý nhiều yêu cầu, anh/chị vui lòng thử lại sau ít phút ạ 😊', 9999, 1, '2026-06-10 15:14:19', NULL, 'da_xem', '2026-06-10 08:14:19'),
(141, 'có ô tô không', 1, 9999, '2026-06-10 15:14:19', NULL, 'chua_xem', '2026-06-10 08:14:19'),
(142, 'Dạ AI đang bận xử lý nhiều yêu cầu, anh/chị vui lòng thử lại sau ít phút ạ 😊', 9999, 1, '2026-06-10 15:14:20', NULL, 'da_xem', '2026-06-10 08:14:20'),
(143, 'có ô tô không', 1, 9999, '2026-06-10 15:14:20', NULL, 'chua_xem', '2026-06-10 08:14:20'),
(144, 'Dạ AI đang bận xử lý nhiều yêu cầu, anh/chị vui lòng thử lại sau ít phút ạ 😊', 9999, 1, '2026-06-10 15:14:20', NULL, 'da_xem', '2026-06-10 08:14:20'),
(145, 'có ô tô không', 1, 9999, '2026-06-10 15:14:20', NULL, 'chua_xem', '2026-06-10 08:14:20'),
(146, 'Dạ AI đang bận xử lý nhiều yêu cầu, anh/chị vui lòng thử lại sau ít phút ạ 😊', 9999, 1, '2026-06-10 15:14:20', NULL, 'da_xem', '2026-06-10 08:14:20'),
(147, 'có ô tô không', 1, 9999, '2026-06-10 15:14:20', NULL, 'chua_xem', '2026-06-10 08:14:20'),
(148, 'Dạ AI đang bận xử lý nhiều yêu cầu, anh/chị vui lòng thử lại sau ít phút ạ 😊', 9999, 1, '2026-06-10 15:14:20', NULL, 'da_xem', '2026-06-10 08:14:20'),
(149, 'có ô tô không', 1, 9999, '2026-06-10 15:14:21', NULL, 'chua_xem', '2026-06-10 08:14:21'),
(150, 'Dạ AI đang bận xử lý nhiều yêu cầu, anh/chị vui lòng thử lại sau ít phút ạ 😊', 9999, 1, '2026-06-10 15:14:21', NULL, 'da_xem', '2026-06-10 08:14:21'),
(151, 'có ô tô không', 1, 9999, '2026-06-10 15:14:21', NULL, 'chua_xem', '2026-06-10 08:14:21'),
(152, 'Dạ AI đang bận xử lý nhiều yêu cầu, anh/chị vui lòng thử lại sau ít phút ạ 😊', 9999, 1, '2026-06-10 15:14:21', NULL, 'da_xem', '2026-06-10 08:14:21'),
(153, 'có ô tô không', 1, 9999, '2026-06-10 15:14:21', NULL, 'chua_xem', '2026-06-10 08:14:21'),
(154, 'Dạ AI đang bận xử lý nhiều yêu cầu, anh/chị vui lòng thử lại sau ít phút ạ 😊', 9999, 1, '2026-06-10 15:14:21', NULL, 'da_xem', '2026-06-10 08:14:21'),
(155, 'có ô tô không', 1, 9999, '2026-06-10 15:14:21', NULL, 'chua_xem', '2026-06-10 08:14:21'),
(156, 'Dạ AI đang bận xử lý nhiều yêu cầu, anh/chị vui lòng thử lại sau ít phút ạ 😊', 9999, 1, '2026-06-10 15:14:21', NULL, 'da_xem', '2026-06-10 08:14:21'),
(157, 'có ô tô không', 1, 9999, '2026-06-10 15:14:21', NULL, 'chua_xem', '2026-06-10 08:14:21'),
(158, 'Dạ AI đang bận xử lý nhiều yêu cầu, anh/chị vui lòng thử lại sau ít phút ạ 😊', 9999, 1, '2026-06-10 15:14:22', NULL, 'da_xem', '2026-06-10 08:14:22'),
(159, 'có ô tô không', 1, 9999, '2026-06-10 15:14:22', NULL, 'chua_xem', '2026-06-10 08:14:22'),
(160, 'Dạ AI đang bận xử lý nhiều yêu cầu, anh/chị vui lòng thử lại sau ít phút ạ 😊', 9999, 1, '2026-06-10 15:14:22', NULL, 'da_xem', '2026-06-10 08:14:22'),
(161, 'có ô tô không', 1, 9999, '2026-06-10 15:14:22', NULL, 'chua_xem', '2026-06-10 08:14:22'),
(162, 'Dạ AI đang bận xử lý nhiều yêu cầu, anh/chị vui lòng thử lại sau ít phút ạ 😊', 9999, 1, '2026-06-10 15:14:22', NULL, 'da_xem', '2026-06-10 08:14:22'),
(163, 'có ô tô không', 1, 9999, '2026-06-10 15:14:22', NULL, 'chua_xem', '2026-06-10 08:14:22'),
(164, 'Dạ AI đang bận xử lý nhiều yêu cầu, anh/chị vui lòng thử lại sau ít phút ạ 😊', 9999, 1, '2026-06-10 15:14:22', NULL, 'da_xem', '2026-06-10 08:14:22'),
(165, 'có ô tô không', 1, 9999, '2026-06-10 15:14:22', NULL, 'chua_xem', '2026-06-10 08:14:22'),
(166, 'Dạ AI đang bận xử lý nhiều yêu cầu, anh/chị vui lòng thử lại sau ít phút ạ 😊', 9999, 1, '2026-06-10 15:14:22', NULL, 'da_xem', '2026-06-10 08:14:22'),
(167, 'có ô tô không', 1, 9999, '2026-06-10 15:14:22', NULL, 'chua_xem', '2026-06-10 08:14:22'),
(168, 'Dạ AI đang bận xử lý nhiều yêu cầu, anh/chị vui lòng thử lại sau ít phút ạ 😊', 9999, 1, '2026-06-10 15:14:23', NULL, 'da_xem', '2026-06-10 08:14:23'),
(169, 'ol', 1, 7, '2026-06-10 15:14:28', NULL, 'chua_xem', '2026-06-10 08:14:28'),
(170, 'có máy ảnh không', 1, 9999, '2026-06-10 15:18:00', NULL, 'chua_xem', '2026-06-10 08:18:00'),
(171, 'Dạ AI đang bận xử lý nhiều yêu cầu, anh/chị vui lòng thử lại sau ít phút ạ 😊', 9999, 1, '2026-06-10 15:18:15', NULL, 'da_xem', '2026-06-10 08:18:15'),
(172, 'chào ai', 1, 9999, '2026-06-10 15:21:05', NULL, 'chua_xem', '2026-06-10 08:21:05'),
(173, 'Dạ AI đang bận xử lý nhiều yêu cầu, anh/chị vui lòng thử lại sau ít phút ạ 😊', 9999, 1, '2026-06-10 15:21:14', NULL, 'da_xem', '2026-06-10 08:21:14'),
(174, '📄 Hóa đơn #102', 2, 20, '2026-06-10 15:26:51', NULL, 'chua_xem', '2026-06-10 08:26:51'),
(175, 'có xe đạp không', 1, 9999, '2026-06-10 15:28:43', NULL, 'chua_xem', '2026-06-10 08:28:43'),
(176, 'Dạ AI đang bận xử lý nhiều yêu cầu, anh/chị vui lòng thử lại sau ít phút ạ 😊', 9999, 1, '2026-06-10 15:28:45', NULL, 'da_xem', '2026-06-10 08:28:45'),
(177, 'có ô tô không', 1, 9999, '2026-06-10 15:31:20', NULL, 'chua_xem', '2026-06-10 08:31:20'),
(178, '{\"code\":503,\"message\":\"This model is currently experiencing high demand. Spikes in demand are usually temporary. Please try again later.\",\"status\":\"UNAVAILABLE\"}', 9999, 1, '2026-06-10 15:31:27', NULL, 'da_xem', '2026-06-10 08:31:27'),
(179, 'có xe máy không', 1, 9999, '2026-06-10 15:33:19', NULL, 'chua_xem', '2026-06-10 08:33:19'),
(180, 'Dạ AI đang bận xử lý nhiều yêu cầu, anh/chị vui lòng thử lại sau ít phút ạ 😊', 9999, 1, '2026-06-10 15:33:19', NULL, 'da_xem', '2026-06-10 08:33:19'),
(181, 'có máy tính không', 1, 9999, '2026-06-10 15:37:48', NULL, 'chua_xem', '2026-06-10 08:37:48'),
(182, 'Dạ AI đang bận xử lý nhiều yêu cầu, anh/chị vui lòng thử lại sau ít phút ạ 😊', 9999, 1, '2026-06-10 15:38:15', NULL, 'da_xem', '2026-06-10 08:38:15'),
(183, '📄 Hóa đơn #103', 2, 10000, '2026-06-10 15:43:41', NULL, 'da_xem', '2026-06-10 08:43:41'),
(184, 'chào ai', 10000, 9999, '2026-06-10 15:44:27', NULL, 'chua_xem', '2026-06-10 08:44:27'),
(185, 'Dạ AI đang bận xử lý nhiều yêu cầu, anh/chị vui lòng thử lại sau ít phút ạ 😊', 9999, 10000, '2026-06-10 15:44:29', NULL, 'da_xem', '2026-06-10 08:44:29'),
(186, 'có ô tô không', 10000, 9999, '2026-06-10 15:47:10', NULL, 'chua_xem', '2026-06-10 08:47:10'),
(187, 'Lỗi API: You exceeded your current quota, please check your plan and billing details. For more information on this error, head to: https://ai.google.dev/gemini-api/docs/rate-limits. To monitor your current usage, head to: https://ai.dev/rate-limit. \n* Quota exceeded for metric: generativelanguage.googleapis.com/generate_content_free_tier_input_token_count, limit: 0, model: gemini-2.0-flash\n* Quota exceeded for metric: generativelanguage.googleapis.com/generate_content_free_tier_requests, limit: 0, model: gemini-2.0-flash\n* Quota exceeded for metric: generativelanguage.googleapis.com/generate_content_free_tier_requests, limit: 0, model: gemini-2.0-flash\nPlease retry in 48.972901938s.', 9999, 10000, '2026-06-10 15:47:10', NULL, 'da_xem', '2026-06-10 08:47:10'),
(188, 'có xe máy không', 10000, 9999, '2026-06-10 15:52:09', NULL, 'chua_xem', '2026-06-10 08:52:09'),
(189, 'Lỗi API: You exceeded your current quota, please check your plan and billing details. For more information on this error, head to: https://ai.google.dev/gemini-api/docs/rate-limits. To monitor your current usage, head to: https://ai.dev/rate-limit. \n* Quota exceeded for metric: generativelanguage.googleapis.com/generate_content_free_tier_input_token_count, limit: 0, model: gemini-2.0-flash\n* Quota exceeded for metric: generativelanguage.googleapis.com/generate_content_free_tier_requests, limit: 0, model: gemini-2.0-flash\n* Quota exceeded for metric: generativelanguage.googleapis.com/generate_content_free_tier_requests, limit: 0, model: gemini-2.0-flash\nPlease retry in 50.02199466s.', 9999, 10000, '2026-06-10 15:52:09', NULL, 'da_xem', '2026-06-10 08:52:09'),
(190, 'n', 10000, 9999, '2026-06-10 15:52:16', NULL, 'chua_xem', '2026-06-10 08:52:16'),
(191, 'Lỗi API: You exceeded your current quota, please check your plan and billing details. For more information on this error, head to: https://ai.google.dev/gemini-api/docs/rate-limits. To monitor your current usage, head to: https://ai.dev/rate-limit. \n* Quota exceeded for metric: generativelanguage.googleapis.com/generate_content_free_tier_input_token_count, limit: 0, model: gemini-2.0-flash\n* Quota exceeded for metric: generativelanguage.googleapis.com/generate_content_free_tier_requests, limit: 0, model: gemini-2.0-flash\n* Quota exceeded for metric: generativelanguage.googleapis.com/generate_content_free_tier_requests, limit: 0, model: gemini-2.0-flash\nPlease retry in 43.611262359s.', 9999, 10000, '2026-06-10 15:52:16', NULL, 'da_xem', '2026-06-10 08:52:16'),
(192, 'có xe đạp k', 10000, 9999, '2026-06-10 15:52:30', NULL, 'chua_xem', '2026-06-10 08:52:30'),
(193, 'Lỗi API: You exceeded your current quota, please check your plan and billing details. For more information on this error, head to: https://ai.google.dev/gemini-api/docs/rate-limits. To monitor your current usage, head to: https://ai.dev/rate-limit. \n* Quota exceeded for metric: generativelanguage.googleapis.com/generate_content_free_tier_input_token_count, limit: 0, model: gemini-2.0-flash\n* Quota exceeded for metric: generativelanguage.googleapis.com/generate_content_free_tier_requests, limit: 0, model: gemini-2.0-flash\n* Quota exceeded for metric: generativelanguage.googleapis.com/generate_content_free_tier_requests, limit: 0, model: gemini-2.0-flash\nPlease retry in 28.78496796s.', 9999, 10000, '2026-06-10 15:52:31', NULL, 'da_xem', '2026-06-10 08:52:31'),
(194, 'có ô tô không', 10000, 9999, '2026-06-10 16:17:48', NULL, 'chua_xem', '2026-06-10 09:17:48'),
(195, 'Lỗi API: You exceeded your current quota, please check your plan and billing details. For more information on this error, head to: https://ai.google.dev/gemini-api/docs/rate-limits. To monitor your current usage, head to: https://ai.dev/rate-limit. \n* Quota exceeded for metric: generativelanguage.googleapis.com/generate_content_free_tier_requests, limit: 0, model: gemini-2.0-flash\n* Quota exceeded for metric: generativelanguage.googleapis.com/generate_content_free_tier_requests, limit: 0, model: gemini-2.0-flash\n* Quota exceeded for metric: generativelanguage.googleapis.com/generate_content_free_tier_input_token_count, limit: 0, model: gemini-2.0-flash\nPlease retry in 10.741231268s.', 9999, 10000, '2026-06-10 16:17:49', NULL, 'da_xem', '2026-06-10 09:17:49'),
(196, '📄 Hóa đơn #103', 2, 10000, '2026-06-10 18:03:15', NULL, 'da_xem', '2026-06-10 11:03:15'),
(197, '📄 Hóa đơn #103', 2, 10000, '2026-06-13 16:00:06', NULL, 'da_xem', '2026-06-13 09:00:06'),
(198, '📄 Hóa đơn #103', 2, 10000, '2026-06-13 16:03:11', NULL, 'da_xem', '2026-06-13 09:03:11'),
(199, '📄 Hóa đơn #103', 2, 10000, '2026-06-13 16:09:45', NULL, 'da_xem', '2026-06-13 09:09:45'),
(200, '📄 Hóa đơn #103', 2, 10000, '2026-06-13 16:18:33', NULL, 'da_xem', '2026-06-13 09:18:33'),
(201, '📄 Hóa đơn #103', 2, 10000, '2026-06-13 16:29:23', NULL, 'da_xem', '2026-06-13 09:29:23'),
(202, '📄 Hóa đơn #103', 2, 10000, '2026-06-13 16:46:29', NULL, 'da_xem', '2026-06-13 09:46:29'),
(203, 'bmw giá có giảm đc tí nữa không ạ', 10006, 10005, '2026-07-14 21:16:15', NULL, 'da_xem', '2026-07-14 14:16:15'),
(204, 'Chung tôi không có bán bmw ạ, chúng tôi chỉ có ZX25r thoi ạ', 10005, 10006, '2026-07-14 21:17:12', NULL, 'da_xem', '2026-07-14 14:17:12'),
(205, 'À thế giá có bớt đc tí tẹo nào nữa không vậy shop ơi', 10006, 10005, '2026-07-14 21:17:37', NULL, 'da_xem', '2026-07-14 14:17:37'),
(206, 'À nếu bn gần tôi có thể sẽ xem xét ạ', 10005, 10006, '2026-07-14 21:18:08', NULL, 'da_xem', '2026-07-14 14:18:08'),
(207, 'à ok shop hiện tại shop còn đc mấy con ạ', 10006, 10005, '2026-07-14 21:18:29', NULL, 'da_xem', '2026-07-14 14:18:29'),
(208, 'À shop còn đc 2 con ạ', 10005, 10006, '2026-07-14 21:18:38', NULL, 'da_xem', '2026-07-14 14:18:38'),
(209, 'Thế chốt tôi 2 con luôn nhá', 10006, 10005, '2026-07-14 21:18:48', NULL, 'da_xem', '2026-07-14 14:18:48'),
(210, 'ok bn thế tôi sẽ chừa lại với giá tốt nhất cho bạn ạ', 10005, 10006, '2026-07-14 21:19:15', NULL, 'da_xem', '2026-07-14 14:19:15'),
(211, 'Cảm ơn bạn đã tin tưởng chúng tôi ạ', 10005, 10006, '2026-07-14 21:19:26', NULL, 'da_xem', '2026-07-14 14:19:26'),
(212, '📄 Hóa đơn #103', 2, 10000, '2026-08-02 13:36:14', NULL, 'da_xem', '2026-08-02 06:36:14'),
(213, 'có xe ôtô không bạn', 10000, 9999, '2026-08-02 13:46:33', NULL, 'chua_xem', '2026-08-02 06:46:33'),
(214, 'Chào anh/chị, em rất vui được hỗ trợ ạ!\n\nVề dòng xe ô tô, cửa hàng em đang có một số lựa chọn rất hấp dẫn, từ xe sedan, SUV hạng sang đến xe điện đời mới, và cả xe tải phục vụ công việc. Anh/chị tham khảo nhé:\n\n1.  **VinFast VF8 2025** - Một lựa chọn hiện đại và tiết kiệm!\n    *   Giá chỉ: **850.000.000đ**\n    *   Chiếc VF8 Plus này đẹp như mới, Odo chỉ 15.000 km, còn gói thuê pin cố định không giới hạn km. Đặc biệt là còn bảo hành chính hãng đến 10 năm, anh/chị hoàn toàn yên tâm sử dụng lâu dài ạ!\n    *   Link: http://localhost/bandocu1/product.php?id=49\n\n2.  **Mercedes C200 đời 2020** - Sang trọng, đẳng cấp và vận hành mượt mà!\n    *   Giá cực tốt: **1.400.000.000đ**\n    *   Xe gia đình đi kỹ, bảo dưỡng đầy đủ, nội ngoại thất còn rất mới. Máy êm, lái cực bốc, cách âm hoàn hảo đúng chất Mer. Cam kết không đâm đụng, không ngập nước, bao check hãng toàn quốc ạ.\n    *   Link: http://localhost/bandocu1/product.php?id=50\n\n3.  **BMW X5 2015** - Mạnh mẽ, đầm chắc và vẫn rất thời thượng!\n    *   Giá: **1.000.000.000đ**\n    *   Chiếc SUV này được gia đình em sử dụng kỹ, bảo dưỡng định kỳ. Nội thất ghế da cao cấp, âm thanh hay, điều hòa mát sâu. Xe nguyên bản, cam kết không đâm đụng, ngập nước, bao check thoải mái luôn ạ.\n    *   Link: http://localhost/bandocu1/product.php?id=61\n\n4.  **Ford Laser 2003** - Bền bỉ, đầy đủ tiện nghi, giá cực mềm!\n    *   Giá siêu hấp dẫn: **4.999.000đ**\n    *   Xe đã được bảo dưỡng định kỳ, mua về là chạy ngay. Đồ chơi thì không thiếu gì: màn Android 4G, camera hành trình, camera lùi nét căng. Lốp mới, đủ 2 chìa khóa. Sedan rộng rãi, đi cực thích anh/chị nhé!\n    *   Link: http://localhost/bandocu1/product.php?id=88\n\n5.  **Volvo XC90 T6 Inscription đời 2021** - An toàn đỉnh cao, full option!\n    *   Giá: **999.000.000đ**\n    *   Chiếc xe đẹp xuất sắc, sơn zin cực nhiều, full lịch sử bảo dưỡng hãng. Nổi bật với loa Bowers & Wilkins, ghế Massage/Làm mát, Camera 360, phanh khoảng cách, treo khí nén cực êm. Cam kết không đâm đụng, ngập nước, bao check hãng toàn quốc. Odo 60.000km ạ.\n    *   Link: http://localhost/bandocu1/product.php?id=89\n\n6.  **Xe điện VinFast VF5 Plus 2025 Xám** - Lựa chọn nhỏ gọn, linh hoạt cho đô thị!\n    *   Giá: **400.000.000đ**\n    *   Phiên bản VF5 Plus 2025 màu xám xi măng cá tính, Odo 33.000 km, xe còn bảo hiểm thân vỏ đầy đủ. Rất phù hợp cho anh/chị nào thích xe nhỏ gọn, tiết kiệm và thân thiện môi trường.\n    *   Link: http://localhost/bandocu1/product.php?id=91\n\n7.  **KIA K2700 SX 2014** - Xe tải nhỏ phục vụ công việc, chất lượng cao!\n    *   Giá: **179.000.000đ**\n    *   Xe đẹp, có màn hình, cam hành trình, cam lùi đầy đủ. Thùng sàn inox, lốp đẹp, khám phí dài, điều hòa mát lạnh. Rất thích hợp cho anh/chị nào cần xe chở hàng mà vẫn tiện nghi ạ.\n    *   Link: http://localhost/bandocu1/product.php?id=93\n\n8.  **Đầu kéo HOWO 420 đời 2021** - Đối tác tin cậy cho vận tải chuyên nghiệp!\n    *   Giá: **700.000.000đ**\n    *   Chiếc đầu kéo HOWO đời 2021, cầu dầu, máy 420. Xe nguyên zin, hồ sơ rút sẵn, mua về là có thể lăn bánh ngay để phục vụ công việc vận chuyển hàng hóa ạ.\n    *   Link: http://localhost/bandocu1/product.php?id=94\n\nAnh/chị quan tâm đến mẫu xe nào hoặc cần thêm thông tin chi tiết, đừng ngần ngại cho em biết nhé. Em luôn sẵn lòng hỗ trợ ạ!', 9999, 10000, '2026-08-02 13:46:42', NULL, 'da_xem', '2026-08-02 06:46:42'),
(215, 'Đây là file hóa đơn mua hàng của em hả shop', 10000, 2, '2026-08-07 16:50:48', NULL, 'da_xem', '2026-08-07 09:50:48'),
(216, 'đúng rồi bạn', 2, 10000, '2026-08-07 16:51:38', NULL, 'da_xem', '2026-08-07 09:51:38'),
(217, '📄 Hóa đơn #119', 2, 10000, '2026-08-08 14:56:52', NULL, 'da_xem', '2026-08-08 07:56:52'),
(218, 'Báo cáo liên quan đến tài khoản của bạn đang được Admin xem xét.', 9999, 2, '2026-09-03 21:52:07', NULL, 'da_xem', '2026-09-03 14:52:07'),
(219, 'Báo cáo của bạn đang được Admin xem xét.', 9999, 1, '2026-09-03 21:52:07', NULL, 'da_xem', '2026-09-03 14:52:07'),
(220, 'Báo cáo liên quan đến tài khoản của bạn đã được Admin xác minh.', 9999, 2, '2026-09-03 21:53:22', NULL, 'da_xem', '2026-09-03 14:53:22'),
(221, 'Báo cáo của bạn đã được Admin xác minh.', 9999, 1, '2026-09-03 21:53:22', NULL, 'da_xem', '2026-09-03 14:53:22'),
(222, 'Báo cáo liên quan đến tài khoản của bạn đã được Admin từ chối.', 9999, 2, '2026-09-03 22:30:48', NULL, 'da_xem', '2026-09-03 15:30:48'),
(223, 'Báo cáo của bạn đã được Admin từ chối.', 9999, 1, '2026-09-03 22:30:48', NULL, 'da_xem', '2026-09-03 15:30:48'),
(224, 'Báo cáo liên quan đến tài khoản của bạn đã được Admin xác minh.', 9999, 2, '2026-09-03 22:32:27', NULL, 'da_xem', '2026-09-03 15:32:27'),
(225, 'Báo cáo của bạn đã được Admin xác minh.', 9999, 1, '2026-09-03 22:32:27', NULL, 'da_xem', '2026-09-03 15:32:27'),
(226, 'Báo cáo liên quan đến tài khoản của bạn đã được Admin xác minh.', 9999, 2, '2026-09-03 22:34:08', NULL, 'chua_xem', '2026-09-03 15:34:08'),
(227, 'Báo cáo của bạn đã được Admin xác minh.', 9999, 1, '2026-09-03 22:34:08', NULL, 'da_xem', '2026-09-03 15:34:08'),
(228, 'Báo cáo liên quan đến tài khoản của bạn đang được Admin xem xét.', 9999, 14, '2026-09-04 21:27:41', NULL, 'da_xem', '2026-09-04 14:27:41'),
(229, 'Báo cáo của bạn đang được Admin xem xét.', 9999, 9, '2026-09-04 21:27:41', NULL, 'da_xem', '2026-09-04 14:27:41');

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
  `tuChoiSuKien` tinyint(1) DEFAULT '1',
  `luotXem` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`maSanPham`),
  KEY `fk_sanpham_nguoiBan` (`maNguoiBan`),
  KEY `fk_sanpham_danhmuc` (`maDanhMuc`)
) ENGINE=InnoDB AUTO_INCREMENT=114 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sanpham`
--

INSERT INTO `sanpham` (`maSanPham`, `tenSanPham`, `moTa`, `gia`, `soLuong`, `tinhTrang`, `video`, `hinhAnh`, `maNguoiBan`, `hinhAnh1`, `hinhAnh2`, `hinhAnh3`, `hinhanh4`, `hinhanh5`, `hinhanh6`, `maDanhMuc`, `trangThai`, `duyetTrangThai`, `giamGia`, `tuChoiSuKien`, `luotXem`) VALUES
(1, 'Airpods Max Apple', 'Airport', 12900000.00, 1, 'Còn hàng', NULL, '1761191772_Airpods_Max_Apple.png', 2, '1761652958_1_23_1.jpg', '1761652958_2_23_2.jpg', '1761652958_3_23_3.jpg', NULL, NULL, NULL, 4, 1, 1, 0, 1, 2),
(2, 'Cặp củ loa dài rộng 18CM', 'Loa rộng 18cm cũ', 245000.00, 3, 'Còn hàng', NULL, '1761191817_C___p_c____loa_d__i_r___ng_18CM.png', 2, '1761654123_1_21_1.jpg', '1761654123_2_21_2.jpg', '1761654123_3_21_3.jpg', '0', NULL, NULL, 4, 1, 1, 0, 1, 2),
(4, 'Dàn karaoke MK Acoustic', 'Dàn karaoke MK Acoustic nghe ổn', 26000000.00, 3, 'Còn hàng', NULL, '1761191894_D__n_karaoke_MK_Acoustic.png', 2, '1761653947_1_19_1.jpg', '1761653947_2_19_2.jpg', '1761653947_3_19_3.jpg', '0', NULL, NULL, 3, 1, 1, 0, 1, 0),
(5, 'Dell Latitude 7400', 'Máy dùng 98%', 12000000.00, 4, 'Còn hàng', NULL, '1761191919_Dell_Latitude_7400.png', 2, '1761653922_1_18_1.jpg', '1761653922_2_18_2.jpg', '1761653922_3_18_3.jpg', '0', NULL, NULL, 5, 1, 1, 0, 1, 2),
(6, 'Imac 2015 i5', 'Dùng 99% không lỗi', 12500000.00, 3, 'Còn hàng', NULL, '1761191961_Imac_2015_i5.png', 2, '1761653894_1_17_1.jpg', '1761653894_2_17_2.jpg', '1761653894_3_17_3.jpg', '0', NULL, NULL, 6, 1, 1, 0, 1, 0),
(7, 'Ipad pro M1', 'Máy mua được 2 năm ai cần nhắn em nha', 7000000.00, 3, 'Còn hàng', NULL, '1761192015_Ipad_pro_M1.png', 2, '1761653863_1_16_1.jpg', '1761653863_2_16_2.jpg', NULL, '0', NULL, NULL, 6, 1, 1, 0, 1, 0),
(9, 'Laptop Gaming Acer Intro', 'Lap sài được 5 năm nhưng giờ không có nhu cầu dùng nữa ai cần inbox em ạ!', 8000000.00, 2, 'Còn hàng', NULL, '1761192113_Laptop_Gaming_Acer_Intro.png', 2, '1761653748_1_12_1.jpg', '1761653748_2_12_2.jpg', NULL, '0', NULL, NULL, 5, 1, 1, 0, 1, 0),
(10, 'Macbook pro 16', 'Máy zin chính hãng', 9050000.00, 3, 'Còn hàng', NULL, '1761192170_Macbook_pro_16.png', 2, '1761653714_1_18_1.jpg', '1761653714_2_18_2.jpg', '1761653714_3_18_3.jpg', '0', NULL, NULL, 5, 1, 1, 0, 1, 0),
(11, 'Máy ảnh Canon FTb', 'Máy ảnh sắc nét mình không dùng nữa', 4000000.00, 3, 'Còn hàng', NULL, '1761192225_M__y____nh_Canon_FTb.png', 3, '1761654396_1_10_1.jpg', '1761654396_2_10_2.jpg', '1761654396_3_10_3.jpg', '0', NULL, NULL, 12, 1, 1, 0, 1, 2),
(12, 'Nubia Red Magic 9 Pro', 'chiến game siêu mượt', 12000000.00, 2, 'Còn hàng', NULL, '1761192265_Nubia_Red_Magic_9_Pro.png', 3, '1761654372_1_9_1.jpg', '1761654372_2_9_2.jpg', '1761654372_3_9_3.jpg', '0', NULL, NULL, 1, 1, 1, 70, 1, 0),
(13, 'Ống kính 7Artisans 12mm', 'Ống kính mới không xước len', 700000.00, 2, 'Còn hàng', NULL, '1761192297____ng_k__nh_7Artisans_12mm.png', 3, '1761654353_1_8_1.jpg', '1761654353_2_8_2.jpg', '1761654353_3_8_3.jpg', '0', NULL, NULL, 12, 1, 1, 0, 1, 2),
(15, 'Phụ kiện zin apple samsung', 'Phụ kiện apple samsung các loại cần nhắn em', 840000.00, 6, 'Còn hàng', NULL, '1761192366_Ph____ki___n_zin_apple_samsung.png', 3, '1761654312_1_6_1.jpg', '1761654312_2_6_2.jpg', '1761654312_3_6_3.jpg', '0', NULL, NULL, 4, 1, 1, 0, 1, 0),
(16, 'Vivo X200 5G 12/256gb camera đẹp\r\n', 'Về lại em vivo giá tốt cho ae đam mê chụp ảnh\r\nVivo X200 5g 12/256gb 98\r\nMáy kèm ốp \r\nChip dimen 9400 siêu mạnh mẽ\r\nPin silicon 5800mah trâu ,Sạc nhanh 90w\r\nCamera zeiss AI chất lượng cực tốt', 20000000.00, 5, 'Còn hàng', NULL, 'vivo4.0.jpg', 3, 'vivo4.1.jpg', 'vivo4.2.jpg', 'vivo4.3.jpg', '0', NULL, NULL, 1, 1, 1, 0, 1, 0),
(17, 'Samsung tab a7 lite', 'Máy cũ dùng được', 7300000.00, 4, 'Còn hàng', NULL, '1761192436_Samsung_tab_a7_lite.png', 3, '1761654250_1_4_1.jpg', '1761654250_2_4_2.jpg', '1761654250_3_4_3.jpg', '0', NULL, NULL, 6, 1, 1, 0, 1, 0),
(18, 'Ssd 512GB', 'ổ ssd 512GB chính hãng', 500000.00, 2, 'Còn hàng', NULL, '1761192472_Ssd_512GB.png', 3, '1761654231_1_3_1.jpg', '1761654231_2_3_2.jpg', '1761654231_3_3_3.jpg', '0', NULL, NULL, 4, 1, 1, 0, 1, 0),
(19, 'Tản corsiar H150i Pro', 'Tản nhiệt máy tính mới', 1000000.00, 3, 'Còn hàng', NULL, '1761192494_T___n_corsiar_H150i_Pro.png', 3, '1761654212_1_2_1.jpg', '1761654212_2_2_2.jpg', NULL, '0', NULL, NULL, 4, 1, 1, 0, 1, 0),
(20, 'thinkpad x1 gen 8', 'Máy cũ như mới', 1900000.00, 0, 'Hết hàng', NULL, '1761192519_thinkpad_x1_gen_8.png', 3, '1761654190_1_1_1.jpg', '1761654190_2_1_2.jpg', '1761654190_3_1_3.jpg', '0', NULL, NULL, 6, 1, 1, 0, 1, 0),
(26, 'Bếp 4 họng', 'Bếp 4 họng lửa sẳn hàng\r\nGiao lắp kv cần thơ', 123000.00, 1, 'Còn hàng', NULL, '1761991761_B___p_4_h___ng.png', 3, NULL, NULL, NULL, '0', NULL, NULL, 2, 1, 1, 30, 1, 0),
(27, 'Máy lạnh Pannasonic 1,5HP Inverter mới 85%', 'Máy lạnh Pannasonic 1,5HP Inverter mới 85% máy hoạt động êm tiết kiệm điện ', 7000000.00, 20, 'Còn hàng', NULL, 'mlanh3.1.jpg', 3, 'mlanh3.0.jpg', NULL, NULL, '0', NULL, NULL, 2, 1, 1, 0, 1, 0),
(28, 'Máy tính để bàn Cooler Master Đen', 'CPU Cooler Master màu đen, hiệu năng ổn định, phù hợp cho máy tính để bàn.\r\n- Thiết kế tản nhiệt tốt, giúp máy hoạt động mát mẻ.\r\n- Dễ dàng lắp đặt, tương thích nhiều loại mainboard.\r\n- Giá tốt.', 18000000.00, 3, 'Còn hàng', NULL, 'thungpc.jpg', 3, 'thungpc.jpg', NULL, NULL, '0', NULL, NULL, 5, 1, 1, 0, 1, 1),
(29, 'Galaxy S24 Ultra 256Gb Chính Hãng Vn Zin Nét', 'S24 Ultra 256Gb\r\nBản Chính hãng VN\r\nRam 12/512Gb\r\n\r\n+ 2 sim, có ghi âm cuộc gọi mặc định hãng\r\n+ Đẹp 99%\r\n+ Zin nguyên áp…', 1990000.00, 70, 'Còn hàng', NULL, '253.0.jpg', 2, '253.1.jpg', '253.2.jpg', '253.3.jpg', NULL, NULL, NULL, 1, 1, 1, 0, 1, 1),
(30, 'Máy chạy bộ Rovera', 'Máy chạy bộ Rovera đã qua sử dụng, màu xám đen. \r\n - Thiết kế gấp gọn tiện lợi, tiết kiệm không gian. \r\n - Phù hợp tập luyện tại nhà, cải thiện sức khỏe.\r\nGia đình chuyển nhà cần thang lí', 2800000.00, 8, 'Còn hàng', NULL, 'chay1.0.jpg', 3, 'chay1.3.jpg', 'chay1.2.jpg', 'chay1.4.jpg', '0', NULL, NULL, 4, 1, 1, 20, 1, 0),
(31, 'Honor X50 128GB ', 'Thanh lý máy cũ honor zin như hư camera', 2000000.00, 3, 'Còn hàng', NULL, 'honor3.0.jpg', 7, 'honor1.0.jpg', 'honor1.1.jpg', NULL, '0', NULL, NULL, 1, 1, 1, 0, 1, 0),
(32, 'Vợt pickleball franklin c45 paristodd ', 'Mình cần bán vợt franklin c45 của paris todd màu cực đẹp.vợt mới mua đánh đc vài trận còn rất mới. Vợt giành cho ace nào thích tấn công đứng lưới tay nhanh.bản 13.25', 2500000.00, 4, 'Còn hàng', NULL, 'vot2.0.jpg', 7, 'vot2.1.jpg', 'vot2.2.jpg', NULL, '0', NULL, NULL, 4, 1, 1, 0, 1, 0),
(33, 'Đàn guitar gỗ màu nâu', 'Đàn guitar gỗ màu nâu, âm thanh ấm áp, phù hợp cho người mới tập chơi hoặc biểu diễn. \r\n - Chất liệu gỗ bền đẹp. \r\n - Thiết kế tinh tế, dễ cầm. \r\n - Âm thanh vang, rõ.', 12000000.00, 200, 'Còn hàng', NULL, 'dan1.0.jpg', 7, 'dan1.1.jpg', 'dan1.2.jpg', 'dan1.4.jpg', '0', NULL, NULL, 4, 1, 1, 0, 1, 0),
(35, 'Laptop HP5', 'HP 430g6 i5/8th/Ram 8/SSD 256/13.3\"HD\r\nVỏ nhôm sáng đẹp, mỏng nhẹ thời trang\r\nPin ok\r\nMáy chạy ngon full chức năng', 5000000.00, 15, 'Còn hàng', NULL, 'lap5.0.jpg', 7, 'lap5.1.jpg', 'lap5.2.jpg', NULL, '0', NULL, NULL, 5, 1, 1, 0, 1, 0),
(37, 'Máy lọc không khí IRIS OHYAMA RHF-404-W', 'Máy lọc không khí bù ẩm có kèm sưởi ấm IRIS OHYAMA ớRHF-404-W màu trắng, sản xuất năm 2020.đẹp xuất sắc \r\n - Thiết kế nhỏ gọn, phù hợp nhiều không gian.\r\n - Công suất 360/372W, hoạt động hiệu quả.ion diệt khuẩn giúp \r\n  không khí trong lành, bảo vệ sức khỏe.', 5000000.00, 19, 'Còn hàng', NULL, 'mayloc1.0.jpg', 7, 'mayloc1.4.jpg', 'mayloc1.3.jpg', 'mayloc1.1.jpg', '0', NULL, NULL, 2, 1, 1, 0, 1, 0),
(38, 'Bếp từ\r\n', 'Model mới nhất bếp nhập nguyên chiếc  Thái Lan Canzy Cz PUJ588PLUS serial 8. Model Nâng cấp Sx năm 2024 . \r\nBo viền có sôi liu riu đều ko bùng chàn ngắt quãng khi nấu.  \r\nMặt kính chống xước , mâm mạch nâng cấp hiện đại ', 12000000.00, 30, 'Còn hàng', NULL, 'bep3.0.jpg', 7, 'bep3.1.jpg', 'bep3.2.jpg', 'bep3.3.jpg', '0', NULL, NULL, 2, 1, 1, 0, 1, 0),
(39, 'Lò vi sóng Electrolux 23l có nướng', 'hình thức đẹp . hoạt động tốt mọi chức năng \r\nBảo hành 2 tháng\r\n\r\n', 550000.00, 39, 'Còn hàng', NULL, 'lo2.0.jpg', 7, 'lo2.2.jpg', 'lo2.1.jpg', NULL, '0', NULL, NULL, 2, 1, 1, 0, 1, 0),
(40, 'Iphone 13 ProMax 128gb vàng.\r\n', 'Mình đổi máy cần bán lại cây 13 Pro Max 128gb màu Gold Sa Mạc đang dùng cực tốt. Phiên bản quốc tế chính hãng Apple. Còn bảo hành gần 6 tháng hoàn toàn yên tâm. Máy mình mua đập hộp tới giờ chưa sửa chữa. Tình trạng không một lỗi nhỏ nào, máy xài kỹ đã dán cường lực 2 mặt xài ốp lưng nên còn đẹp 99% Pin mình xài 2 ngày, phụ kiện zin theo máy còn đầy đủ không thiếu gì sạc cáp tai nghe hộp trùng imei hoá đơn.', 12000000.00, 10, 'Còn hàng', NULL, 'ip6.0.jpg', 7, 'ip5.1.jpg', 'ip5.3.jpg', 'ip5.2.jpg', '0', NULL, NULL, 1, 1, 1, 0, 1, 0),
(41, 'MOTO GPX 150cc - FULL KIỂNG.', 'Cần thanh lý xe MOTO GPX DEMON 150GR FULL KIỂNG\r\n💥- Đăng ký 2018 - Chất xe còn rất mới \r\n❤️‍🔥ĐỒ CHƠI FULL XE NHƯ HÌNH NHA ANH EM\r\n                XE ĐẸP - SỬ DỤNG NGAY\r\n✅Máy móc zin êm . Ko một tiếng động lạ \r\n💁‍♂️- Xe chạy tốt  tiết kiệm nhiên liệu\r\n💁‍♂️-Dàn chân cứng cáp ,sạch sẽ rất đẹp ,lốp dày cui mới\r\n💁‍♂️-Dàn áo còn rất đẹp leng keng \r\n💁‍♂️-Giây tờ đầy đủ.Giao cavet và hoá đơn cửa hàng bán ra\r\n🤝-Hỗ trợ rút gốc sang tên ', 30000000.00, 60, 'Còn hàng', NULL, 'xemoi3.0.jpg', 7, 'xemoi3.3.jpg', 'xemoi3.2.jpg', 'xemoi3.1.jpg', '0', NULL, NULL, 10, 1, 1, 0, 1, 3),
(42, 'tai nghe i37', '🎧 Tai Nghe i37 - Bass Cực Lớn, Giá Cực Sốc!\r\nTên sản phẩm: Tai nghe i37 (Có vẻ là tai nghe over-ear cao cấp tương tự AirPods Max)\r\n\r\nGiá bán: 300.000 VND (Mức giá không tưởng!)\r\n\r\nĐiểm nhấn: \"nghe tốt bass lớn\" – Tận hưởng âm nhạc sống động với âm trầm (bass) mạnh mẽ, bùng nổ. Thiết kế sang trọng, đệm tai êm ái, đeo lâu không đau.\r\n\r\nTình trạng: Còn hàng | Số lượng còn: 19 chiếc.\r\n\r\nMục đích: Giải trí đỉnh cao, nghe nhạc, xem phim, chơi game.', 300000.00, 19, 'Còn hàng', NULL, '1762138808_23_1.jpg', 7, '', '', NULL, '0', NULL, NULL, 4, 1, 1, 0, 1, 0),
(43, 'Z1000 xe chính chủ, xe zin êm.', 'Z1000 date 2012 xe chính chủ, máy móc zin êm. Có giao lưu trao đổi\r\n\"AN TÂM KHI MUA XE TẠI CHIẾN MOTOR\r\n✅ Bảo hàng 06 tháng + giấy tờ đầy đủ\r\n✅ Công chứng ủy quyền hoặc sang tên toàn quốc\r\n✅ Máy móc zin, xe ngoại hình đẹp\r\n✅ Bán trả góp - trả trước xx', 99900000.00, 1, 'Còn hàng', NULL, 'xe3.0.jpg', 7, 'xe3.1.jpg', 'xe3.4.jpg', 'xe3.2.jpg', '0', NULL, NULL, 10, 1, 1, 0, 1, 0),
(44, 'Máy ảnh Canon 450D Đen Đã sử dụng', 'Bộ Canon 450D còn đẹp, sài ngon, đầy đủ phụ kiện.', 3500000.00, 1, 'Còn hàng', NULL, 'mayanh2.0.jpg', 2, 'mayanh2.1.jpg', 'mayanh2.3.jpg', 'mayanh2,4.jpg', '0', NULL, NULL, 12, 1, 1, 20, 1, 2),
(45, 'Apple iPad Pro M1 11 inch 128GB.', 'IPad Pro M1 11inh 128gb wifi hàng chính hãng VN mua Minh Tuấn Mobile\r\nNgoại hình máy còn đẹp 98%\r\nPin 89%\r\nMàn hình ko trầy sướt dán cường lực từ đầu, màn hình bị đè có 1 đường pixel như hình mình có chụp, ko loang hay lỗi gì\r\nMáy nguyên zin chưa sửa chữa, khuyến khích dẫn thợ thầy check test', 99000000.00, 3, 'Còn hàng', NULL, 'apple2.0.jpg', 7, 'apple2.1.jpg', 'apple2.2.jpg', 'apple2.3.jpg', '0', NULL, NULL, 1, 1, 1, 0, 1, 0),
(46, 'Xe đạp đua', 'Full chính hãng sườn cacbon đời cao dây âm sườn size s group điện 2x12 bánh roval chính hãng cl50 vỏ ruột rời ', 3200000.00, 5, 'Còn hàng', NULL, 'xedap1.0.jpg', 7, 'xedap1.1.jpg', 'xedap1.3.jpg', 'xedap1.2.jpg', '0', NULL, NULL, 11, 1, 1, 0, 1, 795),
(47, 'Quái Thú Pc AK56', 'Chào mừng các game thủ và tín đồ công nghệ! Đây chính là Quái Thú Pc AK56 - chiếc PC mạnh nhất thế giới (PC mạnh nhất TG) mà bạn hằng mơ ước!\r\nGiá cực sốc: 1.000.000.000 VND (Một tỷ đồng chẵn). Đầu tư một lần, trải nghiệm đỉnh cao vĩnh viễn!\r\nTình trạng: Còn hàng – Cơ hội sở hữu có hạn!\r\nĐiểm nhấn: Không chỉ là một cỗ máy, đây là một tác phẩm nghệ thuật với hệ thống ánh sáng RGB rực rỡ, bàn phím cơ và màn hình kép siêu rộng, mang lại trải nghiệm chơi game và làm việc không đối thủ. Tận hưởng mọi tựa game AAA ở mức cài đặt cao nhất mà không hề giật lag!', 1000000000.00, 2, 'Còn hàng', NULL, '1762140959_Hinh-anh-dan-PC-khung.jpg', 7, NULL, NULL, NULL, '0', NULL, NULL, 6, 1, 1, 0, 1, 0),
(48, 'Máy Tính Văn phòng v500', 'Mô tả nổi bật:\r\n\r\nMục đích sử dụng: Tuy là máy văn phòng nhưng có khả năng chiến game nét nên nét (ám chỉ khả năng chơi game tốt).\r\n\r\nĐặc điểm: Gọn nhẹ nhàng, cấu lưu trữ cá nhân.\r\n\r\nLưu trữ: Sử dụng NVMe PCIe 4.0 256 GB.\r\n\r\nThiết kế: Gọn, hiện đại.\r\n\r\nCổng kết nối: Có hỗ trợ HDMI 1.4, ...', 20000000.00, 20, 'Còn hàng', NULL, '1762141140_vi-vn-asus-v500mv-i3-1315u-8gb-256gb-slide-1.jpg', 7, '', NULL, NULL, '0', NULL, NULL, 6, 1, 1, 0, 1, 1),
(49, 'VinFast VF8 2025.', 'BÁN VINFAST VF8 PLUS - XE ĐẸP NHƯ MỚI\r\n\r\nOdo: 15.000 km (Xe đi rất ít).\r\n\r\nPin: Gói thuê pin cố định (không giới hạn km), tiết kiệm chi phí.\r\n\r\nTình trạng: Xe nguyên bản, sơn zin 100%. Đã cập nhật phần mềm mới nhất cực mượt, không lỗi vặt.\r\n\r\nBảo hành: Còn bảo hành chính hãng đến 10 năm (yên tâm sử dụng).', 850000000.00, 100, 'Còn hàng', NULL, 'vf82.0.jpg', 7, 'vf82.3.jpg', 'vf82.1.jpg', 'vf82.4.jpg', '0', NULL, NULL, 8, 1, 1, 0, 1, 1),
(50, 'Mec c200', 'CẦN BÁN MERCEDES C200 - XE ĐẸP, GIÁ CỰC TỐT\r\n\r\nĐời xe: 2020.\r\n\r\nTình trạng: Xe gia đình đi kỹ, bảo dưỡng full lịch trình, nội ngoại thất còn rất mới.\r\n\r\nVận hành: Máy êm, lái cực bốc, cách âm hoàn hảo đúng chất Mer.\r\n\r\nCam kết: Không đâm đụng, không ngập nước, bao check hãng toàn quốc.', 1400000000.00, 1, 'Còn hàng', NULL, 'mec2.0.jpg', 7, 'mec2.2.jpg', 'mec2.1.jpg', NULL, '0', NULL, NULL, 8, 1, 1, 0, 1, 0),
(51, 'Thanh lý máy lạnh LG 2HP', 'Tình trạng: Máy cũ nhưng làm lạnh siêu nhanh, thổi gió cực mạnh.\r\n\r\nCông suất: 2HP (phù hợp phòng khách hoặc phòng lớn 20-30m²).\r\n\r\nƯu điểm: Máy LG chạy bền, dễ sử dụng, bao test tại chỗ.', 4000000.00, 4, 'Còn hàng', NULL, 'maylanh3.0.jpg', 7, 'maylanh3.1.jpg', 'maylanh3.2.jpg', 'maylanh3.3.jpg', '0', NULL, NULL, 2, 1, 1, 0, 1, 0),
(52, 'Tủ lạnh Toshiba Inverter 311 lít', 'Tủ lạnh Toshiba Inverter 311 lít\r\n- Siêu thị bán : 13.090.000\r\n- Em bán : 9.000.000\r\n- Hàng mới, full thùng , bảo hành chính hãng\r\n- Hàng có sẵn ', 8000000.00, 29, 'Còn hàng', NULL, 'tulanh4.0.jpg', 7, 'tulanh4.1.jpg', 'tulanh4.3.jpg', NULL, '0', NULL, NULL, 2, 1, 1, 0, 1, 4),
(53, 'Tủ lạnh AQUA 93 lít Xám', 'tủ lạnh AQUA 93lit zin đẹp tiết kiệm điện có bảo hành miễn phí vận chuyển tủ chạy êm lạnh nhanh', 4000000.00, 3, 'Còn hàng', NULL, 'tulanh3.0.jpg', 7, 'tulanh3.2.jpg', 'tulanh3.1.jpg', 'tulanh3.3.jpg', '0', NULL, NULL, 2, 1, 1, 0, 1, 2),
(54, 'Tủ lạnh Hitachi Side by Side Đen', 'Tủ lạnh Hitachi Side by Side mặt gương, 3 cánh\r\nCông nghệ Inverter tiết kiệm điện. \r\n - Thiết kế sang trọng, dung tích lớn. \r\n - Giữ thực phẩm tươi ngon lâu hơn. \r\n - Hoạt động êm ái, bền bỉ.', 10000000.00, 39, 'Còn hàng', NULL, 'tulanh1.2.jpg', 7, 'tulanh2.jpg', 'tulanh1.jpg', 'tulanh3.jpg', '0', NULL, NULL, 2, 1, 1, 0, 1, 2),
(55, 'Máy Giặt HITACHI', 'THANH LÝ HITACHI 8kg - GIÁ RẺ\r\n\r\nMáy cũ theo thời gian nhưng máy móc bao bền, chạy khỏe.\r\n\r\nGiặt sạch, vắt khô, mọi chức năng bình thường.\r\n\r\nPhù hợp cho ai cần máy dùng chống cháy, sinh viên, thợ thuyền.', 25000000.00, 18, 'Còn hàng', NULL, 'maygiat2.1.1.jpg', 7, 'maygiat2.2.jpg', 'maygiat2.3.jpg', NULL, '0', NULL, NULL, 2, 1, 1, 0, 1, 1),
(56, 'Máy Giặt PANASONIC', 'MÁY GIẶT PANASONIC – GIẶT SẠCH NHƯ TAY MẸ 🧺 Nhà lên đời máy sấy nên dư ra em Panasonic 9kg. Máy dùng cực giữ gìn, lồng giặt lúc nào cũng thơm tho.\r\n\r\nƯu điểm: Chạy êm ru, không rung lắc, siêu tiết kiệm điện nước.\r\n\r\nCông nghệ: Có Econavi và kháng khuẩn Blue Ag+ (đồ trẻ em giặt cực yên tâm).\r\n\r\nTình trạng: Nguyên zin, chưa một lần đụng chạm sửa chữa.', 30000000.00, 19, 'Còn hàng', NULL, '1762142891_panasonic-82-kg-na-f82y01drv1-700x467.jpg', 7, '1762520507_1_panasonic-82-kg-na-f82y01drv2-700x467.jpg', 'maygiat3.jpg', 'maygiat2.jpg', '0', NULL, NULL, 2, 1, 1, 0, 1, 1),
(57, 'Samsung Galaxy S25 FE', 'S25 FE – CẤU HÌNH S25 NHƯNG GIÁ \"HỌC SINH\" Cần pass em S25 FE đẹp như mới. Máy dành cho ai thích chip mạnh mà ngại bỏ tiền mua bản Ultra.\r\n\r\nSức mạnh: RAM 8GB/256GB, cân mọi loại game không nóng máy.\r\n\r\nCamera: Chụp đêm đỉnh cao, nét từng sợi tóc.\r\n\r\nNgoại hình: Màu xanh cực sang.', 15000000.00, 25, 'Còn hàng', NULL, '1762143162_samsung-galaxy-s25-fe-blue-5-638938966702603121-750x500.jpg', 7, '1762520350_1_samsung-galaxy-s25-fe-blue-4-638938966708861699-750x500.jpg', NULL, 'samsung1.jpg', '0', NULL, NULL, 1, 1, 1, 0, 1, 0),
(58, 'Samsung Galaxy A17 5G', 'SAMSUNG A17 5G – ĐẸP KENG, GIÁ HẠT DẺ\r\n\r\nMáy đẹp như đập hộp, không một vết xước.\r\n\r\nMàn AMOLED 120Hz lướt mượt như người yêu cũ trở mặt.\r\n\r\nPin trâu bò, 5G chạy xé gió.\r\n\r\nRAM 8GB / ROM 128GB – Đa nhiệm không độ trễ.', 7000000.00, 8, 'Còn hàng', NULL, '1762143269_samsung-galaxy-a17-5g-gray-1-638925131547875229-750x500.jpg', 7, '1762520231_1_samsung-galaxy-a17-5g-gray-4-638925131528503776-750x500.jpg', NULL, NULL, '0', NULL, NULL, 1, 1, 1, 0, 1, 2),
(59, 'Oppo reno 14 pro 5G', 'Máy chính chủ sử dụng, ngoại hình như mới, mọi chức năng hoàn hảo.\r\n\r\nCấu hình: RAM 12GB / ROM 256GB (Chạy cực mượt, lưu trữ thoải mái).\r\n\r\nCamera: Chụp chân dung AI siêu đẹp, chống rung tốt.\r\n\r\nPin: Sạc siêu nhanh SuperVOOC, pin cực bền.\r\n\r\nTình trạng: Nguyên zin, chưa sửa chữa, màn hình không trầy xước.', 10000000.00, 31, 'Còn hàng', NULL, 'oppo14.jpg', 7, 'oppo141.jpg', 'oppo142.jpg', NULL, '0', NULL, NULL, 1, 1, 1, 0, 1, 3),
(60, 'VIVO V60 5G', 'Máy như mới, chính chủ dùng kỹ, cực kỳ mượt mà.\r\n\r\nCấu hình: RAM 12GB (+ mở rộng), Bộ nhớ 256GB (tha hồ lưu ảnh/video).\r\n\r\nPin: 6500mAh cực trâu + Sạc nhanh 90W.\r\n\r\nCamera: ZEISS chụp ảnh cực nét.\r\n\r\nTình trạng: Đẹp 99%, zin 100%, không lỗi lầm.', 12000000.00, 20, 'Còn hàng', NULL, '1762143563_7f1aa11dec84a0c3134a834df83fa518.png', 7, 'vivoy60.jpg', 'vivoy601.jpg', NULL, '0', NULL, NULL, 1, 1, 1, 0, 1, 2),
(61, 'BMW X5 2015', 'Mình cần nhượng lại chiếc BMW X5 đời 2015, xe gia đình sử dụng kỹ, bảo dưỡng định kỳ đúng chuẩn. Dòng này chạy cực đầm, cách âm tuyệt vời và ngoại hình vẫn rất thời thượng.\r\n\r\nTình trạng: Xe nguyên bản, không đâm đụng, không ngập nước (bao check hãng/gara toàn quốc).\r\n\r\nNội thất: Ghế da cao cấp còn mới, hệ thống âm thanh cực hay, điều hòa mát sâu.\r\n\r\nNgoại thất: Màu sơn còn bóng đẹp, đăng kiểm dài hạn.\r\n\r\nVận hành: Động cơ mạnh mẽ, hộp số mượt mà, cảm giác lái đúng chất BMW.', 1000000000.00, 0, 'Hết hàng', NULL, 'bwm1.jpg', 7, 'bwm.jpg', 'bwm1.jpg', NULL, '0', NULL, NULL, 8, 0, 1, 0, 1, 55),
(73, 'iPhone XR độ 17 Pro quốc tế', 'Loại máy: iPhone XR độ 17 Pro màu cam\r\nMàu: cam\r\nBộ nhớ: 64G\r\nBản : Quốc tế\r\nChức năng: full , face ID tốt\r\nDung lượng pin: 82\r\nVỏ hộp và phụ kiện: không có\r\nTình trạng: máy zin hết chỉ độ vỏ\r\nBảo hành: 1 tuần', 5000000.00, 30, 'Còn hàng', NULL, 'ip172.0.jpg', 12, 'ip172.1.jpg', 'ip172.2.jpg', 'ip172.3.jpg', '0', NULL, NULL, 1, 1, 1, 0, 1, 4),
(74, 'Máy tính PC văn phòng, học tập, đọc báo đủ bộ.', 'Thanh lý bộ máy tính để bàn Lenovo, đầy đủ trọn bộ.\r\n - Cấu hình: Intel Pentium G3420, RAM 4GB DDR3, SSD 120GB.\r\n - Card đồ họa tích hợp Intel HD Graphics.\r\n - Màn hình độ phân giải 1680 x 1050.\r\n - Phù hợp cho công việc văn phòng, học tập.\r\nĐầy đủ trọn bộ, bao gồm chuột, bàn phím, card wifi, về chỉ cắm điện là dùng \r\nHệ điều hành Windows 10, bao test trong ngày, kể cả thợ.\r\ncam kết chưa sửa chữa.', 2500000.00, 3, 'Còn hàng', NULL, 'pc2.0.jpg', 12, 'pc2.1.jpg', 'pc2.2.jpg', 'pc2.3.jpg', '0', NULL, NULL, 5, 1, 1, 0, 1, 0),
(76, 'Philips 206V6', 'Em cần bán bộ máy tính như trên 2 màn 1 màn 19inch và 1 màn 22inch i5 card 730 máy dùng ổn định \r\nmàn hình bên phải có bị lỗi em bán nhanh fix mạnh ai nhiệt tình ạ', 2900000.00, 1, '0', NULL, 'mt3.0.jpg', 12, 'mt3.3.jpg', 'mt3.2.jpg', 'mt3.1.jpg', '0', NULL, NULL, 5, 1, 1, 0, 1, 0),
(77, 'Loa Bluetooth PHICOMM R1 Infinity Harman', '-Mình cần bán loa để bàn Phicomm R1 âm thanh chất lượng, đèn led phát sáng tuỳ chỉnh nháy theo nhạc.\r\n', 400000.00, 50, 'Còn hàng', NULL, 'loa1.0.jpg', 12, 'loa1.2.jpg', 'loa1.1jpg.jpg', NULL, '0', NULL, NULL, 6, 1, 1, 0, 1, 0),
(78, 'Soundbar Yamaha ysp 2200', 'Loa soundbar Yamaha YSP-2200 màu đen, thiết kế mỏng gọn, chất liệu kim loại bền bỉ.\r\n - Âm thanh vòm sống động, chân thực.\r\n - Kết nối đa dạng, dễ dàng lắp đặt.\r\n - Phù hợp cho không gian giải trí gia đình.', 4900000.00, 99, '0', NULL, 'so1.0.jpg', 12, 'so1.2.jpg', 'so1.3.jpg', NULL, '0', NULL, NULL, 6, 1, 1, 0, 1, 0),
(79, 'Tai nghe Sony WH-CH520 Trắng', 'Tai nghe Bluetooth chụp tai Sony WH-CH520 hàng chính hãng \r\n- Tai Nghe Sony WH-CH520 với mức giá hạt rẻ \r\n- Kết nối Bluetooth 5.2, tuỳ chỉnh EQ qua app Sony Headphones Connect', 990000.00, 7, 'Còn hàng', NULL, 'tai3.0.jpg', 12, 'tai3.3.jpg', 'tai3.1.jpg', 'tai3.2.jpg', '0', NULL, NULL, 6, 1, 1, 0, 1, 0),
(80, 'Airpods Pro 2 Type C Fullbox Còn Bảo Hành', 'AirPods Pro 2 TypeC Fullbox BH apple Care+ 31.8.2026\r\n- Airpods pro 2 TypeC siêu đẹp\r\n- Fullbox đầy đủ pk zin theo tai nghe: dây cáp Typec, 3 núm cao su phụ zin\r\n- Tất cả zin và còn đẹp', 2500000.00, 20, 'Còn hàng', NULL, 'nghe2.0.jpg', 12, 'nghe2.3.jpg', 'nghe2.2.jpg', 'nghe2.1.jpg', '0', NULL, NULL, 6, 1, 1, 0, 1, 0),
(81, 'Máy ảnh Canon 750D Đen', 'Máy ảnh Canon 750D kèm kit ngoại hình máy đẹp.\r\nTình trạng chức năng, phím nút hoạt động hoàn hảo.\r\nMàn hình sáng đẹp có cảm ứng, xoay lật, có wifi .Chụp đẹp, dễ dùng người mới chơi \r\nPhụ kiện máy đi kèm đủ', 8550000.00, 40, 'Còn hàng', NULL, 'anh4.0.jpg', 12, 'anh4.4.jpg', 'anh4.1.jpg', NULL, '0', NULL, NULL, 12, 1, 1, 0, 1, 768),
(82, 'Card đồ họa Gigabyte RTX 3060 12GB ', 'Gigabyte rtx 3060 12gb còn bảo hành t7/27 vẫn còn mới nhiệt mát ko lỗi lầm gì', 5000000.00, 3, 'Còn hàng', NULL, 'bo1.0.jpg', 12, 'bo1.3.jpg', 'bo1.1.jpg', 'bo1.2.jpg', '0', NULL, NULL, 6, 1, 1, 0, 1, 1),
(83, 'Máy sấy Electrolux 8,5kg đời mới 98%', 'Em về mẫu máy sấy Electrolux 8,5kg thông hơi đời mới siêu lướt, date 2023 màu đen nhám đẹp long lanh chưa 1 vết sơn xước, zin nguyên bản. \r\nDòng đời mới có cấp nước tạo ẩm, tăng cường chống nhăn tốt hơn, sấy nhanh khô diệt khuẩn, hút bụi sơ vải sạch sẽ.', 7000000.00, 20, 'Còn hàng', NULL, 's1.0.jpg', 12, 's1.1.jpg', 's1.3.jpg', NULL, '0', NULL, NULL, 2, 1, 1, 0, 1, 6),
(84, 'Đồng hồ Rado Golden Horse Nâu', 'Rado 30 Jewels thuỵ sỹ\r\nSize 35 máy cơ Automatic\r\nZin nguyên củ', 3200000.00, 39, 'Còn hàng', NULL, 'dong2.0.jpg', 12, 'dong2.4.jpg', 'dong2.3.jpg', 'dong2.1.jpg', '0', NULL, NULL, 7, 1, 1, 0, 1, 11),
(85, 'Đồng hồ Maurice Lacroix Pontos.', 'Maurice Lacroix Pontos PT6178\r\n- Máy caliber ML112,trữ cót 46h\r\n- Kính saphire nguyên khối \r\n- Kim cọc siêu nét \r\n- Kháng nước 50m\r\n', 25000000.00, 10, 'Còn hàng', NULL, 'dh3.0.jpg', 12, 'dh3.1.jpg', 'dh3.3.jpg', 'dh3.4.jpg', '0', NULL, NULL, 7, 1, 1, 0, 1, 11),
(86, 'Đồng hồ để bàn Jaeger Vàng', 'Như một khối vàng\r\nCao 45cm, ngang 25cm\r\nMáy điện tử của Pháp chạy Pin đại từ những thập niên 80.', 1500000.00, 3, 'Còn hàng', NULL, 'vang1.0.jpg', 13, 'vang1.2.jpg', 'vang1.4.jpg', 'vang1.3.jpg', '0', NULL, NULL, 7, 1, 1, 0, 1, 10),
(87, 'Panasonic inverter 280l cấp đông.', 'Em về mẫu Panasonic inverter dung tích tổng 280l (255l) đời mới sạch đẹp như mới, khay kệ đầy đủ sạch đẹp, nội thất roăng trắng sáng.', 6990000.00, 99, 'Còn hàng', NULL, 'l2.0.jpg', 13, 'l2.2.jpg', 'l2.1.jpg', NULL, '0', NULL, NULL, 2, 1, 1, 0, 1, 5),
(88, 'Ford Laser 2003', 'xe em bảo dưỡng định kì đầy đủ, bác nào mua về la chạy luôn thôi ạ.\r\nRiêng đồ chơi ko thiếu gì, Màn Android 4G xịn xò, nghe nhạc đỉnh cao, camera hành trình, camera lùi nét căng cũng  hơn chục triệu, giàn lốp cũng mới 1chục. đủ 2 chìa khóa. Sedan rộng rãi, đi cực thích. Nói chung ngon hế.', 4999000.00, 2, 'Còn hàng', NULL, 'ban3.0.jpg', 13, 'ban3.3.jpg', 'ban3.1.jpg', 'ban3.4.jpg', '0', NULL, NULL, 8, 1, 1, 0, 1, 677),
(89, 'Volvo XC90 T6 Inscription', 'Đời xe:2021\r\n\r\nOdo: 600000 km\r\n\r\nMàu: Đen / Nội thất [Màu ghế]\r\n\r\nTình trạng: Xe đẹp xuất sắc, sơn zin cực nhiều. Full lịch sử bảo dưỡng hãng.\r\n\r\nOption nổi bật: Loa Bowers & Wilkins, Ghế Massage/Làm mát, Camera 360, Phanh khoảng cách, Treo khí nén cực êm.\r\n\r\n✅ Cam kết không đâm đụng, không ngập nước, máy móc nguyên bản. Bao check hãng toàn quốc.', 999000000.00, 4, 'Còn hàng', NULL, 'xe4.0.jpg', 13, 'xe4.1.jpg', 'xe4.2.jpg', 'xe4.3.jpg', '0', NULL, NULL, 8, 1, 1, 0, 1, 8442),
(90, 'Yamaha R15V3 2019 máy zin biển số 67', 'Yamaha R15V3 2019 biển số 67\r\nGiấy tờ đầy đủ , giao cccd chủ \r\nMáy móc bao zin êm\r\nDàn áo , chânn sạch đẹp\r\nXe lên được vài món đồ chơi kiểng như hình\r\nMọi chức năng hoạt động tốt', 29900000.00, 20, 'Còn hàng', NULL, 'r152.0.jpg', 13, 'r152.1.jpg', 'r152.2.jpg', 'r152.3.jpg', '0', NULL, NULL, 10, 1, 1, 0, 1, 597),
(91, 'Xe điện VinFast VF5 Plus 2025 Xám', 'VF5 Plus 2025 xám xi măng\r\nOdo: 3v3 km\r\nXe còn bảo hiểm thân vỏ', 400000000.00, 30, 'Còn hàng', NULL, 'vf3.0.jpg', 13, 'vf3.2.jpg', 'vf3.3.jpg', 'vf3.1.jpg', '0', NULL, NULL, 8, 1, 1, 0, 1, 889),
(92, 'Suzuki GSX 150r 2020', '𝗡𝗵𝗮̣̂𝗻 𝗚𝗶𝗮𝗼 𝗟𝘂̛𝘂 - 𝗠𝘂𝗮 𝗕𝗮́𝗻 - Đ𝗼̂̉𝗶 𝗖𝗮́𝗰 𝗫𝗲 𝗟𝗲̂𝗻 Đ𝗼̛̀𝗶 𝗚𝗶𝗮́ 𝗧𝗼̂́𝘁\r\n✔ Bao Check test 1 đổi 1 Miễn Phí Trong 1 Tuần \r\n✔ Hỗ trợ trả góp đến 45tr chỉ cần CCCD\r\n✔ Hỗ trợ trả góp 0% cho thẻ tín dụng\r\n✔ Hỗ trợ trả góp cho cả ae nợ chú ý, xấu\r\n✔ Bảo hành động cơ xe 2 năm', 29000000.00, 99, 'Còn hàng', NULL, 'su1.0.jpg', 13, 'su1.1.jpg', 'su1.3.jpg', 'su1.2.jpg', '0', NULL, NULL, 10, 1, 1, 0, 1, 8806),
(93, 'KIA k2700 xe đẹp sx2014i', 'KIA K2700 SX 2014 xe đẹp. có màn hình cam hanh trình cam lùi. thùng sàn inox. lốp đẹp. khám phí dài. điều hòa mát. xe chất lượng các bác hợp việc đến xem xe nhé', 179000000.00, 49, 'Còn hàng', NULL, 'xetai1.0jpg.jpg', 13, 'xetai1.1.jpg', 'xetai1.3.jpg', 'xetai1.2.jpg', '0', NULL, NULL, 8, 1, 1, 0, 1, 3471),
(94, 'Đầu kéo HOWO 420, đời 2021.', 'Bán đầu kéo HOWO đời 2021,cầu dầu, máy 420, xe nguyên zin, hồ sơ rút sãn.', 700000000.00, 2, 'Còn hàng', NULL, 'keo1.0.jpg', 13, 'keo1.1.jpg', 'keo1.3.jpg', 'keo1.4.jpg', '0', NULL, NULL, 8, 1, 1, 0, 1, 6744),
(96, 'Xe máy điện Vespa màu Xám lịch sự - bền bỉ', 'Xe còn khá mới anh em giúp em', 17990000.00, 99, 'Còn hàng', NULL, 'xedien1.0.jpg', 13, 'xedien1.1.jpg', 'xedien1.3.jpg', NULL, '0', NULL, NULL, 10, 1, 1, 0, 1, 4590),
(97, 'Xe đạp trợ lực Nhật Bản 20 inch', 'xe đạp trợ lưc nhật bãi pin mới bh 12tháng bánh 20inch khung nhôm full tính năng', 10000000.00, 20, 'Còn hàng', NULL, 'dap2.0.jpg', 13, 'dap2.2.jpg', 'dap2.3.jpg', 'dap2.1.jpg', '0', NULL, NULL, 11, 1, 1, 0, 1, 9886),
(98, 'Xe đạp địa hình Bianchi Magma 29.2', 'Pass xe đạp Bianchi magma 29.2 chính hãng hình thức 98% đẹp ít sử dụng size 29 inch phù hợp ng chiều cao m7 -m85 đi đẹp xe khung nhôm aluminum siêu nhẹ vành size 29 phuộc suntour êm mọi địa hình bộ chuyển động shimano alivio 2x9,phanh đĩa thuỷ lực,lốp kenda chính hãng xe chính hãng của Ý phù hợp cho ng mới bắt đầu và đẹp xe thường xuyên xe khung siêu bền và lâu dài', 20000000.00, 38, 'Còn hàng', NULL, 'd4.0.jpg', 13, 'd4.1.jpg', 'd4.2.jpg', NULL, '0', NULL, NULL, 11, 1, 1, 0, 1, 6549),
(99, 'Kawasaki Zx25R 2022 abs.', '✔️ Màu đen đỏ ngầu chất sport, đời form đại diện Sport của nhà Kawasaki \r\n✔️ Động cơ 250cc , năng động , Công nghệ fun xăng điện tử, phanh đĩa to 2 kênh ABS, máy êm cực mượt, đầm xe. xe đã lên đôi Lốp to 190 siêu to\r\n✔️ Date 2022 abs  biển số 29 odo 11000 . Bảo hành đầy đủ 12 tháng\r\n✔️ Bảo hành Cam kết xe rất mới nguyên bản tuyệt đối\r\n✔️ Cam kết xe không tai nạn, không ngập nước', 85000000.00, 9, 'Còn hàng', 'kawa400.mp4', 'ka1.0.jpg', 14, 'ka1.1.jpg', 'ka1.2.jpg', 'ka1.3.jpg', NULL, NULL, NULL, 10, 1, 1, 0, 1, 8795),
(100, 'BMW 2015 320i', 'can bán bmw 320i máy mới B48 xe màu trắng xe còn nguyên bản', 500000000.00, 10, 'Còn hàng', 'bwm320i.mp4', '320i1.0.jpg', 14, '320i1.1.jpg', '320i1.2.jpg', '320i1.3.jpg', NULL, NULL, NULL, 8, 1, 1, 0, 1, 9008),
(101, 'BMW S1000RR 2018, XE ĐẸP ODO 22K', 'BMW S1000RR – hàng chơi khỏi bàn\r\n\r\n• ĐK lần đầu 2018\r\n• Odo 22.000km – siêu lướt - biển Sg \r\n• Màu xanh HP – gắp bạc nhìn là mê\r\n\r\n🔧 Đồ chơi đã lên sẵn:\r\n• Pô Austin Racing slip-on real\r\n• Chống đổ T-Rex\r\n• Bảo vệ lốc GB Racing\r\n• Chắn gió\r\n• Ốp sườn carbon\r\n• Ốp dè con carbon\r\n• Pát tăng sên\r\n• Cặp vỏ Pirelli Rosso còn dày cui', 499000000.00, 2, 'Còn hàng', 'ca2.0.mp4', 'ca2.0.jpg', 14, 'ca2.4.jpg', 'ca2.5.jpg', 'ca2.3.jpg', NULL, NULL, NULL, 10, 1, 1, 0, 1, 12232),
(103, 'Nissan 370Z Nismo', '💥💥 Nissan #370z_Nismo sx 2020 độc bản siêu hiếm lướt nhẹ đúng 4.567 km căng đét như xe mới trong hãng 🔥🔥\r\n1 viên duy 1’ tại thị trường\r\n✅𝐌𝐚̀𝐮 𝐱𝐞: Đen - nội thất đỏ\r\n✅𝐒𝐚̉𝐧 𝐱𝐮𝐚̂́𝐭: 2020\r\n©️ 𝑂𝑑𝑜 𝑐ℎ𝑢𝑎̂̉𝑛: 4.567 km - mới như xe trong hãng thiếu mỗi cái thùng 🔥\r\n👌𝐓𝐢̀𝐧𝐡 𝐭𝐫𝐚̣𝐧𝐠 𝐱𝐞: Xe cá nhân 1 chủ siêu lướt 4.567 km tiết kiệm ngay 3🧄\r\n', 3999000000.00, 0, 'Hết hàng', 'nissan2.0.mp4', 'nissan2.0.jpg', 14, 'nissan2.1.jpg', 'nisson2.3.jpg', 'nisson2.4.jpg', 'nisson2.2.jpg', NULL, NULL, 8, 1, 1, 0, 1, 7988),
(109, 'Canon EOS M50 + 15-45mm (Màu trắng), Mới 97%', 'Độ phân giải 24,1 MP, bộ cảm biến CMOS APS-C\r\nDual pixel CMOS AF\r\nBộ xử lý hình ảnh DIGIC 8\r\nVideo 4K và Quay phim HD 720p120\r\nISO: 100-25600 (mở rộng ISO: 51200)\r\nChụp Liên tục: Lên đến 10 khung hình/giây (ở servo AF: lên đến 7,4 khung hình/giây)\r\nMàn hình: EVF: 2,36 điểm ảnh\r\nWi-Fi · Bluetooth · Đã cài đặt NFC\r\nKết hợp hệ thống chống rung 5 trục\r\nỐng kính STM EF-M 15-45mm f / 3.5-6.3 IS', 15000000.00, 50, 'Còn hàng', NULL, '1775051683_mayanh99.jpg', 2, NULL, NULL, NULL, NULL, NULL, NULL, 12, 1, 1, 0, 1, 4835),
(113, 'Xe AB Đời 2019 cao cấp', 'Xe AB (Honda Air Blade) là dòng xe tay ga thể thao phổ biến tại Việt Nam. Xe có thiết kế góc cạnh, mạnh mẽ với hai tùy chọn động cơ chính là 125cc và 160cc, trang bị đèn LED hiện đại, hệ thống khóa thông minh Smart Key, phanh ABS (tùy phiên bản) và cốp chứa đồ rộng rãi.', 49000000.00, 27, 'Còn hàng', NULL, '1786174071_anhab2019.png', 2, NULL, NULL, NULL, NULL, NULL, NULL, 10, 1, 1, 10, 1, 62);

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
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vaitro` enum('admin','seller','buyer') COLLATE utf8mb4_unicode_ci NOT NULL,
  `trangThai` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`maTaiKhoan`),
  UNIQUE KEY `uk_tenDangNhap` (`tenDangNhap`)
) ENGINE=InnoDB AUTO_INCREMENT=10011 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `taikhoan`
--

INSERT INTO `taikhoan` (`maTaiKhoan`, `tenNguoiDung`, `tenDangNhap`, `matKhau`, `soDienThoai`, `diaChi`, `avatar`, `vaitro`, `trangThai`) VALUES
(1, 'NguyenVan', 'mua@gmail.com', '$2y$10$PLu0.yaNMnEU.wKHFOGCTeRMoQeN.a5kfV7evgmDwQULghcyISkPK', '0321412388', 'Cần Thơ', 'assets/img/avatars/user_1.png', 'buyer', 1),
(2, 'Nguyenđây', 'ban@gmail.com', '$2y$12$FI.1JVe5mQLapvpylGs7xe9D5SEaFR9SiBRSb/6.i/hOy2RLSdZUS', '0351268532', 'An Giang', 'assets/img/avatars/user_2.png', 'seller', 1),
(3, 'Nhi', 'ban1@gmail.com', '$2y$10$Tafvxk3n/PWRA3nHJ5eYHOl1n98SPccDOOeQwYv1PzQ/TDUDL7Cwi', '0842560721', 'Vĩnh Long', NULL, 'seller', 1),
(7, 'Phu', 'nguoiban@gmail.com', '$2y$10$965nPYkyY3G1M6RzfHySM.91svOclqMNJ25FtYlLwviHRu0Rg7Q3.', '115', 'cam', NULL, 'seller', 1),
(8, 'Lan', 'nguoimua@gmail.com', '$2y$10$Rdm4Vi1aGyTePAmg2YpXZ.su2fVKnwG5LyBtRASszt.GQQHfh06XC', '09672345124', '', NULL, 'buyer', 1),
(9, 'Diep', 'admin1@gmail.com', '$2y$12$UFgq1XCxXBLK69lgIohdxOlfrIKpC5vIHv.7sPdK.LnJTSI/kOVwa', '0939011229', 'An Giang', 'assets/img/avatars/default.png', 'admin', 1),
(10, 'nhan', 'nhan@gmail.com', '$2y$10$t8SUXuSLp1fMKy9FRrlqZOFd7icdZ4UUhG1GtQzQUhhUi.4N1Lx0y', '', '', NULL, 'buyer', 1),
(12, 'Anh Tuấn', 'Atuan@gmail.com', '$2y$10$m4PdrNeyXY.ljv7gK1PyYu2H9JWGv.QsqCRnLII.hr0HIjQ6klztW', '01247473825', 'Châu Đốc An Giang', NULL, 'seller', 1),
(13, 'Nguyễn Đức Lương', 'Aluong@gmail.com', '$2y$10$utCXOP8bFCLKvO9Wrr2PaOLxIugumFF.uUOi4uA2qAfxWNBIuPZqO', '0989898988', 'Phong Điền Cần Thơ', NULL, 'seller', 1),
(14, 'Quang Thuận', 'thuan@gmail.com', '$2y$12$hJ.npWUp/hl.ktLW9k6EIeG0nq4rnTJwj.u5MoOiP5TQQSaQT3wQC', '0767676543', 'Ô Môn Cần Thơ', NULL, 'seller', 1),
(15, 'test1@11', 'test1@11', '$2y$10$aiDtE5RPvPzKD71jP4ukpORzXOEb5Q1OMHyqNN/XgWae.cBIDfXTu', '0939011666', 'ấp 7', NULL, 'seller', 1),
(16, 'test1@22', 'test1@22', '$2y$10$wbx3ry3pnF8/6hRC9SjWJugJrGTXMEA5md7xkYtohvDnTxUZlvMv.', '0763992600', 'ấp 7', NULL, 'seller', 1),
(18, 'test099', 'mua2@gmail.com', '$2y$10$w.qrt9Hj3C48P/MTHghKPuYB0MUkTFXlRu.99Irov9nSu5/3KtmLC', '0984738489', 'Làng Vũ Đại', NULL, 'buyer', 1),
(20, 'Thiên Kim', 'kim@gmail.com', '$2y$12$J92hW.gji4j30yYzJ4kSi.qbmzueuNRTYP50/wkw79K87MNUvGmMu', '0763992600', 'Phường 7-TPHCM', NULL, 'buyer', 1),
(9999, 'AI Hỗ Trợ', 'ai_bot', '', NULL, NULL, NULL, 'admin', 1),
(10000, 'phúc', 'phuc@gmail.com', '$2y$12$OzpQXeXJS19OxebLJFkKc.voU9.IpWRRPQ/qbM08Y5D7PXMtZzzHi', '0939011666', 'Hà TâyXuân Lộc-Hà Bắc', NULL, 'buyer', 1),
(10001, 'Thiên phú', 'phu@gmail.com', '$2y$12$Jr6uWVDyHynDdxyaZ2/cj.HQWjiZiaE.o7gQ6UlKiC3Ggi8axLpmy', '0939011666', 'ấp Mỹ chánh,xã Hiệp Hưng,Thành Phố Cần Thơ', NULL, 'buyer', 1),
(10002, 'Mẫn Nhi', 'Nhi2011@gmail.com', '$2y$12$7V4PIVul1bDmK9hT3wbrqOi4wTDmYgKU0vnnFSVFSwGZ8UEMZ6lSS', '0866645162', 'Huyện Chợ Mới Tỉnh An Giang', NULL, 'buyer', 0),
(10003, 'Quang Hùng', 'hung@gmail.com', '$2y$10$2ytchKqK3wWHqk1ghb8u9.OJ7HxrITr/hYMOhJTrOrdD1Uh2VV5t.', '0976665437', 'Cà Mau', NULL, 'buyer', 1),
(10004, 'Độ', 'Do@gmail.com', '$2y$10$wvnPiC0qPRFTCe.QN4v3Le5ZnjfD.H8xSQw0uOsQIdHdN8vxBEAiS', '0966657888', 'Thái Nguyên', NULL, 'buyer', 1),
(10005, 'John', 'john01072005@gmail.com', '$2y$10$DnVvW91nwFqMCCe92u3BJ.tA/RM/8bOPo4hZREwXG4zVIBMcOqOfi', '0998865777', 'An Giang', NULL, 'seller', 1),
(10006, 'Đức Nguyên', 'Nguyen@gmail.com', '$2y$10$gUSVDdgE0akgwnO7ACEtGe0Y4DmUBx0d79NFCByURjhpPmvEPqfkm', '0866645161', 'Huyện Chợ Mới Tỉnh An Giang', NULL, 'buyer', 1),
(10007, 'Thiên Tài', 'tai@gmail.com', '$2y$10$0JsCHsk7Zq/I2OkvjABnpuX.i/XHIBBhW.xvsZBXztyVfdJn912T2', '0977688953', 'Huế', NULL, 'buyer', 1),
(10008, 'Hoàng', 'hoan1234@gmail.com', '$2y$12$N.1XXteEmjluvw3kR2hp8uZTpAIlIRNkxiiJKNafSD7fBTpgXoBC2', '0837198200', 'Hà TâyXuân Lộc-Hà Bắc', NULL, 'buyer', 1),
(10009, 'Aday', 'aday@gmai.com', '$2y$12$3xzCtJ4xtlvMt4NhZJhrUePpnjeuB6q8tJ/78OzygdJhx/VojT42y', '0351212311', 'An Giang', NULL, 'buyer', 1),
(10010, 'Aem', 'aem@gmai.com', '$2y$12$ecK.xwmp4WTx2e5o6/Z/qu9CDsl.JrpQKOOdPzOzmt25dVsGpf0wG', '0351211211', 'An Giang', NULL, 'buyer', 1);

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
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `thanhtoan`
--

INSERT INTO `thanhtoan` (`maThanhToan`, `maDonHang`, `phuongThuc`, `soTien`, `trangThai`, `ngayThanhToan`, `nganHang`, `maGiaoDich`) VALUES
(33, 80, 'cod', 570000000, 'pending', '2026-03-06 11:13:28', NULL, NULL),
(34, 81, 'cod', 403750000, 'pending', '2026-03-06 11:13:40', NULL, NULL),
(35, 82, 'fake_vnpay', 570000000, 'success', '2026-03-06 11:13:48', NULL, NULL),
(36, 83, 'cod', 195000000, 'pending', '2026-03-06 11:20:21', NULL, NULL),
(37, 84, 'fake_vnpay', 215000000, 'success', '2026-03-06 11:20:30', NULL, NULL),
(38, 85, 'fake_vnpay', 195000000, 'success', '2026-03-06 15:00:49', NULL, NULL),
(39, 86, 'cod', 195000000, 'pending', '2026-03-06 15:12:48', NULL, NULL),
(40, 87, 'cod', 403750000, 'pending', '2026-03-13 11:29:26', NULL, NULL),
(41, 88, 'cod', 195000000, 'pending', '2026-03-14 13:38:16', NULL, NULL),
(42, 89, 'cod', 215000000, 'pending', '2026-03-23 15:24:45', NULL, NULL),
(43, 90, 'fake_vnpay', 3000000, 'success', '2026-03-23 15:33:55', NULL, NULL),
(44, 91, 'fake_vnpay', 29000000, 'success', '2026-03-23 15:40:39', NULL, NULL),
(45, 92, 'fake_vnpay', 425000000, 'success', '2026-03-23 15:52:09', NULL, NULL),
(46, 93, 'cod', 215000000, 'pending', '2026-03-23 15:52:32', NULL, NULL),
(47, 94, 'fake_vnpay', 6990000, 'success', '2026-03-23 15:55:24', NULL, NULL),
(48, 95, 'cod', 17990000, 'pending', '2026-03-23 16:02:30', NULL, NULL),
(49, 96, 'fake_vnpay', 700000000, 'success', '2026-03-23 16:02:39', NULL, NULL),
(50, 97, 'cod', 215000000, 'pending', '2026-03-27 09:40:16', NULL, NULL),
(51, 98, 'cod', 620000000, 'pending', '2026-03-27 14:11:27', NULL, NULL),
(52, 99, 'cod', 215000000, 'pending', '2026-03-27 14:43:41', NULL, NULL),
(53, 100, 'cod', 15000000, 'pending', '2026-04-01 20:59:41', NULL, NULL),
(54, 101, 'cod', 20000000, 'pending', '2026-04-01 21:18:22', NULL, NULL),
(55, 102, 'fake_vnpay', 15000000, 'success', '2026-04-01 21:19:51', NULL, NULL),
(56, 103, 'cod', 15000000, 'pending', '2026-06-10 15:43:10', NULL, NULL),
(57, 104, 'cod', 499000000, 'pending', '2026-06-19 22:52:16', NULL, NULL),
(58, 107, 'cod', 17990000, 'pending', '2026-07-13 19:14:19', NULL, NULL),
(59, 108, 'cod', 89900000, 'pending', '2026-07-13 19:40:49', NULL, NULL),
(60, 109, 'cod', 89900000, 'pending', '2026-07-14 20:27:15', NULL, NULL),
(62, 111, 'cod', 588900000, 'paid', '2026-07-14 21:09:19', NULL, NULL),
(63, 112, 'cod', 500000000, 'paid', '2026-07-14 21:12:41', NULL, NULL),
(64, 113, 'cod', 179800000, 'paid', '2026-07-14 21:19:47', NULL, NULL),
(65, 114, 'cod', 85000000, 'paid', '2026-07-14 21:30:29', NULL, NULL),
(66, 115, 'cod', 179800000, 'paid', '2026-07-15 23:03:29', NULL, NULL),
(67, 116, 'cod', 998000000, 'paid', '2026-07-15 23:04:58', NULL, NULL),
(68, 117, 'cod', 58000000, 'paid', '2026-08-07 17:05:57', NULL, NULL),
(69, 118, 'fake_vnpay', 500000000, 'success', '2026-08-07 17:07:29', NULL, NULL),
(70, 119, 'cod', 49000000, 'paid', '2026-08-08 14:35:25', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_interactions`
--

DROP TABLE IF EXISTS `user_interactions`;
CREATE TABLE IF NOT EXISTS `user_interactions` (
  `maInteraction` int NOT NULL AUTO_INCREMENT,
  `maTaiKhoan` int NOT NULL,
  `maSanPham` int NOT NULL,
  `soLanXem` int DEFAULT '1',
  `yeuThich` tinyint(1) DEFAULT '0',
  `lienHe` tinyint(1) DEFAULT '0',
  `muaHang` tinyint(1) DEFAULT '0',
  `thoiGianXem` int DEFAULT '0',
  `ngayCapNhat` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`maInteraction`),
  UNIQUE KEY `unique_user_product` (`maTaiKhoan`,`maSanPham`),
  KEY `maSanPham` (`maSanPham`)
) ENGINE=MyISAM AUTO_INCREMENT=94 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_interactions`
--

INSERT INTO `user_interactions` (`maInteraction`, `maTaiKhoan`, `maSanPham`, `soLanXem`, `yeuThich`, `lienHe`, `muaHang`, `thoiGianXem`, `ngayCapNhat`) VALUES
(1, 10002, 101, 3, 1, 0, 1, 0, '2026-07-14 16:26:29'),
(2, 10002, 110, 28, 1, 0, 1, 0, '2026-07-14 16:26:30'),
(3, 10002, 99, 1, 1, 0, 0, 0, '2026-07-14 16:51:43'),
(4, 10002, 109, 1, 1, 0, 0, 0, '2026-07-14 16:56:33'),
(5, 10002, 97, 1, 1, 0, 0, 0, '2026-07-14 16:56:37'),
(6, 10002, 93, 1, 1, 0, 0, 0, '2026-07-14 16:56:39'),
(7, 10002, 92, 7, 1, 0, 0, 0, '2026-07-14 16:56:42'),
(8, 10006, 100, 4, 1, 0, 1, 0, '2026-07-14 21:12:19'),
(9, 10006, 101, 3, 1, 0, 1, 0, '2026-07-14 21:15:30'),
(10, 10006, 110, 17, 1, 0, 1, 0, '2026-07-14 21:19:35'),
(11, 10005, 110, 3, 0, 0, 0, 0, '2026-07-14 21:21:49'),
(12, 10006, 109, 2, 1, 0, 0, 0, '2026-07-14 21:28:47'),
(13, 10006, 99, 6, 1, 0, 1, 0, '2026-07-14 21:28:53'),
(14, 10006, 98, 1, 1, 0, 0, 0, '2026-07-14 21:28:55'),
(15, 10006, 94, 1, 1, 0, 0, 0, '2026-07-14 21:28:57'),
(16, 10006, 92, 1, 1, 0, 0, 0, '2026-07-14 21:28:59'),
(17, 10006, 88, 1, 1, 0, 0, 0, '2026-07-14 21:29:01'),
(18, 10006, 83, 1, 1, 0, 0, 0, '2026-07-14 21:29:10'),
(19, 10006, 82, 1, 0, 0, 0, 0, '2026-07-14 21:29:20'),
(20, 10006, 55, 1, 1, 0, 0, 0, '2026-07-14 21:29:26'),
(21, 10006, 29, 1, 1, 0, 0, 0, '2026-07-14 21:29:40'),
(22, 10006, 46, 1, 1, 0, 0, 0, '2026-07-14 21:30:03'),
(23, 10006, 97, 1, 1, 0, 0, 0, '2026-07-14 21:30:08'),
(24, 10006, 96, 1, 0, 0, 0, 0, '2026-07-14 21:30:13'),
(25, 14, 99, 3, 0, 0, 0, 0, '2026-07-14 21:31:31'),
(26, 10007, 110, 1, 1, 0, 0, 0, '2026-07-14 21:34:33'),
(27, 10007, 109, 1, 0, 0, 0, 0, '2026-07-14 21:34:35'),
(28, 10007, 101, 1, 1, 0, 0, 0, '2026-07-14 21:34:37'),
(29, 10007, 99, 1, 1, 0, 0, 0, '2026-07-14 21:34:40'),
(30, 10007, 98, 2, 1, 0, 0, 0, '2026-07-14 21:34:42'),
(31, 10007, 93, 1, 1, 0, 0, 0, '2026-07-14 21:34:45'),
(32, 10007, 92, 1, 1, 0, 0, 0, '2026-07-14 21:34:47'),
(33, 10007, 90, 2, 1, 0, 0, 0, '2026-07-14 21:34:49'),
(34, 10007, 89, 1, 1, 0, 0, 0, '2026-07-14 21:34:51'),
(35, 10007, 85, 1, 1, 0, 0, 0, '2026-07-14 21:34:54'),
(36, 10007, 87, 1, 1, 0, 0, 0, '2026-07-14 21:34:56'),
(37, 10007, 86, 1, 1, 0, 0, 0, '2026-07-14 21:34:58'),
(38, 10007, 84, 1, 1, 0, 0, 0, '2026-07-14 21:35:00'),
(39, 10007, 91, 1, 1, 0, 0, 0, '2026-07-14 21:35:04'),
(40, 10005, 111, 5, 1, 0, 0, 0, '2026-07-14 22:04:58'),
(41, 10005, 112, 3, 0, 0, 0, 0, '2026-07-14 22:18:09'),
(42, 10006, 112, 1, 0, 0, 0, 0, '2026-07-15 23:05:46'),
(43, 14, 110, 1, 0, 0, 0, 0, '2026-07-15 23:07:21'),
(44, 10006, 111, 1, 0, 0, 0, 0, '2026-07-16 13:26:57'),
(45, 10000, 111, 4, 0, 0, 0, 0, '2026-07-28 23:24:58'),
(46, 10000, 109, 1, 1, 0, 0, 0, '2026-07-28 23:26:30'),
(47, 10000, 13, 1, 1, 0, 0, 0, '2026-07-28 23:26:36'),
(48, 10000, 11, 1, 1, 0, 0, 0, '2026-07-28 23:26:39'),
(49, 10000, 44, 1, 1, 0, 0, 0, '2026-07-28 23:26:42'),
(50, 10000, 91, 2, 1, 0, 0, 0, '2026-07-28 23:34:24'),
(51, 10000, 112, 1, 0, 0, 0, 0, '2026-07-28 23:34:39'),
(52, 10000, 101, 6, 1, 0, 0, 0, '2026-07-28 23:39:46'),
(53, 10000, 90, 1, 0, 0, 0, 0, '2026-07-28 23:39:57'),
(54, 10000, 41, 1, 0, 0, 0, 0, '2026-07-28 23:39:59'),
(55, 10000, 85, 4, 1, 0, 0, 0, '2026-07-28 23:42:18'),
(56, 10000, 84, 3, 1, 0, 0, 0, '2026-07-28 23:42:21'),
(57, 10000, 86, 4, 1, 0, 0, 0, '2026-07-28 23:42:23'),
(58, 10000, 36, 4, 1, 0, 0, 0, '2026-07-28 23:42:25'),
(59, 10000, 58, 1, 0, 0, 0, 0, '2026-07-28 23:43:26'),
(60, 10000, 73, 2, 1, 0, 0, 0, '2026-07-28 23:43:28'),
(61, 10000, 59, 1, 0, 0, 0, 0, '2026-07-28 23:43:29'),
(62, 10000, 60, 1, 0, 0, 0, 0, '2026-07-28 23:43:34'),
(63, 10000, 97, 2, 0, 0, 0, 0, '2026-07-28 23:44:14'),
(64, 10008, 53, 1, 1, 0, 0, 0, '2026-07-29 08:57:25'),
(65, 10008, 87, 1, 0, 0, 0, 0, '2026-07-29 08:57:29'),
(66, 10008, 54, 1, 1, 0, 0, 0, '2026-07-29 08:57:32'),
(67, 10008, 5, 1, 1, 0, 0, 0, '2026-07-29 09:06:32'),
(68, 10008, 101, 1, 0, 0, 0, 0, '2026-07-29 09:06:59'),
(69, 10008, 84, 1, 0, 0, 0, 0, '2026-07-29 09:16:34'),
(70, 10008, 83, 2, 1, 0, 0, 0, '2026-07-29 09:16:43'),
(71, 10008, 92, 9, 0, 0, 0, 0, '2026-07-29 09:20:37'),
(72, 10000, 100, 1, 0, 0, 1, 0, '2026-08-02 13:52:46'),
(73, 10000, 89, 4, 0, 0, 0, 0, '2026-08-02 13:52:56'),
(74, 10000, 87, 1, 1, 0, 0, 0, '2026-08-02 13:54:27'),
(75, 10000, 52, 2, 1, 0, 0, 0, '2026-08-02 13:54:33'),
(76, 10000, 92, 1, 1, 0, 1, 0, '2026-08-02 13:55:06'),
(77, 10000, 1, 1, 1, 0, 0, 0, '2026-08-02 13:55:42'),
(78, 10000, 98, 1, 0, 0, 0, 0, '2026-08-04 20:11:42'),
(79, 10000, 113, 2, 0, 0, 1, 0, '2026-08-08 14:35:25'),
(80, 2, 113, 17, 0, 0, 0, 0, '2026-08-08 15:04:32'),
(81, 1, 109, 13, 0, 0, 0, 0, '2026-09-02 18:47:11'),
(82, 2, 109, 9, 0, 0, 0, 0, '2026-09-02 19:13:04'),
(83, 2, 93, 1, 0, 0, 0, 0, '2026-09-02 19:21:27'),
(84, 2, 101, 3, 0, 0, 0, 0, '2026-09-02 19:47:51'),
(85, 2, 99, 1, 0, 0, 0, 0, '2026-09-02 21:12:01'),
(86, 10010, 113, 2, 0, 0, 0, 0, '2026-09-02 21:16:22'),
(87, 1, 97, 1, 0, 0, 0, 0, '2026-09-02 21:24:40'),
(88, 1, 113, 6, 0, 0, 0, 0, '2026-09-02 21:31:00'),
(89, 2, 103, 1, 0, 0, 0, 0, '2026-09-02 21:49:17'),
(90, 1, 100, 1, 0, 0, 0, 0, '2026-09-03 20:01:29'),
(91, 1, 101, 1, 0, 0, 0, 0, '2026-09-03 20:09:36'),
(92, 9, 101, 4, 0, 0, 0, 0, '2026-09-04 21:08:52'),
(93, 14, 113, 1, 0, 0, 0, 0, '2026-09-04 21:27:30');

-- --------------------------------------------------------

--
-- Table structure for table `yeuthich`
--

DROP TABLE IF EXISTS `yeuthich`;
CREATE TABLE IF NOT EXISTS `yeuthich` (
  `maYeuThich` int NOT NULL AUTO_INCREMENT,
  `maTaiKhoan` int NOT NULL,
  `maSanPham` int NOT NULL,
  `ngayYeuThich` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`maYeuThich`),
  KEY `maTaiKhoan` (`maTaiKhoan`),
  KEY `maSanPham` (`maSanPham`)
) ENGINE=MyISAM AUTO_INCREMENT=76 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `yeuthich`
--

INSERT INTO `yeuthich` (`maYeuThich`, `maTaiKhoan`, `maSanPham`, `ngayYeuThich`) VALUES
(1, 1, 25, '2026-07-12 16:55:45'),
(3, 10004, 109, '2026-07-13 18:33:32'),
(4, 10004, 101, '2026-07-13 18:33:40'),
(5, 10004, 96, '2026-07-13 18:34:12'),
(6, 10004, 85, '2026-07-13 18:34:19'),
(7, 10004, 92, '2026-07-13 18:34:26'),
(8, 10003, 101, '2026-07-13 18:59:19'),
(9, 10003, 99, '2026-07-13 18:59:26'),
(10, 10003, 96, '2026-07-13 18:59:32'),
(11, 10003, 91, '2026-07-13 18:59:43'),
(12, 10003, 59, '2026-07-13 19:00:07'),
(13, 10003, 100, '2026-07-13 19:00:18'),
(14, 10003, 109, '2026-07-13 19:00:22'),
(15, 10003, 92, '2026-07-13 19:00:35'),
(16, 10002, 96, '2026-07-13 19:07:45'),
(17, 10005, 110, '2026-07-13 19:40:09'),
(18, 10002, 110, '2026-07-14 16:51:36'),
(19, 10002, 101, '2026-07-14 16:51:41'),
(20, 10002, 99, '2026-07-14 16:51:43'),
(21, 10002, 109, '2026-07-14 16:56:35'),
(22, 10002, 97, '2026-07-14 16:56:38'),
(23, 10002, 93, '2026-07-14 16:56:40'),
(24, 10002, 92, '2026-07-14 16:56:43'),
(55, 10006, 110, '2026-07-15 23:03:18'),
(26, 10006, 109, '2026-07-14 21:28:48'),
(27, 10006, 101, '2026-07-14 21:28:50'),
(28, 10006, 100, '2026-07-14 21:28:52'),
(29, 10006, 99, '2026-07-14 21:28:54'),
(30, 10006, 98, '2026-07-14 21:28:56'),
(31, 10006, 94, '2026-07-14 21:28:58'),
(32, 10006, 92, '2026-07-14 21:29:00'),
(33, 10006, 88, '2026-07-14 21:29:03'),
(34, 10006, 83, '2026-07-14 21:29:11'),
(35, 10006, 55, '2026-07-14 21:29:27'),
(36, 10006, 29, '2026-07-14 21:29:41'),
(37, 10006, 46, '2026-07-14 21:30:05'),
(38, 10006, 97, '2026-07-14 21:30:09'),
(39, 10007, 110, '2026-07-14 21:34:34'),
(40, 10007, 101, '2026-07-14 21:34:39'),
(41, 10007, 99, '2026-07-14 21:34:41'),
(42, 10007, 98, '2026-07-14 21:34:43'),
(43, 10007, 93, '2026-07-14 21:34:46'),
(44, 10007, 92, '2026-07-14 21:34:48'),
(45, 10007, 90, '2026-07-14 21:34:50'),
(46, 10007, 89, '2026-07-14 21:34:52'),
(47, 10007, 85, '2026-07-14 21:34:54'),
(48, 10007, 87, '2026-07-14 21:34:57'),
(49, 10007, 86, '2026-07-14 21:34:59'),
(50, 10007, 84, '2026-07-14 21:35:01'),
(51, 10007, 91, '2026-07-14 21:35:05'),
(52, 10005, 111, '2026-07-14 22:09:05'),
(61, 10000, 91, '2026-07-28 23:34:26'),
(57, 10000, 109, '2026-07-28 23:26:35'),
(58, 10000, 13, '2026-07-28 23:26:38'),
(59, 10000, 11, '2026-07-28 23:26:41'),
(60, 10000, 44, '2026-07-28 23:26:44'),
(62, 10000, 85, '2026-07-28 23:42:42'),
(63, 10000, 36, '2026-07-28 23:42:45'),
(64, 10000, 84, '2026-07-28 23:42:50'),
(65, 10000, 73, '2026-07-28 23:43:32'),
(66, 10008, 53, '2026-07-29 08:57:27'),
(67, 10008, 54, '2026-07-29 08:57:34'),
(68, 10008, 5, '2026-07-29 09:06:35'),
(69, 10008, 83, '2026-07-29 09:16:45'),
(70, 10000, 87, '2026-08-02 13:54:29'),
(71, 10000, 52, '2026-08-02 13:54:36'),
(72, 10000, 101, '2026-08-02 13:55:02'),
(73, 10000, 92, '2026-08-02 13:55:07'),
(74, 10000, 1, '2026-08-02 13:55:43'),
(75, 10000, 86, '2026-08-04 20:09:08');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `sanpham`
--
ALTER TABLE `sanpham` ADD FULLTEXT KEY `idx_fulltext` (`tenSanPham`,`moTa`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `baocao_shop`
--
ALTER TABLE `baocao_shop`
  ADD CONSTRAINT `fk_baocao_nguoi` FOREIGN KEY (`maNguoiBaoCao`) REFERENCES `taikhoan` (`maTaiKhoan`),
  ADD CONSTRAINT `fk_baocao_shop` FOREIGN KEY (`maShop`) REFERENCES `taikhoan` (`maTaiKhoan`);

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
-- Constraints for table `donhang_shop`
--
ALTER TABLE `donhang_shop`
  ADD CONSTRAINT `donhang_shop_ibfk_1` FOREIGN KEY (`maDonHang`) REFERENCES `donhang` (`maDonHang`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `donhang_shop_ibfk_2` FOREIGN KEY (`maNguoiMua`) REFERENCES `taikhoan` (`maTaiKhoan`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `donhang_shop_ibfk_3` FOREIGN KEY (`maNguoiBan`) REFERENCES `taikhoan` (`maTaiKhoan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `giohang`
--
ALTER TABLE `giohang`
  ADD CONSTRAINT `giohang_ibfk_1` FOREIGN KEY (`maNguoiDung`) REFERENCES `taikhoan` (`maTaiKhoan`) ON DELETE CASCADE,
  ADD CONSTRAINT `giohang_ibfk_2` FOREIGN KEY (`maSanPham`) REFERENCES `sanpham` (`maSanPham`) ON DELETE CASCADE;

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
