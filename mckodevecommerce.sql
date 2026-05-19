-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 18, 2026 at 01:29 PM
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
-- Table structure for table `addition_product_images`
--

DROP TABLE IF EXISTS `addition_product_images`;
CREATE TABLE IF NOT EXISTS `addition_product_images` (
  `id` int NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(255) NOT NULL,
  `tb` varchar(255) DEFAULT 'panel_product',
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

-- addition_product_images: populated via ADMC admin when extra gallery images are uploaded per product
-- tb = 'panel_product', tb_link = panel_product.hash_id

-- --------------------------------------------------------

--
-- Table structure for table `addition_special_offers`
--
CREATE TABLE IF NOT EXISTS `addition_special_offers` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(255) NOT NULL,
  `tb` varchar(255) DEFAULT 'panel_product' COMMENT 'parent table',
  `tb_link` varchar(255) DEFAULT NULL COMMENT 'panel_product.hash_id',
  `input_offer_title` varchar(255) DEFAULT NULL,
  `input_discount_percent` int DEFAULT NULL COMMENT '0-100',
  `input_discount_fixed` varchar(20) DEFAULT NULL COMMENT 'flat amount off',
  `input_promo_code` varchar(50) DEFAULT NULL,
  `input_offer_start` datetime DEFAULT NULL,
  `input_offer_end` datetime DEFAULT NULL,
  `input_badge_label` varchar(50) DEFAULT 'SALE' COMMENT 'e.g. SALE, HOT, 20% OFF',
  `visibility` varchar(50) DEFAULT 'show',
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  `created_by` varchar(255) DEFAULT 'system',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_offers_hash` (`hash_id`),
  KEY `idx_offers_product` (`tb_link`),
  KEY `idx_offers_end` (`input_offer_end`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  `image_hash_id` varchar(225) NOT NULL,
  `asset_hash_id` varchar(255) NOT NULL,
  `image_1` text,
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  `hash_id` varchar(225) DEFAULT NULL,
  `created_by` varchar(225) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=122 DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `panel_pages`
--

INSERT INTO `panel_pages` (`id`, `hash_id`, `input_name`, `bool_method`, `boolkey_method`, `input_link`, `label`, `input_file`, `file_path`, `input_iframe_link`, `input_order`, `add_pages`, `visibility`, `date_created`, `time_created`, `created_by`) VALUES
(1, 'nav001', 'Home', NULL, NULL, '/', NULL, NULL, NULL, NULL, '1', NULL, 'show', '2026-05-15', '17:09:33', 'system'),
(2, 'nav002', 'About', NULL, NULL, '/about', NULL, NULL, NULL, NULL, '2', NULL, 'show', '2026-05-15', '17:09:33', 'system'),
(3, 'nav003', 'Products', NULL, NULL, '/products', NULL, NULL, NULL, NULL, '3', NULL, 'show', '2026-05-15', '17:09:33', 'system'),
(4, 'nav004', 'Contact', NULL, NULL, '/contact', NULL, NULL, NULL, NULL, '4', NULL, 'show', '2026-05-15', '17:09:33', 'system');

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
-- Table structure for table `panel_product`
--

CREATE TABLE IF NOT EXISTS `panel_product` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `input_product_name` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `text_description` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `select_product_category` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `input_discount_percentage` char(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dated_discount_enddate` char(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hash_id` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_2` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `visibility` char(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_created` date DEFAULT NULL,
  `time_created` time DEFAULT NULL,
  `created_by` char(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `input_order` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `hash_id` (`hash_id`),
  KEY `input_order` (`input_order`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `panel_product`
--

INSERT INTO `panel_product` (`id`, `hash_id`, `input_product_name`, `text_description`, `select_product_category`, `input_discount_percentage`, `dated_discount_enddate`, `image_2`, `visibility`, `date_created`, `time_created`, `created_by`, `input_order`) VALUES
(1,  'vnr-srs-001', 'Radiance Boost Serum',      'Bring back your skin\'s natural glow with VENORA\'s Radiance Boost Serum. Enriched with Vitamin C and Hyaluronic Acid, this lightweight serum brightens dull skin, smooths fine lines, and deeply hydrates for a luminous, healthy complexion. Ideal for daily use, it absorbs quickly, leaving your skin soft, radiant, and revitalized.', '1', '', NULL, '/assets/img/products/radiance-serum-1.webp',     'show', '2026-01-01', '12:00:00', 'system', 1),
(2,  'vnr-eye-001', 'Anti-Aging Eye Cream',       'Combat the signs of aging around your eyes with Venora Anti-Aging Eye Cream, a rich yet fast-absorbing formula designed to reduce fine lines, wrinkles, and sagging while hydrating and firming the delicate eye area. Enriched with Retinol and Peptides, this cream supports skin renewal, improves elasticity, and promotes a youthful, lifted appearance.', '2', '', NULL, '/assets/img/products/anti-aging-cream-1.webp',   'show', '2026-01-01', '12:00:00', 'system', 2),
(3,  'vnr-cln-001', 'Refreshing Gel Cleanser',    'Start your skincare routine with Refreshing Gel Cleanser, a gentle yet effective formula that purifies and revitalizes your skin. Infused with Green Tea Extract and Aloe Vera, this refreshing gel removes impurities, excess oil, and makeup without stripping your skin of its natural moisture.', '3', '', NULL, '/assets/img/products/gel-cleanser-1.webp',        'show', '2026-01-01', '12:00:00', 'system', 3),
(4,  'vnr-mos-001', 'Hydrasilk Moisturizer',      'Nourish and hydrate your skin with Hydrasilk Moisturizer, a luxurious, lightweight cream that delivers deep moisture while leaving your skin silky-smooth and radiant. Enriched with Hyaluronic Acid and Squalane, this moisturizer replenishes hydration, softens fine lines, and restores elasticity.', '4', '', NULL, '/assets/img/products/hydrasilk-1.webp',          'show', '2026-01-01', '12:00:00', 'system', 4),
(5,  'vnr-nit-001', 'Velvet Night Cream',          'Replenish and rejuvenate your skin overnight with Venora Velvet Night Cream, a rich, luxurious formula designed to deeply hydrate and repair while you sleep. Infused with Retinol and Hyaluronic Acid, this cream smooths fine lines, restores elasticity, and nourishes the skin for a soft, supple, and radiant complexion by morning.', '5', '', NULL, '/assets/img/products/velvet-cream-1.webp',       'show', '2026-01-01', '12:00:00', 'system', 5),
(6,  'vnr-day-001', 'Luminous Day Cream',          'Start your day with radiant, hydrated skin using Venora Luminous Day Cream, a luxurious, lightweight moisturizer designed to brighten and protect your complexion. Enriched with Vitamin C and Hyaluronic Acid, this cream deeply hydrates, smooths fine lines, and promotes a luminous, even skin tone.', '4', '', NULL, '/assets/img/products/luminous-day-1.webp',       'show', '2026-01-01', '12:00:00', 'system', 6),
(7,  'vnr-eye-002', 'Brightening Eye Serum',       'Illuminate and refresh your delicate eye area with Venora Brightening Eye Serum, a lightweight, fast-absorbing formula designed to reduce dark circles, puffiness, and fine lines. Enriched with Vitamin C and Peptides, this serum brightens the under-eye area, smooths texture, and provides gentle hydration.', '2', '', NULL, '/assets/img/products/brightening-serum-1.webp',  'show', '2026-01-01', '12:00:00', 'system', 7),
(8,  'vnr-fcl-001', 'Gentle Foaming Cleanser',    'Experience a delicate yet thorough cleanse with Venora Gentle Foaming Cleanser, a lightweight foaming formula that purifies and refreshes your skin without stripping its natural moisture. Enriched with Chamomile Extract and Aloe Vera, this gentle cleanser removes impurities, excess oil, and light makeup.', '3', '', NULL, '/assets/img/products/foaming-cleanser-1.webp',   'show', '2026-01-01', '12:00:00', 'system', 8),
(9,  'vnr-dhy-001', 'Deep Hydration Serum',        'Quench your skin\'s thirst with Venora Deep Hydration Serum, a luxurious, fast-absorbing formula designed to deliver intense moisture and restore suppleness. Enriched with Hyaluronic Acid and Aloe Vera, this serum deeply penetrates the skin to smooth fine lines, plump dehydrated areas, and leave your complexion radiant.', '1', '', NULL, '/assets/img/products/deep-hydration-1.webp',     'show', '2026-01-01', '12:00:00', 'system', 9),
(10, 'vnr-por-001', 'Pore Perfect Treatment',      'Refine and clarify your complexion with Venora Pore Perfect Serum. This lightweight, fast-absorbing serum is enriched with Niacinamide and Salicylic Acid to minimize the appearance of pores, control excess oil, and smooth skin texture. Ideal for combination and oily skin.', '5', '', NULL, '/assets/img/products/pore-perfect-1.webp',       'show', '2026-01-01', '12:00:00', 'system', 10),
(11, 'vnr-mcl-001', 'Hydrating Milk Cleanser',    'Gently cleanse and nourish your skin with Venora Hydrating Milk Cleanser, a luxurious formula that removes impurities and makeup while delivering lasting hydration. Enriched with Shea Butter and Aloe Vera, this creamy milk cleanser softens and soothes the skin.', '3', '', NULL, '/assets/img/products/milk-cleanser-1.webp',      'show', '2026-01-01', '12:00:00', 'system', 11),
(12, 'vnr-ney-001', 'Soothing Night Eye Cream',    'Repair and rejuvenate your delicate eye area overnight with Venora Soothing Night Eye Cream, a rich, calming formula designed to reduce puffiness, dark circles, and fine lines while you sleep. Enriched with Hyaluronic Acid and Chamomile Extract, this cream deeply hydrates and soothes.', '2', '', NULL, '/assets/img/products/night-eye-cream-1.webp',    'show', '2026-01-01', '12:00:00', 'system', 12);


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
-- Table structure for table `panel_trust_badges`
--

DROP TABLE IF EXISTS `panel_trust_badges`;
CREATE TABLE IF NOT EXISTS `panel_trust_badges` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(255) NOT NULL,
  `input_label` varchar(255) DEFAULT NULL COMMENT 'Badge text e.g. Dermatologist Tested',
  `input_icon` text COMMENT 'SVG path or icon class',
  `input_order` int DEFAULT '0',
  `visibility` varchar(50) DEFAULT 'show',
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  `created_by` varchar(255) DEFAULT 'system',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_badge_hash` (`hash_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `panel_trust_badges`
--

INSERT INTO `panel_trust_badges` (`id`, `hash_id`, `input_label`, `input_icon`, `input_order`, `visibility`, `date_created`, `time_created`, `created_by`) VALUES
(1, 'bdg001', 'Dermatologist Tested', 'M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z', 1, 'show', '2026-05-15', '17:12:40', 'system'),
(2, 'bdg002', 'Cruelty Free', 'M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z', 2, 'show', '2026-05-15', '17:12:40', 'system'),
(3, 'bdg003', '30-Day Returns', 'M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z M9 12l2 2 4-4', 3, 'show', '2026-05-15', '17:12:40', 'system');


-- --------------------------------------------------------

--
-- Table structure for table `read_cart`
--

DROP TABLE IF EXISTS `read_cart`;
CREATE TABLE IF NOT EXISTS `read_cart` (
  `id` int NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(255) NOT NULL,
  `input_session_id` varchar(255) DEFAULT NULL,
  `input_user_id` varchar(255) DEFAULT NULL COMMENT 'read_users.hash_id — NULL for guest sessions',
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
) ENGINE=MyISAM AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `read_cart`
--

INSERT INTO `read_cart` (`id`, `hash_id`, `input_session_id`, `input_user_id`, `input_product_id`, `input_variant_id`, `input_variant`, `input_quantity`, `input_price`, `visibility`, `date_created`, `time_created`, `created_by`) VALUES
(1, 'cart_6a05b7e1668db1.73647043', 'fa2nt2pr381rh2jmqbcvmsb0bm', NULL, 'mmgc001', '', '', 1, '30.00', 'hide', '2026-05-14', '11:54:09', 'fa2nt2pr381rh2jmqbcvmsb0bm'),
(2, 'cart_6a05b8121c0846.87190345', 'fa2nt2pr381rh2jmqbcvmsb0bm', NULL, 'mmhs001', '', '', 1, '70.00', 'hide', '2026-05-14', '11:54:58', 'fa2nt2pr381rh2jmqbcvmsb0bm'),
(3, 'cart_6a05c3bd610bf4.84034033', 'fa2nt2pr381rh2jmqbcvmsb0bm', NULL, 'mmmc001', '', '', 1, '32.00', 'hide', '2026-05-14', '12:44:45', 'fa2nt2pr381rh2jmqbcvmsb0bm'),
(4, 'cart_6a05c48580b151.04726145', 'fa2nt2pr381rh2jmqbcvmsb0bm', NULL, 'mmvc001', 'var006', '50ml', 6, '65.00', 'hide', '2026-05-14', '12:48:05', 'fa2nt2pr381rh2jmqbcvmsb0bm'),
(5, 'cart_6a062ae64670f3.61855960', 'fa2nt2pr381rh2jmqbcvmsb0bm', NULL, 'mmns001', 'var001', '30ml', 2, '50.00', 'hide', '2026-05-14', '20:04:54', 'fa2nt2pr381rh2jmqbcvmsb0bm'),
(6, 'cart_6a0633994d9749.53899667', 'fa2nt2pr381rh2jmqbcvmsb0bm', NULL, 'mmns001', '', '', 2, '50.00', 'hide', '2026-05-14', '20:42:01', 'fa2nt2pr381rh2jmqbcvmsb0bm'),
(7, 'cart_6a0633d1a9c6c1.34702317', 'fa2nt2pr381rh2jmqbcvmsb0bm', NULL, 'mmvc001', '', '', 1, '65.00', 'hide', '2026-05-14', '20:42:57', 'fa2nt2pr381rh2jmqbcvmsb0bm'),
(8, 'cart_6a063dedc5ab13.71028933', '', NULL, 'mmns001', '', '', 1, '50.00', 'show', '2026-05-14', '21:26:05', ''),
(9, 'cart_6a063e0d456313.26537628', 'fa2nt2pr381rh2jmqbcvmsb0bm', NULL, 'mmfc001', '', '', 2, '28.00', 'hide', '2026-05-14', '21:26:37', 'fa2nt2pr381rh2jmqbcvmsb0bm'),
(10, 'cart_6a063e1d4d0882.35299454', 'fa2nt2pr381rh2jmqbcvmsb0bm', NULL, 'mmac001', '', '', 5, '30.00', 'hide', '2026-05-14', '21:26:53', 'fa2nt2pr381rh2jmqbcvmsb0bm'),
(11, 'cart_6a0640e3df2d88.83084502', 'fa2nt2pr381rh2jmqbcvmsb0bm', NULL, 'mmac001', '', '', 2, '30.00', 'hide', '2026-05-14', '21:38:43', 'fa2nt2pr381rh2jmqbcvmsb0bm'),
(12, 'cart_6a0640f0ea63d9.24812077', 'fa2nt2pr381rh2jmqbcvmsb0bm', NULL, 'mmns001', '', '', 2, '50.00', 'hide', '2026-05-14', '21:38:56', 'fa2nt2pr381rh2jmqbcvmsb0bm'),
(13, 'cart_6a0642a1149ce0.99328318', 'fa2nt2pr381rh2jmqbcvmsb0bm', NULL, 'mmfc001', '', '', 1, '28.00', 'hide', '2026-05-14', '21:46:09', 'fa2nt2pr381rh2jmqbcvmsb0bm'),
(14, 'cart_6a0643e60e98e6.05351904', 'fa2nt2pr381rh2jmqbcvmsb0bm', NULL, 'mmns001', '', '', 2, '50.00', 'hide', '2026-05-14', '21:51:34', 'fa2nt2pr381rh2jmqbcvmsb0bm'),
(15, 'cart_6a064c0e1adc08.62444069', 'fa2nt2pr381rh2jmqbcvmsb0bm', NULL, 'mmns001', 'vsize_mmns001_large,vcol_mmns001_yellow', 'Large / Yellow', 1, '50.00', 'hide', '2026-05-14', '22:26:22', 'fa2nt2pr381rh2jmqbcvmsb0bm'),
(16, 'cart_6a064c19301be1.76475349', 'fa2nt2pr381rh2jmqbcvmsb0bm', NULL, 'mmgc001', '', '', 1, '30.00', 'hide', '2026-05-14', '22:26:33', 'fa2nt2pr381rh2jmqbcvmsb0bm'),
(17, 'cart_6a065105e434e7.30898312', 'fa2nt2pr381rh2jmqbcvmsb0bm', NULL, 'mmmc001', 'vsize_mmmc001_xlarge,vcol_mmmc001_green', 'XLarge / Green', 1, '32.00', 'hide', '2026-05-14', '22:47:33', 'fa2nt2pr381rh2jmqbcvmsb0bm'),
(18, 'cart_6a06511c5eefa9.54300880', 'fa2nt2pr381rh2jmqbcvmsb0bm', NULL, 'mmmc001', '', '', 1, '32.00', 'hide', '2026-05-14', '22:47:56', 'fa2nt2pr381rh2jmqbcvmsb0bm'),
(19, 'cart_6a06554d7f77c5.62431175', 'fa2nt2pr381rh2jmqbcvmsb0bm', NULL, 'mmac001', 'vsize_mmac001_xlarge,vcol_mmac001_yellow', 'XLarge / Yellow', 1, '30.00', 'hide', '2026-05-14', '23:05:49', 'fa2nt2pr381rh2jmqbcvmsb0bm'),
(20, 'cart_6a06555c6f4372.85986683', 'fa2nt2pr381rh2jmqbcvmsb0bm', NULL, 'mmac001', '', '', 1, '30.00', 'hide', '2026-05-14', '23:06:04', 'fa2nt2pr381rh2jmqbcvmsb0bm'),
(21, 'cart_6a0655687610b3.11125824', 'fa2nt2pr381rh2jmqbcvmsb0bm', NULL, 'mmac001', '', '', 2, '30.00', 'hide', '2026-05-14', '23:06:16', 'fa2nt2pr381rh2jmqbcvmsb0bm'),
(22, 'cart_6a07073019d4d1.86771426', 'fa2nt2pr381rh2jmqbcvmsb0bm', NULL, 'mmld001', 'vsize_mmld001_xlarge,vcol_mmld001_blue', 'XLarge / Blue', 1, '60.00', 'hide', '2026-05-15', '11:44:48', 'fa2nt2pr381rh2jmqbcvmsb0bm'),
(23, 'cart_6a0731b21b8900.17319932', 'mpkt36nqgki6vo0vb93nb44te4', NULL, 'mmmc001', '', '', 1, '32.00', 'hide', '2026-05-15', '14:46:10', 'mpkt36nqgki6vo0vb93nb44te4'),
(24, 'cart_6a0731b9dfdb14.08435227', 'mpkt36nqgki6vo0vb93nb44te4', NULL, 'mmmc001', '', '', 1, '32.00', 'hide', '2026-05-15', '14:46:17', 'mpkt36nqgki6vo0vb93nb44te4'),
(25, 'cart_6a073268482989.17151357', 'mpkt36nqgki6vo0vb93nb44te4', NULL, 'mmne001', '', '', 2, '40.00', 'hide', '2026-05-15', '14:49:12', 'mpkt36nqgki6vo0vb93nb44te4'),
(26, 'cart_6a073277e27f71.32365820', 'mpkt36nqgki6vo0vb93nb44te4', NULL, 'mmne001', '', '', 1, '40.00', 'hide', '2026-05-15', '14:49:27', 'mpkt36nqgki6vo0vb93nb44te4'),
(27, 'cart_6a073360b9d7a5.47165997', 'mpkt36nqgki6vo0vb93nb44te4', NULL, 'mmne001', '', '', 1, '40.00', 'hide', '2026-05-15', '14:53:20', 'mpkt36nqgki6vo0vb93nb44te4'),
(28, 'cart_6a0733e9e4ffd2.21724517', 'mpkt36nqgki6vo0vb93nb44te4', NULL, 'mmne001', '', '', 1, '40.00', 'hide', '2026-05-15', '14:55:37', 'mpkt36nqgki6vo0vb93nb44te4'),
(29, 'cart_6a07344c304699.35939690', 'mpkt36nqgki6vo0vb93nb44te4', NULL, 'mmne001', '', '', 1, '40.00', 'hide', '2026-05-15', '14:57:16', 'mpkt36nqgki6vo0vb93nb44te4'),
(30, 'cart_6a07345f3c1ec1.15088954', 'mpkt36nqgki6vo0vb93nb44te4', NULL, 'mmne001', '', '', 1, '40.00', 'hide', '2026-05-15', '14:57:35', 'mpkt36nqgki6vo0vb93nb44te4'),
(31, 'cart_6a073485829c32.83050552', 'mpkt36nqgki6vo0vb93nb44te4', NULL, 'mmne001', '', '', 1, '40.00', 'hide', '2026-05-15', '14:58:13', 'mpkt36nqgki6vo0vb93nb44te4'),
(32, 'cart_6a0735948e1721.97712229', 'mpkt36nqgki6vo0vb93nb44te4', NULL, 'mmne001', 'vsize_mmne001_large,vcol_mmne001_blue', 'Large / Blue', 2, '40.00', 'hide', '2026-05-15', '15:02:44', 'mpkt36nqgki6vo0vb93nb44te4'),
(33, 'cart_6a073e098b6c27.88418223', 'mpkt36nqgki6vo0vb93nb44te4', NULL, 'mmne001', 'vsize_mmne001_large,vcol_mmne001_blue', '30ml / Oily', 2, '40.00', 'hide', '2026-05-15', '15:38:49', 'mpkt36nqgki6vo0vb93nb44te4'),
(34, 'cart_6a073e6385c651.65186018', 'mpkt36nqgki6vo0vb93nb44te4', NULL, 'mmmc001', 'vsize_mmmc001_large,vcol_mmmc001_green', '30ml / Dry', 2, '32.00', 'hide', '2026-05-15', '15:40:19', 'mpkt36nqgki6vo0vb93nb44te4'),
(35, 'cart_6a0749edd0b3e7.75793651', 'mpkt36nqgki6vo0vb93nb44te4', NULL, 'mmns001', 'vsize_mmns001_large,vcol_mmns001_red', '30ml / Normal', 1, '50.00', 'hide', '2026-05-15', '16:29:33', 'mpkt36nqgki6vo0vb93nb44te4'),
(36, 'cart_6a0754b8ea86c1.03409159', 'mpkt36nqgki6vo0vb93nb44te4', NULL, 'mmmc001', 'vsize_mmmc001_large,vcol_mmmc001_blue', '30ml / Oily', 1, '32.00', 'hide', '2026-05-15', '17:15:36', 'mpkt36nqgki6vo0vb93nb44te4'),
(37, 'cart_6a0754db80d751.34792245', 'mpkt36nqgki6vo0vb93nb44te4', NULL, 'mmmc001', 'vsize_mmmc001_xlarge,vcol_mmmc001_red', '50ml / Normal', 1, '32.00', 'hide', '2026-05-15', '17:16:11', 'mpkt36nqgki6vo0vb93nb44te4'),
(38, 'cart_6a0755d29f4cf7.37851712', 'mpkt36nqgki6vo0vb93nb44te4', NULL, 'mmmc001', 'vsize_mmmc001_large,vcol_mmmc001_blue', '30ml / Oily', 1, '32.00', 'hide', '2026-05-15', '17:20:18', 'mpkt36nqgki6vo0vb93nb44te4'),
(39, 'cart_6a07596cd296b1.43792396', 'mpkt36nqgki6vo0vb93nb44te4', NULL, 'mmpp001', '', '', 1, '45.00', 'hide', '2026-05-15', '17:35:40', 'mpkt36nqgki6vo0vb93nb44te4'),
(40, 'cart_6a07597a6a4966.46721221', 'mpkt36nqgki6vo0vb93nb44te4', NULL, 'mmpp001', '', '', 1, '45.00', 'hide', '2026-05-15', '17:35:54', 'mpkt36nqgki6vo0vb93nb44te4'),
(41, 'cart_6a075c0a756956.79894132', 'mpkt36nqgki6vo0vb93nb44te4', NULL, 'mmns001', 'vsize_mmns001_large,vcol_mmns001_blue', '30ml / Oily', 1, '50.00', 'hide', '2026-05-15', '17:46:50', 'mpkt36nqgki6vo0vb93nb44te4'),
(42, 'cart_6a075c88664484.04057711', 'mpkt36nqgki6vo0vb93nb44te4', NULL, 'mmne001', 'vsize_mmne001_xlarge,vcol_mmne001_yellow', '50ml / Sensitive', 1, '40.00', 'hide', '2026-05-15', '17:48:56', 'mpkt36nqgki6vo0vb93nb44te4'),
(43, 'cart_6a0ad5e0829860.73534259', 'o83g825lthbm6kslfgehisvoon', NULL, 'mmac001', 'vsize_mmac001_large,vcol_mmac001_blue', '30ml / Oily', 2, '30.00', 'hide', '2026-05-18', '09:03:28', 'o83g825lthbm6kslfgehisvoon'),
(44, 'cart_6a0ad63d7e6e68.44620695', 'o83g825lthbm6kslfgehisvoon', NULL, 'mmac001', 'vsize_mmac001_large,vcol_mmac001_green', '30ml / Dry', 1, '30.00', 'hide', '2026-05-18', '09:05:01', 'o83g825lthbm6kslfgehisvoon'),
(45, 'cart_6a0ad84971d315.46822602', 'o83g825lthbm6kslfgehisvoon', NULL, 'mmns001', 'vsize_mmns001_large,vcol_mmns001_blue', '30ml / Oily', 1, '50.00', 'hide', '2026-05-18', '09:13:45', 'o83g825lthbm6kslfgehisvoon'),
(46, 'cart_6a0ae737a693f0.62631641', 'o83g825lthbm6kslfgehisvoon', NULL, 'mmne001', 'vsize_mmne001_large,vcol_mmne001_blue', '30ml / Oily', 1, '40.00', 'hide', '2026-05-18', '10:17:27', 'o83g825lthbm6kslfgehisvoon'),
(47, 'cart_6a0aee08d01cc3.23050419', 'o83g825lthbm6kslfgehisvoon', NULL, 'mmfc001', 'vsize_mmfc001_xlarge,vcol_mmfc001_yellow', '50ml / Sensitive', 3, '28.00', 'hide', '2026-05-18', '10:46:32', 'o83g825lthbm6kslfgehisvoon');

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
(1, 'fav001', '/assets/img/brand/venora-dark.svg', 'show', '2026-05-14', '13:02:01', 'system');

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
  `input_user_id` varchar(255) DEFAULT NULL COMMENT 'read_users.hash_id',
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
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `read_orders`
--

INSERT INTO `read_orders` (`id`, `hash_id`, `input_user_id`, `input_first_name`, `input_last_name`, `input_email`, `input_phone`, `text_address`, `input_status`, `input_total`, `input_subtotal`, `input_tax`, `input_shipping`, `input_payment_method`, `input_payment_ref`, `text_notes`, `visibility`, `date_created`, `time_created`, `created_by`) VALUES
(1, 'ORDE4894558', NULL, 'Kato', 'Howe', 'gewez@mailinator.com', '+1 (929) 447-9334', '490 West Hague Lane\nQui porro natus qui \nDebitis eos nisi cu, Iure earum ipsum er Quis qui dolores ius\nNG', 'paid', '150.00', '150.00', '0.00', '0.00', 'card', NULL, 'Elit dolore veritat', 'show', '2026-05-14', '21:27:36', 'fa2nt2pr381rh2jmqbcvmsb0bm'),
(2, 'ORD07D30802', NULL, 'Emmanuel', 'Okikiola', 'akadiriokiki@gmail.com', '07082783187', 'lagos state\n\nlagos State Nigeria, Lagos 0002\nNG', 'paid', '84.00', '84.00', '0.00', '0.00', 'card', NULL, '', 'show', '2026-05-18', '10:57:01', 'o83g825lthbm6kslfgehisvoon');

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
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `read_order_items`
--

INSERT INTO `read_order_items` (`id`, `hash_id`, `tb`, `tb_link`, `input_product_id`, `input_title`, `input_variant`, `input_quantity`, `input_price`, `input_total`, `image_1`, `visibility`, `date_created`, `time_created`, `created_by`) VALUES
(1, 'oi_6a063e48950c04.61777795', 'read_orders', 'ORDE4894558', 'mmac001', 'Anti-Aging Eye Cream', '', 5, '30.00', '150.00', '/assets/img/products/anti-aging-cream-1.webp', 'show', '2026-05-14', '21:27:36', 'fa2nt2pr381rh2jmqbcvmsb0bm'),
(2, 'oi_6a0af07d30aae5.48157682', 'read_orders', 'ORD07D30802', 'mmfc001', 'Gentle Foaming Cleanser', '50ml / Sensitive', 3, '28.00', '84.00', '/assets/img/products/foaming-cleanser-1.webp', 'show', '2026-05-18', '10:57:01', 'o83g825lthbm6kslfgehisvoon');

-- --------------------------------------------------------

--
-- Table structure for table `read_password_resets`
--

DROP TABLE IF EXISTS `read_password_resets`;
CREATE TABLE IF NOT EXISTS `read_password_resets` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(255) DEFAULT NULL,
  `input_email` varchar(255) DEFAULT NULL,
  `token` varchar(500) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL,
  `input_used` enum('1','0') DEFAULT '0',
  `visibility` varchar(50) DEFAULT 'show',
  `date_created` date DEFAULT NULL,
  `time_created` time DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_reset_email` (`input_email`),
  KEY `idx_reset_token` (`token`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `read_reviews`
--

DROP TABLE IF EXISTS `read_reviews`;
CREATE TABLE IF NOT EXISTS `read_reviews` (
  `id` int NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(255) NOT NULL,
  `tb` varchar(255) DEFAULT 'panel_product',
  `tb_link` varchar(255) DEFAULT NULL,
  `image_1` text,
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
-- Table structure for table `read_users`
--

DROP TABLE IF EXISTS `read_users`;
CREATE TABLE IF NOT EXISTS `read_users` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(255) NOT NULL,
  `input_firstname` varchar(255) DEFAULT NULL,
  `input_lastname` varchar(255) DEFAULT NULL,
  `input_email` varchar(255) DEFAULT NULL,
  `input_phone` varchar(100) DEFAULT NULL,
  `input_address` varchar(500) DEFAULT NULL,
  `input_city` varchar(255) DEFAULT NULL,
  `input_state` varchar(255) DEFAULT NULL,
  `input_country` varchar(255) DEFAULT NULL,
  `input_password` varchar(255) DEFAULT NULL,
  `input_avatar` text,
  `input_verify` enum('1','0') DEFAULT '0' COMMENT '1=verified, 0=unverified',
  `input_status` enum('1','0') DEFAULT '1' COMMENT '1=active, 0=suspended',
  `visibility` varchar(50) DEFAULT 'show',
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  `created_by` varchar(255) DEFAULT 'system',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_hash_id` (`hash_id`),
  UNIQUE KEY `uq_users_email` (`input_email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `read_users`
--

INSERT INTO `read_users` (`id`, `hash_id`, `input_firstname`, `input_lastname`, `input_email`, `input_phone`, `input_address`, `input_city`, `input_state`, `input_country`, `input_password`, `input_avatar`, `input_verify`, `input_status`, `visibility`, `date_created`, `time_created`, `created_by`) VALUES
(1, 'usr_6a0746730c3087.58462091', 'Lucian', 'Mercado', 'nyjucuhen@mailinator.com', '', NULL, NULL, NULL, NULL, '$2y$10$d7Uxe2T6Wii.im0PTGxuDeK9L3p03ViW4CDxRUFVmHx45WRuHakHy', NULL, '0', '1', 'show', '2026-05-15', '16:14:43', 'register'),
(2, 'usr_6a0748f70516e5.98029028', 'Sade', 'Dillon', 'xewumyxu@mailinator.com', '', NULL, NULL, NULL, NULL, '$2y$10$8ygF19cFxvQjEYfj/pXK7.fX.VwKkSMHWbsvCqbGh78wV.VJFYhwG', NULL, '0', '1', 'show', '2026-05-15', '16:25:27', 'register');

-- --------------------------------------------------------

--
-- Table structure for table `read_user_addresses`
--

DROP TABLE IF EXISTS `read_user_addresses`;
CREATE TABLE IF NOT EXISTS `read_user_addresses` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(255) NOT NULL,
  `tb` varchar(255) DEFAULT 'read_users',
  `tb_link` varchar(255) DEFAULT NULL COMMENT 'read_users.hash_id',
  `input_label` varchar(100) DEFAULT 'Home' COMMENT 'Home|Work|Other',
  `input_firstname` varchar(255) DEFAULT NULL,
  `input_lastname` varchar(255) DEFAULT NULL,
  `input_phone` varchar(100) DEFAULT NULL,
  `input_address` varchar(500) DEFAULT NULL,
  `input_city` varchar(255) DEFAULT NULL,
  `input_state` varchar(255) DEFAULT NULL,
  `input_country` varchar(255) DEFAULT NULL,
  `input_postcode` varchar(20) DEFAULT NULL,
  `input_is_default` enum('1','0') DEFAULT '0',
  `visibility` varchar(50) DEFAULT 'show',
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  `created_by` varchar(255) DEFAULT 'system',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_addr_hash` (`hash_id`),
  KEY `idx_addr_user` (`tb_link`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `selection_product_category`
--

DROP TABLE IF EXISTS `selection_product_category`;
CREATE TABLE IF NOT EXISTS `selection_product_category` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `input_title` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hash_id` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_1` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `visibility` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `time_created` time NOT NULL,
  `date_created` date NOT NULL,
  `created_by` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icon_icon` varchar(2222) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `selection_product_category`
--

INSERT INTO `selection_product_category` (`id`, `input_title`, `hash_id`, `image_1`, `visibility`, `time_created`, `date_created`, `created_by`, `icon_icon`) VALUES
(1, 'Serums',       'cat-serums',       NULL, 'show', '12:00:00', '2026-01-01', 'system', ''),
(2, 'Eye Care',     'cat-eye-care',     NULL, 'show', '12:00:00', '2026-01-01', 'system', ''),
(3, 'Cleansers',    'cat-cleansers',    NULL, 'show', '12:00:00', '2026-01-01', 'system', ''),
(4, 'Moisturizers', 'cat-moisturizers', NULL, 'show', '12:00:00', '2026-01-01', 'system', ''),
(5, 'Treatments',   'cat-treatments',   NULL, 'show', '12:00:00', '2026-01-01', 'system', '');

-- --------------------------------------------------------

--
-- Table structure for table `settings_checkout`
--

DROP TABLE IF EXISTS `settings_checkout`;
CREATE TABLE IF NOT EXISTS `settings_checkout` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(100) NOT NULL,
  `input_page_title` varchar(100) DEFAULT 'Checkout',
  `input_contact_title` varchar(100) DEFAULT 'Contact Information',
  `input_address_title` varchar(100) DEFAULT 'Shipping Address',
  `input_shipping_title` varchar(100) DEFAULT 'Shipping Method',
  `input_payment_title` varchar(100) DEFAULT 'Payment Information',
  `input_btn_text` varchar(100) DEFAULT 'Place Order',
  `input_summary_title` varchar(100) DEFAULT 'Order Summary',
  `visibility` char(4) NOT NULL DEFAULT 'show',
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `settings_checkout`
--

INSERT INTO `settings_checkout` (`id`, `hash_id`, `input_page_title`, `input_contact_title`, `input_address_title`, `input_shipping_title`, `input_payment_title`, `input_btn_text`, `input_summary_title`, `visibility`, `date_created`, `time_created`) VALUES
(1, 'chk001', 'Checkout', 'Contact Information', 'Shipping Address', 'Shipping Method', 'Payment Information', 'Place Order', 'Order Summary', 'show', '2026-05-18', '10:05:59'),
(2, 'chk001', 'Checkout', 'Contact Information', 'Shipping Address', 'Shipping Method', 'Payment Information', 'Place Order', 'Order Summary', 'show', '2026-05-18', '10:05:59');

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
-- Table structure for table `settings_shop_about_sections`
--

DROP TABLE IF EXISTS `settings_shop_about_sections`;
CREATE TABLE IF NOT EXISTS `settings_shop_about_sections` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(255) NOT NULL,
  `input_values_heading` varchar(255) DEFAULT 'Pure & Trusted',
  `text_values_subheading` varchar(500) DEFAULT 'Our products are crafted with your skin''s health in mind.',
  `input_faq_heading` varchar(255) DEFAULT 'Frequently Asked Questions',
  `input_gallery_heading` varchar(255) DEFAULT 'Follow us on Instagram',
  `visibility` varchar(50) DEFAULT 'show',
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  `created_by` varchar(255) DEFAULT 'system',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_about_sections` (`hash_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `settings_shop_about_sections`
--

INSERT INTO `settings_shop_about_sections` (`id`, `hash_id`, `input_values_heading`, `text_values_subheading`, `input_faq_heading`, `input_gallery_heading`, `visibility`, `date_created`, `time_created`, `created_by`) VALUES
(1, 'absc001', 'Pure & Trusted', 'Our products are crafted with your skin\'s health in mind.', 'Frequently Asked Questions', 'Follow us on Instagram', 'show', '2026-05-15', '17:16:56', 'system');

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
-- Table structure for table `settings_shop_cart_labels`
--

DROP TABLE IF EXISTS `settings_shop_cart_labels`;
CREATE TABLE IF NOT EXISTS `settings_shop_cart_labels` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(255) NOT NULL,
  `input_page_heading` varchar(255) DEFAULT 'Your Cart',
  `input_empty_heading` varchar(255) DEFAULT 'Your cart is empty',
  `text_empty_description` varchar(500) DEFAULT 'Add something you love to get started.',
  `input_header_product` varchar(100) DEFAULT 'Product',
  `input_header_qty` varchar(100) DEFAULT 'Qty',
  `input_header_total` varchar(100) DEFAULT 'Total',
  `input_remove_btn` varchar(100) DEFAULT 'Remove',
  `input_summary_title` varchar(255) DEFAULT 'Order Summary',
  `input_subtotal_label` varchar(100) DEFAULT 'Subtotal',
  `input_shipping_label` varchar(100) DEFAULT 'Shipping',
  `input_free_shipping_text` varchar(100) DEFAULT 'Free',
  `input_tax_label` varchar(100) DEFAULT 'Tax',
  `input_total_label` varchar(100) DEFAULT 'Total',
  `input_checkout_btn` varchar(100) DEFAULT 'Proceed to Checkout',
  `input_continue_shopping` varchar(100) DEFAULT 'Continue Shopping',
  `text_free_shipping_note` varchar(500) DEFAULT 'Add {amount} more for free shipping',
  `visibility` varchar(50) DEFAULT 'show',
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  `created_by` varchar(255) DEFAULT 'system',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cart_labels` (`hash_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `settings_shop_cart_labels`
--

INSERT INTO `settings_shop_cart_labels` (`id`, `hash_id`, `input_page_heading`, `input_empty_heading`, `text_empty_description`, `input_header_product`, `input_header_qty`, `input_header_total`, `input_remove_btn`, `input_summary_title`, `input_subtotal_label`, `input_shipping_label`, `input_free_shipping_text`, `input_tax_label`, `input_total_label`, `input_checkout_btn`, `input_continue_shopping`, `text_free_shipping_note`, `visibility`, `date_created`, `time_created`, `created_by`) VALUES
(1, 'crtlbl001', 'Your Cart', 'Your cart is empty', 'Add something you love to get started.', 'Product', 'Qty', 'Total', 'Remove', 'Order Summary', 'Subtotal', 'Shipping', 'Free', 'Tax', 'Total', 'Proceed to Checkout', 'Continue Shopping', 'Add {amount} more for free shipping', 'show', '2026-05-15', '17:16:56', 'system');

-- --------------------------------------------------------

--
-- Table structure for table `settings_shop_checkout_labels`
--

DROP TABLE IF EXISTS `settings_shop_checkout_labels`;
CREATE TABLE IF NOT EXISTS `settings_shop_checkout_labels` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(255) NOT NULL,
  `input_page_heading` varchar(255) DEFAULT 'Checkout',
  `input_contact_block_heading` varchar(255) DEFAULT 'Contact Information',
  `input_first_name_label` varchar(100) DEFAULT 'First Name',
  `input_last_name_label` varchar(100) DEFAULT 'Last Name',
  `input_email_label` varchar(100) DEFAULT 'Email Address',
  `input_phone_label` varchar(100) DEFAULT 'Phone Number',
  `input_address_block_heading` varchar(255) DEFAULT 'Shipping Address',
  `input_address1_label` varchar(100) DEFAULT 'Address Line 1',
  `input_address2_label` varchar(100) DEFAULT 'Address Line 2',
  `input_city_label` varchar(100) DEFAULT 'City',
  `input_postal_label` varchar(100) DEFAULT 'Postal / ZIP Code',
  `input_state_label` varchar(100) DEFAULT 'State / Province',
  `input_country_label` varchar(100) DEFAULT 'Country',
  `input_notes_label` varchar(100) DEFAULT 'Order Notes',
  `input_notes_placeholder` varchar(500) DEFAULT 'Any special instructions for your order?',
  `input_shipping_block_heading` varchar(255) DEFAULT 'Shipping Method',
  `input_standard_name` varchar(100) DEFAULT 'Standard Shipping',
  `input_standard_time` varchar(100) DEFAULT '5–7 business days',
  `input_express_name` varchar(100) DEFAULT 'Express Shipping',
  `input_express_time` varchar(100) DEFAULT '2–3 business days',
  `input_express_price` varchar(50) DEFAULT '12.99',
  `input_payment_block_heading` varchar(255) DEFAULT 'Payment Information',
  `text_payment_message` varchar(500) DEFAULT 'Secure payment powered by Stripe',
  `input_card_number_label` varchar(100) DEFAULT 'Card Number',
  `input_expiry_label` varchar(100) DEFAULT 'MM / YY',
  `input_cvc_label` varchar(100) DEFAULT 'CVC',
  `input_place_order_btn` varchar(100) DEFAULT 'Place Order',
  `text_payment_secure_msg` varchar(500) DEFAULT 'Your payment info is encrypted and secure.',
  `input_summary_title` varchar(255) DEFAULT 'Order Summary',
  `input_subtotal_label` varchar(100) DEFAULT 'Subtotal',
  `input_shipping_label` varchar(100) DEFAULT 'Shipping',
  `input_tax_label` varchar(100) DEFAULT 'Tax',
  `input_total_label` varchar(100) DEFAULT 'Total',
  `visibility` varchar(50) DEFAULT 'show',
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  `created_by` varchar(255) DEFAULT 'system',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_checkout_labels` (`hash_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `settings_shop_checkout_labels`
--

INSERT INTO `settings_shop_checkout_labels` (`id`, `hash_id`, `input_page_heading`, `input_contact_block_heading`, `input_first_name_label`, `input_last_name_label`, `input_email_label`, `input_phone_label`, `input_address_block_heading`, `input_address1_label`, `input_address2_label`, `input_city_label`, `input_postal_label`, `input_state_label`, `input_country_label`, `input_notes_label`, `input_notes_placeholder`, `input_shipping_block_heading`, `input_standard_name`, `input_standard_time`, `input_express_name`, `input_express_time`, `input_express_price`, `input_payment_block_heading`, `text_payment_message`, `input_card_number_label`, `input_expiry_label`, `input_cvc_label`, `input_place_order_btn`, `text_payment_secure_msg`, `input_summary_title`, `input_subtotal_label`, `input_shipping_label`, `input_tax_label`, `input_total_label`, `visibility`, `date_created`, `time_created`, `created_by`) VALUES
(1, 'ckllbl001', 'Checkout', 'Contact Information', 'First Name', 'Last Name', 'Email Address', 'Phone Number', 'Shipping Address', 'Address Line 1', 'Address Line 2', 'City', 'Postal / ZIP Code', 'State / Province', 'Country', 'Order Notes', 'Any special instructions for your order?', 'Shipping Method', 'Standard Shipping', '5–7 business days', 'Express Shipping', '2–3 business days', '12.99', 'Payment Information', 'Secure payment powered by Stripe', 'Card Number', 'MM / YY', 'CVC', 'Place Order', 'Your payment info is encrypted and secure.', 'Order Summary', 'Subtotal', 'Shipping', 'Tax', 'Total', 'show', '2026-05-15', '17:16:56', 'system');

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
  `input_orders_email` varchar(255) DEFAULT 'orders@venora.com',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `settings_shop_config`
--

INSERT INTO `settings_shop_config` (`id`, `hash_id`, `input_name`, `input_tagline`, `input_email`, `input_email_from`, `input_email_smtp_host`, `input_email_smtp_secure_type`, `input_email_smtp_port`, `input_email_password`, `input_phone`, `input_address`, `input_currency`, `input_currency_symbol`, `input_tax_rate`, `input_shipping_rate`, `input_free_shipping`, `input_seo_keywords`, `text_description`, `image_1`, `image_2`, `visibility`, `date_created`, `time_created`, `created_by`, `input_orders_email`) VALUES
(1, 'cfg001', 'Venora', 'Luxury Skincare', 'hello@venora.com', '', 'smtp.gmail.com', 'tls', '587', '', '', '', 'USD', '$', '0', '5.99', '75', 'luxury skincare, natural beauty, moisturizer, serum, cleanser, eye cream', 'VENORA was born from a passion for luxurious skincare that empowers women to feel confident in their own skin. Inspired by elegance, nature, and science, every product is carefully crafted to enhance your natural beauty.', '/assets/img/brand/venora-white.svg', '/assets/img/brand/venora-dark.svg', 'show', '2026-05-14', '12:51:53', 'system', 'orders@venora.com');

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
-- Table structure for table `settings_shop_contact_labels`
--

DROP TABLE IF EXISTS `settings_shop_contact_labels`;
CREATE TABLE IF NOT EXISTS `settings_shop_contact_labels` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(255) NOT NULL,
  `input_email_label` varchar(100) DEFAULT 'Email',
  `input_phone_label` varchar(100) DEFAULT 'Phone',
  `input_orders_label` varchar(100) DEFAULT 'Orders & Shipping',
  `input_orders_email` varchar(255) DEFAULT 'orders@venora.com',
  `input_fn_label` varchar(100) DEFAULT 'First name',
  `input_fn_placeholder` varchar(255) DEFAULT 'Jane',
  `input_ln_label` varchar(100) DEFAULT 'Last name',
  `input_ln_placeholder` varchar(255) DEFAULT 'Doe',
  `input_email_form_label` varchar(100) DEFAULT 'Email address',
  `input_email_form_placeholder` varchar(255) DEFAULT 'jane@example.com',
  `input_subject_label` varchar(100) DEFAULT 'Subject',
  `input_subject_placeholder` varchar(255) DEFAULT 'How can we help?',
  `input_message_label` varchar(100) DEFAULT 'Message',
  `input_message_placeholder` varchar(500) DEFAULT 'Tell us more…',
  `input_submit_btn` varchar(100) DEFAULT 'Send Message',
  `text_success_message` varchar(500) DEFAULT 'Thank you! We''ll be in touch shortly.',
  `text_error_message` varchar(500) DEFAULT 'Something went wrong. Please try again.',
  `visibility` varchar(50) DEFAULT 'show',
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  `created_by` varchar(255) DEFAULT 'system',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_contact_labels` (`hash_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `settings_shop_contact_labels`
--

INSERT INTO `settings_shop_contact_labels` (`id`, `hash_id`, `input_email_label`, `input_phone_label`, `input_orders_label`, `input_orders_email`, `input_fn_label`, `input_fn_placeholder`, `input_ln_label`, `input_ln_placeholder`, `input_email_form_label`, `input_email_form_placeholder`, `input_subject_label`, `input_subject_placeholder`, `input_message_label`, `input_message_placeholder`, `input_submit_btn`, `text_success_message`, `text_error_message`, `visibility`, `date_created`, `time_created`, `created_by`) VALUES
(1, 'cnllbl001', 'Email', 'Phone', 'Orders & Shipping', 'orders@venora.com', 'First name', 'Jane', 'Last name', 'Doe', 'Email address', 'jane@example.com', 'Subject', 'How can we help?', 'Message', 'Tell us more…', 'Send Message', 'Thank you! We\'ll be in touch shortly.', 'Something went wrong. Please try again.', 'show', '2026-05-15', '17:16:56', 'system');

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
  `input_newsletter_popup_heading` varchar(255) DEFAULT 'Get 10% off your first order',
  `text_newsletter_popup_description` text,
  `input_newsletter_popup_btn` varchar(100) DEFAULT 'Subscribe',
  `input_newsletter_popup_dismiss` varchar(100) DEFAULT 'No thanks',
  `input_newsletter_placeholder` varchar(255) DEFAULT 'Email address...',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `settings_shop_footer`
--

INSERT INTO `settings_shop_footer` (`id`, `hash_id`, `input_cta_heading`, `input_cta_btn`, `input_newsletter_heading`, `input_powered_by`, `input_instagram`, `input_facebook`, `input_linkedin`, `visibility`, `date_created`, `time_created`, `created_by`, `input_newsletter_popup_heading`, `text_newsletter_popup_description`, `input_newsletter_popup_btn`, `input_newsletter_popup_dismiss`, `input_newsletter_placeholder`) VALUES
(1, 'ftr001', 'Ready for Your Best Skin Yet?', 'Book a Consultation', 'Stay updated with the latest from Venora!', '', 'https://www.instagram.com/', 'https://www.facebook.com/', 'https://www.linkedin.com/', 'show', '2026-05-14', '12:51:54', 'system', 'Get 10% off your first order', 'Subscribe for exclusive offers, skincare tips, and early access to new products.', 'Subscribe', 'No thanks', 'Email address...');

-- --------------------------------------------------------

--
-- Table structure for table `settings_home_hero`
--

DROP TABLE IF EXISTS `settings_home_hero`;
CREATE TABLE IF NOT EXISTS `settings_home_hero` (
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
  `input_partners_heading` varchar(255) DEFAULT 'Trusted by leading brands',
  `input_scroll_text` varchar(100) DEFAULT 'Scroll Down',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `settings_home_hero`
--

INSERT INTO `settings_home_hero` (`id`, `hash_id`, `input_heading`, `input_btn1_label`, `input_btn2_label`, `input_trust_text`, `input_rating`, `input_video_url`, `image_1`, `visibility`, `date_created`, `time_created`, `created_by`, `input_partners_heading`, `input_scroll_text`) VALUES
(1, 'hero001', 'Your natural beauty, expressed with care', 'Shop now', 'Our collection', 'Trusted by 300+ clients', '4.9/5', 'https://videos.pexels.com/video-files/7304311/7304311-hd_1920_1080_30fps.mp4', '/assets/img/products/radiance-serum-1.webp', 'show', '2026-05-14', '12:51:53', 'system', 'Trusted by leading brands', 'Scroll Down');

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
-- Table structure for table `settings_shop_products_page`
--

DROP TABLE IF EXISTS `settings_shop_products_page`;
CREATE TABLE IF NOT EXISTS `settings_shop_products_page` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(255) NOT NULL,
  `input_heading` varchar(255) DEFAULT 'Explore products',
  `text_description` varchar(500) DEFAULT 'Carefully crafted formulas for every skin type. Clean ingredients. Real results.',
  `input_featured_heading` varchar(255) DEFAULT 'We believe skincare is a ritual, not a routine',
  `text_featured_desc` varchar(500) DEFAULT 'Discover our curated selection of products designed to highlight your unique beauty.',
  `input_no_products_msg` varchar(500) DEFAULT 'No products found in this category.',
  `visibility` varchar(50) DEFAULT 'show',
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  `created_by` varchar(255) DEFAULT 'system',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_products_page` (`hash_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `settings_shop_products_page`
--

INSERT INTO `settings_shop_products_page` (`id`, `hash_id`, `input_heading`, `text_description`, `input_featured_heading`, `text_featured_desc`, `input_no_products_msg`, `visibility`, `date_created`, `time_created`, `created_by`) VALUES
(1, 'pdgpg001', 'Explore products', 'Carefully crafted formulas for every skin type. Clean ingredients. Real results.', 'We believe skincare is a ritual, not a routine', 'Discover our curated selection of products designed to highlight your unique beauty.', 'No products found in this category.', 'show', '2026-05-15', '17:16:56', 'system');

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
-- Table structure for table `settings_shop_ui_labels`
--

DROP TABLE IF EXISTS `settings_shop_ui_labels`;
CREATE TABLE IF NOT EXISTS `settings_shop_ui_labels` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(255) NOT NULL,
  `input_add_to_cart` varchar(100) DEFAULT 'Add to Cart',
  `input_adding_to_cart` varchar(100) DEFAULT 'Adding...',
  `input_added_to_cart` varchar(100) DEFAULT 'Added!',
  `input_out_of_stock` varchar(100) DEFAULT 'Out of stock',
  `input_only_x_left` varchar(100) DEFAULT 'Only {n} left in stock — order soon',
  `input_in_stock` varchar(100) DEFAULT 'In stock',
  `input_x_in_stock` varchar(100) DEFAULT '{n} in stock',
  `input_shop_now` varchar(100) DEFAULT 'Shop Now',
  `input_load_more` varchar(100) DEFAULT 'Load More',
  `input_all_loaded` varchar(100) DEFAULT 'All products shown',
  `input_quantity_label` varchar(100) DEFAULT 'Quantity',
  `input_no_products_msg` varchar(500) DEFAULT 'No products in this category yet.',
  `input_search_placeholder` varchar(255) DEFAULT 'Search products…',
  `input_sort_label` varchar(100) DEFAULT 'Sort by',
  `input_price_label` varchar(100) DEFAULT 'Price',
  `input_apply_label` varchar(100) DEFAULT 'Apply',
  `input_clear_filters` varchar(100) DEFAULT 'Clear filters',
  `input_details_tab` varchar(100) DEFAULT 'Details',
  `input_ingredients_tab` varchar(100) DEFAULT 'Ingredients',
  `input_reviews_tab` varchar(100) DEFAULT 'Reviews',
  `input_no_ingredients_msg` varchar(500) DEFAULT 'Ingredient list not available for this product.',
  `input_no_reviews_msg` varchar(500) DEFAULT 'No reviews yet — be the first to share your experience.',
  `input_write_review_btn` varchar(100) DEFAULT 'Write a Review',
  `input_review_form_heading` varchar(255) DEFAULT 'Share your experience',
  `input_review_name_placeholder` varchar(255) DEFAULT 'Your name',
  `input_review_title_placeholder` varchar(255) DEFAULT 'Review title',
  `input_review_body_placeholder` varchar(500) DEFAULT 'Tell others what you think about this product…',
  `input_review_submit_btn` varchar(100) DEFAULT 'Submit Review',
  `input_review_success_msg` varchar(500) DEFAULT 'Thank you for your review! It will appear shortly.',
  `input_you_might_like` varchar(255) DEFAULT 'You might also like',
  `visibility` varchar(50) DEFAULT 'show',
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  `created_by` varchar(255) DEFAULT 'system',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_ui_labels` (`hash_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `settings_shop_ui_labels`
--

INSERT INTO `settings_shop_ui_labels` (`id`, `hash_id`, `input_add_to_cart`, `input_adding_to_cart`, `input_added_to_cart`, `input_out_of_stock`, `input_only_x_left`, `input_in_stock`, `input_x_in_stock`, `input_shop_now`, `input_load_more`, `input_all_loaded`, `input_quantity_label`, `input_no_products_msg`, `input_search_placeholder`, `input_sort_label`, `input_price_label`, `input_apply_label`, `input_clear_filters`, `input_details_tab`, `input_ingredients_tab`, `input_reviews_tab`, `input_no_ingredients_msg`, `input_no_reviews_msg`, `input_write_review_btn`, `input_review_form_heading`, `input_review_name_placeholder`, `input_review_title_placeholder`, `input_review_body_placeholder`, `input_review_submit_btn`, `input_review_success_msg`, `input_you_might_like`, `visibility`, `date_created`, `time_created`, `created_by`) VALUES
(1, 'uilbl001', 'Add to Cart', 'Adding...', 'Added!', 'Out of stock', 'Only {n} left in stock — order soon', 'In stock', '{n} in stock', 'Shop Now', 'Load More', 'All products shown', 'Quantity', 'No products in this category yet.', 'Search products…', 'Sort by', 'Price', 'Apply', 'Clear filters', 'Details', 'Ingredients', 'Reviews', 'Ingredient list not available for this product.', 'No reviews yet — be the first to share your experience.', 'Write a Review', 'Share your experience', 'Your name', 'Review title', 'Tell others what you think about this product…', 'Submit Review', 'Thank you for your review! It will appear shortly.', 'You might also like', 'show', '2026-05-15', '17:16:56', 'system');

-- --------------------------------------------------------

--
-- Table structure for table `settings_website_info`
--

DROP TABLE IF EXISTS `settings_website_info`;
CREATE TABLE IF NOT EXISTS `settings_website_info` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(225) DEFAULT NULL,
  `input_name` varchar(225) NOT NULL,
  `input_email` varchar(225) DEFAULT NULL,
  `input_phone_number` varchar(225) DEFAULT NULL,
  `input_whatsapp_number` char(50) DEFAULT NULL,
  `input_bank_account_name` char(150) DEFAULT NULL,
  `input_bank_name` char(150) DEFAULT NULL,
  `input_bank_account_number` char(50) DEFAULT NULL,
  `input_address` varchar(225) NOT NULL,
  `input_linkedin` varchar(225) NOT NULL,
  `input_facebook` varchar(225) NOT NULL,
  `input_instagram` varchar(225) NOT NULL,
  `input_behance` varchar(225) DEFAULT NULL,
  `input_dribbble` varchar(225) DEFAULT NULL,
  `input_twitter` varchar(225) NOT NULL,
  `image_1` text,
  `text_description` text NOT NULL,
  `input_day_open_closed` varchar(225) DEFAULT NULL,
  `input_time_open_closed` varchar(225) DEFAULT NULL,
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
  `created_by` varchar(225) NOT NULL,
  `input_logo_width` varchar(225) DEFAULT NULL,
  `input_usd_toggle` int DEFAULT NULL,
  `input_size_chart_toggle` int NOT NULL DEFAULT 0,
  `input_paystack_toggle` char(1) DEFAULT NULL,
  `input_whatsapp_toggle` char(1) DEFAULT NULL,
  `input-seo_keyword` text,
  `text_bank_details` text,
  `input_bank_label` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `settings_website_info`
--

INSERT INTO `settings_website_info` (`id`, `hash_id`, `input_name`, `input_email`, `input_phone_number`, `input_whatsapp_number`, `input_bank_account_name`, `input_bank_name`, `input_bank_account_number`, `input_address`, `input_linkedin`, `input_facebook`, `input_instagram`, `input_behance`, `input_dribbble`, `input_twitter`, `image_1`, `text_description`, `input_day_open_closed`, `input_time_open_closed`, `input_country`, `input_seo_keywords`, `visibility`, `date_created`, `time_created`, `input_email_smtp_port`, `input_email_password`, `input_email_smtp_host`, `input_email_smtp_secure_type`, `input_email_from`, `created_by`, `input_logo_width`, `input_usd_toggle`, `input_size_chart_toggle`, `input_paystack_toggle`, `input_whatsapp_toggle`, `input-seo_keyword`, `text_bank_details`, `input_bank_label`) VALUES
(1, '345yjhgfse3456yhbgvfc', 'Venora', 'hello@venora.com', '', '', '', '', '', 'Nigeria', 'http://linkedin.com', 'http://facebook.com', 'http://instagram.com', 'https://behance.net', 'https://dribbble.com', 'http://twitter.com', '/assets/img/brand/venora-dark.svg', 'VENORA was born from a passion for luxurious skincare that empowers women to feel confident in their own skin. Inspired by elegance, nature, and science, every product is carefully crafted to enhance your natural beauty.', 'Monday-Friday', '9AM-6PM', 'Nigeria', NULL, 'show', '2026-01-01', '12:00:00', '587', '', 'smtp.gmail.com', 'tls', 'hello@venora.com', 'system', '120', 1, 0, NULL, '1', NULL, NULL, NULL);


-- --------------------------------------------------------

--
-- Table structure for table `verify`
--

DROP TABLE IF EXISTS `verify`;
CREATE TABLE IF NOT EXISTS `verify` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(255) DEFAULT NULL,
  `input_email` varchar(500) DEFAULT NULL,
  `token` varchar(500) DEFAULT NULL,
  `token_s` varchar(500) DEFAULT NULL,
  `verify_token` varchar(8) DEFAULT NULL,
  `token_type` varchar(50) DEFAULT 'email_verify' COMMENT 'email_verify | password_reset',
  `token_expiry` datetime DEFAULT NULL,
  `visibility` varchar(50) DEFAULT 'show',
  `date_created` date DEFAULT NULL,
  `time_created` time DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_verify_email` (`input_email`(191)),
  KEY `idx_verify_hash` (`hash_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `verify`
--

INSERT INTO `verify` (`id`, `hash_id`, `input_email`, `token`, `token_s`, `verify_token`, `token_type`, `token_expiry`, `visibility`, `date_created`, `time_created`) VALUES
(1, 'vrf_6a0746730cfed5.38130832', 'nyjucuhen@mailinator.com', NULL, NULL, 'F5F8B1', 'email_verify', '2026-05-16 16:14:43', 'show', '2026-05-15', '16:14:43'),
(2, 'vrf_6a0748f7065fb5.94117538', 'xewumyxu@mailinator.com', NULL, NULL, '20614D', 'email_verify', '2026-05-16 16:25:27', 'show', '2026-05-15', '16:25:27');

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

-- ============================================================
-- NEW TABLES PORTED FROM DEMO16 BACKEND STRUCTURE
-- ============================================================

-- --------------------------------------------------------
-- Table: product_options (exact demo16.sql structure)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `product_options` (
  `id` int NOT NULL AUTO_INCREMENT,
  `option_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `product_options` (`id`, `option_name`) VALUES
(1, 'Size'),
(2, 'Skin Type');

-- --------------------------------------------------------
-- Table: product_option_values (exact demo16.sql structure)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `product_option_values` (
  `id` int NOT NULL AUTO_INCREMENT,
  `option_id` int NOT NULL,
  `value_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `option_id` (`option_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `product_option_values` (`id`, `option_id`, `value_name`) VALUES
(1, 1, '30ml'),
(2, 1, '50ml'),
(3, 2, 'Normal'),
(4, 2, 'Oily'),
(5, 2, 'Dry'),
(6, 2, 'Sensitive');

-- --------------------------------------------------------
-- Table: variants (exact demo16.sql structure)
-- One row per product (or per option combination)
-- product_hash_id references panel_product.hash_id
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `variants` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_hash_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `input_price_ngn` decimal(10,2) DEFAULT NULL,
  `input_price_usd` decimal(10,2) DEFAULT NULL,
  `input_inventory` int DEFAULT NULL,
  `sku` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `input_weight_in_kg` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_hash_id` (`product_hash_id`)
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `variants` (`id`, `product_hash_id`, `input_price_ngn`, `input_price_usd`, `input_inventory`, `sku`, `image_1`, `input_weight_in_kg`) VALUES
(1, 'vnr-srs-001', 75000.00, 50.00, 999, 'VNR-SRM-001-SZ-LA', NULL, NULL),
(2, 'vnr-srs-001', 75000.00, 50.00, 999, 'VNR-SRM-001-SZ-XL', NULL, NULL),
(3, 'vnr-srs-001', 75000.00, 50.00, 999, 'VNR-SRM-001-CL-RE', NULL, NULL),
(4, 'vnr-srs-001', 75000.00, 50.00, 999, 'VNR-SRM-001-CL-BL', NULL, NULL),
(5, 'vnr-srs-001', 75000.00, 50.00, 999, 'VNR-SRM-001-CL-GR', NULL, NULL),
(6, 'vnr-srs-001', 75000.00, 50.00, 999, 'VNR-SRM-001-CL-YE', NULL, NULL),
(7, 'vnr-eye-001', 45000.00, 30.00, 999, 'VNR-EYE-001-SZ-LA', NULL, NULL),
(8, 'vnr-eye-001', 45000.00, 30.00, 999, 'VNR-EYE-001-SZ-XL', NULL, NULL),
(9, 'vnr-eye-001', 45000.00, 30.00, 999, 'VNR-EYE-001-CL-RE', NULL, NULL),
(10, 'vnr-eye-001', 45000.00, 30.00, 999, 'VNR-EYE-001-CL-BL', NULL, NULL),
(11, 'vnr-eye-001', 45000.00, 30.00, 999, 'VNR-EYE-001-CL-GR', NULL, NULL),
(12, 'vnr-eye-001', 45000.00, 30.00, 999, 'VNR-EYE-001-CL-YE', NULL, NULL),
(13, 'vnr-cln-001', 45000.00, 30.00, 999, 'VNR-CLN-001-SZ-LA', NULL, NULL),
(14, 'vnr-cln-001', 45000.00, 30.00, 999, 'VNR-CLN-001-SZ-XL', NULL, NULL),
(15, 'vnr-cln-001', 45000.00, 30.00, 999, 'VNR-CLN-001-CL-RE', NULL, NULL),
(16, 'vnr-cln-001', 45000.00, 30.00, 999, 'VNR-CLN-001-CL-BL', NULL, NULL),
(17, 'vnr-cln-001', 45000.00, 30.00, 999, 'VNR-CLN-001-CL-GR', NULL, NULL),
(18, 'vnr-cln-001', 45000.00, 30.00, 999, 'VNR-CLN-001-CL-YE', NULL, NULL),
(19, 'vnr-mos-001', 105000.00, 70.00, 999, 'VNR-MOS-001-SZ-LA', NULL, NULL),
(20, 'vnr-mos-001', 105000.00, 70.00, 999, 'VNR-MOS-001-SZ-XL', NULL, NULL),
(21, 'vnr-mos-001', 105000.00, 70.00, 999, 'VNR-MOS-001-CL-RE', NULL, NULL),
(22, 'vnr-mos-001', 105000.00, 70.00, 999, 'VNR-MOS-001-CL-BL', NULL, NULL),
(23, 'vnr-mos-001', 105000.00, 70.00, 999, 'VNR-MOS-001-CL-GR', NULL, NULL),
(24, 'vnr-mos-001', 105000.00, 70.00, 999, 'VNR-MOS-001-CL-YE', NULL, NULL),
(25, 'vnr-nit-001', 97500.00, 65.00, 999, 'VNR-NIT-001-SZ-LA', NULL, NULL),
(26, 'vnr-nit-001', 97500.00, 65.00, 999, 'VNR-NIT-001-SZ-XL', NULL, NULL),
(27, 'vnr-nit-001', 97500.00, 65.00, 999, 'VNR-NIT-001-CL-RE', NULL, NULL),
(28, 'vnr-nit-001', 97500.00, 65.00, 999, 'VNR-NIT-001-CL-BL', NULL, NULL),
(29, 'vnr-nit-001', 97500.00, 65.00, 999, 'VNR-NIT-001-CL-GR', NULL, NULL),
(30, 'vnr-nit-001', 97500.00, 65.00, 999, 'VNR-NIT-001-CL-YE', NULL, NULL),
(31, 'vnr-day-001', 90000.00, 60.00, 999, 'VNR-DAY-001-SZ-LA', NULL, NULL),
(32, 'vnr-day-001', 90000.00, 60.00, 999, 'VNR-DAY-001-SZ-XL', NULL, NULL),
(33, 'vnr-day-001', 90000.00, 60.00, 999, 'VNR-DAY-001-CL-RE', NULL, NULL),
(34, 'vnr-day-001', 90000.00, 60.00, 999, 'VNR-DAY-001-CL-BL', NULL, NULL),
(35, 'vnr-day-001', 90000.00, 60.00, 999, 'VNR-DAY-001-CL-GR', NULL, NULL),
(36, 'vnr-day-001', 90000.00, 60.00, 999, 'VNR-DAY-001-CL-YE', NULL, NULL),
(37, 'vnr-eye-002', 82500.00, 55.00, 999, 'VNR-EYS-001-SZ-LA', NULL, NULL),
(38, 'vnr-eye-002', 82500.00, 55.00, 999, 'VNR-EYS-001-SZ-XL', NULL, NULL),
(39, 'vnr-eye-002', 82500.00, 55.00, 999, 'VNR-EYS-001-CL-RE', NULL, NULL),
(40, 'vnr-eye-002', 82500.00, 55.00, 999, 'VNR-EYS-001-CL-BL', NULL, NULL),
(41, 'vnr-eye-002', 82500.00, 55.00, 999, 'VNR-EYS-001-CL-GR', NULL, NULL),
(42, 'vnr-eye-002', 82500.00, 55.00, 999, 'VNR-EYS-001-CL-YE', NULL, NULL),
(43, 'vnr-fcl-001', 42000.00, 28.00, 999, 'VNR-FCL-001-SZ-LA', NULL, NULL),
(44, 'vnr-fcl-001', 42000.00, 28.00, 999, 'VNR-FCL-001-SZ-XL', NULL, NULL),
(45, 'vnr-fcl-001', 42000.00, 28.00, 999, 'VNR-FCL-001-CL-RE', NULL, NULL),
(46, 'vnr-fcl-001', 42000.00, 28.00, 999, 'VNR-FCL-001-CL-BL', NULL, NULL),
(47, 'vnr-fcl-001', 42000.00, 28.00, 999, 'VNR-FCL-001-CL-GR', NULL, NULL),
(48, 'vnr-fcl-001', 42000.00, 28.00, 999, 'VNR-FCL-001-CL-YE', NULL, NULL),
(49, 'vnr-dhy-001', 112500.00, 75.00, 999, 'VNR-DHY-001-SZ-LA', NULL, NULL),
(50, 'vnr-dhy-001', 112500.00, 75.00, 999, 'VNR-DHY-001-SZ-XL', NULL, NULL),
(51, 'vnr-dhy-001', 112500.00, 75.00, 999, 'VNR-DHY-001-CL-RE', NULL, NULL),
(52, 'vnr-dhy-001', 112500.00, 75.00, 999, 'VNR-DHY-001-CL-BL', NULL, NULL),
(53, 'vnr-dhy-001', 112500.00, 75.00, 999, 'VNR-DHY-001-CL-GR', NULL, NULL),
(54, 'vnr-dhy-001', 112500.00, 75.00, 999, 'VNR-DHY-001-CL-YE', NULL, NULL),
(55, 'vnr-por-001', 67500.00, 45.00, 999, 'VNR-POR-001-SZ-LA', NULL, NULL),
(56, 'vnr-por-001', 67500.00, 45.00, 999, 'VNR-POR-001-SZ-XL', NULL, NULL),
(57, 'vnr-por-001', 67500.00, 45.00, 999, 'VNR-POR-001-CL-RE', NULL, NULL),
(58, 'vnr-por-001', 67500.00, 45.00, 999, 'VNR-POR-001-CL-BL', NULL, NULL),
(59, 'vnr-por-001', 67500.00, 45.00, 999, 'VNR-POR-001-CL-GR', NULL, NULL),
(60, 'vnr-por-001', 67500.00, 45.00, 999, 'VNR-POR-001-CL-YE', NULL, NULL),
(61, 'vnr-mcl-001', 48000.00, 32.00, 999, 'VNR-MCL-001-SZ-LA', NULL, NULL),
(62, 'vnr-mcl-001', 48000.00, 32.00, 999, 'VNR-MCL-001-SZ-XL', NULL, NULL),
(63, 'vnr-mcl-001', 48000.00, 32.00, 999, 'VNR-MCL-001-CL-RE', NULL, NULL),
(64, 'vnr-mcl-001', 48000.00, 32.00, 999, 'VNR-MCL-001-CL-BL', NULL, NULL),
(65, 'vnr-mcl-001', 48000.00, 32.00, 999, 'VNR-MCL-001-CL-GR', NULL, NULL),
(66, 'vnr-mcl-001', 48000.00, 32.00, 999, 'VNR-MCL-001-CL-YE', NULL, NULL),
(67, 'vnr-ney-001', 60000.00, 40.00, 999, 'VNR-NEY-001-SZ-LA', NULL, NULL),
(68, 'vnr-ney-001', 60000.00, 40.00, 999, 'VNR-NEY-001-SZ-XL', NULL, NULL),
(69, 'vnr-ney-001', 60000.00, 40.00, 999, 'VNR-NEY-001-CL-RE', NULL, NULL),
(70, 'vnr-ney-001', 60000.00, 40.00, 999, 'VNR-NEY-001-CL-BL', NULL, NULL),
(71, 'vnr-ney-001', 60000.00, 40.00, 999, 'VNR-NEY-001-CL-GR', NULL, NULL),
(72, 'vnr-ney-001', 60000.00, 40.00, 999, 'VNR-NEY-001-CL-YE', NULL, NULL);

-- --------------------------------------------------------
-- Table: variant_values_link (exact demo16.sql structure)
-- Junction linking variants to product_option_values
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `variant_values_link` (
  `variant_id` int NOT NULL,
  `value_id` int NOT NULL,
  PRIMARY KEY (`variant_id`,`value_id`),
  KEY `value_id` (`value_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `variant_values_link` (`variant_id`, `value_id`) VALUES
(1, 1),(7, 1),(13, 1),(19, 1),(25, 1),(31, 1),(37, 1),(43, 1),(49, 1),(55, 1),(61, 1),(67, 1),
(2, 2),(8, 2),(14, 2),(20, 2),(26, 2),(32, 2),(38, 2),(44, 2),(50, 2),(56, 2),(62, 2),(68, 2),
(3, 3),(9, 3),(15, 3),(21, 3),(27, 3),(33, 3),(39, 3),(45, 3),(51, 3),(57, 3),(63, 3),(69, 3),
(4, 4),(10, 4),(16, 4),(22, 4),(28, 4),(34, 4),(40, 4),(46, 4),(52, 4),(58, 4),(64, 4),(70, 4),
(5, 5),(11, 5),(17, 5),(23, 5),(29, 5),(35, 5),(41, 5),(47, 5),(53, 5),(59, 5),(65, 5),(71, 5),
(6, 6),(12, 6),(18, 6),(24, 6),(30, 6),(36, 6),(42, 6),(48, 6),(54, 6),(60, 6),(66, 6),(72, 6);


-- --------------------------------------------------------
-- Table: cart
-- Shopping cart (user-linked, variant-aware)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cart` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(225) DEFAULT NULL,
  `user_id` char(150) DEFAULT NULL,
  `product_id` varchar(225) DEFAULT NULL COMMENT 'References panel_product.hash_id',
  `variant_id` char(50) DEFAULT NULL COMMENT 'References variants.id',
  `quantity` char(11) DEFAULT NULL,
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
-- Table: invoice
-- Orders / invoices from checkout
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `invoice` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(225) DEFAULT NULL,
  `user_id` varchar(225) DEFAULT NULL,
  `invoice_id` varchar(225) DEFAULT NULL,
  `shipping_id` varchar(225) DEFAULT NULL,
  `shipping_amount` varchar(20) DEFAULT NULL,
  `shipping_amount2` varchar(20) DEFAULT NULL COMMENT 'USD shipping',
  `shipping_data` text,
  `shipping_status` char(2) DEFAULT NULL,
  `rate_id` char(100) DEFAULT NULL,
  `payment_plan` varchar(225) DEFAULT NULL,
  `plan_id` varchar(225) DEFAULT NULL,
  `amount_due` varchar(20) DEFAULT NULL,
  `amount_due2` varchar(20) DEFAULT NULL COMMENT 'USD amount',
  `amount_paid` varchar(20) DEFAULT NULL,
  `subtotal_amount` decimal(15,2) DEFAULT NULL,
  `subtotal_amount2` decimal(15,2) DEFAULT NULL COMMENT 'USD subtotal',
  `discount_amount` decimal(15,2) DEFAULT 0.00,
  `discount_amount2` decimal(15,2) DEFAULT 0.00 COMMENT 'USD discount',
  `tax_amount` decimal(15,2) DEFAULT 0.00,
  `tax_amount2` decimal(15,2) DEFAULT 0.00 COMMENT 'USD tax',
  `tax_percentage` decimal(5,2) DEFAULT 0.00,
  `applied_coupon_code` varchar(100) DEFAULT NULL,
  `status` varchar(225) DEFAULT NULL,
  `title` varchar(500) DEFAULT NULL,
  `quantity` varchar(500) DEFAULT NULL,
  `unit_price` varchar(1000) DEFAULT NULL,
  `unit_price2` varchar(1000) DEFAULT NULL COMMENT 'USD unit prices',
  `name` varchar(225) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phonenumber` varchar(225) DEFAULT NULL,
  `description` text,
  `date_created` date DEFAULT NULL,
  `time_created` time DEFAULT NULL,
  `custom` text,
  `paystack_ref` varchar(225) DEFAULT NULL,
  `currency` char(4) DEFAULT NULL,
  `address` text,
  `created_by` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_id` (`invoice_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
-- Table: ecommerce_coupon
-- Coupon codes with global and per-user usage limits
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `ecommerce_coupon` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(225) NOT NULL,
  `code` varchar(100) NOT NULL,
  `type` varchar(20) NOT NULL COMMENT 'percentage or fixed',
  `value` decimal(15,2) NOT NULL,
  `min_cart_amount` decimal(15,2) DEFAULT 0.00,
  `max_global_usage` int(11) NOT NULL DEFAULT 0 COMMENT '0 = unlimited',
  `max_user_usage` int(11) NOT NULL DEFAULT 1,
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
  `time_created` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
-- Table: ecommerce_coupon_usage
-- Tracks coupon usage per user/invoice
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `ecommerce_coupon_usage` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `coupon_id` int(10) UNSIGNED NOT NULL,
  `identifier` varchar(255) NOT NULL COMMENT 'user email or user_id',
  `invoice_id` varchar(225) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'applied' COMMENT 'applied or consumed',
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
  `time_created` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `coupon_id` (`coupon_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
-- Table: settings_global_discount
-- System-wide sale/discount toggle with expiry
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `settings_global_discount` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(225) NOT NULL,
  `discount_type` varchar(20) NOT NULL DEFAULT 'percentage' COMMENT 'percentage or fixed',
  `discount_value` decimal(15,2) NOT NULL DEFAULT 0.00,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `discount_label` varchar(100) DEFAULT 'Flash Sale',
  `expires_at` datetime DEFAULT NULL,
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
  `time_created` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT IGNORE INTO `settings_global_discount`
  (`id`, `hash_id`, `discount_type`, `discount_value`, `is_active`, `discount_label`, `expires_at`)
VALUES
  (1, 'globaldisc_001', 'percentage', 0.00, 0, 'Flash Sale', NULL);

-- --------------------------------------------------------
-- Table: panel_shipping_locations
-- Location-based shipping cost matrix
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `panel_shipping_locations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(225) NOT NULL,
  `input_location_name` varchar(255) NOT NULL,
  `input_shipping_fee` decimal(15,2) NOT NULL DEFAULT 0.00,
  `input_shipping_fee_usd` decimal(15,2) NOT NULL DEFAULT 0.00,
  `input_estimated_delivery_time` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
  `time_created` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
-- Table: settings_vat_settings
-- VAT percentage configuration
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `settings_vat_settings` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(225) NOT NULL,
  `input_vat_percentage` decimal(5,2) NOT NULL DEFAULT 7.50,
  `visibility` varchar(20) NOT NULL DEFAULT 'show',
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
  `time_created` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT IGNORE INTO `settings_vat_settings` (`id`, `hash_id`, `input_vat_percentage`, `visibility`)
VALUES (1, 'vatset_001', 7.50, 'show');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
