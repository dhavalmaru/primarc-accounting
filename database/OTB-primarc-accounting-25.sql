ALTER TABLE  `acc_grn_sku_entries` ADD  `po_mrp` DOUBLE NULL DEFAULT NULL ,
ADD  `margin_diff_excl_tax` DOUBLE NULL DEFAULT NULL ,
ADD  `margin_diff_cgst` DOUBLE NULL DEFAULT NULL ,
ADD  `margin_diff_sgst` DOUBLE NULL DEFAULT NULL ,
ADD  `margin_diff_igst` DOUBLE NULL DEFAULT NULL ,
ADD  `margin_diff_tax` DOUBLE NULL DEFAULT NULL ,
ADD  `margin_diff_total` DOUBLE NULL DEFAULT NULL ;