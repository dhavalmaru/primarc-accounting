<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\GrnEntries */

$this->title = $model->grn_entries_id;
$this->params['breadcrumbs'][] = ['label' => 'Grn Entries', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="grn-entries-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->grn_entries_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->grn_entries_id], [
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
            'grn_entries_id',
            'ean',
            'asin',
            'fnsku',
            'marketplace_title',
            'product_title',
            'vat_cst',
            'is_active',
            'msku',
            'psku',
            'product_type',
            'list_price',
            'box_price',
            'cost_excl_vat',
            'vat_percen',
            'cost_incl_vat_cst',
            'po_qty',
            'proper_qty',
            'status',
            'damaged_qty',
            'is_sku_adjustment',
            'total_qty',
            'shortage_qty',
            'excess_qty',
            'po_shortage_qty',
            'po_excess_qty',
            'invoice_qty',
            'manufacturing_date',
            'expiry_date',
            'expiry_issue',
            'remarks',
            'issues',
            'expiry_qty',
            'mrp_issue_qty',
            'percent_expiry_left',
            'expiry_months_left',
            'grn_id',
            'company_id',
            'min_percentage_shelf_life_required',
            'min_no_of_months_shelf_life_required',
            'created_by',
            'updated_by',
            'created_date',
            'updated_date',
            'issue_type',
            'other_type',
            'invoice_no',
            'physical_allocation',
            'removal_qty',
            'challan_qty',
            'removal_shortage_qty',
            'removal_excess_qty',
            'challan_shortage_qty',
            'challan_excess_qty',
            'is_bulk_upload',
            'is_first_scan',
            'initial_invoice_qty',
            'demand_qty',
        ],
    ]) ?>

</div>
