-- ============================================================
-- VENORA SKINCARE — CONTENT POPULATION SQL
-- Replaces placeholder/Kray Pharmacy data with real Venora content
-- Run this after importing mckodevecommerce.sql
-- ============================================================

-- --------------------------------------------------------
-- 1. PRODUCT CATEGORIES
-- --------------------------------------------------------
TRUNCATE TABLE `selection_product_category`;

INSERT INTO `selection_product_category` (`id`, `input_title`, `hash_id`, `image_1`, `visibility`, `time_created`, `date_created`, `created_by`, `icon_icon`) VALUES
(1, 'Serums',      'cat-serums',        NULL, 'show', '12:00:00', '2026-01-01', 'system', ''),
(2, 'Eye Care',    'cat-eye-care',      NULL, 'show', '12:00:00', '2026-01-01', 'system', ''),
(3, 'Cleansers',   'cat-cleansers',     NULL, 'show', '12:00:00', '2026-01-01', 'system', ''),
(4, 'Moisturizers','cat-moisturizers',  NULL, 'show', '12:00:00', '2026-01-01', 'system', ''),
(5, 'Treatments',  'cat-treatments',    NULL, 'show', '12:00:00', '2026-01-01', 'system', '');

-- --------------------------------------------------------
-- 2. PRODUCTS  (panel_product — demo16 schema)
--    select_product_category: 1=Serums 2=Eye Care 3=Cleansers 4=Moisturizers 5=Treatments
-- --------------------------------------------------------
TRUNCATE TABLE `panel_product`;

INSERT INTO `panel_product`
  (`id`, `hash_id`, `input_product_name`, `text_description`, `select_product_category`,
   `input_discount_percentage`, `dated_discount_enddate`, `image_2`, `visibility`, `date_created`, `time_created`, `created_by`)
VALUES
(1,  'vnr-srs-001', 'Radiance Boost Serum',
  'Bring back your skin\'s natural glow with VENORA\'s Radiance Boost Serum. Enriched with Vitamin C and Hyaluronic Acid, this lightweight serum brightens dull skin, smooths fine lines, and deeply hydrates for a luminous, healthy complexion. Ideal for daily use, it absorbs quickly, leaving your skin soft, radiant, and revitalized.',
  '1', '', NULL, '/assets/img/products/radiance-serum-1.webp', 'show', '2026-01-01', '12:00:00', 'system'),

(2,  'vnr-eye-001', 'Anti-Aging Eye Cream',
  'Combat the signs of aging around your eyes with Venora Anti-Aging Eye Cream, a rich yet fast-absorbing formula designed to reduce fine lines, wrinkles, and sagging while hydrating and firming the delicate eye area. Enriched with Retinol and Peptides, this cream supports skin renewal, improves elasticity, and promotes a youthful, lifted appearance.',
  '2', '', NULL, '/assets/img/products/anti-aging-cream-1.webp', 'show', '2026-01-01', '12:00:00', 'system'),

(3,  'vnr-cln-001', 'Refreshing Gel Cleanser',
  'Start your skincare routine with Refreshing Gel Cleanser, a gentle yet effective formula that purifies and revitalizes your skin. Infused with Green Tea Extract and Aloe Vera, this refreshing gel removes impurities, excess oil, and makeup without stripping your skin of its natural moisture.',
  '3', '', NULL, '/assets/img/products/gel-cleanser-1.webp', 'show', '2026-01-01', '12:00:00', 'system'),

(4,  'vnr-mos-001', 'Hydrasilk Moisturizer',
  'Nourish and hydrate your skin with Hydrasilk Moisturizer, a luxurious, lightweight cream that delivers deep moisture while leaving your skin silky-smooth and radiant. Enriched with Hyaluronic Acid and Squalane, this moisturizer replenishes hydration, softens fine lines, and restores elasticity.',
  '4', '', NULL, '/assets/img/products/hydrasilk-1.webp', 'show', '2026-01-01', '12:00:00', 'system'),

