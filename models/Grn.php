<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "grn".
 *
 * @property integer $grn_id
 * @property string $gi_id
 * @property string $gi_date
 * @property string $vat_cst
 * @property string $grn_start_date_time
 * @property integer $vendor_id
 * @property string $vendor_name
 * @property string $customer_name
 * @property string $customer_type
 * @property integer $invoice_sku
 * @property integer $scanned_sku
 * @property integer $category_id
 * @property string $category_name
 * @property string $grn_no
 * @property integer $invoice_qty
 * @property double $invoice_amt
 * @property integer $scanned_qty
 * @property string $location
 * @property integer $company_id
 * @property string $po_no
 * @property string $warehouse_id
 * @property string $idt_warehouse_code
 * @property integer $user_id
 * @property string $user_name
 * @property integer $summary_user_id
 * @property string $summary_user_name
 * @property double $invoice_val_bef_tax
 * @property double $invoice_val_after_tax
 * @property double $payable_val_before_tax
 * @property double $payable_val_after_tax
 * @property string $gi_type
 * @property integer $manufacturer_id
 * @property string $manufacturer
 * @property integer $received_sku
 * @property integer $received_qty
 * @property integer $shortage
 * @property string $status
 * @property integer $is_active
 * @property string $remarks
 * @property string $summary_status
 * @property string $summary_docket
 * @property string $shipment_date
 * @property integer $shipment_id
 * @property integer $no_of_sku
 * @property integer $sku
 * @property integer $qty
 * @property integer $no_of_boxes
 * @property string $vehicle_no
 * @property string $delivery_challan_no
 * @property integer $warehouse_bal
 * @property integer $goods_not_shipped
 * @property integer $unresolved_unit
 * @property integer $sellable_units
 * @property integer $damaged_units
 * @property integer $expired_units
 * @property integer $sellable_amt
 * @property integer $unsellable_amt
 * @property integer $asin_issue_units
 * @property integer $listing_issue_units
 * @property integer $hazmat_issue_units
 * @property integer $mrp_issue_units
 * @property integer $marketplace_removed_units
 * @property integer $excess_units
 * @property double $percent_fill_rate
 * @property integer $total_issues
 * @property string $grn_end_date_time
 * @property string $created_by
 * @property string $updated_by
 * @property string $created_date
 * @property string $updated_date
 * @property string $approver_comments
 * @property string $approver_user_id
 * @property string $approver_name
 * @property string $grn_approved_date_time
 * @property string $from_warehouse_name
 * @property string $interdepot_type
 * @property string $removal_id
 * @property string $challan_no
 * @property string $from_warehouse_code
 * @property string $is_bulk_upload
 * @property string $combo_type
 * @property string $combo_child_sku
 *
 * @property GrnEntriesArchives[] $grnEntriesArchives
 */
