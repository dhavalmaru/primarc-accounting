CREATE TABLE IF NOT EXISTS `payment_advices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_id` bigint(11) DEFAULT NULL,
  `account_id` bigint(11) DEFAULT NULL,
  `payment_advice_path` varchar(255) DEFAULT NULL,
  `status` varchar(45) DEFAULT NULL,
  `created_by` bigint(11) DEFAULT NULL,
  `updated_by` bigint(11) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `updated_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `approver_comments` varchar(250) DEFAULT NULL,
  `approved_by` bigint(11) DEFAULT NULL,
  `approved_date` datetime DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