(5,  'vnr-nit-001', 'Velvet Night Cream',
  'Replenish and rejuvenate your skin overnight with Venora Velvet Night Cream, a rich, luxurious formula designed to deeply hydrate and repair while you sleep. Infused with Retinol and Hyaluronic Acid, this cream smooths fine lines, restores elasticity, and nourishes the skin for a soft, supple, and radiant complexion by morning.',
  '5', '', NULL, '/assets/img/products/velvet-cream-1.webp', 'show', '2026-01-01', '12:00:00', 'system'),

(6,  'vnr-day-001', 'Luminous Day Cream',
  'Start your day with radiant, hydrated skin using Venora Luminous Day Cream, a luxurious, lightweight moisturizer designed to brighten and protect your complexion. Enriched with Vitamin C and Hyaluronic Acid, this cream deeply hydrates, smooths fine lines, and promotes a luminous, even skin tone.',
  '4', '', NULL, '/assets/img/products/luminous-day-1.webp', 'show', '2026-01-01', '12:00:00', 'system'),

(7,  'vnr-eye-002', 'Brightening Eye Serum',
  'Illuminate and refresh your delicate eye area with Venora Brightening Eye Serum, a lightweight, fast-absorbing formula designed to reduce dark circles, puffiness, and fine lines. Enriched with Vitamin C and Peptides, this serum brightens the under-eye area, smooths texture, and provides gentle hydration for a refreshed, awake appearance.',
  '2', '', NULL, '/assets/img/products/brightening-serum-1.webp', 'show', '2026-01-01', '12:00:00', 'system'),

(8,  'vnr-fcl-001', 'Gentle Foaming Cleanser',
  'Experience a delicate yet thorough cleanse with Venora Gentle Foaming Cleanser, a lightweight foaming formula that purifies and refreshes your skin without stripping its natural moisture. Enriched with Chamomile Extract and Aloe Vera, this gentle cleanser removes impurities, excess oil, and light makeup, leaving the skin soft, balanced, and revitalized.',
  '3', '', NULL, '/assets/img/products/foaming-cleanser-1.webp', 'show', '2026-01-01', '12:00:00', 'system'),

(9,  'vnr-dhy-001', 'Deep Hydration Serum',
  'Quench your skin\'s thirst with Venora Deep Hydration Serum, a luxurious, fast-absorbing formula designed to deliver intense moisture and restore suppleness. Enriched with Hyaluronic Acid and Aloe Vera, this serum deeply penetrates the skin to smooth fine lines, plump dehydrated areas, and leave your complexion radiant and refreshed.',
  '1', '', NULL, '/assets/img/products/deep-hydration-1.webp', 'show', '2026-01-01', '12:00:00', 'system'),

(10, 'vnr-por-001', 'Pore Perfect Treatment',
  'Refine and clarify your complexion with Venora Pore Perfect Serum. This lightweight, fast-absorbing serum is enriched with Niacinamide and Salicylic Acid to minimize the appearance of pores, control excess oil, and smooth skin texture. Ideal for combination and oily skin, it leaves your face feeling fresh, balanced, and flawlessly smooth.',
  '5', '', NULL, '/assets/img/products/pore-perfect-1.webp', 'show', '2026-01-01', '12:00:00', 'system'),

(11, 'vnr-mcl-001', 'Hydrating Milk Cleanser',
  'Gently cleanse and nourish your skin with Venora Hydrating Milk Cleanser, a luxurious formula that removes impurities and makeup while delivering lasting hydration. Enriched with Shea Butter and Aloe Vera, this creamy milk cleanser softens and soothes the skin, leaving it supple, comfortable, and radiant.',
  '3', '', NULL, '/assets/img/products/milk-cleanser-1.webp', 'show', '2026-01-01', '12:00:00', 'system'),

