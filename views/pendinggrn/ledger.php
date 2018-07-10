<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Grn */


$this->title = 'Ledger Grn: ' . $grn_details[0]['grn_id'];
$this->params['breadcrumbs'][] = ['label' => 'Grns', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $grn_details[0]['grn_id'], 'url' => ['update', 'id' => $grn_details[0]['grn_id']]];
$this->params['breadcrumbs'][] = 'Ledger';
$mycomponent = Yii::$app->mycomponent;
?>

<style>
.table-head { font-weight:500;  
    background: #41ace9;
    color: #fff;
    border-bottom: 1px solid #41ace9;
    background-image: linear-gradient(#54b4eb, #2fa4e7 60%, #1d9ce5);
   }

.bold-text { font-weight:600; letter-spacing:.5px; background:#f9f9f9;   }
.btn-margin { margin-top:20px;}
.row-container { position:relative;  background:#f1f1f1; margin:0 0 10px; padding:10px 0; border-radius:3px;
background-image: linear-gradient(#54b4eb, #2fa4e7 60%, #1d9ce5); color:#fff;  border:1px solid #37a8e8;}
label { font-weight:normal;}
.actual_value {   padding:0px 3px;   color:#d82f2f; font-weight:400; text-align:right;}
.table > thead > tr:nth-child(2) {     background:none!important;  }
.modal-dialog .table  tr  td { min-width:60px;}
.table-bordered > thead > tr > th, .table-bordered > thead > tr > td { border-bottom-width:1px;  }
.modal-body { padding:10px; }
input { outline:none; background:none; width:100%; }
.edit-text { border:1px solid #ddd; padding:1px 5px;}
table tr th{font-weight: normal;}
table tr td:last-child {   padding:4px 10px; min-width:150px; }
table tr td:first-child  {   text-align:center;     }
table tr th:nth-child(1) { padding:4px 3px!important; width:55px;    }
table tr td:nth-child(3) {  width:150px;   }
 .total-amount { width:100%; padding:0; }
table tr td:last-child input{   border:1px solid #ddd;  padding-left:5px; }
.navbar-inverse .container { 	 width:100%;}
.wrap .container {    width:100%;}
.modal-lg { width:100%;}
.modal-dialog { margin:10px;}
.modal-content { border-radius:0;}
.modal-body  { padding:0;}
.modal-body-inside { max-width:1310px; overflow-y:hidden!important; margin:20px auto;}
.modal-body .table {  width:3200px; }
.close { outline:none;}
.diversion {   /*box-shadow: 0 0 5px rgba(0,0,0,.1);   padding:3px 10px 10px 10px; */  margin:20px 0;}
@media only screen and (min-width:250px) and (max-width:420px) { 
.diversion  { width:100%; overflow-x:scroll;}
.grn-view .table { width:500px; padding:10px;}
}

@media only screen and (min-width:250px) and (max-width:767px) {
	.col-xs-6 {   padding:5px 10px; } 
	.row-container { padding:0;}
	label { margin:0;}
	.table-container { max-width:700px; overflow-x:scroll;}
	.table-container table{ width:1200px;   } 
	.navbar-collapse.in { overflow:hidden!important;}
	}

@media print{#btn_close, #btn_print{ display : none }}
</style>

<div class="grn-view">  
    <div class=" col-md-12"> 
        <a class="btn btn-sm btn-danger pull-left" id="btn_close" href="<?php echo Url::base(); ?>index.php?r=pendinggrn%2Findex">Close</a>
        <button type="button" class="btn btn-sm btn-info pull-right" id="btn_print" onclick="javascript:window.print();">Print</button>
        <h3 class="text-center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Primarc Pecan Retail Pvt. Ltd.</h3>
        <h5 class="text-center">Mumbai</h5>
        <h5 class="text-center">GRN Posting Voucher</h5>
        <div>Vendor Name: <?php echo $grn_details[0]['vendor_name']; ?></div>
        <div class="pull-left">Grn Id: <?php echo $grn_details[0]['grn_id']; ?></div>
        <div class="pull-right">Posting Date: <?php if(isset($acc_ledger_entries[0]["ref_date"])) echo (($acc_ledger_entries[0]["ref_date"]!=null && $acc_ledger_entries[0]["ref_date"]!='')?date('d/m/Y',strtotime($acc_ledger_entries[0]["ref_date"])):date('d/m/Y')); else echo date('d/m/Y'); ?></div>
        

    <?php //echo count($acc_ledger_entries); ?> 
    <?php $rows = ""; $new_invoice_no = ""; $invoice_no = ""; $debit_amt=0; $credit_amt=0; $sr_no=1;
        $total_debit_amt=0; $total_credit_amt=0; 
        $table_arr = array(); $table_cnt = 0;

        for($i=0; $i<count($acc_ledger_entries); $i++) {
            $rows = $rows . '<tr>
                                <td>' . ($sr_no++) . '</td>
                                <td>' . $acc_ledger_entries[$i]["voucher_id"] . '</td>
                                <td>' . $acc_ledger_entries[$i]["ledger_name"] . '</td>
                                <td>' . $acc_ledger_entries[$i]["ledger_code"] . '</td>';

            if($acc_ledger_entries[$i]["type"]=="Debit") {
                $debit_amt = $debit_amt + $acc_ledger_entries[$i]["amount"];
                $total_debit_amt = $total_debit_amt + $acc_ledger_entries[$i]["amount"];
                $rows = $rows . '<td class="text-right">'.$mycomponent->format_money($acc_ledger_entries[$i]["amount"],2).'</td>';
            } else {
                $rows = $rows . '<td></td>';
            }

            if($acc_ledger_entries[$i]["type"]=="Credit") {
                $credit_amt = $credit_amt + $acc_ledger_entries[$i]["amount"];
                $total_credit_amt = $total_credit_amt + $acc_ledger_entries[$i]["amount"];
                $rows = $rows . '<td class="text-right">'.$mycomponent->format_money($acc_ledger_entries[$i]["amount"],2).'</td>';
            } else {
                $rows = $rows . '<td></td>';
            }

            $rows = $rows . '</tr>';

            if($acc_ledger_entries[$i]["entry_type"]=="Total Amount" || $acc_ledger_entries[$i]["entry_type"]=="Total Deduction"){
                if($acc_ledger_entries[$i]["entry_type"]=="Total Amount"){
                    $particular = "Total Purchase Amount";
                } else {
                    $particular = "Total Deduction Amount";
                    
                    $debit_amt = $debit_amt - ($total_debit_amt*2);
                    $credit_amt = $credit_amt - ($total_credit_amt*2);
                }

                $rows = $rows . '<tr class="bold-text text-right">
                                    <td colspan="4" style="text-align:right;">'.$particular.'</td>
                                    <td class="bold-text text-right">'.$mycomponent->format_money($total_debit_amt,2).'</td>
                                    <td class="bold-text text-right">'.$mycomponent->format_money($total_credit_amt,2).'</td>';
                $rows = $rows . '<tr><td colspan="6"></td></tr>';

                $total_debit_amt = 0;
                $total_credit_amt = 0;
                $sr_no=1;

                if($acc_ledger_entries[$i]["entry_type"]=="Total Amount"){
                    $rows = $rows . '<tr class="bold-text text-right">
                                        <td colspan="6" style="text-align:left;">Deduction Entry</td>
                                    </tr>';
                }
            }

            $blFlag = false;
            if(($i+1)==count($acc_ledger_entries)){
                $blFlag = true;
            } else if($acc_ledger_entries[$i]["invoice_no"]!=$acc_ledger_entries[$i+1]["invoice_no"]){
                $blFlag = true;
            }

            if($blFlag == true){
                $rows = '<tr class="bold-text text-right">
                            <td colspan="6" style="text-align:left;">Purchase Entry</td>
                        </tr>' . $rows;

                $invoice_date = '';
                if(isset($acc_ledger_entries[0]["invoice_date"])) {
                    if(($acc_ledger_entries[0]["invoice_date"]!=null && $acc_ledger_entries[0]["invoice_date"]!='')){
                        $invoice_date = date('d/m/Y',strtotime($acc_ledger_entries[0]["invoice_date"]));
                    }
                }
                
                $debit_amt = round($debit_amt,2);
                $credit_amt = round($credit_amt,2);

                $table = '<div class="diversion">
                            <div class="pull-left">Invoice No: ' . $acc_ledger_entries[$i]["invoice_no"] . '</div>
                            <div class="pull-right">Invoice Date: ' . $invoice_date . '</div>
                            <table class="table table-bordered">
                                <tr class="table-head">
                                    <th>Sr. No.</th>
                                    <th>Voucher No</th>
                                    <th>Ledger Name</th>
                                    <th>Ledger Code</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                </tr>
                                ' . $rows . '
                                <tr class="bold-text text-right">
                                    <td colspan="4" style="text-align:right;">Total Amount</td>
                                    <td>' . $mycomponent->format_money($debit_amt,2) . '</td>
                                    <td>' . $mycomponent->format_money($credit_amt,2) . '</td>
                                </tr>
                            </table>
                        </div>';

                echo $table;
                $table_arr[$table_cnt] = $table;
                $table_cnt = $table_cnt + 1;

                $rows=""; $debit_amt=0; $credit_amt=0; $sr_no=1;
            }
        }
         ?>
 </div>
</div>