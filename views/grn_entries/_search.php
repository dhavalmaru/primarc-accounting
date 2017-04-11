<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\GrnEntriesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="grn-entries-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'grn_entries_id') ?>

    <?= $form->field($model, 'ean') ?>

    <?= $form->field($model, 'asin') ?>

    <?= $form->field($model, 'fnsku') ?>

    <?= $form->field($model, 'marketplace_title') ?>

    <?php // echo $form->field($model, 'product_title') ?>

    <?php // echo $form->field($model, 'vat_cst') ?>

    <?php // echo $form->field($model, 'is_active') ?>

    <?php // echo $form->field($model, 'msku') ?>

    <?php // echo $form->field($model, 'psku') ?>

    <?php // echo $form->field($model, 'product_type') ?>

    <?php // echo $form->field($model, 'list_price') ?>

    <?php // echo $form->field($model, 'box_price') ?>

    <?php // echo $form->field($model, 'cost_excl_vat') ?>

    <?php // echo $form->field($model, 'vat_percen') ?>

    <?php // echo $form->field($model, 'cost_incl_vat_cst') ?>

    <?php // echo $form->field($model, 'po_qty') ?>

    <?php // echo $form->field($model, 'proper_qty') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'damaged_qty') ?>

    <?php // echo $form->field($model, 'is_sku_adjustment') ?>

    <?php // echo $form->field($model, 'total_qty') ?>

    <?php // echo $form->field($model, 'shortage_qty') ?>

    <?php // echo $form->field($model, 'excess_qty') ?>

    <?php // echo $form->field($model, 'po_shortage_qty') ?>

    <?php // echo $form->field($model, 'po_excess_qty') ?>

    <?php // echo $form->field($model, 'invoice_qty') ?>

    <?php // echo $form->field($model, 'manufacturing_date') ?>

    <?php // echo $form->field($model, 'expiry_date') ?>

    <?php // echo $form->field($model, 'expiry_issue') ?>

    <?php // echo $form->field($model, 'remarks') ?>

    <?php // echo $form->field($model, 'issues') ?>

    <?php // echo $form->field($model, 'expiry_qty') ?>

    <?php // echo $form->field($model, 'mrp_issue_qty') ?>

    <?php // echo $form->field($model, 'percent_expiry_left') ?>

    <?php // echo $form->field($model, 'expiry_months_left') ?>

    <?php // echo $form->field($model, 'grn_id') ?>

    <?php // echo $form->field($model, 'company_id') ?>

    <?php // echo $form->field($model, 'min_percentage_shelf_life_required') ?>

    <?php // echo $form->field($model, 'min_no_of_months_shelf_life_required') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

    <?php // echo $form->field($model, 'created_date') ?>

    <?php // echo $form->field($model, 'updated_date') ?>

    <?php // echo $form->field($model, 'issue_type') ?>

    <?php // echo $form->field($model, 'other_type') ?>

    <?php // echo $form->field($model, 'invoice_no') ?>

    <?php // echo $form->field($model, 'physical_allocation') ?>

    <?php // echo $form->field($model, 'removal_qty') ?>

    <?php // echo $form->field($model, 'challan_qty') ?>

    <?php // echo $form->field($model, 'removal_shortage_qty') ?>

    <?php // echo $form->field($model, 'removal_excess_qty') ?>

    <?php // echo $form->field($model, 'challan_shortage_qty') ?>

    <?php // echo $form->field($model, 'challan_excess_qty') ?>

    <?php // echo $form->field($model, 'is_bulk_upload') ?>

    <?php // echo $form->field($model, 'is_first_scan') ?>

    <?php // echo $form->field($model, 'initial_invoice_qty') ?>

    <?php // echo $form->field($model, 'demand_qty') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