(12, 'vnr-ney-001', 'Soothing Night Eye Cream',
  'Repair and rejuvenate your delicate eye area overnight with Venora Soothing Night Eye Cream, a rich, calming formula designed to reduce puffiness, dark circles, and fine lines while you sleep. Enriched with Hyaluronic Acid and Chamomile Extract, this cream deeply hydrates, soothes, and restores elasticity.',
  '2', '', NULL, '/assets/img/products/night-eye-cream-1.webp', 'show', '2026-01-01', '12:00:00', 'system');

-- --------------------------------------------------------
-- 3. VARIANTS (Size and Skin Type options for all 12 products)
--    NGN at ~1,500/USD exchange rate
-- --------------------------------------------------------
TRUNCATE TABLE `variants`;
TRUNCATE TABLE `variant_values_link`;

INSERT IGNORE INTO `product_options` (`id`, `option_name`) VALUES (1, 'Size'), (2, 'Skin Type');
INSERT IGNORE INTO `product_option_values` (`id`, `option_id`, `value_name`) VALUES 
(1, 1, '30ml'), 
(2, 1, '50ml'),
(3, 2, 'Normal'),
(4, 2, 'Oily'),
(5, 2, 'Dry'),
(6, 2, 'Sensitive');

INSERT INTO `variants`
  (`id`, `product_hash_id`, `input_price_ngn`, `input_price_usd`, `input_inventory`, `sku`, `image_1`, `input_weight_in_kg`)
