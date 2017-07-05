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

truncate table acc_grn_entries;
truncate table acc_grn_sku_entries;
truncate table acc_grn_debit_notes;
truncate table acc_jv_details;
truncate table acc_jv_entries;
truncate table acc_jv_docs;
truncate table acc_ledger_entries;
truncate table acc_payment_receipt;
truncate table acc_payment_advices;
truncate table acc_user_log;
truncate table acc_user_log_history;
update acc_series_master set series = 0 where type = 'Voucher';

update acc_categories set status = 'approved';
update acc_master set status = 'approved';



ALTER TABLE `acc_grn_sku_entries`  ADD `cgst_acc_id` BIGINT(11) NULL DEFAULT NULL ,  ADD `cgst_ledger_name` VARCHAR(255) NULL DEFAULT NULL ,  ADD `cgst_ledger_code` VARCHAR(255) NULL DEFAULT NULL ,  ADD `sgst_acc_id` BIGINT(11) NULL DEFAULT NULL ,  ADD `sgst_ledger_name` VARCHAR(255) NULL DEFAULT NULL ,  ADD `sgst_ledger_code` VARCHAR(255) NULL DEFAULT NULL ,  ADD `igst_acc_id` BIGINT(11) NULL DEFAULT NULL ,  ADD `igst_ledger_name` VARCHAR(255) NULL DEFAULT NULL ,  ADD `igst_ledger_code` VARCHAR(255) NULL DEFAULT NULL ,  ADD `cgst_rate` FLOAT(14,2) NULL DEFAULT NULL ,  ADD `sgst_rate` FLOAT(14,2) NULL DEFAULT NULL ,  ADD `igst_rate` FLOAT(14,2) NULL DEFAULT NULL ,  ADD `cgst_per_unit` FLOAT(14,2) NULL DEFAULT NULL ,  ADD `sgst_per_unit` FLOAT(14,2) NULL DEFAULT NULL ,  ADD `igst_per_unit` FLOAT(14,2) NULL DEFAULT NULL ,  ADD `cgst` FLOAT(14,2) NULL DEFAULT NULL ,  ADD `sgst` FLOAT(14,2) NULL DEFAULT NULL ,  ADD `igst` FLOAT(14,2) NULL DEFAULT NULL ,  ADD `po_cgst` FLOAT(14,2) NULL DEFAULT NULL ,  ADD `po_sgst` FLOAT(14,2) NULL DEFAULT NULL ,  ADD `po_igst` FLOAT(14,2) NULL DEFAULT NULL ;

ALTER TABLE `acc_grn_debit_notes` ADD `debit_note_ref` VARCHAR(50) NULL DEFAULT NULL ;