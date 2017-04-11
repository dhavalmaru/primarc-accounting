<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\GrnEntries */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="grn-entries-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'ean')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'asin')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fnsku')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'marketplace_title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'product_title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'vat_cst')->dropDownList([ 'VAT' => 'VAT', 'CST' => 'CST', 'NO TAX' => 'NO TAX', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'is_active')->textInput() ?>

    <?= $form->field($model, 'msku')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'psku')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'product_type')->dropDownList([ 'Product' => 'Product', 'Combo' => 'Combo', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'list_price')->textInput() ?>

    <?= $form->field($model, 'box_price')->textInput() ?>

    <?= $form->field($model, 'cost_excl_vat')->textInput() ?>

    <?= $form->field($model, 'vat_percen')->textInput() ?>

    <?= $form->field($model, 'cost_incl_vat_cst')->textInput() ?>

    <?= $form->field($model, 'po_qty')->textInput() ?>

    <?= $form->field($model, 'proper_qty')->textInput() ?>

    <?= $form->field($model, 'status')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'damaged_qty')->textInput() ?>

    <?= $form->field($model, 'is_sku_adjustment')->textInput() ?>

    <?= $form->field($model, 'total_qty')->textInput() ?>

    <?= $form->field($model, 'shortage_qty')->textInput() ?>

    <?= $form->field($model, 'excess_qty')->textInput() ?>

    <?= $form->field($model, 'po_shortage_qty')->textInput() ?>

    <?= $form->field($model, 'po_excess_qty')->textInput() ?>

    <?= $form->field($model, 'invoice_qty')->textInput() ?>

    <?= $form->field($model, 'manufacturing_date')->textInput() ?>

    <?= $form->field($model, 'expiry_date')->textInput() ?>

    <?= $form->field($model, 'expiry_issue')->dropDownList([ 'Y' => 'Y', 'N' => 'N', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'remarks')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'issues')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'expiry_qty')->textInput() ?>

    <?= $form->field($model, 'mrp_issue_qty')->textInput() ?>

    <?= $form->field($model, 'percent_expiry_left')->textInput() ?>

    <?= $form->field($model, 'expiry_months_left')->textInput() ?>

    <?= $form->field($model, 'grn_id')->textInput() ?>

    <?= $form->field($model, 'company_id')->textInput() ?>

    <?= $form->field($model, 'min_percentage_shelf_life_required')->textInput() ?>

    <?= $form->field($model, 'min_no_of_months_shelf_life_required')->textInput() ?>

    <?= $form->field($model, 'created_by')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'updated_by')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'created_date')->textInput() ?>

    <?= $form->field($model, 'updated_date')->textInput() ?>

    <?= $form->field($model, 'issue_type')->dropDownList([ 'MRP' => 'MRP', 'DAMAGED' => 'DAMAGED', 'EXPIRY' => 'EXPIRY', 'HAZMAT' => 'HAZMAT', 'LISTING' => 'LISTING', 'GOOD' => 'GOOD', 'OTHER' => 'OTHER', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'other_type')->dropDownList([ 'ASIN' => 'ASIN', 'INVALID' => 'INVALID', 'HAZMAT' => 'HAZMAT', 'LOADING' => 'LOADING', 'BUCKET1' => 'BUCKET1', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'invoice_no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'physical_allocation')->textInput() ?>

    <?= $form->field($model, 'removal_qty')->textInput() ?>

    <?= $form->field($model, 'challan_qty')->textInput() ?>

    <?= $form->field($model, 'removal_shortage_qty')->textInput() ?>

    <?= $form->field($model, 'removal_excess_qty')->textInput() ?>

    <?= $form->field($model, 'challan_shortage_qty')->textInput() ?>

    <?= $form->field($model, 'challan_excess_qty')->textInput() ?>

    <?= $form->field($model, 'is_bulk_upload')->dropDownList([ 'yes' => 'Yes', 'no' => 'No', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'is_first_scan')->textInput() ?>

    <?= $form->field($model, 'initial_invoice_qty')->textInput() ?>

    <?= $form->field($model, 'demand_qty')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
