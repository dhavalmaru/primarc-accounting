truncate table grn_acc_entries;
truncate table grn_acc_sku_entries;
truncate table journal_voucher_details;
truncate table journal_voucher_entries;
truncate table ledger_entries;
truncate table payment_receipt_details;
truncate table payment_advices;
update series_master set series = 0 where type = 'Voucher';