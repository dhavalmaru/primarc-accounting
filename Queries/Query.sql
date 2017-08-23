alter table acc_grn_sku_entries add column po_cost_excl_vat float(14,2) null;
alter table acc_grn_sku_entries add column po_tax float(14,2) null;
alter table acc_grn_sku_entries add column po_total float(14,2) null;
alter table acc_jv_details add column approver_id int(11) null;
alter table acc_master add column approver_id int(11) null;
alter table acc_payment_receipt add column approver_id int(11) null;truncate table acc_email_log;


truncate table acc_grn_debit_notes;
truncate table acc_grn_entries;
truncate table acc_grn_sku_entries;
truncate table acc_jv_details;
truncate table acc_jv_docs;
truncate table acc_jv_entries;
truncate table acc_ledger_entries;
truncate table acc_payment_advices;
truncate table acc_payment_receipt;
truncate table acc_user_log;
truncate table acc_user_log_history;
update acc_series_master set series = 0 where type = 'Voucher';

drop table acc_category_master;

CREATE TABLE IF NOT EXISTS `acc_category_master` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
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
	`approved_date` datetime DEFAULT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

INSERT INTO `acc_category_master` (`id`, `category_1`, `category_2`, `category_3`, `status`, `created_by`, `updated_by`, `created_date`, `updated_date`, `approver_comments`, `approved_by`, `approved_date`) VALUES
(1, 'Cat 1', 'Cat 2', 'Cat 3', 'approved', NULL, NULL, NULL, '2017-03-15 11:03:43', NULL, NULL, NULL),
(2, 'Liability', 'Purchase A/C', '', 'approved', NULL, NULL, NULL, '2017-03-16 08:27:06', NULL, NULL, NULL);

ALTER TABLE `acc_grn_debit_notes` ADD `debit_note_path` VARCHAR(255) NULL DEFAULT NULL , ADD `debit_note_ref` VARCHAR(50) NULL DEFAULT NULL ;

ALTER TABLE `acc_grn_sku_entries`  ADD `voucher_id` VARCHAR(100) NULL DEFAULT NULL ,  ADD `ledger_type` VARCHAR(100) NULL DEFAULT NULL ,  ADD `cgst_acc_id` BIGINT(11) NULL DEFAULT NULL ,  ADD `cgst_ledger_name` VARCHAR(255) NULL DEFAULT NULL ,  ADD `cgst_ledger_code` VARCHAR(255) NULL DEFAULT NULL ,  ADD `sgst_acc_id` BIGINT(11) NULL DEFAULT NULL ,  ADD `sgst_ledger_name` VARCHAR(255) NULL DEFAULT NULL ,  ADD `sgst_ledger_code` VARCHAR(255) NULL DEFAULT NULL ,  ADD `igst_acc_id` BIGINT(11) NULL DEFAULT NULL ,  ADD `igst_ledger_name` VARCHAR(255) NULL DEFAULT NULL ,  ADD `igst_ledger_code` VARCHAR(255) NULL DEFAULT NULL ,  ADD `cgst_rate` FLOAT(14,2) NULL DEFAULT NULL ,  ADD `sgst_rate` FLOAT(14,2) NULL DEFAULT NULL ,  ADD `igst_rate` FLOAT(14,2) NULL DEFAULT NULL ,  ADD `cgst_per_unit` FLOAT(14,2) NULL DEFAULT NULL ,  ADD `sgst_per_unit` FLOAT(14,2) NULL DEFAULT NULL ,  ADD `igst_per_unit` FLOAT(14,2) NULL DEFAULT NULL ,  ADD `cgst` FLOAT(14,2) NULL DEFAULT NULL ,  ADD `sgst` FLOAT(14,2) NULL DEFAULT NULL ,  ADD `igst` FLOAT(14,2) NULL DEFAULT NULL ,  ADD `po_cgst` FLOAT(14,2) NULL DEFAULT NULL ,  ADD `po_sgst` FLOAT(14,2) NULL DEFAULT NULL ,  ADD `po_igst` FLOAT(14,2) NULL DEFAULT NULL ,  ADD `hsn_code` VARCHAR(255) NULL DEFAULT NULL ;

ALTER TABLE `acc_master` ADD `gst_id` VARCHAR(100) NULL DEFAULT NULL ;
