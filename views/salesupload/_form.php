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
    #form_sale_upload_details .error {color: #dd4b39!important;}
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
    /*table tr td:last-child input{   border:1px solid #ddd;  padding-left:5px; }*/
     
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
        <form id="form_sale_upload_details" action="<?php echo Url::base(); ?>index.php?r=salesupload%2Fsave" method="post" onkeypress="return event.keyCode != 13;">
    
        <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />

        <div class="row row-container">
            <div class="form-group">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <?php //echo json_encode($invoice_tax); ?>
                </div>
                <div class="col-md-2 col-sm-2 col-xs-6">
                    <label class=" control-label">Posting Date </label> 
                    <div class=" "> 
                        <input style="background-color: #fff;" type="text" class="form-control datepicker" name="date_of_upload" id="date_of_upload" value="<?php if(isset($upload_details)) { if($upload_details[0]['date_of_upload']!=null && $upload_details[0]['date_of_upload']!='') echo date('d/m/Y',strtotime($upload_details[0]['date_of_upload'])); } else echo date('d/m/Y'); ?>" readonly />
                        <input type="hidden" class="form-control" name="file_id" id="file_id" placeholder="File Id" value="<?= $upload_details[0]['id'] ?>" />
                        <input type="hidden" class="form-control" name="action" id="action" placeholder="Action" value="<?= $action ?>" />
                        <input type="hidden" class="form-control" name="no_of_invoices" id="no_of_invoices" value="<?= count($invoices) ?>" />
                        <input type="hidden" class="form-control" name="no_of_marketplaces" id="no_of_marketplaces" value="<?= count($marketplaces) ?>" />
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
        
        <div id="update_grn_div" class="table-container sticky-table sticky-headers sticky-ltr-cells">
            <?php 
            for($k=0; $k<count($data); $k++) { 
                // $invoice_no = $data[$k]['invoice_no'];
                $marketplace = $data[$k]['marketplace'];
                $item_details = $data[$k]['item_details'];
            ?>
            <!-- <div>
                <input type="hidden" id="invoice_no_<?php //echo $k;?>" name="invoice_no_<?php //echo $k;?>" value="<?php //echo $data[$k]['invoice_no']; ?>" />
                Invoice No: <?php //echo $invoice_no; ?>
            </div> -->
            <table id="update_grn" class="table table-bordered">
                <tr class="table-head">
                    <th class="sticky-cell" style="border: none!important;">Sr. No.</th>
                    <th class="sticky-cell" style="border: none!important;">Particulars</th>
                    <th class="sticky-cell" style="width: 250px; border: none!important;">Ledger Name</th>
                    <th class="sticky-cell" style="width: 200px; border: none!important;">Ledger Code</th>
                    <th class="sticky-cell" style="width: 25px; border: none!important;">Tax <br/> Percent</th>
                    <th class="sticky-cell" style="width: 25px; border: none!important;">From State</th>
                    <th class="sticky-cell" style="width: 25px; border: none!important;">To State</th>
                    <?php for($j=0; $j<count($marketplace); $j++) { ?>
                        <th class="text-center">
                            <?php echo $marketplace[$j]['market_place']; ?>
                            <input type="hidden" id="marketplace_acc_id_<?php echo $k;?>_<?php echo $j;?>" name="marketplace_acc_id_<?php echo $k;?>_<?php echo $j;?>" value="<?php echo $marketplace[$j]['acc_id']; ?>" />
                            <input type="hidden" id="marketplace_acc_code_<?php echo $k;?>_<?php echo $j;?>" name="marketplace_acc_code_<?php echo $k;?>_<?php echo $j;?>" value="<?php echo $marketplace[$j]['acc_code']; ?>" />
                            <input type="hidden" id="marketplace_acc_legal_name_<?php echo $k;?>_<?php echo $j;?>" name="marketplace_acc_legal_name_<?php echo $k;?>_<?php echo $j;?>" value="<?php echo $marketplace[$j]['acc_legal_name']; ?>" />
                            <input type="hidden" id="marketplace_voucher_id_<?php echo $k;?>_<?php echo $j;?>" name="marketplace_voucher_id_<?php echo $k;?>_<?php echo $j;?>" value="<?php echo $marketplace[$j]['voucher_id']; ?>" />
                        </th>
                    <?php } ?>
                </tr>

                <?php for($i=0; $i<count($item_details); $i++) { ?>
                <tr>
                    <td class="sticky-cell" style="border: none!important;"><?php echo $i+1; ?></td>
                    <td class="sticky-cell" style="border: none!important;">
                        <input type="hidden" id="particular_<?php echo $k;?>_<?php echo $i;?>" name="particular_<?php echo $k;?>[]" value="<?php echo $item_details[$i]['particular']; ?>" />
                        <?php echo $item_details[$i]['particular']; ?>
                    </td>
                    <td class="sticky-cell" style="border: none!important;">
                        <select id="acc_id_<?php echo $k;?>_<?php echo $i;?>" class="form-control acc_id select2" name="acc_id_<?php echo $k;?>[]" onChange="get_acc_details(this)" data-error="#acc_id_<?php echo $k;?>_<?php echo $i;?>_error">
                            <option value="">Select</option>
                            <?php for($j=0; $j<count($acc_master); $j++) { 
                                    if($acc_master[$j]['type']==$item_details[$i]['acc_type']) { 
                            ?>
                            <option value="<?php echo $acc_master[$j]['id']; ?>" <?php if($item_details[$i]['acc_id']==$acc_master[$j]['id']) echo 'selected'; ?>><?php echo $acc_master[$j]['legal_name']; ?></option>
                            <?php }} ?>
                        </select>
                        <input type="hidden" id="ledger_name_<?php echo $k;?>_<?php echo $i;?>" name="ledger_name_<?php echo $k;?>[]" value="<?php echo $item_details[$i]['ledger_name']; ?>" />
                        <span id="acc_id_<?php echo $k;?>_<?php echo $i;?>_error" style="display: block;"></span>
                    </td>
                    <td class="sticky-cell" style="border: none!important;"><input type="text" id="ledger_code_<?php echo $k;?>_<?php echo $i;?>" name="ledger_code_<?php echo $k;?>[]" value="<?php echo $item_details[$i]['ledger_code']; ?>" style="border: none;" readonly /></td>
                    <td class="sticky-cell" style="border: none!important;">
                        <input type="hidden" id="tax_percent_<?php echo $k;?>_<?php echo $i;?>" name="tax_percent_<?php echo $k;?>[]" value="<?php echo $item_details[$i]['tax_percent']; ?>" />
                        <?php echo $mycomponent->format_money($item_details[$i]['tax_percent'],2); ?>
                    </td>
                    <td class="sticky-cell" style="border: none!important;">
                        <input type="hidden" id="ship_from_state_<?php echo $k;?>_<?php echo $i;?>" name="ship_from_state_<?php echo $k;?>[]" value="<?php echo $item_details[$i]['ship_from_state']; ?>" />
                        <?php echo $item_details[$i]['ship_from_state']; ?>
                    </td>
                    <td class="sticky-cell" style="border: none!important;">
                        <input type="hidden" id="ship_to_state_<?php echo $k;?>_<?php echo $i;?>" name="ship_to_state_<?php echo $k;?>[]" value="<?php echo $item_details[$i]['ship_to_state']; ?>" />
                        <?php echo $item_details[$i]['ship_to_state']; ?>
                    </td>
                    
                    <?php for($j=0; $j<count($marketplace); $j++) { ?>
                        <td class="sticky-cell text-right" style="border: none!important;">
                            <input type="text" class="text-right" id="amount_<?php echo $k;?>_<?php echo $j;?>_<?php echo $i;?>" name="amount_<?php echo $k;?>_<?php echo $j;?>[]" value="<?php echo $mycomponent->format_money($item_details[$i][$marketplace[$j]['marketplace_id']], 2); ?>" readonly />
                        </td>
                    <?php } ?>
                </tr>
                <?php } ?>
                
                <tr class="bold-text" >
                    <td class="sticky-cell" style="border: none!important; background-color: #f9f9f9;"></td>
                    <td class="sticky-cell" style="border: none!important; background-color: #f9f9f9;">Total Amount</td>
                    <td class="sticky-cell" style="border: none!important; background-color: #f9f9f9;"></td>
                    <td class="sticky-cell" style="border: none!important; background-color: #f9f9f9;"></td>
                    <td class="sticky-cell" style="border: none!important; background-color: #f9f9f9;"></td>
                    <td class="sticky-cell" style="border: none!important; background-color: #f9f9f9;"></td>
                    <td class="sticky-cell" style="border: none!important; background-color: #f9f9f9;"></td>
                    
                    <?php for($j=0; $j<count($marketplace); $j++) { ?>
                        <td class="sticky-cell text-right" style="border: none!important; background-color: #f9f9f9;">
                            <input type="text" class="text-right" id="total_amount_<?php echo $k;?>_<?php echo $j;?>" name="total_amount_<?php echo $k;?>_<?php echo $j;?>" value="<?php echo $mycomponent->format_money($marketplace[$j]['sales_incl_gst'], 2); ?>" readonly />
                        </td>
                    <?php } ?>
                </tr>
            </table>
            <br/>
            <?php } ?>
            
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
            <a class="btn btn-sm btn-danger pull-right" href="<?php echo Url::base(); ?>index.php?r=salesupload%2Findex">Close</a>
            <!-- <button type="button" class="btn   btn-sm  btn-info" id="view_debit_note">View Debit Note</button> -->
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
        '@web/js/sales_upload.js',
        ['depends' => [\yii\web\JqueryAsset::className()]]
    );
?>