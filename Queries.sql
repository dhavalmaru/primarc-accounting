ALTER TABLE `acc_grn_entries` CHANGE `vat_cst` `vat_cst` ENUM('VAT','CST','NO TAX','INTRA','INTER') CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;

ALTER TABLE `acc_grn_sku_entries` CHANGE `vat_cst` `vat_cst` ENUM('VAT','CST','NO TAX','INTRA','INTER', 'GST') CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;

INSERT INTO `acc_user_role_options` (`id`, `role_id`, `r_section`, `r_view`, `r_insert`, `r_edit`, `r_delete`, `r_approval`, `r_export`) VALUES (NULL, '1', 'S_Email_Log', '1', '1', '1', '1', '1', '1');
INSERT INTO `acc_user_role_options` (`id`, `role_id`, `r_section`, `r_view`, `r_insert`, `r_edit`, `r_delete`, `r_approval`, `r_export`) VALUES (NULL, '2', 'S_Email_Log', '1', '0', '0', '0', '0', '0');
INSERT INTO `acc_user_role_options` (`id`, `role_id`, `r_section`, `r_view`, `r_insert`, `r_edit`, `r_delete`, `r_approval`, `r_export`) VALUES (NULL, '3', 'S_Email_Log', '1', '1', '1', '1', '0', '0');
INSERT INTO `acc_user_role_options` (`id`, `role_id`, `r_section`, `r_view`, `r_insert`, `r_edit`, `r_delete`, `r_approval`, `r_export`) VALUES (NULL, '4', 'S_Email_Log', '1', '1', '1', '1', '1', '0');