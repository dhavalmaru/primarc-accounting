<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Grn */


$this->title = 'Ledger Debit Credit Note: ' . $ledger[0]['trans_id'];
$this->params['breadcrumbs'][] = ['label' => 'Debit Credit Note', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $ledger[0]['trans_id'], 'url' => ['edit', 'trans_id' => $ledger[0]['trans_id']]];
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

.bold-text { font-weight:600; letter-spacing:.5px; background:#f1f1f1;   }
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

    <h1 class=" "><?= Html::encode($this->title) ?></h1>

    <?php $rows = ""; $new_trans_id = ""; $trans_id = ""; $debit_amt=0; $credit_amt=0; $sr_no=1;

    for($i=0; $i<count($ledger); $i++) {
        $rows = $rows . '<tr>
                            <td>' . ($sr_no++) . '</td>
                            <td>' . $ledger[$i]["particular"] . '</td>';

        if($ledger[$i]["type"]=="Debit") {
            $debit_amt = $debit_amt + $ledger[$i]["amount"];
            $rows = $rows . '<td class=" text-right">'.$mycomponent->format_money($ledger[$i]["amount"],2).'</td>';
        } else {
            $rows = $rows . '<td></td>';
        }

        if($ledger[$i]["type"]=="Credit") {
            $credit_amt = $credit_amt + $ledger[$i]["amount"];
            $rows = $rows . '<td class=" text-right">'.$mycomponent->format_money($ledger[$i]["amount"],2).'</td>';
        } else {
            $rows = $rows . '<td></td>';
        }

        $rows = $rows . '</tr>';

        $blFlag = false;
        if(($i+1)==count($ledger)){
            $blFlag = true;
        } else if($ledger[$i]["trans_id"]!=$ledger[$i+1]["trans_id"]){
            $blFlag = true;
        }

        if($blFlag == true){
            $table = '<div class="diversion"><h4 class=" ">Transaction Id: ' . $ledger[$i]["trans_id"] . '</h4>
                    <table class="table table-bordered">
                        <tr class="table-head">
                            <th>Sr. No.</th>
                            <th>Particular</th>
                            <th >Debit</th>
                            <th>Credit</th>
                        </tr>
                        ' . $rows . '
                        <tr class="bold-text text-right">
					 
                            <td colspan="2"  style="text-align:right;" >Total Amount</td>
                            <td>' . $mycomponent->format_money($debit_amt,2) . '</td>
                            <td>' . $mycomponent->format_money($credit_amt,2) . '</td>
                        </tr>
                    </table></div>';

            echo $table;
			$rows=""; $debit_amt=0; $credit_amt=0; $sr_no=1;
        }
        
    } ?>
</div>
