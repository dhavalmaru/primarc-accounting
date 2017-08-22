ALTER TABLE `acc_grn_entries` CHANGE `vat_cst` `vat_cst` ENUM('VAT','CST','NO TAX','INTRA','INTER') CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;

ALTER TABLE `acc_grn_sku_entries` CHANGE `vat_cst` `vat_cst` ENUM('VAT','CST','NO TAX','INTRA','INTER', 'GST') CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;