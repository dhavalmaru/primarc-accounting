ALTER TABLE `acc_categories` ADD `company_id` BIGINT(11) NOT NULL DEFAULT '1' AFTER `is_active`;
ALTER TABLE `acc_categories` CHANGE `company_id` `company_id` BIGINT(11) NULL DEFAULT NULL;

ALTER TABLE `acc_category_master` ADD `company_id` BIGINT(11) NOT NULL DEFAULT '1' AFTER `approved_date`;
ALTER TABLE `acc_category_master` CHANGE `company_id` `company_id` BIGINT(11) NULL DEFAULT NULL;

ALTER TABLE `acc_email_log` ADD `company_id` BIGINT(11) NOT NULL DEFAULT '1' AFTER `error_message`;
ALTER TABLE `acc_email_log` CHANGE `company_id` `company_id` BIGINT(11) NULL DEFAULT NULL;

ALTER TABLE `acc_grn_debit_notes` ADD `company_id` BIGINT(11) NOT NULL DEFAULT '1' AFTER `debit_note_ref`;
ALTER TABLE `acc_grn_debit_notes` CHANGE `company_id` `company_id` BIGINT(11) NULL DEFAULT NULL;

ALTER TABLE `acc_grn_entries` ADD `company_id` BIGINT(11) NOT NULL DEFAULT '1' AFTER `gi_date`;
ALTER TABLE `acc_grn_entries` CHANGE `company_id` `company_id` BIGINT(11) NULL DEFAULT NULL;

ALTER TABLE `acc_grn_sku_entries` ADD `company_id` BIGINT(11) NOT NULL DEFAULT '1' AFTER `margin_diff_total`;
ALTER TABLE `acc_grn_sku_entries` CHANGE `company_id` `company_id` BIGINT(11) NULL DEFAULT NULL;

ALTER TABLE `acc_jv_details` ADD `company_id` BIGINT(11) NOT NULL DEFAULT '1' AFTER `approver_id`;
ALTER TABLE `acc_jv_details` CHANGE `company_id` `company_id` BIGINT(11) NULL DEFAULT NULL;

ALTER TABLE `acc_jv_docs` ADD `company_id` BIGINT(11) NOT NULL DEFAULT '1' AFTER `is_active`;
ALTER TABLE `acc_jv_docs` CHANGE `company_id` `company_id` BIGINT(11) NULL DEFAULT NULL;

ALTER TABLE `acc_jv_entries` ADD `company_id` BIGINT(11) NOT NULL DEFAULT '1' AFTER `is_active`;
ALTER TABLE `acc_jv_entries` CHANGE `company_id` `company_id` BIGINT(11) NULL DEFAULT NULL;

ALTER TABLE `acc_ledger_entries` ADD `company_id` BIGINT(11) NOT NULL DEFAULT '1' AFTER `ref_date`;
ALTER TABLE `acc_ledger_entries` CHANGE `company_id` `company_id` BIGINT(11) NULL DEFAULT NULL;

ALTER TABLE `acc_master` ADD `company_id` BIGINT(11) NOT NULL DEFAULT '1' AFTER `gst_id`;
ALTER TABLE `acc_master` CHANGE `company_id` `company_id` BIGINT(11) NULL DEFAULT NULL;

ALTER TABLE `acc_payment_advices` ADD `company_id` BIGINT(11) NOT NULL DEFAULT '1' AFTER `is_active`;
ALTER TABLE `acc_payment_advices` CHANGE `company_id` `company_id` BIGINT(11) NULL DEFAULT NULL;

ALTER TABLE `acc_payment_receipt` ADD `company_id` BIGINT(11) NOT NULL DEFAULT '1' AFTER `approver_id`;
ALTER TABLE `acc_payment_receipt` CHANGE `company_id` `company_id` BIGINT(11) NULL DEFAULT NULL;

ALTER TABLE `acc_series_master` ADD `company_id` BIGINT(11) NOT NULL DEFAULT '1' AFTER `series`;
ALTER TABLE `acc_series_master` CHANGE `company_id` `company_id` BIGINT(11) NULL DEFAULT NULL;

ALTER TABLE `acc_user_log` ADD `company_id` BIGINT(11) NOT NULL DEFAULT '1' AFTER `table_id`;
ALTER TABLE `acc_user_log` CHANGE `company_id` `company_id` BIGINT(11) NULL DEFAULT NULL;

ALTER TABLE `acc_user_log_history` ADD `company_id` BIGINT(11) NOT NULL DEFAULT '1' AFTER `id`;
ALTER TABLE `acc_user_log_history` CHANGE `company_id` `company_id` BIGINT(11) NULL DEFAULT NULL;

ALTER TABLE `acc_user_roles` ADD `company_id` BIGINT(11) NOT NULL DEFAULT '1' AFTER `role_id`;
ALTER TABLE `acc_user_roles` CHANGE `company_id` `company_id` BIGINT(11) NULL DEFAULT NULL;