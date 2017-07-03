-- phpMyAdmin SQL Dump
-- version 4.0.10.17
-- https://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 03, 2017 at 04:15 AM
-- Server version: 5.6.32
-- PHP Version: 5.4.45

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `primarc_st`
--

-- --------------------------------------------------------

--
-- Table structure for table `acc_categories`
--

CREATE TABLE IF NOT EXISTS `acc_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `acc_id` bigint(20) DEFAULT NULL,
  `category_id` bigint(11) DEFAULT NULL,
  `category_name` varchar(255) DEFAULT NULL,
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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=21 ;

-- --------------------------------------------------------

--
-- Table structure for table `acc_category_master`
--

CREATE TABLE IF NOT EXISTS `acc_category_master` (
  `id` int(11) NOT NULL,
  `category_1` varchar(255) DEFAULT NULL,
  `category_2` varchar(255) DEFAULT NULL,
  `category_3` varchar(255) DEFAULT NULL,
  `status` varchar(45) DEFAULT NULL,
  `created_by` bigint(11) DEFAULT NULL,
  `updated_by` bigint(11) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `updated_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `approver_comments` varchar(250) DEFAULT NULL,
  `approved_by` bigint(11) DEFAULT NULL,
  `approved_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `acc_grn_debit_notes`
--

CREATE TABLE IF NOT EXISTS `acc_grn_debit_notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `grn_id` int(11) DEFAULT NULL,
  `vendor_id` bigint(11) DEFAULT NULL,
  `invoice_no` varchar(255) DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `ded_type` varchar(255) DEFAULT NULL,
  `total_qty` double DEFAULT NULL,
  `total_deduction` double DEFAULT NULL,
  `status` varchar(45) DEFAULT NULL,
  `created_by` bigint(11) DEFAULT NULL,
  `updated_by` bigint(11) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `updated_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `approver_comments` varchar(250) DEFAULT NULL,
  `approved_by` bigint(11) DEFAULT NULL,
  `approved_date` datetime DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT NULL,
  `invoice_id` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `acc_grn_entries`
--

CREATE TABLE IF NOT EXISTS `acc_grn_entries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `grn_id` int(11) DEFAULT NULL,
  `vendor_id` bigint(11) DEFAULT NULL,
  `particular` varchar(255) DEFAULT NULL,
  `sub_particular` varchar(255) DEFAULT NULL,
  `acc_id` bigint(11) DEFAULT NULL,
  `ledger_name` varchar(255) DEFAULT NULL,
  `ledger_code` varchar(255) DEFAULT NULL,
  `vat_cst` enum('VAT','CST','NO TAX') DEFAULT NULL,
  `vat_percen` float(5,2) DEFAULT NULL,
  `invoice_no` varchar(255) DEFAULT NULL,
  `total_val` double DEFAULT NULL,
  `invoice_val` double DEFAULT NULL,
  `edited_val` double DEFAULT NULL,
  `difference_val` double DEFAULT NULL,
  `narration` varchar(250) DEFAULT NULL,
  `status` varchar(45) DEFAULT NULL,
  `created_by` bigint(11) DEFAULT NULL,
  `updated_by` bigint(11) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `updated_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `approver_comments` varchar(250) DEFAULT NULL,
  `approved_by` bigint(11) DEFAULT NULL,
  `approved_date` datetime DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT NULL,
  `voucher_id` varchar(100) DEFAULT NULL,
  `ledger_type` varchar(100) DEFAULT NULL,
  `gi_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=117 ;

-- --------------------------------------------------------

--
-- Table structure for table `acc_grn_sku_entries`
--

