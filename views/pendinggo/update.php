<style type="text/css">
.text-right, .text-right input { border:none; }

</style>

<?php

use yii\helpers\Html;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model app\models\Grn */

<<<<<<< HEAD
$this->title = 'Update Go: ' . $grn_details[0]['gi_go_id'];
$this->params['breadcrumbs'][] = ['label' => 'GO', 'url' => ['index']];
=======
$this->title = 'Update Go: ' . '';//$grn_details[0]['grn_id'];
$this->params['breadcrumbs'][] = ['label' => 'Grns', 'url' => ['index']];
>>>>>>> 40251667d20641f61579b49c4e0131e7351baf6f
//$this->params['breadcrumbs'][] = ['label' => $grn_details[0]['grn_id'], 'url' => ['update', 'id' => $grn_details[0]['grn_id']]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="grn-update"> 
		 
    <?= $this->render('_form', ['grn_details' => $grn_details, 'total_val' => $total_val, 'total_tax' => $total_tax, 
    							'invoice_details' => $invoice_details, 'invoice_tax' => $invoice_tax, 'narration' => $narration, 
                                'deductions' => $deductions, 'acc_master' => $acc_master, 'acc' => $acc, 
                                'debit_note' => $debit_note, 'action' => $action]) ?>
 
</div>