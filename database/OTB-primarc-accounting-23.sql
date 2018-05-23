ALTER TABLE `acc_grn_sku_entries` ADD `po_mrp` FLOAT(0) NULL DEFAULT NULL
AFTER `hsn_code`, ADD `margin_diff_excl_tax` FLOAT(0) NULL DEFAULT NULL AFTER
`po_mrp`, ADD `margin_diff_cgst` FLOAT(0) NULL DEFAULT NULL AFTER
`margin_diff_excl_tax`, ADD `margin_diff_sgst` FLOAT(0) NULL DEFAULT NULL
AFTER `margin_diff_cgst`, ADD `margin_diff_igst` FLOAT(0) NULL DEFAULT NULL
AFTER `margin_diff_sgst`, ADD `margin_diff_tax` FLOAT(0) NULL DEFAULT NULL
AFTER `margin_diff_igst`, ADD `margin_diff_total` FLOAT(0) NULL DEFAULT NULL
AFTER `margin_diff_tax`;
