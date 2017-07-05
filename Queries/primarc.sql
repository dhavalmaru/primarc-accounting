--
-- Database: `primarc`
--

rename table account_category_master to acc_category_master;
rename table grn_acc_entries to acc_grn_entries;
rename table grn_debit_notes to acc_grn_debit_notes;
rename table grn_acc_sku_entries to acc_grn_sku_entries;
rename table journal_voucher_details to acc_jv_details;
rename table journal_voucher_entries to acc_jv_entries;
rename table ledger_entries to acc_ledger_entries;
rename table payment_advices to acc_payment_advices;
rename table payment_receipt_details to acc_payment_receipt;
rename table series_master to acc_series_master;

truncate table acc_email_log;
truncate table acc_grn_debit_notes;
truncate table acc_grn_entries;
truncate table acc_grn_sku_entries;
truncate table acc_jv_details;
truncate table acc_jv_docs;
truncate table acc_jv_entries;
truncate table acc_ledger_entries;
truncate table acc_payment_advices;
truncate table acc_payment_receipt;
update acc_series_master set series = 0 where type = 'Voucher';

update acc_categories set status = 'approved';
update acc_master set status = 'approved';

-- --------------------------------------------------------

--
-- Table structure for table `acc_email_log`
--

CREATE TABLE IF NOT EXISTS `acc_email_log` (
  `id` bigint(12) NOT NULL AUTO_INCREMENT,
  `module` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `email_type` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `warehouse_code` varchar(45) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `vendor_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `from_email_id` varchar(1024) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `to_recipient` varchar(1024) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `cc_recipient` varchar(1024) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `supporting_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `reference_number` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `reference_status` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `email_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `email_content` varchar(2048) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `email_attachment` varchar(1024) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `attachment_type` enum('PDF','XLS') CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `email_sent_status` tinyint(1) NOT NULL DEFAULT '1',
  `error_message` varchar(1024) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `company_id` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;


--
-- Table structure for table `acc_jv_docs`
--

CREATE TABLE IF NOT EXISTS `acc_jv_docs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `jv_id` bigint(20) DEFAULT NULL,
  `doc_path` varchar(500) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `acc_user_log`
--

CREATE TABLE IF NOT EXISTS `acc_user_log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `module_name` varchar(150) DEFAULT NULL,
  `sub_module` varchar(150) DEFAULT NULL,
  `action` varchar(150) DEFAULT NULL,
  `vendor_id` bigint(20) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  `log_activity_date` datetime DEFAULT NULL,
  `table_name` varchar(100) DEFAULT NULL,
  `table_id` bigint(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;


--
-- Table structure for table `acc_user_log_history`
--

CREATE TABLE IF NOT EXISTS `acc_user_log_history` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `company_id` bigint(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_action` varchar(250) CHARACTER SET utf8 NOT NULL,
  `action_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `acc_user_roles`
--

CREATE TABLE IF NOT EXISTS `acc_user_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `status` varchar(45) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT NULL,
  `created_by` bigint(11) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `updated_by` bigint(11) DEFAULT NULL,
  `updated_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `acc_user_roles`
--

INSERT INTO `acc_user_roles` (`id`, `user_id`, `role_id`, `status`, `is_active`, `created_by`, `created_date`, `updated_by`, `updated_date`) VALUES
(1, 127, 1, 'approved', 1, 127, '2017-05-24 19:43:24', 127, '2017-05-24 14:14:28'),
(2, 125, 3, 'approved', 1, 127, '2017-05-25 07:29:52', 127, '2017-05-25 04:29:16'),
(3, 21, 3, 'approved', 1, 127, '2017-05-25 07:58:20', 127, '2017-05-25 02:28:20');

-- --------------------------------------------------------

--
-- Table structure for table `acc_user_role_master`
--

CREATE TABLE IF NOT EXISTS `acc_user_role_master` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role` varchar(100) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `status` varchar(45) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT NULL,
  `created_by` bigint(11) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `updated_by` bigint(11) DEFAULT NULL,
  `updated_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `acc_user_role_master`
--

INSERT INTO `acc_user_role_master` (`id`, `role`, `description`, `status`, `is_active`, `created_by`, `created_date`, `updated_by`, `updated_date`) VALUES
(1, 'Admin', 'Admin', 'approved', 1, 127, '2017-05-23 00:00:00', 127, '2017-05-23 07:49:58'),
(2, 'View', 'View', 'approved', 1, 127, '2017-05-24 00:00:00', 127, '2017-05-25 02:36:09'),
(3, 'Update', 'Update', 'approved', 1, 127, '2017-05-24 15:15:50', 127, '2017-05-25 02:36:49'),
(4, 'Approve', 'Approve', 'approved', 1, 127, '2017-05-25 08:07:30', 127, '2017-05-25 02:37:30');

-- --------------------------------------------------------

--
-- Table structure for table `acc_user_role_options`
--

CREATE TABLE IF NOT EXISTS `acc_user_role_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) DEFAULT NULL,
  `r_section` varchar(100) DEFAULT NULL,
  `r_view` int(11) DEFAULT NULL,
  `r_insert` int(11) DEFAULT NULL,
  `r_edit` int(11) DEFAULT NULL,
  `r_delete` int(11) DEFAULT NULL,
  `r_approval` int(11) DEFAULT NULL,
  `r_export` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=31 ;