VALUES
-- 1. Radiance Boost Serum
(1,  'vnr-srs-001', 60000.00,  40.00, 999, 'VNR-SRM-001-SZ-30ML', NULL, '0.1'),
(2,  'vnr-srs-001', 75000.00,  50.00, 999, 'VNR-SRM-001-SZ-50ML', NULL, '0.15'),
(3,  'vnr-srs-001', 75000.00,  50.00, 999, 'VNR-SRM-001-SK-NORMAL', NULL, '0.15'),
(4,  'vnr-srs-001', 75000.00,  50.00, 999, 'VNR-SRM-001-SK-OILY', NULL, '0.15'),
(5,  'vnr-srs-001', 75000.00,  50.00, 999, 'VNR-SRM-001-SK-DRY', NULL, '0.15'),
(6,  'vnr-srs-001', 75000.00,  50.00, 999, 'VNR-SRM-001-SK-SENSIT', NULL, '0.15'),
-- 2. Anti-Aging Eye Cream
(7,  'vnr-eye-001', 36000.00,  24.00, 999, 'VNR-EYE-001-SZ-30ML', NULL, '0.1'),
(8,  'vnr-eye-001', 45000.00,  30.00, 999, 'VNR-EYE-001-SZ-50ML', NULL, '0.15'),
(9,  'vnr-eye-001', 45000.00,  30.00, 999, 'VNR-EYE-001-SK-NORMAL', NULL, '0.15'),
(10, 'vnr-eye-001', 45000.00,  30.00, 999, 'VNR-EYE-001-SK-OILY', NULL, '0.15'),
(11, 'vnr-eye-001', 45000.00,  30.00, 999, 'VNR-EYE-001-SK-DRY', NULL, '0.15'),
(12, 'vnr-eye-001', 45000.00,  30.00, 999, 'VNR-EYE-001-SK-SENSIT', NULL, '0.15'),
-- 3. Refreshing Gel Cleanser
(13, 'vnr-cln-001', 36000.00,  24.00, 999, 'VNR-CLN-001-SZ-30ML', NULL, '0.1'),
(14, 'vnr-cln-001', 45000.00,  30.00, 999, 'VNR-CLN-001-SZ-50ML', NULL, '0.15'),
(15, 'vnr-cln-001', 45000.00,  30.00, 999, 'VNR-CLN-001-SK-NORMAL', NULL, '0.15'),
(16, 'vnr-cln-001', 45000.00,  30.00, 999, 'VNR-CLN-001-SK-OILY', NULL, '0.15'),
(17, 'vnr-cln-001', 45000.00,  30.00, 999, 'VNR-CLN-001-SK-DRY', NULL, '0.15'),
(18, 'vnr-cln-001', 45000.00,  30.00, 999, 'VNR-CLN-001-SK-SENSIT', NULL, '0.15'),
-- 4. Hydrasilk Moisturizer
(19, 'vnr-mos-001', 84000.00,  56.00, 999, 'VNR-MOS-001-SZ-30ML', NULL, '0.1'),
(20, 'vnr-mos-001', 105000.00, 70.00, 999, 'VNR-MOS-001-SZ-50ML', NULL, '0.15'),
(21, 'vnr-mos-001', 105000.00, 70.00, 999, 'VNR-MOS-001-SK-NORMAL', NULL, '0.15'),
(22, 'vnr-mos-001', 105000.00, 70.00, 999, 'VNR-MOS-001-SK-OILY', NULL, '0.15'),
(23, 'vnr-mos-001', 105000.00, 70.00, 999, 'VNR-MOS-001-SK-DRY', NULL, '0.15'),
(24, 'vnr-mos-001', 105000.00, 70.00, 999, 'VNR-MOS-001-SK-SENSIT', NULL, '0.15'),
-- 5. Velvet Night Cream
(25, 'vnr-nit-001', 78000.00,  52.00, 999, 'VNR-NIT-001-SZ-30ML', NULL, '0.1'),
(26, 'vnr-nit-001', 97500.00,  65.00, 999, 'VNR-NIT-001-SZ-50ML', NULL, '0.15'),
(27, 'vnr-nit-001', 97500.00,  65.00, 999, 'VNR-NIT-001-SK-NORMAL', NULL, '0.15'),
(28, 'vnr-nit-001', 97500.00,  65.00, 999, 'VNR-NIT-001-SK-OILY', NULL, '0.15'),
(29, 'vnr-nit-001', 97500.00,  65.00, 999, 'VNR-NIT-001-SK-DRY', NULL, '0.15'),
(30, 'vnr-nit-001', 97500.00,  65.00, 999, 'VNR-NIT-001-SK-SENSIT', NULL, '0.15'),
-- 6. Luminous Day Cream
(31, 'vnr-day-001', 72000.00,  48.00, 999, 'VNR-DAY-001-SZ-30ML', NULL, '0.1'),
(32, 'vnr-day-001', 90000.00,  60.00, 999, 'VNR-DAY-001-SZ-50ML', NULL, '0.15'),
(33, 'vnr-day-001', 90000.00,  60.00, 999, 'VNR-DAY-001-SK-NORMAL', NULL, '0.15'),
(34, 'vnr-day-001', 90000.00,  60.00, 999, 'VNR-DAY-001-SK-OILY', NULL, '0.15'),
(35, 'vnr-day-001', 90000.00,  60.00, 999, 'VNR-DAY-001-SK-DRY', NULL, '0.15'),
(36, 'vnr-day-001', 90000.00,  60.00, 999, 'VNR-DAY-001-SK-SENSIT', NULL, '0.15'),
-- 7. Brightening Eye Serum
(37, 'vnr-eye-002', 66000.00,  44.00, 999, 'VNR-EYS-001-SZ-30ML', NULL, '0.1'),
(38, 'vnr-eye-002', 82500.00,  55.00, 999, 'VNR-EYS-001-SZ-50ML', NULL, '0.15'),
(39, 'vnr-eye-002', 82500.00,  55.00, 999, 'VNR-EYS-001-SK-NORMAL', NULL, '0.15'),
(40, 'vnr-eye-002', 82500.00,  55.00, 999, 'VNR-EYS-001-SK-OILY', NULL, '0.15'),
(41, 'vnr-eye-002', 82500.00,  55.00, 999, 'VNR-EYS-001-SK-DRY', NULL, '0.15'),
(42, 'vnr-eye-002', 82500.00,  55.00, 999, 'VNR-EYS-001-SK-SENSIT', NULL, '0.15'),
-- 8. Gentle Foaming Cleanser
(43, 'vnr-fcl-001', 33600.00,  22.40, 999, 'VNR-FCL-001-SZ-30ML', NULL, '0.1'),
(44, 'vnr-fcl-001', 42000.00,  28.00, 999, 'VNR-FCL-001-SZ-50ML', NULL, '0.15'),
(45, 'vnr-fcl-001', 42000.00,  28.00, 999, 'VNR-FCL-001-SK-NORMAL', NULL, '0.15'),
(46, 'vnr-fcl-001', 42000.00,  28.00, 999, 'VNR-FCL-001-SK-OILY', NULL, '0.15'),
(47, 'vnr-fcl-001', 42000.00,  28.00, 999, 'VNR-FCL-001-SK-DRY', NULL, '0.15'),
(48, 'vnr-fcl-001', 42000.00,  28.00, 999, 'VNR-FCL-001-SK-SENSIT', NULL, '0.15'),
-- 9. Deep Hydration Serum
(49, 'vnr-dhy-001', 90000.00,  60.00, 999, 'VNR-DHY-001-SZ-30ML', NULL, '0.1'),
(50, 'vnr-dhy-001', 112500.00, 75.00, 999, 'VNR-DHY-001-SZ-50ML', NULL, '0.15'),
(51, 'vnr-dhy-001', 112500.00, 75.00, 999, 'VNR-DHY-001-SK-NORMAL', NULL, '0.15'),
(52, 'vnr-dhy-001', 112500.00, 75.00, 999, 'VNR-DHY-001-SK-OILY', NULL, '0.15'),
(53, 'vnr-dhy-001', 112500.00, 75.00, 999, 'VNR-DHY-001-SK-DRY', NULL, '0.15'),
(54, 'vnr-dhy-001', 112500.00, 75.00, 999, 'VNR-DHY-001-SK-SENSIT', NULL, '0.15'),
-- 10. Pore Perfect Treatment
(55, 'vnr-por-001', 54000.00,  36.00, 999, 'VNR-POR-001-SZ-30ML', NULL, '0.1'),
(56, 'vnr-por-001', 67500.00,  45.00, 999, 'VNR-POR-001-SZ-50ML', NULL, '0.15'),
(57, 'vnr-por-001', 67500.00,  45.00, 999, 'VNR-POR-001-SK-NORMAL', NULL, '0.15'),
(58, 'vnr-por-001', 67500.00,  45.00, 999, 'VNR-POR-001-SK-OILY', NULL, '0.15'),
(59, 'vnr-por-001', 67500.00,  45.00, 999, 'VNR-POR-001-SK-DRY', NULL, '0.15'),
(60, 'vnr-por-001', 67500.00,  45.00, 999, 'VNR-POR-001-SK-SENSIT', NULL, '0.15'),
-- 11. Hydrating Milk Cleanser
(61, 'vnr-mcl-001', 38400.00,  25.60, 999, 'VNR-MCL-001-SZ-30ML', NULL, '0.1'),
(62, 'vnr-mcl-001', 48000.00,  32.00, 999, 'VNR-MCL-001-SZ-50ML', NULL, '0.15'),
(63, 'vnr-mcl-001', 48000.00,  32.00, 999, 'VNR-MCL-001-SK-NORMAL', NULL, '0.15'),
(64, 'vnr-mcl-001', 48000.00,  32.00, 999, 'VNR-MCL-001-SK-OILY', NULL, '0.15'),
(65, 'vnr-mcl-001', 48000.00,  32.00, 999, 'VNR-MCL-001-SK-DRY', NULL, '0.15'),
(66, 'vnr-mcl-001', 48000.00,  32.00, 999, 'VNR-MCL-001-SK-SENSIT', NULL, '0.15'),
-- 12. Soothing Night Eye Cream
(67, 'vnr-ney-001', 48000.00,  32.00, 999, 'VNR-NEY-001-SZ-30ML', NULL, '0.1'),
(68, 'vnr-ney-001', 60000.00,  40.00, 999, 'VNR-NEY-001-SZ-50ML', NULL, '0.15'),
(69, 'vnr-ney-001', 60000.00,  40.00, 999, 'VNR-NEY-001-SK-NORMAL', NULL, '0.15'),
(70, 'vnr-ney-001', 60000.00,  40.00, 999, 'VNR-NEY-001-SK-OILY', NULL, '0.15'),
(71, 'vnr-ney-001', 60000.00,  40.00, 999, 'VNR-NEY-001-SK-DRY', NULL, '0.15'),
(72, 'vnr-ney-001', 60000.00,  40.00, 999, 'VNR-NEY-001-SK-SENSIT', NULL, '0.15');

