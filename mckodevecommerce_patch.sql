-- ============================================================
-- mckodevecommerce — Patch SQL (from demo61 audit)
-- Adds genuinely missing tables not covered by existing structure
-- Run: mysql -u root mckodevecommerce < mckodevecommerce_patch.sql
-- ============================================================

USE mckodevecommerce;

-- ── 1. read_users ────────────────────────────────────────────
-- Customer account management.  Follows ADMC naming conventions.
-- demo61 equivalent: read_users (adapted to our utf8mb4 + ADMC style)
CREATE TABLE IF NOT EXISTS `read_users` (
  `id`                INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `hash_id`           VARCHAR(255)  NOT NULL,
  `input_firstname`   VARCHAR(255)  DEFAULT NULL,
  `input_lastname`    VARCHAR(255)  DEFAULT NULL,
  `input_email`       VARCHAR(255)  DEFAULT NULL,
  `input_phone`       VARCHAR(100)  DEFAULT NULL,
  `input_address`     VARCHAR(500)  DEFAULT NULL,
  `input_city`        VARCHAR(255)  DEFAULT NULL,
  `input_state`       VARCHAR(255)  DEFAULT NULL,
  `input_country`     VARCHAR(255)  DEFAULT NULL,
  `input_password`    VARCHAR(255)  DEFAULT NULL,
  `input_avatar`      TEXT          DEFAULT NULL,
  `input_verify`      ENUM('1','0') DEFAULT '0'  COMMENT '1=verified, 0=unverified',
  `input_status`      ENUM('1','0') DEFAULT '1'  COMMENT '1=active, 0=suspended',
  `visibility`        VARCHAR(50)   DEFAULT 'show',
  `date_created`      DATE          NOT NULL,
  `time_created`      TIME          NOT NULL,
  `created_by`        VARCHAR(255)  DEFAULT 'system',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_email`    (`input_email`),
  UNIQUE KEY `uq_users_hash_id`  (`hash_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ── 2. verify ────────────────────────────────────────────────
-- Email / password-reset verification tokens for customers.
-- demo61 equivalent: verify
CREATE TABLE IF NOT EXISTS `verify` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `hash_id`       VARCHAR(255) DEFAULT NULL,
  `input_email`   VARCHAR(500) DEFAULT NULL,
  `token`         VARCHAR(500) DEFAULT NULL,  -- full JWT / uuid token
  `token_s`       VARCHAR(500) DEFAULT NULL,  -- short token (password reset)
  `verify_token`  VARCHAR(8)   DEFAULT NULL,  -- 6-8 digit OTP
  `token_type`    VARCHAR(50)  DEFAULT 'email_verify' COMMENT 'email_verify | password_reset',
  `token_expiry`  DATETIME     DEFAULT NULL,
  `visibility`    VARCHAR(50)  DEFAULT 'show',
  `date_created`  DATE         DEFAULT NULL,
  `time_created`  TIME         DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_verify_email` (`input_email`(191)),
  KEY `idx_verify_hash`  (`hash_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ── 3. shipping_data ─────────────────────────────────────────
-- Shipping tracking records linked to orders.
-- demo61 equivalent: shipping_data
CREATE TABLE IF NOT EXISTS `shipping_data` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `hash_id`       VARCHAR(255) DEFAULT NULL,
  `input_order_id`   VARCHAR(255) DEFAULT NULL  COMMENT 'links to read_orders.hash_id',
  `input_carrier`    VARCHAR(255) DEFAULT NULL  COMMENT 'e.g. DHL, FedEx, Royal Mail',
  `tracking_id`      VARCHAR(255) DEFAULT NULL,
  `tracking_code`    VARCHAR(255) DEFAULT NULL,
  `tracking_url`     TEXT         DEFAULT NULL,
  `input_status`     VARCHAR(100) DEFAULT 'pending' COMMENT 'pending|in_transit|delivered|failed',
  `text_notes`       TEXT         DEFAULT NULL,
  `text_data`        TEXT         DEFAULT NULL  COMMENT 'raw carrier response JSON',
  `visibility`       VARCHAR(50)  DEFAULT 'show',
  `date_created`     DATE         DEFAULT NULL,
  `time_created`     TIME         DEFAULT NULL,
  `created_by`       VARCHAR(255) DEFAULT 'system',
  PRIMARY KEY (`id`),
  KEY `idx_shipping_order` (`input_order_id`),
  KEY `idx_shipping_hash`  (`hash_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ── 4. addition_special_offers ───────────────────────────────
-- Product-level timed discount offers.
-- demo61 equivalent: addition_special_offers
-- Linked to panel_products via tb_link = panel_products.hash_id
CREATE TABLE IF NOT EXISTS `addition_special_offers` (
  `id`                        INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `hash_id`                   VARCHAR(255) NOT NULL,
  `tb`                        VARCHAR(255) DEFAULT 'panel_products' COMMENT 'parent table',
  `tb_link`                   VARCHAR(255) DEFAULT NULL             COMMENT 'panel_products.hash_id',
  `input_offer_title`         VARCHAR(255) DEFAULT NULL,
  `input_discount_percent`    INT(3)       DEFAULT NULL             COMMENT '0-100',
  `input_discount_fixed`      VARCHAR(20)  DEFAULT NULL             COMMENT 'flat amount off',
  `input_promo_code`          VARCHAR(50)  DEFAULT NULL,
  `input_offer_start`         DATETIME     DEFAULT NULL,
  `input_offer_end`           DATETIME     DEFAULT NULL,
  `input_badge_label`         VARCHAR(50)  DEFAULT 'SALE'           COMMENT 'e.g. SALE, HOT, 20% OFF',
  `visibility`                VARCHAR(50)  DEFAULT 'show',
  `date_created`              DATE         NOT NULL,
  `time_created`              TIME         NOT NULL,
  `created_by`                VARCHAR(255) DEFAULT 'system',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_offers_hash` (`hash_id`),
  KEY `idx_offers_product`    (`tb_link`),
  KEY `idx_offers_end`        (`input_offer_end`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ── 5. read_user_addresses ───────────────────────────────────
-- Multiple saved addresses per customer (extends read_users).
-- Not in demo61 but required for a complete checkout flow.
CREATE TABLE IF NOT EXISTS `read_user_addresses` (
  `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `hash_id`          VARCHAR(255) NOT NULL,
  `tb`               VARCHAR(255) DEFAULT 'read_users',
  `tb_link`          VARCHAR(255) DEFAULT NULL COMMENT 'read_users.hash_id',
  `input_label`      VARCHAR(100) DEFAULT 'Home' COMMENT 'Home|Work|Other',
  `input_firstname`  VARCHAR(255) DEFAULT NULL,
  `input_lastname`   VARCHAR(255) DEFAULT NULL,
  `input_phone`      VARCHAR(100) DEFAULT NULL,
  `input_address`    VARCHAR(500) DEFAULT NULL,
  `input_city`       VARCHAR(255) DEFAULT NULL,
  `input_state`      VARCHAR(255) DEFAULT NULL,
  `input_country`    VARCHAR(255) DEFAULT NULL,
  `input_postcode`   VARCHAR(20)  DEFAULT NULL,
  `input_is_default` ENUM('1','0') DEFAULT '0',
  `visibility`       VARCHAR(50)  DEFAULT 'show',
  `date_created`     DATE         NOT NULL,
  `time_created`     TIME         NOT NULL,
  `created_by`       VARCHAR(255) DEFAULT 'system',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_addr_hash`    (`hash_id`),
  KEY `idx_addr_user`          (`tb_link`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ── 6. read_password_resets ──────────────────────────────────
-- Separate password reset token table (cleaner than reusing verify).
CREATE TABLE IF NOT EXISTS `read_password_resets` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `hash_id`      VARCHAR(255) DEFAULT NULL,
  `input_email`  VARCHAR(255) DEFAULT NULL,
  `token`        VARCHAR(500) DEFAULT NULL,
  `token_expiry` DATETIME     DEFAULT NULL,
  `input_used`   ENUM('1','0') DEFAULT '0',
  `visibility`   VARCHAR(50)  DEFAULT 'show',
  `date_created` DATE         DEFAULT NULL,
  `time_created` TIME         DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_reset_email` (`input_email`),
  KEY `idx_reset_token` (`token`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ── 7. Alter read_orders — add user_id column if missing ─────
-- Links orders back to registered customer accounts
SET @col_exists = (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME   = 'read_orders'
    AND COLUMN_NAME  = 'input_user_id'
);
SET @sql = IF(@col_exists = 0,
  'ALTER TABLE `read_orders` ADD COLUMN `input_user_id` VARCHAR(255) DEFAULT NULL COMMENT ''read_users.hash_id'' AFTER `hash_id`',
  'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ── 8. Alter read_cart — add user_id column if missing ───────
-- Allows cart persistence for logged-in customers
SET @col_exists2 = (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME   = 'read_cart'
    AND COLUMN_NAME  = 'input_user_id'
);
SET @sql2 = IF(@col_exists2 = 0,
  'ALTER TABLE `read_cart` ADD COLUMN `input_user_id` VARCHAR(255) DEFAULT NULL COMMENT ''read_users.hash_id — NULL for guest sessions'' AFTER `input_session_id`',
  'SELECT 1'
);
PREPARE stmt2 FROM @sql2; EXECUTE stmt2; DEALLOCATE PREPARE stmt2;

-- ── Seed data ─────────────────────────────────────────────────
-- (no seed rows needed — these are user-generated or admin-managed tables)
