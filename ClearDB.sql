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