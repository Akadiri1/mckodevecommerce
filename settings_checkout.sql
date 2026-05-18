-- settings_checkout table for mckodevecommerce
CREATE TABLE IF NOT EXISTS `settings_checkout` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `hash_id` varchar(100) NOT NULL,
  `input_page_title`    varchar(100) DEFAULT 'Checkout',
  `input_contact_title` varchar(100) DEFAULT 'Contact Information',
  `input_address_title` varchar(100) DEFAULT 'Shipping Address',
  `input_shipping_title`varchar(100) DEFAULT 'Shipping Method',
  `input_payment_title` varchar(100) DEFAULT 'Payment Information',
  `input_btn_text`      varchar(100) DEFAULT 'Place Order',
  `input_summary_title` varchar(100) DEFAULT 'Order Summary',
  `visibility` char(4) NOT NULL DEFAULT 'show',
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `settings_checkout`
  (`hash_id`, `input_page_title`, `input_contact_title`, `input_address_title`,
   `input_shipping_title`, `input_payment_title`, `input_btn_text`, `input_summary_title`,
   `visibility`, `date_created`, `time_created`)
VALUES
  ('chk001', 'Checkout', 'Contact Information', 'Shipping Address',
   'Shipping Method', 'Payment Information', 'Place Order', 'Order Summary',
   'show', CURDATE(), CURTIME());
