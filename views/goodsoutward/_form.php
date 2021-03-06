<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Grn */
/* @var $form yii\widgets\ActiveForm */

$mycomponent = Yii::$app->mycomponent;
?>
<style>
    #form_purchase_details .error {color: #dd4b39!important;}
    .table-head { font-weight:100;  
        background: #41ace9; 
        color: #fff;
        border-bottom: 1px solid #41ace9;
        background-image: linear-gradient(#54b4eb, #2fa4e7 60%, #1d9ce5);
       }

       .border-ok { border:1px solid #ddd!important; padding: 0 5px;}
    /*--------------------------*/
    #shortage_sku_details { width: 2500px; }
    #shortage_sku_details tr td input { border: none; outline: none; }
        
    #shortage_sku_details .table-bordered>thead>tr>th, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>tbody>tr>td, .table-bordered>tfoot>tr>td{ border:1px solid #ddd!important; }
    #shortage_sku_details tr td  select { width: 100%;  border:1px solid #ddd!important;  outline: none;}
    #shortage_sku_details tr td:nth-child(25) input, #shortage_sku_details tr td:nth-child(54) input { border: 1px solid #ddd!important; outline: none; }
    #shortage_sku_details tr td:nth-child(54) { width: 400px; }
    /*-----------------------

    /*----------------------*/

    /*----------------------*/

    /*----------------------*/
    #ledger_details .modal-body .table   {  }

    .bold-text { font-weight:600; letter-spacing:.5px; background:#f9f9f9;   }
    .btn-margin { margin-top:20px;}
    .row-container { position:relative;    padding:10px 0; margin-bottom:15px;  }
    label { font-weight:normal;     font-size: 12px; }
    .actual_value {   padding:0px 3px;   color:#fff; font-weight:400; text-align:right;}
    .table   tr th  { font-weight:normal;}
    .table > thead > tr:nth-child(2) {     background:none!important;  }
    .modal-dialog .table  tr  td { min-width:60px;}
    .table-bordered > thead > tr > th, .table-bordered > thead > tr > td { border-bottom-width:1px;  }
    .modal-body { padding:10px; }
    input { outline:none; background:none; width:100%; }
    .edit-text { border:1px solid #ddd; padding:1px 5px;}
    #update_grn tr td:last-child {   padding:4px 5px; min-width:150px; }
    table tr td:first-child  {   text-align:center;     }
    table tr th:nth-child(1) { padding:4px 3px!important; width:55px;    }
    table tr td:nth-child(2) {    }
     .total-amount { width:100%; padding:0; }
    table tr td:last-child input{   border:1px solid #ddd;  padding-left:5px; }
     
    .btn-danger {
        background-color: #dd4b39;
        border-color: #d73925;
        color: #fff;
    }

    .modal-lg { width:100%;}
    .modal-dialog { margin:10px;}
    .modal-content { border-radius:0;}
    .modal-body  { padding:0;}
    .modal-body-inside { /*max-width:1310px;*/ overflow-y:hidden!important; margin:20px auto; }
    .modal-header { background:#f1f1f1;}
    .modal-footer { background:#f1f1f1;}
    /*.modal-body .table {  width:3200px; }*/
    .close { outline:none;}



    #update_grn th, td { white-space: nowrap; }
        div.dataTables_wrapper {
            width:100%;
            margin: 0 auto;
        }

    .dataTables_scrollBody{ height:auto!important; }
    #update_grn   th {   background-image: linear-gradient(#54b4eb, #2fa4e7 60%, #1d9ce5)!important; color:#fff;}  
    .table-container { /* overflow-x:scroll;*/  width:100%; margin:20px 0;}
     table.dataTable tbody tr { background:#fff!important;}
      table.dataTable.display tbody tr.odd > .sorting_1, table.dataTable.order-column.stripe tbody tr.odd > .sorting_1 {
        background-color: #fff!important;
    }
    #update_grn>thead>tr>th, #update_grn>tbody>tr>th, #update_grn>tfoot>tr>th, #update_grn>thead>tr>td, #update_grn>tbody>tr>td, #update_grn>tfoot>tr>td {
        border-bottom: 1px solid #ddd!important;
         border-right: 1px solid #ddd!important;
    }
    #update_grn thead tr th{ height:0; padding:0;   /*line-height:30px!important;*/  border:none!important;   }
     #update_grn   tr th {
     height:auto!important; padding:0px 10px!important; /*line-height:30px;*/ border:none!important;  
    }
     #update_grn  tr td {
     height:auto!important; padding:3px 10px!important;
    }

    @media only screen and (min-width:250px) and (max-width:767px) {
    	.col-xs-5 {   padding:5px 10px; } 
    	.col-xs-7 {  padding:5px 10px; } 
    	.row-container { padding:0;}
    	label {margin-top:5px; margin-bottom:2px;}
    	.table-container { max-width:700px; overflow-x:scroll;}
    	.table-container table{ width:1200px;   } 
    	 .navbar-collapse.in { overflow:hidden!important;}
    	

    	}
    @media only screen and (min-width:250px) and (max-width:1350px) {	 
     .modal-body {   padding: 0 15px;}
    }
    	@media 
      only screen and (min-width: 768px),
      not all and (min-width: 768px),
      not print and (min-height: 768px),
      (color),
      (min-height: 768px) and (max-height: 1000px),
      handheld and (orientation: landscape)
    {
    	/*.table-container { width:100%; overflow-x:scroll;}*/
    	.table-container table{   } 
    }
    .diversion {margin-left: 10px;}
    .diversion table{max-width: 1300px;}
    .table-container {overflow: auto;}
    table {width: 1200px;}
</style>
<div class="grn-form">
    <div class=" col-md-12">  
        <form id="form_purchase_details" action="<?php echo Url::base(); ?>index.php?r=goodsoutward%2Fsave" method="post" onkeypress="return event.keyCode != 13;">
        <!-- <form id="form_purchase_details" action="<?php //echo Url::base(); ?>index.php?r=pendinggo%2Fgetgrnparticulars" method="post"> -->
    
        <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />

        <div class="row row-container">
            <div class="form-group">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <?php //echo json_encode($invoice_tax); ?>
                </div>
    			<div class="col-md-2 col-sm-2 col-xs-6">
                    <label class=" control-label">Posting Date </label> 
                    <div class=" "> 
    					<input style="background-color: #fff;" type="text" class="form-control datepicker" name="gi_date" id="gi_date" value="<?php if(isset($grn_details)) { if($grn_details[0]['gi_go_final_commit_date']!=null && $grn_details[0]['gi_go_final_commit_date']!='') echo date('d/m/Y',strtotime($grn_details[0]['gi_go_final_commit_date'])); else echo (($grn_details[0]['gi_date']!=null && $grn_details[0]['gi_date']!='')?date('d/m/Y',strtotime($grn_details[0]['gi_date'])):date('d/m/Y')); } else echo date('d/m/Y'); ?>" readonly />
                        <input type="hidden" class="form-control" name="no_of_invoices" id="no_of_invoices" value="<?= count($invoice_details) ?>" />
                        <input type="hidden" class="form-control" name="id" id="id" value="<?= $grn_details[0]['id']?>" />
                        <input type="hidden" class="form-control" name="debit_note_ref" id="debit_note_ref" value="<?= $grn_details[0]['debit_note_ref']?>" />
                    </div>
    			</div>
                <div class="col-md-2 col-sm-2 col-xs-6">
                    <label class="control-label">Go Id</label>
                    <div class=" "> 
                        <input type="text" class="form-control" name="gi_go_id" id="grn_id" placeholder="Go No" value="<?= $grn_details[0]['gi_go_id'] ?>" readonly>
                    </div>
               </div>
    			<div class="col-md-2 col-sm-2 col-xs-6">
                    <label class="control-label">Go No </label> 
                    <div class=" ">
                        <input type="hidden" class="form-control" name="action" id="action" placeholder="Action" value="<?= $action ?>" />
                        <input type="text" class="form-control" name="gi_go_ref_no" id="gi_id" placeholder="Go No" value="<?= $grn_details[0]['gi_go_ref_no'] ?>" readonly />
                    </div>
                </div>
		        <div class="col-md-2 col-sm-2 col-xs-6">
                    <label class=" control-label">Vendor name</label> 
                    <div class=" "> 
    					<input type="hidden" class="form-control" name="vendor_id" id="vendor_id" value="<?= $grn_details[0]['vendor_id1']?>" /> 
                        <input type="hidden" class="form-control" name="warehouse_id" id="warehouse_id" value="<?= $grn_details[0]['warehouse_id']?>" /> 
                        <input type="text" class="form-control" name="vendor_name" id="vendor_name" value="<?= ($grn_details[0]['vendor_name']!=""?$grn_details[0]['vendor_name']:$grn_details[0]['idt_warehouse'])  ?>" readonly />  
                    </div>
                </div>
    		    <div class="col-md-2 col-sm-2 col-xs-6">
                    <label class=" control-label">Category Name </label> 
                    <div class=" "> 
    					<input type="text" class="form-control" name="category_name" id="category_name"   value="<?= $grn_details[0]['product_category_name'] ?>" readonly /> 
                    </div>
                </div>
    			<div class="col-md-2 col-sm-2 col-xs-6">
                    <label class=" control-label">Location From </label> 
                    <div class=" "> 
    				    <input type="text" class="form-control" name="location_from" id="location_from"   value="<?= $grn_details[0]['warehouse_state'] ?>" readonly />  
                    </div>
    			</div>
                <div class="col-md-2 col-sm-2 col-xs-6">
                    <label class=" control-label">Location To </label> 
                    <div class=" "> 
                        <input type="text" class="form-control" name="location_to" id="location_to"   value="<?= $grn_details[0]['to_state'] ?>" readonly />  
                    </div>
                </div>
                <div class="col-md-8 col-sm-8 col-xs-6">
                    <label class=" control-label">Narration </label> 
                    <div class=" "> 
                        <input type="text" class="form-control" name="narration" id="narration"   value="<?= $grn_details[0]['narration'] ?>" />  
                    </div>
                </div>
    			 <input type="hidden" class="form-control" name="vat_cst" id="vat_cst"   value="<?= $grn_details[0]['tax_zone_code'] ?>" readonly />
            </div>
            <div class="form-group" id="form_errors_group" style="display:none;">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <label class="control-label">&nbsp;</label>
                    <div id="form_errors" style="display:none; color:#E04B4A;" class="error"></div>
               </div>
            </div>
        </div>

        <?php 

           /* echo "<pre>";
            print_r($skuwise);
            echo "</pre>"*/
        ?>
    	
        <?php 
            $intra_state_style = "";
            $inter_state_style = "";
            if(strtoupper($grn_details[0]['tax_zone_code'])=="INTRA"){
                $inter_state_style = "display:none;";
            } else {
                $intra_state_style = "display:none;";
            }
        ?>

    	<div id="update_grn_div" class="table-container sticky-table sticky-headers sticky-ltr-cells">
            <table id="update_grn" class="table table-bordered">
                <tr class="table-head">
                    <th class="sticky-cell" style="border: none!important;">Sr. No.</th>
                    <th class="sticky-cell" style="border: none!important;">Particulars</th>
                    <th class="sticky-cell" style="width: 250px; border: none!important;">Ledger Name</th>
                    <th class="sticky-cell" style="width: 200px; border: none!important;">Ledger Code</th>
                    <th class="sticky-cell" style="width: 25px; border: none!important;">Tax <br/> Percent</th>
                    <th class="sticky-cell" style="border: none!important;">Value</th>
                    <?php for($i=0; $i<count($invoice_details); $i++) { ?>
                        <th class="text-center">
                            <input type="hidden" id="invoice_no_<?php echo $i;?>" name="invoice_no[]" value="<?php echo $invoice_details[$i]['invoice_number']; ?>" />
                            <span>Invoice</span> <br/> <span class="actual_value"> (<?php echo $invoice_details[$i]['invoice_number']; ?>) </span>    </th>
                        <th style="width: 150px;"><span>Edited</span> <br/> <span class="actual_value"> (<?php echo $invoice_details[$i]['invoice_number']; ?>) </span>  </span></th>
                        <th><span>Difference</span> <br/> <span class="actual_value"> (<?php echo $invoice_details[$i]['invoice_number']; ?>) </span>  </span></th>
                    <?php } ?>
                    <th>Narration</th>
                </tr>

                <?php
                $taxcount = count($total_tax);
                for($j=0; $j<count($total_tax); $j++) { ?>

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

                                    if($invoice_tax[$i]['invoice_cost']==0)
                                    {
                                        $total_tax[$j]['total_cost']=$invoice_tax[$i]['invoice_cost'];
                                        $total_tax[$j]['total_cgst']=0;
                                        $total_tax[$j]['total_sgst']=0;
                                        $total_tax[$j]['total_igst']=0;
                                    }
                                    else
                                    {
                                        $total_tax[$j]['total_cost'] = $invoice_tax[$i]['invoice_cost'];
                                        $total_tax[$j]['total_cgst'] = $invoice_tax[$i]['invoice_cgst'];
                                        $total_tax[$j]['total_sgst'] = $invoice_tax[$i]['invoice_sgst'];
                                        $total_tax[$j]['total_igst'] = $invoice_tax[$i]['invoice_igst'];
                                    }

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
                        <select id="invoicecost_acc_id_<?php echo $j;?>" class="form-control acc_id select2" name="invoice_cost_acc_id[]" onChange="get_acc_details(this)">
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
                        <select id="invoicetax_acc_id_<?php echo $j;?>" class="form-control acc_id select2" name="invoice_tax_acc_id[]" onChange="get_acc_details(this)">
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
                        <select id="invoicecgst_acc_id_<?php echo $j;?>" class="form-control acc_id select2" name="invoice_cgst_acc_id[]" onChange="get_acc_details(this)">
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
                        <select id="invoicesgst_acc_id_<?php echo $j;?>" class="form-control acc_id select2" name="invoice_sgst_acc_id[]" onChange="get_acc_details(this)">
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
                        <select id="invoiceigst_acc_id_<?php echo $j;?>" class="form-control acc_id select2" name="invoice_igst_acc_id[]" onChange="get_acc_details(this)">
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
                            <select id="othercharges_acc_id_0" class="form-control acc_id select2" name="other_charges_acc_id" onChange="get_acc_details(this)">
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
                            <select id="totalamount_acc_id_0" class="form-control acc_id " name="total_amount_acc_id" onChange="get_acc_details(this)" style="display: none;">
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

                              <select id="totalamount_acc_id_0" class="form-control acc_id " name="total_amount_acc_id" onChange="get_acc_details(this)" >
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
            </table>
         </div>

         <?php 
            /*echo "<pre>";
            print_r($skuwise);
            echo "</pre>";*/
         ?>
         <!-- Shortage Modal -->
        <div class="modal fade" id="shortage_modal" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">SKU Details</h4>
                    </div>
                    <div class="modal-body" style=" ">
                        <div class="modal-body-inside"  >
                       <table class="table table-bordered" id="shortage_sku_details">
                            <thead>
                                <tr>
                                    <th colspan="8">SKU Details</th>
                                    <th colspan="2">Quantity</th>

                                    <th colspan="10">Purchase Order Details</th>
                                </tr>
                                <tr>
                                    <th>Sr no</th>
                                    <th>Asin</th>
                                    <th>HSN Code</th>
                                    <th>Batch code</th>
                                    <th>Expiry Date</th>
                                    <th>MSKU</th>
                                    <th>FNSKU</th>
                                    <th>Product title</th>
                                    <th>Qty</th>
                                    <th>MRP</th>
                                    <th>Cost Price</th>
                                    <th>Value at Cost</th>
                                    <th>Total Tax Rate</th>
                                    <th>CGST Rate</th>
                                    <th>CGST Val</th>
                                    <th>SGST Rate</th>
                                    <th>SGST Val</th>
                                    <th>IGST Rate</th>
                                    <th>IGST Val</th>
                                    <th>Total Amount</th>
                                <tr>
                            </thead>
                            <?php 

                                $tabbody = '';
                                $sr_no = 1;
                                $ded_type = 'goodsoutwards';

                                for($i=0;$i<count($skuwise);$i++)
                                {
                                    $cgst_rate = $skuwise[$i]['cgst_rate'];
                                    $sgst_rate = $skuwise[$i]['sgst_rate'];
                                    $igst_rate = $skuwise[$i]['igst_rate'];
                                    $vat_percen = floatval($skuwise[$i]["vat_percent"]);
                                    $cost_excl_vat = floatval($skuwise[$i]["cost_excl_vat"]);
                                    $total_tax = $skuwise[$i]["total_tax"];
                                    $total_amount = ROUND($cost_excl_vat+$total_tax,2);
                                    $cost_excl_tax_per_unit = $skuwise[$i]['per_unit_exc_tax'];
                                    $state  = $skuwise[$i]['tax_zone_code'];
                                    /*$cgst_per_unit = ($cost_excl_tax_per_unit*$cgst_rate)/100;
                                    $sgst_per_unit = ($cost_excl_tax_per_unit*$sgst_rate)/100;
                                    $igst_per_unit = ($cost_excl_tax_per_unit*$igst_rate)/100;
                                    // $tax_per_unit = ($cost_excl_tax_per_unit*$vat_percen)/100;
                                    $tax_per_unit = $cgst_per_unit+$sgst_per_unit+$igst_per_unit;
                                    $total_per_unit = $skuwise[$i]['per_unit'];*///$cost_excl_tax_per_unit + $tax_per_unit;

                                    /*$cost_excl_tax = round($qty*$cost_excl_tax_per_unit,2);
                                    $cgst = round($qty*$cgst_per_unit,2);
                                    $sgst = round($qty*$sgst_per_unit,2);
                                    $igst = round($qty*$igst_per_unit,2);
                                    // $tax = $qty*$tax_per_unit;
                                    $tax = $cgst+$sgst+$igst;
                                    $total = $cost_excl_tax + $tax;
                                    $invoice_total = $invoice_total + $total;
                                    $grand_total = $grand_total + $total;*/

                                   echo '<tr>
                                                <td>'.$sr_no.'<input type="hidden" class="'.$ded_type.'_state_'.$sr_no.'" id="'.$ded_type.'_state_'.$i.'" name="'.$ded_type.'_state[]" value="'.$state.'" readonly />
                                                <input type="hidden" class="'.$ded_type.'_oldcost_'.$sr_no.'" id="'.$ded_type.'_oldcost_'.$i.'" name="'.$ded_type.'_oldcost[]" value="'.$skuwise[$i]["per_unit_exc_tax"].'" readonly />
                                                <input type="hidden"  name="'.$ded_type.'_gi_go_id[]" value="'.$skuwise[$i]["gi_go_id"].'" />
                                                <input type="hidden"  name="'.$ded_type.'_ean[]" value="'.$skuwise[$i]["ean"].'" />
                                                <input type="hidden"  name="'.$ded_type.'_sku_code[]" value="'.$skuwise[$i]["sku_code"].'" />
                                                <input type="hidden"  name="'.$ded_type.'_manual_discount[]" value="'.$skuwise[$i]["manual_discount"].'" />
                                                <input type="hidden"  name="'.$ded_type.'_grn_no[]" value="'.$skuwise[$i]["grn_no"].'" />
                                                <input type="hidden"  name="'.$ded_type.'_shipment_id[]" value="'.$skuwise[$i]["shipment_id"].'" />
                                                <input type="hidden"  name="'.$ded_type.'_shipment_plan_name[]" value="'.$skuwise[$i]["shipment_plan_name"].'" />
                                                <input type="hidden"  name="'.$ded_type.'_isa[]" value="'.$skuwise[$i]["isa"].'" />
                                                <input type="hidden"  name="'.$ded_type.'_po_no[]" value="'.$skuwise[$i]["po_no"].'" />
                                                <input type="hidden"  name="'.$ded_type.'_go_no[]" value="'.$skuwise[$i]["go_no"].'" />
                                                <input type="hidden"  name="'.$ded_type.'_grn_entries_id[]" value="'.$skuwise[$i]["grn_entries_id"].'" />
                                                <input type="hidden"  name="'.$ded_type.'_product_id[]" value="'.$skuwise[$i]["product_id"].'" />
                                                <input type="hidden"  name="'.$ded_type.'_bucket_name[]" value="'.$skuwise[$i]["bucket_name"].'" />
                                                <input type="hidden"  name="'.$ded_type.'_prepare_go_id[]" value="'.$skuwise[$i]["prepare_go_id"].'" />
                                                <input type="hidden"  name="'.$ded_type.'_company_id[]" value="'.$skuwise[$i]["company_id"].'" />
                                                <input type="hidden"  name="'.$ded_type.'_created_by[]" value="'.$skuwise[$i]["created_by"].'" />
                                                <input type="hidden"  name="'.$ded_type.'_updated_by[]" value="'.$skuwise[$i]["updated_by"].'" />
                                                <input type="hidden"  name="'.$ded_type.'_created_date[]" value="'.$skuwise[$i]["created_date"].'" />
                                                <input type="hidden"  name="'.$ded_type.'_updated_date[]" value="'.$skuwise[$i]["updated_date"].'" />
                                                <input type="hidden"  name="'.$ded_type.'_is_active[]" value="'.$skuwise[$i]["is_active"].'" />
                                                <input type="hidden"  name="'.$ded_type.'_is_combo_items[]" value="'.$skuwise[$i]["is_combo_items"].'" />
                                                <input type="hidden"  name="'.$ded_type.'_order_qty[]" value="'.$skuwise[$i]["order_qty"].'" />
                                                <input type="hidden"  name="'.$ded_type.'_order_id[]" value="'.$skuwise[$i]["order_id"].'" />
                                                <input type="hidden"  name="'.$ded_type.'_order_id[]" value="'.$skuwise[$i]["order_id"].'" />
                                                <input type="hidden"  name="'.$ded_type.'_value_at_mrp[]" value="'.$skuwise[$i]["value_at_mrp"].'" />
                                                </td>
                                                <td><input type="text" class="'.$ded_type.'_asin_code_'.$sr_no.' valid" id="'.$ded_type.'_asin_code_'.$i.'" name="'.$ded_type.'_asin_code[]" value="'.$skuwise[$i]['asin'].'" readonly
                                                    aria-invalid="false"></td>
                                                <td><input type="text" style="border: 1px solid #ddd!important;" class="'.$ded_type.'_hsn_code_'.$sr_no.'" id="'.$ded_type.'_hsn_code_'.$i.'" name="'.$ded_type.'_hsn_code[]" value="'.$skuwise[$i]['hsn_code'].'"/></td>
                                                <td><input type="text" class="'.$ded_type.'_batch_code_'.$sr_no.' valid" id="'.$ded_type.'_batch_code_'.$i.'" name="'.$ded_type.'_batch_code[]" value="'.$skuwise[$i]['batch_code'].'" readonly
                                                    aria-invalid="false"></td>
                                                <td ><input type="text" class="'.$ded_type.'_expiry_date_'.$sr_no.'" id="'.$ded_type.'_expiry_date_'.$i.'" name="'.$ded_type.'_expiry_date[]" value="" readonly /></td>
                                                <td><input type="text" class="'.$ded_type.'_psku_'.$sr_no.' valid" id="'.$ded_type.'_psku_'.$i.'" name="'.$ded_type.'_psku[]" value="'.$skuwise[$i]['psku'].'" readonly
                                                    aria-invalid="false"></td>
                                                <td><input type="text" class="'.$ded_type.'_fnsku_'.$sr_no.' valid" id="'.$ded_type.'_fnsku_'.$i.'" name="'.$ded_type.'_fnsku[]" value="'.$skuwise[$i]['fnsku'].'" readonly
                                                    aria-invalid="false"></td>
                                                <td><input type="text" class="'.$ded_type.'_product_title_'.$sr_no.'" id="'.$ded_type.'_product_title_'.$i.'" name="'.$ded_type.'_product_title[]" value="'.$skuwise[$i]["product_title"].'" readonly /></td>
                                                <td><input type="text" class="'.$ded_type.'_qty_'.$sr_no.' edit-sku" id="'.$ded_type.'_qty_'.$i.'" name="'.$ded_type.'_qty[]" value="' . $mycomponent->format_money($skuwise[$i]["invoice_qty"],2) . '" readonly/>
                                                </td>
                                                <td><input type="text" class="'.$ded_type.'_mrp_'.$sr_no.'" id="'.$ded_type.'_mrp_'.$i.'" name="'.$ded_type.'_mrp[]" value="'.$mycomponent->format_money($skuwise[$i]["mrp"],4).'" readonly /></td>
                                                <td><input type="text" style="border: 1px solid #ddd!important;" class="'.$ded_type.'_cost_excl_tax_per_unit_'.$sr_no.'" id="'.$ded_type.'_cost_excl_tax_per_unit_'.$i.'" name="'.$ded_type.'_cost_excl_tax_per_unit[]" value="'.$skuwise[$i]["per_unit_exc_tax"].'" onChange="set_sku_details2(this)"/></td>
                                                 <td><input type="text" class="'.$ded_type.'_cost_excl_tax_'.$sr_no.'" id="'.$ded_type.'_cost_excl_tax_'.$i.'" name="'.$ded_type.'_cost_excl_tax[]" value="'.$mycomponent->format_money($skuwise[$i]["cost_excl_vat"],2).'" readonly/>
                                                 <input type="hidden" class="'.$ded_type.'_cost_inc_tax_'.$sr_no.'" id="'.$ded_type.'_cost_inc_tax_'.$i.'" name="'.$ded_type.'_cost_inc_tax[]" value="'.$mycomponent->format_money($skuwise[$i]["per_unit"],2).'" readonly/>
                                                 </td>
                                                 <td>
                                                 <input type="hidden" class="'.$ded_type.'_old_percen_'.$sr_no.'" id="'.$ded_type.'_old_percen_'.$i.'" name="'.$ded_type.'_old_percen_tax[]" value="'.$vat_percen.'" />
                                                 <select  class="form-control select2 '.$ded_type.'_vat_percen_'.$sr_no.'" id="'.$ded_type.'_vat_percen_'.$i.'" name="'.$ded_type.'_vat_percen_tax[]" onChange="set_sku_details2(this)">
                                                <option value="">Select</option>';
                                                for($j=0; $j<count($tax_per); $j++) { 

                                                echo '<option value="'.round($tax_per[$j]['tax_rate'],2).'" '.($vat_percen==round($tax_per[$j]['tax_rate'],2)?"selected":'').' >
                                                    '.round($tax_per[$j]['tax_rate'],2).'</option>';
                                                 }
                                                echo '</select></td>
                                                <td><input type="text" class="'.$ded_type.'_cgst_rate_'.$sr_no.'" id="'.$ded_type.'_cgst_rate_'.$i.'" name="'.$ded_type.'_cgst_rate[]" value="'.$mycomponent->format_money($cgst_rate,2).'" readonly /></td>
                                                <td><input type="text" class="'.$ded_type.'_cgst_per_unit_'.$sr_no.'" id="'.$ded_type.'_cgst_'.$i.'" name="'.$ded_type.'_cgst[]" value="'.$mycomponent->format_money($skuwise[$i]["total_cgst"],2).'" readonly /></td>
                                                <td><input type="text" class="'.$ded_type.'_sgst_rate_'.$sr_no.'" id="'.$ded_type.'_sgst_rate_'.$i.'" name="'.$ded_type.'_sgst_rate[]" value="'.$mycomponent->format_money($sgst_rate,2).'" readonly /></td>
                                                <td><input type="text" class="'.$ded_type.'_sgst_'.$sr_no.'" id="'.$ded_type.'_sgst_'.$i.'" name="'.$ded_type.'_sgst_[]" value="'.$mycomponent->format_money($skuwise[$i]["total_sgst"],2).'" readonly />
                                                </td>
                                                 <td><input type="text" class="'.$ded_type.'_igst_rate_'.$sr_no.'" id="'.$ded_type.'_igst_rate_'.$i.'" name="'.$ded_type.'_igst_rate[]" value="'.$mycomponent->format_money($igst_rate,2).'" readonly />
                                                </td>
                                                 <td><input type="text" class="'.$ded_type.'_igst_'.$sr_no.'" id="'.$ded_type.'_igst_'.$i.'" name="'.$ded_type.'_igst[]" value="'.$mycomponent->format_money($skuwise[$i]["total_igst"],2).'" readonly /></td>
                                                
                                                <td><input type="text" class="'.$ded_type.'_total_'.$sr_no.'" id="'.$ded_type.'_total_'.$i.'" name="'.$ded_type.'_total[]" value="'.$total_amount.'" readonly /></td>
                                                </tr>';
                                    $sr_no++;
                                }
                            ?>
                        </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-danger" id="close_shortage_modal">Save</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ledger Modal -->
        <div class="modal fade" id="ledger_modal" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Ledger Details</h4>
                    </div>
                    <div class="modal-body"  >
                        <div class="modal-body-inside grn-view" id="ledger_details">
                            
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default  btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group btn-margin">
            <button type="submit" class="btn btn-sm btn-success" id="btn_submit">Create</button>
            <button type="button" class="btn btn-sm btn-info" id="get_ledger">View Ledger</button>
            <a class="btn btn-sm btn-danger pull-right" href="<?php echo Url::base(); ?>index.php?r=goodsoutward%2Findex">Close</a>
            <!-- <button type="button" class="btn   btn-sm  btn-info" id="view_debit_note">View Debit Note</button> -->
        </div>
        </form>
   </div>
</div>



<script type="text/javascript">
    var BASE_URL="<?php echo Url::base(); ?>";
    var invoices = "<?php echo count($invoice_details);?>";
    var taxes = "<?php echo $taxcount;?>";
</script>

<?php 
    $this->registerJsFile(
        '@web/js/jquery-ui-1.11.2/jquery-ui.min.js',
        ['depends' => [\yii\web\JqueryAsset::className()]]
    );
    $this->registerJsFile(
        '@web/js/good_outwards.js',
        ['depends' => [\yii\web\JqueryAsset::className()]]
    );
?>
