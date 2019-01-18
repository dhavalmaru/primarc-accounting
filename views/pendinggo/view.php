<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Grn */

$this->title = $model->grn_id;
$this->params['breadcrumbs'][] = ['label' => 'GO', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="grn-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->grn_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->grn_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'grn_id',
            'gi_id',
            'gi_date',
            'vat_cst',
            'grn_start_date_time',
            'vendor_id',
            'vendor_name',
            'customer_name',
            'customer_type',
            'invoice_sku',
            'scanned_sku',
            'category_id',
            'category_name',
            'grn_no',
            'invoice_qty',
            'invoice_amt',
            'scanned_qty',
            'location',
            'company_id',
            'po_no',
            'warehouse_id',
            'idt_warehouse_code',
            'user_id',
            'user_name',
            'summary_user_id',
            'summary_user_name',
            'invoice_val_bef_tax',
            'invoice_val_after_tax',
            'payable_val_before_tax',
            'payable_val_after_tax',
            'gi_type',
            'manufacturer_id',
            'manufacturer',
            'received_sku',
            'received_qty',
            'shortage',
            'status',
            'is_active',
            'remarks',
            'summary_status',
            'summary_docket',
            'shipment_date',
            'shipment_id',
            'no_of_sku',
            'sku',
            'qty',
            'no_of_boxes',
            'vehicle_no',
            'delivery_challan_no',
            'warehouse_bal',
            'goods_not_shipped',
            'unresolved_unit',
            'sellable_units',
            'damaged_units',
            'expired_units',
            'sellable_amt',
            'unsellable_amt',
            'asin_issue_units',
            'listing_issue_units',
            'hazmat_issue_units',
            'mrp_issue_units',
            'marketplace_removed_units',
            'excess_units',
            'percent_fill_rate',
            'total_issues',
            'grn_end_date_time',
            'created_by',
            'updated_by',
            'created_date',
            'updated_date',
            'approver_comments',
            'approver_user_id',
            'approver_name',
            'grn_approved_date_time',
            'from_warehouse_name',
            'interdepot_type',
            'removal_id',
            'challan_no',
            'from_warehouse_code',
            'is_bulk_upload',
            'combo_type',
            'combo_child_sku',
        ],
    ]) ?>

</div>
