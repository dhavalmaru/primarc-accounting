<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\GrnSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="grn-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'grn_id') ?>

    <?= $form->field($model, 'gi_id') ?>

    <?= $form->field($model, 'gi_date') ?>

    <?= $form->field($model, 'vat_cst') ?>

    <?= $form->field($model, 'grn_start_date_time') ?>

    <?php // echo $form->field($model, 'vendor_id') ?>

    <?php // echo $form->field($model, 'vendor_name') ?>

    <?php // echo $form->field($model, 'customer_name') ?>

    <?php // echo $form->field($model, 'customer_type') ?>

    <?php // echo $form->field($model, 'invoice_sku') ?>

    <?php // echo $form->field($model, 'scanned_sku') ?>

    <?php // echo $form->field($model, 'category_id') ?>

    <?php // echo $form->field($model, 'category_name') ?>

    <?php // echo $form->field($model, 'grn_no') ?>

    <?php // echo $form->field($model, 'invoice_qty') ?>

    <?php // echo $form->field($model, 'invoice_amt') ?>

    <?php // echo $form->field($model, 'scanned_qty') ?>

    <?php // echo $form->field($model, 'location') ?>

    <?php // echo $form->field($model, 'company_id') ?>

    <?php // echo $form->field($model, 'po_no') ?>

    <?php // echo $form->field($model, 'warehouse_id') ?>

    <?php // echo $form->field($model, 'idt_warehouse_code') ?>

    <?php // echo $form->field($model, 'user_id') ?>

    <?php // echo $form->field($model, 'user_name') ?>

    <?php // echo $form->field($model, 'summary_user_id') ?>

    <?php // echo $form->field($model, 'summary_user_name') ?>

    <?php // echo $form->field($model, 'invoice_val_bef_tax') ?>

    <?php // echo $form->field($model, 'invoice_val_after_tax') ?>

    <?php // echo $form->field($model, 'payable_val_before_tax') ?>

    <?php // echo $form->field($model, 'payable_val_after_tax') ?>

    <?php // echo $form->field($model, 'gi_type') ?>

    <?php // echo $form->field($model, 'manufacturer_id') ?>

    <?php // echo $form->field($model, 'manufacturer') ?>

    <?php // echo $form->field($model, 'received_sku') ?>

    <?php // echo $form->field($model, 'received_qty') ?>

    <?php // echo $form->field($model, 'shortage') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'is_active') ?>

    <?php // echo $form->field($model, 'remarks') ?>

    <?php // echo $form->field($model, 'summary_status') ?>

    <?php // echo $form->field($model, 'summary_docket') ?>

    <?php // echo $form->field($model, 'shipment_date') ?>

    <?php // echo $form->field($model, 'shipment_id') ?>

    <?php // echo $form->field($model, 'no_of_sku') ?>

    <?php // echo $form->field($model, 'sku') ?>

    <?php // echo $form->field($model, 'qty') ?>

    <?php // echo $form->field($model, 'no_of_boxes') ?>

    <?php // echo $form->field($model, 'vehicle_no') ?>

    <?php // echo $form->field($model, 'delivery_challan_no') ?>

    <?php // echo $form->field($model, 'warehouse_bal') ?>

    <?php // echo $form->field($model, 'goods_not_shipped') ?>

    <?php // echo $form->field($model, 'unresolved_unit') ?>

    <?php // echo $form->field($model, 'sellable_units') ?>

    <?php // echo $form->field($model, 'damaged_units') ?>

    <?php // echo $form->field($model, 'expired_units') ?>

    <?php // echo $form->field($model, 'sellable_amt') ?>

    <?php // echo $form->field($model, 'unsellable_amt') ?>

    <?php // echo $form->field($model, 'asin_issue_units') ?>

    <?php // echo $form->field($model, 'listing_issue_units') ?>

    <?php // echo $form->field($model, 'hazmat_issue_units') ?>

    <?php // echo $form->field($model, 'mrp_issue_units') ?>

    <?php // echo $form->field($model, 'marketplace_removed_units') ?>

    <?php // echo $form->field($model, 'excess_units') ?>

    <?php // echo $form->field($model, 'percent_fill_rate') ?>

    <?php // echo $form->field($model, 'total_issues') ?>

    <?php // echo $form->field($model, 'grn_end_date_time') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

    <?php // echo $form->field($model, 'created_date') ?>

    <?php // echo $form->field($model, 'updated_date') ?>

    <?php // echo $form->field($model, 'approver_comments') ?>

    <?php // echo $form->field($model, 'approver_user_id') ?>

    <?php // echo $form->field($model, 'approver_name') ?>

    <?php // echo $form->field($model, 'grn_approved_date_time') ?>

    <?php // echo $form->field($model, 'from_warehouse_name') ?>

    <?php // echo $form->field($model, 'interdepot_type') ?>

    <?php // echo $form->field($model, 'removal_id') ?>

    <?php // echo $form->field($model, 'challan_no') ?>

    <?php // echo $form->field($model, 'from_warehouse_code') ?>

    <?php // echo $form->field($model, 'is_bulk_upload') ?>

    <?php // echo $form->field($model, 'combo_type') ?>

    <?php // echo $form->field($model, 'combo_child_sku') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
