<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "grn_entries".
 *
 * @property integer $grn_entries_id
 * @property string $ean
 * @property string $asin
 * @property string $fnsku
 * @property string $marketplace_title
 * @property string $product_title
 * @property string $vat_cst
 * @property integer $is_active
 * @property string $msku
 * @property string $psku
 * @property string $product_type
 * @property double $list_price
 * @property double $box_price
 * @property double $cost_excl_vat
 * @property double $vat_percen
 * @property double $cost_incl_vat_cst
 * @property integer $po_qty
 * @property integer $proper_qty
 * @property string $status
 * @property integer $damaged_qty
 * @property integer $is_sku_adjustment
 * @property integer $total_qty
 * @property integer $shortage_qty
 * @property integer $excess_qty
 * @property integer $po_shortage_qty
 * @property integer $po_excess_qty
 * @property integer $invoice_qty
 * @property string $manufacturing_date
 * @property string $expiry_date
 * @property string $expiry_issue
 * @property string $remarks
 * @property string $issues
 * @property integer $expiry_qty
 * @property integer $mrp_issue_qty
 * @property double $percent_expiry_left
 * @property double $expiry_months_left
 * @property integer $grn_id
 * @property integer $company_id
 * @property double $min_percentage_shelf_life_required
 * @property integer $min_no_of_months_shelf_life_required
 * @property string $created_by
 * @property string $updated_by
 * @property string $created_date
 * @property string $updated_date
 * @property string $issue_type
 * @property string $other_type
 * @property string $invoice_no
 * @property integer $physical_allocation
 * @property integer $removal_qty
 * @property integer $challan_qty
 * @property integer $removal_shortage_qty
 * @property integer $removal_excess_qty
 * @property integer $challan_shortage_qty
 * @property integer $challan_excess_qty
 * @property string $is_bulk_upload
 * @property integer $is_first_scan
 * @property integer $initial_invoice_qty
 * @property integer $demand_qty
 *
 * @property GrnEntriesArchives[] $grnEntriesArchives
 */
class GrnEntries extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'grn_entries';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['vat_cst', 'product_type', 'expiry_issue', 'issue_type', 'other_type', 'is_bulk_upload'], 'string'],
            [['is_active', 'po_qty', 'proper_qty', 'damaged_qty', 'is_sku_adjustment', 'total_qty', 'shortage_qty', 'excess_qty', 'po_shortage_qty', 'po_excess_qty', 'invoice_qty', 'expiry_qty', 'mrp_issue_qty', 'grn_id', 'company_id', 'min_no_of_months_shelf_life_required', 'created_by', 'updated_by', 'physical_allocation', 'removal_qty', 'challan_qty', 'removal_shortage_qty', 'removal_excess_qty', 'challan_shortage_qty', 'challan_excess_qty', 'is_first_scan', 'initial_invoice_qty', 'demand_qty'], 'integer'],
            [['list_price', 'box_price', 'cost_excl_vat', 'vat_percen', 'cost_incl_vat_cst', 'percent_expiry_left', 'expiry_months_left', 'min_percentage_shelf_life_required'], 'number'],
            [['manufacturing_date', 'expiry_date', 'created_date', 'updated_date'], 'safe'],
            [['grn_id', 'created_by', 'updated_by', 'created_date'], 'required'],
            [['ean', 'asin', 'marketplace_title', 'product_title', 'msku', 'remarks', 'issues', 'invoice_no'], 'string', 'max' => 255],
            [['fnsku'], 'string', 'max' => 100],
            [['psku'], 'string', 'max' => 256],
            [['status'], 'string', 'max' => 45],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'grn_entries_id' => 'Grn Entries ID',
            'ean' => 'Ean',
            'asin' => 'Asin',
            'fnsku' => 'Fnsku',
            'marketplace_title' => 'Marketplace Title',
            'product_title' => 'Product Title',
            'vat_cst' => 'Vat Cst',
            'is_active' => 'Is Active',
            'msku' => 'Msku',
            'psku' => 'Psku',
            'product_type' => 'Product Type',
            'list_price' => 'List Price',
            'box_price' => 'Box Price',
            'cost_excl_vat' => 'Cost Excl Vat',
            'vat_percen' => 'Vat Percen',
            'cost_incl_vat_cst' => 'Cost Incl Vat Cst',
            'po_qty' => 'Po Qty',
            'proper_qty' => 'Proper Qty',
            'status' => 'Status',
            'damaged_qty' => 'Damaged Qty',
            'is_sku_adjustment' => 'Is Sku Adjustment',
            'total_qty' => 'Total Qty',
            'shortage_qty' => 'Shortage Qty',
            'excess_qty' => 'Excess Qty',
            'po_shortage_qty' => 'Po Shortage Qty',
            'po_excess_qty' => 'Po Excess Qty',
            'invoice_qty' => 'Invoice Qty',
            'manufacturing_date' => 'Manufacturing Date',
            'expiry_date' => 'Expiry Date',
            'expiry_issue' => 'Expiry Issue',
            'remarks' => 'Remarks',
            'issues' => 'Issues',
            'expiry_qty' => 'Expiry Qty',
            'mrp_issue_qty' => 'Mrp Issue Qty',
            'percent_expiry_left' => 'Percent Expiry Left',
            'expiry_months_left' => 'Expiry Months Left',
            'grn_id' => 'Grn ID',
            'company_id' => 'Company ID',
            'min_percentage_shelf_life_required' => 'Min Percentage Shelf Life Required',
            'min_no_of_months_shelf_life_required' => 'Min No Of Months Shelf Life Required',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'created_date' => 'Created Date',
            'updated_date' => 'Updated Date',
            'issue_type' => 'Issue Type',
            'other_type' => 'Other Type',
            'invoice_no' => 'Invoice No',
            'physical_allocation' => 'Physical Allocation',
            'removal_qty' => 'Removal Qty',
            'challan_qty' => 'Challan Qty',
            'removal_shortage_qty' => 'Removal Shortage Qty',
            'removal_excess_qty' => 'Removal Excess Qty',
            'challan_shortage_qty' => 'Challan Shortage Qty',
            'challan_excess_qty' => 'Challan Excess Qty',
            'is_bulk_upload' => 'Is Bulk Upload',
            'is_first_scan' => 'Is First Scan',
            'initial_invoice_qty' => 'Initial Invoice Qty',
            'demand_qty' => 'Demand Qty',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGrnEntriesArchives()
    {
        return $this->hasMany(GrnEntriesArchives::className(), ['grn_entries_id' => 'grn_entries_id']);
    }
}