class Grn extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'grn';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['gi_date', 'grn_start_date_time', 'shipment_date', 'grn_end_date_time', 'created_date', 'updated_date', 'grn_approved_date_time'], 'safe'],
            [['vat_cst', 'customer_type', 'gi_type', 'status', 'interdepot_type', 'is_bulk_upload', 'combo_type'], 'string'],
            [['vendor_id', 'invoice_sku', 'scanned_sku', 'category_id', 'invoice_qty', 'scanned_qty', 'company_id', 'user_id', 'summary_user_id', 'manufacturer_id', 'received_sku', 'received_qty', 'shortage', 'is_active', 'shipment_id', 'no_of_sku', 'sku', 'qty', 'no_of_boxes', 'warehouse_bal', 'goods_not_shipped', 'unresolved_unit', 'sellable_units', 'damaged_units', 'expired_units', 'sellable_amt', 'unsellable_amt', 'asin_issue_units', 'listing_issue_units', 'hazmat_issue_units', 'mrp_issue_units', 'marketplace_removed_units', 'excess_units', 'total_issues', 'created_by', 'updated_by'], 'integer'],
            [['customer_name', 'created_by', 'updated_by', 'created_date', 'approver_user_id', 'approver_name', 'interdepot_type'], 'required'],
            [['invoice_amt', 'invoice_val_bef_tax', 'invoice_val_after_tax', 'payable_val_before_tax', 'payable_val_after_tax', 'percent_fill_rate'], 'number'],
            [['gi_id', 'idt_warehouse_code', 'user_name', 'summary_user_name', 'summary_status', 'summary_docket', 'vehicle_no', 'delivery_challan_no'], 'string', 'max' => 45],
            [['vendor_name', 'category_name', 'grn_no', 'location', 'po_no', 'warehouse_id', 'manufacturer', 'from_warehouse_name', 'removal_id', 'challan_no', 'from_warehouse_code'], 'string', 'max' => 255],
            [['customer_name', 'combo_child_sku'], 'string', 'max' => 512],
            [['remarks'], 'string', 'max' => 1000],
            [['approver_comments'], 'string', 'max' => 250],
            [['approver_user_id', 'approver_name'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'grn_id' => 'Grn ID',
            'gi_id' => 'Grn No',
            'gi_date' => 'Received On',
            'vat_cst' => 'Vat Cst',
            'grn_start_date_time' => 'Grn Start Date Time',
            'vendor_id' => 'Vendor ID',
            'vendor_name' => 'Vendor Name',
            'customer_name' => 'Customer Name',
            'customer_type' => 'Customer Type',
            'invoice_sku' => 'Invoice Sku',
            'scanned_sku' => 'Scanned Sku',
            'category_id' => 'Category ID',
            'category_name' => 'Category Name',
            'grn_no' => 'Grn No',
            'invoice_qty' => 'Invoice Qty',
            'invoice_amt' => 'Invoice Amt',
            'scanned_qty' => 'Total Qty',
            'location' => 'Warehouse Location',
            'company_id' => 'Company ID',
            'po_no' => 'Po No',
            'warehouse_id' => 'Warehouse ID',
            'idt_warehouse_code' => 'Idt Warehouse Code',
            'user_id' => 'User ID',
            'user_name' => 'User Name',
            'summary_user_id' => 'Summary User ID',
            'summary_user_name' => 'Summary User Name',
            'invoice_val_bef_tax' => 'Invoice Val Bef Tax',
            'invoice_val_after_tax' => 'Invoice Val After Tax',
            'payable_val_before_tax' => 'Payable Val Before Tax',
            'payable_val_after_tax' => 'Total Amount',
            'gi_type' => 'Gi Type',
            'manufacturer_id' => 'Manufacturer ID',
            'manufacturer' => 'Manufacturer',
            'received_sku' => 'Received Sku',
            'received_qty' => 'Received Qty',
            'shortage' => 'Shortage',
            'status' => 'Status',
            'is_active' => 'Is Active',
            'remarks' => 'Remarks',
            'summary_status' => 'Summary Status',
            'summary_docket' => 'Summary Docket',
            'shipment_date' => 'Shipment Date',
            'shipment_id' => 'Shipment ID',
            'no_of_sku' => 'No Of Sku',
            'sku' => 'Sku',
            'qty' => 'Qty',
            'no_of_boxes' => 'No Of Boxes',
            'vehicle_no' => 'Vehicle No',
            'delivery_challan_no' => 'Delivery Challan No',
            'warehouse_bal' => 'Warehouse Bal',
            'goods_not_shipped' => 'Goods Not Shipped',
            'unresolved_unit' => 'Unresolved Unit',
            'sellable_units' => 'Sellable Units',
            'damaged_units' => 'Damaged Units',
            'expired_units' => 'Expired Units',
            'sellable_amt' => 'Sellable Amt',
            'unsellable_amt' => 'Unsellable Amt',
            'asin_issue_units' => 'Asin Issue Units',
            'listing_issue_units' => 'Listing Issue Units',
            'hazmat_issue_units' => 'Hazmat Issue Units',
            'mrp_issue_units' => 'Mrp Issue Units',
            'marketplace_removed_units' => 'Marketplace Removed Units',
            'excess_units' => 'Excess Units',
            'percent_fill_rate' => 'Percent Fill Rate',
            'total_issues' => 'Total Issues',
            'grn_end_date_time' => 'Grn End Date Time',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'created_date' => 'Created Date',
            'updated_date' => 'Updated Date',
            'approver_comments' => 'Approver Comments',
            'approver_user_id' => 'Approver User ID',
            'approver_name' => 'Approver Name',
            'grn_approved_date_time' => 'Grn Approved Date Time',
            'from_warehouse_name' => 'From Warehouse Name',
            'interdepot_type' => 'Interdepot Type',
            'removal_id' => 'Removal ID',
            'challan_no' => 'Challan No',
            'from_warehouse_code' => 'From Warehouse Code',
            'is_bulk_upload' => 'Is Bulk Upload',
            'combo_type' => 'Combo Type',
            'combo_child_sku' => 'Combo Child Sku',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGrnEntriesArchives()
    {
        return $this->hasMany(GrnEntriesArchives::className(), ['grn_id' => 'grn_id']);
    }


    // public function getGrnDetails($id){
    //     $sql = "select * from grn where grn_id = '$id'";
    //     // $sql = "select * from grn where grn_id = '".$id."'";
    //     $command = Yii::$app->db->createCommand($sql);
    //     $reader = $command->query();
    //     return $reader->readAll();
    // }

    public function getGrnDetails(){
        $sql = "select * from 
                (select A.*, B.grn_id as b_grn_id from 
                (select * from grn where status = 'approved') A 
                left join 
                (select distinct grn_id from acc_grn_entries) B 
                on (A.grn_id = B.grn_id)) C 
                where b_grn_id is null";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getPendingGrnDetails(){
        $sql = "select B.*, C.grn_no, C.vendor_name, C.category_name, C.po_no from 
                (select grn_id, inv_nos, (taxable_amt+tax_amt+other_amt) as net_amt, (shortage_amt+expiry_amt+damaged_amt+magrin_diff_amt) as ded_amt from 
                (select grn_id, GROUP_CONCAT(distinct invoice_no) as inv_nos, sum(case when particular='Taxable Amount' then edited_val else 0 end) as taxable_amt, 
                        sum(case when particular='Tax' then edited_val else 0 end) as tax_amt, 
                        sum(case when particular='Other Charges' then edited_val else 0 end) as other_amt, 
                        sum(case when particular='Shortage Amount' then edited_val else 0 end) as shortage_amt, 
                        sum(case when particular='Expiry Amount' then edited_val else 0 end) as expiry_amt, 
                        sum(case when particular='Damaged Amount' then edited_val else 0 end) as damaged_amt, 
                        sum(case when particular='Margin Diff Amount' then edited_val else 0 end) as magrin_diff_amt 
                from acc_grn_entries where status = 'pending' group by grn_id) A) B 
                left join 
                (select * from grn where status = 'approved') C 
                on (B.grn_id = C.grn_id)";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getApprovedGrnDetails(){
        $sql = "select B.*, C.grn_no, C.vendor_name, C.category_name, C.po_no from 
                (select grn_id, inv_nos, (taxable_amt+tax_amt+other_amt) as net_amt, (shortage_amt+expiry_amt+damaged_amt+magrin_diff_amt) as ded_amt from 
                (select grn_id, GROUP_CONCAT(distinct invoice_no) as inv_nos, sum(case when particular='Taxable Amount' then edited_val else 0 end) as taxable_amt, 
                        sum(case when particular='Tax' then edited_val else 0 end) as tax_amt, 
                        sum(case when particular='Other Charges' then edited_val else 0 end) as other_amt, 
                        sum(case when particular='Shortage Amount' then edited_val else 0 end) as shortage_amt, 
                        sum(case when particular='Expiry Amount' then edited_val else 0 end) as expiry_amt, 
                        sum(case when particular='Damaged Amount' then edited_val else 0 end) as damaged_amt, 
                        sum(case when particular='Margin Diff Amount' then edited_val else 0 end) as magrin_diff_amt 
                from acc_grn_entries where status = 'approved' group by grn_id) A) B 
                left join 
                (select * from grn where status = 'approved') C 
                on (B.grn_id = C.grn_id)";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getTotalValue($id){
        $sql = "select sum(total_cost) as total_cost, sum(total_tax) as total_tax, 0 as other_charges, sum(total_amount) as total_amount, 
                sum(excess_amount) as excess_amount, sum(shortage_amount) as shortage_amount, sum(expiry_amount) as expiry_amount, 
                sum(damaged_amount) as damaged_amount, sum(margin_diff_amount) as margin_diff_amount, sum(total_deduction) as total_deduction, 
                sum(total_payable_amount) as total_payable_amount from 
                (select invoice_no, total_cost, total_tax, total_amount, excess_amount, shortage_amount, expiry_amount, damaged_amount, 
                    margin_diff_amount, total_deduction, (total_amount-total_deduction) as total_payable_amount from 
                (select invoice_no, total_cost, total_tax, (total_cost+total_tax) as total_amount, excess_amount, shortage_amount, expiry_amount, 
                    damaged_amount, margin_diff_amount, (shortage_amount+expiry_amount+damaged_amount+margin_diff_amount) as total_deduction from 
                (select invoice_no, (total_cost+shortage_cost+expiry_cost+damaged_cost+margin_diff_cost-excess_cost) as total_cost, 
                    (total_tax+shortage_tax+expiry_tax+damaged_tax+margin_diff_tax-excess_tax) as total_tax, 
                    (excess_cost+excess_tax) as excess_amount, (shortage_cost+shortage_tax) as shortage_amount, 
                    (expiry_cost+expiry_tax) as expiry_amount, (damaged_cost+damaged_tax) as damaged_amount, 
                    (margin_diff_cost+margin_diff_tax) as margin_diff_amount from 
                (select invoice_no, ifnull((total_qty*cost_excl_vat),0) as total_cost, ifnull((total_qty*cost_excl_vat*vat_percen)/100,0) as total_tax, 
                    ifnull((excess_qty*cost_excl_vat),0) as excess_cost, ifnull((excess_qty*cost_excl_vat*vat_percen)/100,0) as excess_tax, 
                    ifnull((shortage_qty*cost_excl_vat),0) as shortage_cost, ifnull((shortage_qty*cost_excl_vat*vat_percen)/100,0) as shortage_tax, 
                    ifnull((expiry_qty*cost_excl_vat),0) as expiry_cost, ifnull((expiry_qty*cost_excl_vat*vat_percen)/100,0) as expiry_tax, 
                    ifnull((damaged_qty*cost_excl_vat),0) as damaged_cost, ifnull((damaged_qty*cost_excl_vat*vat_percen)/100,0) as damaged_tax, 
                    ifnull((0*cost_excl_vat),0) as margin_diff_cost, ifnull((0*cost_excl_vat*vat_percen)/100,0) as margin_diff_tax 
                    from grn_entries where is_active = '1' and grn_id = '$id') A) B) C) D";
        // $sql = "select * from grn where grn_id = '".$id."'";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getTotalTax($id){
        $sql = "select vat_cst, vat_percen, sum(total_tax) as total_tax from 
                (select vat_cst, vat_percen, (total_tax+shortage_tax+expiry_tax+damaged_tax+margin_diff_tax-excess_tax) as total_tax from 
                (select vat_cst, vat_percen, ifnull((total_qty*cost_excl_vat*vat_percen)/100,0) as total_tax, 
                    ifnull((excess_qty*cost_excl_vat*vat_percen)/100,0) as excess_tax, 
                    ifnull((shortage_qty*cost_excl_vat*vat_percen)/100,0) as shortage_tax, 
                    ifnull((expiry_qty*cost_excl_vat*vat_percen)/100,0) as expiry_tax, 
                    ifnull((damaged_qty*cost_excl_vat*vat_percen)/100,0) as damaged_tax, 
                    ifnull((0*cost_excl_vat*vat_percen)/100,0) as margin_diff_tax from grn_entries 
                where is_active = '1' and grn_id = '$id' and vat_percen>0) A) B 
                group by vat_cst, vat_percen order by vat_cst, vat_percen";
        // $sql = "select * from grn where grn_id = '".$id."'";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getInvoiceDetails($id){
        $sql = "select invoice_no, total_cost as invoice_total_cost, total_tax as invoice_total_tax, total_amount as invoice_total_amount, 
                excess_amount as invoice_excess_amount, shortage_amount as invoice_shortage_amount, expiry_amount as invoice_expiry_amount, 
                damaged_amount as invoice_damaged_amount, margin_diff_amount as invoice_margin_diff_amount, total_deduction as invoice_total_deduction, 
                total_payable_amount as invoice_total_payable_amount, total_cost as edited_total_cost, total_tax as edited_total_tax, total_amount as edited_total_amount, 
                excess_amount as edited_excess_amount, shortage_amount as edited_shortage_amount, expiry_amount as edited_expiry_amount, 
                damaged_amount as edited_damaged_amount, margin_diff_amount as edited_margin_diff_amount, total_deduction as edited_total_deduction, 
                total_payable_amount as edited_total_payable_amount, 0 as diff_total_cost, 0 as diff_total_tax, 0 as diff_total_amount, 
                0 as diff_excess_amount, 0 as diff_shortage_amount, 0 as diff_expiry_amount, 0 as diff_damaged_amount, 
                0 as diff_margin_diff_amount, 0 as diff_total_deduction, 0 as diff_total_payable_amount, 
                0 as invoice_other_charges, 0 as edited_other_charges, 0 as diff_other_charges from 
                (select invoice_no, sum(total_cost) as total_cost, sum(total_tax) as total_tax, sum(total_amount) as total_amount, 
                sum(excess_amount) as excess_amount, sum(shortage_amount) as shortage_amount, sum(expiry_amount) as expiry_amount, 
                sum(damaged_amount) as damaged_amount, sum(margin_diff_amount) as margin_diff_amount, sum(total_deduction) as total_deduction, 
                sum(total_payable_amount) as total_payable_amount from 
                (select invoice_no, total_cost, total_tax, total_amount, excess_amount, shortage_amount, expiry_amount, damaged_amount, 
                    margin_diff_amount, total_deduction, (total_amount-total_deduction) as total_payable_amount from 
                (select invoice_no, total_cost, total_tax, (total_cost+total_tax) as total_amount, excess_amount, shortage_amount, expiry_amount, 
                    damaged_amount, margin_diff_amount, (shortage_amount+expiry_amount+damaged_amount+margin_diff_amount) as total_deduction from 
                (select invoice_no, (total_cost+shortage_cost+expiry_cost+damaged_cost+margin_diff_cost-excess_cost) as total_cost, 
                    (total_tax+shortage_tax+expiry_tax+damaged_tax+margin_diff_tax-excess_tax) as total_tax, 
                    (excess_cost+excess_tax) as excess_amount, (shortage_cost+shortage_tax) as shortage_amount, 
                    (expiry_cost+expiry_tax) as expiry_amount, (damaged_cost+damaged_tax) as damaged_amount, 
                    (margin_diff_cost+margin_diff_tax) as margin_diff_amount from 
                (select invoice_no, ifnull((total_qty*cost_excl_vat),0) as total_cost, ifnull((total_qty*cost_excl_vat*vat_percen)/100,0) as total_tax, 
                    ifnull((excess_qty*cost_excl_vat),0) as excess_cost, ifnull((excess_qty*cost_excl_vat*vat_percen)/100,0) as excess_tax, 
                    ifnull((shortage_qty*cost_excl_vat),0) as shortage_cost, ifnull((shortage_qty*cost_excl_vat*vat_percen)/100,0) as shortage_tax, 
                    ifnull((expiry_qty*cost_excl_vat),0) as expiry_cost, ifnull((expiry_qty*cost_excl_vat*vat_percen)/100,0) as expiry_tax, 
                    ifnull((damaged_qty*cost_excl_vat),0) as damaged_cost, ifnull((damaged_qty*cost_excl_vat*vat_percen)/100,0) as damaged_tax, 
                    ifnull((0*cost_excl_vat),0) as margin_diff_cost, ifnull((0*cost_excl_vat*vat_percen)/100,0) as margin_diff_tax 
                    from grn_entries where is_active = '1' and grn_id = '$id') A) B) C) D 
                group by invoice_no) E order by invoice_no";
        // $sql = "select * from grn where grn_id = '".$id."'";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getInvoiceTax($id){
        $sql = "select invoice_no, vat_cst, vat_percen, total_tax as invoice_tax, total_tax as edited_tax, 0 as diff_tax from 
                (select invoice_no, vat_cst, vat_percen, sum(total_tax) as total_tax from 
                (select invoice_no, vat_cst, vat_percen, (total_tax+shortage_tax+expiry_tax+damaged_tax+margin_diff_tax-excess_tax) as total_tax from 
                (select invoice_no, vat_cst, vat_percen, ifnull((total_qty*cost_excl_vat*vat_percen)/100,0) as total_tax, 
                    ifnull((excess_qty*cost_excl_vat*vat_percen)/100,0) as excess_tax, 
                    ifnull((shortage_qty*cost_excl_vat*vat_percen)/100,0) as shortage_tax, 
                    ifnull((expiry_qty*cost_excl_vat*vat_percen)/100,0) as expiry_tax, 
                    ifnull((damaged_qty*cost_excl_vat*vat_percen)/100,0) as damaged_tax, 
                    ifnull((0*cost_excl_vat*vat_percen)/100,0) as margin_diff_tax from grn_entries 
                where is_active = '1' and grn_id = '$id') A) B 
                group by invoice_no, vat_cst, vat_percen) C 
                order by invoice_no, vat_cst, vat_percen";
        // $sql = "select * from grn where grn_id = '".$id."'";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getGrnAccEntries($id){
        $sql = "select * from acc_grn_entries where grn_id = '$id' and status = 'pending' order by grn_id, invoice_no, vat_cst, vat_percen";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getInvoiceDeductionDetails($id, $col_qty){
        $sql = "select * from grn_entries where grn_id = '$id' and " . $col_qty . " > 0 order by invoice_no, vat_percen";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getGrnAccSkuEntries($id, $ded_type){
        $sql = "select invoice_no, ean, psku, product_title, vat_percen, box_price, cost_excl_vat_per_unit, tax_per_unit, 
                total_per_unit, cost_excl_vat, tax, total, qty as ".$ded_type."_qty from acc_grn_sku_entries 
                where grn_id = '$id' and ded_type = '$ded_type' order by invoice_no, vat_percen";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getGrnAccLedgerEntries($id){
        $sql = "select * from acc_ledger_entries where grn_id = '$id' and status = 'pending' order by grn_id, invoice_no, id";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

}
