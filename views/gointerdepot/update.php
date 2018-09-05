<style type="text/css">
.text-right, .text-right input { border:none; }

</style>

<?php

use yii\helpers\Html;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model app\models\Grn */

$this->title = 'Update Goods Outward Inter Depot: ' . $grn_details[0]['gi_go_id'];//$grn_details[0]['grn_id'];
$this->params['breadcrumbs'][] = ['label' => 'Goods Outward Inter Depot', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $grn_details[0]['grn_id'], 'url' => ['update', 'id' => $grn_details[0]['grn_id']]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="grn-update"> 
		 
    <?= $this->render('_form', ['grn_details' => $grn_details, 'total_val' => $total_val, 'total_tax' => $total_tax, 
    							'invoice_details' => $invoice_details, 'invoice_tax' => $invoice_tax, 'narration' => $narration, 'narration2' => $narration2, 
                                'deductions' => $deductions, 'acc_master' => $acc_master, 'acc' => $acc, 
                                'debit_note' => $debit_note, 'action' => $action, 'ware_array'=>$ware_array, 'skuwise' => $skuwise,
                                'tax_per'=>$tax_per, 'ware_array2'=>$ware_array, 'total_val2' => $total_val2, 'total_tax2' => $total_tax2, 
                                'invoice_details2' => $invoice_details2, 'invoice_tax2' => $invoice_tax2]) ?>
 
</div>