INSERT INTO `variant_values_link` (`variant_id`, `value_id`) VALUES
(1, 1),(7, 1),(13, 1),(19, 1),(25, 1),(31, 1),(37, 1),(43, 1),(49, 1),(55, 1),(61, 1),(67, 1),
(2, 2),(8, 2),(14, 2),(20, 2),(26, 2),(32, 2),(38, 2),(44, 2),(50, 2),(56, 2),(62, 2),(68, 2),
(3, 3),(9, 3),(15, 3),(21, 3),(27, 3),(33, 3),(39, 3),(45, 3),(51, 3),(57, 3),(63, 3),(69, 3),
(4, 4),(10, 4),(16, 4),(22, 4),(28, 4),(34, 4),(40, 4),(46, 4),(52, 4),(58, 4),(64, 4),(70, 4),
(5, 5),(11, 5),(17, 5),(23, 5),(29, 5),(35, 5),(41, 5),(47, 5),(53, 5),(59, 5),(65, 5),(71, 5),
(6, 6),(12, 6),(18, 6),(24, 6),(30, 6),(36, 6),(42, 6),(48, 6),(54, 6),(60, 6),(66, 6),(72, 6); -- Soothing Night Eye Cream

-- --------------------------------------------------------
-- 4. SETTINGS — Shop Config (Venora brand)
-- --------------------------------------------------------
UPDATE `settings_shop_config` SET
  `input_name`             = 'Venora',
  `input_tagline`          = 'Luxury Skincare',
  `input_currency`         = 'USD',
  `input_currency_symbol`  = '$',
  `input_tax_rate`         = '0',
  `input_shipping_rate`    = '10',
  `input_free_shipping`    = '100',
  `image_1`                = '/assets/img/brand/venora-white.svg',
  `image_2`                = '/assets/img/brand/venora-dark.svg'