--
-- Dumping data for table `acc_user_role_options`
--

INSERT INTO `acc_user_role_options` (`id`, `role_id`, `r_section`, `r_view`, `r_insert`, `r_edit`, `r_delete`, `r_approval`, `r_export`) VALUES
(1, 1, 'S_Account_Master', 1, 1, 1, 1, 1, 1),
(2, 1, 'S_Purchase', 1, 1, 1, 1, 1, 1),
(3, 1, 'S_Journal_Voucher', 1, 1, 1, 1, 1, 1),
(4, 1, 'S_Payment_Receipt', 1, 1, 1, 1, 1, 1),
(5, 1, 'S_User_Roles', 1, 1, 1, 1, 1, 1),
(6, 1, 'S_Reports', 1, 1, 1, 1, 1, 1),
(7, 2, 'S_Account_Master', 1, 0, 0, 0, 0, 0),
(8, 2, 'S_Purchase', 1, 0, 0, 0, 0, 0),
(9, 2, 'S_Journal_Voucher', 1, 0, 0, 0, 0, 0),
(10, 2, 'S_Payment_Receipt', 1, 0, 0, 0, 0, 0),
(11, 2, 'S_User_Roles', 0, 0, 0, 0, 0, 0),
(12, 2, 'S_Reports', 1, 0, 0, 0, 0, 0),
(19, 3, 'S_Account_Master', 1, 1, 1, 1, 0, 0),
(20, 3, 'S_Purchase', 1, 1, 1, 1, 0, 0),
(21, 3, 'S_Journal_Voucher', 1, 1, 1, 1, 0, 0),
(22, 3, 'S_Payment_Receipt', 1, 1, 1, 1, 0, 0),
(23, 3, 'S_User_Roles', 1, 1, 1, 1, 1, 1),
(24, 3, 'S_Reports', 1, 0, 0, 0, 0, 0),
(25, 4, 'S_Account_Master', 1, 1, 1, 1, 1, 0),
(26, 4, 'S_Purchase', 1, 1, 1, 1, 1, 0),
(27, 4, 'S_Journal_Voucher', 1, 1, 1, 1, 1, 0),
(28, 4, 'S_Payment_Receipt', 1, 1, 1, 1, 1, 0),
(29, 4, 'S_User_Roles', 0, 0, 0, 0, 0, 0),
(30, 4, 'S_Reports', 1, 0, 0, 0, 0, 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
