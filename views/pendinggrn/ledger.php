<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

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
</style>

<div class="grn-view">  
    <div class=" col-md-12"> 
    <?php //echo count($grn_acc_ledger_entries); ?> 
    <?php $rows = ""; $new_invoice_no = ""; $invoice_no = ""; $debit_amt=0; $credit_amt=0; $sr_no=1;
        $total_debit_amt=0; $total_credit_amt=0; 
        $table_arr = array(); $table_cnt = 0;

        for($i=0; $i<count($grn_acc_ledger_entries); $i++) {
            $rows = $rows . '<tr>
                                <td>' . ($sr_no++) . '</td>
                                <td>' . $grn_acc_ledger_entries[$i]["ledger_name"] . '</td>
                                <td>' . $grn_acc_ledger_entries[$i]["ledger_code"] . '</td>';

            if($grn_acc_ledger_entries[$i]["type"]=="Debit") {
                $debit_amt = $debit_amt + $grn_acc_ledger_entries[$i]["amount"];
                $total_debit_amt = $total_debit_amt + $grn_acc_ledger_entries[$i]["amount"];
                $rows = $rows . '<td class="text-right">'.$mycomponent->format_money($grn_acc_ledger_entries[$i]["amount"],2).'</td>';
            } else {
                $rows = $rows . '<td></td>';
            }

            if($grn_acc_ledger_entries[$i]["type"]=="Credit") {
                $credit_amt = $credit_amt + $grn_acc_ledger_entries[$i]["amount"];
                $total_credit_amt = $total_credit_amt + $grn_acc_ledger_entries[$i]["amount"];
                $rows = $rows . '<td class="text-right">'.$mycomponent->format_money($grn_acc_ledger_entries[$i]["amount"],2).'</td>';
            } else {
                $rows = $rows . '<td></td>';
            }

            $rows = $rows . '</tr>';

            if($grn_acc_ledger_entries[$i]["entry_type"]=="Total Amount" || $grn_acc_ledger_entries[$i]["entry_type"]=="Total Deduction"){
                if($grn_acc_ledger_entries[$i]["entry_type"]=="Total Amount"){
                    $particular = "Total Purchase Amount";
                } else {
                    $particular = "Total Deduction Amount";
                    
                    $debit_amt = $debit_amt - ($total_debit_amt*2);
                    $credit_amt = $credit_amt - ($total_credit_amt*2);
                }

                $rows = $rows . '<tr class="bold-text text-right">
                                    <td colspan="3" style="text-align:right;">'.$particular.'</td>
                                    <td class="bold-text text-right">'.$mycomponent->format_money($total_debit_amt,2).'</td>
                                    <td class="bold-text text-right">'.$mycomponent->format_money($total_credit_amt,2).'</td>';
                $rows = $rows . '<tr><td colspan="5"></td></tr>';

                $total_debit_amt = 0;
                $total_credit_amt = 0;
                $sr_no=1;

                if($grn_acc_ledger_entries[$i]["entry_type"]=="Total Amount"){
                    $rows = $rows . '<tr class="bold-text text-right">
                                        <td colspan="5" style="text-align:left;">Deduction Entry</td>
                                    </tr>';
                }
            }

            $blFlag = false;
            if(($i+1)==count($grn_acc_ledger_entries)){
                $blFlag = true;
            } else if($grn_acc_ledger_entries[$i]["invoice_no"]!=$grn_acc_ledger_entries[$i+1]["invoice_no"]){
                $blFlag = true;
            }

            if($blFlag == true){
                $rows = '<tr class="bold-text text-right">
                            <td colspan="5" style="text-align:left;">Purchase Entry</td>
                        </tr>' . $rows;

                $table = '<div class="diversion"><h4 class=" ">Invoice No: ' . $grn_acc_ledger_entries[$i]["invoice_no"] . '</h4>
                        <table class="table table-bordered">
                            <tr class="table-head">
                                <th>Sr. No.</th>
                                <th>Ledger Name</th>
                                <th>Ledger Code</th>
                                <th>Debit</th>
                                <th>Credit</th>
                            </tr>
                            ' . $rows . '
                            <tr class="bold-text text-right">
                                <td colspan="3" style="text-align:right;">Total Amount</td>
                                <td>' . $mycomponent->format_money($debit_amt,2) . '</td>
                                <td>' . $mycomponent->format_money($credit_amt,2) . '</td>
                            </tr>
                        </table></div>';

                echo $table;
                $table_arr[$table_cnt] = $table;
                $table_cnt = $table_cnt + 1;

                $rows=""; $debit_amt=0; $credit_amt=0; $sr_no=1;
            }
        }
         ?>
 </div>
</div>