CREATE TABLE IF NOT EXISTS `acc_grn_sku_entries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `grn_id` int(11) DEFAULT NULL,
  `vendor_id` bigint(11) DEFAULT NULL,
  `ded_type` varchar(255) DEFAULT NULL,
  `cost_acc_id` bigint(11) DEFAULT NULL,
  `cost_ledger_name` varchar(255) DEFAULT NULL,
  `cost_ledger_code` varchar(255) DEFAULT NULL,
  `tax_acc_id` bigint(11) DEFAULT NULL,
  `tax_ledger_name` varchar(255) DEFAULT NULL,
  `tax_ledger_code` varchar(255) DEFAULT NULL,
  `invoice_no` varchar(255) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `ean` varchar(255) DEFAULT NULL,
  `psku` varchar(255) DEFAULT NULL,
  `product_title` varchar(255) DEFAULT NULL,
  `vat_cst` enum('VAT','CST','NO TAX') DEFAULT NULL,
  `vat_percen` float(5,2) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `box_price` float(14,2) DEFAULT NULL,
  `cost_excl_vat_per_unit` float(14,2) DEFAULT NULL,
  `tax_per_unit` float(14,2) DEFAULT NULL,
  `total_per_unit` float(14,2) DEFAULT NULL,
  `cost_excl_vat` float(14,2) DEFAULT NULL,
  `tax` float(14,2) DEFAULT NULL,
  `total` float(14,2) DEFAULT NULL,
  `status` varchar(45) DEFAULT NULL,
  `created_by` bigint(11) DEFAULT NULL,
  `updated_by` bigint(11) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `updated_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `approver_comments` varchar(250) DEFAULT NULL,
  `approved_by` bigint(11) DEFAULT NULL,
  `approved_date` datetime DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT NULL,
  `expiry_date` datetime DEFAULT NULL,
  `earliest_expected_date` datetime DEFAULT NULL,
  `remarks` varchar(1000) DEFAULT NULL,
  `po_cost_excl_vat` float(14,2) DEFAULT NULL,
  `po_tax` float(14,2) DEFAULT NULL,
  `po_total` float(14,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Table structure for table `acc_jv_details`
--

CREATE TABLE IF NOT EXISTS `acc_jv_details` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `reference` varchar(255) DEFAULT NULL,
  `narration` longtext,
  `debit_acc` varchar(255) DEFAULT NULL,
  `credit_acc` varchar(255) DEFAULT NULL,
  `debit_amt` double DEFAULT NULL,
  `credit_amt` double DEFAULT NULL,
  `diff_amt` double DEFAULT NULL,
  `doc_name` varchar(255) DEFAULT NULL,
  `doc_path` varchar(255) DEFAULT NULL,
  `status` varchar(45) DEFAULT NULL,
  `created_by` bigint(11) DEFAULT NULL,
  `updated_by` bigint(11) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `updated_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `approver_comments` varchar(250) DEFAULT NULL,
  `approved_by` bigint(11) DEFAULT NULL,
  `approved_date` datetime DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT NULL,
  `voucher_id` varchar(100) DEFAULT NULL,
  `ledger_type` varchar(100) DEFAULT NULL,
  `jv_date` date DEFAULT NULL,
  `approver_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `acc_jv_entries`
--

CREATE TABLE IF NOT EXISTS `acc_jv_entries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `jv_id` bigint(20) DEFAULT NULL,
  `account_id` bigint(11) DEFAULT NULL,
  `account_name` varchar(255) DEFAULT NULL,
  `account_code` varchar(255) DEFAULT NULL,
  `transaction` varchar(100) DEFAULT NULL,
  `debit_amt` double DEFAULT NULL,
  `credit_amt` double DEFAULT NULL,
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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Table structure for table `acc_ledger_entries`
--

CREATE TABLE IF NOT EXISTS `acc_ledger_entries` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `ref_id` int(11) DEFAULT NULL,
  `sub_ref_id` bigint(11) DEFAULT NULL,
  `ref_type` varchar(100) DEFAULT NULL,
  `entry_type` varchar(100) DEFAULT NULL,
  `invoice_no` varchar(255) DEFAULT NULL,
  `vendor_id` bigint(11) DEFAULT NULL,
  `acc_id` bigint(11) DEFAULT NULL,
  `ledger_name` varchar(255) DEFAULT NULL,
  `ledger_code` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `amount` float(14,2) DEFAULT NULL,
  `status` varchar(45) DEFAULT NULL,
  `created_by` bigint(11) DEFAULT NULL,
  `updated_by` bigint(11) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `updated_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `approver_comments` varchar(250) DEFAULT NULL,
  `approved_by` bigint(11) DEFAULT NULL,
  `approved_date` datetime DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT NULL,
  `is_paid` tinyint(1) DEFAULT NULL,
  `payment_ref` int(11) DEFAULT NULL,
  `voucher_id` varchar(100) DEFAULT NULL,
  `ledger_type` varchar(100) DEFAULT NULL,
  `narration` varchar(255) DEFAULT NULL,
  `ref_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=73 ;

-- --------------------------------------------------------

--
-- Table structure for table `acc_master`
--

CREATE TABLE IF NOT EXISTS `acc_master` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `legal_name` varchar(255) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `account_type` varchar(255) DEFAULT NULL,
  `expense_type` varchar(255) DEFAULT NULL,
  `details` varchar(255) DEFAULT NULL,
  `category_1` varchar(255) DEFAULT NULL,
  `category_2` varchar(255) DEFAULT NULL,
  `category_3` varchar(255) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `address` varchar(500) DEFAULT NULL,
  `legal_entity_name` varchar(255) DEFAULT NULL,
  `pan_no` varchar(100) DEFAULT NULL,
  `aadhar_card_no` varchar(100) DEFAULT NULL,
  `service_tax_no` varchar(100) DEFAULT NULL,
  `vat_no` varchar(100) DEFAULT NULL,
  `pf_esic_no` varchar(100) DEFAULT NULL,
  `agreement_details` varchar(255) DEFAULT NULL,
  `acc_no` varchar(20) DEFAULT NULL,
  `account_holder_name` varchar(100) DEFAULT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `branch` varchar(100) DEFAULT NULL,
  `ifsc_code` varchar(20) DEFAULT NULL,
  `other` varchar(255) DEFAULT NULL,
  `address_doc_path` varchar(255) DEFAULT NULL,
  `pan_no_doc_path` varchar(255) DEFAULT NULL,
  `aadhar_card_no_doc_path` varchar(255) DEFAULT NULL,
  `service_tax_no_doc_path` varchar(255) DEFAULT NULL,
  `vat_no_doc_path` varchar(255) DEFAULT NULL,
  `pf_esic_no_doc_path` varchar(255) DEFAULT NULL,
  `agreement_details_doc_path` varchar(255) DEFAULT NULL,
  `acc_no_doc_path` varchar(255) DEFAULT NULL,
  `other_doc_path` varchar(255) DEFAULT NULL,
  `status` varchar(45) DEFAULT NULL,
  `created_by` bigint(11) DEFAULT NULL,
  `updated_by` bigint(11) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `updated_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `approver_comments` varchar(250) DEFAULT NULL,
  `approved_by` bigint(11) DEFAULT NULL,
  `approved_date` datetime DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT NULL,
  `approver_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=46 ;

-- --------------------------------------------------------

--
-- Table structure for table `acc_payment_advices`
--

CREATE TABLE IF NOT EXISTS `acc_payment_advices` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `acc_payment_receipt`
--

CREATE TABLE IF NOT EXISTS `acc_payment_receipt` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `trans_type` varchar(255) DEFAULT NULL,
  `account_id` bigint(11) DEFAULT NULL,
  `account_name` varchar(255) DEFAULT NULL,
  `account_code` varchar(255) DEFAULT NULL,
  `bank_id` int(11) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `payment_type` varchar(255) DEFAULT NULL,
  `amount` float(14,2) DEFAULT NULL,
  `ref_no` varchar(255) DEFAULT NULL,
  `narration` varchar(255) DEFAULT NULL,
  `status` varchar(45) DEFAULT NULL,
  `created_by` bigint(11) DEFAULT NULL,
  `updated_by` bigint(11) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `updated_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `approver_comments` varchar(250) DEFAULT NULL,
  `approved_by` bigint(11) DEFAULT NULL,
  `approved_date` datetime DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT NULL,
  `is_paid` tinyint(1) DEFAULT NULL,
  `payment_ref` bigint(11) DEFAULT NULL,
  `voucher_id` varchar(100) DEFAULT NULL,
  `ledger_type` varchar(100) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `approver_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Table structure for table `acc_series_master`
--

CREATE TABLE IF NOT EXISTS `acc_series_master` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(100) DEFAULT NULL,
  `series` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=345 ;

-- --------------------------------------------------------

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=82 ;


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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `acc_user_roles`
--

INSERT INTO `acc_user_roles` (`id`, `user_id`, `role_id`, `status`, `is_active`, `created_by`, `created_date`, `updated_by`, `updated_date`) VALUES
(1, 127, 1, 'approved', 1, 127, '2017-05-24 19:43:24', 127, '2017-05-24 14:14:28'),
(2, 46, 4, 'approved', 1, 127, '2017-05-25 07:29:52', 46, '2017-06-06 11:27:39'),
(3, 21, 3, 'approved', 1, 127, '2017-05-25 07:58:20', 127, '2017-05-25 02:28:20'),
(4, 16, 4, 'approved', 1, 46, '2017-06-12 10:09:24', 46, '2017-06-12 10:09:24'),
(5, 2, 1, 'approved', 1, 46, '2017-06-12 10:10:00', 46, '2017-06-12 10:10:00'),
(6, 26, 1, 'approved', 1, 46, '2017-06-29 07:55:12', 46, '2017-06-29 07:55:12');

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
(1, 'Admin', 'Admin', 'approved', 1, 127, '2017-05-23 00:00:00', 46, '2017-06-12 10:27:23'),
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
(6, 1, 'S_Reports', 1, 0, 0, 0, 0, 0),
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
(29, 4, 'S_User_Roles', 1, 1, 1, 1, 1, 1),
(30, 4, 'S_Reports', 1, 0, 0, 0, 0, 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
