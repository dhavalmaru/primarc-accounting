

-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 29, 2018 at 06:58 AM
-- Server version: 5.7.14
-- PHP Version: 7.0.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `primarc`
--

-- --------------------------------------------------------

--
-- Table structure for table `acc_go_debit_details`
--

CREATE TABLE `acc_go_debit_details` (
  `id` bigint(20) NOT NULL,
  `gi_go_id` bigint(20) DEFAULT NULL,
  `debit_acc` varchar(1000) DEFAULT NULL,
  `credit_acc` varchar(1000) DEFAULT NULL,
  `debit_amt` double DEFAULT NULL,
  `credit_amt` double DEFAULT NULL,
  `diff_amt` double DEFAULT NULL,
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
  `date_of_transaction` date DEFAULT NULL,
  `approver_id` int(11) DEFAULT NULL,
  `company_id` bigint(11) DEFAULT NULL,
  `debit_note_ref` varchar(255) DEFAULT NULL,
  `debit_note_path` varchar(1000) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `acc_go_debit_entries`
--

CREATE TABLE `acc_go_debit_entries` (
  `id` int(11) NOT NULL,
  `gi_go_id` bigint(20) DEFAULT NULL,
  `acc_id` bigint(11) DEFAULT NULL,
  `acc_type` varchar(255) DEFAULT NULL,
  `ledger_type` varchar(255) DEFAULT NULL,
  `ledger_name` varchar(255) DEFAULT NULL,
  `ledger_code` varchar(255) DEFAULT NULL,
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
  `company_id` bigint(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `acc_other_debit_credit_details`
--

CREATE TABLE `acc_other_debit_credit_details` (
  `id` bigint(20) NOT NULL,
  `date_of_transaction` date DEFAULT NULL,
  `trans_type` varchar(255) DEFAULT NULL,
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
  `approver_id` int(11) DEFAULT NULL,
  `company_id` bigint(11) DEFAULT NULL,
  `debit_credit_note_ref` varchar(255) DEFAULT NULL,
  `debit_credit_note_path` varchar(1000) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `acc_other_debit_credit_entries`
--

CREATE TABLE `acc_other_debit_credit_entries` (
  `id` int(11) NOT NULL,
  `other_debit_credit_id` bigint(20) DEFAULT NULL,
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
  `company_id` bigint(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `acc_promotion_details`
--

CREATE TABLE `acc_promotion_details` (
  `id` bigint(20) NOT NULL,
  `vendor_id` bigint(11) DEFAULT NULL,
  `promotion_type` varchar(255) DEFAULT NULL,
  `promotion_code` varchar(1000) DEFAULT NULL,
  `date_of_transaction` date DEFAULT NULL,
  `trans_type` varchar(255) DEFAULT NULL,
  `warehouse_id` bigint(11) DEFAULT NULL,
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
  `approver_id` int(11) DEFAULT NULL,
  `company_id` bigint(11) DEFAULT NULL,
  `debit_credit_note_ref` varchar(255) DEFAULT NULL,
  `debit_credit_note_path` varchar(1000) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `acc_promotion_entries`
--

CREATE TABLE `acc_promotion_entries` (
  `id` int(11) NOT NULL,
  `other_debit_credit_id` bigint(20) DEFAULT NULL,
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
  `company_id` bigint(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


ALTER TABLE  `acc_grn_debit_notes` ADD  `total_without_tax` DOUBLE NULL DEFAULT NULL AFTER  `total_qty` ,
ADD  `total_cgst` DOUBLE NULL DEFAULT NULL AFTER  `total_without_tax` ,
ADD  `total_sgst` DOUBLE NULL DEFAULT NULL AFTER  `total_cgst` ,
ADD  `total_igst` DOUBLE NULL DEFAULT NULL AFTER  `total_sgst` ,
ADD  `total_tax` DOUBLE NULL DEFAULT NULL AFTER  `total_igst`;


--
-- Indexes for dumped tables
--

--
-- Indexes for table `acc_go_debit_details`
--
ALTER TABLE `acc_go_debit_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `acc_go_debit_entries`
--
ALTER TABLE `acc_go_debit_entries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `acc_other_debit_credit_details`
--
ALTER TABLE `acc_other_debit_credit_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `acc_other_debit_credit_entries`
--
ALTER TABLE `acc_other_debit_credit_entries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `acc_promotion_details`
--
ALTER TABLE `acc_promotion_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `acc_promotion_entries`
--
ALTER TABLE `acc_promotion_entries`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `acc_go_debit_details`
--
ALTER TABLE `acc_go_debit_details`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `acc_go_debit_entries`
--
ALTER TABLE `acc_go_debit_entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `acc_other_debit_credit_details`
--
ALTER TABLE `acc_other_debit_credit_details`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `acc_other_debit_credit_entries`
--
ALTER TABLE `acc_other_debit_credit_entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `acc_promotion_details`
--
ALTER TABLE `acc_promotion_details`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `acc_promotion_entries`
--
ALTER TABLE `acc_promotion_entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
