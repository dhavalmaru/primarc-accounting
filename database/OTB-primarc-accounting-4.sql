alter table ledger_entries add column voucher_id varchar(100), add column ledger_type varchar(100);
alter table grn_acc_entries add column voucher_id varchar(100), add column ledger_type varchar(100);
alter table payment_receipt_details add column voucher_id varchar(100), add column ledger_type varchar(100);
alter table journal_voucher_details add column voucher_id varchar(100), add column ledger_type varchar(100);