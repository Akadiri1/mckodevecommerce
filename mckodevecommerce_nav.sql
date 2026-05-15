USE mckodevecommerce;

-- Replace panel_pages with correct venora ecommerce nav
TRUNCATE TABLE `panel_pages`;

INSERT INTO `panel_pages` (`id`, `hash_id`, `input_name`, `input_link`, `input_order`, `visibility`, `date_created`, `time_created`, `created_by`)
VALUES
(1, 'nav001', 'Home',     '/',        1, 'show', CURDATE(), CURTIME(), 'system'),
(2, 'nav002', 'About',    '/about',   2, 'show', CURDATE(), CURTIME(), 'system'),
(3, 'nav003', 'Products', '/products',3, 'show', CURDATE(), CURTIME(), 'system'),
(4, 'nav004', 'Contact',  '/contact', 4, 'show', CURDATE(), CURTIME(), 'system');
