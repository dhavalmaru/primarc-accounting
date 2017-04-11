<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\Session;

class PendingGrn extends Model
{
    public function getNewGrnDetails(){
        $sql = "select * from 
                (select A.*, B.grn_id as b_grn_id from 
                (select * from grn where is_active = '1' and status = 'approved') A 
                left join 
                (select distinct grn_id from grn_acc_entries) B 
                on (A.grn_id = B.grn_id)) C 
                where b_grn_id is null";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getAllGrnDetails(){
        $sql = "select * from 
                (select A.*, B.status as grn_status from 
                (select * from grn where is_active = '1' and status = 'approved') A 
                left join 
                (select distinct grn_id, status from grn_acc_entries) B 
                on (A.grn_id = B.grn_id)) C";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

	public function getGrnDetails($id){
        $sql = "select A.*, B.vendor_code from grn A left join vendor_master B on (A.vendor_id = B.id) 
                where A.grn_id = '$id' and A.status = 'approved' and A.is_active='1'";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getPurchaseDetails($status=""){
        $cond = "";
        if($status!=""){
            $cond = " and status = '$status'";
        }
        $sql = "select B.*, C.grn_no, C.vendor_name, C.category_name, C.po_no from 
                (select grn_id, inv_nos, (taxable_amt+tax_amt+other_amt) as net_amt, 
                    (shortage_amt+expiry_amt+damaged_amt+magrin_diff_amt) as ded_amt, updated_by, approved_by from 
                (select grn_id, GROUP_CONCAT(distinct invoice_no) as inv_nos, 
                        sum(case when particular='Taxable Amount' then edited_val else 0 end) as taxable_amt, 
                        sum(case when particular='Tax' then edited_val else 0 end) as tax_amt, 
                        sum(case when particular='Other Charges' then edited_val else 0 end) as other_amt, 
                        sum(case when particular='Shortage Amount' then edited_val else 0 end) as shortage_amt, 
                        sum(case when particular='Expiry Amount' then edited_val else 0 end) as expiry_amt, 
                        sum(case when particular='Damaged Amount' then edited_val else 0 end) as damaged_amt, 
                        sum(case when particular='Margin Diff Amount' then edited_val else 0 end) as magrin_diff_amt, 
                        max(updated_by) as updated_by, max(approved_by) as approved_by 
                from grn_acc_entries where is_active = '1' ".$cond." group by grn_id) A) B 
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

    // public function getInvoiceTax($id){
    //     $sql = "select invoice_no, vat_cst, vat_percen, total_tax as invoice_tax, total_tax as edited_tax, 0 as diff_tax from 
    //             (select invoice_no, vat_cst, vat_percen, sum(total_cost) as total_cost, sum(total_tax) as total_tax from 
    //             (select invoice_no, vat_cst, vat_percen, (total_cost+shortage_cost+expiry_cost+damaged_cost+margin_diff_cost-excess_cost) as total_cost, 
    //                 (total_tax+shortage_tax+expiry_tax+damaged_tax+margin_diff_tax-excess_tax) as total_tax from 
    //             (select invoice_no, vat_cst, vat_percen, ifnull((total_qty*cost_excl_vat),0) as total_cost, ifnull((total_qty*cost_excl_vat*vat_percen)/100,0) as total_tax, 
    //                 ifnull((excess_qty*cost_excl_vat),0) as excess_cost, ifnull((excess_qty*cost_excl_vat*vat_percen)/100,0) as excess_tax, 
    //                 ifnull((shortage_qty*cost_excl_vat),0) as shortage_cost, ifnull((shortage_qty*cost_excl_vat*vat_percen)/100,0) as shortage_tax, 
    //                 ifnull((expiry_qty*cost_excl_vat),0) as expiry_cost, ifnull((expiry_qty*cost_excl_vat*vat_percen)/100,0) as expiry_tax, 
    //                 ifnull((damaged_qty*cost_excl_vat),0) as damaged_cost, ifnull((damaged_qty*cost_excl_vat*vat_percen)/100,0) as damaged_tax, 
    //                 ifnull((0*cost_excl_vat),0) as margin_diff_cost, ifnull((0*cost_excl_vat*vat_percen)/100,0) as margin_diff_tax from grn_entries 
    //             where is_active = '1' and grn_id = '$id') A) B 
    //             group by invoice_no, vat_cst, vat_percen) C 
    //             order by invoice_no, vat_cst, vat_percen";
    //     // $sql = "select * from grn where grn_id = '".$id."'";
    //     $command = Yii::$app->db->createCommand($sql);
    //     $reader = $command->query();
    //     return $reader->readAll();
    // }

    public function getTotalTax($id){
        $sql = "select A.tax_zone_code, A.tax_zone_name, B.* from 
                (select AA.*, CC.tax_zone_code, CC.tax_zone_name from grn AA 
                    left join internal_warehouse_master BB on (AA.warehouse_id = BB.warehouse_code and AA.company_id = BB.company_id) 
                left join tax_zone_master CC on (BB.tax_zone_id = CC.id) 
                where AA.grn_id = '$id' and AA.is_active = '1' and BB.is_active = '1' and CC.is_active = '1' limit 1) A 
                left join 
                (select grn_id, invoice_cost_acc_id, invoice_cost_ledger_name, invoice_cost_ledger_code, 
                    invoice_tax_acc_id, invoice_tax_ledger_name, invoice_tax_ledger_code, vat_cst, vat_percen, 
                    sum(total_cost) as total_cost, sum(total_tax) as total_tax from 
                (select grn_id, invoice_cost_acc_id, invoice_cost_ledger_name, invoice_cost_ledger_code, 
                    invoice_tax_acc_id, invoice_tax_ledger_name, invoice_tax_ledger_code, vat_cst, vat_percen, 
                    (total_cost+shortage_cost+expiry_cost+damaged_cost+margin_diff_cost-excess_cost) as total_cost, 
                    (total_tax+shortage_tax+expiry_tax+damaged_tax+margin_diff_tax-excess_tax) as total_tax from 
                (select grn_id, null as invoice_cost_acc_id, null as invoice_cost_ledger_name, null as invoice_cost_ledger_code, 
                    null as invoice_tax_acc_id, null as invoice_tax_ledger_name, null as invoice_tax_ledger_code, vat_cst, vat_percen, 
                    ifnull((total_qty*cost_excl_vat),0) as total_cost, ifnull((total_qty*cost_excl_vat*vat_percen)/100,0) as total_tax, 
                    ifnull((excess_qty*cost_excl_vat),0) as excess_cost, ifnull((excess_qty*cost_excl_vat*vat_percen)/100,0) as excess_tax, 
                    ifnull((shortage_qty*cost_excl_vat),0) as shortage_cost, ifnull((shortage_qty*cost_excl_vat*vat_percen)/100,0) as shortage_tax, 
                    ifnull((expiry_qty*cost_excl_vat),0) as expiry_cost, ifnull((expiry_qty*cost_excl_vat*vat_percen)/100,0) as expiry_tax, 
                    ifnull((damaged_qty*cost_excl_vat),0) as damaged_cost, ifnull((damaged_qty*cost_excl_vat*vat_percen)/100,0) as damaged_tax, 
                    ifnull((0*cost_excl_vat),0) as margin_diff_cost, ifnull((0*cost_excl_vat*vat_percen)/100,0) as margin_diff_tax from grn_entries 
                where is_active = '1' and grn_id = '$id') A) B 
                group by grn_id, vat_percen, vat_cst order by grn_id, vat_percen, vat_cst) B 
                on (A.grn_id = B.grn_id) where total_cost > 0 
                order by A.tax_zone_code, B.vat_percen, B.vat_cst";
        // $sql = "select * from grn where grn_id = '".$id."'";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getInvoiceTaxDetails($id){
        $sql = "select A.tax_zone_code, A.tax_zone_name, B.* from 
                (select AA.*, CC.tax_zone_code, CC.tax_zone_name from grn AA 
                left join internal_warehouse_master BB on (AA.warehouse_id = BB.warehouse_code and AA.company_id = BB.company_id) 
                left join tax_zone_master CC on (BB.tax_zone_id = CC.id) 
                where AA.grn_id = '$id' and AA.is_active = '1' and BB.is_active = '1' and CC.is_active = '1' limit 1) A 
                left join 
                (select grn_id, invoice_no, vat_cst, vat_percen, total_cost as invoice_cost, total_tax as invoice_tax, 
                    total_cost as edited_cost, total_tax as edited_tax, 0 as diff_cost, 0 as diff_tax, 
                    null as invoice_cost_acc_id, null as invoice_cost_ledger_name, null as invoice_cost_ledger_code, 
                    null as invoice_tax_acc_id, null as invoice_tax_ledger_name, null as invoice_tax_ledger_code from 
                (select grn_id, invoice_no, vat_cst, vat_percen, sum(total_cost) as total_cost, sum(total_tax) as total_tax from 
                (select grn_id, invoice_no, vat_cst, vat_percen, (total_cost+shortage_cost+expiry_cost+damaged_cost+margin_diff_cost-excess_cost) as total_cost, 
                    (total_tax+shortage_tax+expiry_tax+damaged_tax+margin_diff_tax-excess_tax) as total_tax from 
                (select grn_id, invoice_no, vat_cst, vat_percen, ifnull((total_qty*cost_excl_vat),0) as total_cost, ifnull((total_qty*cost_excl_vat*vat_percen)/100,0) as total_tax, 
                    ifnull((excess_qty*cost_excl_vat),0) as excess_cost, ifnull((excess_qty*cost_excl_vat*vat_percen)/100,0) as excess_tax, 
                    ifnull((shortage_qty*cost_excl_vat),0) as shortage_cost, ifnull((shortage_qty*cost_excl_vat*vat_percen)/100,0) as shortage_tax, 
                    ifnull((expiry_qty*cost_excl_vat),0) as expiry_cost, ifnull((expiry_qty*cost_excl_vat*vat_percen)/100,0) as expiry_tax, 
                    ifnull((damaged_qty*cost_excl_vat),0) as damaged_cost, ifnull((damaged_qty*cost_excl_vat*vat_percen)/100,0) as damaged_tax, 
                    ifnull((0*cost_excl_vat),0) as margin_diff_cost, ifnull((0*cost_excl_vat*vat_percen)/100,0) as margin_diff_tax from grn_entries 
                where is_active = '1' and grn_id = '$id') A) B 
                group by grn_id, invoice_no, vat_percen, vat_cst) C 
                where total_cost > 0 
                order by grn_id, invoice_no, vat_percen, vat_cst) B 
                on (A.grn_id = B.grn_id) 
                order by B.invoice_no, A.tax_zone_code, B.vat_percen, B.vat_cst";
        // $sql = "select * from grn where grn_id = '".$id."'";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getGrnAccEntries($id){
        $sql = "select A.tax_zone_code, A.tax_zone_name, B.* from 
                (select AA.*, CC.tax_zone_code, CC.tax_zone_name from grn AA 
                    left join internal_warehouse_master BB on (AA.warehouse_id = BB.warehouse_code and AA.company_id = BB.company_id) 
                    left join tax_zone_master CC on (BB.tax_zone_id = CC.id) 
                where AA.grn_id = '$id' and AA.is_active = '1' and BB.is_active = '1' and CC.is_active = '1' limit 1) A 
                left join 
                (select * from grn_acc_entries where grn_id = '$id' and status = 'pending' and is_active = '1' 
                order by grn_id, invoice_no, id, vat_percen, vat_cst) B 
                on (A.grn_id = B.grn_id) 
                where B.id is not null
                order by B.id";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getGrnSkues($id){
        $sql = "select distinct psku from grn_entries where grn_id = '$id' and is_active = '1'";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getGrnInvoices($id){
        $sql = "select distinct invoice_no from grn_entries where grn_id = '$id' and is_active = '1'";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getInvoiceDeductionDetails($id, $col_qty){
        // $sql = "select B.*, null as cost_acc_id, null as cost_ledger_name, null as cost_ledger_code, null as tax_acc_id, 
        //             null as tax_ledger_name, null as tax_ledger_code, A.tax_zone_code, A.tax_zone_name from 
        //         (select AA.*, CC.tax_zone_code, CC.tax_zone_name from grn AA 
        //         left join vendor_warehouse_address BB on (AA.vendor_id = BB.vendor_id) 
        //         left join tax_zone_master CC on (BB.tax_zone_id = CC.id) 
        //         where AA.grn_id = '$id' and AA.is_active = '1' and BB.is_active = '1' and CC.is_active = '1' limit 1) A 
        //         left join 
        //         (select * from grn_entries where grn_id = '$id' and " . $col_qty . " > 0 and is_active = '1' 
        //         order by invoice_no, vat_percen) B 
        //         on (A.grn_id = B.grn_id) 
        //         where B.invoice_no is not null
        //         order by B.invoice_no, B.vat_percen";

        $sql = "select A.grn_id, B.*, D.tax_zone_code, D.tax_zone_name, E.invoice_date, A.gi_date, 
                    date_add(A.gi_date, interval ifnull(min_no_of_months_shelf_life_required,0) month) as earliest_expected_date, 
                    null as cost_acc_id, null as cost_ledger_name, null as cost_ledger_code, 
                    null as tax_acc_id, null as tax_ledger_name, null as tax_ledger_code from grn A 
                left join grn_entries B on (A.grn_id = B.grn_id) 
                left join internal_warehouse_master C on (A.warehouse_id = C.warehouse_code and A.company_id = C.company_id) 
                left join tax_zone_master D on (C.tax_zone_id = D.id) 
                left join goods_inward_outward_invoices E on (A.gi_id = E.gi_go_ref_no and B.invoice_no = E.invoice_no) 
                where A.grn_id = '$id' and B.grn_id = '$id' and A.is_active = '1' and B.is_active = '1' and 
                    C.is_active = '1' and D.is_active = '1' and B.invoice_no is not null and B." . $col_qty . " > 0 
                order by B.invoice_no, B.vat_percen";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getGrnAccSku($id){
        $sql = "select * from grn_acc_sku_entries 
                where grn_id = '$id' and is_active = '1' order by invoice_no, vat_percen";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getGrnAccSkuEntries($id, $ded_type){
        // $sql = "select B.*, A.tax_zone_code, A.tax_zone_name from 
        //         (select AA.*, CC.tax_zone_code, CC.tax_zone_name from grn AA 
        //         left join vendor_warehouse_address BB on (AA.vendor_id = BB.vendor_id) 
        //         left join tax_zone_master CC on (BB.tax_zone_id = CC.id) 
        //         where AA.grn_id = '$id' and AA.is_active = '1' and BB.is_active = '1' and CC.is_active = '1' limit 1) A 
        //         left join 
        //         (select grn_id, cost_acc_id, cost_ledger_name, cost_ledger_code, tax_acc_id, tax_ledger_name, tax_ledger_code, 
        //             invoice_no, ean, psku, product_title, vat_cst, vat_percen, box_price, cost_excl_vat_per_unit, tax_per_unit, 
        //             total_per_unit, cost_excl_vat, tax, total, qty as ".$ded_type."_qty from grn_acc_sku_entries 
        //         where grn_id = '$id' and ded_type = '$ded_type' and is_active = '1' order by invoice_no, vat_percen) B 
        //         on (A.grn_id = B.grn_id) 
        //         where B.invoice_no is not null 
        //         order by B.invoice_no, B.vat_percen";

        $sql = "select A.grn_id, B.*, B.qty as ".$ded_type."_qty, D.tax_zone_code, D.tax_zone_name, E.invoice_date from grn A 
                left join grn_acc_sku_entries B on (A.grn_id = B.grn_id) 
                left join internal_warehouse_master C on (A.warehouse_id = C.warehouse_code and A.company_id = C.company_id) 
                left join tax_zone_master D on (C.tax_zone_id = D.id) 
                left join goods_inward_outward_invoices E on (A.gi_id = E.gi_go_ref_no and B.invoice_no = E.invoice_no) 
                where A.grn_id = '$id' and B.grn_id = '$id' and A.is_active = '1' and B.is_active = '1' and 
                    C.is_active = '1' and D.is_active = '1' and B.invoice_no is not null and B.ded_type = '$ded_type' > 0 
                order by B.invoice_no, B.vat_percen";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getGrnAccLedgerEntries($id){
        $sql = "select B.*, A.tax_zone_code, A.tax_zone_name from 
                (select AA.*, CC.tax_zone_code, CC.tax_zone_name from grn AA 
                left join internal_warehouse_master BB on (AA.warehouse_id = BB.warehouse_code and AA.company_id = BB.company_id) 
                left join tax_zone_master CC on (BB.tax_zone_id = CC.id) 
                where AA.grn_id = '$id' and AA.is_active = '1' and BB.is_active = '1' and CC.is_active = '1' limit 1) A 
                left join 
                (select * from ledger_entries where ref_id = '$id' and ref_type = 'purchase' and status = 'pending' and is_active = '1' 
                order by ref_id, invoice_no, id) B 
                on (A.grn_id = B.ref_id) 
                order by B.ref_id, B.invoice_no, B.id";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getGrnParticulars(){
        $request = Yii::$app->request;
        $mycomponent = Yii::$app->mycomponent;
        $session = Yii::$app->session;

        $gi_id = $request->post('gi_id');
        $vendor_id = $request->post('vendor_id');
        $vendor_code = $request->post('vendor_code');
        $vendor_name = $request->post('vendor_name');
        $invoice_no = $request->post('invoice_no');

        // $taxable_amount = $request->post('taxable_amount');
        // $invoice_taxable_amount = $request->post('invoice_taxable_amount');
        // $edited_taxable_amount = $request->post('edited_taxable_amount');
        // $diff_taxable_amount = $request->post('diff_taxable_amount');
        // $narration_taxable_amount = $request->post('narration_taxable_amount');
        // $total_tax = $request->post('total_tax');
        // $invoice_total_tax = $request->post('invoice_total_tax');
        // $edited_total_tax = $request->post('edited_total_tax');
        // $diff_total_tax = $request->post('diff_total_tax');
        // $narration_total_tax = $request->post('narration_total_tax');

        $vat_cst = $request->post('vat_cst');
        $vat_percen = $request->post('vat_percen');
        $sub_particular_cost = $request->post('sub_particular_cost');
        $sub_particular_tax = $request->post('sub_particular_tax');
        $invoice_cost_acc_id = $request->post('invoice_cost_acc_id');
        $invoice_cost_ledger_name = $request->post('invoice_cost_ledger_name');
        $invoice_cost_ledger_code = $request->post('invoice_cost_ledger_code');
        $invoice_tax_acc_id = $request->post('invoice_tax_acc_id');
        $invoice_tax_ledger_name = $request->post('invoice_tax_ledger_name');
        $invoice_tax_ledger_code = $request->post('invoice_tax_ledger_code');

        for($i=0; $i<count($vat_cst); $i++){
            $total_cost[$i] = $request->post('total_cost_'.$i);
            $invoice_cost[$i] = $request->post('invoice_cost_'.$i);
            $edited_cost[$i] = $request->post('edited_cost_'.$i);
            $diff_cost[$i] = $request->post('diff_cost_'.$i);
            $narration_cost[$i] = $request->post('narration_cost_'.$i);

            $total_tax[$i] = $request->post('total_tax_'.$i);
            $invoice_tax[$i] = $request->post('invoice_tax_'.$i);
            $edited_tax[$i] = $request->post('edited_tax_'.$i);
            $diff_tax[$i] = $request->post('diff_tax_'.$i);
            $narration_tax[$i] = $request->post('narration_tax_'.$i);
        }

        $other_charges_acc_id = $request->post('other_charges_acc_id');
        $other_charges_ledger_name = $request->post('other_charges_ledger_name');
        $other_charges_ledger_code = $request->post('other_charges_ledger_code');
        $other_charges = $request->post('other_charges');
        $invoice_other_charges = $request->post('invoice_other_charges');
        $edited_other_charges = $request->post('edited_other_charges');
        $diff_other_charges = $request->post('diff_other_charges');
        $narration_other_charges = $request->post('narration_other_charges');

        $total_amount_acc_id = $request->post('total_amount_acc_id');
        $total_amount_ledger_name = $request->post('total_amount_ledger_name');
        $total_amount_ledger_code = $request->post('total_amount_ledger_code');
        $total_amount = $request->post('total_amount');
        $invoice_total_amount = $request->post('invoice_total_amount');
        $edited_total_amount = $request->post('edited_total_amount');
        $diff_total_amount = $request->post('diff_total_amount');
        $narration_total_amount = $request->post('narration_total_amount');
        
        $shortage_amount = $request->post('shortage_amount');
        $invoice_shortage_amount = $request->post('invoice_shortage_amount');
        $edited_shortage_amount = $request->post('edited_shortage_amount');
        $diff_shortage_amount = $request->post('diff_shortage_amount');
        $narration_shortage_amount = $request->post('narration_shortage_amount');
        $expiry_amount = $request->post('expiry_amount');
        $invoice_expiry_amount = $request->post('invoice_expiry_amount');
        $edited_expiry_amount = $request->post('edited_expiry_amount');
        $diff_expiry_amount = $request->post('diff_expiry_amount');
        $narration_expiry_amount = $request->post('narration_expiry_amount');
        $damaged_amount = $request->post('damaged_amount');
        $invoice_damaged_amount = $request->post('invoice_damaged_amount');
        $edited_damaged_amount = $request->post('edited_damaged_amount');
        $diff_damaged_amount = $request->post('diff_damaged_amount');
        $narration_damaged_amount = $request->post('narration_damaged_amount');
        $margin_diff_amount = $request->post('margin_diff_amount');
        $invoice_margin_diff_amount = $request->post('invoice_margin_diff_amount');
        $edited_margin_diff_amount = $request->post('edited_margin_diff_amount');
        $diff_margin_diff_amount = $request->post('diff_margin_diff_amount');
        $narration_margin_diff_amount = $request->post('narration_margin_diff_amount');

        $total_deduction_acc_id = $request->post('total_deduction_acc_id');
        $total_deduction_ledger_name = $request->post('total_deduction_ledger_name');
        $total_deduction_ledger_code = $request->post('total_deduction_ledger_code');
        $total_deduction = $request->post('total_deduction');
        $invoice_total_deduction = $request->post('invoice_total_deduction');
        $edited_total_deduction = $request->post('edited_total_deduction');
        $diff_total_deduction = $request->post('diff_total_deduction');
        $narration_total_deduction = $request->post('narration_total_deduction');
        
        $edited_total_payable_amount = $request->post('edited_total_payable_amount');

        
        $num = 0;

        for($i=0; $i<count($invoice_no); $i++){
            // $particular[$num] = "Taxable Amount";
            // $sub_particular_val[$num] = null;
            // $vat_cst_val[$num] = null;
            // $vat_percen_val[$num] = null;
            // $invoice_no_val[$num] = $invoice_no[$i];
            // $total_val[$num] = $taxable_amount;
            // $invoice_val[$num] = $invoice_taxable_amount[$i];
            // $edited_val[$num] = $edited_taxable_amount[$i];
            // $difference_val[$num] = $diff_taxable_amount[$i];
            // $narration_val[$num] = $narration_taxable_amount;
            // $num = $num + 1;

            // $particular[$num] = "Tax";
            // $sub_particular_val[$num] = null;
            // $vat_cst_val[$num] = null;
            // $vat_percen_val[$num] = null;
            // $invoice_no_val[$num] = $invoice_no[$i];
            // $total_val[$num] = $total_tax;
            // $invoice_val[$num] = $invoice_total_tax[$i];
            // $edited_val[$num] = $edited_total_tax[$i];
            // $difference_val[$num] = $diff_total_tax[$i];
            // $narration_val[$num] = $narration_total_tax;
            // $num = $num + 1;

            for ($j=0; $j<count($vat_cst); $j++){
                $edited_cost_val = $mycomponent->format_number($edited_cost[$j][$i],2);
                if($edited_cost_val>0){
                    $particular[$num] = "Taxable Amount";
                    $sub_particular_val[$num] = $sub_particular_cost[$j];
                    $acc_id[$num] = $invoice_cost_acc_id[$j];
                    $ledger_name[$num] = $invoice_cost_ledger_name[$j];
                    $ledger_code[$num] = $invoice_cost_ledger_code[$j];
                    $vat_cst_val[$num] = $vat_cst[$j];
                    $vat_percen_val[$num] = $vat_percen[$j];
                    $invoice_no_val[$num] = $invoice_no[$i];
                    $total_val[$num] = $total_cost[$j];
                    $invoice_val[$num] = $invoice_cost[$j][$i];
                    $edited_val[$num] = $edited_cost[$j][$i];
                    $difference_val[$num] = $diff_cost[$j][$i];
                    $narration_val[$num] = $narration_cost[$j];
                    $num = $num + 1;

                    $particular[$num] = "Tax";
                    $sub_particular_val[$num] = $sub_particular_tax[$j];
                    $acc_id[$num] = $invoice_tax_acc_id[$j];
                    $ledger_name[$num] = $invoice_tax_ledger_name[$j];
                    $ledger_code[$num] = $invoice_tax_ledger_code[$j];
                    $vat_cst_val[$num] = $vat_cst[$j];
                    $vat_percen_val[$num] = $vat_percen[$j];
                    $invoice_no_val[$num] = $invoice_no[$i];
                    $total_val[$num] = $total_tax[$j];
                    $invoice_val[$num] = $invoice_tax[$j][$i];
                    $edited_val[$num] = $edited_tax[$j][$i];
                    $difference_val[$num] = $diff_tax[$j][$i];
                    $narration_val[$num] = $narration_tax[$j];
                    $num = $num + 1;
                }
            }

            $particular[$num] = "Other Charges";
            $sub_particular_val[$num] = null;
            $acc_id[$num] = $other_charges_acc_id;
            $ledger_name[$num] = $other_charges_ledger_name;
            $ledger_code[$num] = $other_charges_ledger_code;
            $vat_cst_val[$num] = null;
            $vat_percen_val[$num] = null;
            $invoice_no_val[$num] = $invoice_no[$i];
            $total_val[$num] = $other_charges;
            $invoice_val[$num] = $invoice_other_charges[$i];
            $edited_val[$num] = $edited_other_charges[$i];
            $difference_val[$num] = $diff_other_charges[$i];
            $narration_val[$num] = $narration_other_charges;
            $num = $num + 1;

            $particular[$num] = "Total Amount";
            $sub_particular_val[$num] = null;
            $acc_id[$num] = $total_amount_acc_id;
            $ledger_name[$num] = $total_amount_ledger_name;
            $ledger_code[$num] = $total_amount_ledger_code;
            $vat_cst_val[$num] = null;
            $vat_percen_val[$num] = null;
            $invoice_no_val[$num] = $invoice_no[$i];
            $total_val[$num] = $total_amount;
            $invoice_val[$num] = $invoice_total_amount[$i];
            $edited_val[$num] = $edited_total_amount[$i];
            $difference_val[$num] = $diff_total_amount[$i];
            $narration_val[$num] = $narration_total_amount;
            $num = $num + 1;

            $particular[$num] = "Shortage Amount";
            $sub_particular_val[$num] = null;
            $acc_id[$num] = null;
            $ledger_name[$num] = null;
            $ledger_code[$num] = null;
            $vat_cst_val[$num] = null;
            $vat_percen_val[$num] = null;
            $invoice_no_val[$num] = $invoice_no[$i];
            $total_val[$num] = $shortage_amount;
            $invoice_val[$num] = $invoice_shortage_amount[$i];
            $edited_val[$num] = $edited_shortage_amount[$i];
            $difference_val[$num] = $diff_shortage_amount[$i];
            $narration_val[$num] = $narration_shortage_amount;
            $num = $num + 1;

            $particular[$num] = "Expiry Amount";
            $sub_particular_val[$num] = null;
            $acc_id[$num] = null;
            $ledger_name[$num] = null;
            $ledger_code[$num] = null;
            $vat_cst_val[$num] = null;
            $vat_percen_val[$num] = null;
            $invoice_no_val[$num] = $invoice_no[$i];
            $total_val[$num] = $expiry_amount;
            $invoice_val[$num] = $invoice_expiry_amount[$i];
            $edited_val[$num] = $edited_expiry_amount[$i];
            $difference_val[$num] = $diff_expiry_amount[$i];
            $narration_val[$num] = $narration_expiry_amount;
            $num = $num + 1;

            $particular[$num] = "Damaged Amount";
            $sub_particular_val[$num] = null;
            $acc_id[$num] = null;
            $ledger_name[$num] = null;
            $ledger_code[$num] = null;
            $vat_cst_val[$num] = null;
            $vat_percen_val[$num] = null;
            $invoice_no_val[$num] = $invoice_no[$i];
            $total_val[$num] = $damaged_amount;
            $invoice_val[$num] = $invoice_damaged_amount[$i];
            $edited_val[$num] = $edited_damaged_amount[$i];
            $difference_val[$num] = $diff_damaged_amount[$i];
            $narration_val[$num] = $narration_damaged_amount;
            $num = $num + 1;

            $particular[$num] = "Margin Diff Amount";
            $sub_particular_val[$num] = null;
            $acc_id[$num] = null;
            $ledger_name[$num] = null;
            $ledger_code[$num] = null;
            $vat_cst_val[$num] = null;
            $vat_percen_val[$num] = null;
            $invoice_no_val[$num] = $invoice_no[$i];
            $total_val[$num] = $margin_diff_amount;
            $invoice_val[$num] = $invoice_margin_diff_amount[$i];
            $edited_val[$num] = $edited_margin_diff_amount[$i];
            $difference_val[$num] = $diff_margin_diff_amount[$i];
            $narration_val[$num] = $narration_margin_diff_amount;
            $num = $num + 1;

            $particular[$num] = "Total Deduction";
            $sub_particular_val[$num] = null;
            $acc_id[$num] = $total_deduction_acc_id;
            $ledger_name[$num] = $total_deduction_ledger_name;
            $ledger_code[$num] = $total_deduction_ledger_code;
            $vat_cst_val[$num] = null;
            $vat_percen_val[$num] = null;
            $invoice_no_val[$num] = $invoice_no[$i];
            $total_val[$num] = $total_deduction;
            $invoice_val[$num] = $invoice_total_deduction[$i];
            $edited_val[$num] = $edited_total_deduction[$i];
            $difference_val[$num] = $diff_total_deduction[$i];
            $narration_val[$num] = $narration_total_deduction;
            $num = $num + 1;

            // $particular[$num] = "Total Payable Amount";
            // $sub_particular_val[$num] = null;
            // $vat_cst_val[$num] = null;
            // $vat_percen_val[$num] = null;
            // $invoice_no_val[$num] = $invoice_no[$i];
            // $total_val[$num] = null;
            // $invoice_val[$num] = null;
            // $edited_val[$num] = $edited_total_payable_amount[$i];
            // $difference_val[$num] = null;
            // $narration_val[$num] = null;
            // $num = $num + 1;
        }

        // echo count($particular);
        // echo '<br/>';
        $bulkInsertArray = array();
        $grnAccEntries = array();
        $ledgerArray = array();
        $j = 0;
        $k = 0;
        $l = 0;

        for($i=0; $i<count($particular); $i++){
            $bulkInsertArray[$j]=[
                'grn_id'=>$gi_id,
                'vendor_id'=>$vendor_id,
                'particular'=>$particular[$i],
                'sub_particular'=>$sub_particular_val[$i],
                'acc_id'=>$acc_id[$i],
                'ledger_name'=>$ledger_name[$i],
                'ledger_code'=>$ledger_code[$i],
                'vat_cst'=>$vat_cst_val[$i],
                'vat_percen'=>$mycomponent->format_number($vat_percen_val[$i],2),
                'invoice_no'=>$invoice_no_val[$i],
                'total_val'=>$mycomponent->format_number($total_val[$i],2),
                'invoice_val'=>$mycomponent->format_number($invoice_val[$i],2),
                'edited_val'=>$mycomponent->format_number($edited_val[$i],2),
                'difference_val'=>$mycomponent->format_number($difference_val[$i],2),
                'narration'=>$narration_val[$i],
                'status'=>'pending',
                'is_active'=>'1',
                'updated_by'=>$session['session_id'],
                'updated_date'=>date('Y-m-d h:i:s')
            ];

            $j = $j + 1;

            $ledg_particular = $particular[$i];
            if($ledg_particular=="Taxable Amount"){
                $ledg_type = "Debit";
                // $type = "Goods Purchase";
                // $legal_name = $particular[$i];
                // $code = $sub_particular_val[$i];
            } else if($ledg_particular=="Tax"){
                $ledg_type = "Debit";
                // $type = "Tax";
                // $legal_name = $particular[$i];
                // $code = $sub_particular_val[$i];
            } else if($ledg_particular=="Other Charges"){
                $ledg_type = "Debit";
                // $type = "Others";
                // $legal_name = $particular[$i];
                // $code = str_replace(" ", "_", $particular[$i]);
            } else if($ledg_particular=="Total Amount"){
                $ledg_type = "Credit";
                // $type = "Others";
                // $legal_name = $particular[$i];
                // $code = str_replace(" ", "_", $particular[$i]);
            } else if($ledg_particular=="Total Deduction"){
                $ledg_type = "Debit";
                // $type = "Vendor Goods";
                // $legal_name = $vendor_name;
                // $code = $vendor_code;
                // $ledg_particular = "Total Payable Amount - " . $vendor_name;

                
                $result = $this->getSkuEntries($gi_id, $request, $invoice_no_val[$i], 'shortage');
                $grnAccEntries = array_merge($grnAccEntries, $result['bulkInsertArray']);
                $ledgerArray = array_merge($ledgerArray, $result['ledgerArray']);

                $result = $this->getSkuEntries($gi_id, $request, $invoice_no_val[$i], 'expiry');
                $grnAccEntries = array_merge($grnAccEntries, $result['bulkInsertArray']);
                $ledgerArray = array_merge($ledgerArray, $result['ledgerArray']);

                $result = $this->getSkuEntries($gi_id, $request, $invoice_no_val[$i], 'damaged');
                $grnAccEntries = array_merge($grnAccEntries, $result['bulkInsertArray']);
                $ledgerArray = array_merge($ledgerArray, $result['ledgerArray']);

                $result = $this->getSkuEntries($gi_id, $request, $invoice_no_val[$i], 'margin_diff');
                $grnAccEntries = array_merge($grnAccEntries, $result['bulkInsertArray']);
                $ledgerArray = array_merge($ledgerArray, $result['ledgerArray']);

                $k = count($ledgerArray);
            } else {
                $ledg_type = "";
                // $type = "";
                // $legal_name = "";
                // $code = "";
            }

            if($ledg_type!=""){
                $ledgerArray[$k]=[
                                'ref_id'=>$gi_id,
                                'ref_type'=>'purchase',
                                'entry_type'=>$particular[$i],
                                'invoice_no'=>$invoice_no_val[$i],
                                'vendor_id'=>$vendor_id,
                                'acc_id'=>$acc_id[$i],
                                'ledger_name'=>$ledger_name[$i],
                                'ledger_code'=>$ledger_code[$i],
                                'type'=>$ledg_type,
                                'amount'=>$mycomponent->format_number($edited_val[$i],2),
                                'status'=>'pending',
                                'is_active'=>'1',
                                'updated_by'=>$session['session_id'],
                                'updated_date'=>date('Y-m-d h:i:s')
                            ];

                $k = $k + 1;

                // if($type == "Vendor Goods"){
                //     $this->setAccCode($type, $legal_name, $code, $vendor_id);
                // } else {
                //     $this->setAccCode($type, $legal_name, $code);
                // }
            }
        }

        $data['bulkInsertArray'] = $bulkInsertArray;
        $data['grnAccEntries'] = $grnAccEntries;
        $data['ledgerArray'] = $ledgerArray;

        return $data;
    }

    public function getSkuEntries($gi_id, $request, $invoice_no_val, $ded_type){
        $vendor_id = $request->post('vendor_id');
        $cost_acc_id = $request->post($ded_type.'_cost_acc_id');
        $cost_ledger_name = $request->post($ded_type.'_cost_ledger_name');
        $cost_ledger_code = $request->post($ded_type.'_cost_ledger_code');
        $tax_acc_id = $request->post($ded_type.'_tax_acc_id');
        $tax_ledger_name = $request->post($ded_type.'_tax_ledger_name');
        $tax_ledger_code = $request->post($ded_type.'_tax_ledger_code');
        $invoice_no = $request->post($ded_type.'_invoice_no');
        $state = $request->post($ded_type.'_state');
        $vat_cst = $request->post($ded_type.'_vat_cst');
        $vat_percen = $request->post($ded_type.'_vat_percen');
        $ean = $request->post($ded_type.'_ean');
        $psku = $request->post($ded_type.'_psku');
        $product_title = $request->post($ded_type.'_product_title');
        $qty = $request->post($ded_type.'_qty');
        $box_price = $request->post($ded_type.'_box_price');
        $cost_excl_tax_per_unit = $request->post($ded_type.'_cost_excl_tax_per_unit');
        $tax_per_unit = $request->post($ded_type.'_tax_per_unit');
        $total_per_unit = $request->post($ded_type.'_total_per_unit');
        $cost_excl_tax = $request->post($ded_type.'_cost_excl_tax');
        $tax = $request->post($ded_type.'_tax');
        $total = $request->post($ded_type.'_total');
        $expiry_date = $request->post($ded_type.'_expiry_date');
        $earliest_expected_date = $request->post($ded_type.'_earliest_expected_date');

        $bulkInsertArray = array();
        $ledgerArray = array();
        $mycomponent = Yii::$app->mycomponent;
        $session = Yii::$app->session;
        $k=0;
        $ledg_type='Credit';
        if($ded_type=='shortage'){
            $ledg_particular = "Shortage Taxable Amount";
            $ledg_particular_tax = "Shortage Tax";
        } else if($ded_type=='expiry'){
            $ledg_particular = "Expiry Taxable Amount";
            $ledg_particular_tax = "Expiry Tax";
        } else if($ded_type=='damaged'){
            $ledg_particular = "Damaged Taxable Amount";
            $ledg_particular_tax = "Damaged Tax";
        } else if($ded_type=='margin_diff'){
            $ledg_particular = "Margin Diff Taxable Amount";
            $ledg_particular_tax = "Margin Diff Tax";
        } else {
            $ledg_particular = "";
            $ledg_particular_tax = "";
        }
        for($i=0; $i<count($invoice_no); $i++){
            $qty_val = $mycomponent->format_number($qty[$i],2);
            if($qty_val>0 && $invoice_no_val==$invoice_no[$i]){
                $bulkInsertArray[$i]=[
                            'grn_id'=>$gi_id,
                            'vendor_id'=>$vendor_id,
                            'ded_type'=>$ded_type,
                            'cost_acc_id'=>$cost_acc_id[$i],
                            'cost_ledger_name'=>$cost_ledger_name[$i],
                            'cost_ledger_code'=>$cost_ledger_code[$i],
                            'tax_acc_id'=>$tax_acc_id[$i],
                            'tax_ledger_name'=>$tax_ledger_name[$i],
                            'tax_ledger_code'=>$tax_ledger_code[$i],
                            'invoice_no'=>$invoice_no[$i],
                            'state'=>$state[$i],
                            'vat_cst'=>$vat_cst[$i],
                            'vat_percen'=>$vat_percen[$i],
                            'ean'=>$ean[$i],
                            'psku'=>$psku[$i],
                            'product_title'=>$product_title[$i],
                            'qty'=>$qty_val,
                            'box_price'=>$mycomponent->format_number($box_price[$i],2),
                            'cost_excl_vat_per_unit'=>$mycomponent->format_number($cost_excl_tax_per_unit[$i],2),
                            'tax_per_unit'=>$mycomponent->format_number($tax_per_unit[$i],2),
                            'total_per_unit'=>$mycomponent->format_number($total_per_unit[$i],2),
                            'cost_excl_vat'=>$mycomponent->format_number($cost_excl_tax[$i],2),
                            'tax'=>$mycomponent->format_number($tax[$i],2),
                            'total'=>$mycomponent->format_number($total[$i],2),
                            'expiry_date'=>$expiry_date[$i],
                            'earliest_expected_date'=>$earliest_expected_date[$i],
                            'status'=>'pending',
                            'is_active'=>'1'
                        ];

                // $code = 'Purchase_'.$state[$i].'_'.$vat_cst[$i].'_'.$vat_percen[$i];
                $ledgerArray[$k]=[
                                'ref_id'=>$gi_id,
                                'ref_type'=>'purchase',
                                'entry_type'=>$ded_type.'_cost',
                                'invoice_no'=>$invoice_no[$i],
                                'vendor_id'=>$vendor_id,
                                'acc_id'=>$cost_acc_id[$i],
                                'ledger_name'=>$cost_ledger_name[$i],
                                'ledger_code'=>$cost_ledger_code[$i],
                                'type'=>$ledg_type,
                                'amount'=>$mycomponent->format_number($cost_excl_tax[$i],2),
                                'status'=>'pending',
                                'is_active'=>'1',
                                'updated_by'=>$session['session_id'],
                                'updated_date'=>date('Y-m-d h:i:s')
                            ];
                // $ledgerArray[$k]=[
                //             'grn_id'=>$gi_id,
                //             'vendor_id'=>$vendor_id,
                //             'code'=>$code,
                //             'invoice_no'=>$invoice_no[$i],
                //             'particular'=>$ledg_particular,
                //             'type'=>$ledg_type,
                //             'amount'=>$mycomponent->format_number($cost_excl_tax[$i],2),
                //             'status'=>'pending',
                //             'is_active'=>'1'
                //         ];
                $k = $k + 1;
                // $type = "Goods Purchase";
                // $legal_name = $ledg_particular;
                // $code = $code;
                // $this->setAccCode($type, $legal_name, $code);

                // $code = 'Tax_'.$state[$i].'_'.$vat_cst[$i].'_'.$vat_percen[$i];
                $ledgerArray[$k]=[
                                'ref_id'=>$gi_id,
                                'ref_type'=>'purchase',
                                'entry_type'=>$ded_type.'_tax',
                                'invoice_no'=>$invoice_no[$i],
                                'vendor_id'=>$vendor_id,
                                'acc_id'=>$tax_acc_id[$i],
                                'ledger_name'=>$tax_ledger_name[$i],
                                'ledger_code'=>$tax_ledger_code[$i],
                                'type'=>$ledg_type,
                                'amount'=>$mycomponent->format_number($tax[$i],2),
                                'status'=>'pending',
                                'is_active'=>'1',
                                'updated_by'=>$session['session_id'],
                                'updated_date'=>date('Y-m-d h:i:s')
                            ];
                // $ledgerArray[$k]=[
                //             'grn_id'=>$gi_id,
                //             'vendor_id'=>$vendor_id,
                //             'code'=>$code,
                //             'invoice_no'=>$invoice_no[$i],
                //             'particular'=>$ledg_particular_tax,
                //             'type'=>$ledg_type,
                //             'amount'=>$mycomponent->format_number($tax[$i],2),
                //             'status'=>'pending',
                //             'is_active'=>'1'
                //         ];
                $k = $k + 1;
                // $type = "Tax";
                // $legal_name = $ledg_particular_tax;
                // $code = $code;
                // $this->setAccCode($type, $legal_name, $code);
            }
        }

        $result['bulkInsertArray'] = $bulkInsertArray;
        $result['ledgerArray'] = $ledgerArray;
        return $result;
    }

    public function setAccCode($type, $legal_name, $code, $vendor_id=null){
        $sql = "select * from acc_master where type = '$type' and code = '$code' and is_active = '1'";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        $result = $reader->readAll();
        if(count($result)==0){
            $accArray = array('type' => $type, 
                                'legal_name' => $legal_name, 
                                'code' => $code,
                                'vendor_id' => $vendor_id,
                                'status' => 'pending',
                                'is_active' => '1');

            $columnNameArray=['type','legal_name','code','vendor_id','status','is_active'];
            $tableName = "acc_master";
            $count = Yii::$app->db->createCommand()
                        ->insert($tableName, $accArray)
                        ->execute();
        }
    }

    public function getSkuDetails(){
        $request = Yii::$app->request;
        $mycomponent = Yii::$app->mycomponent;

        $psku = $request->post('psku');
        $grn_id = $request->post('grn_id');

        // $psku = 'PET1236';
        // $grn_id = '28';

        $sql = "select A.tax_zone_code, A.tax_zone_name, B.* from 
                (select AA.*, CC.tax_zone_code, CC.tax_zone_name from grn AA 
                left join internal_warehouse_master BB on (AA.warehouse_id = BB.warehouse_code and AA.company_id = BB.company_id) 
                left join tax_zone_master CC on (BB.tax_zone_id = CC.id) 
                where AA.grn_id = '$grn_id' and AA.is_active = '1' and BB.is_active = '1' and CC.is_active = '1' limit 1) A 
                left join 
                (select * from grn_entries where grn_id = '$grn_id' and psku = '$psku' and is_active = '1') B 
                on(A.grn_id = B.grn_id)";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }
}