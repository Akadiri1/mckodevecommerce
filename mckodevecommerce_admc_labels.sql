-- ============================================================
-- mckodevecommerce — ADMC Labels & UI Text SQL
-- Makes every page text/image editable from ADMC admin panel
-- Run after mckodevecommerce.sql and mckodevecommerce_patch.sql
-- ============================================================

USE mckodevecommerce;

-- ── 1. settings_shop_ui_labels ───────────────────────────────
-- Shared UI text used across multiple pages (buttons, messages, labels)
CREATE TABLE IF NOT EXISTS `settings_shop_ui_labels` (
  `id`                              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `hash_id`                         VARCHAR(255) NOT NULL,
  -- Global buttons
  `input_add_to_cart`               VARCHAR(100) DEFAULT 'Add to Cart',
  `input_adding_to_cart`            VARCHAR(100) DEFAULT 'Adding...',
  `input_added_to_cart`             VARCHAR(100) DEFAULT 'Added!',
  `input_out_of_stock`              VARCHAR(100) DEFAULT 'Out of stock',
  `input_only_x_left`               VARCHAR(100) DEFAULT 'Only {n} left in stock — order soon',
  `input_in_stock`                  VARCHAR(100) DEFAULT 'In stock',
  `input_x_in_stock`                VARCHAR(100) DEFAULT '{n} in stock',
  `input_shop_now`                  VARCHAR(100) DEFAULT 'Shop Now',
  `input_load_more`                 VARCHAR(100) DEFAULT 'Load More',
  `input_all_loaded`                VARCHAR(100) DEFAULT 'All products shown',
  `input_quantity_label`            VARCHAR(100) DEFAULT 'Quantity',
  `input_no_products_msg`           VARCHAR(500) DEFAULT 'No products in this category yet.',
  `input_search_placeholder`        VARCHAR(255) DEFAULT 'Search products…',
  `input_sort_label`                VARCHAR(100) DEFAULT 'Sort by',
  `input_price_label`               VARCHAR(100) DEFAULT 'Price',
  `input_apply_label`               VARCHAR(100) DEFAULT 'Apply',
  `input_clear_filters`             VARCHAR(100) DEFAULT 'Clear filters',
  -- Product detail
  `input_details_tab`               VARCHAR(100) DEFAULT 'Details',
  `input_ingredients_tab`           VARCHAR(100) DEFAULT 'Ingredients',
  `input_reviews_tab`               VARCHAR(100) DEFAULT 'Reviews',
  `input_no_ingredients_msg`        VARCHAR(500) DEFAULT 'Ingredient list not available for this product.',
  `input_no_reviews_msg`            VARCHAR(500) DEFAULT 'No reviews yet — be the first to share your experience.',
  `input_write_review_btn`          VARCHAR(100) DEFAULT 'Write a Review',
  `input_review_form_heading`       VARCHAR(255) DEFAULT 'Share your experience',
  `input_review_name_placeholder`   VARCHAR(255) DEFAULT 'Your name',
  `input_review_title_placeholder`  VARCHAR(255) DEFAULT 'Review title',
  `input_review_body_placeholder`   VARCHAR(500) DEFAULT 'Tell others what you think about this product…',
  `input_review_submit_btn`         VARCHAR(100) DEFAULT 'Submit Review',
  `input_review_success_msg`        VARCHAR(500) DEFAULT 'Thank you for your review! It will appear shortly.',
  `input_you_might_like`            VARCHAR(255) DEFAULT 'You might also like',
  `visibility`                      VARCHAR(50)  DEFAULT 'show',
  `date_created`                    DATE         NOT NULL,
  `time_created`                    TIME         NOT NULL,
  `created_by`                      VARCHAR(255) DEFAULT 'system',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_ui_labels` (`hash_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT IGNORE INTO `settings_shop_ui_labels`
  (`id`,`hash_id`,`visibility`,`date_created`,`time_created`,`created_by`)
VALUES (1,'uilbl001','show',CURDATE(),CURTIME(),'system');

-- ── 2. settings_shop_cart_labels ─────────────────────────────
CREATE TABLE IF NOT EXISTS `settings_shop_cart_labels` (
  `id`                            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `hash_id`                       VARCHAR(255) NOT NULL,
  `input_page_heading`            VARCHAR(255) DEFAULT 'Your Cart',
  `input_empty_heading`           VARCHAR(255) DEFAULT 'Your cart is empty',
  `text_empty_description`        VARCHAR(500) DEFAULT 'Add something you love to get started.',
  `input_header_product`          VARCHAR(100) DEFAULT 'Product',
  `input_header_qty`              VARCHAR(100) DEFAULT 'Qty',
  `input_header_total`            VARCHAR(100) DEFAULT 'Total',
  `input_remove_btn`              VARCHAR(100) DEFAULT 'Remove',
  `input_summary_title`           VARCHAR(255) DEFAULT 'Order Summary',
  `input_subtotal_label`          VARCHAR(100) DEFAULT 'Subtotal',
  `input_shipping_label`          VARCHAR(100) DEFAULT 'Shipping',
  `input_free_shipping_text`      VARCHAR(100) DEFAULT 'Free',
  `input_tax_label`               VARCHAR(100) DEFAULT 'Tax',
  `input_total_label`             VARCHAR(100) DEFAULT 'Total',
  `input_checkout_btn`            VARCHAR(100) DEFAULT 'Proceed to Checkout',
  `input_continue_shopping`       VARCHAR(100) DEFAULT 'Continue Shopping',
  `text_free_shipping_note`       VARCHAR(500) DEFAULT 'Add {amount} more for free shipping',
  `visibility`                    VARCHAR(50)  DEFAULT 'show',
  `date_created`                  DATE         NOT NULL,
  `time_created`                  TIME         NOT NULL,
  `created_by`                    VARCHAR(255) DEFAULT 'system',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cart_labels` (`hash_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT IGNORE INTO `settings_shop_cart_labels`
  (`id`,`hash_id`,`visibility`,`date_created`,`time_created`,`created_by`)
VALUES (1,'crtlbl001','show',CURDATE(),CURTIME(),'system');

-- ── 3. settings_shop_checkout_labels ─────────────────────────
CREATE TABLE IF NOT EXISTS `settings_shop_checkout_labels` (
  `id`                             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `hash_id`                        VARCHAR(255) NOT NULL,
  `input_page_heading`             VARCHAR(255) DEFAULT 'Checkout',
  `input_contact_block_heading`    VARCHAR(255) DEFAULT 'Contact Information',
  `input_first_name_label`         VARCHAR(100) DEFAULT 'First Name',
  `input_last_name_label`          VARCHAR(100) DEFAULT 'Last Name',
  `input_email_label`              VARCHAR(100) DEFAULT 'Email Address',
  `input_phone_label`              VARCHAR(100) DEFAULT 'Phone Number',
  `input_address_block_heading`    VARCHAR(255) DEFAULT 'Shipping Address',
  `input_address1_label`           VARCHAR(100) DEFAULT 'Address Line 1',
  `input_address2_label`           VARCHAR(100) DEFAULT 'Address Line 2',
  `input_city_label`               VARCHAR(100) DEFAULT 'City',
  `input_postal_label`             VARCHAR(100) DEFAULT 'Postal / ZIP Code',
  `input_state_label`              VARCHAR(100) DEFAULT 'State / Province',
  `input_country_label`            VARCHAR(100) DEFAULT 'Country',
  `input_notes_label`              VARCHAR(100) DEFAULT 'Order Notes',
  `input_notes_placeholder`        VARCHAR(500) DEFAULT 'Any special instructions for your order?',
  `input_shipping_block_heading`   VARCHAR(255) DEFAULT 'Shipping Method',
  `input_standard_name`            VARCHAR(100) DEFAULT 'Standard Shipping',
  `input_standard_time`            VARCHAR(100) DEFAULT '5–7 business days',
  `input_express_name`             VARCHAR(100) DEFAULT 'Express Shipping',
  `input_express_time`             VARCHAR(100) DEFAULT '2–3 business days',
  `input_express_price`            VARCHAR(50)  DEFAULT '12.99',
  `input_payment_block_heading`    VARCHAR(255) DEFAULT 'Payment Information',
  `text_payment_message`           VARCHAR(500) DEFAULT 'Secure payment powered by Stripe',
  `input_card_number_label`        VARCHAR(100) DEFAULT 'Card Number',
  `input_expiry_label`             VARCHAR(100) DEFAULT 'MM / YY',
  `input_cvc_label`                VARCHAR(100) DEFAULT 'CVC',
  `input_place_order_btn`          VARCHAR(100) DEFAULT 'Place Order',
  `text_payment_secure_msg`        VARCHAR(500) DEFAULT 'Your payment info is encrypted and secure.',
  `input_summary_title`            VARCHAR(255) DEFAULT 'Order Summary',
  `input_subtotal_label`           VARCHAR(100) DEFAULT 'Subtotal',
  `input_shipping_label`           VARCHAR(100) DEFAULT 'Shipping',
  `input_tax_label`                VARCHAR(100) DEFAULT 'Tax',
  `input_total_label`              VARCHAR(100) DEFAULT 'Total',
  `visibility`                     VARCHAR(50)  DEFAULT 'show',
  `date_created`                   DATE         NOT NULL,
  `time_created`                   TIME         NOT NULL,
  `created_by`                     VARCHAR(255) DEFAULT 'system',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_checkout_labels` (`hash_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT IGNORE INTO `settings_shop_checkout_labels`
  (`id`,`hash_id`,`visibility`,`date_created`,`time_created`,`created_by`)
VALUES (1,'ckllbl001','show',CURDATE(),CURTIME(),'system');

-- ── 4. settings_shop_contact_labels ──────────────────────────
CREATE TABLE IF NOT EXISTS `settings_shop_contact_labels` (
  `id`                              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `hash_id`                         VARCHAR(255) NOT NULL,
  `input_email_label`               VARCHAR(100) DEFAULT 'Email',
  `input_phone_label`               VARCHAR(100) DEFAULT 'Phone',
  `input_orders_label`              VARCHAR(100) DEFAULT 'Orders & Shipping',
  `input_orders_email`              VARCHAR(255) DEFAULT 'orders@venora.com',
  `input_fn_label`                  VARCHAR(100) DEFAULT 'First name',
  `input_fn_placeholder`            VARCHAR(255) DEFAULT 'Jane',
  `input_ln_label`                  VARCHAR(100) DEFAULT 'Last name',
  `input_ln_placeholder`            VARCHAR(255) DEFAULT 'Doe',
  `input_email_form_label`          VARCHAR(100) DEFAULT 'Email address',
  `input_email_form_placeholder`    VARCHAR(255) DEFAULT 'jane@example.com',
  `input_subject_label`             VARCHAR(100) DEFAULT 'Subject',
  `input_subject_placeholder`       VARCHAR(255) DEFAULT 'How can we help?',
  `input_message_label`             VARCHAR(100) DEFAULT 'Message',
  `input_message_placeholder`       VARCHAR(500) DEFAULT 'Tell us more…',
  `input_submit_btn`                VARCHAR(100) DEFAULT 'Send Message',
  `text_success_message`            VARCHAR(500) DEFAULT 'Thank you! We\'ll be in touch shortly.',
  `text_error_message`              VARCHAR(500) DEFAULT 'Something went wrong. Please try again.',
  `visibility`                      VARCHAR(50)  DEFAULT 'show',
  `date_created`                    DATE         NOT NULL,
  `time_created`                    TIME         NOT NULL,
  `created_by`                      VARCHAR(255) DEFAULT 'system',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_contact_labels` (`hash_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT IGNORE INTO `settings_shop_contact_labels`
  (`id`,`hash_id`,`visibility`,`date_created`,`time_created`,`created_by`)
VALUES (1,'cnllbl001','show',CURDATE(),CURTIME(),'system');

-- ── 5. panel_trust_badges ─────────────────────────────────────
-- Editable trust/feature badges on product detail page
CREATE TABLE IF NOT EXISTS `panel_trust_badges` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `hash_id`       VARCHAR(255) NOT NULL,
  `input_label`   VARCHAR(255) DEFAULT NULL  COMMENT 'Badge text e.g. Dermatologist Tested',
  `input_icon`    TEXT DEFAULT NULL COMMENT 'SVG path or icon class',
  `input_order`   INT          DEFAULT 0,
  `visibility`    VARCHAR(50)  DEFAULT 'show',
  `date_created`  DATE         NOT NULL,
  `time_created`  TIME         NOT NULL,
  `created_by`    VARCHAR(255) DEFAULT 'system',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_badge_hash` (`hash_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT IGNORE INTO `panel_trust_badges`
  (`id`,`hash_id`,`input_label`,`input_icon`,`input_order`,`visibility`,`date_created`,`time_created`,`created_by`)
VALUES
(1,'bdg001','Dermatologist Tested','M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z',1,'show',CURDATE(),CURTIME(),'system'),
(2,'bdg002','Cruelty Free','M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z',2,'show',CURDATE(),CURTIME(),'system'),
(3,'bdg003','30-Day Returns','M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z M9 12l2 2 4-4',3,'show',CURDATE(),CURTIME(),'system');

-- ── 6. Extend settings_shop_footer with newsletter popup ──────
-- Use safe ALTER for MySQL 5.7 compatibility
SET @c1=(SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='settings_shop_footer' AND COLUMN_NAME='input_newsletter_popup_heading');
SET @s1=IF(@c1=0,'ALTER TABLE `settings_shop_footer` ADD COLUMN `input_newsletter_popup_heading` VARCHAR(255) DEFAULT \'Get 10% off your first order\'','SELECT 1');
PREPARE st FROM @s1; EXECUTE st; DEALLOCATE PREPARE st;

SET @c2=(SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='settings_shop_footer' AND COLUMN_NAME='text_newsletter_popup_description');
SET @s2=IF(@c2=0,'ALTER TABLE `settings_shop_footer` ADD COLUMN `text_newsletter_popup_description` TEXT','SELECT 1');
PREPARE st FROM @s2; EXECUTE st; DEALLOCATE PREPARE st;

SET @c3=(SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='settings_shop_footer' AND COLUMN_NAME='input_newsletter_popup_btn');
SET @s3=IF(@c3=0,'ALTER TABLE `settings_shop_footer` ADD COLUMN `input_newsletter_popup_btn` VARCHAR(100) DEFAULT \'Subscribe\'','SELECT 1');
PREPARE st FROM @s3; EXECUTE st; DEALLOCATE PREPARE st;

SET @c4=(SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='settings_shop_footer' AND COLUMN_NAME='input_newsletter_popup_dismiss');
SET @s4=IF(@c4=0,'ALTER TABLE `settings_shop_footer` ADD COLUMN `input_newsletter_popup_dismiss` VARCHAR(100) DEFAULT \'No thanks\'','SELECT 1');
PREPARE st FROM @s4; EXECUTE st; DEALLOCATE PREPARE st;

SET @c5=(SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='settings_shop_footer' AND COLUMN_NAME='input_newsletter_placeholder');
SET @s5=IF(@c5=0,'ALTER TABLE `settings_shop_footer` ADD COLUMN `input_newsletter_placeholder` VARCHAR(255) DEFAULT \'Email address...\'','SELECT 1');
PREPARE st FROM @s5; EXECUTE st; DEALLOCATE PREPARE st;

-- Update existing footer row with popup defaults
UPDATE `settings_shop_footer` SET
  `input_newsletter_popup_heading`     = IFNULL(`input_newsletter_popup_heading`,     'Get 10% off your first order'),
  `text_newsletter_popup_description`  = IFNULL(`text_newsletter_popup_description`,  'Subscribe for exclusive offers, skincare tips, and early access to new products.'),
  `input_newsletter_popup_btn`         = IFNULL(`input_newsletter_popup_btn`,          'Subscribe'),
  `input_newsletter_popup_dismiss`     = IFNULL(`input_newsletter_popup_dismiss`,      'No thanks'),
  `input_newsletter_placeholder`       = IFNULL(`input_newsletter_placeholder`,        'Email address...')
WHERE id = 1;

-- ── 7. Extend settings_shop_hero with missing fields ─────────
SET @c6=(SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='settings_shop_hero' AND COLUMN_NAME='input_partners_heading');
SET @s6=IF(@c6=0,'ALTER TABLE `settings_shop_hero` ADD COLUMN `input_partners_heading` VARCHAR(255) DEFAULT \'Trusted by leading brands\'','SELECT 1');
PREPARE st FROM @s6; EXECUTE st; DEALLOCATE PREPARE st;

SET @c7=(SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='settings_shop_hero' AND COLUMN_NAME='input_scroll_text');
SET @s7=IF(@c7=0,'ALTER TABLE `settings_shop_hero` ADD COLUMN `input_scroll_text` VARCHAR(100) DEFAULT \'Scroll Down\'','SELECT 1');
PREPARE st FROM @s7; EXECUTE st; DEALLOCATE PREPARE st;

-- ── 8. Extend settings_shop_config with orders email ─────────
SET @c8=(SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='settings_shop_config' AND COLUMN_NAME='input_orders_email');
SET @s8=IF(@c8=0,'ALTER TABLE `settings_shop_config` ADD COLUMN `input_orders_email` VARCHAR(255) DEFAULT \'orders@venora.com\'','SELECT 1');
PREPARE st FROM @s8; EXECUTE st; DEALLOCATE PREPARE st;

-- ── 9. settings_shop_products_page ────────────────────────────
-- Products page hero text
CREATE TABLE IF NOT EXISTS `settings_shop_products_page` (
  `id`                     INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `hash_id`                VARCHAR(255) NOT NULL,
  `input_heading`          VARCHAR(255) DEFAULT 'Explore products',
  `text_description`       VARCHAR(500) DEFAULT 'Carefully crafted formulas for every skin type. Clean ingredients. Real results.',
  `input_featured_heading` VARCHAR(255) DEFAULT 'We believe skincare is a ritual, not a routine',
  `text_featured_desc`     VARCHAR(500) DEFAULT 'Discover our curated selection of products designed to highlight your unique beauty.',
  `input_no_products_msg`  VARCHAR(500) DEFAULT 'No products found in this category.',
  `visibility`             VARCHAR(50)  DEFAULT 'show',
  `date_created`           DATE         NOT NULL,
  `time_created`           TIME         NOT NULL,
  `created_by`             VARCHAR(255) DEFAULT 'system',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_products_page` (`hash_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT IGNORE INTO `settings_shop_products_page`
  (`id`,`hash_id`,`visibility`,`date_created`,`time_created`,`created_by`)
VALUES (1,'pdgpg001','show',CURDATE(),CURTIME(),'system');

-- ── 10. settings_shop_about_sections ─────────────────────────
-- Stores the hardcoded headings in about.php
CREATE TABLE IF NOT EXISTS `settings_shop_about_sections` (
  `id`                         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `hash_id`                    VARCHAR(255) NOT NULL,
  `input_values_heading`       VARCHAR(255) DEFAULT 'Pure & Trusted',
  `text_values_subheading`     VARCHAR(500) DEFAULT 'Our products are crafted with your skin\'s health in mind.',
  `input_faq_heading`          VARCHAR(255) DEFAULT 'Frequently Asked Questions',
  `input_gallery_heading`      VARCHAR(255) DEFAULT 'Follow us on Instagram',
  `visibility`                 VARCHAR(50)  DEFAULT 'show',
  `date_created`               DATE         NOT NULL,
  `time_created`               TIME         NOT NULL,
  `created_by`                 VARCHAR(255) DEFAULT 'system',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_about_sections` (`hash_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT IGNORE INTO `settings_shop_about_sections`
  (`id`,`hash_id`,`visibility`,`date_created`,`time_created`,`created_by`)
VALUES (1,'absc001','show',CURDATE(),CURTIME(),'system');

-- ── Final: verify all tables created ─────────────────────────
SELECT TABLE_NAME, TABLE_ROWS
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME IN (
    'settings_shop_ui_labels',
    'settings_shop_cart_labels',
    'settings_shop_checkout_labels',
    'settings_shop_contact_labels',
    'panel_trust_badges',
    'settings_shop_products_page',
    'settings_shop_about_sections'
  )
ORDER BY TABLE_NAME;
