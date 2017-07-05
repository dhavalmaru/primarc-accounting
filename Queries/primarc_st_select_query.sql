select * from account_master;
select * from company_master;
select * from customer_master;
select * from customer_warehouse_address;
select * from internal_warehouse_master;
select * from legal_entity_type_master;
select * from tax_component;
select * from tax_rate_master;
select * from tax_type_master;
select * from tax_zone_master;
select * from vendor_master;
select * from vendor_contacts;
select * from vendor_office_address;
select * from vendor_warehouse_address;
select * from goods_inward_outward_invoices where gi_go_id > 5700;
select * from grn where grn_id > 5700;
select * from grn_buckets where grn_id > 5700;
select * from grn_entries where grn_id > 5700;
select * from purchase_order where purchase_order_Id > 7400;
select * from purchase_order_items where purchase_order_Id > 7400;



alter table product_master add column hsn_code varchar(250) null after product_name;


DROP TABLE `account_master`, `company_master`, `customer_master`, `customer_warehouse_address`, `goods_inward_outward_invoices`, `grn`, `grn_buckets`, `grn_entries`, `tax_component`, `tax_rate_master`, `tax_type_master`, `tax_zone_master`, `vendor_contacts`, `vendor_master`, `vendor_warehouse_address`;

DROP TABLE `ampp_data`, `ampp_ld_uploaded_files`, `approve_level_permission`, `authorize_permission`, `auth_assignment`, `auth_item`, `auth_item_child`, `auth_rule`, `b2b_invoice_sequence_number`, `brand_master`, `bucket_movement`, `bulk_customer_order_items_temp`, `bulk_customer_order_temp`, `bulk_inventory_temp`, `bulk_purchase_order_items_temp`, `bulk_purchase_order_temp`, `category_contact`, `combo_products_relation`, `combo_sku_grn_items`, `combo_sku_transactions`, `combo_sku_transactions_combos`, `company_documents`, `company_master`, `contribution_margin_order_level`, `cron_log_master`, `customer_contacts`, `customer_documents`, `customer_office_address`, `customer_order`, `customer_order_history`, `customer_order_items`, `customer_order_sequence_number`, `customer_order_terms`, `customer_order_workflow`, `debit_credit_entries`, `debit_credit_ledger`, `debit_notes`, `delivery_challan`, `document_type_master`, `email_log`, `error_log`, `goods_inward_outward`, `goods_inward_outward_camshots`, `goods_inward_outward_documents`, `goods_inward_outward_return_items`, `go_grn_mapping`, `grn_buckets`, `grn_entries_archives`, `grn_workflow`, `handle_issues`, `imported_files`, `imported_market_place_master`, `imported_market_place_product_master`, `imported_product_category_relation`, `imported_product_images`, `imported_product_master`, `imported_vendor_contacts`, `imported_vendor_master`, `imported_vendor_office_address`, `imported_vendor_warehouse_address`, `imported_vrf_documents`, `internal_warehouse_contacts`, `internal_warehouse_master`, `inventory_master`, `invoice_tracker`, `inv_adjustment`, `inv_recived`, `inv_removal_shipment`, `inv_replacement`, `inv_shipment`, `legal_entity_type_master`, `margin_percentage_master`, `margin_type_master`, `market_fee_method`, `market_master`, `market_place_master`, `market_place_product_master`, `market_sub_type`, `menu`, `migration`, `new_settlement_master_order`, `notification_scheduler`, `order_master`, `order_master_data`, `order_master_data_new`, `order_other_data`, `order_reimbursement_master`, `order_report`, `order_return_master`, `order_shipment_master`, `organization`, `permission`, `prepare_go`, `prepare_go_items`, `prepare_go_items_temp`, `product_category_relation`, `profile`, `purchase_order`, `purchase_order_history`, `purchase_order_items`, `purchase_order_marketplaces`, `purchase_order_terms`, `purchase_order_workflow`, `report_download_history`, `report_permission_master`, `resource`, `return_file_data`, `roles`, `role_permission`, `sequence_number`, `settlement_master`, `settlement_master_data`, `settlement_master_order`, `settlement_master_order_data`, `settlement_report`, `shipment`, `shipment_items`, `sku_adjustment`, `sku_adjustment_details`, `sku_movements`, `social_account`, `table 149`, `temp_impacted_mppm`, `ticket_tracker_items`, `token`, `vendor_communication`, `vendor_communication_email_history`, `vendor_office_address`, `vendor_promotions`, `vendor_promotions_notes`, `virtual_inventory`, `virtual_inventory_archives`, `virtual_inventory_file_history`, `vp_notification`, `vp_permission_action`, `vp_permission_resource`, `vp_user`, `vp_user_list`, `vp_user_notification_rel`, `vp_user_token`, `vp_vendor_permission`, `vp_year_product_data`, `vp_year_sales_data`, `vrf_documents`, `warehouse_documents`, `warehouse_inventory`, `warehouse_inventory_valuation_files`, `warehouse_physical_location`, `wh_inv_eod_calc`, `wh_inv_eod_pan_india_level`, `wh_inv_eod_qty`, `wh_inv_eod_statewise`, `wh_inv_eod_warehouse_level`, `wh_inv_running_purchase`;