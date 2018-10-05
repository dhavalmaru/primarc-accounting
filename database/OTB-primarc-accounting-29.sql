CREATE TABLE IF NOT EXISTS `acc_go_entries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gi_go_id` int(11) DEFAULT NULL,
  `customer_id` bigint(11) DEFAULT NULL,
  `particular` varchar(255) DEFAULT NULL,
  `sub_particular` varchar(255) DEFAULT NULL,
  `acc_id` bigint(11) DEFAULT NULL,
  `ledger_name` varchar(255) DEFAULT NULL,
  `ledger_code` varchar(255) DEFAULT NULL,
  `vat_cst` enum('VAT','CST','NO TAX','INTRA','INTER') DEFAULT NULL,
  `vat_percen` double DEFAULT NULL,
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
  `company_id` bigint(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `acc_group_master` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `account_type` varchar(255) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
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
  `company_id` bigint(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `acc_gst_tax_type_master` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tax_name` varchar(255) DEFAULT NULL,
  `tax_details` varchar(255) DEFAULT NULL,
  `approver_id` int(11) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `created_by` bigint(11) DEFAULT NULL,
  `updated_by` bigint(11) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `updated_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `approved_by` bigint(11) DEFAULT NULL,
  `approved_date` datetime DEFAULT NULL,
  `approver_comments` text,
  `company_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `acc_jv_invoices_entries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `jv_details_id` int(11) NOT NULL,
  `jv_entries_id` int(11) NOT NULL,
  `invoice_number` varchar(250) NOT NULL,
  `invoice_date` date NOT NULL,
  `invoice_amount` double NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

ALTER TABLE  `acc_go_debit_entries` ADD  `from_state` VARCHAR(100) NULL DEFAULT NULL ,
ADD  `to_state` VARCHAR(100) NULL DEFAULT NULL ,
ADD  `entry_type` VARCHAR(100) NULL DEFAULT NULL ;

ALTER TABLE  `acc_master` ADD  `sub_account_type` int(11) NULL DEFAULT NULL ,
ADD  `customer_id` int(11) NULL DEFAULT NULL ,
ADD  `main_type` VARCHAR(255) NULL DEFAULT NULL ;
ADD  `state_id` int(11) NULL DEFAULT NULL ,
ADD  `state_type` VARCHAR(255) NULL DEFAULT NULL ;
ADD  `input_output` VARCHAR(255) NULL DEFAULT NULL ,
ADD  `gst_rate` double NULL DEFAULT NULL ;
ADD  `bus_type` VARCHAR(255) NULL DEFAULT NULL ,
ADD  `legal_name_tree` VARCHAR(255) NULL DEFAULT NULL ;
ADD  `tax_id` int(11) NULL DEFAULT NULL ,
ADD  `bill_wise` VARCHAR(100) NULL DEFAULT NULL ;