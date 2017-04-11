-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Apr 10, 2017 at 05:11 PM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `primarc`
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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `grn_acc_entries`
--

CREATE TABLE IF NOT EXISTS `grn_acc_entries` (
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `grn_acc_ledger_entries`
--

CREATE TABLE IF NOT EXISTS `grn_acc_ledger_entries` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `grn_id` int(11) DEFAULT NULL,
  `invoice_no` varchar(255) DEFAULT NULL,
  `vendor_id` bigint(11) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `particular` varchar(255) DEFAULT NULL,
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `grn_acc_sku_entries`
--

CREATE TABLE IF NOT EXISTS `grn_acc_sku_entries` (
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `grn_debit_notes`
--

CREATE TABLE IF NOT EXISTS `grn_debit_notes` (
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `journal_voucher_details`
--

CREATE TABLE IF NOT EXISTS `journal_voucher_details` (
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `journal_voucher_entries`
--

CREATE TABLE IF NOT EXISTS `journal_voucher_entries` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ledger_entries`
--

CREATE TABLE IF NOT EXISTS `ledger_entries` (
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `payment_receipt_details`
--

CREATE TABLE IF NOT EXISTS `payment_receipt_details` (
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `series_master`
--

CREATE TABLE IF NOT EXISTS `series_master` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(100) DEFAULT NULL,
  `series` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
