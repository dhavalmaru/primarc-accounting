alter table acc_grn_sku_entries add column po_cost_excl_vat float(14,2) null;
alter table acc_grn_sku_entries add column po_tax float(14,2) null;
alter table acc_grn_sku_entries add column po_total float(14,2) null;
alter table acc_jv_details add column approver_id int(11) null;
alter table acc_master add column approver_id int(11) null;
alter table acc_payment_receipt add column approver_id int(11) null;