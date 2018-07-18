<?php
    use yii\helpers\Html;
    use yii\grid\GridView;
    use yii\helpers\Url;
    use yii\jui\Autocomplete;

    use yii\jui\DatePicker;
    use yii\web\JsExpression;
    use yii\db\Query;

    $this->title = 'Goods Outwrd Debit Note';
    $this->params['breadcrumbs'][] = $this->title;
    $mycomponent = Yii::$app->mycomponent;
?>
<style type="text/css">
    #go_debit_details .error {color: #dd4b39!important;}
    input:-webkit-autofill {background-color: white !important;}
    .form-devident { margin-top: 10px; }
    .form-horizontal .control-label {font-size: 12px; letter-spacing: .5px; margin-top:5px; }
    .form-devident { margin-top: 10px; }
    .table-hover>tbody>tr:hover {background:none!important;}
    table tr td { border: 1px solid #eee!important; }
</style>

<div class="grn-index"> 
    <div class=" col-md-12 ">  
        <form id="go_debit_details" class="form-horizontal" action="<?php echo Url::base(); ?>index.php?r=goodsoutward%2Fsave" method="post" enctype="multipart/form-data" onkeypress="return event.keyCode != 13;"> 
            <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
            <input type="hidden" name="id" id="id" value="<?php if(isset($data[0]['id'])) echo $data[0]['id']; ?>" />
            <input type="hidden" name="gi_go_id" id="gi_go_id" value="<?php if(isset($data[0]['gi_go_id'])) echo $data[0]['gi_go_id']; ?>" />
            <input type="hidden" id="action" name="action" value="<?php if(isset($action)) echo $action; ?>">
            <input type="hidden" id="status" name="status" value="<?php if(isset($data[0]['status'])) echo $data[0]['status']; ?>" />
            <input type="hidden" name="vendor_id" value="<?php if(isset($data[0]['vendor_id'])) echo $data[0]['vendor_id']; ?>" />
            <input type="hidden" name="voucher_id" value="<?php if(isset($data[0]['voucher_id'])) echo $data[0]['voucher_id']; ?>" />
            <input type="hidden" name="warehouse_state" value="<?php if(isset($data[0]['warehouse_state'])) echo $data[0]['warehouse_state']; ?>" />
            <input type="hidden" name="debit_note_ref" value="<?php if(isset($data[0]['debit_note_ref'])) echo $data[0]['debit_note_ref']; ?>" />
            <!-- <input type="hidden" name="ledger_type" value="<?php //if(isset($data[0]['ledger_type'])) echo $data[0]['ledger_type']; ?>" /> -->

            <div class="form-devident">
                <div class="form-group">
                    <div class="row">
                        <div class=" col-md-3 col-sm-3 col-xs-6">
                            <label class="control-label">Date Of Transaction</label>
                            <div class=" ">
                                <div class=" "> 
                                    <input class="form-control datepicker" type="text" id="date_of_transaction" name="date_of_transaction" value="<?php if(isset($data[0]['date_of_transaction'])) { $data[0]['gi_go_date_time'] = $data[0]['date_of_transaction'];} if(isset($data[0]['gi_go_date_time'])) echo (($data[0]['gi_go_date_time']!=null && $data[0]['gi_go_date_time']!='')?date('d/m/Y',strtotime($data[0]['gi_go_date_time'])):date('d/m/Y')); else echo date('d/m/Y'); ?>" readonly /> 
                                </div>
                            </div>
                        </div>
                        <div class=" col-md-3 col-sm-3 col-xs-6">
                            <label class="control-label">GO Number</label>
                            <div class=" ">
                                <div class=" ">  
                                    <input name="gi_go_ref_no" id="gi_go_ref_no" class="form-control" type="text" value="<?php if(isset($data)) echo $data[0]['gi_go_ref_no']; ?>" readonly />
                                </div>
                            </div>
                        </div>
                        <div class=" col-md-3 col-sm-3 col-xs-6">
                            <label class="control-label">From</label>
                            <div class=" ">
                                <div class=" "> 
                                    <input class="form-control " type="text" id="from_entity" name="from_entity" value="<?php if(isset($data)) echo $data[0]['warehouse_name']; ?>" readonly /> 
                                </div>
                            </div>
                        </div>
                        <div class=" col-md-3 col-sm-3 col-xs-6">
                            <label class="control-label">To</label>
                            <div class=" ">
                                <div class=" ">  
                                    <input class="form-control" type="text" id="to_entity" name="to_entity" value="<?php if(isset($data)) echo $data[0]['to_party']; ?>" readonly />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-devident">
                <div class="form-group col-md-12 col-sm-12 col-xs-12">
                    <table class="table table-bordered" id="acc_jv_details">
                        <thead>
                            <tr>
                                <th width="200">Account</th>
                                <th width="150">Account Code</th>
                                <th width="150">Debit Amt</th>
                                <th width="150">Credit Amt</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php for($i=0; $i<count($data_entries); $i++) { ?>
                            <tr>
                                <td>
                                    <select class="form-control select2" id="acc_id_<?php echo $i; ?>" name="acc_id[]" onChange="get_acc_details(this)">
                                        <option value="">Select</option>
                                        <?php for($j=0; $j<count($acc_master); $j++) { 
                                                if($acc_master[$j]['type']==$data_entries[$i]['acc_type']) { 
                                        ?>
                                        <option value="<?php echo $acc_master[$j]['id']; ?>" <?php if($data_entries[$i]['acc_id']==$acc_master[$j]['id']) echo 'selected'; ?>><?php echo $acc_master[$j]['legal_name']; ?></option>
                                        <?php }} ?>
                                    </select>
                                    <input type="hidden" id="acc_type_<?php echo $i; ?>" name="acc_type[]" value="<?php echo $data_entries[$i]['acc_type']; ?>" />
                                    <input type="hidden" id="ledger_name_<?php echo $i; ?>" name="ledger_name[]" value="<?php echo $data_entries[$i]['ledger_name']; ?>" />
                                    <input type="hidden" id="ledger_type_<?php echo $i; ?>" name="ledger_type[]" value="<?php echo $data_entries[$i]['ledger_type']; ?>" />
                                    <input type="hidden" id="transaction_<?php echo $i; ?>" name="transaction[]" value="<?php echo $data_entries[$i]['transaction']; ?>" />
                                </td>
                                <td><input class="form-control " type="text" id="ledger_code_<?php echo $i; ?>" name="ledger_code[]" value="<?php echo $data_entries[$i]['ledger_code']; ?>" readonly /></td>
                                <td><input class="form-control debit_amt" type="text" id="debit_amt_<?php echo $i; ?>" name="debit_amt[]" value="<?php echo $mycomponent->format_money($data_entries[$i]['debit_amt'],2); ?>" onChange="get_total();" <?php if($data_entries[$i]['transaction']=="Credit") echo 'readonly'; ?> /></td>
                                <td><input class="form-control credit_amt" type="text" id="credit_amt_<?php echo $i; ?>" name="credit_amt[]" value="<?php echo $mycomponent->format_money($data_entries[$i]['credit_amt'],2); ?>" onChange="get_total();" <?php if($data_entries[$i]['transaction']=="Debit") echo 'readonly'; ?> /></td>
                            </tr>
                            <?php } ?>

                            <!-- <tr>
                                <td>
                                    <select class="form-control" id="vendor_acc_id" name="vendor_acc_id" onChange="get_acc_details(this)">
                                        <option value="">Select</option>
                                        <?php //for($i=0; $i<count($acc_master); $i++) { 
                                                //if($acc_master[$i]['type']=="Vendor Goods") { 
                                        ?>
                                        <option value="<?php //echo $acc_master[$i]['id']; ?>" <?php //if($data[0]['vendor_acc_id']==$acc_master[$i]['id']) echo 'selected'; ?>><?php //echo $acc_master[$i]['legal_name']; ?></option>
                                        <?php //}} ?>
                                    </select>
                                    <input type="hidden" id="vendor_ledger_name" name="vendor_ledger_name" value="<?php //echo $data[0]['vendor_ledger_name']; ?>" />
                                </td>
                                <td><input type="text" id="vendor_ledger_code" name="vendor_ledger_code" value="<?php //echo $data[0]['vendor_ledger_code']; ?>" /></td>
                                <td><input class="form-control" type="text" name="vendor_debit_amt" id="vendor_debit_amt" value="<?php //echo $mycomponent->format_money($data[0]['vendor_debit_amt'],2); ?>" onChange="get_total();" <?php //if($data[0]['vendor_transaction']=="Credit") echo 'readonly'; ?> /></td>
                                <td><input class="form-control" type="text" name="vendor_credit_amt" id="vendor_credit_amt" value="<?php //echo $mycomponent->format_money($data[0]['vendor_credit_amt'],2); ?>" onChange="get_total();" <?php //if($data[0]['vendor_transaction']=="Debit") echo 'readonly'; ?> /></td>
                            </tr>
                            <tr>
                                <td>
                                    <select class="form-control" id="tax_acc_id" name="tax_acc_id" onChange="get_acc_details(this)">
                                        <option value="">Select</option>
                                        <?php //for($i=0; $i<count($acc_master); $i++) { 
                                                //if($acc_master[$i]['type']=="Vendor Goods") { 
                                        ?>
                                        <option value="<?php //echo $acc_master[$i]['id']; ?>" <?php //if($data[0]['tax_acc_id']==$acc_master[$i]['id']) echo 'selected'; ?>><?php //echo $acc_master[$i]['legal_name']; ?></option>
                                        <?php //}} ?>
                                    </select>
                                    <input type="hidden" id="tax_ledger_name" name="tax_ledger_name" value="<?php //echo $data[0]['tax_ledger_name']; ?>" />
                                </td>
                                <td><input type="text" id="tax_ledger_code" name="tax_ledger_code" value="<?php //echo $data[0]['tax_ledger_code']; ?>" /></td>
                                <td><input class="form-control" type="text" name="tax_debit_amt" id="tax_debit_amt" value="<?php //echo $mycomponent->format_money($data[0]['tax_debit_amt'],2); ?>" onChange="get_total();" <?php //if($data[0]['tax_transaction']=="Credit") echo 'readonly'; ?> /></td>
                                <td><input class="form-control" type="text" name="tax_credit_amt" id="tax_credit_amt" value="<?php //echo $mycomponent->format_money($data[0]['tax_credit_amt'],2); ?>" onChange="get_total();" <?php //if($data[0]['tax_transaction']=="Debit") echo 'readonly'; ?> /></td>
                            </tr>
                            <tr>
                                <td>
                                    <select class="form-control" id="cgst_acc_id" name="cgst_acc_id" onChange="get_acc_details(this)">
                                        <option value="">Select</option>
                                        <?php //for($i=0; $i<count($acc_master); $i++) { 
                                                //if($acc_master[$i]['type']=="Vendor Goods") { 
                                        ?>
                                        <option value="<?php //echo $acc_master[$i]['id']; ?>" <?php //if($data[0]['cgst_acc_id']==$acc_master[$i]['id']) echo 'selected'; ?>><?php //echo $acc_master[$i]['legal_name']; ?></option>
                                        <?php //}} ?>
                                    </select>
                                    <input type="hidden" id="cgst_ledger_name" name="cgst_ledger_name" value="<?php //echo $data[0]['cgst_ledger_name']; ?>" />
                                </td>
                                <td><input type="text" id="cgst_ledger_code" name="cgst_ledger_code" value="<?php //echo $data[0]['cgst_ledger_code']; ?>" /></td>
                                <td><input class="form-control" type="text" name="cgst_debit_amt" id="cgst_debit_amt" value="<?php //echo $mycomponent->format_money($data[0]['cgst_debit_amt'],2); ?>" onChange="get_total();" <?php //if($data[0]['cgst_transaction']=="Credit") echo 'readonly'; ?> /></td>
                                <td><input class="form-control" type="text" name="cgst_credit_amt" id="cgst_credit_amt" value="<?php //echo $mycomponent->format_money($data[0]['cgst_credit_amt'],2); ?>" onChange="get_total();" <?php //if($data[0]['cgst_transaction']=="Debit") echo 'readonly'; ?> /></td>
                            </tr>
                            <tr>
                                <td>
                                    <select class="form-control" id="sgst_acc_id" name="sgst_acc_id" onChange="get_acc_details(this)">
                                        <option value="">Select</option>
                                        <?php //for($i=0; $i<count($acc_master); $i++) { 
                                                //if($acc_master[$i]['type']=="Vendor Goods") { 
                                        ?>
                                        <option value="<?php //echo $acc_master[$i]['id']; ?>" <?php //if($data[0]['sgst_acc_id']==$acc_master[$i]['id']) echo 'selected'; ?>><?php //echo $acc_master[$i]['legal_name']; ?></option>
                                        <?php //}} ?>
                                    </select>
                                    <input type="hidden" id="sgst_ledger_name" name="sgst_ledger_name" value="<?php //echo $data[0]['sgst_ledger_name']; ?>" />
                                </td>
                                <td><input type="text" id="sgst_ledger_code" name="sgst_ledger_code" value="<?php //echo $data[0]['sgst_ledger_code']; ?>" /></td>
                                <td><input class="form-control" type="text" name="sgst_debit_amt" id="sgst_debit_amt" value="<?php //echo $mycomponent->format_money($data[0]['sgst_debit_amt'],2); ?>" onChange="get_total();" <?php //if($data[0]['sgst_transaction']=="Credit") echo 'readonly'; ?> /></td>
                                <td><input class="form-control" type="text" name="sgst_credit_amt" id="sgst_credit_amt" value="<?php //echo $mycomponent->format_money($data[0]['sgst_credit_amt'],2); ?>" onChange="get_total();" <?php //if($data[0]['sgst_transaction']=="Debit") echo 'readonly'; ?> /></td>
                            </tr>
                            <tr>
                                <td>
                                    <select class="form-control" id="igst_acc_id" name="igst_acc_id" onChange="get_acc_details(this)">
                                        <option value="">Select</option>
                                        <?php //for($i=0; $i<count($acc_master); $i++) { 
                                                //if($acc_master[$i]['type']=="Vendor Goods") { 
                                        ?>
                                        <option value="<?php //echo $acc_master[$i]['id']; ?>" <?php //if($data[0]['igst_acc_id']==$acc_master[$i]['id']) echo 'selected'; ?>><?php //echo $acc_master[$i]['legal_name']; ?></option>
                                        <?php //}} ?>
                                    </select>
                                    <input type="hidden" id="igst_ledger_name" name="igst_ledger_name" value="<?php //echo $data[0]['igst_ledger_name']; ?>" />
                                </td>
                                <td><input type="text" id="igst_ledger_code" name="igst_ledger_code" value="<?php //echo $data[0]['igst_ledger_code']; ?>" /></td>
                                <td><input class="form-control" type="text" name="igst_debit_amt" id="igst_debit_amt" value="<?php //echo $mycomponent->format_money($data[0]['igst_debit_amt'],2); ?>" onChange="get_total();" <?php //if($data[0]['igst_transaction']=="Credit") echo 'readonly'; ?> /></td>
                                <td><input class="form-control" type="text" name="igst_credit_amt" id="igst_credit_amt" value="<?php //echo $mycomponent->format_money($data[0]['igst_credit_amt'],2); ?>" onChange="get_total();" <?php //if($data[0]['igst_transaction']=="Debit") echo 'readonly'; ?> /></td>
                            </tr> -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2" class="text-right">Total</th>
                                <th><input class="form-control" type="text" id="total_debit_amt" name="total_debit_amt" value="<?php if(isset($data)) echo $mycomponent->format_money($data[0]['debit_amt'],2); ?>" readonly /></th>
                                <th><input class="form-control" type="text" id="total_credit_amt" name="total_credit_amt" value="<?php if(isset($data)) echo $mycomponent->format_money($data[0]['credit_amt'],2); ?>" readonly /></th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-right">Difference</th>
                                <th><input class="form-control" type="text" id="diff_amt" name="diff_amt" value="<?php if(isset($data)) echo $mycomponent->format_money($data[0]['diff_amt'],2); ?>" readonly /></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            

            <div class="form-group">
                <div class="col-md-6 col-sm-6 col-xs-6">
                    <label class="control-label">Remarks</label>
                    <textarea id="remarks" name="remarks" class="form-control" rows="2" maxlength="1000"><?php if(isset($data[0]['approver_comments'])) echo $data[0]['approver_comments']; ?></textarea>
                </div>
                <!-- <div class="col-md-3 col-sm-3 col-xs-6">
                    <label class="control-label">Approver</label>
                    <select id="approver_id" name="approver_id" class="form-control">
                        <option value="">Select</option>
                        <?php //for($i=0; $i<count($approver_list); $i++) { ?>
                            <option value="<?php //echo $approver_list[$i]['id']; ?>" <?php //if(isset($data[0])) { if($data[0]['approver_id']==$approver_list[$i]['id']) echo "selected"; } ?>><?php //echo $approver_list[$i]['username']; ?></option>
                        <?php //} ?>
                    </select>
                </div> -->
            </div>

            <div class="  btn-container"> 
                <div class=" ">
                    <input type="submit" class="btn btn-success btn-sm" id="btn_submit" name="btn_submit" value="Submit" />
                    <!-- <input type="submit" class="btn btn-danger btn-sm" id="btn_reject" name="btn_reject" value="Reject" /> -->
                    <a href="<?php echo Url::base(); ?>index.php?r=goodsoutward%2Findex" class="btn btn-primary btn-sm pull-right" >Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
    var BASE_URL="<?php echo Url::base(); ?>";
</script>

<?php 
    $this->registerJsFile(
        '@web/js/jquery-ui-1.11.2/jquery-ui.min.js',
        ['depends' => [\yii\web\JqueryAsset::className()]]
    );
    $this->registerJsFile(
        '@web/js/go_debit_details.js',
        ['depends' => [\yii\web\JqueryAsset::className()]]
    );
?>