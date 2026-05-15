-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 15, 2026 at 03:46 PM
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
-- Database: `mckodevecommerce`
--

-- --------------------------------------------------------

--
-- Table structure for table `addition_mm_pages`
--

DROP TABLE IF EXISTS `addition_mm_pages`;
CREATE TABLE IF NOT EXISTS `addition_mm_pages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(255) NOT NULL,
  `tb` varchar(255) DEFAULT 'panel_mm_pages',
  `tb_link` varchar(255) DEFAULT NULL,
  `input_name` varchar(255) DEFAULT NULL,
  `input_link` varchar(255) DEFAULT NULL,
  `input_order` int DEFAULT '0',
  `visibility` varchar(50) DEFAULT 'show',
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  `created_by` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `addition_mm_pages`
--

INSERT INTO `addition_mm_pages` (`id`, `hash_id`, `tb`, `tb_link`, `input_name`, `input_link`, `input_order`, `visibility`, `date_created`, `time_created`, `created_by`) VALUES
(1, 'mmapg001', 'panel_mm_pages', 'mmpg003', 'NetSuite Sprint', '/services/mmns001/netsuite-reporting-clarity-sprint', 1, 'show', '2026-05-14', '08:17:39', 'system'),
(2, 'mmapg002', 'panel_mm_pages', 'mmpg003', 'DCAT Method', '/services/mmdc001/decentralized-a-team-method', 2, 'show', '2026-05-14', '08:17:39', 'system'),
(3, 'mmapg003', 'panel_mm_pages', 'mmpg003', 'Fractional CTO', '/services/mmcto01/fractional-cto-services', 3, 'show', '2026-05-14', '08:17:39', 'system');

-- --------------------------------------------------------

--
-- Table structure for table `addition_product_images`
--

DROP TABLE IF EXISTS `addition_product_images`;
CREATE TABLE IF NOT EXISTS `addition_product_images` (
  `id` int NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(255) NOT NULL,
  `tb` varchar(255) DEFAULT 'panel_products',
  `tb_link` varchar(255) DEFAULT NULL,
  `input_order` int DEFAULT '0',
  `image_1` text,
  `visibility` varchar(50) DEFAULT 'show',
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  `created_by` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `addition_product_images`
--

INSERT INTO `addition_product_images` (`id`, `hash_id`, `tb`, `tb_link`, `input_order`, `image_1`, `visibility`, `date_created`, `time_created`, `created_by`) VALUES
(1, 'img001', 'panel_products', 'mmhs001', 1, '/assets/img/products/hydrasilk-2.webp', 'show', '2026-05-14', '12:51:54', 'system'),
(2, 'img002', 'panel_products', 'mmhs001', 2, '/assets/img/products/hydrasilk-3.webp', 'show', '2026-05-14', '12:51:54', 'system');

-- --------------------------------------------------------

--
-- Table structure for table `addition_product_variants`
--

DROP TABLE IF EXISTS `addition_product_variants`;
CREATE TABLE IF NOT EXISTS `addition_product_variants` (
  `id` int NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(255) NOT NULL,
  `tb` varchar(255) DEFAULT 'panel_products',
  `tb_link` varchar(255) DEFAULT NULL,
  `input_name` varchar(100) DEFAULT 'Size',
  `input_value` varchar(100) DEFAULT NULL,
  `input_price` varchar(50) DEFAULT NULL,
  `input_stock` varchar(20) DEFAULT '999',
  `input_sku` varchar(100) DEFAULT NULL,
  `input_order` int DEFAULT '0',
  `visibility` varchar(50) DEFAULT 'show',
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  `created_by` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `addition_product_variants`
--

INSERT INTO `addition_product_variants` (`id`, `hash_id`, `tb`, `tb_link`, `input_name`, `input_value`, `input_price`, `input_stock`, `input_sku`, `input_order`, `visibility`, `date_created`, `time_created`, `created_by`) VALUES
(1, 'vsize_mmns001_large', 'panel_products', 'mmns001', 'Size', '30ml', '50.00', '999', 'VNR-SRM-001-SZ-LA', 1, 'show', '2026-05-14', '22:27:17', 'system'),
(2, 'vsize_mmns001_xlarge', 'panel_products', 'mmns001', 'Size', '50ml', '50.00', '999', 'VNR-SRM-001-SZ-XL', 2, 'show', '2026-05-14', '22:27:17', 'system'),
(3, 'vcol_mmns001_red', 'panel_products', 'mmns001', 'Skin Type', 'Normal', '50.00', '999', 'VNR-SRM-001-CL-RE', 3, 'show', '2026-05-14', '22:27:17', 'system'),
(4, 'vcol_mmns001_blue', 'panel_products', 'mmns001', 'Skin Type', 'Oily', '50.00', '999', 'VNR-SRM-001-CL-BL', 4, 'show', '2026-05-14', '22:27:17', 'system'),
(5, 'vcol_mmns001_green', 'panel_products', 'mmns001', 'Skin Type', 'Dry', '50.00', '999', 'VNR-SRM-001-CL-GR', 5, 'show', '2026-05-14', '22:27:17', 'system'),
(6, 'vcol_mmns001_yellow', 'panel_products', 'mmns001', 'Skin Type', 'Sensitive', '50.00', '999', 'VNR-SRM-001-CL-YE', 6, 'show', '2026-05-14', '22:27:17', 'system'),
(7, 'vsize_mmac001_large', 'panel_products', 'mmac001', 'Size', '30ml', '30.00', '999', 'VNR-EYE-001-SZ-LA', 7, 'show', '2026-05-14', '22:27:17', 'system'),
(8, 'vsize_mmac001_xlarge', 'panel_products', 'mmac001', 'Size', '50ml', '30.00', '999', 'VNR-EYE-001-SZ-XL', 8, 'show', '2026-05-14', '22:27:17', 'system'),
(9, 'vcol_mmac001_red', 'panel_products', 'mmac001', 'Skin Type', 'Normal', '30.00', '999', 'VNR-EYE-001-CL-RE', 9, 'show', '2026-05-14', '22:27:17', 'system'),
(10, 'vcol_mmac001_blue', 'panel_products', 'mmac001', 'Skin Type', 'Oily', '30.00', '999', 'VNR-EYE-001-CL-BL', 10, 'show', '2026-05-14', '22:27:17', 'system'),
(11, 'vcol_mmac001_green', 'panel_products', 'mmac001', 'Skin Type', 'Dry', '30.00', '999', 'VNR-EYE-001-CL-GR', 11, 'show', '2026-05-14', '22:27:17', 'system'),
(12, 'vcol_mmac001_yellow', 'panel_products', 'mmac001', 'Skin Type', 'Sensitive', '30.00', '999', 'VNR-EYE-001-CL-YE', 12, 'show', '2026-05-14', '22:27:17', 'system'),
(13, 'vsize_mmgc001_large', 'panel_products', 'mmgc001', 'Size', '30ml', '30.00', '999', 'VNR-CLN-001-SZ-LA', 13, 'show', '2026-05-14', '22:27:17', 'system'),
(14, 'vsize_mmgc001_xlarge', 'panel_products', 'mmgc001', 'Size', '50ml', '30.00', '999', 'VNR-CLN-001-SZ-XL', 14, 'show', '2026-05-14', '22:27:17', 'system'),
(15, 'vcol_mmgc001_red', 'panel_products', 'mmgc001', 'Skin Type', 'Normal', '30.00', '999', 'VNR-CLN-001-CL-RE', 15, 'show', '2026-05-14', '22:27:17', 'system'),
(16, 'vcol_mmgc001_blue', 'panel_products', 'mmgc001', 'Skin Type', 'Oily', '30.00', '999', 'VNR-CLN-001-CL-BL', 16, 'show', '2026-05-14', '22:27:17', 'system'),
(17, 'vcol_mmgc001_green', 'panel_products', 'mmgc001', 'Skin Type', 'Dry', '30.00', '999', 'VNR-CLN-001-CL-GR', 17, 'show', '2026-05-14', '22:27:17', 'system'),
(18, 'vcol_mmgc001_yellow', 'panel_products', 'mmgc001', 'Skin Type', 'Sensitive', '30.00', '999', 'VNR-CLN-001-CL-YE', 18, 'show', '2026-05-14', '22:27:17', 'system'),
(19, 'vsize_mmhs001_large', 'panel_products', 'mmhs001', 'Size', '30ml', '70.00', '999', 'VNR-MOS-001-SZ-LA', 19, 'show', '2026-05-14', '22:27:17', 'system'),
(20, 'vsize_mmhs001_xlarge', 'panel_products', 'mmhs001', 'Size', '50ml', '70.00', '999', 'VNR-MOS-001-SZ-XL', 20, 'show', '2026-05-14', '22:27:17', 'system'),
(21, 'vcol_mmhs001_red', 'panel_products', 'mmhs001', 'Skin Type', 'Normal', '70.00', '999', 'VNR-MOS-001-CL-RE', 21, 'show', '2026-05-14', '22:27:17', 'system'),
(22, 'vcol_mmhs001_blue', 'panel_products', 'mmhs001', 'Skin Type', 'Oily', '70.00', '999', 'VNR-MOS-001-CL-BL', 22, 'show', '2026-05-14', '22:27:17', 'system'),
(23, 'vcol_mmhs001_green', 'panel_products', 'mmhs001', 'Skin Type', 'Dry', '70.00', '999', 'VNR-MOS-001-CL-GR', 23, 'show', '2026-05-14', '22:27:17', 'system'),
(24, 'vcol_mmhs001_yellow', 'panel_products', 'mmhs001', 'Skin Type', 'Sensitive', '70.00', '999', 'VNR-MOS-001-CL-YE', 24, 'show', '2026-05-14', '22:27:17', 'system'),
(25, 'vsize_mmvc001_large', 'panel_products', 'mmvc001', 'Size', '30ml', '65.00', '999', 'VNR-NIT-001-SZ-LA', 25, 'show', '2026-05-14', '22:27:17', 'system'),
(26, 'vsize_mmvc001_xlarge', 'panel_products', 'mmvc001', 'Size', '50ml', '65.00', '999', 'VNR-NIT-001-SZ-XL', 26, 'show', '2026-05-14', '22:27:17', 'system'),
(27, 'vcol_mmvc001_red', 'panel_products', 'mmvc001', 'Skin Type', 'Normal', '65.00', '999', 'VNR-NIT-001-CL-RE', 27, 'show', '2026-05-14', '22:27:17', 'system'),
(28, 'vcol_mmvc001_blue', 'panel_products', 'mmvc001', 'Skin Type', 'Oily', '65.00', '999', 'VNR-NIT-001-CL-BL', 28, 'show', '2026-05-14', '22:27:17', 'system'),
(29, 'vcol_mmvc001_green', 'panel_products', 'mmvc001', 'Skin Type', 'Dry', '65.00', '999', 'VNR-NIT-001-CL-GR', 29, 'show', '2026-05-14', '22:27:17', 'system'),
(30, 'vcol_mmvc001_yellow', 'panel_products', 'mmvc001', 'Skin Type', 'Sensitive', '65.00', '999', 'VNR-NIT-001-CL-YE', 30, 'show', '2026-05-14', '22:27:17', 'system'),
(31, 'vsize_mmld001_large', 'panel_products', 'mmld001', 'Size', '30ml', '60.00', '999', 'VNR-DAY-001-SZ-LA', 31, 'show', '2026-05-14', '22:27:17', 'system'),
(32, 'vsize_mmld001_xlarge', 'panel_products', 'mmld001', 'Size', '50ml', '60.00', '999', 'VNR-DAY-001-SZ-XL', 32, 'show', '2026-05-14', '22:27:17', 'system'),
(33, 'vcol_mmld001_red', 'panel_products', 'mmld001', 'Skin Type', 'Normal', '60.00', '999', 'VNR-DAY-001-CL-RE', 33, 'show', '2026-05-14', '22:27:17', 'system'),
(34, 'vcol_mmld001_blue', 'panel_products', 'mmld001', 'Skin Type', 'Oily', '60.00', '999', 'VNR-DAY-001-CL-BL', 34, 'show', '2026-05-14', '22:27:17', 'system'),
(35, 'vcol_mmld001_green', 'panel_products', 'mmld001', 'Skin Type', 'Dry', '60.00', '999', 'VNR-DAY-001-CL-GR', 35, 'show', '2026-05-14', '22:27:17', 'system'),
(36, 'vcol_mmld001_yellow', 'panel_products', 'mmld001', 'Skin Type', 'Sensitive', '60.00', '999', 'VNR-DAY-001-CL-YE', 36, 'show', '2026-05-14', '22:27:17', 'system'),
(37, 'vsize_mmbs001_large', 'panel_products', 'mmbs001', 'Size', '30ml', '55.00', '999', 'VNR-EYS-001-SZ-LA', 37, 'show', '2026-05-14', '22:27:17', 'system'),
(38, 'vsize_mmbs001_xlarge', 'panel_products', 'mmbs001', 'Size', '50ml', '55.00', '999', 'VNR-EYS-001-SZ-XL', 38, 'show', '2026-05-14', '22:27:17', 'system'),
(39, 'vcol_mmbs001_red', 'panel_products', 'mmbs001', 'Skin Type', 'Normal', '55.00', '999', 'VNR-EYS-001-CL-RE', 39, 'show', '2026-05-14', '22:27:17', 'system'),
(40, 'vcol_mmbs001_blue', 'panel_products', 'mmbs001', 'Skin Type', 'Oily', '55.00', '999', 'VNR-EYS-001-CL-BL', 40, 'show', '2026-05-14', '22:27:17', 'system'),
(41, 'vcol_mmbs001_green', 'panel_products', 'mmbs001', 'Skin Type', 'Dry', '55.00', '999', 'VNR-EYS-001-CL-GR', 41, 'show', '2026-05-14', '22:27:17', 'system'),
(42, 'vcol_mmbs001_yellow', 'panel_products', 'mmbs001', 'Skin Type', 'Sensitive', '55.00', '999', 'VNR-EYS-001-CL-YE', 42, 'show', '2026-05-14', '22:27:17', 'system'),
(43, 'vsize_mmfc001_large', 'panel_products', 'mmfc001', 'Size', '30ml', '28.00', '999', 'VNR-FCL-001-SZ-LA', 43, 'show', '2026-05-14', '22:27:17', 'system'),
(44, 'vsize_mmfc001_xlarge', 'panel_products', 'mmfc001', 'Size', '50ml', '28.00', '999', 'VNR-FCL-001-SZ-XL', 44, 'show', '2026-05-14', '22:27:17', 'system'),
(45, 'vcol_mmfc001_red', 'panel_products', 'mmfc001', 'Skin Type', 'Normal', '28.00', '999', 'VNR-FCL-001-CL-RE', 45, 'show', '2026-05-14', '22:27:17', 'system'),
(46, 'vcol_mmfc001_blue', 'panel_products', 'mmfc001', 'Skin Type', 'Oily', '28.00', '999', 'VNR-FCL-001-CL-BL', 46, 'show', '2026-05-14', '22:27:17', 'system'),
(47, 'vcol_mmfc001_green', 'panel_products', 'mmfc001', 'Skin Type', 'Dry', '28.00', '999', 'VNR-FCL-001-CL-GR', 47, 'show', '2026-05-14', '22:27:17', 'system'),
(48, 'vcol_mmfc001_yellow', 'panel_products', 'mmfc001', 'Skin Type', 'Sensitive', '28.00', '999', 'VNR-FCL-001-CL-YE', 48, 'show', '2026-05-14', '22:27:17', 'system'),
(49, 'vsize_mmdh001_large', 'panel_products', 'mmdh001', 'Size', '30ml', '75.00', '999', 'VNR-DHY-001-SZ-LA', 49, 'show', '2026-05-14', '22:27:17', 'system'),
(50, 'vsize_mmdh001_xlarge', 'panel_products', 'mmdh001', 'Size', '50ml', '75.00', '999', 'VNR-DHY-001-SZ-XL', 50, 'show', '2026-05-14', '22:27:17', 'system'),
(51, 'vcol_mmdh001_red', 'panel_products', 'mmdh001', 'Skin Type', 'Normal', '75.00', '999', 'VNR-DHY-001-CL-RE', 51, 'show', '2026-05-14', '22:27:17', 'system'),
(52, 'vcol_mmdh001_blue', 'panel_products', 'mmdh001', 'Skin Type', 'Oily', '75.00', '999', 'VNR-DHY-001-CL-BL', 52, 'show', '2026-05-14', '22:27:17', 'system'),
(53, 'vcol_mmdh001_green', 'panel_products', 'mmdh001', 'Skin Type', 'Dry', '75.00', '999', 'VNR-DHY-001-CL-GR', 53, 'show', '2026-05-14', '22:27:17', 'system'),
(54, 'vcol_mmdh001_yellow', 'panel_products', 'mmdh001', 'Skin Type', 'Sensitive', '75.00', '999', 'VNR-DHY-001-CL-YE', 54, 'show', '2026-05-14', '22:27:17', 'system'),
(55, 'vsize_mmpp001_large', 'panel_products', 'mmpp001', 'Size', '30ml', '45.00', '999', 'VNR-POR-001-SZ-LA', 55, 'show', '2026-05-14', '22:27:17', 'system'),
(56, 'vsize_mmpp001_xlarge', 'panel_products', 'mmpp001', 'Size', '50ml', '45.00', '999', 'VNR-POR-001-SZ-XL', 56, 'show', '2026-05-14', '22:27:17', 'system'),
(57, 'vcol_mmpp001_red', 'panel_products', 'mmpp001', 'Skin Type', 'Normal', '45.00', '999', 'VNR-POR-001-CL-RE', 57, 'show', '2026-05-14', '22:27:17', 'system'),
(58, 'vcol_mmpp001_blue', 'panel_products', 'mmpp001', 'Skin Type', 'Oily', '45.00', '999', 'VNR-POR-001-CL-BL', 58, 'show', '2026-05-14', '22:27:17', 'system'),
(59, 'vcol_mmpp001_green', 'panel_products', 'mmpp001', 'Skin Type', 'Dry', '45.00', '999', 'VNR-POR-001-CL-GR', 59, 'show', '2026-05-14', '22:27:17', 'system'),
(60, 'vcol_mmpp001_yellow', 'panel_products', 'mmpp001', 'Skin Type', 'Sensitive', '45.00', '999', 'VNR-POR-001-CL-YE', 60, 'show', '2026-05-14', '22:27:17', 'system'),
(61, 'vsize_mmmc001_large', 'panel_products', 'mmmc001', 'Size', '30ml', '32.00', '999', 'VNR-MCL-001-SZ-LA', 61, 'show', '2026-05-14', '22:27:17', 'system'),
(62, 'vsize_mmmc001_xlarge', 'panel_products', 'mmmc001', 'Size', '50ml', '32.00', '999', 'VNR-MCL-001-SZ-XL', 62, 'show', '2026-05-14', '22:27:17', 'system'),
(63, 'vcol_mmmc001_red', 'panel_products', 'mmmc001', 'Skin Type', 'Normal', '32.00', '999', 'VNR-MCL-001-CL-RE', 63, 'show', '2026-05-14', '22:27:17', 'system'),
(64, 'vcol_mmmc001_blue', 'panel_products', 'mmmc001', 'Skin Type', 'Oily', '32.00', '999', 'VNR-MCL-001-CL-BL', 64, 'show', '2026-05-14', '22:27:17', 'system'),
(65, 'vcol_mmmc001_green', 'panel_products', 'mmmc001', 'Skin Type', 'Dry', '32.00', '999', 'VNR-MCL-001-CL-GR', 65, 'show', '2026-05-14', '22:27:17', 'system'),
(66, 'vcol_mmmc001_yellow', 'panel_products', 'mmmc001', 'Skin Type', 'Sensitive', '32.00', '999', 'VNR-MCL-001-CL-YE', 66, 'show', '2026-05-14', '22:27:17', 'system'),
(67, 'vsize_mmne001_large', 'panel_products', 'mmne001', 'Size', '30ml', '40.00', '999', 'VNR-NEY-001-SZ-LA', 67, 'show', '2026-05-14', '22:27:17', 'system'),
(68, 'vsize_mmne001_xlarge', 'panel_products', 'mmne001', 'Size', '50ml', '40.00', '999', 'VNR-NEY-001-SZ-XL', 68, 'show', '2026-05-14', '22:27:17', 'system'),
(69, 'vcol_mmne001_red', 'panel_products', 'mmne001', 'Skin Type', 'Normal', '40.00', '999', 'VNR-NEY-001-CL-RE', 69, 'show', '2026-05-14', '22:27:17', 'system'),
(70, 'vcol_mmne001_blue', 'panel_products', 'mmne001', 'Skin Type', 'Oily', '40.00', '999', 'VNR-NEY-001-CL-BL', 70, 'show', '2026-05-14', '22:27:17', 'system'),
(71, 'vcol_mmne001_green', 'panel_products', 'mmne001', 'Skin Type', 'Dry', '40.00', '999', 'VNR-NEY-001-CL-GR', 71, 'show', '2026-05-14', '22:27:17', 'system'),
(72, 'vcol_mmne001_yellow', 'panel_products', 'mmne001', 'Skin Type', 'Sensitive', '40.00', '999', 'VNR-NEY-001-CL-YE', 72, 'show', '2026-05-14', '22:27:17', 'system');

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `hash` varchar(255) NOT NULL,
  `hash_id` varchar(255) NOT NULL,
  `position` varchar(255) DEFAULT NULL,
  `phone_number` varchar(255) DEFAULT NULL,
  `facebook_link` varchar(255) DEFAULT NULL,
  `twitter_link` varchar(255) DEFAULT NULL,
  `linkedin_link` varchar(255) DEFAULT NULL,
  `image_2` text,
  `image_1` text,
  `time_created` time NOT NULL,
  `date_created` date NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `last_logout` datetime DEFAULT NULL,
  `login_status` varchar(255) DEFAULT NULL,
  `level` varchar(255) DEFAULT NULL,
  `verification` varchar(255) DEFAULT NULL,
  `profile_status` varchar(255) DEFAULT NULL,
  `user_status` varchar(255) DEFAULT NULL,
  `defaulted` int DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `firstname`, `lastname`, `email`, `hash`, `hash_id`, `position`, `phone_number`, `facebook_link`, `twitter_link`, `linkedin_link`, `image_2`, `image_1`, `time_created`, `date_created`, `last_login`, `last_logout`, `login_status`, `level`, `verification`, `profile_status`, `user_status`, `defaulted`, `created_by`) VALUES
(7, 'Banji', 'Akole', 'banjimayowa@gmail.com', '$2y$10$01gLmVPQOYgJRfhBw.d1IOGPUdQ0DzoC/NU4yUgKYOzwI8OoWKHXq', 'j90819542aBn72i', '555666777888999000', NULL, NULL, NULL, NULL, 'b12b9681-2bdc-4237-bfa1-51db8b8c2d81', '1545335942mailIMG-20181022-WA0003.jpg', '14:25:12', '2018-02-28', '2020-03-15 17:31:46', '2019-01-01 19:53:53', 'Logged In', 'MASTER', '1', NULL, '1', NULL, NULL),
(35, 'Abayomi', 'Sarumi', 'aatsarumi@gmail.com', '$2y$10$R0xO.ooeAZX2AHtFC7NZkeiR9Yc3rWGUqtlKWPhwTQex/XMEw1yC2', '96b78075a0oim85ay', '555666777888999000', '8037455296', NULL, NULL, NULL, NULL, NULL, '20:37:18', '2019-08-04', '2019-08-04 21:01:35', NULL, 'Logged In', 'MASTER', '1', NULL, '1', NULL, NULL),
(36, 'Mckodev', 'Project Manager', 'pm@mckodev', '$2y$10$5qbZZ4cTe3KpAp7dAnkjwOpnGvFLHEGi0kHUi9xehYx1AzNJUJWEW', '15847344529ve54312633d9mkco', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '20:00:52', '2020-03-20', NULL, NULL, NULL, '3', '1', NULL, '1', NULL, NULL),
(40, 'Bello', 'Afeez', 'belloafeez7@gmail.com', '$2y$10$rge1Plyc0tWWEEPIDoZFMuQJQzyCHetcOszQdoW/PKtjIPDvXpzqe', '1590537355141l896bl585e1o', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '23:55:55', '2020-05-26', NULL, NULL, NULL, '3', '1', NULL, '1', NULL, NULL),
(41, 'Tolu', 'Akintayo', 'tolubama@gmail.com', '$2y$10$zbI3i.Z07nOK/8NPk63gaeV6t1i3n0s8nqealpGQbnfV4GjzSV3iu', '159647435833784tl257u83o', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '17:05:58', '2020-08-03', NULL, NULL, NULL, 'MASTER', '1', NULL, '1', NULL, NULL),
(42, 'Ajewole', 'Foyinsayemi', 'ajewolefoyin30@gmail.com', '$2y$10$hRktzJzJQLb6YqZSTVhtNeokHZq8l97xX7JuQostZrP4nlen0uvVe', '1602674515ew9j215a2o3l338e3', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '11:21:55', '2020-10-14', NULL, NULL, NULL, '3', '1', NULL, '1', NULL, NULL),
(44, 'Adewumi', 'Oba', 'oluwadunsinoba@gmail.com', '$2y$10$5XWkVaiiRjoLLnN0ieHKqOKPbhRPj/0E9Nei1eIqfu9ecJ.J8baqq', '16566034767de2i5u9w16460m5a', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '15:37:56', '2022-06-30', NULL, NULL, NULL, '3', '1', NULL, '1', NULL, NULL),
(45, 'System', 'Admin', 'mckodev@admin', '$2y$10$uTyP5Zt/khiX/iG/QchtL.qynCgjlKt9CvtBrDl3HwT9q8gqMFyrC', 'mckodev_1767617591', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '12:53:11', '2026-01-05', NULL, NULL, NULL, 'MASTER', '1', NULL, '1', NULL, '1'),
(46, 'Easy Events Nigeria', 'Easy Events Nigeria', 'easyevents.ng@gmail.com', '$2y$10$qb1hqAZ9qMMdPFwYuld8VO2FMygLaYqooLmGosvnmqOS1.9VnaWRi', '1767617572229195', '555666777888999000', NULL, NULL, NULL, NULL, NULL, '', '12:53:11', '2026-01-05', NULL, NULL, NULL, 'MASTER', '1', NULL, '1', NULL, '1');

-- --------------------------------------------------------

--
-- Table structure for table `admin_auth`
--

DROP TABLE IF EXISTS `admin_auth`;
CREATE TABLE IF NOT EXISTS `admin_auth` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `auth` varchar(225) DEFAULT NULL,
  `created_by` varchar(225) DEFAULT NULL,
  `used_by` varchar(225) DEFAULT NULL,
  `date_created` date DEFAULT NULL,
  `time_created` time DEFAULT NULL,
  `hash_id` varchar(225) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin_auth`
