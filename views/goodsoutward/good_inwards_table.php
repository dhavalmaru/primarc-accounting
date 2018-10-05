<?php for($j=0; $j<count($total_tax); $j++) { ?>

                    <?php 
                        $inv_num = 0; 
                        $invoice_cost_td = ''; 
                        $invoice_tax_td = ''; 
                        $invoice_cgst_td = ''; 
                        $invoice_sgst_td = ''; 
                        $invoice_igst_td = '';

                        for($k=0; $k<count($invoice_details); $k++) { 
                            $bl_invoice=false;
                            for($i=0; $i<count($invoice_tax); $i++) { 
                            if($invoice_details[$k]['invoice_number']==$invoice_tax[$i]['invoice_number']) {
                                if($total_tax[$j]['tax_zone_code']==$invoice_tax[$i]['tax_zone_code'] && 
                                    // $total_tax[$j]['vat_cst'] == $invoice_tax[$i]['vat_cst'] && 
                                    floatval($total_tax[$j]['vat_percent']) == floatval($invoice_tax[$i]['vat_percent'])) {

                                    $total_tax[$j]['invoice_cost_acc_id']=$invoice_tax[$i]['invoice_cost_acc_id'];
                                    $total_tax[$j]['invoice_cost_ledger_name']=$invoice_tax[$i]['invoice_cost_ledger_name'];
                                    $total_tax[$j]['invoice_cost_ledger_code']=$invoice_tax[$i]['invoice_cost_ledger_code'];
                                    $total_tax[$j]['invoice_tax_acc_id']=$invoice_tax[$i]['invoice_tax_acc_id'];
                                    $total_tax[$j]['invoice_tax_ledger_name']=$invoice_tax[$i]['invoice_tax_ledger_name'];
                                    $total_tax[$j]['invoice_tax_ledger_code']=$invoice_tax[$i]['invoice_tax_ledger_code'];
                                    $total_tax[$j]['invoice_cgst_acc_id']=$invoice_tax[$i]['invoice_cgst_acc_id'];
                                    $total_tax[$j]['invoice_cgst_ledger_name']=$invoice_tax[$i]['invoice_cgst_ledger_name'];
                                    $total_tax[$j]['invoice_cgst_ledger_code']=$invoice_tax[$i]['invoice_cgst_ledger_code'];
                                    $total_tax[$j]['invoice_sgst_acc_id']=$invoice_tax[$i]['invoice_sgst_acc_id'];
                                    $total_tax[$j]['invoice_sgst_ledger_name']=$invoice_tax[$i]['invoice_sgst_ledger_name'];
                                    $total_tax[$j]['invoice_sgst_ledger_code']=$invoice_tax[$i]['invoice_sgst_ledger_code'];
                                    $total_tax[$j]['invoice_igst_acc_id']=$invoice_tax[$i]['invoice_igst_acc_id'];
                                    $total_tax[$j]['invoice_igst_ledger_name']=$invoice_tax[$i]['invoice_igst_ledger_name'];
                                    $total_tax[$j]['invoice_igst_ledger_code']=$invoice_tax[$i]['invoice_igst_ledger_code'];

                                    $td = '<td>
                                                <input type="text" class="text-right" id="invoice_'.$k.'_cost_'.$j.'" name="invoice_cost_'.$j.'[]" value="'.$mycomponent->format_money($invoice_tax[$i]['invoice_cost'], 2).'" readonly />
                                                <input type="hidden" id="invoice_'.$k.'_cost_voucher_id_'.$j.'" name="invoice_cost_voucher_id_'.$j.'[]" value="" />
                                                <input type="hidden" id="invoice_'.$k.'_cost_ledger_type_'.$j.'" name="invoice_cost_ledger_type_'.$j.'[]" value="Sub Entry" />
                                            </td>
                                            <td>
                                                <input type="text" class="text-right edited-cost edit-text" id="edited_'.$k.'_cost_'.$j.'" name="edited_cost_'.$j.'[]" value="'.$mycomponent->format_money($invoice_tax[$i]['edited_cost'], 2).'" onChange="getDifference(this);" />
                                            </td>
                                            <td>
                                                <input type="text" class="text-right diff" id="diff_'.$k.'_cost_'.$j.'" name="diff_cost_'.$j.'[]" value="'.$mycomponent->format_money($invoice_tax[$i]['diff_cost'], 2).'" readonly />
                                            </td>';
                                    $invoice_cost_td = $invoice_cost_td . $td;

                                    $td = '<td>
                                                <input type="text" class="text-right" id="invoice_'.$k.'_tax_'.$j.'" name="invoice_tax_'.$j.'[]" value="'.$mycomponent->format_money($invoice_tax[$i]['invoice_tax'], 2).'" readonly />
                                                <input type="hidden" id="invoice_'.$k.'_tax_voucher_id_'.$j.'" name="invoice_tax_voucher_id_'.$j.'[]" value="" />
                                                <input type="hidden" id="invoice_'.$k.'_tax_ledger_type_'.$j.'" name="invoice_tax_ledger_type_'.$j.'[]" value="Sub Entry" />
                                            </td>
                                            <td style="display: none;">
                                                <input type="text" class="text-right" id="edited_'.$k.'_tax_'.$j.'" name="edited_tax_'.$j.'[]" value="'.$mycomponent->format_money($invoice_tax[$i]['edited_tax'], 2).'" onChange="getDifference(this);" readonly />
                                            </td>
                                            <td style="display: none;">
                                                <input type="text" class="text-right " id="diff_'.$k.'_tax_'.$j.'" name="diff_tax_'.$j.'[]" value="'.$mycomponent->format_money($invoice_tax[$i]['diff_tax'], 2).'" readonly />
                                            </td>';
                                    $invoice_tax_td = $invoice_tax_td . $td;

                                    $td = '<td>
                                                <input type="text" class="text-right" id="invoice_'.$k.'_cgst_'.$j.'" name="invoice_cgst_'.$j.'[]" value="'.$mycomponent->format_money($invoice_tax[$i]['invoice_cgst'], 2).'" readonly />
                                                <input type="hidden" id="invoice_'.$k.'_cgst_voucher_id_'.$j.'" name="invoice_cgst_voucher_id_'.$j.'[]" value="" />
                                                <input type="hidden" id="invoice_'.$k.'_cgst_ledger_type_'.$j.'" name="invoice_cgst_ledger_type_'.$j.'[]" value="Sub Entry" />
                                            </td>
                                            <td>
                                                <input type="text" class="text-right" id="edited_'.$k.'_cgst_'.$j.'" name="edited_cgst_'.$j.'[]" value="'.$mycomponent->format_money($invoice_tax[$i]['edited_cgst'], 2).'" onChange="getDifference(this);" readonly />
                                            </td>
                                            <td>
                                                <input type="text" class="text-right diff" id="diff_'.$k.'_cgst_'.$j.'" name="diff_cgst_'.$j.'[]" value="'.$mycomponent->format_money($invoice_tax[$i]['diff_cgst'], 2).'" readonly />
                                            </td>';
                                    $invoice_cgst_td = $invoice_cgst_td . $td;

                                    $td = '<td>
                                                <input type="text" class="text-right" id="invoice_'.$k.'_sgst_'.$j.'" name="invoice_sgst_'.$j.'[]" value="'.$mycomponent->format_money($invoice_tax[$i]['invoice_sgst'], 2).'" readonly />
                                                <input type="hidden" id="invoice_'.$k.'_sgst_voucher_id_'.$j.'" name="invoice_sgst_voucher_id_'.$j.'[]" value="" />
                                                <input type="hidden" id="invoice_'.$k.'_sgst_ledger_type_'.$j.'" name="invoice_sgst_ledger_type_'.$j.'[]" value="Sub Entry" />
                                            </td>
                                            <td>
                                                <input type="text" class="text-right" id="edited_'.$k.'_sgst_'.$j.'" name="edited_sgst_'.$j.'[]" value="'.$mycomponent->format_money($invoice_tax[$i]['edited_sgst'], 2).'" onChange="getDifference(this);" readonly />
                                            </td>
                                            <td>
                                                <input type="text" class="text-right diff" id="diff_'.$k.'_sgst_'.$j.'" name="diff_sgst_'.$j.'[]" value="'.$mycomponent->format_money($invoice_tax[$i]['diff_sgst'], 2).'" readonly />
                                            </td>';
                                    $invoice_sgst_td = $invoice_sgst_td . $td;

                                    $td = '<td>
                                                <input type="text" class="text-right" id="invoice_'.$k.'_igst_'.$j.'" name="invoice_igst_'.$j.'[]" value="'.$mycomponent->format_money($invoice_tax[$i]['invoice_igst'], 2).'" readonly />
                                                <input type="hidden" id="invoice_'.$k.'_igst_voucher_id_'.$j.'" name="invoice_igst_voucher_id_'.$j.'[]" value="" />
                                                <input type="hidden" id="invoice_'.$k.'_igst_ledger_type_'.$j.'" name="invoice_igst_ledger_type_'.$j.'[]" value="Sub Entry" />
                                            </td>
                                            <td>
                                                <input type="text" class="text-right" id="edited_'.$k.'_igst_'.$j.'" name="edited_igst_'.$j.'[]" value="'.$mycomponent->format_money($invoice_tax[$i]['edited_igst'], 2).'" onChange="getDifference(this);" readonly />
                                            </td>
                                            <td>
                                                <input type="text" class="text-right diff" id="diff_'.$k.'_igst_'.$j.'" name="diff_igst_'.$j.'[]" value="'.$mycomponent->format_money($invoice_tax[$i]['diff_igst'], 2).'" readonly />
                                            </td>';
                                    $invoice_igst_td = $invoice_igst_td . $td;

                                    $bl_invoice=true; $inv_num = $inv_num + 1;
                                }
                            }
                            }
                            if($bl_invoice==false) {
                                $td = '<td>
                                            <input type="text" class="text-right" id="invoice_'.$inv_num.'_cost_'.$j.'" name="invoice_cost_'.$j.'[]" value="0.00" readonly />
                                            <input type="hidden" id="invoice_'.$k.'_cost_voucher_id_'.$j.'" name="invoice_cost_voucher_id_'.$j.'[]" value="" />
                                            <input type="hidden" id="invoice_'.$k.'_cost_ledger_type_'.$j.'" name="invoice_cost_ledger_type_'.$j.'[]" value="Sub Entry" />
                                        </td>
                                        <td>
                                            <input type="text" class="text-right edit-text" id="edited_'.$inv_num.'_cost_'.$j.'" name="edited_cost_'.$j.'[]" value="0.00" onChange="getDifference(this);" />
                                        </td>
                                        <td>
                                            <input type="text" class="text-right diff" id="diff_'.$inv_num.'_cost_'.$j.'" name="diff_cost_'.$j.'[]" value="0.00" readonly />
                                        </td>';
                                $invoice_cost_td = $invoice_cost_td . $td;

                                $td = '<td>
                                            <input type="text" class="text-right" id="invoice_'.$inv_num.'_tax_'.$j.'" name="invoice_tax_'.$j.'[]" value="0.00" readonly />
                                            <input type="hidden" id="invoice_'.$k.'_tax_voucher_id_'.$j.'" name="invoice_tax_voucher_id_'.$j.'[]" value="" />
                                            <input type="hidden" id="invoice_'.$k.'_tax_ledger_type_'.$j.'" name="invoice_tax_ledger_type_'.$j.'[]" value="Sub Entry" />
                                        </td>
                                        <td style="display: none;">
                                            <input type="text" class="text-right" id="edited_'.$inv_num.'_tax_'.$j.'" name="edited_tax_'.$j.'[]" value="0.00" onChange="getDifference(this);" readonly />
                                        </td>
                                        <td style="display: none;">
                                            <input type="text" class="text-right " id="diff_'.$inv_num.'_tax_'.$j.'" name="diff_tax_'.$j.'[]" value="0.00" readonly />
                                        </td>';
                                $invoice_tax_td = $invoice_tax_td . $td;
                                
                                $td = '<td>
                                            <input type="text" class="text-right" id="invoice_'.$inv_num.'_cgst_'.$j.'" name="invoice_cgst_'.$j.'[]" value="0.00" readonly />
                                            <input type="hidden" id="invoice_'.$k.'_cgst_voucher_id_'.$j.'" name="invoice_cgst_voucher_id_'.$j.'[]" value="" />
                                            <input type="hidden" id="invoice_'.$k.'_cgst_ledger_type_'.$j.'" name="invoice_cgst_ledger_type_'.$j.'[]" value="Sub Entry" />
                                        </td>
                                        <td>
                                            <input type="text" class="text-right" id="edited_'.$inv_num.'_cgst_'.$j.'" name="edited_cgst_'.$j.'[]" value="0.00" onChange="getDifference(this);" readonly />
                                        </td>
                                        <td>
                                            <input type="text" class="text-right diff" id="diff_'.$inv_num.'_cgst_'.$j.'" name="diff_cgst_'.$j.'[]" value="0.00" readonly />
                                        </td>';
                                $invoice_cgst_td = $invoice_cgst_td . $td;
                                
                                $td = '<td>
                                            <input type="text" class="text-right" id="invoice_'.$inv_num.'_sgst_'.$j.'" name="invoice_sgst_'.$j.'[]" value="0.00" readonly />
                                            <input type="hidden" id="invoice_'.$k.'_sgst_voucher_id_'.$j.'" name="invoice_sgst_voucher_id_'.$j.'[]" value="" />
                                            <input type="hidden" id="invoice_'.$k.'_sgst_ledger_type_'.$j.'" name="invoice_sgst_ledger_type_'.$j.'[]" value="Sub Entry" />
                                        </td>
                                        <td>
                                            <input type="text" class="text-right" id="edited_'.$inv_num.'_sgst_'.$j.'" name="edited_sgst_'.$j.'[]" value="0.00" onChange="getDifference(this);" readonly />
                                        </td>
                                        <td>
                                            <input type="text" class="text-right diff" id="diff_'.$inv_num.'_sgst_'.$j.'" name="diff_sgst_'.$j.'[]" value="0.00" readonly />
                                        </td>';
                                $invoice_sgst_td = $invoice_sgst_td . $td;
                                
                                $td = '<td>
                                            <input type="text" class="text-right" id="invoice_'.$inv_num.'_igst_'.$j.'" name="invoice_igst_'.$j.'[]" value="0.00" readonly />
                                            <input type="hidden" id="invoice_'.$k.'_igst_voucher_id_'.$j.'" name="invoice_igst_voucher_id_'.$j.'[]" value="" />
                                            <input type="hidden" id="invoice_'.$k.'_igst_ledger_type_'.$j.'" name="invoice_igst_ledger_type_'.$j.'[]" value="Sub Entry" />
                                        </td>
                                        <td>
                                            <input type="text" class="text-right" id="edited_'.$inv_num.'_igst_'.$j.'" name="edited_igst_'.$j.'[]" value="0.00" onChange="getDifference(this);" readonly />
                                        </td>
                                        <td>
                                            <input type="text" class="text-right diff" id="diff_'.$inv_num.'_igst_'.$j.'" name="diff_igst_'.$j.'[]" value="0.00" readonly />
                                        </td>';
                                $invoice_igst_td = $invoice_igst_td . $td;
                                
                                $inv_num = $inv_num + 1; 
                            }
                        }

                        $total_tax[$j]['invoice_cost_td']=$invoice_cost_td;
                        $total_tax[$j]['invoice_tax_td']=$invoice_tax_td;
                        $total_tax[$j]['invoice_cgst_td']=$invoice_cgst_td;
                        $total_tax[$j]['invoice_sgst_td']=$invoice_sgst_td;
                        $total_tax[$j]['invoice_igst_td']=$invoice_igst_td;
                    ?>

                <tr>
                    <td class="sticky-cell" style="border: none!important;"><?php echo '1.'.($j+1); ?></td>
                    <td class="sticky-cell" style="border: none!important;">Taxable Amount</td>
                    <td class="sticky-cell" style="border: none!important;">
                        <select id="invoicecost_acc_id_<?php echo $j;?>" class="form-control acc_id" name="invoice_cost_acc_id[]" onChange="get_acc_details(this)">
                            <option value="">Select</option>
                            <?php for($i=0; $i<count($acc_master); $i++) { 
                                    if($acc_master[$i]['type']=="Goods Purchase") { 
                            ?>
                            <option value="<?php echo $acc_master[$i]['id']; ?>" <?php if($total_tax[$j]['invoice_cost_acc_id']==$acc_master[$i]['id']) echo 'selected'; ?>><?php echo $acc_master[$i]['legal_name']; ?></option>
                            <?php }} ?>
                        </select>
                        <input type="hidden" id="invoicecost_ledger_name_<?php echo $j;?>" name="invoice_cost_ledger_name[]" value="<?php echo $total_tax[$j]['invoice_cost_ledger_name']; ?>" />
                    </td>
                    <td class="sticky-cell" style="border: none!important;"><input type="text" id="invoicecost_ledger_code_<?php echo $j;?>" name="invoice_cost_ledger_code[]" value="<?php echo $total_tax[$j]['invoice_cost_ledger_code']; ?>" style="border: none;" readonly /></td>
                    <td class="sticky-cell" style="border: none!important;">
                        <input type="hidden" id="vat_cst_<?php echo $j;?>" name="vat_cst[]" value="<?php echo $total_tax[$j]['vat_cst']; ?>" />
                        <input type="hidden" id="vat_percen_<?php echo $j;?>" name="vat_percen[]" value="<?php echo $total_tax[$j]['vat_percent']; ?>" />
                        <input type="hidden" id="cgst_rate_<?php echo $j;?>" name="cgst_rate[]" value="<?php echo $total_tax[$j]['cgst_rate']; ?>" />
                        <input type="hidden" id="sgst_rate_<?php echo $j;?>" name="sgst_rate[]" value="<?php echo $total_tax[$j]['sgst_rate']; ?>" />
                        <input type="hidden" id="igst_rate_<?php echo $j;?>" name="igst_rate[]" value="<?php echo $total_tax[$j]['igst_rate']; ?>" />
                        <input type="hidden" id="sub_particular_cost_<?php echo $j;?>" name="sub_particular_cost[]" value="<?php echo 'Purchase_'.$total_tax[$j]['tax_zone_code'].'_'.$total_tax[$j]['vat_cst'].'_'.$total_tax[$j]['vat_percent']; ?>" />
                        <?php //echo 'Purchase_'.$total_tax[$j]['tax_zone_code'].'_'.$total_tax[$j]['vat_cst'].'_'.$total_tax[$j]['vat_percen']; ?>
                        <?php echo $mycomponent->format_money($total_tax[$j]['vat_percent'],2); ?>
                    </td>
                    <td class="sticky-cell text-right" style="border: none!important;">
                        <input type="text" class="text-right" id="total_cost_<?php echo $j;?>" name="total_cost_<?php echo $j;?>" value="<?php echo $mycomponent->format_money($total_tax[$j]['total_cost'], 2); ?>" readonly />
                    </td>
                    <?php echo $total_tax[$j]['invoice_cost_td']; ?>
                    <td>
                        <input type="text" id="narration_cost_<?php echo $j;?>" name="narration_cost_<?php echo $j;?>" value="<?php echo $narration[$j]['cost']; ?>" class="narration"/>
                    </td>
                </tr>
                <tr style="display: none;">
                    <td class="sticky-cell" style="border: none!important;"><?php echo '2.'.($j+1); ?></td>
                    <td class="sticky-cell" style="border: none!important;">Tax</td>
                    <td class="sticky-cell" style="border: none!important;">
                        <select id="invoicetax_acc_id_<?php echo $j;?>" class="form-control acc_id" name="invoice_tax_acc_id[]" onChange="get_acc_details(this)">
                            <option value="">Select</option>
                            <?php for($i=0; $i<count($acc_master); $i++) { 
                                    if($acc_master[$i]['type']=="Tax") { 
                            ?>
                            <option value="<?php echo $acc_master[$i]['id']; ?>" <?php if($total_tax[$j]['invoice_tax_acc_id']==$acc_master[$i]['id']) echo 'selected'; ?>><?php echo $acc_master[$i]['legal_name']; ?></option>
                            <?php }} ?>
                        </select>
                        <input type="hidden" id="invoicetax_ledger_name_<?php echo $j;?>" name="invoice_tax_ledger_name[]" value="<?php echo $total_tax[$j]['invoice_tax_ledger_name']; ?>" />
                    </td>
                    <td class="sticky-cell" style="border: none!important;"><input type="text" id="invoicetax_ledger_code_<?php echo $j;?>" name="invoice_tax_ledger_code[]" value="<?php echo $total_tax[$j]['invoice_tax_ledger_code']; ?>" style="border: none;" readonly /></td>
                    <td class="sticky-cell" style="border: none!important;">
                        <!-- <input type="hidden" id="vat_cst_<?php //echo $j;?>" name="vat_cst[]" value="<?php //echo $total_tax[$j]['vat_cst']; ?>" />
                        <input type="hidden" id="vat_percen_<?php //echo $j;?>" name="vat_percen[]" value="<?php //echo $total_tax[$j]['vat_percen']; ?>" /> -->
                        <input type="hidden" id="sub_particular_tax_<?php echo $j;?>" name="sub_particular_tax[]" value="<?php echo 'Tax_'.$total_tax[$j]['tax_zone_code'].'_'.$total_tax[$j]['vat_percent']; ?>" />
                        <?php //echo 'Tax_'.$total_tax[$j]['tax_zone_code'].'_'.$total_tax[$j]['vat_cst'].'_'.$total_tax[$j]['vat_percen']; ?>
                        <?php echo $mycomponent->format_money($total_tax[$j]['vat_percent'],2); ?>
                    </td>
                    <td class="sticky-cell text-right" style="border: none!important;">
                        <input type="text" class="text-right " id="total_tax_<?php echo $j;?>" name="total_tax_<?php echo $j;?>" value="<?php echo $mycomponent->format_money($total_tax[$j]['total_tax'], 2); ?>" readonly />
                    </td>
                    <?php echo $total_tax[$j]['invoice_tax_td']; ?>
                    <td>
                        <input type="text" id="narration_tax_<?php echo $j;?>" name="narration_tax_<?php echo $j;?>" value="<?php echo $narration[$j]['tax']; ?>" class="narration"/>
                    </td>
                </tr>
                <tr style="<?php echo $intra_state_style; ?>">
                    <td class="sticky-cell" style="border: none!important;"><?php echo '2.'.($j+1); ?></td>
                    <td class="sticky-cell" style="border: none!important;">CGST</td>
                    <td class="sticky-cell" style="border: none!important;">
                        <select id="invoicecgst_acc_id_<?php echo $j;?>" class="form-control acc_id" name="invoice_cgst_acc_id[]" onChange="get_acc_details(this)">
                            <option value="">Select</option>
                            <?php for($i=0; $i<count($acc_master); $i++) { 
                                    if($acc_master[$i]['type']=="CGST") { 
                            ?>
                            <option value="<?php echo $acc_master[$i]['id']; ?>" <?php if($total_tax[$j]['invoice_cgst_acc_id']==$acc_master[$i]['id']) echo 'selected'; ?>><?php echo $acc_master[$i]['legal_name']; ?></option>
                            <?php }} ?>
                        </select>
                        <input type="hidden" id="invoicecgst_ledger_name_<?php echo $j;?>" name="invoice_cgst_ledger_name[]" value="<?php echo $total_tax[$j]['invoice_cgst_ledger_name']; ?>" />
                    </td>
                    <td class="sticky-cell" style="border: none!important;"><input type="text" id="invoicecgst_ledger_code_<?php echo $j;?>" name="invoice_cgst_ledger_code[]" value="<?php echo $total_tax[$j]['invoice_cgst_ledger_code']; ?>" style="border: none;" readonly /></td>
                    <td class="sticky-cell" style="border: none!important;">
                        <!-- <input type="hidden" id="vat_cst_<?php //echo $j;?>" name="vat_cst[]" value="<?php //echo $total_tax[$j]['vat_cst']; ?>" />
                        <input type="hidden" id="vat_percen_<?php //echo $j;?>" name="vat_percen[]" value="<?php //echo $total_tax[$j]['cgst']; ?>" /> -->
                        <input type="hidden" id="sub_particular_cgst_<?php echo $j;?>" name="sub_particular_cgst[]" value="<?php echo 'Tax_cgst_'.$total_tax[$j]['cgst_rate']; ?>" />
                        <?php //echo 'Tax_cgst_'.$total_tax[$j]['cgst']; ?>
                        <?php echo $mycomponent->format_money($total_tax[$j]['cgst_rate'],2); ?>
                    </td>
                    <td class="sticky-cell text-right" style="border: none!important;">
                        <input type="text" class="text-right " id="total_cgst_<?php echo $j;?>" name="total_cgst_<?php echo $j;?>" value="<?php echo $mycomponent->format_money($total_tax[$j]['total_cgst'], 2); ?>" readonly />
                    </td>
                    <?php echo $total_tax[$j]['invoice_cgst_td']; ?>
                    <td>
                        <input type="text" id="narration_cgst_<?php echo $j;?>" name="narration_cgst_<?php echo $j;?>" value="<?php echo $narration[$j]['cgst']; ?>" class="narration"/>
                    </td>
                </tr>
                <tr style="<?php echo $intra_state_style; ?>">
                    <td class="sticky-cell" style="border: none!important;"><?php echo '2.'.($j+1); ?></td>
                    <td class="sticky-cell" style="border: none!important;">SGST</td>
                    <td class="sticky-cell" style="border: none!important;">
                        <select id="invoicesgst_acc_id_<?php echo $j;?>" class="form-control acc_id" name="invoice_sgst_acc_id[]" onChange="get_acc_details(this)">
                            <option value="">Select</option>
                            <?php for($i=0; $i<count($acc_master); $i++) { 
                                    if($acc_master[$i]['type']=="SGST") { 
                            ?>
                            <option value="<?php echo $acc_master[$i]['id']; ?>" <?php if($total_tax[$j]['invoice_sgst_acc_id']==$acc_master[$i]['id']) echo 'selected'; ?>><?php echo $acc_master[$i]['legal_name']; ?></option>
                            <?php }} ?>
                        </select>
                        <input type="hidden" id="invoicesgst_ledger_name_<?php echo $j;?>" name="invoice_sgst_ledger_name[]" value="<?php echo $total_tax[$j]['invoice_sgst_ledger_name']; ?>" />
                    </td>
                    <td class="sticky-cell" style="border: none!important;"><input type="text" id="invoicesgst_ledger_code_<?php echo $j;?>" name="invoice_sgst_ledger_code[]" value="<?php echo $total_tax[$j]['invoice_sgst_ledger_code']; ?>" style="border: none;" readonly /></td>
                    <td class="sticky-cell" style="border: none!important;">
                        <!-- <input type="hidden" id="vat_cst_<?php //echo $j;?>" name="vat_cst[]" value="<?php //echo $total_tax[$j]['vat_cst']; ?>" />
                        <input type="hidden" id="vat_percen_<?php //echo $j;?>" name="vat_percen[]" value="<?php //echo $total_tax[$j]['sgst']; ?>" /> -->
                        <input type="hidden" id="sub_particular_sgst_<?php echo $j;?>" name="sub_particular_sgst[]" value="<?php echo 'Tax_sgst_'.$total_tax[$j]['sgst_rate']; ?>" />
                        <?php //echo 'Tax_sgst_'.$total_tax[$j]['sgst']; ?>
                        <?php echo $mycomponent->format_money($total_tax[$j]['sgst_rate'],2); ?>
                    </td>
                    <td class="sticky-cell text-right" style="border: none!important;">
                        <input type="text" class="text-right " id="total_sgst_<?php echo $j;?>" name="total_sgst_<?php echo $j;?>" value="<?php echo $mycomponent->format_money($total_tax[$j]['total_sgst'], 2); ?>" readonly />
                    </td>
                    <?php echo $total_tax[$j]['invoice_sgst_td']; ?>
                    <td>
                        <input type="text" id="narration_sgst_<?php echo $j;?>" name="narration_sgst_<?php echo $j;?>" value="<?php echo $narration[$j]['sgst']; ?>" class="narration"/>
                    </td>
                </tr>
                <tr style="<?php echo $inter_state_style; ?>">
                    <td class="sticky-cell" style="border: none!important;"><?php echo '2.'.($j+1); ?></td>
                    <td class="sticky-cell" style="border: none!important;">IGST</td>
                    <td class="sticky-cell" style="border: none!important;">
                        <select id="invoiceigst_acc_id_<?php echo $j;?>" class="form-control acc_id" name="invoice_igst_acc_id[]" onChange="get_acc_details(this)">
                            <option value="">Select</option>
                            <?php for($i=0; $i<count($acc_master); $i++) { 
                                    if($acc_master[$i]['type']=="IGST") { 
                            ?>
                            <option value="<?php echo $acc_master[$i]['id']; ?>" <?php if($total_tax[$j]['invoice_igst_acc_id']==$acc_master[$i]['id']) echo 'selected'; ?>><?php echo $acc_master[$i]['legal_name']; ?></option>
                            <?php }} ?>
                        </select>
                        <input type="hidden" id="invoiceigst_ledger_name_<?php echo $j;?>" name="invoice_igst_ledger_name[]" value="<?php echo $total_tax[$j]['invoice_igst_ledger_name']; ?>" />
                    </td>
                    <td class="sticky-cell" style="border: none!important;"><input type="text" id="invoiceigst_ledger_code_<?php echo $j;?>" name="invoice_igst_ledger_code[]" value="<?php echo $total_tax[$j]['invoice_igst_ledger_code']; ?>" style="border: none;" readonly /></td>
                    <td class="sticky-cell" style="border: none!important;">
                        <!-- <input type="hidden" id="vat_cst_<?php //echo $j;?>" name="vat_cst[]" value="<?php //echo $total_tax[$j]['vat_cst']; ?>" />
                        <input type="hidden" id="vat_percen_<?php //echo $j;?>" name="vat_percen[]" value="<?php //echo $total_tax[$j]['igst']; ?>" /> -->
                        <input type="hidden" id="sub_particular_igst_<?php echo $j;?>" name="sub_particular_igst[]" value="<?php echo 'Tax_igst_'.$total_tax[$j]['igst_rate']; ?>" />
                        <?php //echo 'Tax_igst_'.$total_tax[$j]['igst']; ?>
                        <?php echo $mycomponent->format_money($total_tax[$j]['igst_rate'],2); ?>
                    </td>
                    <td class="sticky-cell text-right" style="border: none!important;">
                        <input type="text" class="text-right " id="total_igst_<?php echo $j;?>" name="total_igst_<?php echo $j;?>" value="<?php echo $mycomponent->format_money($total_tax[$j]['total_igst'], 2); ?>" readonly />
                    </td>
                    <?php echo $total_tax[$j]['invoice_igst_td']; ?>
                    <td>
                        <input type="text" class="narration" id="narration_igst_<?php echo $j;?>" name="narration_igst_<?php echo $j;?>" value="<?php echo $narration[$j]['igst']; ?>" />
                    </td>
                </tr>
                <?php } ?>
                 <tr id="othercharges">
                        <td class="sticky-cell" style="border: none!important;">3</td>
                        <td class="sticky-cell" style="border: none!important;">Other Charges</td>
                        <td class="sticky-cell" style="border: none!important;">
                            <select id="othercharges_acc_id_0" class="form-control acc_id" name="other_charges_acc_id" onChange="get_acc_details(this)">
                                <option value="">Select</option>
                                <?php for($i=0; $i<count($acc_master); $i++) { 
                                        if($acc_master[$i]['type']=="Others") { 
                                ?>
                                <option value="<?php echo $acc_master[$i]['id']; ?>" <?php if($acc['other_charges_acc_id']==$acc_master[$i]['id']) echo 'selected'; ?>><?php echo $acc_master[$i]['legal_name']; ?></option>
                                <?php }} ?>
                            </select>
                            <input type="hidden" id="othercharges_ledger_name_0" name="other_charges_ledger_name" value="<?php echo $acc['other_charges_ledger_name']; ?>" />
                        </td>
                        <td class="sticky-cell" style="border: none!important;">
                            <input type="text" id="othercharges_ledger_code_0" name="other_charges_ledger_code" value="<?php echo $acc['other_charges_ledger_code']; ?>" style="border: none;" readonly />
                        </td>
                        <td class="sticky-cell" style="border: none!important;"></td>
                        <td class="sticky-cell" style="border: none!important;">
                            <input type="text" class="text-right" id="other_charges" name="other_charges" value="<?php echo $mycomponent->format_money($total_val[0]['other_charges'], 2); ?>" readonly />
                        </td>
                        <?php for($i=0; $i<count($invoice_details); $i++) { ?>
                            <td>
                                <input type="text" class="text-right" id="invoice_other_charges_<?php echo $i;?>" name="invoice_other_charges[]" value="<?php echo $mycomponent->format_money($invoice_details[$i]['invoice_other_charges'], 2); ?>" readonly />
                            </td>
                            <td>
                                <input type="text" class="text-right edit-text edited_other_charges diff" id="edited_other_charges_<?php echo $i;?>" name="edited_other_charges[]" value="<?php echo $mycomponent->format_money($invoice_details[$i]['edited_other_charges'], 2); ?>" onChange="getDifference(this);" />
                            </td>
                            <td>
                                <input type="text" class="text-right" id="diff_other_charges_<?php echo $i;?>" name="diff_other_charges[]" value="<?php echo $mycomponent->format_money($invoice_details[$i]['diff_other_charges'], 2); ?>" readonly />
                            </td>
                    <?php } ?>
                        <td>
                            <input type="text"  id="narration_other_charges" name="narration_other_charges" value="<?php echo $narration['narration_other_charges']; ?>" />
                        </td>
                </tr>
                <tr class="bold-text" style=" ">
                    <td class="sticky-cell" style="border: none!important; background-color: #f9f9f9;"></td>
                    <td class="sticky-cell" style="border: none!important; background-color: #f9f9f9;">Total Amount</td>
                    <td class="sticky-cell" style="border: none!important; background-color: #f9f9f9;">

                        <?php 

                        if($grn_details[0]['vendor_id1']!=""){
                        ?>
                            <select id="totalamount_acc_id_0" class="form-control acc_id" name="total_amount_acc_id" onChange="get_acc_details(this)" style="display: none;">
                                <option value="">Select</option>
                                <?php for($i=0; $i<count($acc_master); $i++) { 
                                        if($acc['total_amount_acc_id']==""){
                                            if($grn_details[0]['vendor_id1']==$acc_master[$i]['vendor_id']) {
                                                if($grn_details[0]['vendor_id1']!="")
                                                {
                                                    $acc['total_amount_acc_id'] = $acc_master[$i]['id'];
                                                    $acc['total_amount_ledger_name'] = $acc_master[$i]['legal_name'];
                                                    $acc['total_amount_ledger_code'] = $acc_master[$i]['code'];  
                                                }
                                                
                                            }
                                        }
                                ?>
                                <option value="<?php echo $acc_master[$i]['id']; ?>" <?php if($acc['total_amount_acc_id']==$acc_master[$i]['id']) echo 'selected'; ?>><?php echo $acc_master[$i]['legal_name']; ?></option>
                                <?php } ?>
                            </select>
                             <input type="hidden" id="totalamount_ledger_name_0" name="total_amount_ledger_name" value="<?php echo $acc['total_amount_ledger_name']; ?>" />
                        <?php echo $acc['total_amount_ledger_name']; ?>
                        <?php } else

                        {?>

                              <select id="totalamount_acc_id_0" class="form-control acc_id" name="total_amount_acc_id" onChange="get_acc_details(this)" >
                                    <option value="">Select</option>
                                    <?php 
                                    for($i=0; $i<count($acc_master); $i++) { 
                                            if($acc_master[$i]['type']=="Goods Purchase") { 
                                            ?>
                                            <option value="<?php echo $acc_master[$i]['id']; ?>" <?php if($ware_array['total_amount_acc_id']==$acc_master[$i]['id']) echo 'selected'; ?>><?php echo $acc_master[$i]['legal_name']; ?></option>
                                            <?php }
                                         } ?>
                            </select>

                            <input type="hidden" id="totalamount_ledger_name_0" name="total_amount_ledger_name" value="<?php echo $ware_array['total_amount_ledger_name']; ?>" />
                             <?php // $ware_array['total_amount_ledger_name']; ?>

                            <?php   } ?>    
                         
                        
                    </td>
                    <td class="sticky-cell" style="border: none!important; background-color: #f9f9f9;">
                      
                        <?php

                            $la_code = '';
                            if($grn_details[0]['vendor_id1']!=""){
                                $la_code =  $acc['total_amount_ledger_code']; 
                            }
                            else
                            {
                                $la_code =  $ware_array['total_amount_ledger_code'];
                            }
                        ?>

                          <input type="text" id="totalamount_ledger_code_0" name="total_amount_ledger_code" value="<?php echo $la_code; ?>" style="display: none;" readonly />
                    </td>
                    <td class="sticky-cell" style="border: none!important; background-color: #f9f9f9;"></td>
                    <td class="sticky-cell" style="border: none!important; background-color: #f9f9f9;">
                        <input type="text" class="text-right" id="total_amount" name="total_amount" value="<?php echo $mycomponent->format_money($total_val[0]['total_amount'], 2); ?>" readonly />
                    </td>
                    <?php for($i=0; $i<count($invoice_details); $i++) { ?>
                        <td>
                            <input type="text" class="text-right" id="invoice_total_amount_<?php echo $i;?>" name="invoice_total_amount[]" value="<?php echo $mycomponent->format_money($invoice_details[$i]['invoice_total_amount'], 2); ?>" readonly />
                            <input type="hidden" id="total_amount_voucher_id_<?php echo $i;?>" name="total_amount_voucher_id[]" value="<?php echo $invoice_details[$i]['total_amount_voucher_id']; ?>" />
                            <input type="hidden" id="total_amount_ledger_type_<?php echo $i;?>" name="total_amount_ledger_type[]" value="<?php echo $invoice_details[$i]['total_amount_ledger_type']; ?>" />
                        </td>
                        <td>
                            <input type="text" class="text-right" id="edited_total_amount_<?php echo $i;?>" name="edited_total_amount[]" value="<?php echo $mycomponent->format_money($invoice_details[$i]['edited_total_amount'], 2); ?>" onChange="getDifference(this);" readonly />
                        </td>
                        <td>
                            <input type="text" class="text-right" id="diff_total_amount_<?php echo $i;?>" name="diff_total_amount[]" value="<?php echo $mycomponent->format_money($invoice_details[$i]['diff_total_amount'], 2); ?>" readonly />
                        </td>
                    <?php } ?>
                    <td>
                        <input type="text" id="narration_total_amount" name="narration_total_amount" value="<?php echo $narration['narration_total_amount']; ?>" />
                    </td>
                </tr>
                <tr class="bold-text" >
                    <td class="sticky-cell" style="border: none!important; background-color: #f9f9f9;"><button type="button" class="btn btn-info btn-xs  " id="get_shortage_qty">Edit</button></td>
                    <td class="sticky-cell" style="border: none!important; background-color: #f9f9f9;">Total Payable Amount</td>
                    <td class="sticky-cell" style="border: none!important; background-color: #f9f9f9;"></td>
                    <td class="sticky-cell" style="border: none!important; background-color: #f9f9f9;"></td>
                    <td class="sticky-cell" style="border: none!important; background-color: #f9f9f9;"></td>
                    <td id="total_payable_amount" class="text-right sticky-cell" style="border: none!important; background-color: #f9f9f9;">

                    </td>
                    <?php for($i=0; $i<count($invoice_details); $i++) { ?>
                        <td id="invoice_total_payable_amount_<?php echo $i;?>" class="text-right">

                        </td>
                        <td>
                            <input type="text" class="text-right total-amount" id="edited_total_payable_amount_<?php echo $i;?>" name="edited_total_payable_amount[]" value="<?php //echo $mycomponent->format_money($invoice_details[$i]['edited_total_payable_amount'], 2); ?>" readonly />
                        </td>
                        <td id="diff_total_payable_amount_<?php echo $i;?>" class="text-right">

                        </td>
                    <?php } ?>
                    <td></td>
                </tr>