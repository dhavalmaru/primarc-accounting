<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Grn */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="grn-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'gi_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'gi_date')->textInput() ?>

    <?= $form->field($model, 'vat_cst')->dropDownList([ 'VAT' => 'VAT', 'CST' => 'CST', 'NO TAX' => 'NO TAX', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'grn_start_date_time')->textInput() ?>

    <?= $form->field($model, 'vendor_id')->textInput() ?>

    <?= $form->field($model, 'vendor_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'customer_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'customer_type')->dropDownList([ 'B2C' => 'B2C', 'B2B' => 'B2B', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'invoice_sku')->textInput() ?>

    <?= $form->field($model, 'scanned_sku')->textInput() ?>

    <?= $form->field($model, 'category_id')->textInput() ?>

    <?= $form->field($model, 'category_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'grn_no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'invoice_qty')->textInput() ?>

    <?= $form->field($model, 'invoice_amt')->textInput() ?>

    <?= $form->field($model, 'scanned_qty')->textInput() ?>

    <?= $form->field($model, 'location')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'company_id')->textInput() ?>

    <?= $form->field($model, 'po_no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'warehouse_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'idt_warehouse_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'user_id')->textInput() ?>

    <?= $form->field($model, 'user_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'summary_user_id')->textInput() ?>

    <?= $form->field($model, 'summary_user_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'invoice_val_bef_tax')->textInput() ?>

    <?= $form->field($model, 'invoice_val_after_tax')->textInput() ?>

    <?= $form->field($model, 'payable_val_before_tax')->textInput() ?>

    <?= $form->field($model, 'payable_val_after_tax')->textInput() ?>

    <?= $form->field($model, 'gi_type')->dropDownList([ 'VENDOR' => 'VENDOR', 'CUSTOMER' => 'CUSTOMER', 'INTER-DEPOT' => 'INTER-DEPOT', 'OTHERS' => 'OTHERS', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'manufacturer_id')->textInput() ?>

    <?= $form->field($model, 'manufacturer')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'received_sku')->textInput() ?>

    <?= $form->field($model, 'received_qty')->textInput() ?>

    <?= $form->field($model, 'shortage')->textInput() ?>

    <?= $form->field($model, 'status')->dropDownList([ 'draft' => 'Draft', 'inprogress' => 'Inprogress', 'pending_approval' => 'Pending approval', 'approved' => 'Approved', 'rejected' => 'Rejected', 'sent_to_vendor' => 'Sent to vendor', 'closed' => 'Closed', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'is_active')->textInput() ?>

    <?= $form->field($model, 'remarks')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'summary_status')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'summary_docket')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'shipment_date')->textInput() ?>

    <?= $form->field($model, 'shipment_id')->textInput() ?>

    <?= $form->field($model, 'no_of_sku')->textInput() ?>

    <?= $form->field($model, 'sku')->textInput() ?>

    <?= $form->field($model, 'qty')->textInput() ?>

    <?= $form->field($model, 'no_of_boxes')->textInput() ?>

    <?= $form->field($model, 'vehicle_no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'delivery_challan_no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'warehouse_bal')->textInput() ?>

    <?= $form->field($model, 'goods_not_shipped')->textInput() ?>

    <?= $form->field($model, 'unresolved_unit')->textInput() ?>

    <?= $form->field($model, 'sellable_units')->textInput() ?>

    <?= $form->field($model, 'damaged_units')->textInput() ?>

    <?= $form->field($model, 'expired_units')->textInput() ?>

    <?= $form->field($model, 'sellable_amt')->textInput() ?>

    <?= $form->field($model, 'unsellable_amt')->textInput() ?>

    <?= $form->field($model, 'asin_issue_units')->textInput() ?>

    <?= $form->field($model, 'listing_issue_units')->textInput() ?>

    <?= $form->field($model, 'hazmat_issue_units')->textInput() ?>

    <?= $form->field($model, 'mrp_issue_units')->textInput() ?>

    <?= $form->field($model, 'marketplace_removed_units')->textInput() ?>

    <?= $form->field($model, 'excess_units')->textInput() ?>

    <?= $form->field($model, 'percent_fill_rate')->textInput() ?>

    <?= $form->field($model, 'total_issues')->textInput() ?>

    <?= $form->field($model, 'grn_end_date_time')->textInput() ?>

    <?= $form->field($model, 'created_by')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'updated_by')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'created_date')->textInput() ?>

    <?= $form->field($model, 'updated_date')->textInput() ?>

    <?= $form->field($model, 'approver_comments')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'approver_user_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'approver_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'grn_approved_date_time')->textInput() ?>

    <?= $form->field($model, 'from_warehouse_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'interdepot_type')->dropDownList([ 'internal' => 'Internal', 'market place' => 'Market place', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'removal_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'challan_no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'from_warehouse_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'is_bulk_upload')->dropDownList([ 'yes' => 'Yes', 'no' => 'No', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'combo_type')->dropDownList([ 'INDIVIDUAL' => 'INDIVIDUAL', 'BUNDLE' => 'BUNDLE', 'UNBUNDLE' => 'UNBUNDLE', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'combo_child_sku')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