--

INSERT INTO `admin_auth` (`id`, `auth`, `created_by`, `used_by`, `date_created`, `time_created`, `hash_id`) VALUES
(39, '191138', '1565905740k783la6e402o033', NULL, '2020-01-08', '10:33:09', NULL),
(40, '446358', '1565905740k783la6e402o033', NULL, '2020-01-08', '17:32:19', NULL),
(41, '986659', 'j90819542aBn72i', '15820373186h5mie15l1a6c3274', '2020-02-07', '15:17:28', NULL),
(42, '729912', 'j90819542aBn72i', '1582040299142aoai8478bym978', '2020-02-18', '13:59:17', NULL),
(43, '798371', 'j90819542aBn72i', '15847344529ve54312633d9mkco', '2020-03-20', '19:59:51', NULL),
(44, '217393', 'j90819542aBn72i', '15878996009ey86a3d9on55t948u', '2020-04-26', '11:09:57', NULL),
(45, '286197', 'j90819542aBn72i', '1587900415968505p5m273', '2020-04-26', '11:11:22', NULL),
(46, '619362', 'j90819542aBn72i', NULL, '2020-05-24', '16:48:43', NULL),
(47, '883224', 'j90819542aBn72i', NULL, '2020-05-24', '17:25:37', NULL),
(48, '452916', 'j90819542aBn72i', '1590421922e4o782692adg4n09ur', '2020-05-25', '15:38:29', NULL),
(49, '127052', 'j90819542aBn72i', '1590440727e51o1aay3if1u6d75lnw-e0i0mi', '2020-05-25', '16:53:50', NULL),
(50, '847282', 'j90819542aBn72i', NULL, '2020-05-25', '22:42:13', NULL),
(51, '632590', 'j90819542aBn72i', '1590537355141l896bl585e1o', '2020-05-26', '23:48:14', NULL),
(52, '134052', 'j90819542aBn72i', NULL, '2020-05-26', '23:48:48', NULL),
(53, '618225', 'j90819542aBn72i', NULL, '2020-08-03', '17:05:19', NULL),
(54, '458506', 'j90819542aBn72i', '159647435833784tl257u83o', '2020-08-03', '17:05:20', NULL),
(55, '871811', 'j90819542aBn72i', '1602674515ew9j215a2o3l338e3', '2020-10-14', '10:59:15', NULL),
(56, '475618', '159647435833784tl257u83o', '1656600963233dmiw3ae40717u', '2022-06-30', '14:48:21', NULL),
(57, '936689', '159647435833784tl257u83o', '16566034767de2i5u9w16460m5a', '2022-06-30', '15:36:21', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `admin_password_resets`
--

DROP TABLE IF EXISTS `admin_password_resets`;
CREATE TABLE IF NOT EXISTS `admin_password_resets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

DROP TABLE IF EXISTS `images`;
CREATE TABLE IF NOT EXISTS `images` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `image_hash_id` varchar(225) DEFAULT NULL,
  `asset_hash_id` varchar(255) DEFAULT NULL,
  `image_1` text,
  `date_created` date DEFAULT NULL,
  `time_created` time DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `hash_id` varchar(225) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `images`
--

INSERT INTO `images` (`id`, `image_hash_id`, `asset_hash_id`, `image_1`, `date_created`, `time_created`, `created_by`, `hash_id`) VALUES
(25, 'IMG_17677752051193871721', 'nhilwhgpw', 'https://mck-admc.s3.amazonaws.com/easyeven7977525/uploads/2026/01/07/17677752045283580WhatsApp_Image_2026_01_07_at_9.23.40_AM.jpeg', '2026-01-07', '08:40:05', NULL, NULL),
(27, 'IMG_17689932982108670764', 'nhilwhgpw', 'https://mck-admc.s3.amazonaws.com/easyeven7977525/uploads/2026/01/21/17689932972199126WhatsApp_Image_2026_01_07_at_9.23.32_AM.jpeg', '2026-01-21', '11:01:38', NULL, NULL),
(28, 'IMG_17689932981483460530', 'nhilwhgpw', 'https://mck-admc.s3.amazonaws.com/easyeven7977525/uploads/2026/01/21/17689932982199126WhatsApp_Image_2026_01_07_at_9.23.40_AM.jpeg', '2026-01-21', '11:01:38', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `panel_about_faqs`
--

DROP TABLE IF EXISTS `panel_about_faqs`;
CREATE TABLE IF NOT EXISTS `panel_about_faqs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(255) NOT NULL,
  `input_question` varchar(255) DEFAULT NULL,
  `text_answer` text,
  `input_order` int DEFAULT '0',
  `visibility` varchar(50) DEFAULT 'show',
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  `created_by` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `panel_about_faqs`
--

INSERT INTO `panel_about_faqs` (`id`, `hash_id`, `input_question`, `text_answer`, `input_order`, `visibility`, `date_created`, `time_created`, `created_by`) VALUES
(1, 'faq001', 'Can I use your products if I have sensitive skin?', 'Yes, our products are formulated with gentle, natural ingredients specifically designed to be safe for sensitive skin.', 1, 'show', '2026-05-15', '00:09:49', 'system'),
(2, 'faq002', 'How long does it take to see results?', 'Most customers see visible improvements in skin texture and hydration within just 2 weeks of consistent use.', 2, 'show', '2026-05-15', '00:09:49', 'system');

-- --------------------------------------------------------

--
-- Table structure for table `panel_about_team`
--

DROP TABLE IF EXISTS `panel_about_team`;
CREATE TABLE IF NOT EXISTS `panel_about_team` (
  `id` int NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(255) NOT NULL,
  `input_name` varchar(255) DEFAULT NULL,
  `input_role` varchar(255) DEFAULT NULL,
  `image_1` text,
  `input_order` int DEFAULT '0',
  `visibility` varchar(50) DEFAULT 'show',
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  `created_by` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `panel_about_team`
--

INSERT INTO `panel_about_team` (`id`, `hash_id`, `input_name`, `input_role`, `image_1`, `input_order`, `visibility`, `date_created`, `time_created`, `created_by`) VALUES
(1, 'team001', 'Dr. Isabella Hartman', 'FOUNDER', 'https://cdn.prod.website-files.com/6918bd445678e83950693c7b/692aa0dccf1e33fb288a9cc2_Frame 1321316498.avif', 1, 'show', '2026-05-15', '00:09:49', 'system'),
(2, 'team002', 'Dr. Alexander Cole', 'Clinical Research', 'https://cdn.prod.website-files.com/6918bd445678e83950693c7b/692aa0decc421d98064eafe2_Frame 1321316506-2.avif', 2, 'show', '2026-05-15', '00:09:49', 'system'),
(3, 'team003', 'Dr. Sophia Lang', 'DERMATOLOGIST', 'https://cdn.prod.website-files.com/6918bd445678e83950693c7b/692aa0dd404f77f26850ba42_Frame 1321316506-1.avif', 3, 'show', '2026-05-15', '00:09:49', 'system');

-- --------------------------------------------------------

--
-- Table structure for table `panel_about_values`
--

DROP TABLE IF EXISTS `panel_about_values`;
CREATE TABLE IF NOT EXISTS `panel_about_values` (
  `id` int NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(255) NOT NULL,
  `input_title` varchar(255) DEFAULT NULL,
  `text_description` text,
  `image_1` text,
  `input_order` int DEFAULT '0',
  `visibility` varchar(50) DEFAULT 'show',
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  `created_by` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `panel_about_values`
--

INSERT INTO `panel_about_values` (`id`, `hash_id`, `input_title`, `text_description`, `image_1`, `input_order`, `visibility`, `date_created`, `time_created`, `created_by`) VALUES
(1, 'val001', 'Paraben free', 'Minimizes the risk of allergic reactions, making it safe for all skin types.', 'https://cdn.prod.website-files.com/6918bd445678e83950693c7b/691a3dd696507bbca660142e_Rectangle 1120.avif', 1, 'show', '2026-05-15', '00:09:49', 'system'),
(2, 'val002', 'Dermatologist tested', 'Formulated and tested under dermatological supervision for reliable skin compatibility.', 'https://cdn.prod.website-files.com/6918bd445678e83950693c7b/691a3dd6eadf70768cc2ea67_Rectangle 1121.avif', 2, 'show', '2026-05-15', '00:09:49', 'system'),
(3, 'val003', 'Suitable for Sensitive Skin', 'Gentle and soothing formula, designed to care for even the most delicate skin.', 'https://cdn.prod.website-files.com/6918bd445678e83950693c7b/691a3dd686f80f4e1ddb8e01_Rectangle 1119.avif', 3, 'show', '2026-05-15', '00:09:49', 'system');

-- --------------------------------------------------------

--
-- Table structure for table `panel_allowed_headers`
--

DROP TABLE IF EXISTS `panel_allowed_headers`;
CREATE TABLE IF NOT EXISTS `panel_allowed_headers` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(500) DEFAULT NULL,
  `input_name` varchar(225) NOT NULL,
  `visibility` varchar(50) NOT NULL,
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `panel_allowed_headers`
--

INSERT INTO `panel_allowed_headers` (`id`, `hash_id`, `input_name`, `visibility`, `date_created`, `time_created`, `created_by`) VALUES
(1, NULL, 'mckodevdemo.local', 'show', '2023-05-10', '11:22:37', NULL),
(2, NULL, 'demo30.mckodev.com.ng', 'show', '2023-05-10', '11:22:37', NULL),
(3, NULL, 'app.demo30.com', 'show', '2023-05-10', '11:22:37', NULL),
(4, '8283672_1767617588', 'easyeven7977525.mckodev.ng', 'show', '2026-01-05', '12:53:08', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `panel_brand_values`
--

DROP TABLE IF EXISTS `panel_brand_values`;
CREATE TABLE IF NOT EXISTS `panel_brand_values` (
  `id` int NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(255) NOT NULL,
  `input_title` varchar(255) DEFAULT NULL,
  `input_emoji` varchar(20) DEFAULT NULL,
  `text_description` text,
  `input_order` int DEFAULT '0',
  `visibility` varchar(50) DEFAULT 'show',
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  `created_by` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `panel_brand_values`
--

INSERT INTO `panel_brand_values` (`id`, `hash_id`, `input_title`, `input_emoji`, `text_description`, `input_order`, `visibility`, `date_created`, `time_created`, `created_by`) VALUES
(1, 'val001', 'Clean Ingredients', '🌿', 'Every formula is free from parabens, sulphates, and artificial fragrances. Pure efficacy, nothing else.', 1, 'show', '2026-05-14', '12:51:55', 'system'),
(2, 'val002', 'Dermatologist Tested', '🔬', 'All products are clinically tested and approved by board-certified dermatologists.', 2, 'show', '2026-05-14', '12:51:55', 'system'),
(3, 'val003', 'Sustainably Crafted', '♻', 'From recyclable packaging to ethically sourced ingredients, sustainability guides every decision.', 3, 'show', '2026-05-14', '12:51:55', 'system'),
(4, 'val004', 'Cruelty Free', '🐰', 'We never test on animals. Certified cruelty-free by Leaping Bunny.', 4, 'show', '2026-05-14', '12:51:55', 'system'),
(5, 'val005', 'Science-Backed', '🧪', 'Every claim is supported by clinical evidence and peer-reviewed research.', 5, 'show', '2026-05-14', '12:51:55', 'system'),
(6, 'val006', 'For Every Skin', '✨', 'Formulated for all skin types, tones, and textures. Inclusivity is not optional.', 6, 'show', '2026-05-14', '12:51:55', 'system');

-- --------------------------------------------------------

--
-- Table structure for table `panel_collections`
--

DROP TABLE IF EXISTS `panel_collections`;
CREATE TABLE IF NOT EXISTS `panel_collections` (
  `id` int NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(255) NOT NULL,
  `input_title` varchar(255) DEFAULT NULL,
  `input_link` varchar(255) DEFAULT NULL,
  `text_description` text,
  `input_order` int DEFAULT '0',
  `image_1` text,
  `visibility` varchar(50) DEFAULT 'show',
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  `created_by` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `panel_collections`
--

INSERT INTO `panel_collections` (`id`, `hash_id`, `input_title`, `input_link`, `text_description`, `input_order`, `image_1`, `visibility`, `date_created`, `time_created`, `created_by`) VALUES
(1, 'col001', 'Serums Collection', '/products?category=Serums', 'Targeted treatments for every skin concern.', 1, '/assets/img/products/radiance-serum-1.webp', 'show', '2026-05-14', '12:51:55', 'system'),
(2, 'col002', 'Moisturizers', '/products?category=Moisturizers', 'Deep hydration for every skin type.', 2, '/assets/img/products/hydrasilk-1.webp', 'show', '2026-05-14', '12:51:55', 'system'),
(3, 'col003', 'Eye Care', '/products?category=Eye+Care', 'Precision treatments for the delicate eye area.', 3, '/assets/img/products/anti-aging-cream-1.webp', 'show', '2026-05-14', '12:51:55', 'system');

-- --------------------------------------------------------

--
-- Table structure for table `panel_gallery_photos`
--

DROP TABLE IF EXISTS `panel_gallery_photos`;
CREATE TABLE IF NOT EXISTS `panel_gallery_photos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(255) NOT NULL,
  `image_1` text,
  `input_alt` varchar(255) DEFAULT 'Gallery',
  `input_order` int DEFAULT '0',
  `visibility` varchar(50) DEFAULT 'show',
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  `created_by` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `panel_gallery_photos`
--

INSERT INTO `panel_gallery_photos` (`id`, `hash_id`, `image_1`, `input_alt`, `input_order`, `visibility`, `date_created`, `time_created`, `created_by`) VALUES
(1, 'gal001', 'https://cdn.prod.website-files.com/6918bd445678e83950693c7b/69193c3a62dfb2e1c46b3d2e_Rectangle 1105.avif', 'Glowing skin model', 1, 'show', '2026-05-15', '15:27:38', 'system'),
(2, 'gal002', 'https://cdn.prod.website-files.com/6918bd445678e83950693c7b/69193c3b7f0a63c03c952cf3_Rectangle 1101.avif', 'Skincare serum', 2, 'show', '2026-05-15', '15:27:38', 'system'),
(3, 'gal003', 'https://cdn.prod.website-files.com/6918bd445678e83950693c7b/69193c3b8256954751ac9026_Rectangle 1102.avif', 'Natural beauty', 3, 'show', '2026-05-15', '15:27:38', 'system'),
(4, 'gal004', 'https://cdn.prod.website-files.com/6918bd445678e83950693c7b/69193c3b0e2c8d3d0bd0253c_Rectangle 1103.avif', 'Skincare routine', 4, 'show', '2026-05-15', '15:27:38', 'system'),
(5, 'gal005', 'https://cdn.prod.website-files.com/6918bd445678e83950693c7b/69193c3b26fef69889644991_Rectangle 1104.avif', 'Confident woman', 5, 'show', '2026-05-15', '15:27:38', 'system');

-- --------------------------------------------------------

--
-- Table structure for table `panel_home_blocks`
--

DROP TABLE IF EXISTS `panel_home_blocks`;
CREATE TABLE IF NOT EXISTS `panel_home_blocks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(255) NOT NULL,
  `input_badge` varchar(255) DEFAULT NULL,
  `input_heading` varchar(255) DEFAULT NULL,
  `text_description` text,
  `input_btn_label` varchar(255) DEFAULT NULL,
  `input_btn_link` varchar(255) DEFAULT NULL,
  `image_1` text,
  `input_order` int DEFAULT '0',
  `visibility` varchar(50) DEFAULT 'show',
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  `created_by` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `panel_home_blocks`
--

INSERT INTO `panel_home_blocks` (`id`, `hash_id`, `input_badge`, `input_heading`, `text_description`, `input_btn_label`, `input_btn_link`, `image_1`, `input_order`, `visibility`, `date_created`, `time_created`, `created_by`) VALUES
(1, 'hb001', 'Dermatologist tested', 'Dermatology-Tested Skincare You Can Trust', 'Our formulas are developed in collaboration with dermatologists to ensure maximum comfort and visible results for every skin type.', 'About us', '/about', 'https://cdn.prod.website-files.com/6918bd445678e83950693c7b/691940b886702b2d2296b5f3_Rectangle 1071.avif', 1, 'show', '2026-05-14', '21:58:41', 'system'),
(2, 'hb002', 'Paraben free', 'Naturally Clean. Always Paraben-Free.', 'Your skin deserves only the best. That’s why every product we create is 100% paraben-free, formulated with gentle and natural ingredients.', 'Learn more', '/about', 'https://cdn.prod.website-files.com/6918bd445678e83950693c7b/69193b3ec862043fc0be46ab_Rectangle 1072.avif', 2, 'show', '2026-05-14', '21:58:41', 'system');

-- --------------------------------------------------------

--
-- Table structure for table `panel_pages`
--

DROP TABLE IF EXISTS `panel_pages`;
CREATE TABLE IF NOT EXISTS `panel_pages` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(225) DEFAULT NULL,
  `input_name` varchar(225) DEFAULT NULL,
  `bool_method` varchar(100) DEFAULT NULL,
  `boolkey_method` varchar(100) DEFAULT NULL,
  `input_link` varchar(225) DEFAULT NULL,
  `label` varchar(30) DEFAULT NULL,
  `input_file` varchar(30) DEFAULT NULL,
  `file_path` varchar(30) DEFAULT NULL,
  `input_iframe_link` varchar(225) DEFAULT NULL,
  `input_order` varchar(225) DEFAULT NULL,
  `add_pages` varchar(225) DEFAULT NULL,
  `visibility` varchar(5) DEFAULT NULL,
  `date_created` date DEFAULT NULL,
  `time_created` time DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `panel_pages`
--

INSERT INTO `panel_pages` (`id`, `hash_id`, `input_name`, `bool_method`, `boolkey_method`, `input_link`, `label`, `input_file`, `file_path`, `input_iframe_link`, `input_order`, `add_pages`, `visibility`, `date_created`, `time_created`, `created_by`) VALUES
(1, 'fj2ouf', 'Home', '0', 'internal_external', '/home', NULL, '', NULL, '', '1', NULL, 'show', '2023-05-10', '11:03:13', NULL),
(2, '2ofof', 'About', '0', 'internal_external', '#', NULL, '', NULL, '', '2', NULL, 'show', '2023-05-10', '11:03:13', NULL),
(3, 'jf34pfp', 'Project', '0', 'internal_external', '/projects', NULL, '', NULL, '', '4', NULL, 'hide', '2023-05-10', '11:03:13', NULL),
(4, 'f30pf;[3', 'Services', '0', 'internal_external', '/events', NULL, '', NULL, '', '3', NULL, 'show', '2023-05-10', '11:03:13', NULL),
(5, '3f3of2p', 'Gallery', '0', 'internal_external', '/gallery', NULL, '', NULL, '', '5', NULL, 'show', '2023-05-10', '11:03:13', NULL),
(6, 'jpf;qf3', 'News', '0', 'internal_external', '/news', NULL, '', NULL, '', '6', NULL, 'show', '2023-05-10', '11:03:13', NULL),
(7, 'hfjlapq', 'Contact', '0', 'internal_external', '/contact', NULL, '', NULL, '', '7', NULL, 'show', '2023-06-01', '05:43:20', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `panel_partners`
--

DROP TABLE IF EXISTS `panel_partners`;
CREATE TABLE IF NOT EXISTS `panel_partners` (
  `id` int NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(255) NOT NULL,
  `input_name` varchar(255) DEFAULT NULL,
  `input_link` varchar(255) DEFAULT NULL,
  `input_order` int DEFAULT '0',
  `image_1` text,
  `visibility` varchar(50) DEFAULT 'show',
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  `created_by` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `panel_partners`
--

INSERT INTO `panel_partners` (`id`, `hash_id`, `input_name`, `input_link`, `input_order`, `image_1`, `visibility`, `date_created`, `time_created`, `created_by`) VALUES
(1, 'prt001', 'Partner 1', '#', 1, '/assets/img/icons/partner-1.svg', 'show', '2026-05-14', '12:51:55', 'system'),
(2, 'prt002', 'Partner 2', '#', 2, '/assets/img/icons/partner-2.svg', 'show', '2026-05-14', '12:51:55', 'system'),
(3, 'prt003', 'Partner 3', '#', 3, '/assets/img/icons/partner-3.svg', 'show', '2026-05-14', '12:51:55', 'system'),
(4, 'prt004', 'Partner 4', '#', 4, '/assets/img/icons/partner-4.svg', 'show', '2026-05-14', '12:51:55', 'system');

-- --------------------------------------------------------

--
-- Table structure for table `panel_products`
--

DROP TABLE IF EXISTS `panel_products`;
CREATE TABLE IF NOT EXISTS `panel_products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(255) NOT NULL,
  `input_title` varchar(255) DEFAULT NULL,
  `input_slug` varchar(255) DEFAULT NULL,
  `select_category` varchar(255) DEFAULT NULL,
  `text_description` longtext,
  `text_short_desc` text,
  `text_ingredients` text,
  `input_price` varchar(50) DEFAULT NULL,
  `input_compare_price` varchar(50) DEFAULT NULL,
  `input_sku` varchar(100) DEFAULT NULL,
  `input_stock` varchar(20) DEFAULT '999',
  `input_weight` varchar(50) DEFAULT NULL,
  `input_badge` varchar(50) DEFAULT NULL,
  `input_rating` varchar(10) DEFAULT '4.5',
  `input_order` int DEFAULT '0',
  `image_1` text,
  `image_2` text,
  `add_product_variants` varchar(255) DEFAULT NULL,
  `visibility` varchar(50) DEFAULT 'show',
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  `created_by` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `panel_products`
--

INSERT INTO `panel_products` (`id`, `hash_id`, `input_title`, `input_slug`, `select_category`, `text_description`, `text_short_desc`, `text_ingredients`, `input_price`, `input_compare_price`, `input_sku`, `input_stock`, `input_weight`, `input_badge`, `input_rating`, `input_order`, `image_1`, `image_2`, `add_product_variants`, `visibility`, `date_created`, `time_created`, `created_by`) VALUES
(1, 'mmns001', 'Radiance Boost Serum', 'radiance-boost-serum', 'Serums', 'Transform your complexion with this powerful brightening serum, formulated with a potent blend of Vitamin C, Niacinamide, and Hyaluronic Acid. This lightweight serum fades dark spots, evens skin tone, and boosts your natural radiance. With regular use, your skin becomes visibly brighter, firmer, and more luminous. Suitable for all skin types including sensitive skin.', 'A lightweight brightening serum that fades dark spots and boosts natural radiance.', 'Aqua, Ascorbic Acid (Vitamin C 15%), Niacinamide, Hyaluronic Acid, Glycerin, Ferulic Acid, Panthenol, Allantoin, Tocopherol.', '50.00', '65.00', 'VNR-SRM-001', '999', '30ml', 'New', '4.8', 1, '/assets/img/products/radiance-serum-1.webp', '/assets/img/products/radiance-serum-2.webp', NULL, 'show', '2026-05-14', '12:51:54', 'system'),
(2, 'mmac001', 'Anti-Aging Eye Cream', 'anti-aging-eye-cream', 'Eye Care', 'Revive the delicate skin around your eyes with this targeted anti-aging eye cream. Enriched with Retinol, Peptides, and Caffeine, it visibly reduces fine lines, puffiness, and dark circles while firming the skin. The ultra-rich yet lightweight formula absorbs quickly and is gentle enough for even the most sensitive eye areas.', 'A targeted eye cream that reduces fine lines, puffiness, and dark circles.', 'Aqua, Retinol, Palmitoyl Tripeptide-1, Caffeine, Niacinamide, Hyaluronic Acid, Shea Butter, Vitamin E.', '30.00', NULL, 'VNR-EYE-001', '999', '15ml', NULL, '4.6', 2, '/assets/img/products/anti-aging-cream-1.webp', '/assets/img/products/anti-aging-cream-2.webp', NULL, 'show', '2026-05-14', '12:51:54', 'system'),
(3, 'mmgc001', 'Refreshing Gel Cleanser', 'refreshing-gel-cleanser', 'Cleansers', 'Start and end your day with this gentle, refreshing gel cleanser that deeply cleanses without stripping the skin. Formulated with Aloe Vera, Green Tea Extract, and Salicylic Acid, it effectively removes impurities, excess oil, and makeup residue while keeping skin balanced and hydrated. Perfect for oily and combination skin.', 'A gentle gel cleanser that deeply cleanses while keeping skin balanced and hydrated.', 'Aqua, Aloe Barbadensis Leaf Juice, Camellia Sinensis (Green Tea) Leaf Extract, Salicylic Acid, Sodium Cocoyl Glutamate, Panthenol.', '30.00', NULL, 'VNR-CLN-001', '999', '150ml', NULL, '4.7', 3, '/assets/img/products/gel-cleanser-1.webp', '/assets/img/products/gel-cleanser-2.webp', NULL, 'show', '2026-05-14', '12:51:54', 'system'),
(4, 'mmhs001', 'Hydrasilk Moisturizer', 'hydrasilk-moisturizer', 'Moisturizers', 'Nourish and hydrate your skin with Hydrasilk Moisturizer, a luxurious, lightweight cream that delivers deep moisture while leaving your skin silky-smooth and radiant. Enriched with Hyaluronic Acid and Squalane, this moisturizer replenishes hydration, softens fine lines, and restores elasticity. Ideal for all skin types, it absorbs quickly without leaving a greasy residue, making it perfect for daily use.', 'A luxurious cream that delivers deep moisture, leaving skin silky-smooth and radiant.', 'Aqua, Hyaluronic Acid, Squalane, Shea Butter, Tocopherol (Vitamin E), Aloe Vera, Glycerin, Caprylic/Capric Triglyceride.', '70.00', NULL, 'VNR-MOS-001', '999', '50ml', NULL, '4.6', 4, '/assets/img/products/hydrasilk-1.webp', '/assets/img/products/hydrasilk-2.webp', NULL, 'show', '2026-05-14', '12:51:54', 'system'),
(5, 'mmvc001', 'Velvet Night Cream', 'velvet-night-cream', 'Moisturizers', 'Wake up to softer, smoother, more radiant skin with this luxurious overnight treatment. Formulated with a rich blend of Bakuchiol (natural Retinol alternative), Ceramides, and Rosehip Oil, this night cream works while you sleep to repair, regenerate, and deeply nourish your skin. Morning after morning, skin looks visibly rejuvenated.', 'An overnight treatment that repairs and deeply nourishes skin while you sleep.', 'Aqua, Bakuchiol, Ceramide NP, Rosa Canina (Rosehip) Seed Oil, Peptides, Shea Butter, Squalane, Niacinamide.', '65.00', NULL, 'VNR-NIT-001', '999', '50ml', 'Best Seller', '4.9', 5, '/assets/img/products/velvet-cream-1.webp', '/assets/img/products/velvet-cream-2.webp', NULL, 'show', '2026-05-14', '12:51:54', 'system'),
(6, 'mmld001', 'Luminous Day Cream', 'luminous-day-cream', 'Moisturizers', 'Achieve a naturally luminous complexion with this lightweight day cream that hydrates, protects, and brightens throughout the day. Packed with Vitamin C, Pearl Extract, and SPF 15, it shields your skin from environmental stressors while delivering a radiant, healthy glow. Ideal for daily use under makeup.', 'A lightweight day cream that hydrates, protects, and delivers a radiant glow.', 'Aqua, Ascorbic Acid, Pearl Extract, Titanium Dioxide (SPF 15), Glycerin, Hyaluronic Acid, Tocopherol.', '60.00', NULL, 'VNR-DAY-001', '999', '50ml', NULL, '4.7', 6, '/assets/img/products/luminous-day-1.webp', '/assets/img/products/luminous-day-2.webp', NULL, 'show', '2026-05-14', '12:51:54', 'system'),
(7, 'mmbs001', 'Brightening Eye Serum', 'brightening-eye-serum', 'Eye Care', 'Tackle dark circles and dullness with this concentrated brightening eye serum. Formulated with Vitamin K, Kojic Acid, and Caffeine, it targets hyperpigmentation and puffiness around the eyes, revealing a brighter, more awake appearance. The silky texture absorbs instantly for long-lasting hydration.', 'A concentrated serum that targets dark circles and puffiness for brighter eyes.', 'Aqua, Phytonadione (Vitamin K), Kojic Acid, Caffeine, Hyaluronic Acid, Niacinamide, Peptides.', '55.00', '70.00', 'VNR-EYS-001', '999', '15ml', 'Sale', '4.5', 7, '/assets/img/products/brightening-serum-1.webp', '/assets/img/products/brightening-serum-2.webp', NULL, 'show', '2026-05-14', '12:51:54', 'system'),
(8, 'mmfc001', 'Gentle Foaming Cleanser', 'gentle-foaming-cleanser', 'Cleansers', 'Experience the luxury of a spa-like cleanse at home. This gentle foaming cleanser creates a rich, pillowy lather that melts away impurities, makeup, and excess oil without disrupting your skin barrier. Enriched with Chamomile and Oat Extract, it calms, soothes, and leaves skin feeling clean, soft, and comfortable.', 'A gentle foaming cleanser that creates a rich lather to remove impurities without irritation.', 'Aqua, Cocamidopropyl Betaine, Chamomilla Recutita Extract, Avena Sativa (Oat) Extract, Allantoin, Panthenol.', '28.00', NULL, 'VNR-FCL-001', '999', '150ml', NULL, '4.8', 8, '/assets/img/products/foaming-cleanser-1.webp', '/assets/img/products/foaming-cleanser-2.webp', NULL, 'show', '2026-05-14', '12:51:54', 'system'),
(9, 'mmdh001', 'Deep Hydration Serum', 'deep-hydration-serum', 'Serums', 'Quench even the most dehydrated skin with this intensive hydrating serum. Combining three molecular weights of Hyaluronic Acid with Betaine and Polyglutamic Acid, it penetrates deep into the skin layers to restore moisture balance, plump fine lines, and create a visibly dewy complexion that lasts all day.', 'An intensive serum that deeply hydrates with multiple forms of Hyaluronic Acid.', 'Aqua, Sodium Hyaluronate (Low, Medium, High Molecular Weight), Betaine, Polyglutamic Acid, Glycerin, Panthenol.', '75.00', NULL, 'VNR-DHY-001', '999', '30ml', NULL, '4.9', 9, '/assets/img/products/deep-hydration-1.webp', '/assets/img/products/deep-hydration-2.webp', NULL, 'show', '2026-05-14', '12:51:54', 'system'),
(10, 'mmpp001', 'Pore Perfect Treatment', 'pore-perfect-treatment', 'Treatments', 'Minimize the appearance of enlarged pores and achieve a smoother, more refined complexion with this targeted treatment. Formulated with Niacinamide, Salicylic Acid, and Zinc, it regulates sebum production, unclogs pores, and visibly reduces their appearance over time. Lightweight and oil-free for daily use.', 'A targeted treatment that minimizes pores and refines skin texture.', 'Aqua, Niacinamide (10%), Salicylic Acid, Zinc PCA, Witch Hazel Extract, Glycerin, Allantoin.', '45.00', NULL, 'VNR-POR-001', '999', '30ml', NULL, '4.6', 10, '/assets/img/products/pore-perfect-1.webp', '/assets/img/products/pore-perfect-2.webp', NULL, 'show', '2026-05-14', '12:51:54', 'system'),
(11, 'mmmc001', 'Hydrating Milk Cleanser', 'hydrating-milk-cleanser', 'Cleansers', 'Dissolve the day with this creamy, hydrating milk cleanser that leaves skin feeling nourished rather than stripped. Enriched with Ceramides, Oat Milk, and Rose Water, it gently removes makeup and impurities while reinforcing the skin barrier. Suitable for dry, sensitive, and mature skin types.', 'A creamy milk cleanser that removes makeup while nourishing and protecting the skin barrier.', 'Aqua, Rosa Damascena (Rose Water), Avena Sativa (Oat) Milk, Ceramide NP, Glycerin, Panthenol, Sodium Cocoyl Glutamate.', '32.00', NULL, 'VNR-MCL-001', '999', '150ml', NULL, '4.7', 11, '/assets/img/products/milk-cleanser-1.webp', '/assets/img/products/milk-cleanser-2.webp', NULL, 'show', '2026-05-14', '12:51:54', 'system'),
(12, 'mmne001', 'Soothing Night Eye Cream', 'soothing-night-eye-cream', 'Eye Care', 'Restore and rejuvenate the delicate eye area overnight with this rich, soothing night cream. Formulated with Bakuchiol, Ceramides, and Lavender Extract, it targets crow feet, puffiness, and loss of firmness while you sleep. Wake up to visibly smoother, plumper, more youthful-looking eyes.', 'A rich overnight eye cream that targets crow feet and restores firmness while you sleep.', 'Aqua, Bakuchiol, Ceramide NP, Lavandula Angustifolia (Lavender) Extract, Retinyl Palmitate, Shea Butter, Peptides.', '40.00', NULL, 'VNR-NEY-001', '999', '15ml', NULL, '4.8', 12, '/assets/img/products/night-eye-cream-1.webp', '/assets/img/products/night-eye-cream-2.webp', NULL, 'show', '2026-05-14', '12:51:54', 'system');

-- --------------------------------------------------------

--
-- Table structure for table `panel_team`
--

DROP TABLE IF EXISTS `panel_team`;
CREATE TABLE IF NOT EXISTS `panel_team` (
  `id` int NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(255) NOT NULL,
  `input_name` varchar(255) DEFAULT NULL,
  `input_role` varchar(255) DEFAULT NULL,
  `text_bio` text,
  `input_order` int DEFAULT '0',
  `image_1` text,
  `visibility` varchar(50) DEFAULT 'show',
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  `created_by` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `panel_team`
--

INSERT INTO `panel_team` (`id`, `hash_id`, `input_name`, `input_role`, `text_bio`, `input_order`, `image_1`, `visibility`, `date_created`, `time_created`, `created_by`) VALUES
(1, 'team001', 'Dr. Isabella Hartman', 'Chief Dermatologist', 'Board-certified dermatologist with 15 years of clinical experience.', 1, NULL, 'show', '2026-05-14', '12:51:55', 'system'),
(2, 'team002', 'Sofia Laurent', 'Founder & CEO', 'Passionate skincare entrepreneur with a background in biochemistry.', 2, NULL, 'show', '2026-05-14', '12:51:55', 'system'),
(3, 'team003', 'Amara Chen', 'Head of Formulation', 'Cosmetic chemist specialising in natural and active ingredients.', 3, NULL, 'show', '2026-05-14', '12:51:55', 'system'),
(4, 'team004', 'Nina Rossi', 'Creative Director', 'Brand designer with a passion for sustainable luxury aesthetics.', 4, NULL, 'show', '2026-05-14', '12:51:55', 'system');

-- --------------------------------------------------------

--
-- Table structure for table `panel_testimonials`
--

DROP TABLE IF EXISTS `panel_testimonials`;
CREATE TABLE IF NOT EXISTS `panel_testimonials` (
  `id` int NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(255) NOT NULL,
  `input_name` varchar(255) DEFAULT NULL,
  `input_initials` varchar(10) DEFAULT NULL,
  `input_role` varchar(255) DEFAULT NULL,
  `input_rating` varchar(10) DEFAULT '5',
  `text_review` text,
  `dated_review` date DEFAULT NULL,
  `input_order` int DEFAULT '0',
  `image_1` text,
  `visibility` varchar(50) DEFAULT 'show',
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  `created_by` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `panel_testimonials`
--

INSERT INTO `panel_testimonials` (`id`, `hash_id`, `input_name`, `input_initials`, `input_role`, `input_rating`, `text_review`, `dated_review`, `input_order`, `image_1`, `visibility`, `date_created`, `time_created`, `created_by`) VALUES
(1, 'tm001', 'Mia P.', 'MP', 'Verified Customer', '5', 'I use it both morning and night, and it\'s made such a difference. My skin texture is smoother, pores look smaller, and I wake up with a soft glow. It\'s gentle enough for my sensitive skin, yet powerful enough to make visible results.', '2025-11-17', 1, NULL, 'show', '2026-05-14', '12:51:55', 'system'),
(2, 'tm002', 'Naomi K.', 'NK', 'Verified Customer', '5', 'I love how elegant and clean this serum feels. It\'s light, refreshing, and has a subtle scent that makes my skincare routine feel like a spa moment. My skin looks more luminous, and my complexion feels balanced.', '2025-11-17', 2, NULL, 'show', '2026-05-14', '12:51:55', 'system'),
(3, 'tm003', 'Elena R.', 'ER', 'Verified Customer', '5', 'I have combination skin and always worry about serums being too heavy, but this one is perfect. It gives the right amount of hydration without feeling oily. My fine lines around the eyes and mouth have softened.', '2025-11-17', 3, NULL, 'show', '2026-05-14', '12:51:55', 'system'),
(4, 'tm004', 'Clara M.', 'CM', 'Verified Customer', '5', 'This product really surprised me. After just a few uses, I noticed how fresh and hydrated my skin felt. I struggle with tired-looking skin from long work hours, and this gives me an instant glow and softness.', '2025-11-17', 4, NULL, 'show', '2026-05-14', '12:51:55', 'system'),
(5, 'tm005', 'Sophie L.', 'SL', 'Verified Customer', '5', 'I\'ve been using this serum every morning for the past three weeks, and my skin has completely transformed. The dullness I used to have is gone — now my face looks brighter, smoother, and more even.', '2025-11-17', 5, NULL, 'show', '2026-05-14', '12:51:55', 'system');

-- --------------------------------------------------------

--
-- Table structure for table `panel_venora_faq`
--

DROP TABLE IF EXISTS `panel_venora_faq`;
CREATE TABLE IF NOT EXISTS `panel_venora_faq` (
  `id` int NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(255) NOT NULL,
  `input_question` varchar(500) DEFAULT NULL,
  `text_answer` text,
  `input_order` int DEFAULT '0',
  `visibility` varchar(50) DEFAULT 'show',
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  `created_by` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `panel_venora_faq`
--

INSERT INTO `panel_venora_faq` (`id`, `hash_id`, `input_question`, `text_answer`, `input_order`, `visibility`, `date_created`, `time_created`, `created_by`) VALUES
(1, 'faq001', 'Can I use your products if I have sensitive skin?', 'Yes! All our products are dermatologist-tested and formulated to be gentle on sensitive skin. We recommend doing a patch test before full use.', 1, 'show', '2026-05-14', '14:11:00', 'system'),
(2, 'faq002', 'How should I store the products for best results?', 'Store your products in a cool, dry place away from direct sunlight. Most products perform best at room temperature between 15-25 degrees Celsius.', 2, 'show', '2026-05-14', '14:11:00', 'system'),
(3, 'faq003', 'Are your products cruelty-free and vegan?', 'Yes, all Venora products are 100% cruelty-free and never tested on animals. The majority of our range is vegan — check individual product labels for confirmation.', 3, 'show', '2026-05-14', '14:11:00', 'system'),
(4, 'faq004', 'How long does it take to see results?', 'Most customers see visible improvements within 2 to 4 weeks of consistent use. For best results, use products as directed both morning and evening.', 4, 'show', '2026-05-14', '14:11:00', 'system'),
(5, 'faq005', 'What is your return policy?', 'We offer a 30-day satisfaction guarantee. If you are not happy with your purchase, contact us and we will arrange a full refund or exchange.', 5, 'show', '2026-05-14', '14:11:00', 'system');

-- --------------------------------------------------------

--
-- Table structure for table `read_cart`
--

DROP TABLE IF EXISTS `read_cart`;
CREATE TABLE IF NOT EXISTS `read_cart` (
  `id` int NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(255) NOT NULL,
  `input_session_id` varchar(255) DEFAULT NULL,
  `input_product_id` varchar(255) DEFAULT NULL,
  `input_variant_id` varchar(255) DEFAULT NULL,
  `input_variant` varchar(255) DEFAULT NULL,
  `input_quantity` int DEFAULT '1',
  `input_price` varchar(50) DEFAULT NULL,
  `visibility` varchar(50) DEFAULT 'show',
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  `created_by` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `read_cart`
--

INSERT INTO `read_cart` (`id`, `hash_id`, `input_session_id`, `input_product_id`, `input_variant_id`, `input_variant`, `input_quantity`, `input_price`, `visibility`, `date_created`, `time_created`, `created_by`) VALUES
(1, 'cart_6a05b7e1668db1.73647043', 'fa2nt2pr381rh2jmqbcvmsb0bm', 'mmgc001', '', '', 1, '30.00', 'hide', '2026-05-14', '11:54:09', 'fa2nt2pr381rh2jmqbcvmsb0bm'),
(2, 'cart_6a05b8121c0846.87190345', 'fa2nt2pr381rh2jmqbcvmsb0bm', 'mmhs001', '', '', 1, '70.00', 'hide', '2026-05-14', '11:54:58', 'fa2nt2pr381rh2jmqbcvmsb0bm'),
(3, 'cart_6a05c3bd610bf4.84034033', 'fa2nt2pr381rh2jmqbcvmsb0bm', 'mmmc001', '', '', 1, '32.00', 'hide', '2026-05-14', '12:44:45', 'fa2nt2pr381rh2jmqbcvmsb0bm'),
(4, 'cart_6a05c48580b151.04726145', 'fa2nt2pr381rh2jmqbcvmsb0bm', 'mmvc001', 'var006', '50ml', 6, '65.00', 'hide', '2026-05-14', '12:48:05', 'fa2nt2pr381rh2jmqbcvmsb0bm'),
(5, 'cart_6a062ae64670f3.61855960', 'fa2nt2pr381rh2jmqbcvmsb0bm', 'mmns001', 'var001', '30ml', 2, '50.00', 'hide', '2026-05-14', '20:04:54', 'fa2nt2pr381rh2jmqbcvmsb0bm'),
(6, 'cart_6a0633994d9749.53899667', 'fa2nt2pr381rh2jmqbcvmsb0bm', 'mmns001', '', '', 2, '50.00', 'hide', '2026-05-14', '20:42:01', 'fa2nt2pr381rh2jmqbcvmsb0bm'),
(7, 'cart_6a0633d1a9c6c1.34702317', 'fa2nt2pr381rh2jmqbcvmsb0bm', 'mmvc001', '', '', 1, '65.00', 'hide', '2026-05-14', '20:42:57', 'fa2nt2pr381rh2jmqbcvmsb0bm'),
(8, 'cart_6a063dedc5ab13.71028933', '', 'mmns001', '', '', 1, '50.00', 'show', '2026-05-14', '21:26:05', ''),
(9, 'cart_6a063e0d456313.26537628', 'fa2nt2pr381rh2jmqbcvmsb0bm', 'mmfc001', '', '', 2, '28.00', 'hide', '2026-05-14', '21:26:37', 'fa2nt2pr381rh2jmqbcvmsb0bm'),
(10, 'cart_6a063e1d4d0882.35299454', 'fa2nt2pr381rh2jmqbcvmsb0bm', 'mmac001', '', '', 5, '30.00', 'hide', '2026-05-14', '21:26:53', 'fa2nt2pr381rh2jmqbcvmsb0bm'),
(11, 'cart_6a0640e3df2d88.83084502', 'fa2nt2pr381rh2jmqbcvmsb0bm', 'mmac001', '', '', 2, '30.00', 'hide', '2026-05-14', '21:38:43', 'fa2nt2pr381rh2jmqbcvmsb0bm'),
(12, 'cart_6a0640f0ea63d9.24812077', 'fa2nt2pr381rh2jmqbcvmsb0bm', 'mmns001', '', '', 2, '50.00', 'hide', '2026-05-14', '21:38:56', 'fa2nt2pr381rh2jmqbcvmsb0bm'),
(13, 'cart_6a0642a1149ce0.99328318', 'fa2nt2pr381rh2jmqbcvmsb0bm', 'mmfc001', '', '', 1, '28.00', 'hide', '2026-05-14', '21:46:09', 'fa2nt2pr381rh2jmqbcvmsb0bm'),
(14, 'cart_6a0643e60e98e6.05351904', 'fa2nt2pr381rh2jmqbcvmsb0bm', 'mmns001', '', '', 2, '50.00', 'hide', '2026-05-14', '21:51:34', 'fa2nt2pr381rh2jmqbcvmsb0bm'),
(15, 'cart_6a064c0e1adc08.62444069', 'fa2nt2pr381rh2jmqbcvmsb0bm', 'mmns001', 'vsize_mmns001_large,vcol_mmns001_yellow', 'Large / Yellow', 1, '50.00', 'hide', '2026-05-14', '22:26:22', 'fa2nt2pr381rh2jmqbcvmsb0bm'),
(16, 'cart_6a064c19301be1.76475349', 'fa2nt2pr381rh2jmqbcvmsb0bm', 'mmgc001', '', '', 1, '30.00', 'hide', '2026-05-14', '22:26:33', 'fa2nt2pr381rh2jmqbcvmsb0bm'),
(17, 'cart_6a065105e434e7.30898312', 'fa2nt2pr381rh2jmqbcvmsb0bm', 'mmmc001', 'vsize_mmmc001_xlarge,vcol_mmmc001_green', 'XLarge / Green', 1, '32.00', 'hide', '2026-05-14', '22:47:33', 'fa2nt2pr381rh2jmqbcvmsb0bm'),
(18, 'cart_6a06511c5eefa9.54300880', 'fa2nt2pr381rh2jmqbcvmsb0bm', 'mmmc001', '', '', 1, '32.00', 'hide', '2026-05-14', '22:47:56', 'fa2nt2pr381rh2jmqbcvmsb0bm'),
(19, 'cart_6a06554d7f77c5.62431175', 'fa2nt2pr381rh2jmqbcvmsb0bm', 'mmac001', 'vsize_mmac001_xlarge,vcol_mmac001_yellow', 'XLarge / Yellow', 1, '30.00', 'hide', '2026-05-14', '23:05:49', 'fa2nt2pr381rh2jmqbcvmsb0bm'),
(20, 'cart_6a06555c6f4372.85986683', 'fa2nt2pr381rh2jmqbcvmsb0bm', 'mmac001', '', '', 1, '30.00', 'hide', '2026-05-14', '23:06:04', 'fa2nt2pr381rh2jmqbcvmsb0bm'),
(21, 'cart_6a0655687610b3.11125824', 'fa2nt2pr381rh2jmqbcvmsb0bm', 'mmac001', '', '', 2, '30.00', 'hide', '2026-05-14', '23:06:16', 'fa2nt2pr381rh2jmqbcvmsb0bm'),
(22, 'cart_6a07073019d4d1.86771426', 'fa2nt2pr381rh2jmqbcvmsb0bm', 'mmld001', 'vsize_mmld001_xlarge,vcol_mmld001_blue', 'XLarge / Blue', 1, '60.00', 'hide', '2026-05-15', '11:44:48', 'fa2nt2pr381rh2jmqbcvmsb0bm'),
(23, 'cart_6a0731b21b8900.17319932', 'mpkt36nqgki6vo0vb93nb44te4', 'mmmc001', '', '', 1, '32.00', 'hide', '2026-05-15', '14:46:10', 'mpkt36nqgki6vo0vb93nb44te4'),
(24, 'cart_6a0731b9dfdb14.08435227', 'mpkt36nqgki6vo0vb93nb44te4', 'mmmc001', '', '', 1, '32.00', 'hide', '2026-05-15', '14:46:17', 'mpkt36nqgki6vo0vb93nb44te4'),
(25, 'cart_6a073268482989.17151357', 'mpkt36nqgki6vo0vb93nb44te4', 'mmne001', '', '', 2, '40.00', 'hide', '2026-05-15', '14:49:12', 'mpkt36nqgki6vo0vb93nb44te4'),
(26, 'cart_6a073277e27f71.32365820', 'mpkt36nqgki6vo0vb93nb44te4', 'mmne001', '', '', 1, '40.00', 'hide', '2026-05-15', '14:49:27', 'mpkt36nqgki6vo0vb93nb44te4'),
(27, 'cart_6a073360b9d7a5.47165997', 'mpkt36nqgki6vo0vb93nb44te4', 'mmne001', '', '', 1, '40.00', 'hide', '2026-05-15', '14:53:20', 'mpkt36nqgki6vo0vb93nb44te4'),
(28, 'cart_6a0733e9e4ffd2.21724517', 'mpkt36nqgki6vo0vb93nb44te4', 'mmne001', '', '', 1, '40.00', 'hide', '2026-05-15', '14:55:37', 'mpkt36nqgki6vo0vb93nb44te4'),
(29, 'cart_6a07344c304699.35939690', 'mpkt36nqgki6vo0vb93nb44te4', 'mmne001', '', '', 1, '40.00', 'hide', '2026-05-15', '14:57:16', 'mpkt36nqgki6vo0vb93nb44te4'),
(30, 'cart_6a07345f3c1ec1.15088954', 'mpkt36nqgki6vo0vb93nb44te4', 'mmne001', '', '', 1, '40.00', 'hide', '2026-05-15', '14:57:35', 'mpkt36nqgki6vo0vb93nb44te4'),
(31, 'cart_6a073485829c32.83050552', 'mpkt36nqgki6vo0vb93nb44te4', 'mmne001', '', '', 1, '40.00', 'hide', '2026-05-15', '14:58:13', 'mpkt36nqgki6vo0vb93nb44te4'),
(32, 'cart_6a0735948e1721.97712229', 'mpkt36nqgki6vo0vb93nb44te4', 'mmne001', 'vsize_mmne001_large,vcol_mmne001_blue', 'Large / Blue', 2, '40.00', 'hide', '2026-05-15', '15:02:44', 'mpkt36nqgki6vo0vb93nb44te4'),
(33, 'cart_6a073e098b6c27.88418223', 'mpkt36nqgki6vo0vb93nb44te4', 'mmne001', 'vsize_mmne001_large,vcol_mmne001_blue', '30ml / Oily', 2, '40.00', 'hide', '2026-05-15', '15:38:49', 'mpkt36nqgki6vo0vb93nb44te4'),
(34, 'cart_6a073e6385c651.65186018', 'mpkt36nqgki6vo0vb93nb44te4', 'mmmc001', 'vsize_mmmc001_large,vcol_mmmc001_green', '30ml / Dry', 2, '32.00', 'hide', '2026-05-15', '15:40:19', 'mpkt36nqgki6vo0vb93nb44te4');

-- --------------------------------------------------------

--
-- Table structure for table `read_contact_messages`
--

DROP TABLE IF EXISTS `read_contact_messages`;
CREATE TABLE IF NOT EXISTS `read_contact_messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(255) NOT NULL,
  `input_name` varchar(255) DEFAULT NULL,
  `input_email` varchar(255) DEFAULT NULL,
  `input_subject` varchar(255) DEFAULT NULL,
  `text_message` text,
  `visibility` varchar(50) DEFAULT 'show',
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  `created_by` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `read_favicon`
--

DROP TABLE IF EXISTS `read_favicon`;
CREATE TABLE IF NOT EXISTS `read_favicon` (
  `id` int NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(255) NOT NULL,
  `image_1` text,
  `visibility` varchar(50) DEFAULT 'show',
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  `created_by` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `read_favicon`
--

INSERT INTO `read_favicon` (`id`, `hash_id`, `image_1`, `visibility`, `date_created`, `time_created`, `created_by`) VALUES
(1, 'fav001', '/assets/img/brand/venora-white.svg', 'show', '2026-05-14', '13:02:01', 'system');

-- --------------------------------------------------------

--
-- Table structure for table `read_newsletter`
--

DROP TABLE IF EXISTS `read_newsletter`;
CREATE TABLE IF NOT EXISTS `read_newsletter` (
  `id` int NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(255) NOT NULL,
  `input_email` varchar(255) DEFAULT NULL,
  `visibility` varchar(50) DEFAULT 'show',
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  `created_by` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `read_newsletter`
--

INSERT INTO `read_newsletter` (`id`, `hash_id`, `input_email`, `visibility`, `date_created`, `time_created`, `created_by`) VALUES
(1, 'nl_6a05c3ee11dfd8.46047280', 'akadiriokiki@gmail.com', 'show', '2026-05-14', '12:45:34', 'visitor');

-- --------------------------------------------------------

--
-- Table structure for table `read_orders`
--

DROP TABLE IF EXISTS `read_orders`;
CREATE TABLE IF NOT EXISTS `read_orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(255) NOT NULL,
  `input_first_name` varchar(255) DEFAULT NULL,
  `input_last_name` varchar(255) DEFAULT NULL,
  `input_email` varchar(255) DEFAULT NULL,
  `input_phone` varchar(50) DEFAULT NULL,
  `text_address` text,
  `input_status` varchar(50) DEFAULT 'pending',
  `input_total` varchar(50) DEFAULT NULL,
  `input_subtotal` varchar(50) DEFAULT NULL,
  `input_tax` varchar(50) DEFAULT NULL,
  `input_shipping` varchar(50) DEFAULT NULL,
  `input_payment_method` varchar(100) DEFAULT NULL,
  `input_payment_ref` varchar(255) DEFAULT NULL,
  `text_notes` text,
  `visibility` varchar(50) DEFAULT 'show',
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  `created_by` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `read_orders`
--

INSERT INTO `read_orders` (`id`, `hash_id`, `input_first_name`, `input_last_name`, `input_email`, `input_phone`, `text_address`, `input_status`, `input_total`, `input_subtotal`, `input_tax`, `input_shipping`, `input_payment_method`, `input_payment_ref`, `text_notes`, `visibility`, `date_created`, `time_created`, `created_by`) VALUES
(1, 'ORDE4894558', 'Kato', 'Howe', 'gewez@mailinator.com', '+1 (929) 447-9334', '490 West Hague Lane\nQui porro natus qui \nDebitis eos nisi cu, Iure earum ipsum er Quis qui dolores ius\nNG', 'paid', '150.00', '150.00', '0.00', '0.00', 'card', NULL, 'Elit dolore veritat', 'show', '2026-05-14', '21:27:36', 'fa2nt2pr381rh2jmqbcvmsb0bm');

-- --------------------------------------------------------

--
-- Table structure for table `read_order_items`
--

DROP TABLE IF EXISTS `read_order_items`;
CREATE TABLE IF NOT EXISTS `read_order_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(255) NOT NULL,
  `tb` varchar(255) DEFAULT 'read_orders',
  `tb_link` varchar(255) DEFAULT NULL,
  `input_product_id` varchar(255) DEFAULT NULL,
  `input_title` varchar(255) DEFAULT NULL,
  `input_variant` varchar(255) DEFAULT NULL,
  `input_quantity` int DEFAULT '1',
  `input_price` varchar(50) DEFAULT NULL,
  `input_total` varchar(50) DEFAULT NULL,
  `image_1` text,
  `visibility` varchar(50) DEFAULT 'show',
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  `created_by` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `read_order_items`
--

INSERT INTO `read_order_items` (`id`, `hash_id`, `tb`, `tb_link`, `input_product_id`, `input_title`, `input_variant`, `input_quantity`, `input_price`, `input_total`, `image_1`, `visibility`, `date_created`, `time_created`, `created_by`) VALUES
(1, 'oi_6a063e48950c04.61777795', 'read_orders', 'ORDE4894558', 'mmac001', 'Anti-Aging Eye Cream', '', 5, '30.00', '150.00', '/assets/img/products/anti-aging-cream-1.webp', 'show', '2026-05-14', '21:27:36', 'fa2nt2pr381rh2jmqbcvmsb0bm');

-- --------------------------------------------------------

--
-- Table structure for table `read_reviews`
--

DROP TABLE IF EXISTS `read_reviews`;
CREATE TABLE IF NOT EXISTS `read_reviews` (
  `id` int NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(255) NOT NULL,
  `input_product_id` varchar(255) DEFAULT NULL,
  `input_reviewer_name` varchar(255) DEFAULT NULL,
  `input_rating` int DEFAULT '5',
  `text_review` text,
  `visibility` varchar(50) DEFAULT 'show',
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  `created_by` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `read_reviews`
--

INSERT INTO `read_reviews` (`id`, `hash_id`, `input_product_id`, `input_reviewer_name`, `input_rating`, `text_review`, `visibility`, `date_created`, `time_created`, `created_by`) VALUES
(1, 'rev001', 'mmhs001', 'Mia P.', 5, 'I use it both morning and night, and it\'s made such a difference. My skin texture is smoother, pores look smaller, and I wake up with a soft glow.', 'show', '2025-11-17', '09:00:00', 'visitor'),
(2, 'rev002', 'mmhs001', 'Naomi K.', 5, 'I love how elegant and clean this serum feels. It\'s light, refreshing, and makes my skincare routine feel like a spa moment. My skin looks more luminous.', 'show', '2025-11-17', '10:00:00', 'visitor'),
(3, 'rev003', 'mmhs001', 'Elena R.', 5, 'I have combination skin and always worry about serums being too heavy, but this one is perfect. My fine lines have softened.', 'show', '2025-11-17', '11:00:00', 'visitor'),
(4, 'rev004', 'mmhs001', 'Clara M.', 5, 'This product really surprised me. After just a few uses, I noticed how fresh and hydrated my skin felt.', 'show', '2025-11-17', '12:00:00', 'visitor'),
(5, 'rev005', 'mmhs001', 'Sophie L.', 5, 'Three weeks in and my skin has completely transformed. The dullness is gone — now my face looks brighter, smoother, and more even.', 'show', '2025-11-17', '13:00:00', 'visitor'),
(6, 'rev006', 'mmns001', 'Isabella T.', 5, 'The Radiance Boost Serum is incredible. My dark spots have faded significantly after just 4 weeks. I get compliments on my skin all the time now.', 'show', '2025-11-10', '09:00:00', 'visitor'),
(7, 'rev007', 'mmns001', 'Olivia B.', 5, 'Lightweight, absorbs quickly, and actually delivers on its brightening promise. My skin tone looks so much more even.', 'show', '2025-11-05', '10:00:00', 'visitor'),
(8, 'rev008', 'mmvc001', 'Charlotte D.', 5, 'The Velvet Night Cream is worth every penny. I wake up with the softest skin — my husband even noticed the difference!', 'show', '2025-11-12', '09:00:00', 'visitor'),
(9, 'rev009', 'mmac001', 'Amelia F.', 5, 'This eye cream is a game-changer. My dark circles are visibly reduced and the puffiness in the morning is gone. Highly recommend.', 'show', '2025-11-08', '11:00:00', 'visitor'),
(10, 'rev_6a0646980121e7.09021553', 'mmac001', 'Grace M.', 4, 'Very good product. I noticed a difference in texture within a week. The only reason it\'s not 5 stars is the shipping took a bit longer than expected.', 'show', '2026-04-12', '22:03:04', 'system'),
(11, 'rev_6a06469801e675.82184992', 'mmac001', 'Lily B.', 5, 'I\'ve tried many brands, but this one actually delivers. My fine lines are noticeably softer.', 'show', '2026-04-13', '22:03:04', 'system'),
(12, 'rev_6a064698021b88.05795585', 'mmac001', 'Olivia S.', 4, 'Great quality for the price. It absorbs quickly and doesn\'t leave a greasy residue. Will definitely buy again.', 'show', '2026-04-10', '22:03:04', 'system'),
(13, 'rev_6a064698024321.25131266', 'mmac001', 'Emma W.', 5, 'The best I have ever used. The fragrance is subtle and the results are visible. Highly recommend to anyone with sensitive skin.', 'show', '2026-03-27', '22:03:04', 'system'),
(14, 'rev_6a0646980292d4.42527260', 'mmgc001', 'Lily B.', 5, 'I\'ve tried many brands, but this one actually delivers. My fine lines are noticeably softer.', 'show', '2026-04-06', '22:03:04', 'system'),
(15, 'rev_6a06469802b110.36049792', 'mmgc001', 'Emma W.', 5, 'The best I have ever used. The fragrance is subtle and the results are visible. Highly recommend to anyone with sensitive skin.', 'show', '2026-05-01', '22:03:04', 'system'),
(16, 'rev_6a064698031fc0.81004879', 'mmvc001', 'Lily B.', 5, 'I\'ve tried many brands, but this one actually delivers. My fine lines are noticeably softer.', 'show', '2026-04-03', '22:03:04', 'system'),
(17, 'rev_6a064698034070.83692741', 'mmvc001', 'Isabella K.', 5, 'Luxury in a bottle. Every application feels like a spa treatment. My skin looks radiant and glowing.', 'show', '2026-05-08', '22:03:04', 'system'),
(18, 'rev_6a0646980364b3.08212562', 'mmvc001', 'Grace M.', 4, 'Very good product. I noticed a difference in texture within a week. The only reason it\'s not 5 stars is the shipping took a bit longer than expected.', 'show', '2026-03-29', '22:03:04', 'system'),
(19, 'rev_6a06469803b032.35677336', 'mmld001', 'Isabella K.', 5, 'Luxury in a bottle. Every application feels like a spa treatment. My skin looks radiant and glowing.', 'show', '2026-04-24', '22:03:04', 'system'),
(20, 'rev_6a06469803d669.55780481', 'mmld001', 'Lily B.', 5, 'I\'ve tried many brands, but this one actually delivers. My fine lines are noticeably softer.', 'show', '2026-03-16', '22:03:04', 'system'),
(21, 'rev_6a06469803f5d7.33737298', 'mmld001', 'Emma W.', 5, 'The best I have ever used. The fragrance is subtle and the results are visible. Highly recommend to anyone with sensitive skin.', 'show', '2026-04-17', '22:03:04', 'system'),
(22, 'rev_6a064698041453.87160790', 'mmld001', 'Grace M.', 4, 'Very good product. I noticed a difference in texture within a week. The only reason it\'s not 5 stars is the shipping took a bit longer than expected.', 'show', '2026-05-07', '22:03:04', 'system'),
(23, 'rev_6a064698047f80.70580928', 'mmbs001', 'Lily B.', 5, 'I\'ve tried many brands, but this one actually delivers. My fine lines are noticeably softer.', 'show', '2026-03-24', '22:03:04', 'system'),
(24, 'rev_6a06469804a640.33909085', 'mmbs001', 'Isabella K.', 5, 'Luxury in a bottle. Every application feels like a spa treatment. My skin looks radiant and glowing.', 'show', '2026-03-29', '22:03:04', 'system'),
(25, 'rev_6a064698050c85.96226695', 'mmfc001', 'Olivia S.', 4, 'Great quality for the price. It absorbs quickly and doesn\'t leave a greasy residue. Will definitely buy again.', 'show', '2026-03-29', '22:03:04', 'system'),
(26, 'rev_6a0646980530f9.90223661', 'mmfc001', 'Grace M.', 4, 'Very good product. I noticed a difference in texture within a week. The only reason it\'s not 5 stars is the shipping took a bit longer than expected.', 'show', '2026-03-23', '22:03:04', 'system'),
(27, 'rev_6a064698055150.08763409', 'mmfc001', 'Lily B.', 5, 'I\'ve tried many brands, but this one actually delivers. My fine lines are noticeably softer.', 'show', '2026-04-18', '22:03:04', 'system'),
(28, 'rev_6a06469805abf5.09358385', 'mmdh001', 'Emma W.', 5, 'The best I have ever used. The fragrance is subtle and the results are visible. Highly recommend to anyone with sensitive skin.', 'show', '2026-05-06', '22:03:04', 'system'),
(29, 'rev_6a06469805d144.08677087', 'mmdh001', 'Grace M.', 4, 'Very good product. I noticed a difference in texture within a week. The only reason it\'s not 5 stars is the shipping took a bit longer than expected.', 'show', '2026-05-01', '22:03:04', 'system'),
(30, 'rev_6a06469805f1d9.83788760', 'mmdh001', 'Sophia R.', 5, 'Absolutely love this! It\'s been a game changer for my morning routine. My skin feels so much more hydrated and plump.', 'show', '2026-03-27', '22:03:04', 'system'),
(31, 'rev_6a0646980610f7.33508746', 'mmdh001', 'Lily B.', 5, 'I\'ve tried many brands, but this one actually delivers. My fine lines are noticeably softer.', 'show', '2026-03-15', '22:03:04', 'system'),
(32, 'rev_6a064698067f60.36525982', 'mmpp001', 'Olivia S.', 4, 'Great quality for the price. It absorbs quickly and doesn\'t leave a greasy residue. Will definitely buy again.', 'show', '2026-03-18', '22:03:04', 'system'),
(33, 'rev_6a06469806a607.71426467', 'mmpp001', 'Emma W.', 5, 'The best I have ever used. The fragrance is subtle and the results are visible. Highly recommend to anyone with sensitive skin.', 'show', '2026-05-03', '22:03:04', 'system'),
(34, 'rev_6a06469806c121.48930382', 'mmpp001', 'Isabella K.', 5, 'Luxury in a bottle. Every application feels like a spa treatment. My skin looks radiant and glowing.', 'show', '2026-04-20', '22:03:04', 'system'),
(35, 'rev_6a064698071861.06124721', 'mmmc001', 'Emma W.', 5, 'The best I have ever used. The fragrance is subtle and the results are visible. Highly recommend to anyone with sensitive skin.', 'show', '2026-04-14', '22:03:04', 'system'),
(36, 'rev_6a064698074287.04159714', 'mmmc001', 'Isabella K.', 5, 'Luxury in a bottle. Every application feels like a spa treatment. My skin looks radiant and glowing.', 'show', '2026-04-15', '22:03:04', 'system'),
(37, 'rev_6a064698076319.78803499', 'mmmc001', 'Grace M.', 4, 'Very good product. I noticed a difference in texture within a week. The only reason it\'s not 5 stars is the shipping took a bit longer than expected.', 'show', '2026-05-02', '22:03:04', 'system'),
(38, 'rev_6a064698078044.01455815', 'mmmc001', 'Sophia R.', 5, 'Absolutely love this! It\'s been a game changer for my morning routine. My skin feels so much more hydrated and plump.', 'show', '2026-04-12', '22:03:04', 'system'),
(39, 'rev_6a06469807eff9.69636583', 'mmne001', 'Sophia R.', 5, 'Absolutely love this! It\'s been a game changer for my morning routine. My skin feels so much more hydrated and plump.', 'show', '2026-05-03', '22:03:04', 'system'),
(40, 'rev_6a064698081bc5.58891054', 'mmne001', 'Emma W.', 5, 'The best I have ever used. The fragrance is subtle and the results are visible. Highly recommend to anyone with sensitive skin.', 'show', '2026-03-20', '22:03:04', 'system'),
(41, 'e706719b-4fe1-11f1-99a6-c03eba6a1949', 'mmns001', 'Sophia R.', 5, 'Absolutely love this! It has been a game changer for my morning routine.', 'show', '2026-05-14', '00:00:00', 'visitor');

-- --------------------------------------------------------

--
-- Table structure for table `selection_product_category`
--

DROP TABLE IF EXISTS `selection_product_category`;
CREATE TABLE IF NOT EXISTS `selection_product_category` (
  `id` int NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(255) NOT NULL,
  `input_name` varchar(255) DEFAULT NULL,
  `visibility` varchar(50) DEFAULT 'show',
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  `created_by` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `selection_product_category`
--

INSERT INTO `selection_product_category` (`id`, `hash_id`, `input_name`, `visibility`, `date_created`, `time_created`, `created_by`) VALUES
(1, 'cat001', 'Serums', 'show', '2026-05-14', '12:51:54', 'system'),
(2, 'cat002', 'Moisturizers', 'show', '2026-05-14', '12:51:54', 'system'),
(3, 'cat003', 'Eye Care', 'show', '2026-05-14', '12:51:54', 'system'),
(4, 'cat004', 'Cleansers', 'show', '2026-05-14', '12:51:54', 'system'),
(5, 'cat005', 'Treatments', 'show', '2026-05-14', '12:51:54', 'system');

-- --------------------------------------------------------

--
-- Table structure for table `settings_home_features`
--

DROP TABLE IF EXISTS `settings_home_features`;
CREATE TABLE IF NOT EXISTS `settings_home_features` (
  `id` int NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(255) NOT NULL,
  `input_heading` varchar(255) DEFAULT NULL,
  `text_subheading` text,
  `input_card1_title` varchar(255) DEFAULT NULL,
  `image_card1` text,
  `input_card2_title` varchar(255) DEFAULT NULL,
  `image_card2` text,
  `image_card2_icon` text,
  `input_card3_title` varchar(255) DEFAULT NULL,
  `input_card3_link_text` varchar(255) DEFAULT NULL,
  `image_card3` text,
  `input_card4_title` varchar(255) DEFAULT NULL,
  `image_card4` text,
  `image_card4_icon` text,
  `input_card5_title` varchar(255) DEFAULT NULL,
  `image_card5` text,
  `visibility` varchar(50) DEFAULT 'show',
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  `created_by` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `settings_home_features`
--

INSERT INTO `settings_home_features` (`id`, `hash_id`, `input_heading`, `text_subheading`, `input_card1_title`, `image_card1`, `input_card2_title`, `image_card2`, `image_card2_icon`, `input_card3_title`, `input_card3_link_text`, `image_card3`, `input_card4_title`, `image_card4`, `image_card4_icon`, `input_card5_title`, `image_card5`, `visibility`, `date_created`, `time_created`, `created_by`) VALUES
(1, 'hf001', 'Why your skin deserves the best', 'We combine science, care, and transparency to create skincare you can truly trust.', '100k+ happy clients', 'https://cdn.prod.website-files.com/6918bd445678e83950693c7b/691a0d2b63b5a131ce1cdeef_Group 1171274846.avif', 'Connect products with a sense of luxury and self-care', 'https://cdn.prod.website-files.com/6918bd445678e83950693c7b/69193b3c1b4c08ab15934abb_Rectangle 1081.avif', 'https://cdn.prod.website-files.com/6918bd445678e83950693c7b/691a0ca882f9f72fca4b3c57_Vector (1).svg', 'Shop easily online or in our stores', 'View our stores', 'https://cdn.prod.website-files.com/6918bd445678e83950693c7b/69193b3c07895c1d914abe40_Textured Green Surface 1.avif', 'Natural ingredients with proven effects', 'https://cdn.prod.website-files.com/6918bd445678e83950693c7b/69193b3d62dfb2e1c46b2f45_Rectangle 16.avif', 'https://cdn.prod.website-files.com/6918bd445678e83950693c7b/691a1f7658c01e989aa647c6_Vector (4).svg', 'Visible results in just 2 weeks', 'https://cdn.prod.website-files.com/6918bd445678e83950693c7b/69193b3ecb604588eb85d1dd_Rectangle 1082.avif', 'show', '2026-05-14', '21:58:41', 'system');

-- --------------------------------------------------------

--
-- Table structure for table `settings_home_quote`
--

DROP TABLE IF EXISTS `settings_home_quote`;
CREATE TABLE IF NOT EXISTS `settings_home_quote` (
  `id` int NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(255) NOT NULL,
  `text_quote` text,
  `image_author_signature` text,
  `image_1` text,
  `image_2` text,
  `image_3` text,
  `visibility` varchar(50) DEFAULT 'show',
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  `created_by` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `settings_home_quote`
--

INSERT INTO `settings_home_quote` (`id`, `hash_id`, `text_quote`, `image_author_signature`, `image_1`, `image_2`, `image_3`, `visibility`, `date_created`, `time_created`, `created_by`) VALUES
(1, 'hq001', 'Beauty is not just what you see in the mirror - it’s how you feel in your own skin. At Venora, every product is crafted to empower that feeling.', 'https://cdn.prod.website-files.com/6918bd445678e83950693c7b/6924b08072d2458c6c880ee4_- Dr. Isabella Hartman.svg', 'https://cdn.prod.website-files.com/6918bd445678e83950693c7b/69193b57599b3f5cb6f09057_Rectangle 25.avif', 'https://cdn.prod.website-files.com/6918bd445678e83950693c7b/69193b5826fef698896435c0_Rectangle 24.avif', 'https://cdn.prod.website-files.com/6918bd445678e83950693c7b/693c3d2fdcd9e59dc0cba40d_Rectangle 1144.avif', 'show', '2026-05-14', '21:58:41', 'system');

-- --------------------------------------------------------

--
-- Table structure for table `settings_home_ticker`
--

DROP TABLE IF EXISTS `settings_home_ticker`;
CREATE TABLE IF NOT EXISTS `settings_home_ticker` (
  `id` int NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(255) NOT NULL,
  `input_text` varchar(255) DEFAULT NULL,
  `image_icon` text,
  `visibility` varchar(50) DEFAULT 'show',
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  `created_by` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `settings_home_ticker`
--

INSERT INTO `settings_home_ticker` (`id`, `hash_id`, `input_text`, `image_icon`, `visibility`, `date_created`, `time_created`, `created_by`) VALUES
(1, 'ht001', 'GET 25% DISCOUNT', 'https://cdn.prod.website-files.com/6918bd445678e83950693c7b/691ca9c870e18036642ecd2f_logoipsum-274 (1) 11.svg', 'show', '2026-05-14', '21:58:41', 'system');

-- --------------------------------------------------------

--
-- Table structure for table `settings_shop_about`
--

DROP TABLE IF EXISTS `settings_shop_about`;
CREATE TABLE IF NOT EXISTS `settings_shop_about` (
  `id` int NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(255) NOT NULL,
  `input_heading_1` varchar(255) DEFAULT NULL,
  `text_content_1` text,
  `image_1` text,
  `input_heading_2` varchar(255) DEFAULT NULL,
  `text_content_2` text,
  `image_2` text,
  `visibility` varchar(50) DEFAULT 'show',
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  `created_by` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `settings_shop_about`
--

INSERT INTO `settings_shop_about` (`id`, `hash_id`, `input_heading_1`, `text_content_1`, `image_1`, `input_heading_2`, `text_content_2`, `image_2`, `visibility`, `date_created`, `time_created`, `created_by`) VALUES
(1, 'abt001', 'Our journey', 'VENORA was born from a passion for luxurious skincare that empowers women to feel confident in their own skin. Inspired by elegance, nature, and science, every product is carefully crafted to enhance your natural beauty and provide a sensorial experience that elevates your daily routine.', 'https://cdn.prod.website-files.com/6918bd445678e83950693c7b/691a3dd7b40efb5491fb2f21_Rectangle 1116.avif', 'Our mission', 'Our mission is to create premium skincare products that nourish, protect, and rejuvenate your skin, helping every individual embrace their unique beauty with confidence and grace.', 'https://cdn.prod.website-files.com/6918bd445678e83950693c7b/691a3dd622ebe87b20e109bc_Rectangle 1118.avif', 'show', '2026-05-15', '15:30:31', 'system');

-- --------------------------------------------------------

--
-- Table structure for table `settings_shop_about_hero`
--

DROP TABLE IF EXISTS `settings_shop_about_hero`;
CREATE TABLE IF NOT EXISTS `settings_shop_about_hero` (
  `id` int NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(255) NOT NULL,
  `input_label` varchar(255) DEFAULT NULL,
  `input_heading` varchar(255) DEFAULT NULL,
  `text_description` text,
  `image_1` text,
  `visibility` varchar(50) DEFAULT 'show',
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  `created_by` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `settings_shop_about_hero`
--

INSERT INTO `settings_shop_about_hero` (`id`, `hash_id`, `input_label`, `input_heading`, `text_description`, `image_1`, `visibility`, `date_created`, `time_created`, `created_by`) VALUES
(1, 'abhero001', 'Our Story', 'Born from a passion for luxurious skincare', 'VENORA was founded with one belief: every woman deserves skincare that works as beautifully as it feels. Inspired by elegance, nature, and the latest in skin science, we craft every product to enhance your natural radiance and turn your daily routine into a sensorial ritual.', 'https://cdn.prod.website-files.com/6918bd445678e83950693c7b/691a3dd6ef0cfc88213b0455_Serene Nature Portrait 1.avif', 'show', '2026-05-14', '12:51:53', 'system');

-- --------------------------------------------------------

--
-- Table structure for table `settings_shop_about_mission`
--

DROP TABLE IF EXISTS `settings_shop_about_mission`;
CREATE TABLE IF NOT EXISTS `settings_shop_about_mission` (
  `id` int NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(255) NOT NULL,
  `input_heading` varchar(255) DEFAULT NULL,
  `text_description` text,
  `image_1` text,
  `visibility` varchar(50) DEFAULT 'show',
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  `created_by` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `settings_shop_about_mission`
--

INSERT INTO `settings_shop_about_mission` (`id`, `hash_id`, `input_heading`, `text_description`, `image_1`, `visibility`, `date_created`, `time_created`, `created_by`) VALUES
(1, 'abms001', 'Our mission', 'We believe that skincare should be a ritual of self-love. Our mission is to provide you with clean, effective products that bring out your natural beauty while caring for your skin and the planet. Every formula is developed with intention — combining the power of nature with the precision of science.', 'https://cdn.prod.website-files.com/6918bd445678e83950693c7b/691a3dda36fa30e73fdf77ab_Rectangle 1115.avif', 'show', '2026-05-14', '14:11:00', 'system');

-- --------------------------------------------------------

--
-- Table structure for table `settings_shop_about_story`
--

DROP TABLE IF EXISTS `settings_shop_about_story`;
CREATE TABLE IF NOT EXISTS `settings_shop_about_story` (
  `id` int NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(255) NOT NULL,
  `input_heading` varchar(255) DEFAULT NULL,
  `text_description` text,
  `image_1` text,
  `visibility` varchar(50) DEFAULT 'show',
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  `created_by` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `settings_shop_about_story`
--

INSERT INTO `settings_shop_about_story` (`id`, `hash_id`, `input_heading`, `text_description`, `image_1`, `visibility`, `date_created`, `time_created`, `created_by`) VALUES
(1, 'abst001', 'Our journey', 'VENORA was born from a passion for luxurious skincare that empowers women to feel confident in their own skin. Inspired by elegance, nature, and science, every product is carefully crafted to enhance your natural beauty and provide a sensorial experience that elevates your daily routine.', 'https://cdn.prod.website-files.com/6918bd445678e83950693c7b/691a3dd7b40efb5491fb2f21_Rectangle 1116.avif', 'show', '2026-05-14', '14:11:00', 'system');

-- --------------------------------------------------------

--
-- Table structure for table `settings_shop_config`
--

DROP TABLE IF EXISTS `settings_shop_config`;
CREATE TABLE IF NOT EXISTS `settings_shop_config` (
  `id` int NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(255) NOT NULL,
  `input_name` varchar(255) DEFAULT NULL,
  `input_tagline` varchar(255) DEFAULT NULL,
  `input_email` varchar(255) DEFAULT NULL,
  `input_email_from` varchar(255) DEFAULT NULL,
  `input_email_smtp_host` varchar(255) DEFAULT 'smtp.gmail.com',
  `input_email_smtp_secure_type` varchar(20) DEFAULT 'tls',
  `input_email_smtp_port` varchar(10) DEFAULT '587',
  `input_email_password` varchar(255) DEFAULT NULL,
  `input_phone` varchar(50) DEFAULT NULL,
  `input_address` varchar(255) DEFAULT NULL,
  `input_currency` varchar(10) DEFAULT 'USD',
  `input_currency_symbol` varchar(10) DEFAULT '$',
  `input_tax_rate` varchar(20) DEFAULT '0',
  `input_shipping_rate` varchar(20) DEFAULT '5.99',
  `input_free_shipping` varchar(20) DEFAULT '75',
  `input_seo_keywords` text,
  `text_description` text,
  `image_1` text,
  `image_2` text,
  `visibility` varchar(50) DEFAULT 'show',
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  `created_by` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `settings_shop_config`
--

INSERT INTO `settings_shop_config` (`id`, `hash_id`, `input_name`, `input_tagline`, `input_email`, `input_email_from`, `input_email_smtp_host`, `input_email_smtp_secure_type`, `input_email_smtp_port`, `input_email_password`, `input_phone`, `input_address`, `input_currency`, `input_currency_symbol`, `input_tax_rate`, `input_shipping_rate`, `input_free_shipping`, `input_seo_keywords`, `text_description`, `image_1`, `image_2`, `visibility`, `date_created`, `time_created`, `created_by`) VALUES
(1, 'cfg001', 'Venora', 'Luxury Skincare', 'hello@venora.com', '', 'smtp.gmail.com', 'tls', '587', '', '', '', 'USD', '$', '0', '5.99', '75', 'luxury skincare, natural beauty, moisturizer, serum, cleanser, eye cream', 'VENORA was born from a passion for luxurious skincare that empowers women to feel confident in their own skin. Inspired by elegance, nature, and science, every product is carefully crafted to enhance your natural beauty.', '/assets/img/brand/venora-white.svg', '/assets/img/brand/venora-dark.svg', 'show', '2026-05-14', '12:51:53', 'system');

-- --------------------------------------------------------

--
-- Table structure for table `settings_shop_contact`
--

DROP TABLE IF EXISTS `settings_shop_contact`;
CREATE TABLE IF NOT EXISTS `settings_shop_contact` (
  `id` int NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(255) NOT NULL,
  `input_label` varchar(255) DEFAULT NULL,
  `input_heading` varchar(255) DEFAULT NULL,
  `text_description` text,
  `visibility` varchar(50) DEFAULT 'show',
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  `created_by` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `settings_shop_contact`
--

INSERT INTO `settings_shop_contact` (`id`, `hash_id`, `input_label`, `input_heading`, `text_description`, `visibility`, `date_created`, `time_created`, `created_by`) VALUES
(1, 'cp001', 'Get in Touch', 'We would love to hear from you', 'Have a question about a product, an order, or just want to say hello? Our team is here to help. We respond to all enquiries within 24 hours.', 'show', '2026-05-14', '12:51:54', 'system');

-- --------------------------------------------------------

--
-- Table structure for table `settings_shop_content_1`
--

DROP TABLE IF EXISTS `settings_shop_content_1`;
CREATE TABLE IF NOT EXISTS `settings_shop_content_1` (
  `id` int NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(255) NOT NULL,
  `input_heading` varchar(255) DEFAULT NULL,
  `input_badge` varchar(100) DEFAULT NULL,
  `text_description` text,
  `image_1` text,
  `visibility` varchar(50) DEFAULT 'show',
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  `created_by` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `settings_shop_content_1`
--

INSERT INTO `settings_shop_content_1` (`id`, `hash_id`, `input_heading`, `input_badge`, `text_description`, `image_1`, `visibility`, `date_created`, `time_created`, `created_by`) VALUES
(1, 'cnt1001', 'Dermatology-Tested Skincare You Can Trust', 'Dermatologist tested', 'Our formulas are developed in collaboration with dermatologists to ensure maximum comfort and visible results for every skin type.', 'https://cdn.prod.website-files.com/6918bd445678e83950693c7b/691940b886702b2d2296b5f3_Rectangle 1071.avif', 'show', '2026-05-14', '13:45:04', 'system');

-- --------------------------------------------------------

--
-- Table structure for table `settings_shop_content_2`
--

DROP TABLE IF EXISTS `settings_shop_content_2`;
CREATE TABLE IF NOT EXISTS `settings_shop_content_2` (
  `id` int NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(255) NOT NULL,
  `input_heading` varchar(255) DEFAULT NULL,
  `input_badge` varchar(100) DEFAULT NULL,
  `text_description` text,
  `image_1` text,
  `visibility` varchar(50) DEFAULT 'show',
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  `created_by` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `settings_shop_content_2`
--

INSERT INTO `settings_shop_content_2` (`id`, `hash_id`, `input_heading`, `input_badge`, `text_description`, `image_1`, `visibility`, `date_created`, `time_created`, `created_by`) VALUES
(1, 'cnt2001', 'Naturally Clean. Always Paraben-Free.', 'Paraben free', 'Your skin deserves only the best. That is why every product we create is 100% paraben-free, formulated with gentle and natural ingredients.', 'https://cdn.prod.website-files.com/6918bd445678e83950693c7b/69193b3ec862043fc0be46ab_Rectangle 1072.avif', 'show', '2026-05-14', '13:45:04', 'system');

-- --------------------------------------------------------

--
-- Table structure for table `settings_shop_footer`
--

DROP TABLE IF EXISTS `settings_shop_footer`;
CREATE TABLE IF NOT EXISTS `settings_shop_footer` (
  `id` int NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(255) NOT NULL,
  `input_cta_heading` varchar(255) DEFAULT NULL,
  `input_cta_btn` varchar(255) DEFAULT NULL,
  `input_newsletter_heading` varchar(255) DEFAULT NULL,
  `input_powered_by` varchar(255) DEFAULT NULL,
  `input_instagram` varchar(255) DEFAULT NULL,
  `input_facebook` varchar(255) DEFAULT NULL,
  `input_linkedin` varchar(255) DEFAULT NULL,
  `visibility` varchar(50) DEFAULT 'show',
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  `created_by` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `settings_shop_footer`
--

INSERT INTO `settings_shop_footer` (`id`, `hash_id`, `input_cta_heading`, `input_cta_btn`, `input_newsletter_heading`, `input_powered_by`, `input_instagram`, `input_facebook`, `input_linkedin`, `visibility`, `date_created`, `time_created`, `created_by`) VALUES
(1, 'ftr001', 'Ready for Your Best Skin Yet?', 'Book a Consultation', 'Stay updated with the latest from Venora!', '', 'https://www.instagram.com/', 'https://www.facebook.com/', 'https://www.linkedin.com/', 'show', '2026-05-14', '12:51:54', 'system');

-- --------------------------------------------------------

--
-- Table structure for table `settings_shop_hero`
--

DROP TABLE IF EXISTS `settings_shop_hero`;
CREATE TABLE IF NOT EXISTS `settings_shop_hero` (
  `id` int NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(255) NOT NULL,
  `input_heading` varchar(255) DEFAULT NULL,
  `input_btn1_label` varchar(255) DEFAULT NULL,
  `input_btn2_label` varchar(255) DEFAULT NULL,
  `input_trust_text` varchar(255) DEFAULT NULL,
  `input_rating` varchar(20) DEFAULT NULL,
  `input_video_url` varchar(500) DEFAULT NULL,
  `image_1` text,
  `visibility` varchar(50) DEFAULT 'show',
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  `created_by` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `settings_shop_hero`
--

INSERT INTO `settings_shop_hero` (`id`, `hash_id`, `input_heading`, `input_btn1_label`, `input_btn2_label`, `input_trust_text`, `input_rating`, `input_video_url`, `image_1`, `visibility`, `date_created`, `time_created`, `created_by`) VALUES
(1, 'hero001', 'Your natural beauty, expressed with care', 'Shop now', 'Our collection', 'Trusted by 300+ clients', '4.9/5', 'https://videos.pexels.com/video-files/7304311/7304311-hd_1920_1080_30fps.mp4', '/assets/img/products/radiance-serum-1.webp', 'show', '2026-05-14', '12:51:53', 'system');

-- --------------------------------------------------------

--
-- Table structure for table `settings_shop_privacy`
--

DROP TABLE IF EXISTS `settings_shop_privacy`;
CREATE TABLE IF NOT EXISTS `settings_shop_privacy` (
  `id` int NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(255) NOT NULL,
  `input_heading` varchar(255) DEFAULT NULL,
  `text_content` text,
  `visibility` varchar(50) DEFAULT 'show',
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  `created_by` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `settings_shop_privacy`
--

INSERT INTO `settings_shop_privacy` (`id`, `hash_id`, `input_heading`, `text_content`, `visibility`, `date_created`, `time_created`, `created_by`) VALUES
(1, 'priv001', 'Privacy Policy', '<h4 style=\"color: var(--dark-green-colour, #072708); margin-top: 30px;\">1. Introduction</h4>\r\n<p>Welcome to our store. We are committed to protecting your personal information and your right to privacy. If you have any questions or concerns about our policy, or our practices with regards to your personal information, please contact us.</p>\r\n<h4 style=\"color: var(--dark-green-colour, #072708); margin-top: 30px;\">2. Information We Collect</h4>\r\n<p>We collect personal information that you voluntarily provide to us when registering at the website, expressing an interest in obtaining information about us or our products and services, when participating in activities on the website, or otherwise contacting us.</p>\r\n<p>The personal information that we collect depends on the context of your interactions with us and the website, the choices you make and the products and features you use.</p>\r\n<h4 style=\"color: var(--dark-green-colour, #072708); margin-top: 30px;\">3. How We Use Your Information</h4>\r\n<p>We use personal information collected via our website for a variety of business purposes described below. We process your personal information for these purposes in reliance on our legitimate business interests, in order to enter into or perform a contract with you, with your consent, and/or for compliance with our legal obligations.</p>\r\n<ul>\r\n<li>To facilitate account creation and logon process.</li>\r\n<li>To send you marketing and promotional communications.</li>\r\n<li>To fulfill and manage your orders.</li>\r\n</ul>\r\n<h4 style=\"color: var(--dark-green-colour, #072708); margin-top: 30px;\">4. Will Your Information be Shared with Anyone?</h4>\r\n<p>We only share information with your consent, to comply with laws, to provide you with services, to protect your rights, or to fulfill business obligations.</p>\r\n<h4 style=\"color: var(--dark-green-colour, #072708); margin-top: 30px;\">5. How Long Do We Keep Your Information?</h4>\r\n<p>We will only keep your personal information for as long as it is necessary for the purposes set out in this privacy policy, unless a longer retention period is required or permitted by law (such as tax, accounting or other legal requirements).</p>\r\n<h4 style=\"color: var(--dark-green-colour, #072708); margin-top: 30px;\">6. Contact Us</h4>\r\n<p>If you have questions or comments about this policy, you may contact us using the information provided on our Contact page.</p>', 'show', '2026-05-14', '22:21:53', 'system');

-- --------------------------------------------------------

--
-- Table structure for table `settings_shop_testimonial`
--

DROP TABLE IF EXISTS `settings_shop_testimonial`;
CREATE TABLE IF NOT EXISTS `settings_shop_testimonial` (
  `id` int NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(255) NOT NULL,
  `text_quote` text,
  `input_author` varchar(255) DEFAULT NULL,
  `image_1` text,
  `visibility` varchar(50) DEFAULT 'show',
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  `created_by` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `settings_shop_testimonial`
--

INSERT INTO `settings_shop_testimonial` (`id`, `hash_id`, `text_quote`, `input_author`, `image_1`, `visibility`, `date_created`, `time_created`, `created_by`) VALUES
(1, 'tst001', 'Beauty is not just what you see in the mirror - it\'s how you feel in your own skin. At Venora, every product is crafted to empower that feeling.', 'Dr. Isabella Hartman', 'https://cdn.prod.website-files.com/6918bd445678e83950693c7b/6924b08072d2458c6c880ee4_- Dr. Isabella Hartman.svg', 'show', '2026-05-14', '13:45:04', 'system');

-- --------------------------------------------------------

--
-- Table structure for table `settings_website_info`
--

DROP TABLE IF EXISTS `settings_website_info`;
CREATE TABLE IF NOT EXISTS `settings_website_info` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(225) NOT NULL,
  `input_name` varchar(225) NOT NULL,
  `input_tagline` varchar(255) DEFAULT '',
  `input_email` varchar(225) DEFAULT NULL,
  `input_phone_number` varchar(225) DEFAULT NULL,
  `input_whatsapp_number` varchar(100) DEFAULT '',
  `input_address` varchar(225) NOT NULL,
  `input_linkedin` varchar(225) NOT NULL,
  `input_podcast_link` varchar(255) DEFAULT '',
  `input_apple_podcast` varchar(500) DEFAULT '',
  `input_book_session_url` varchar(500) DEFAULT '',
  `input_facebook` varchar(225) NOT NULL,
  `input_instagram` varchar(225) NOT NULL,
  `input_behance` varchar(225) DEFAULT NULL,
  `input_dribbble` varchar(225) DEFAULT NULL,
  `input_twitter` varchar(225) NOT NULL,
  `input_pinterest` varchar(225) DEFAULT NULL,
  `image_1` text NOT NULL,
  `text_description` text NOT NULL,
  `input_day` varchar(225) DEFAULT NULL,
  `input_time` varchar(225) DEFAULT NULL,
  `input_country` varchar(225) DEFAULT NULL,
  `input_seo_keywords` text,
  `visibility` varchar(20) NOT NULL,
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  `input_email_smtp_port` varchar(225) DEFAULT NULL,
  `input_email_password` varchar(225) DEFAULT NULL,
  `input_email_smtp_host` varchar(225) DEFAULT NULL,
  `input_email_smtp_secure_type` varchar(225) DEFAULT NULL,
  `input_email_from` varchar(225) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `input_image_width` varchar(225) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `settings_website_info`
--

INSERT INTO `settings_website_info` (`id`, `hash_id`, `input_name`, `input_tagline`, `input_email`, `input_phone_number`, `input_whatsapp_number`, `input_address`, `input_linkedin`, `input_podcast_link`, `input_apple_podcast`, `input_book_session_url`, `input_facebook`, `input_instagram`, `input_behance`, `input_dribbble`, `input_twitter`, `input_pinterest`, `image_1`, `text_description`, `input_day`, `input_time`, `input_country`, `input_seo_keywords`, `visibility`, `date_created`, `time_created`, `input_email_smtp_port`, `input_email_password`, `input_email_smtp_host`, `input_email_smtp_secure_type`, `input_email_from`, `created_by`, `input_image_width`) VALUES
(1, '345yjhgfse3456yhbgvfc', 'Mike Mahony', 'Fractional CTO', 'hello@mikemahony.com', '', '447344225808', 'North Las Vegas, Nevada', 'https://www.linkedin.com/in/michaeljmahony/', 'https://gtle.show', 'https://podcasts.apple.com/us/podcast/gaining-the-technology-leadership-edge/id1664607772', 'https://GetYourVirtualCTO.com/StrategySession', 'http://facebook.com', 'http://instagram.com', 'https://behance.net', 'https://dibbble.com', 'http://twitter.com', 'http://pinterest.com', '/uploads/mm_logo.jpg', 'Mike Mahony helps tech leaders and NetSuite-driven operators eliminate decision bottlenecks and build autonomous, high-performing teams.', 'Monday-Friday', '8AM-9PM', 'Nigeria', 'Fractional CTO, NetSuite Expert, DCAT Method, Tech Leadership', 'show', '2021-06-20', '11:33:49', '587', '', 'smtp.gmail.com', 'tls', 'hello@nextshinegroup.co.uk', NULL, '48');

-- --------------------------------------------------------

--
-- Table structure for table `website_status`
--

DROP TABLE IF EXISTS `website_status`;
CREATE TABLE IF NOT EXISTS `website_status` (
  `id` int NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(255) NOT NULL,
  `status` varchar(50) DEFAULT 'live',
  `color` varchar(100) DEFAULT '#072708',
  `secondary_color` varchar(100) DEFAULT '#202c22',
  `bgcolor_background` varchar(100) DEFAULT '#f6f6f6',
  `bgcolor_surface` varchar(100) DEFAULT '#ffffff',
  `textcolor_heading` varchar(100) DEFAULT '#072708',
  `textcolor_body` varchar(100) DEFAULT '#5c5f6a',
  `textcolor_muted` varchar(100) DEFAULT '#b5b5b5',
  `visibility` varchar(50) DEFAULT 'show',
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  `created_by` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `website_status`
--

INSERT INTO `website_status` (`id`, `hash_id`, `status`, `color`, `secondary_color`, `bgcolor_background`, `bgcolor_surface`, `textcolor_heading`, `textcolor_body`, `textcolor_muted`, `visibility`, `date_created`, `time_created`, `created_by`) VALUES
(1, 'ws001', 'live', '#072708', '#202c22', '#f6f6f6', '#ffffff', '#072708', '#5c5f6a', '#b5b5b5', 'show', '2026-05-14', '13:02:01', 'system');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