WHERE id = 1;

-- --------------------------------------------------------
-- 5. HERO — Home banner
-- --------------------------------------------------------
UPDATE `settings_home_hero` SET
  `input_heading`    = 'Your natural beauty, expressed with care',
  `input_btn1_label` = 'Shop now',
  `input_btn2_label` = 'Our collection',
  `input_trust_text` = 'Trusted by 300+ clients',
  `input_rating`     = '4.9/5',
  `input_video_url`  = 'https://videos.pexels.com/video-files/7304311/7304311-hd_1920_1080_30fps.mp4',
  `image_1`          = '/assets/img/products/radiance-serum-1.webp'
WHERE id = 1;

-- --------------------------------------------------------
-- 6. WEBSITE INFO (Venora)
-- --------------------------------------------------------
UPDATE `settings_website_info` SET
  `input_name`        = 'Venora',
  `text_description`  = 'VENORA was born from a passion for luxurious skincare that empowers women to feel confident in their own skin. Inspired by elegance, nature, and science, every product is carefully crafted to enhance your natural beauty.',
  `input_country`     = 'Nigeria',
  `input_email`       = 'hello@venora.com',
  `input_usd_toggle`  = 1
WHERE id = 1;

-- --------------------------------------------------------
-- 7. FOOTER
-- --------------------------------------------------------
UPDATE `settings_shop_footer` SET
  `input_cta_heading`        = 'Ready for Your Best Skin Yet?',
  `input_cta_btn`            = 'Book a Consultation',
  `input_newsletter_heading` = 'Stay updated with the latest from Venora!'
WHERE id = 1;

