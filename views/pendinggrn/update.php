<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Grn */

$this->title = 'Update Grn: ' . $grn_details[0]['grn_id'];
$this->params['breadcrumbs'][] = ['label' => 'Grns', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $grn_details[0]['grn_id'], 'url' => ['view', 'id' => $grn_details[0]['grn_id']]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="grn-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', ['grn_details' => $grn_details, 'total_val' => $total_val, 'total_tax' => $total_tax, 
    							'invoice_details' => $invoice_details, 'invoice_tax' => $invoice_tax]) ?>

</div>
