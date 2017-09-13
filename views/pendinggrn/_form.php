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
/*-----------------------*/
#expiry_sku_details { width: 2500px; }
#expiry_sku_details tr td input { border: none; outline: none; }
#expiry_sku_details .table-bordered>thead>tr>th, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>tbody>tr>td, .table-bordered>tfoot>tr>td{ border:1px solid #ddd!important; }
#expiry_sku_details tr td  select { width: 100%;  border:1px solid #ddd!important;  outline: none;}
#expiry_sku_details tr td:nth-child(25) input, #expiry_sku_details tr td:nth-child(54) input { border: 1px solid #ddd!important; outline: none; }
#expiry_sku_details tr td:nth-child(54) { width: 400px; }
/*----------------------*/
#damaged_sku_details { width: 2500px; }
#damaged_sku_details tr td input { border: none; outline: none; }
#damaged_sku_details .table-bordered>thead>tr>th, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>tbody>tr>td, .table-bordered>tfoot>tr>td{ border:1px solid #ddd!important; }
#damaged_sku_details tr td  select { width: 100%;  border:1px solid #ddd!important;  outline: none;}
#damaged_sku_details tr td:nth-child(25) input, #damaged_sku_details tr td:nth-child(54) input { border: 1px solid #ddd!important; outline: none; }
#damaged_sku_details tr td:nth-child(54) { width: 400px; }
/*----------------------*/
#margindiff_sku_details { width: 3000px; }
#margindiff_sku_details tr td input { border: none; outline: none; }
#margindiff_sku_details .table-bordered>thead>tr>th, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>tbody>tr>td, .table-bordered>tfoot>tr>td{ border:1px solid #ddd!important; }
#margindiff_sku_details tr td  select { width: 100%;  border:1px solid #ddd!important;  outline: none;}
#margindiff_sku_details tr td:nth-child(47) input, #margindiff_sku_details tr td:nth-child(54) input { border: 1px solid #ddd!important; outline: none; }
#margindiff_sku_details tr td:nth-child(54) { width: 400px; }
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
        <form id="form_purchase_details" action="<?php echo Url::base(); ?>index.php?r=pendinggrn%2Fsave" method="post" onkeypress="return event.keyCode != 13;">
        <!-- <form id="form_purchase_details" action="<?php //echo Url::base(); ?>index.php?r=pendinggrn%2Fgetgrnparticulars" method="post"> -->
    
        <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />

        <div class="row    row-container">
            <div class="form-group">
    			<div class="col-md-2 col-sm-2 col-xs-6">
                    <label class=" control-label">Posting Date </label> 
                    <div class=" "> 
    					<input style="background-color: #fff;" type="text" class="form-control datepicker" name="gi_date" id="gi_date" value="<?php if(isset($grn_details)) { if($grn_details[0]['grn_date']!=null && $grn_details[0]['grn_date']!='') echo date('d/m/Y',strtotime($grn_details[0]['grn_date'])); else echo (($grn_details[0]['gi_date']!=null && $grn_details[0]['gi_date']!='')?date('d/m/Y',strtotime($grn_details[0]['gi_date'])):date('d/m/Y')); } else echo date('d/m/Y'); ?>" readonly />
                        <input type="hidden" class="form-control" name="no_of_invoices" id="no_of_invoices" value="<?= count($invoice_details) ?>" />
                    </div>
    			</div>
    			<div class="col-md-2 col-sm-2 col-xs-6">
                    <label class="control-label">Grn No. </label> 
                    <div class=" ">
                        <input type="hidden" class="form-control" name="gi_id" id="grn_id" placeholder="Grn No" value="<?= $grn_details[0]['grn_id'] ?>" />
                        <input type="hidden" class="form-control" name="action" id="action" placeholder="Action" value="<?= $action ?>" />
                        <input type="text" class="form-control" id="gi_id" placeholder="Grn No" value="<?= $grn_details[0]['gi_id'] ?>" readonly />
                    </div>
                </div>
		        <div class="col-md-2 col-sm-2 col-xs-6">
                    <label class=" control-label">Vendor Name </label> 
                    <div class=" "> 
    					<input type="hidden" class="form-control" name="vendor_id" id="vendor_id" value="<?= $grn_details[0]['vendor_id'] ?>" /> 
                        <input type="hidden" class="form-control" name="vendor_code" id="vendor_code" value="<?= $grn_details[0]['vendor_code'] ?>" /> 
                        <input type="text" class="form-control" name="vendor_name" id="vendor_name" value="<?= $grn_details[0]['vendor_name'] ?>" readonly />  
                    </div>
                </div>
    		    <div class="col-md-2 col-sm-2 col-xs-6">
                    <label class=" control-label">Category Name </label> 
                    <div class=" "> 
    					 <input type="text" class="form-control" name="category_name" id="category_name"   value="<?= $grn_details[0]['category_name'] ?>" readonly /> 
                    </div>
                </div>
    			<div class="col-md-2 col-sm-2 col-xs-6">
                    <label class=" control-label">Location </label> 
                  <div class=" "> 
    				 <input type="text" class="form-control" name="location" id="location"   value="<?= $grn_details[0]['location'] ?>" readonly />  
                  </div>
    			</div>
    			<div class="col-md-2 col-sm-2 col-xs-6">
                    <label class="control-label">Vat/Cst </label>
    			 
                    <div class=" "> 
    					 <input type="text" class="form-control" name="vat_cst" id="vat_cst"   value="<?= $grn_details[0]['vat_cst'] ?>" readonly />
                    </div>
               </div>
            </div>
            <div class="form-group" id="form_errors_group" style="display:none;">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <label class="control-label">&nbsp;</label>
                    <div id="form_errors" style="display:none; color:#E04B4A;" class="error"></div>
               </div>
            </div>
        </div>
    	
        <?php 
            $intra_state_style = "";
            $inter_state_style = "";
            if(strtoupper($grn_details[0]['vat_cst'])=="INTRA"){
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
                            <input type="hidden" id="invoice_no_<?php echo $i;?>" name="invoice_no[]" value="<?php echo $invoice_details[$i]['invoice_no']; ?>" />
                            <span>Invoice</span> <br/> <span class="actual_value"> (<?php echo $invoice_details[$i]['invoice_no']; ?>) </span>    </th>
                        <th style="width: 150px;"><span>Edited</span> <br/> <span class="actual_value"> (<?php echo $invoice_details[$i]['invoice_no']; ?>) </span>  </span></th>
                        <th><span>Difference</span> <br/> <span class="actual_value"> (<?php echo $invoice_details[$i]['invoice_no']; ?>) </span>  </span></th>
                    <?php } ?>
                    <th>Narration</th>
                </tr>

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
                            if($invoice_details[$k]['invoice_no']==$invoice_tax[$i]['invoice_no']) {
                                if($total_tax[$j]['tax_zone_code']==$invoice_tax[$i]['tax_zone_code'] && 
                                    // $total_tax[$j]['vat_cst'] == $invoice_tax[$i]['vat_cst'] && 
                                    floatval($total_tax[$j]['vat_percen']) == floatval($invoice_tax[$i]['vat_percen'])) {

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
                                                <input type="text" class="text-right edit-text" id="edited_'.$k.'_cost_'.$j.'" name="edited_cost_'.$j.'[]" value="'.$mycomponent->format_money($invoice_tax[$i]['edited_cost'], 2).'" onChange="getDifference(this);" />
                                            </td>
                                            <td>
                                                <input type="text" class="text-right" id="diff_'.$k.'_cost_'.$j.'" name="diff_cost_'.$j.'[]" value="'.$mycomponent->format_money($invoice_tax[$i]['diff_cost'], 2).'" readonly />
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
                                                <input type="text" class="text-right" id="diff_'.$k.'_tax_'.$j.'" name="diff_tax_'.$j.'[]" value="'.$mycomponent->format_money($invoice_tax[$i]['diff_tax'], 2).'" readonly />
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
                                                <input type="text" class="text-right" id="diff_'.$k.'_cgst_'.$j.'" name="diff_cgst_'.$j.'[]" value="'.$mycomponent->format_money($invoice_tax[$i]['diff_cgst'], 2).'" readonly />
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
                                                <input type="text" class="text-right" id="diff_'.$k.'_sgst_'.$j.'" name="diff_sgst_'.$j.'[]" value="'.$mycomponent->format_money($invoice_tax[$i]['diff_sgst'], 2).'" readonly />
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
                                                <input type="text" class="text-right" id="diff_'.$k.'_igst_'.$j.'" name="diff_igst_'.$j.'[]" value="'.$mycomponent->format_money($invoice_tax[$i]['diff_igst'], 2).'" readonly />
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
                                            <input type="text" class="text-right" id="diff_'.$inv_num.'_cost_'.$j.'" name="diff_cost_'.$j.'[]" value="0.00" readonly />
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
                                            <input type="text" class="text-right" id="diff_'.$inv_num.'_tax_'.$j.'" name="diff_tax_'.$j.'[]" value="0.00" readonly />
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
                                            <input type="text" class="text-right" id="diff_'.$inv_num.'_cgst_'.$j.'" name="diff_cgst_'.$j.'[]" value="0.00" readonly />
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
                                            <input type="text" class="text-right" id="diff_'.$inv_num.'_sgst_'.$j.'" name="diff_sgst_'.$j.'[]" value="0.00" readonly />
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
                                            <input type="text" class="text-right" id="diff_'.$inv_num.'_igst_'.$j.'" name="diff_igst_'.$j.'[]" value="0.00" readonly />
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
                        <input type="hidden" id="vat_percen_<?php echo $j;?>" name="vat_percen[]" value="<?php echo $total_tax[$j]['vat_percen']; ?>" />
                        <input type="hidden" id="cgst_rate_<?php echo $j;?>" name="cgst_rate[]" value="<?php echo $total_tax[$j]['cgst_rate']; ?>" />
                        <input type="hidden" id="sgst_rate_<?php echo $j;?>" name="sgst_rate[]" value="<?php echo $total_tax[$j]['sgst_rate']; ?>" />
                        <input type="hidden" id="igst_rate_<?php echo $j;?>" name="igst_rate[]" value="<?php echo $total_tax[$j]['igst_rate']; ?>" />
                        <input type="hidden" id="sub_particular_cost_<?php echo $j;?>" name="sub_particular_cost[]" value="<?php echo 'Purchase_'.$total_tax[$j]['tax_zone_code'].'_'.$total_tax[$j]['vat_cst'].'_'.$total_tax[$j]['vat_percen']; ?>" />
                        <?php //echo 'Purchase_'.$total_tax[$j]['tax_zone_code'].'_'.$total_tax[$j]['vat_cst'].'_'.$total_tax[$j]['vat_percen']; ?>
                        <?php echo $mycomponent->format_money($total_tax[$j]['vat_percen'],2); ?>
                    </td>
                    <td class="sticky-cell text-right" style="border: none!important;">
                        <input type="text" class="text-right" id="total_cost_<?php echo $j;?>" name="total_cost_<?php echo $j;?>" value="<?php echo $mycomponent->format_money($total_tax[$j]['total_cost'], 2); ?>" readonly />
                    </td>
                    <?php echo $total_tax[$j]['invoice_cost_td']; ?>
                    <td>
                        <input type="text" id="narration_<?php echo $j;?>" name="narration_cost_<?php echo $j;?>" value="<?php echo $narration[$j]['cost']; ?>" />
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
                        <input type="hidden" id="sub_particular_tax_<?php echo $j;?>" name="sub_particular_tax[]" value="<?php echo 'Tax_'.$total_tax[$j]['tax_zone_code'].'_'.$total_tax[$j]['vat_percen']; ?>" />
                        <?php //echo 'Tax_'.$total_tax[$j]['tax_zone_code'].'_'.$total_tax[$j]['vat_cst'].'_'.$total_tax[$j]['vat_percen']; ?>
                        <?php echo $mycomponent->format_money($total_tax[$j]['vat_percen'],2); ?>
                    </td>
                    <td class="sticky-cell text-right" style="border: none!important;">
                        <input type="text" class="text-right " id="total_tax_<?php echo $j;?>" name="total_tax_<?php echo $j;?>" value="<?php echo $mycomponent->format_money($total_tax[$j]['total_tax'], 2); ?>" readonly />
                    </td>
                    <?php echo $total_tax[$j]['invoice_tax_td']; ?>
                    <td>
                        <input type="text" id="narration_<?php echo $j;?>" name="narration_tax_<?php echo $j;?>" value="<?php echo $narration[$j]['tax']; ?>" />
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
                        <input type="text" id="narration_<?php echo $j;?>" name="narration_cgst_<?php echo $j;?>" value="<?php echo $narration[$j]['cgst']; ?>" />
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
                        <input type="text" id="narration_<?php echo $j;?>" name="narration_sgst_<?php echo $j;?>" value="<?php echo $narration[$j]['sgst']; ?>" />
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
                        <input type="text" id="narration_<?php echo $j;?>" name="narration_igst_<?php echo $j;?>" value="<?php echo $narration[$j]['igst']; ?>" />
                    </td>
                </tr>
                <?php } ?>

                <tr>
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
                            <input type="text" class="text-right edit-text edited_other_charges" id="edited_other_charges_<?php echo $i;?>" name="edited_other_charges[]" value="<?php echo $mycomponent->format_money($invoice_details[$i]['edited_other_charges'], 2); ?>" onChange="getDifference(this);" />
                        </td>
                        <td>
                            <input type="text" class="text-right" id="diff_other_charges_<?php echo $i;?>" name="diff_other_charges[]" value="<?php echo $mycomponent->format_money($invoice_details[$i]['diff_other_charges'], 2); ?>" readonly />
                        </td>
                    <?php } ?>
                    <td>
                        <input type="text" id="narration_other_charges" name="narration_other_charges" value="<?php echo $narration['narration_other_charges']; ?>" />
                    </td>
                </tr>
                <tr class="bold-text" style=" ">
                    <td class="sticky-cell" style="border: none!important; background-color: #f9f9f9;"></td>
                    <td class="sticky-cell" style="border: none!important; background-color: #f9f9f9;">Total Amount</td>
                    <td class="sticky-cell" style="border: none!important; background-color: #f9f9f9;">
                        <select id="totalamount_acc_id_0" class="form-control acc_id" name="total_amount_acc_id" onChange="get_acc_details(this)" style="display: none;">
                            <option value="">Select</option>
                            <?php for($i=0; $i<count($acc_master); $i++) { 
                                    if($acc['total_amount_acc_id']==""){
                                        if($grn_details[0]['vendor_id']==$acc_master[$i]['vendor_id']) {
                                            $acc['total_amount_acc_id'] = $acc_master[$i]['id'];
                                            $acc['total_amount_ledger_name'] = $acc_master[$i]['legal_name'];
                                            $acc['total_amount_ledger_code'] = $acc_master[$i]['code'];
                                        }
                                    }
                            ?>
                            <option value="<?php echo $acc_master[$i]['id']; ?>" <?php if($acc['total_amount_acc_id']==$acc_master[$i]['id']) echo 'selected'; ?>><?php echo $acc_master[$i]['legal_name']; ?></option>
                            <?php } ?>
                        </select>
                        <input type="hidden" id="totalamount_ledger_name_0" name="total_amount_ledger_name" value="<?php echo $acc['total_amount_ledger_name']; ?>" />
                        <?php echo $acc['total_amount_ledger_name']; ?>
                    </td>
                    <td class="sticky-cell" style="border: none!important; background-color: #f9f9f9;">
                        <input type="text" id="totalamount_ledger_code_0" name="total_amount_ledger_code" value="<?php echo $acc['total_amount_ledger_code']; ?>" style="display: none;" readonly />
                        <?php echo $acc['total_amount_ledger_code']; ?>
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
               
                <tr>
                    <td class="sticky-cell" style="border: none!important;">4</td>
                    <td class="sticky-cell" style="border: none!important;">Less Amount - Shortage</td>
                    <td class="sticky-cell" style="border: none!important;" align="center"><button type="button" class="btn btn-info btn-xs  " id="get_shortage_qty">Edit</button></td>
                    <td class="sticky-cell" style="text-align: center; border: none!important;"><i id="shortage_validation_icon" class="fa fa-close" style="display: none; color: #dd4b39;"></i></td>
                    <td class="sticky-cell" style="border: none!important;"></td>
                    <td class="sticky-cell" style="border: none!important;">
                        <input type="text" class="text-right" id="shortage_amount" name="shortage_amount" value="<?php echo $mycomponent->format_money($total_val[0]['shortage_amount'], 2); ?>" readonly />
                    </td>
                    <?php for($i=0; $i<count($invoice_details); $i++) { ?>
                        <td>
                            <input type="text" class="text-right" id="invoice_shortage_amount_<?php echo $i;?>" name="invoice_shortage_amount[]" value="<?php echo $mycomponent->format_money($invoice_details[$i]['invoice_shortage_amount'], 2); ?>" readonly />
                        </td>
                        <td>
                            <input type="text" class="text-right" id="edited_shortage_amount_<?php echo $i;?>" name="edited_shortage_amount[]" value="<?php echo $mycomponent->format_money($invoice_details[$i]['edited_shortage_amount'], 2); ?>" onChange="getDifference(this);" readonly />
                        </td>
                        <td>
                            <input type="text" class="text-right" id="diff_shortage_amount_<?php echo $i;?>" name="diff_shortage_amount[]" value="<?php echo $mycomponent->format_money($invoice_details[$i]['diff_shortage_amount'], 2); ?>" readonly />
                        </td>
                    <?php } ?>
                    <td>
                        <input type="text" id="narration_shortage_amount" name="narration_shortage_amount" value="<?php echo $narration['narration_shortage_amount']; ?>" />
                    </td>
                </tr>
                <tr>
                    <td class="sticky-cell" style="border: none!important;">5</td>
                    <td class="sticky-cell" style="border: none!important;">Less Amount - Expiry</td>
                    <td class="sticky-cell" align="center" style="border: none!important;"><button type="button" class="btn btn-info btn-xs " id="get_expiry_qty">Edit</button></td>
                    <td class="sticky-cell" style="text-align: center; border: none!important;"><i id="expiry_validation_icon" class="fa fa-close" style="display: none; color: #dd4b39;"></i></td>
                    <td class="sticky-cell" style="border: none!important;"></td>
                    <td class="sticky-cell" style="border: none!important;">
                        <input type="text" class="text-right" id="expiry_amount" name="expiry_amount" value="<?php echo $mycomponent->format_money($total_val[0]['expiry_amount'], 2); ?>" readonly />
                    </td>
                    <?php for($i=0; $i<count($invoice_details); $i++) { ?>
                        <td>
                            <input type="text" class="text-right" id="invoice_expiry_amount_<?php echo $i;?>" name="invoice_expiry_amount[]" value="<?php echo $mycomponent->format_money($invoice_details[$i]['invoice_expiry_amount'], 2); ?>" readonly />
                        </td>
                        <td>
                            <input type="text" class="text-right" id="edited_expiry_amount_<?php echo $i;?>" name="edited_expiry_amount[]" value="<?php echo $mycomponent->format_money($invoice_details[$i]['edited_expiry_amount'], 2); ?>" onChange="getDifference(this);" readonly />
                        </td>
                        <td>
                            <input type="text" class="text-right" id="diff_expiry_amount_<?php echo $i;?>" name="diff_expiry_amount[]" value="<?php echo $mycomponent->format_money($invoice_details[$i]['diff_expiry_amount'], 2); ?>" readonly />
                        </td>
                    <?php } ?>
                    <td>
                        <input type="text" id="narration_expiry_amount" name="narration_expiry_amount" value="<?php echo $narration['narration_expiry_amount']; ?>" />
                    </td>
                </tr>
                <tr>
                    <td class="sticky-cell" style="border: none!important;">6</td>
                    <td class="sticky-cell" style="border: none!important;">Less Amount - Damage</td>
                    <td class="sticky-cell" align="center" style="border: none!important;"><button type="button" class="btn btn-info btn-xs" id="get_damaged_qty">Edit</button></td>
                    <td class="sticky-cell" style="text-align: center; border: none!important;"><i id="damaged_validation_icon" class="fa fa-close" style="display: none; color: #dd4b39;"></i></td>
                    <td class="sticky-cell" style="border: none!important;"></td>
                    <td class="sticky-cell" style="border: none!important;">
                        <input type="text" class="text-right" id="damaged_amount" name="damaged_amount" value="<?php echo $mycomponent->format_money($total_val[0]['damaged_amount'], 2); ?>" readonly />
                    </td>
                    <?php for($i=0; $i<count($invoice_details); $i++) { ?>
                        <td>
                            <input type="text" class="text-right" id="invoice_damaged_amount_<?php echo $i;?>" name="invoice_damaged_amount[]" value="<?php echo $mycomponent->format_money($invoice_details[$i]['invoice_damaged_amount'], 2); ?>" readonly />
                        </td>
                        <td>
                            <input type="text" class="text-right" id="edited_damaged_amount_<?php echo $i;?>" name="edited_damaged_amount[]" value="<?php echo $mycomponent->format_money($invoice_details[$i]['edited_damaged_amount'], 2); ?>" onChange="getDifference(this);" readonly />
                        </td>
                        <td>
                            <input type="text" class="text-right" id="diff_damaged_amount_<?php echo $i;?>" name="diff_damaged_amount[]" value="<?php echo $mycomponent->format_money($invoice_details[$i]['diff_damaged_amount'], 2); ?>" readonly />
                        </td>
                    <?php } ?>
                    <td>
                        <input type="text" id="narration_damaged_amount" name="narration_damaged_amount" value="<?php echo $narration['narration_damaged_amount']; ?>" />
                    </td>
                </tr>
                <tr>
                    <td class="sticky-cell" style="border: none!important;">7</td>
                    <td class="sticky-cell" style="border: none!important;">Less Amount - Margin Diff</td>
                    <td class="sticky-cell" align="center" style="border: none!important;"><button type="button" class="btn btn-info btn-xs " id="get_margindiff_qty">Edit</button></td>
                    <td class="sticky-cell" style="text-align: center; border: none!important;"><i id="margindiff_validation_icon" class="fa fa-close" style="display: none; color: #dd4b39;"></i></td>
                    <td class="sticky-cell" style="border: none!important;"></td>
                    <td class="sticky-cell" style="border: none!important;">
                        <input type="text" class="text-right" id="margindiff_amount" name="margindiff_amount" value="<?php echo $mycomponent->format_money($total_val[0]['margindiff_amount'], 2); ?>" readonly />
                    </td>
                    <?php for($i=0; $i<count($invoice_details); $i++) { ?>
                        <td>
                            <input type="text" class="text-right" id="invoice_margindiff_amount_<?php echo $i;?>" name="invoice_margindiff_amount[]" value="<?php echo $mycomponent->format_money($invoice_details[$i]['invoice_margindiff_amount'], 2); ?>" readonly />
                        </td>
                        <td>
                            <input type="text" class="text-right" id="edited_margindiff_amount_<?php echo $i;?>" name="edited_margindiff_amount[]" value="<?php echo $mycomponent->format_money($invoice_details[$i]['edited_margindiff_amount'], 2); ?>" onChange="getDifference(this);" readonly />
                        </td>
                        <td>
                            <input type="text" class="text-right" id="diff_margindiff_amount_<?php echo $i;?>" name="diff_margindiff_amount[]" value="<?php echo $mycomponent->format_money($invoice_details[$i]['diff_margindiff_amount'], 2); ?>" readonly />
                        </td>
                    <?php } ?>
                    <td>
                        <input type="text" id="narration_margindiff_amount" name="narration_margindiff_amount" value="<?php echo $narration['narration_margindiff_amount']; ?>" />
                    </td>
                </tr>
         
                <tr class="bold-text" >
                    <td class="sticky-cell" style="border: none!important; background-color: #f9f9f9;"></td>
                    <td class="sticky-cell" style="border: none!important; background-color: #f9f9f9;">Total Deduction</td>
                    <td class="sticky-cell" style="border: none!important; background-color: #f9f9f9;">
                        <select id="totaldeduction_acc_id_0" class="form-control acc_id" name="total_deduction_acc_id" onChange="get_acc_details(this)" style="display: none;">
                            <option value="">Select</option>
                            <?php for($i=0; $i<count($acc_master); $i++) { 
                                    if($acc['total_deduction_acc_id']==""){
                                        if($grn_details[0]['vendor_id']==$acc_master[$i]['vendor_id']) {
                                            $acc['total_deduction_acc_id'] = $acc_master[$i]['id'];
                                            $acc['total_deduction_ledger_name'] = $acc_master[$i]['legal_name'];
                                            $acc['total_deduction_ledger_code'] = $acc_master[$i]['code'];
                                        }
                                    }
                            ?>
                            <option value="<?php echo $acc_master[$i]['id']; ?>" <?php if($acc['total_deduction_acc_id']==$acc_master[$i]['id']) echo 'selected'; ?>><?php echo $acc_master[$i]['legal_name']; ?></option>
                            <?php } ?>
                        </select>
                        <input type="hidden" id="totaldeduction_ledger_name_0" name="total_deduction_ledger_name" value="<?php echo $acc['total_deduction_ledger_name']; ?>" />
                        <?php echo $acc['total_deduction_ledger_name']; ?>
                    </td>
                    <td class="sticky-cell" style="border: none!important; background-color: #f9f9f9;">
                        <input type="text" id="totaldeduction_ledger_code_0" name="total_deduction_ledger_code" value="<?php echo $acc['total_deduction_ledger_code']; ?>" style="display: none;" readonly />
                        <?php echo $acc['total_deduction_ledger_code']; ?>
                    </td>
                    <td class="sticky-cell" style="border: none!important; background-color: #f9f9f9;"></td>
                    <td class="sticky-cell" style="border: none!important; background-color: #f9f9f9;">
                        <input type="text" class="text-right" id="total_deduction" name="total_deduction" value="<?php echo $mycomponent->format_money($total_val[0]['total_deduction'], 2); ?>" readonly />
                    </td>
                    <?php for($i=0; $i<count($invoice_details); $i++) { ?>
                        <td>
                            <input type="text" class="text-right" id="invoice_total_deduction_<?php echo $i;?>" name="invoice_total_deduction[]" value="<?php echo $mycomponent->format_money($invoice_details[$i]['invoice_total_deduction'], 2); ?>" readonly />
                            <input type="hidden" id="total_deduction_voucher_id_<?php echo $i;?>" name="total_deduction_voucher_id[]" value="<?php echo $invoice_details[$i]['total_deduction_voucher_id']; ?>" />
                            <input type="hidden" id="total_deduction_ledger_type_<?php echo $i;?>" name="total_deduction_ledger_type[]" value="<?php echo $invoice_details[$i]['total_deduction_ledger_type']; ?>" />
                        </td>
                        <td>
                            <input type="text" class="text-right" id="edited_total_deduction_<?php echo $i;?>" name="edited_total_deduction[]" value="<?php echo $mycomponent->format_money($invoice_details[$i]['edited_total_deduction'], 2); ?>" onChange="getDifference(this);" readonly />
                        </td>
                        <td>
                            <input type="text" class="text-right" id="diff_total_deduction_<?php echo $i;?>" name="diff_total_deduction[]" value="<?php echo $mycomponent->format_money($invoice_details[$i]['diff_total_deduction'], 2); ?>" readonly />
                        </td>
                    <?php } ?>
                    <td>
                        <input type="text" id="narration_total_deduction" name="narration_total_deduction" value="<?php echo $narration['narration_total_deduction']; ?>" />
                    </td>
                </tr>
                
                <tr class="bold-text" >
                    <td class="sticky-cell" style="border: none!important; background-color: #f9f9f9;"></td>
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
        <!-- Shortage Modal -->
        <div class="modal fade" id="shortage_modal" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Shortage Deductions</h4>
                    </div>
                    <div class="modal-body" style=" ">
    				    <div class="modal-body-inside"  >
                        <?php echo $deductions['shortage']; ?>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-danger" id="close_shortage_modal">Save</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Expiry Modal -->
        <div class="modal fade" id="expiry_modal" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Expiry Deductions</h4>
                    </div>
                    <div class="modal-body" >
    				    <div class="modal-body-inside"  >
                            <?php echo $deductions['expiry']; ?>
                        </div>
    				</div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-danger" id="close_expiry_modal">Save</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Damaged Modal -->
        <div class="modal fade" id="damaged_modal" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Damaged Deductions</h4>
                    </div>
                    <div class="modal-body" style=" ">
    				    <div class="modal-body-inside"  >
                            <?php echo $deductions['damaged']; ?>
                        </div>
    				</div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-danger" id="close_damaged_modal">Save</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Margin Diff Modal -->
        <div class="modal fade" id="margindiff_modal" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Margin Diff Deductions</h4>
                    </div>
                    <div class="modal-body"  >
    				    <div class="modal-body-inside"  >
                            <?php echo $deductions['margindiff']; ?>
    					</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-danger" id="close_margindiff_modal">Save</button>
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
            <a class="btn btn-sm btn-danger pull-right" href="<?php echo Url::base(); ?>index.php?r=pendinggrn%2Findex">Close</a>
            <!-- <button type="button" class="btn   btn-sm  btn-info" id="view_debit_note">View Debit Note</button> -->
        </div>
        </form>

        <?php if(isset($debit_note)) { if(count($debit_note)>0) { ?>
        <div class="table-container">
        <h4>Debit Notes</h4>
        <table id="debit_note" class="table table-bordered">
            <tr class="table-head">
                <th>Sr. No.</th>
                <th>Invoice No</th>
                <th>Invoice Date</th>
                <th>Total Deduction</th>
                <th style="width: 125px;">View</th>
                <th style="width: 125px;">Download</th>
                <th style="width: 125px;">Email</th>
            </tr>
            <?php for($i=0; $i<count($debit_note); $i++) { ?>
            <tr>
                <td><?php echo $i+1; ?></td>
                <td><?php echo $debit_note[$i]['invoice_no']; ?></td>
                <td><?php echo (($debit_note[$i]['invoice_date']!=null && $debit_note[$i]['invoice_date']!='')?
                                date('d/m/Y',strtotime($debit_note[$i]['invoice_date'])):''); ?></td>
                <td><?php echo $mycomponent->format_money($debit_note[$i]['total_deduction'],2); ?></td>
                <td><a href="<?php echo Url::base(); ?>index.php?r=pendinggrn%2Fviewdebitnote&invoice_id=<?php echo $debit_note[$i]['gi_go_invoice_id']; ?>" target="_blank">View</a></td>
                <td><a href="<?php echo Url::base(); ?>index.php?r=pendinggrn%2Fdownload&invoice_id=<?php echo $debit_note[$i]['gi_go_invoice_id']; ?>" target="_blank">Download</a></td>
                <td><a href="<?php echo Url::base(); ?>index.php?r=pendinggrn%2Femaildebitnote&invoice_id=<?php echo $debit_note[$i]['gi_go_invoice_id']; ?>">Email</a></td>
            </tr>
            <?php } ?>
        </table>
        </div>
        <?php }} ?>
   </div>
</div>

<script type="text/javascript">
    var BASE_URL="<?php echo Url::base(); ?>";
    var invoices = "<?php echo count($invoice_details);?>";
    var taxes = "<?php echo count($total_tax);?>";
</script>

<?php 
    $this->registerJsFile(
        '@web/js/jquery-ui-1.11.2/jquery-ui.min.js',
        ['depends' => [\yii\web\JqueryAsset::className()]]
    );
    $this->registerJsFile(
        '@web/js/pending_grn.js',
        ['depends' => [\yii\web\JqueryAsset::className()]]
    );
?>