-- --------------------------------------------------------
-- 8. PRODUCT GALLERY IMAGES (images table)
-- --------------------------------------------------------
DELETE FROM `images` WHERE `asset_hash_id` LIKE 'vnr-%';

INSERT INTO `images` (`image_hash_id`, `asset_hash_id`, `image_1`, `date_created`, `time_created`) VALUES
('img-srs-001-1', 'vnr-srs-001', '/assets/img/products/radiance-serum-1.webp', '2026-05-19', '15:26:00'),
('img-srs-001-2', 'vnr-srs-001', '/assets/img/products/radiance-serum-2.webp', '2026-05-19', '15:26:00'),
('img-eye-001-1', 'vnr-eye-001', '/assets/img/products/anti-aging-cream-1.webp', '2026-05-19', '15:26:00'),
('img-eye-001-2', 'vnr-eye-001', '/assets/img/products/anti-aging-cream-2.webp', '2026-05-19', '15:26:00'),
('img-cln-001-1', 'vnr-cln-001', '/assets/img/products/gel-cleanser-1.webp', '2026-05-19', '15:26:00'),
('img-cln-001-2', 'vnr-cln-001', '/assets/img/products/gel-cleanser-2.webp', '2026-05-19', '15:26:00'),
('img-mos-001-1', 'vnr-mos-001', '/assets/img/products/hydrasilk-1.webp', '2026-05-19', '15:26:00'),
('img-mos-001-2', 'vnr-mos-001', '/assets/img/products/hydrasilk-2.webp', '2026-05-19', '15:26:00'),
('img-mos-001-3', 'vnr-mos-001', '/assets/img/products/hydrasilk-3.webp', '2026-05-19', '15:26:00'),
('img-nit-001-1', 'vnr-nit-001', '/assets/img/products/velvet-cream-1.webp', '2026-05-19', '15:26:00'),
('img-nit-001-2', 'vnr-nit-001', '/assets/img/products/velvet-cream-2.webp', '2026-05-19', '15:26:00'),
('img-day-001-1', 'vnr-day-001', '/assets/img/products/luminous-day-1.webp', '2026-05-19', '15:26:00'),
('img-day-001-2', 'vnr-day-001', '/assets/img/products/luminous-day-2.webp', '2026-05-19', '15:26:00'),
('img-eye-002-1', 'vnr-eye-002', '/assets/img/products/brightening-serum-1.webp', '2026-05-19', '15:26:00'),
('img-eye-002-2', 'vnr-eye-002', '/assets/img/products/brightening-serum-2.webp', '2026-05-19', '15:26:00'),
('img-fcl-001-1', 'vnr-fcl-001', '/assets/img/products/foaming-cleanser-1.webp', '2026-05-19', '15:26:00'),
('img-fcl-001-2', 'vnr-fcl-001', '/assets/img/products/foaming-cleanser-2.webp', '2026-05-19', '15:26:00'),
('img-dhy-001-1', 'vnr-dhy-001', '/assets/img/products/deep-hydration-1.webp', '2026-05-19', '15:26:00'),
('img-dhy-001-2', 'vnr-dhy-001', '/assets/img/products/deep-hydration-2.webp', '2026-05-19', '15:26:00'),
('img-por-001-1', 'vnr-por-001', '/assets/img/products/pore-perfect-1.webp', '2026-05-19', '15:26:00'),
('img-por-001-2', 'vnr-por-001', '/assets/img/products/pore-perfect-2.webp', '2026-05-19', '15:26:00'),
('img-mcl-001-1', 'vnr-mcl-001', '/assets/img/products/milk-cleanser-1.webp', '2026-05-19', '15:26:00'),
('img-mcl-001-2', 'vnr-mcl-001', '/assets/img/products/milk-cleanser-2.webp', '2026-05-19', '15:26:00'),
('img-ney-001-1', 'vnr-ney-001', '/assets/img/products/night-eye-cream-1.webp', '2026-05-19', '15:26:00'),
('img-ney-001-2', 'vnr-ney-001', '/assets/img/products/night-eye-cream-2.webp', '2026-05-19', '15:26:00');
