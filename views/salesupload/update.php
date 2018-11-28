<style type="text/css">
.text-right, .text-right input { border:none; }

</style>

<?php

use yii\helpers\Html;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model app\models\Grn */

$this->title = 'Sales Details';
$this->params['breadcrumbs'][] = ['label' => 'Sales', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $grn_details[0]['grn_id'], 'url' => ['update', 'id' => $grn_details[0]['grn_id']]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="grn-update"> 
		 
    <?= $this->render('_form', ['data' => $data, 'acc_master' => $acc_master, 'upload_details' => $upload_details, 
    				'invoices' => $invoices, 'marketplaces' => $marketplaces, 'action' => $action]) ?>
 
</div>