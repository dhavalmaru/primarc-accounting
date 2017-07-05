<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\Session;
use mPDF;

class PendingGrn extends Model
{
    public function getAccess(){
        $session = Yii::$app->session;
        $session_id = $session['session_id'];
        $role_id = $session['role_id'];

        $sql = "select A.*, '".$session_id."' as session_id from acc_user_role_options A 
                where A.role_id = '$role_id' and A.r_section = 'S_Purchase'";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getAccountDetails($id="", $status=""){
        $cond = "";
        $cond2 = "";
        if($id!=""){
            $cond = " and id = '$id'";
            $cond2 = " and acc_id = '$id'";
        }

        if($status!=""){
            $cond = $cond . " and status = '$status'";
        }

        $sql = "select A.*, concat_ws(',', A.category_1, A.category_2, A.category_3) as acc_category, B.bus_category from 
                (select * from acc_master where is_active = '1'" . $cond . ") A 
                left join 
                (select acc_id, GROUP_CONCAT(category_name) as bus_category from acc_categories where is_active = '1'" . $cond2 . " 
                    group by acc_id) B 
                on (A.id = B.acc_id) order by legal_name";

        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getNewGrnDetails(){
        $sql = "select * from 
                (select A.*, B.grn_id as b_grn_id from 
                (select A.*, B.username from grn A left join user B on(A.updated_by = B.id) 
                    where A.is_active = '1' and A.status = 'approved' and date(A.gi_date) > date('2017-07-01')) A 
                left join 
                (select distinct grn_id from acc_grn_entries) B 
                on (A.grn_id = B.grn_id)) C 
                where b_grn_id is null order by UNIX_TIMESTAMP(updated_date) desc";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getAllGrnDetails(){
        // $sql = "select * from 
        //         (select A.*, B.status as grn_status from 
        //         (select distinct A.*, B.username from grn A left join user B on(A.updated_by = B.id) 
        //             where A.is_active = '1' and A.status = 'approved' and date(A.gi_date) > date('2017-07-01')) A 
        //         left join 
        //         (select distinct grn_id, status from acc_grn_entries) B 
        //         on (A.grn_id = B.grn_id)) C order by UNIX_TIMESTAMP(updated_date) desc";
        // $command = Yii::$app->db->createCommand($sql);
        // $reader = $command->query();
        // return $reader->readAll();

        $sql = "select * from 
                (select A.*, B.status as grn_status from 
                (select distinct A.*, B.username, C.is_paid from grn A left join user B on(A.updated_by = B.id) 
                    left join acc_ledger_entries C on (A.grn_id = C.ref_id and 'purchase' = C.ref_type and 
                        '1' = C.is_active and 'Approved' = C.status and '1' = C.is_paid) 
                    where A.is_active = '1' and A.status = 'approved' and date(A.gi_date) > date('2017-07-01')) A 
                left join 
                (select distinct grn_id, status from acc_grn_entries) B 
                on (A.grn_id = B.grn_id)) C order by UNIX_TIMESTAMP(updated_date) desc";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

	public function getGrnDetails($id){
        $sql = "select A.*, B.vendor_code, C.gi_date as grn_date from grn A left join vendor_master B on (A.vendor_id = B.id) 
                left join acc_grn_entries C on (A.grn_id = C.grn_id and C.status = 'approved' and C.is_active='1' and 
                C.particular = 'Total Amount') where A.grn_id = '$id' and 
                A.status = 'approved' and A.is_active='1'";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getPurchaseDetails($status=""){
        $cond = "";
        if($status!=""){
            $cond = " and A.status = '$status'";
        }
        $sql = "select D.*, E.username from 
                (select B.*, C.grn_no, C.vendor_name, C.category_name, C.po_no from 
                (select grn_id, inv_nos, (taxable_amt+tax_amt+other_amt) as net_amt, 
                    (shortage_amt+expiry_amt+damaged_amt+magrin_diff_amt) as ded_amt, 
                    updated_date, updated_by, approved_by, is_paid from 
                (select distinct A.grn_id, GROUP_CONCAT(distinct A.invoice_no) as inv_nos, 
                        sum(case when A.particular='Taxable Amount' then A.edited_val else 0 end) as taxable_amt, 
                        sum(case when A.particular='Tax' then A.edited_val else 0 end) as tax_amt, 
                        sum(case when A.particular='Other Charges' then A.edited_val else 0 end) as other_amt, 
                        sum(case when A.particular='Shortage Amount' then A.edited_val else 0 end) as shortage_amt, 
                        sum(case when A.particular='Expiry Amount' then A.edited_val else 0 end) as expiry_amt, 
                        sum(case when A.particular='Damaged Amount' then A.edited_val else 0 end) as damaged_amt, 
                        sum(case when A.particular='Margin Diff Amount' then A.edited_val else 0 end) as magrin_diff_amt, 
                        max(A.updated_date) as updated_date, max(A.updated_by) as updated_by, max(A.approved_by) as approved_by, 
                        B.is_paid 
                from acc_grn_entries A 
                    left join acc_ledger_entries B on (A.grn_id = B.ref_id and 'purchase' = B.ref_type and 
                        '1' = B.is_active and 'Approved' = B.status and '1' = B.is_paid) 
                where A.is_active = '1' ".$cond." group by grn_id) A) B 
                left join 
                (select * from grn where status = 'approved') C 
                on (B.grn_id = C.grn_id)) D 
                left join 
                (select * from user) E 
                on (D.updated_by = E.id) 
                order by UNIX_TIMESTAMP(D.updated_date) desc";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getTotalValue($id){
        $sql = "select sum(total_cost) as total_cost, sum(total_tax) as total_tax, 0 as other_charges, sum(total_amount) as total_amount, 
                    sum(excess_amount) as excess_amount, sum(shortage_amount) as shortage_amount, sum(expiry_amount) as expiry_amount, 
                    sum(damaged_amount) as damaged_amount, sum(margindiff_amount) as margindiff_amount, sum(total_deduction) as total_deduction, 
                    sum(total_payable_amount) as total_payable_amount from 
                (select invoice_no, total_cost, total_tax, total_amount, excess_amount, shortage_amount, expiry_amount, damaged_amount, 
                    margindiff_amount, total_deduction, (total_amount-total_deduction) as total_payable_amount from 
                (select invoice_no, total_cost, total_tax, (total_cost+total_tax) as total_amount, excess_amount, shortage_amount, expiry_amount, 
                    damaged_amount, margindiff_amount, (shortage_amount+expiry_amount+damaged_amount+margindiff_amount) as total_deduction from 
                (select invoice_no, (total_cost+shortage_cost+expiry_cost+damaged_cost+margindiff_cost-excess_cost) as total_cost, 
                    (total_tax+shortage_tax+expiry_tax+damaged_tax+margindiff_tax-excess_tax) as total_tax, 
                    (excess_cost+excess_tax) as excess_amount, (shortage_cost+shortage_tax) as shortage_amount, 
                    (expiry_cost+expiry_tax) as expiry_amount, (damaged_cost+damaged_tax) as damaged_amount, 
                    (margindiff_cost+margindiff_tax) as margindiff_amount from 
                (select invoice_no, ifnull((invoice_qty*cost_excl_vat),0) as total_cost, ifnull((invoice_qty*cost_excl_vat*vat_percen)/100,0) as total_tax, 
                    ifnull((excess_qty*cost_excl_vat),0) as excess_cost, ifnull((excess_qty*cost_excl_vat*vat_percen)/100,0) as excess_tax, 
                    ifnull((shortage_qty*cost_excl_vat),0) as shortage_cost, ifnull((shortage_qty*cost_excl_vat*vat_percen)/100,0) as shortage_tax, 
                    ifnull((expiry_qty*cost_excl_vat),0) as expiry_cost, ifnull((expiry_qty*cost_excl_vat*vat_percen)/100,0) as expiry_tax, 
                    ifnull((damaged_qty*cost_excl_vat),0) as damaged_cost, ifnull((damaged_qty*cost_excl_vat*vat_percen)/100,0) as damaged_tax, 
                    ifnull(margindiff_cost,0) as margindiff_cost, ifnull((margindiff_cost*vat_percen)/100,0) as margindiff_tax from 
                (select A.grn_id, A.vat_cst, B.invoice_no, B.cost_excl_vat, B.vat_percen, B.invoice_qty, B.excess_qty, B.shortage_qty, B.expiry_qty, B.damaged_qty, 
                        case when round(ifnull((B.invoice_qty*B.cost_excl_vat),0)-ifnull((B.invoice_qty*D.cost_price_exc_tax),0), 2) < 0 
                            then 0 else round(ifnull((B.invoice_qty*B.cost_excl_vat),0)-ifnull((B.invoice_qty*D.cost_price_exc_tax),0), 2) end as margindiff_cost 
                from grn A 
                left join grn_entries B on (A.grn_id = B.grn_id) 
                left join purchase_order C on (A.po_no = C.po_no) 
                left join purchase_order_items D on (C.purchase_order_id = D.purchase_order_id and B.psku = D.psku) 
                where A.status = 'approved' and A.is_active = '1' and A.grn_id = '$id' and B.is_active = '1' and 
                B.grn_id = '$id' and B.invoice_no is not null) AA) A) B) C) D";
        // $sql = "select * from grn where grn_id = '".$id."'";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getInvoiceDetails($id){
        // $sql = "select invoice_no, total_cost as invoice_total_cost, total_tax as invoice_total_tax, total_amount as invoice_total_amount, 
        //         excess_amount as invoice_excess_amount, shortage_amount as invoice_shortage_amount, expiry_amount as invoice_expiry_amount, 
        //         damaged_amount as invoice_damaged_amount, margindiff_amount as invoice_margindiff_amount, total_deduction as invoice_total_deduction, 
        //         total_payable_amount as invoice_total_payable_amount, total_cost as edited_total_cost, total_tax as edited_total_tax, total_amount as edited_total_amount, 
        //         excess_amount as edited_excess_amount, shortage_amount as edited_shortage_amount, expiry_amount as edited_expiry_amount, 
        //         damaged_amount as edited_damaged_amount, margindiff_amount as edited_margindiff_amount, total_deduction as edited_total_deduction, 
        //         total_payable_amount as edited_total_payable_amount, 0 as diff_total_cost, 0 as diff_total_tax, 0 as diff_total_amount, 
        //         0 as diff_excess_amount, 0 as diff_shortage_amount, 0 as diff_expiry_amount, 0 as diff_damaged_amount, 
        //         0 as diff_margindiff_amount, 0 as diff_total_deduction, 0 as diff_total_payable_amount, 
        //         0 as invoice_other_charges, 0 as edited_other_charges, 0 as diff_other_charges, 
        //         null as total_amount_voucher_id, null as total_amount_ledger_type, 
        //         null as total_deduction_voucher_id, null as total_deduction_ledger_type from 
        //         (select invoice_no, sum(total_cost) as total_cost, sum(total_tax) as total_tax, sum(total_amount) as total_amount, 
        //         sum(excess_amount) as excess_amount, sum(shortage_amount) as shortage_amount, sum(expiry_amount) as expiry_amount, 
        //         sum(damaged_amount) as damaged_amount, sum(margindiff_amount) as margindiff_amount, sum(total_deduction) as total_deduction, 
        //         sum(total_payable_amount) as total_payable_amount from 
        //         (select invoice_no, total_cost, total_tax, total_amount, excess_amount, shortage_amount, expiry_amount, damaged_amount, 
        //             margindiff_amount, total_deduction, (total_amount-total_deduction) as total_payable_amount from 
        //         (select invoice_no, total_cost, total_tax, (total_cost+total_tax) as total_amount, excess_amount, shortage_amount, expiry_amount, 
        //             damaged_amount, margindiff_amount, (shortage_amount+expiry_amount+damaged_amount+margindiff_amount) as total_deduction from 
        //         (select invoice_no, (total_cost+shortage_cost+expiry_cost+damaged_cost+margindiff_cost-excess_cost) as total_cost, 
        //             (total_tax+shortage_tax+expiry_tax+damaged_tax+margindiff_tax-excess_tax) as total_tax, 
        //             (excess_cost+excess_tax) as excess_amount, (shortage_cost+shortage_tax) as shortage_amount, 
        //             (expiry_cost+expiry_tax) as expiry_amount, (damaged_cost+damaged_tax) as damaged_amount, 
        //             (margindiff_cost+margindiff_tax) as margindiff_amount from 
        //         (select invoice_no, total_cost, total_tax, excess_cost, excess_tax, shortage_cost, shortage_tax, expiry_cost, expiry_tax, damaged_cost, damaged_tax, 
        //                 case when margindiff_cost<=0 then 0 else margindiff_cost end as margindiff_cost, 
        //                 case when margindiff_tax<=0 then 0 else margindiff_tax end as margindiff_tax from 
        //         (select B.invoice_no, ifnull((B.invoice_qty*B.cost_excl_vat),0) as total_cost, ifnull((B.invoice_qty*B.cost_excl_vat*B.vat_percen)/100,0) as total_tax, 
        //             ifnull((B.excess_qty*B.cost_excl_vat),0) as excess_cost, ifnull((B.excess_qty*B.cost_excl_vat*B.vat_percen)/100,0) as excess_tax, 
        //             ifnull((B.shortage_qty*B.cost_excl_vat),0) as shortage_cost, ifnull((B.shortage_qty*B.cost_excl_vat*B.vat_percen)/100,0) as shortage_tax, 
        //             ifnull((B.expiry_qty*B.cost_excl_vat),0) as expiry_cost, ifnull((B.expiry_qty*B.cost_excl_vat*B.vat_percen)/100,0) as expiry_tax, 
        //             ifnull((B.damaged_qty*B.cost_excl_vat),0) as damaged_cost, ifnull((B.damaged_qty*B.cost_excl_vat*B.vat_percen)/100,0) as damaged_tax, 
        //             round(ifnull((B.invoice_qty*B.cost_excl_vat),0)-ifnull((B.invoice_qty*G.cost_price_exc_tax),0), 2) as margindiff_cost, 
        //             round(ifnull((B.invoice_qty*B.cost_excl_vat*B.vat_percen)/100,0)-ifnull((B.invoice_qty*G.cost_price_exc_tax*B.vat_percen)/100,0), 2) as margindiff_tax 
        //         from grn A 
        //         left join grn_entries B on (A.grn_id = B.grn_id) 
        //         left join internal_warehouse_master C on (A.warehouse_id = C.warehouse_code and A.company_id = C.company_id) 
        //         left join tax_zone_master D on (C.tax_zone_id = D.id) 
        //         left join goods_inward_outward_invoices E on (A.gi_id = E.gi_go_ref_no and B.invoice_no = E.invoice_no) 
        //         left join purchase_order F on (A.po_no = F.po_no) 
        //         left join purchase_order_items G on (F.purchase_order_id = G.purchase_order_id and B.psku = G.psku) 
        //         where A.grn_id = '$id' and B.grn_id = '$id' and A.is_active = '1' and B.is_active = '1' and 
        //             C.is_active = '1' and D.is_active = '1' and B.invoice_no is not null) AA) A) B) C) D 
        //         group by invoice_no) E order by invoice_no";

        $sql = "select invoice_no, total_cost as invoice_total_cost, total_tax as invoice_total_tax, total_amount as invoice_total_amount, 
                    excess_amount as invoice_excess_amount, shortage_amount as invoice_shortage_amount, expiry_amount as invoice_expiry_amount, 
                    damaged_amount as invoice_damaged_amount, margindiff_amount as invoice_margindiff_amount, total_deduction as invoice_total_deduction, 
                    total_payable_amount as invoice_total_payable_amount, total_cost as edited_total_cost, total_tax as edited_total_tax, total_amount as edited_total_amount, 
                    excess_amount as edited_excess_amount, shortage_amount as edited_shortage_amount, expiry_amount as edited_expiry_amount, 
                    damaged_amount as edited_damaged_amount, margindiff_amount as edited_margindiff_amount, total_deduction as edited_total_deduction, 
                    total_payable_amount as edited_total_payable_amount, 0 as diff_total_cost, 0 as diff_total_tax, 0 as diff_total_amount, 
                    0 as diff_excess_amount, 0 as diff_shortage_amount, 0 as diff_expiry_amount, 0 as diff_damaged_amount, 
                    0 as diff_margindiff_amount, 0 as diff_total_deduction, 0 as diff_total_payable_amount, 
                    0 as invoice_other_charges, 0 as edited_other_charges, 0 as diff_other_charges, 
                    null as total_amount_voucher_id, null as total_amount_ledger_type, 
                    null as total_deduction_voucher_id, null as total_deduction_ledger_type from 
                (select invoice_no, sum(total_cost) as total_cost, sum(total_tax) as total_tax, sum(total_amount) as total_amount, 
                    sum(excess_amount) as excess_amount, sum(shortage_amount) as shortage_amount, sum(expiry_amount) as expiry_amount, 
                    sum(damaged_amount) as damaged_amount, sum(margindiff_amount) as margindiff_amount, sum(total_deduction) as total_deduction, 
                    sum(total_payable_amount) as total_payable_amount from 
                (select invoice_no, total_cost, total_tax, total_amount, excess_amount, shortage_amount, expiry_amount, damaged_amount, 
                    margindiff_amount, total_deduction, (total_amount-total_deduction) as total_payable_amount from 
                (select invoice_no, total_cost, total_tax, (total_cost+total_tax) as total_amount, excess_amount, shortage_amount, expiry_amount, 
                    damaged_amount, margindiff_amount, (shortage_amount+expiry_amount+damaged_amount+margindiff_amount) as total_deduction from 
                (select invoice_no, total_cost, total_tax, 
                    (excess_cost+excess_tax) as excess_amount, (shortage_cost+shortage_tax) as shortage_amount, 
                    (expiry_cost+expiry_tax) as expiry_amount, (damaged_cost+damaged_tax) as damaged_amount, 
                    (margindiff_cost+margindiff_tax) as margindiff_amount from 
                (

                select DD.grn_id, DD.tax_zone_code, DD.tax_zone_name, DD.invoice_no, DD.vat_cst, DD.vat_percen, DD.total_cost, DD.total_tax, DD.total_cgst, 
                        DD.total_sgst, DD.total_igst, DD.excess_cost, DD.excess_tax, DD.excess_cgst, DD.excess_sgst, 
                        DD.excess_igst, DD.shortage_cost, DD.shortage_tax, DD.shortage_cgst, DD.shortage_sgst, 
                        DD.shortage_igst, DD.expiry_cost, DD.expiry_tax, DD.expiry_cgst, DD.expiry_sgst, 
                        DD.expiry_igst, DD.damaged_cost, DD.damaged_tax, DD.damaged_cgst, DD.damaged_sgst, 
                        DD.damaged_igst, DD.margindiff_cost, DD.margindiff_tax, DD.margindiff_cgst, 
                        DD.margindiff_sgst, DD.margindiff_igst from 
                (select CC.grn_id, CC.tax_zone_code, CC.tax_zone_name, CC.invoice_no, CC.vat_cst, CC.vat_percen, 
                    sum(CC.total_cost+CC.shortage_cost+CC.expiry_cost+CC.damaged_cost+CC.margindiff_cost-CC.excess_cost) as total_cost, 
                    sum(CC.total_tax+CC.shortage_tax+CC.expiry_tax+CC.damaged_tax+CC.margindiff_tax-CC.excess_tax) as total_tax, 
                    sum(CC.total_cgst+CC.shortage_cgst+CC.expiry_cgst+CC.damaged_cgst+CC.margindiff_cgst-CC.excess_cgst) as total_cgst, 
                    sum(CC.total_sgst+CC.shortage_sgst+CC.expiry_sgst+CC.damaged_sgst+CC.margindiff_sgst-CC.excess_sgst) as total_sgst, 
                    sum(CC.total_igst+CC.shortage_igst+CC.expiry_igst+CC.damaged_igst+CC.margindiff_igst-CC.excess_igst) as total_igst, 
                    sum(excess_cost) as excess_cost, sum(excess_tax) as excess_tax, sum(excess_cgst) as excess_cgst, 
                    sum(excess_sgst) as excess_sgst, sum(excess_igst) as excess_igst, 
                    sum(shortage_cost) as shortage_cost, sum(shortage_tax) as shortage_tax, sum(shortage_cgst) as shortage_cgst, 
                    sum(shortage_sgst) as shortage_sgst, sum(shortage_igst) as shortage_igst, 
                    sum(expiry_cost) as expiry_cost, sum(expiry_tax) as expiry_tax, sum(expiry_cgst) as expiry_cgst, 
                    sum(expiry_sgst) as expiry_sgst, sum(expiry_igst) as expiry_igst, 
                    sum(damaged_cost) as damaged_cost, sum(damaged_tax) as damaged_tax, sum(damaged_cgst) as damaged_cgst, 
                    sum(damaged_sgst) as damaged_sgst, sum(damaged_igst) as damaged_igst, 
                    sum(margindiff_cost) as margindiff_cost, sum(margindiff_tax) as margindiff_tax, sum(margindiff_cgst) as margindiff_cgst, 
                    sum(margindiff_sgst) as margindiff_sgst, sum(margindiff_igst) as margindiff_igst from 
                (select AA.grn_id, BB.tax_zone_code, BB.tax_zone_name, AA.invoice_no, AA.vat_cst, AA.vat_percen, BB.cgst_rate, BB.sgst_rate, BB.igst_rate, 
                    ifnull((AA.invoice_qty*AA.cost_excl_vat),0) as total_cost, ifnull((AA.invoice_qty*AA.cost_excl_vat*AA.vat_percen)/100,0) as total_tax, 
                    ifnull((AA.invoice_qty*AA.cost_excl_vat*BB.cgst_rate)/100,0) as total_cgst, ifnull((AA.invoice_qty*AA.cost_excl_vat*BB.sgst_rate)/100,0) as total_sgst, 
                    ifnull((AA.invoice_qty*AA.cost_excl_vat*BB.igst_rate)/100,0) as total_igst, 
                    ifnull((AA.excess_qty*AA.cost_excl_vat),0) as excess_cost, ifnull((AA.excess_qty*AA.cost_excl_vat*AA.vat_percen)/100,0) as excess_tax, 
                    ifnull((AA.excess_qty*AA.cost_excl_vat*BB.cgst_rate)/100,0) as excess_cgst, ifnull((AA.excess_qty*AA.cost_excl_vat*BB.sgst_rate)/100,0) as excess_sgst, 
                    ifnull((AA.excess_qty*AA.cost_excl_vat*BB.igst_rate)/100,0) as excess_igst, 
                    ifnull((AA.shortage_qty*AA.cost_excl_vat),0) as shortage_cost, ifnull((AA.shortage_qty*AA.cost_excl_vat*AA.vat_percen)/100,0) as shortage_tax, 
                    ifnull((AA.shortage_qty*AA.cost_excl_vat*BB.cgst_rate)/100,0) as shortage_cgst, ifnull((AA.shortage_qty*AA.cost_excl_vat*BB.sgst_rate)/100,0) as shortage_sgst, 
                    ifnull((AA.shortage_qty*AA.cost_excl_vat*BB.igst_rate)/100,0) as shortage_igst, 
                    ifnull((AA.expiry_qty*AA.cost_excl_vat),0) as expiry_cost, ifnull((AA.expiry_qty*AA.cost_excl_vat*AA.vat_percen)/100,0) as expiry_tax, 
                    ifnull((AA.expiry_qty*AA.cost_excl_vat*BB.cgst_rate)/100,0) as expiry_cgst, ifnull((AA.expiry_qty*AA.cost_excl_vat*BB.sgst_rate)/100,0) as expiry_sgst, 
                    ifnull((AA.expiry_qty*AA.cost_excl_vat*BB.igst_rate)/100,0) as expiry_igst, 
                    ifnull((AA.damaged_qty*AA.cost_excl_vat),0) as damaged_cost, ifnull((AA.damaged_qty*AA.cost_excl_vat*AA.vat_percen)/100,0) as damaged_tax, 
                    ifnull((AA.damaged_qty*AA.cost_excl_vat*BB.cgst_rate)/100,0) as damaged_cgst, ifnull((AA.damaged_qty*AA.cost_excl_vat*BB.sgst_rate)/100,0) as damaged_sgst, 
                    ifnull((AA.damaged_qty*AA.cost_excl_vat*BB.igst_rate)/100,0) as damaged_igst, 
                    AA.margindiff_cost as margindiff_cost, ifnull((AA.margindiff_cost*AA.vat_percen)/100,0) as margindiff_tax, 
                    ifnull((AA.margindiff_cost*BB.cgst_rate)/100,0) as margindiff_cgst, ifnull((AA.margindiff_cost*BB.sgst_rate)/100,0) as margindiff_sgst, 
                    ifnull((AA.margindiff_cost*BB.igst_rate)/100,0) as margindiff_igst from 
                (select A.grn_id, A.vat_cst, B.invoice_no, B.cost_excl_vat, B.vat_percen, B.invoice_qty, B.excess_qty, B.shortage_qty, B.expiry_qty, B.damaged_qty, 
                        case when round(ifnull((B.invoice_qty*B.cost_excl_vat),0)-ifnull((B.invoice_qty*D.cost_price_exc_tax),0), 2) < 0 
                            then 0 else round(ifnull((B.invoice_qty*B.cost_excl_vat),0)-ifnull((B.invoice_qty*D.cost_price_exc_tax),0), 2) end as margindiff_cost 
                from grn A 
                left join grn_entries B on (A.grn_id = B.grn_id) 
                left join purchase_order C on (A.po_no = C.po_no) 
                left join purchase_order_items D on (C.purchase_order_id = D.purchase_order_id and B.psku = D.psku) 
                where A.status = 'approved' and A.is_active = '1' and A.grn_id = '$id' and B.is_active = '1' and B.grn_id = '$id' and B.invoice_no is not null) AA 
                left join 
                (select id, tax_zone_code, tax_zone_name, parent_id, tax_rate, 
                    sum(case when child_tax_type_code = 'CGST' then child_tax_rate else 0 end) as cgst_rate, 
                    sum(case when child_tax_type_code = 'SGST' then child_tax_rate else 0 end) as sgst_rate, 
                    sum(case when child_tax_type_code = 'IGST' then child_tax_rate else 0 end) as igst_rate from 
                (select A.id, A.tax_zone_code, A.tax_zone_name, B.id as parent_id, B.tax_type_id as parent_tax_type_id, 
                    B.tax_rate, C.child_id, D.tax_type_id as child_tax_type_id, D.tax_rate as child_tax_rate, 
                    E.tax_category as child_tax_category, E.tax_type_code as child_tax_type_code, 
                    E.tax_type_name as child_tax_type_name 
                from tax_zone_master A 
                left join tax_rate_master B on (A.id = B.tax_zone_id) 
                left join tax_component C on (B.id = C.parent_id) 
                left join tax_rate_master D on (C.child_id = D.id) 
                left join tax_type_master E on (D.tax_type_id = E.id)) C 
                where child_tax_rate != 0
                group by id, tax_zone_code, tax_zone_name, parent_id, tax_rate) BB 
                on (AA.vat_cst = BB.tax_zone_code and round(AA.vat_percen,4)=round(BB.tax_rate,4))) CC 
                group by CC.grn_id, CC.tax_zone_code, CC.tax_zone_name, CC.invoice_no, CC.vat_cst, CC.vat_percen) DD 
                where DD.total_cost > 0 
                order by DD.tax_zone_code, DD.vat_percen, DD.vat_cst

                ) A) B) C) D 
                group by invoice_no) E order by invoice_no";

        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    // public function getInvoiceTax($id){
    //     $sql = "select invoice_no, vat_cst, vat_percen, total_tax as invoice_tax, total_tax as edited_tax, 0 as diff_tax from 
    //             (select invoice_no, vat_cst, vat_percen, sum(total_cost) as total_cost, sum(total_tax) as total_tax from 
    //             (select invoice_no, vat_cst, vat_percen, (total_cost+shortage_cost+expiry_cost+damaged_cost+margindiff_cost-excess_cost) as total_cost, 
    //                 (total_tax+shortage_tax+expiry_tax+damaged_tax+margindiff_tax-excess_tax) as total_tax from 
    //             (select invoice_no, vat_cst, vat_percen, ifnull((invoice_qty*cost_excl_vat),0) as total_cost, ifnull((invoice_qty*cost_excl_vat*vat_percen)/100,0) as total_tax, 
    //                 ifnull((excess_qty*cost_excl_vat),0) as excess_cost, ifnull((excess_qty*cost_excl_vat*vat_percen)/100,0) as excess_tax, 
    //                 ifnull((shortage_qty*cost_excl_vat),0) as shortage_cost, ifnull((shortage_qty*cost_excl_vat*vat_percen)/100,0) as shortage_tax, 
    //                 ifnull((expiry_qty*cost_excl_vat),0) as expiry_cost, ifnull((expiry_qty*cost_excl_vat*vat_percen)/100,0) as expiry_tax, 
    //                 ifnull((damaged_qty*cost_excl_vat),0) as damaged_cost, ifnull((damaged_qty*cost_excl_vat*vat_percen)/100,0) as damaged_tax, 
    //                 ifnull((0*cost_excl_vat),0) as margindiff_cost, ifnull((0*cost_excl_vat*vat_percen)/100,0) as margindiff_tax from grn_entries 
    //             where is_active = '1' and grn_id = '$id') A) B 
    //             group by invoice_no, vat_cst, vat_percen) C 
    //             order by invoice_no, vat_cst, vat_percen";
    //     // $sql = "select * from grn where grn_id = '".$id."'";
    //     $command = Yii::$app->db->createCommand($sql);
    //     $reader = $command->query();
    //     return $reader->readAll();
    // }

    public function getTotalTax($id){
        // $sql = "select A.tax_zone_code, A.tax_zone_name, B.* from 
        //         (select AA.*, CC.tax_zone_code, CC.tax_zone_name from grn AA 
        //             left join internal_warehouse_master BB on (AA.warehouse_id = BB.warehouse_code and AA.company_id = BB.company_id) 
        //         left join tax_zone_master CC on (BB.tax_zone_id = CC.id) 
        //         where AA.grn_id = '$id' and AA.is_active = '1' and BB.is_active = '1' and CC.is_active = '1' limit 1) A 
        //         left join 
        //         (select grn_id, invoice_cost_acc_id, invoice_cost_ledger_name, invoice_cost_ledger_code, 
        //             invoice_tax_acc_id, invoice_tax_ledger_name, invoice_tax_ledger_code, vat_cst, vat_percen, 
        //             sum(total_cost) as total_cost, sum(total_tax) as total_tax from 
        //         (select grn_id, invoice_cost_acc_id, invoice_cost_ledger_name, invoice_cost_ledger_code, 
        //             invoice_tax_acc_id, invoice_tax_ledger_name, invoice_tax_ledger_code, vat_cst, vat_percen, 
        //             (total_cost+shortage_cost+expiry_cost+damaged_cost+margindiff_cost-excess_cost) as total_cost, 
        //             (total_tax+shortage_tax+expiry_tax+damaged_tax+margindiff_tax-excess_tax) as total_tax from 
        //         (select grn_id, null as invoice_cost_acc_id, null as invoice_cost_ledger_name, null as invoice_cost_ledger_code, 
        //             null as invoice_tax_acc_id, null as invoice_tax_ledger_name, null as invoice_tax_ledger_code, vat_cst, vat_percen, 
        //             ifnull((invoice_qty*cost_excl_vat),0) as total_cost, ifnull((invoice_qty*cost_excl_vat*vat_percen)/100,0) as total_tax, 
        //             ifnull((excess_qty*cost_excl_vat),0) as excess_cost, ifnull((excess_qty*cost_excl_vat*vat_percen)/100,0) as excess_tax, 
        //             ifnull((shortage_qty*cost_excl_vat),0) as shortage_cost, ifnull((shortage_qty*cost_excl_vat*vat_percen)/100,0) as shortage_tax, 
        //             ifnull((expiry_qty*cost_excl_vat),0) as expiry_cost, ifnull((expiry_qty*cost_excl_vat*vat_percen)/100,0) as expiry_tax, 
        //             ifnull((damaged_qty*cost_excl_vat),0) as damaged_cost, ifnull((damaged_qty*cost_excl_vat*vat_percen)/100,0) as damaged_tax, 
        //             ifnull((0*cost_excl_vat),0) as margindiff_cost, ifnull((0*cost_excl_vat*vat_percen)/100,0) as margindiff_tax from grn_entries 
        //         where is_active = '1' and grn_id = '$id') A) B 
        //         group by grn_id, vat_percen, vat_cst order by grn_id, vat_percen, vat_cst) B 
        //         on (A.grn_id = B.grn_id) where total_cost > 0 
        //         order by A.tax_zone_code, B.vat_percen, B.vat_cst";

        $sql = "select DD.grn_id, DD.tax_zone_code, DD.tax_zone_name, DD.vat_cst, DD.vat_percen, 
                    DD.cgst_rate, DD.sgst_rate, DD.igst_rate, DD.total_cost, DD.total_tax, DD.total_cgst, 
                    DD.total_sgst, DD.total_igst, DD.excess_cost, DD.excess_tax, DD.excess_cgst, DD.excess_sgst, 
                    DD.excess_igst, DD.shortage_cost, DD.shortage_tax, DD.shortage_cgst, DD.shortage_sgst, 
                    DD.shortage_igst, DD.expiry_cost, DD.expiry_tax, DD.expiry_cgst, DD.expiry_sgst, 
                    DD.expiry_igst, DD.damaged_cost, DD.damaged_tax, DD.damaged_cgst, DD.damaged_sgst, 
                    DD.damaged_igst, DD.margindiff_cost, DD.margindiff_tax, DD.margindiff_cgst, 
                    DD.margindiff_sgst, DD.margindiff_igst, 
                    null as invoice_cost_acc_id, null as invoice_cost_ledger_name, null as invoice_cost_ledger_code, 
                    null as invoice_tax_acc_id, null as invoice_tax_ledger_name, null as invoice_tax_ledger_code, 
                    null as invoice_cgst_acc_id, null as invoice_cgst_ledger_name, null as invoice_cgst_ledger_code, 
                    null as invoice_sgst_acc_id, null as invoice_sgst_ledger_name, null as invoice_sgst_ledger_code, 
                    null as invoice_igst_acc_id, null as invoice_igst_ledger_name, null as invoice_igst_ledger_code from 
                (select CC.grn_id, CC.tax_zone_code, CC.tax_zone_name, CC.vat_cst, CC.vat_percen, 
                    CC.cgst_rate, CC.sgst_rate, CC.igst_rate, 
                    sum(CC.total_cost+CC.shortage_cost+CC.expiry_cost+CC.damaged_cost+CC.margindiff_cost-CC.excess_cost) as total_cost, 
                    sum(CC.total_tax+CC.shortage_tax+CC.expiry_tax+CC.damaged_tax+CC.margindiff_tax-CC.excess_tax) as total_tax, 
                    sum(CC.total_cgst+CC.shortage_cgst+CC.expiry_cgst+CC.damaged_cgst+CC.margindiff_cgst-CC.excess_cgst) as total_cgst, 
                    sum(CC.total_sgst+CC.shortage_sgst+CC.expiry_sgst+CC.damaged_sgst+CC.margindiff_sgst-CC.excess_sgst) as total_sgst, 
                    sum(CC.total_igst+CC.shortage_igst+CC.expiry_igst+CC.damaged_igst+CC.margindiff_igst-CC.excess_igst) as total_igst, 
                    sum(excess_cost) as excess_cost, sum(excess_tax) as excess_tax, sum(excess_cgst) as excess_cgst, 
                    sum(excess_sgst) as excess_sgst, sum(excess_igst) as excess_igst, 
                    sum(shortage_cost) as shortage_cost, sum(shortage_tax) as shortage_tax, sum(shortage_cgst) as shortage_cgst, 
                    sum(shortage_sgst) as shortage_sgst, sum(shortage_igst) as shortage_igst, 
                    sum(expiry_cost) as expiry_cost, sum(expiry_tax) as expiry_tax, sum(expiry_cgst) as expiry_cgst, 
                    sum(expiry_sgst) as expiry_sgst, sum(expiry_igst) as expiry_igst, 
                    sum(damaged_cost) as damaged_cost, sum(damaged_tax) as damaged_tax, sum(damaged_cgst) as damaged_cgst, 
                    sum(damaged_sgst) as damaged_sgst, sum(damaged_igst) as damaged_igst, 
                    sum(margindiff_cost) as margindiff_cost, sum(margindiff_tax) as margindiff_tax, sum(margindiff_cgst) as margindiff_cgst, 
                    sum(margindiff_sgst) as margindiff_sgst, sum(margindiff_igst) as margindiff_igst from 
                (select AA.grn_id, BB.tax_zone_code, BB.tax_zone_name, AA.invoice_no, AA.vat_cst, AA.vat_percen, 
                    BB.cgst_rate, BB.sgst_rate, BB.igst_rate, 
                    ifnull((AA.invoice_qty*AA.cost_excl_vat),0) as total_cost, ifnull((AA.invoice_qty*AA.cost_excl_vat*AA.vat_percen)/100,0) as total_tax, 
                    ifnull((AA.invoice_qty*AA.cost_excl_vat*BB.cgst_rate)/100,0) as total_cgst, ifnull((AA.invoice_qty*AA.cost_excl_vat*BB.sgst_rate)/100,0) as total_sgst, 
                    ifnull((AA.invoice_qty*AA.cost_excl_vat*BB.igst_rate)/100,0) as total_igst, 
                    ifnull((AA.excess_qty*AA.cost_excl_vat),0) as excess_cost, ifnull((AA.excess_qty*AA.cost_excl_vat*AA.vat_percen)/100,0) as excess_tax, 
                    ifnull((AA.excess_qty*AA.cost_excl_vat*BB.cgst_rate)/100,0) as excess_cgst, ifnull((AA.excess_qty*AA.cost_excl_vat*BB.sgst_rate)/100,0) as excess_sgst, 
                    ifnull((AA.excess_qty*AA.cost_excl_vat*BB.igst_rate)/100,0) as excess_igst, 
                    ifnull((AA.shortage_qty*AA.cost_excl_vat),0) as shortage_cost, ifnull((AA.shortage_qty*AA.cost_excl_vat*AA.vat_percen)/100,0) as shortage_tax, 
                    ifnull((AA.shortage_qty*AA.cost_excl_vat*BB.cgst_rate)/100,0) as shortage_cgst, ifnull((AA.shortage_qty*AA.cost_excl_vat*BB.sgst_rate)/100,0) as shortage_sgst, 
                    ifnull((AA.shortage_qty*AA.cost_excl_vat*BB.igst_rate)/100,0) as shortage_igst, 
                    ifnull((AA.expiry_qty*AA.cost_excl_vat),0) as expiry_cost, ifnull((AA.expiry_qty*AA.cost_excl_vat*AA.vat_percen)/100,0) as expiry_tax, 
                    ifnull((AA.expiry_qty*AA.cost_excl_vat*BB.cgst_rate)/100,0) as expiry_cgst, ifnull((AA.expiry_qty*AA.cost_excl_vat*BB.sgst_rate)/100,0) as expiry_sgst, 
                    ifnull((AA.expiry_qty*AA.cost_excl_vat*BB.igst_rate)/100,0) as expiry_igst, 
                    ifnull((AA.damaged_qty*AA.cost_excl_vat),0) as damaged_cost, ifnull((AA.damaged_qty*AA.cost_excl_vat*AA.vat_percen)/100,0) as damaged_tax, 
                    ifnull((AA.damaged_qty*AA.cost_excl_vat*BB.cgst_rate)/100,0) as damaged_cgst, ifnull((AA.damaged_qty*AA.cost_excl_vat*BB.sgst_rate)/100,0) as damaged_sgst, 
                    ifnull((AA.damaged_qty*AA.cost_excl_vat*BB.igst_rate)/100,0) as damaged_igst, 
                    AA.margindiff_cost as margindiff_cost, ifnull((AA.margindiff_cost*AA.vat_percen)/100,0) as margindiff_tax, 
                    ifnull((AA.margindiff_cost*BB.cgst_rate)/100,0) as margindiff_cgst, ifnull((AA.margindiff_cost*BB.sgst_rate)/100,0) as margindiff_sgst, 
                    ifnull((AA.margindiff_cost*BB.igst_rate)/100,0) as margindiff_igst from 
                (select A.grn_id, A.vat_cst, B.invoice_no, B.cost_excl_vat, B.vat_percen, B.invoice_qty, B.excess_qty, B.shortage_qty, B.expiry_qty, B.damaged_qty, 
                        case when round(ifnull((B.invoice_qty*B.cost_excl_vat),0)-ifnull((B.invoice_qty*D.cost_price_exc_tax),0), 2) < 0 
                            then 0 else round(ifnull((B.invoice_qty*B.cost_excl_vat),0)-ifnull((B.invoice_qty*D.cost_price_exc_tax),0), 2) end as margindiff_cost 
                from grn A 
                left join grn_entries B on (A.grn_id = B.grn_id) 
                left join purchase_order C on (A.po_no = C.po_no) 
                left join purchase_order_items D on (C.purchase_order_id = D.purchase_order_id and B.psku = D.psku) 
                where A.status = 'approved' and A.is_active = '1' and A.grn_id = '$id' and B.is_active = '1' and B.grn_id = '$id' and B.invoice_no is not null) AA 
                left join 
                (select id, tax_zone_code, tax_zone_name, parent_id, tax_rate, 
                    sum(case when child_tax_type_code = 'CGST' then child_tax_rate else 0 end) as cgst_rate, 
                    sum(case when child_tax_type_code = 'SGST' then child_tax_rate else 0 end) as sgst_rate, 
                    sum(case when child_tax_type_code = 'IGST' then child_tax_rate else 0 end) as igst_rate from 
                (select A.id, A.tax_zone_code, A.tax_zone_name, B.id as parent_id, B.tax_type_id as parent_tax_type_id, 
                    B.tax_rate, C.child_id, D.tax_type_id as child_tax_type_id, D.tax_rate as child_tax_rate, 
                    E.tax_category as child_tax_category, E.tax_type_code as child_tax_type_code, 
                    E.tax_type_name as child_tax_type_name 
                from tax_zone_master A 
                left join tax_rate_master B on (A.id = B.tax_zone_id) 
                left join tax_component C on (B.id = C.parent_id) 
                left join tax_rate_master D on (C.child_id = D.id) 
                left join tax_type_master E on (D.tax_type_id = E.id)) C 
                where child_tax_rate != 0
                group by id, tax_zone_code, tax_zone_name, parent_id, tax_rate) BB 
                on (AA.vat_cst = BB.tax_zone_code and round(AA.vat_percen,4)=round(BB.tax_rate,4))) CC 
                group by CC.grn_id, CC.tax_zone_code, CC.tax_zone_name, CC.vat_cst, CC.vat_percen, CC.cgst_rate, CC.sgst_rate, CC.igst_rate) DD 
                where DD.total_cost > 0 
                order by DD.tax_zone_code, DD.vat_percen, DD.vat_cst, DD.cgst_rate, DD.sgst_rate, DD.igst_rate";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getInvoiceTaxDetails($id){
        // $sql = "select A.tax_zone_code, A.tax_zone_name, B.* from 
        //         (select AA.*, CC.tax_zone_code, CC.tax_zone_name from grn AA 
        //         left join internal_warehouse_master BB on (AA.warehouse_id = BB.warehouse_code and AA.company_id = BB.company_id) 
        //         left join tax_zone_master CC on (BB.tax_zone_id = CC.id) 
        //         where AA.grn_id = '$id' and AA.is_active = '1' and BB.is_active = '1' and CC.is_active = '1' limit 1) A 
        //         left join 
        //         (select grn_id, invoice_no, vat_cst, vat_percen, total_cost as invoice_cost, total_tax as invoice_tax, 
        //             total_cost as edited_cost, total_tax as edited_tax, 0 as diff_cost, 0 as diff_tax, 
        //             null as invoice_cost_acc_id, null as invoice_cost_ledger_name, null as invoice_cost_ledger_code, 
        //             null as invoice_cost_voucher_id, null as invoice_cost_ledger_type, 
        //             null as invoice_tax_acc_id, null as invoice_tax_ledger_name, null as invoice_tax_ledger_code, 
        //             null as invoice_tax_voucher_id, null as invoice_tax_ledger_type from 
        //         (select grn_id, invoice_no, vat_cst, vat_percen, sum(total_cost) as total_cost, sum(total_tax) as total_tax from 
        //         (select grn_id, invoice_no, vat_cst, vat_percen, (total_cost+shortage_cost+expiry_cost+damaged_cost+margindiff_cost-excess_cost) as total_cost, 
        //             (total_tax+shortage_tax+expiry_tax+damaged_tax+margindiff_tax-excess_tax) as total_tax from 
        //         (select grn_id, invoice_no, vat_cst, vat_percen, ifnull((invoice_qty*cost_excl_vat),0) as total_cost, ifnull((invoice_qty*cost_excl_vat*vat_percen)/100,0) as total_tax, 
        //             ifnull((excess_qty*cost_excl_vat),0) as excess_cost, ifnull((excess_qty*cost_excl_vat*vat_percen)/100,0) as excess_tax, 
        //             ifnull((shortage_qty*cost_excl_vat),0) as shortage_cost, ifnull((shortage_qty*cost_excl_vat*vat_percen)/100,0) as shortage_tax, 
        //             ifnull((expiry_qty*cost_excl_vat),0) as expiry_cost, ifnull((expiry_qty*cost_excl_vat*vat_percen)/100,0) as expiry_tax, 
        //             ifnull((damaged_qty*cost_excl_vat),0) as damaged_cost, ifnull((damaged_qty*cost_excl_vat*vat_percen)/100,0) as damaged_tax, 
        //             ifnull((0*cost_excl_vat),0) as margindiff_cost, ifnull((0*cost_excl_vat*vat_percen)/100,0) as margindiff_tax from grn_entries 
        //         where is_active = '1' and grn_id = '$id') A) B 
        //         group by grn_id, invoice_no, vat_percen, vat_cst) C 
        //         where total_cost > 0 
        //         order by grn_id, invoice_no, vat_percen, vat_cst) B 
        //         on (A.grn_id = B.grn_id) 
        //         order by B.invoice_no, A.tax_zone_code, B.vat_percen, B.vat_cst";

        $sql = "select DD.grn_id, DD.tax_zone_code, DD.tax_zone_name, DD.invoice_no, DD.vat_cst, DD.vat_percen, 
                        DD.total_cost, DD.total_tax, DD.total_cgst, DD.total_sgst, DD.total_igst, 
                        DD.total_cost as invoice_cost, DD.total_tax as invoice_tax, DD.total_cgst as invoice_cgst, 
                        DD.total_sgst as invoice_sgst, DD.total_igst as invoice_igst, 
                        DD.total_cost as edited_cost, DD.total_tax as edited_tax, DD.total_cgst as edited_cgst, 
                        DD.total_sgst as edited_sgst, DD.total_igst as edited_igst, 
                        0 as diff_cost, 0 as diff_tax, 0 as diff_cgst, 0 as diff_sgst, 0 as diff_igst, 
                        DD.excess_cost, DD.excess_tax, DD.excess_cgst, DD.excess_sgst, 
                        DD.excess_igst, DD.shortage_cost, DD.shortage_tax, DD.shortage_cgst, DD.shortage_sgst, 
                        DD.shortage_igst, DD.expiry_cost, DD.expiry_tax, DD.expiry_cgst, DD.expiry_sgst, 
                        DD.expiry_igst, DD.damaged_cost, DD.damaged_tax, DD.damaged_cgst, DD.damaged_sgst, 
                        DD.damaged_igst, DD.margindiff_cost, DD.margindiff_tax, DD.margindiff_cgst, 
                        DD.margindiff_sgst, DD.margindiff_igst, 
                        null as invoice_cost_acc_id, null as invoice_cost_ledger_name, null as invoice_cost_ledger_code, 
                        null as invoice_tax_acc_id, null as invoice_tax_ledger_name, null as invoice_tax_ledger_code, 
                        null as invoice_cgst_acc_id, null as invoice_cgst_ledger_name, null as invoice_cgst_ledger_code, 
                        null as invoice_sgst_acc_id, null as invoice_sgst_ledger_name, null as invoice_sgst_ledger_code, 
                        null as invoice_igst_acc_id, null as invoice_igst_ledger_name, null as invoice_igst_ledger_code from 
                (select CC.grn_id, CC.tax_zone_code, CC.tax_zone_name, CC.invoice_no, CC.vat_cst, CC.vat_percen, 
                    sum(CC.total_cost+CC.shortage_cost+CC.expiry_cost+CC.damaged_cost+CC.margindiff_cost-CC.excess_cost) as total_cost, 
                    sum(CC.total_tax+CC.shortage_tax+CC.expiry_tax+CC.damaged_tax+CC.margindiff_tax-CC.excess_tax) as total_tax, 
                    sum(CC.total_cgst+CC.shortage_cgst+CC.expiry_cgst+CC.damaged_cgst+CC.margindiff_cgst-CC.excess_cgst) as total_cgst, 
                    sum(CC.total_sgst+CC.shortage_sgst+CC.expiry_sgst+CC.damaged_sgst+CC.margindiff_sgst-CC.excess_sgst) as total_sgst, 
                    sum(CC.total_igst+CC.shortage_igst+CC.expiry_igst+CC.damaged_igst+CC.margindiff_igst-CC.excess_igst) as total_igst, 
                    sum(excess_cost) as excess_cost, sum(excess_tax) as excess_tax, sum(excess_cgst) as excess_cgst, 
                    sum(excess_sgst) as excess_sgst, sum(excess_igst) as excess_igst, 
                    sum(shortage_cost) as shortage_cost, sum(shortage_tax) as shortage_tax, sum(shortage_cgst) as shortage_cgst, 
                    sum(shortage_sgst) as shortage_sgst, sum(shortage_igst) as shortage_igst, 
                    sum(expiry_cost) as expiry_cost, sum(expiry_tax) as expiry_tax, sum(expiry_cgst) as expiry_cgst, 
                    sum(expiry_sgst) as expiry_sgst, sum(expiry_igst) as expiry_igst, 
                    sum(damaged_cost) as damaged_cost, sum(damaged_tax) as damaged_tax, sum(damaged_cgst) as damaged_cgst, 
                    sum(damaged_sgst) as damaged_sgst, sum(damaged_igst) as damaged_igst, 
                    sum(margindiff_cost) as margindiff_cost, sum(margindiff_tax) as margindiff_tax, sum(margindiff_cgst) as margindiff_cgst, 
                    sum(margindiff_sgst) as margindiff_sgst, sum(margindiff_igst) as margindiff_igst from 
                (select AA.grn_id, BB.tax_zone_code, BB.tax_zone_name, AA.invoice_no, AA.vat_cst, AA.vat_percen, BB.cgst_rate, BB.sgst_rate, BB.igst_rate, 
                    ifnull((AA.invoice_qty*AA.cost_excl_vat),0) as total_cost, ifnull((AA.invoice_qty*AA.cost_excl_vat*AA.vat_percen)/100,0) as total_tax, 
                    ifnull((AA.invoice_qty*AA.cost_excl_vat*BB.cgst_rate)/100,0) as total_cgst, ifnull((AA.invoice_qty*AA.cost_excl_vat*BB.sgst_rate)/100,0) as total_sgst, 
                    ifnull((AA.invoice_qty*AA.cost_excl_vat*BB.igst_rate)/100,0) as total_igst, 
                    ifnull((AA.excess_qty*AA.cost_excl_vat),0) as excess_cost, ifnull((AA.excess_qty*AA.cost_excl_vat*AA.vat_percen)/100,0) as excess_tax, 
                    ifnull((AA.excess_qty*AA.cost_excl_vat*BB.cgst_rate)/100,0) as excess_cgst, ifnull((AA.excess_qty*AA.cost_excl_vat*BB.sgst_rate)/100,0) as excess_sgst, 
                    ifnull((AA.excess_qty*AA.cost_excl_vat*BB.igst_rate)/100,0) as excess_igst, 
                    ifnull((AA.shortage_qty*AA.cost_excl_vat),0) as shortage_cost, ifnull((AA.shortage_qty*AA.cost_excl_vat*AA.vat_percen)/100,0) as shortage_tax, 
                    ifnull((AA.shortage_qty*AA.cost_excl_vat*BB.cgst_rate)/100,0) as shortage_cgst, ifnull((AA.shortage_qty*AA.cost_excl_vat*BB.sgst_rate)/100,0) as shortage_sgst, 
                    ifnull((AA.shortage_qty*AA.cost_excl_vat*BB.igst_rate)/100,0) as shortage_igst, 
                    ifnull((AA.expiry_qty*AA.cost_excl_vat),0) as expiry_cost, ifnull((AA.expiry_qty*AA.cost_excl_vat*AA.vat_percen)/100,0) as expiry_tax, 
                    ifnull((AA.expiry_qty*AA.cost_excl_vat*BB.cgst_rate)/100,0) as expiry_cgst, ifnull((AA.expiry_qty*AA.cost_excl_vat*BB.sgst_rate)/100,0) as expiry_sgst, 
                    ifnull((AA.expiry_qty*AA.cost_excl_vat*BB.igst_rate)/100,0) as expiry_igst, 
                    ifnull((AA.damaged_qty*AA.cost_excl_vat),0) as damaged_cost, ifnull((AA.damaged_qty*AA.cost_excl_vat*AA.vat_percen)/100,0) as damaged_tax, 
                    ifnull((AA.damaged_qty*AA.cost_excl_vat*BB.cgst_rate)/100,0) as damaged_cgst, ifnull((AA.damaged_qty*AA.cost_excl_vat*BB.sgst_rate)/100,0) as damaged_sgst, 
                    ifnull((AA.damaged_qty*AA.cost_excl_vat*BB.igst_rate)/100,0) as damaged_igst, 
                    AA.margindiff_cost as margindiff_cost, ifnull((AA.margindiff_cost*AA.vat_percen)/100,0) as margindiff_tax, 
                    ifnull((AA.margindiff_cost*BB.cgst_rate)/100,0) as margindiff_cgst, ifnull((AA.margindiff_cost*BB.sgst_rate)/100,0) as margindiff_sgst, 
                    ifnull((AA.margindiff_cost*BB.igst_rate)/100,0) as margindiff_igst from 
                (select A.grn_id, A.vat_cst, B.invoice_no, B.cost_excl_vat, B.vat_percen, B.invoice_qty, B.excess_qty, B.shortage_qty, B.expiry_qty, B.damaged_qty, 
                        case when round(ifnull((B.invoice_qty*B.cost_excl_vat),0)-ifnull((B.invoice_qty*D.cost_price_exc_tax),0), 2) < 0 
                            then 0 else round(ifnull((B.invoice_qty*B.cost_excl_vat),0)-ifnull((B.invoice_qty*D.cost_price_exc_tax),0), 2) end as margindiff_cost 
                from grn A 
                left join grn_entries B on (A.grn_id = B.grn_id) 
                left join purchase_order C on (A.po_no = C.po_no) 
                left join purchase_order_items D on (C.purchase_order_id = D.purchase_order_id and B.psku = D.psku) 
                where A.status = 'approved' and A.is_active = '1' and A.grn_id = '$id' and B.is_active = '1' and B.grn_id = '$id' and B.invoice_no is not null) AA 
                left join 
                (select id, tax_zone_code, tax_zone_name, parent_id, tax_rate, 
                    sum(case when child_tax_type_code = 'CGST' then child_tax_rate else 0 end) as cgst_rate, 
                    sum(case when child_tax_type_code = 'SGST' then child_tax_rate else 0 end) as sgst_rate, 
                    sum(case when child_tax_type_code = 'IGST' then child_tax_rate else 0 end) as igst_rate from 
                (select A.id, A.tax_zone_code, A.tax_zone_name, B.id as parent_id, B.tax_type_id as parent_tax_type_id, 
                    B.tax_rate, C.child_id, D.tax_type_id as child_tax_type_id, D.tax_rate as child_tax_rate, 
                    E.tax_category as child_tax_category, E.tax_type_code as child_tax_type_code, 
                    E.tax_type_name as child_tax_type_name 
                from tax_zone_master A 
                left join tax_rate_master B on (A.id = B.tax_zone_id) 
                left join tax_component C on (B.id = C.parent_id) 
                left join tax_rate_master D on (C.child_id = D.id) 
                left join tax_type_master E on (D.tax_type_id = E.id)) C 
                where child_tax_rate != 0
                group by id, tax_zone_code, tax_zone_name, parent_id, tax_rate) BB 
                on (AA.vat_cst = BB.tax_zone_code and round(AA.vat_percen,4)=round(BB.tax_rate,4))) CC 
                group by CC.grn_id, CC.tax_zone_code, CC.tax_zone_name, CC.invoice_no, CC.vat_cst, CC.vat_percen) DD 
                where DD.total_cost > 0 
                order by DD.invoice_no, DD.tax_zone_code, DD.vat_percen, DD.vat_cst";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getGrnAccEntries($id){
        // $sql = "select A.tax_zone_code, A.tax_zone_name, B.* from 
        //         (select AA.*, CC.tax_zone_code, CC.tax_zone_name from grn AA 
        //             left join internal_warehouse_master BB on (AA.warehouse_id = BB.warehouse_code and AA.company_id = BB.company_id) 
        //             left join tax_zone_master CC on (BB.tax_zone_id = CC.id) 
        //         where AA.grn_id = '$id' and AA.is_active = '1' and BB.is_active = '1' and CC.is_active = '1' limit 1) A 
        //         left join 
        //         (select * from acc_grn_entries where grn_id = '$id' and status = 'approved' and is_active = '1' 
        //         order by grn_id, invoice_no, id, vat_percen, vat_cst) B 
        //         on (A.grn_id = B.grn_id) 
        //         where B.id is not null
        //         order by B.id";

        $sql = "select A.tax_zone_code, A.tax_zone_name, B.* from 
                (select AA.*, BB.tax_zone_code, BB.tax_zone_name from grn AA 
                    left join tax_zone_master BB on (AA.vat_cst = BB.tax_zone_code) 
                where AA.grn_id = '$id' and AA.is_active = '1' and BB.is_active = '1' limit 1) A 
                left join 
                (select * from acc_grn_entries where grn_id = '$id' and status = 'approved' and is_active = '1' 
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

        $sql = "select AA.*, BB.cgst_rate, BB.sgst_rate, BB.igst_rate from 
                (select * from 
                (select AA.*, case when AA.margindiff_amount>0 then AA.invoice_qty else 0 end as margindiff_qty from 
                (select B.*, D.tax_zone_code, D.tax_zone_name, E.invoice_date, A.gi_date, 
                    date_add(A.gi_date, interval ifnull(min_no_of_months_shelf_life_required,0) month) as earliest_expected_date, 
                    null as cost_acc_id, null as cost_ledger_name, null as cost_ledger_code, 
                    null as tax_acc_id, null as tax_ledger_name, null as tax_ledger_code, 
                    null as cgst_acc_id, null as cgst_ledger_name, null as cgst_ledger_code, 
                    null as sgst_acc_id, null as sgst_ledger_name, null as sgst_ledger_code, 
                    null as igst_acc_id, null as igst_ledger_name, null as igst_ledger_code, 
                    F.purchase_order_id, G.quantity as po_quantity, G.cost_price_exc_tax as po_unit_rate_excl_tax, 
                    G.unit_tax_amount as po_unit_tax, G.cost_price_inc_tax as po_unit_rate_incl_tax, 
                    ifnull((B.invoice_qty*B.cost_incl_vat_cst),0)-ifnull((B.invoice_qty*G.cost_price_inc_tax),0) as margindiff_amount 
                from grn A 
                left join grn_entries B on (A.grn_id = B.grn_id) 
                left join tax_zone_master D on (A.vat_cst = D.tax_zone_code) 
                left join goods_inward_outward_invoices E on (A.gi_id = E.gi_go_ref_no and B.invoice_no = E.invoice_no) 
                left join purchase_order F on (A.po_no = F.po_no) 
                left join purchase_order_items G on (F.purchase_order_id = G.purchase_order_id and B.psku = G.psku) 
                where A.grn_id = '$id' and B.grn_id = '$id' and A.is_active = '1' and B.is_active = '1' and 
                    D.is_active = '1' and B.invoice_no is not null) AA) BB 
                where BB." . $col_qty . " > 0 
                order by BB.invoice_no, BB.vat_percen) AA 
                left join 
                (select id, tax_zone_code, tax_zone_name, parent_id, tax_rate, 
                    sum(case when child_tax_type_code = 'CGST' then child_tax_rate else 0 end) as cgst_rate, 
                    sum(case when child_tax_type_code = 'SGST' then child_tax_rate else 0 end) as sgst_rate, 
                    sum(case when child_tax_type_code = 'IGST' then child_tax_rate else 0 end) as igst_rate from 
                (select A.id, A.tax_zone_code, A.tax_zone_name, B.id as parent_id, B.tax_type_id as parent_tax_type_id, 
                    B.tax_rate, C.child_id, D.tax_type_id as child_tax_type_id, D.tax_rate as child_tax_rate, 
                    E.tax_category as child_tax_category, E.tax_type_code as child_tax_type_code, 
                    E.tax_type_name as child_tax_type_name 
                from tax_zone_master A 
                left join tax_rate_master B on (A.id = B.tax_zone_id) 
                left join tax_component C on (B.id = C.parent_id) 
                left join tax_rate_master D on (C.child_id = D.id) 
                left join tax_type_master E on (D.tax_type_id = E.id)) C 
                where child_tax_rate != 0
                group by id, tax_zone_code, tax_zone_name, parent_id, tax_rate) BB 
                on (AA.tax_zone_code = BB.tax_zone_code and round(AA.vat_percen,4)=round(BB.tax_rate,4))";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getGrnAccSku($id){
        $sql = "select * from acc_grn_sku_entries where grn_id = '$id' and is_active = '1' order by invoice_no, vat_percen";
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
        //             total_per_unit, cost_excl_vat, tax, total, qty as ".$ded_type."_qty from acc_grn_sku_entries 
        //         where grn_id = '$id' and ded_type = '$ded_type' and is_active = '1' order by invoice_no, vat_percen) B 
        //         on (A.grn_id = B.grn_id) 
        //         where B.invoice_no is not null 
        //         order by B.invoice_no, B.vat_percen";

        $sql = "select A.grn_id, B.*, B.qty as ".$ded_type."_qty, D.tax_zone_code, D.tax_zone_name, E.invoice_date 
                from grn A 
                left join acc_grn_sku_entries B on (A.grn_id = B.grn_id) 
                left join tax_zone_master D on (A.vat_cst = D.tax_zone_code) 
                left join goods_inward_outward_invoices E on (A.gi_id = E.gi_go_ref_no and B.invoice_no = E.invoice_no) 
                where A.grn_id = '$id' and B.grn_id = '$id' and A.is_active = '1' and B.is_active = '1' and 
                    D.is_active = '1' and B.invoice_no is not null and B.ded_type = '$ded_type' and B.qty > 0 
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
                (select A.*, C.invoice_date, D.due_date from acc_ledger_entries A 
                    left join grn B on(A.ref_id = B.grn_id and A.ref_type = 'purchase') 
                    left join goods_inward_outward_invoices C on(A.invoice_no = C.invoice_no and 
                     A.ref_type = 'purchase' and B.gi_id = C.gi_go_ref_no) 
                    left join invoice_tracker D on(A.ref_id=D.gi_id and A.invoice_no=D.invoice_id) 
                where A.ref_id = '$id' and A.status = 'approved' and A.is_active = '1' and A.ref_type = 'purchase' and 
                        B.status = 'Approved' and B.is_active = '1' 
                order by A.ref_id, A.invoice_no, A.id) B 
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
        $gi_date = $request->post('gi_date');
        if($gi_date==''){
            $gi_date=NULL;
        } else {
            $gi_date=$mycomponent->formatdate($gi_date);
        }

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
        $sub_particular_cgst = $request->post('sub_particular_cgst');
        $sub_particular_sgst = $request->post('sub_particular_sgst');
        $sub_particular_igst = $request->post('sub_particular_igst');
        $invoice_cost_acc_id = $request->post('invoice_cost_acc_id');
        $invoice_cost_ledger_name = $request->post('invoice_cost_ledger_name');
        $invoice_cost_ledger_code = $request->post('invoice_cost_ledger_code');
        // $invoice_cost_voucher_id = $request->post('invoice_cost_voucher_id');
        // $invoice_cost_ledger_type = $request->post('invoice_cost_ledger_type');
        $invoice_tax_acc_id = $request->post('invoice_tax_acc_id');
        $invoice_tax_ledger_name = $request->post('invoice_tax_ledger_name');
        $invoice_tax_ledger_code = $request->post('invoice_tax_ledger_code');
        // $invoice_tax_voucher_id = $request->post('invoice_tax_voucher_id');
        // $invoice_tax_ledger_type = $request->post('invoice_tax_ledger_type');
        $invoice_cgst_acc_id = $request->post('invoice_cgst_acc_id');
        $invoice_cgst_ledger_name = $request->post('invoice_cgst_ledger_name');
        $invoice_cgst_ledger_code = $request->post('invoice_cgst_ledger_code');
        // $invoice_cgst_voucher_id = $request->post('invoice_cgst_voucher_id');
        // $invoice_cgst_ledger_type = $request->post('invoice_cgst_ledger_type');
        $invoice_sgst_acc_id = $request->post('invoice_sgst_acc_id');
        $invoice_sgst_ledger_name = $request->post('invoice_sgst_ledger_name');
        $invoice_sgst_ledger_code = $request->post('invoice_sgst_ledger_code');
        // $invoice_sgst_voucher_id = $request->post('invoice_sgst_voucher_id');
        // $invoice_sgst_ledger_type = $request->post('invoice_sgst_ledger_type');
        $invoice_igst_acc_id = $request->post('invoice_igst_acc_id');
        $invoice_igst_ledger_name = $request->post('invoice_igst_ledger_name');
        $invoice_igst_ledger_code = $request->post('invoice_igst_ledger_code');
        // $invoice_igst_voucher_id = $request->post('invoice_igst_voucher_id');
        // $invoice_igst_ledger_type = $request->post('invoice_igst_ledger_type');

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

            $total_cgst[$i] = $request->post('total_cgst_'.$i);
            $invoice_cgst[$i] = $request->post('invoice_cgst_'.$i);
            $edited_cgst[$i] = $request->post('edited_cgst_'.$i);
            $diff_cgst[$i] = $request->post('diff_cgst_'.$i);
            $narration_cgst[$i] = $request->post('narration_cgst_'.$i);

            $total_sgst[$i] = $request->post('total_sgst_'.$i);
            $invoice_sgst[$i] = $request->post('invoice_sgst_'.$i);
            $edited_sgst[$i] = $request->post('edited_sgst_'.$i);
            $diff_sgst[$i] = $request->post('diff_sgst_'.$i);
            $narration_sgst[$i] = $request->post('narration_sgst_'.$i);

            $total_igst[$i] = $request->post('total_igst_'.$i);
            $invoice_igst[$i] = $request->post('invoice_igst_'.$i);
            $edited_igst[$i] = $request->post('edited_igst_'.$i);
            $diff_igst[$i] = $request->post('diff_igst_'.$i);
            $narration_igst[$i] = $request->post('narration_igst_'.$i);
        }

        $other_charges_acc_id = $request->post('other_charges_acc_id');
        $other_charges_ledger_name = $request->post('other_charges_ledger_name');
        $other_charges_ledger_code = $request->post('other_charges_ledger_code');
        // $other_charges_voucher_id = $request->post('other_charges_voucher_id');
        // $other_charges_ledger_type = $request->post('other_charges_ledger_type');
        $other_charges = $request->post('other_charges');
        $invoice_other_charges = $request->post('invoice_other_charges');
        $edited_other_charges = $request->post('edited_other_charges');
        $diff_other_charges = $request->post('diff_other_charges');
        $narration_other_charges = $request->post('narration_other_charges');

        $total_amount_acc_id = $request->post('total_amount_acc_id');
        $total_amount_ledger_name = $request->post('total_amount_ledger_name');
        $total_amount_ledger_code = $request->post('total_amount_ledger_code');
        $total_amount_voucher_id = $request->post('total_amount_voucher_id');
        $total_amount_ledger_type = $request->post('total_amount_ledger_type');
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
        $margindiff_amount = $request->post('margindiff_amount');
        $invoice_margindiff_amount = $request->post('invoice_margindiff_amount');
        $edited_margindiff_amount = $request->post('edited_margindiff_amount');
        $diff_margindiff_amount = $request->post('diff_margindiff_amount');
        $narration_margindiff_amount = $request->post('narration_margindiff_amount');

        $total_deduction_acc_id = $request->post('total_deduction_acc_id');
        $total_deduction_ledger_name = $request->post('total_deduction_ledger_name');
        $total_deduction_ledger_code = $request->post('total_deduction_ledger_code');
        $total_deduction_voucher_id = $request->post('total_deduction_voucher_id');
        $total_deduction_ledger_type = $request->post('total_deduction_ledger_type');
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
                    $voucher_id[$num] = $total_amount_voucher_id[$i];
                    $ledger_type[$num] = 'Sub Entry';
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
                    $voucher_id[$num] = $total_amount_voucher_id[$i];
                    $ledger_type[$num] = 'Sub Entry';
                    $vat_cst_val[$num] = $vat_cst[$j];
                    $vat_percen_val[$num] = $vat_percen[$j];
                    $invoice_no_val[$num] = $invoice_no[$i];
                    $total_val[$num] = $total_tax[$j];
                    $invoice_val[$num] = $invoice_tax[$j][$i];
                    $edited_val[$num] = $edited_tax[$j][$i];
                    $difference_val[$num] = $diff_tax[$j][$i];
                    $narration_val[$num] = $narration_tax[$j];
                    $num = $num + 1;

                    $particular[$num] = "CGST";
                    $sub_particular_val[$num] = $sub_particular_cgst[$j];
                    $acc_id[$num] = $invoice_cgst_acc_id[$j];
                    $ledger_name[$num] = $invoice_cgst_ledger_name[$j];
                    $ledger_code[$num] = $invoice_cgst_ledger_code[$j];
                    $voucher_id[$num] = $total_amount_voucher_id[$i];
                    $ledger_type[$num] = 'Sub Entry';
                    $vat_cst_val[$num] = $vat_cst[$j];
                    $vat_percen_val[$num] = $vat_percen[$j];
                    $invoice_no_val[$num] = $invoice_no[$i];
                    $total_val[$num] = $total_cgst[$j];
                    $invoice_val[$num] = $invoice_cgst[$j][$i];
                    $edited_val[$num] = $edited_cgst[$j][$i];
                    $difference_val[$num] = $diff_cgst[$j][$i];
                    $narration_val[$num] = $narration_cgst[$j];
                    $num = $num + 1;

                    $particular[$num] = "SGST";
                    $sub_particular_val[$num] = $sub_particular_sgst[$j];
                    $acc_id[$num] = $invoice_sgst_acc_id[$j];
                    $ledger_name[$num] = $invoice_sgst_ledger_name[$j];
                    $ledger_code[$num] = $invoice_sgst_ledger_code[$j];
                    $voucher_id[$num] = $total_amount_voucher_id[$i];
                    $ledger_type[$num] = 'Sub Entry';
                    $vat_cst_val[$num] = $vat_cst[$j];
                    $vat_percen_val[$num] = $vat_percen[$j];
                    $invoice_no_val[$num] = $invoice_no[$i];
                    $total_val[$num] = $total_sgst[$j];
                    $invoice_val[$num] = $invoice_sgst[$j][$i];
                    $edited_val[$num] = $edited_sgst[$j][$i];
                    $difference_val[$num] = $diff_sgst[$j][$i];
                    $narration_val[$num] = $narration_sgst[$j];
                    $num = $num + 1;

                    $particular[$num] = "IGST";
                    $sub_particular_val[$num] = $sub_particular_igst[$j];
                    $acc_id[$num] = $invoice_igst_acc_id[$j];
                    $ledger_name[$num] = $invoice_igst_ledger_name[$j];
                    $ledger_code[$num] = $invoice_igst_ledger_code[$j];
                    $voucher_id[$num] = $total_amount_voucher_id[$i];
                    $ledger_type[$num] = 'Sub Entry';
                    $vat_cst_val[$num] = $vat_cst[$j];
                    $vat_percen_val[$num] = $vat_percen[$j];
                    $invoice_no_val[$num] = $invoice_no[$i];
                    $total_val[$num] = $total_igst[$j];
                    $invoice_val[$num] = $invoice_igst[$j][$i];
                    $edited_val[$num] = $edited_igst[$j][$i];
                    $difference_val[$num] = $diff_igst[$j][$i];
                    $narration_val[$num] = $narration_igst[$j];
                    $num = $num + 1;
                }
            }

            $particular[$num] = "Other Charges";
            $sub_particular_val[$num] = null;
            $acc_id[$num] = $other_charges_acc_id;
            $ledger_name[$num] = $other_charges_ledger_name;
            $ledger_code[$num] = $other_charges_ledger_code;
            $voucher_id[$num] = $total_amount_voucher_id[$i];
            $ledger_type[$num] = 'Sub Entry';
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
            $voucher_id[$num] = $total_amount_voucher_id[$i];
            $ledger_type[$num] = 'Main Entry';
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
            $voucher_id[$num] = null;
            $ledger_type[$num] = null;
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
            $voucher_id[$num] = null;
            $ledger_type[$num] = null;
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
            $voucher_id[$num] = null;
            $ledger_type[$num] = null;
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
            $voucher_id[$num] = null;
            $ledger_type[$num] = null;
            $vat_cst_val[$num] = null;
            $vat_percen_val[$num] = null;
            $invoice_no_val[$num] = $invoice_no[$i];
            $total_val[$num] = $margindiff_amount;
            $invoice_val[$num] = $invoice_margindiff_amount[$i];
            $edited_val[$num] = $edited_margindiff_amount[$i];
            $difference_val[$num] = $diff_margindiff_amount[$i];
            $narration_val[$num] = $narration_margindiff_amount;
            $num = $num + 1;

            $particular[$num] = "Total Deduction";
            $sub_particular_val[$num] = null;
            $acc_id[$num] = $total_deduction_acc_id;
            $ledger_name[$num] = $total_deduction_ledger_name;
            $ledger_code[$num] = $total_deduction_ledger_code;
            $voucher_id[$num] = $total_deduction_voucher_id[$i];
            $ledger_type[$num] = 'Main Entry';
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
                'voucher_id'=>$voucher_id[$i],
                'ledger_type'=>$ledger_type[$i],
                'vat_cst'=>$vat_cst_val[$i],
                'vat_percen'=>$mycomponent->format_number($vat_percen_val[$i],2),
                'invoice_no'=>$invoice_no_val[$i],
                'total_val'=>$mycomponent->format_number($total_val[$i],2),
                'invoice_val'=>$mycomponent->format_number($invoice_val[$i],2),
                'edited_val'=>$mycomponent->format_number($edited_val[$i],2),
                'difference_val'=>$mycomponent->format_number($difference_val[$i],2),
                'narration'=>$narration_val[$i],
                'status'=>'approved',
                'is_active'=>'1',
                'updated_by'=>$session['session_id'],
                'updated_date'=>date('Y-m-d h:i:s'),
                'gi_date'=>$gi_date
            ];

            $j = $j + 1;

            $ledg_particular = $particular[$i];
            if($mycomponent->format_number($edited_val[$i],2)!=0){
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
                } else if($ledg_particular=="CGST"){
                    $ledg_type = "Debit";
                    // $type = "CGST";
                    // $legal_name = $particular[$i];
                    // $code = $sub_particular_val[$i];
                } else if($ledg_particular=="SGST"){
                    $ledg_type = "Debit";
                    // $type = "SGST";
                    // $legal_name = $particular[$i];
                    // $code = $sub_particular_val[$i];
                } else if($ledg_particular=="IGST"){
                    $ledg_type = "Debit";
                    // $type = "IGST";
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
                    
                    $result = $this->getSkuEntries($gi_id, $request, $invoice_no_val[$i], 'shortage', $voucher_id[$i], $narration_val[$i]);
                    $grnAccEntries = array_merge($grnAccEntries, $result['bulkInsertArray']);
                    // $ledgerArray = array_merge($ledgerArray, $result['ledgerArray']);
                    $bl_flag = true;
                    // echo json_encode($result['ledgerArray']);
                    // echo '<br/>';
                    // echo count($result['ledgerArray']);
                    // echo '<br/>';
                    // echo json_encode($ledgerArray);
                    // echo '<br/>';
                    // echo count($ledgerArray);
                    // echo '<br/>';

                    // echo $ledgerArray[3]['ref_id'];
                    // echo '<br/>';

                    for($l=0; $l<count($result['ledgerArray']); $l++){
                        for($m=0; $m<count($ledgerArray); $m++){
                            // echo count($ledgerArray);
                            // echo '<br/>';
                            // echo $m;

                            // echo $ledgerArray[$m]['ref_id'];
                            // echo '<br/>';
                            // echo $result['ledgerArray'][$l]['ref_id'];
                            // echo '<br/>';

                            if($ledgerArray[$m]['ref_id']==$result['ledgerArray'][$l]['ref_id'] && 
                                $ledgerArray[$m]['ref_type']==$result['ledgerArray'][$l]['ref_type'] && 
                                // $ledgerArray[$m]['entry_type']==$result['ledgerArray'][$l]['entry_type'] && 
                                $ledgerArray[$m]['invoice_no']==$result['ledgerArray'][$l]['invoice_no'] && 
                                $ledgerArray[$m]['vendor_id']==$result['ledgerArray'][$l]['vendor_id'] && 
                                $ledgerArray[$m]['acc_id']==$result['ledgerArray'][$l]['acc_id'] && 
                                $ledgerArray[$m]['voucher_id']==$result['ledgerArray'][$l]['voucher_id'] && 
                                $ledgerArray[$m]['ledger_type']==$result['ledgerArray'][$l]['ledger_type']){

                                    $bl_flag = false;
                                    $tot_amount = floatval($ledgerArray[$m]['amount']);
                                    $amount = floatval($result['ledgerArray'][$l]['amount']);
                                    if($ledgerArray[$m]['type']=="Debit"){
                                        $tot_amount = $tot_amount * -1;
                                    }
                                    if($result['ledgerArray'][$l]['type']=="Debit"){
                                        $amount = $amount * -1;
                                    }
                                    $tot_amount = $tot_amount + $amount;

                                    if($tot_amount<0){
                                        $ledgerArray[$m]['type'] = "Debit";
                                        $tot_amount = $tot_amount * -1;
                                    } else {
                                        $ledgerArray[$m]['type'] = "Credit";
                                    }

                                    $ledgerArray[$m]['amount'] = $tot_amount;
                            }
                        }

                        if($bl_flag == true){
                            // echo json_encode($result['ledgerArray'][$l]);
                            // echo '<br/>';
                            $temp_array[0]=$result['ledgerArray'][$l];
                            $ledgerArray = array_merge($ledgerArray, $temp_array);
                            // echo json_encode($ledgerArray);
                            // echo '<br/>';
                        }
                    }
                    

                    $result = $this->getSkuEntries($gi_id, $request, $invoice_no_val[$i], 'expiry', $voucher_id[$i], $narration_val[$i]);
                    $grnAccEntries = array_merge($grnAccEntries, $result['bulkInsertArray']);
                    // $ledgerArray = array_merge($ledgerArray, $result['ledgerArray']);
                    $bl_flag = true;
                    // echo json_encode($result['ledgerArray']);
                    // echo '<br/>';
                    // echo count($result['ledgerArray']);
                    // echo '<br/>';
                    // echo json_encode($ledgerArray);
                    // echo '<br/>';
                    // echo count($ledgerArray);
                    // echo '<br/>';

                    for($l=0; $l<count($result['ledgerArray']); $l++){
                        for($m=0; $m<count($ledgerArray); $m++){
                            if($ledgerArray[$m]['ref_id']==$result['ledgerArray'][$l]['ref_id'] && 
                                $ledgerArray[$m]['ref_type']==$result['ledgerArray'][$l]['ref_type'] && 
                                // $ledgerArray[$m]['entry_type']==$result['ledgerArray'][$l]['entry_type'] && 
                                $ledgerArray[$m]['invoice_no']==$result['ledgerArray'][$l]['invoice_no'] && 
                                $ledgerArray[$m]['vendor_id']==$result['ledgerArray'][$l]['vendor_id'] && 
                                $ledgerArray[$m]['acc_id']==$result['ledgerArray'][$l]['acc_id'] && 
                                $ledgerArray[$m]['voucher_id']==$result['ledgerArray'][$l]['voucher_id'] && 
                                $ledgerArray[$m]['ledger_type']==$result['ledgerArray'][$l]['ledger_type']){

                                    $bl_flag = false;
                                    $tot_amount = floatval($ledgerArray[$m]['amount']);
                                    $amount = floatval($result['ledgerArray'][$l]['amount']);
                                    if($ledgerArray[$m]['type']=="Debit"){
                                        $tot_amount = $tot_amount * -1;
                                    }
                                    if($result['ledgerArray'][$l]['type']=="Debit"){
                                        $amount = $amount * -1;
                                    }
                                    $tot_amount = $tot_amount + $amount;

                                    if($tot_amount<0){
                                        $ledgerArray[$m]['type'] = "Debit";
                                        $tot_amount = $tot_amount * -1;
                                    } else {
                                        $ledgerArray[$m]['type'] = "Credit";
                                    }

                                    $ledgerArray[$m]['amount'] = $tot_amount;
                            }
                        }

                        if($bl_flag == true){
                            // $ledgerArray = array_merge($ledgerArray, $result['ledgerArray'][$l]);
                            $temp_array[0]=$result['ledgerArray'][$l];
                            $ledgerArray = array_merge($ledgerArray, $temp_array);
                        }
                    }

                    $result = $this->getSkuEntries($gi_id, $request, $invoice_no_val[$i], 'damaged', $voucher_id[$i], $narration_val[$i]);
                    $grnAccEntries = array_merge($grnAccEntries, $result['bulkInsertArray']);
                    // $ledgerArray = array_merge($ledgerArray, $result['ledgerArray']);
                    $bl_flag = true;
                    for($l=0; $l<count($result['ledgerArray']); $l++){
                        for($m=0; $m<count($ledgerArray); $m++){
                            if($ledgerArray[$m]['ref_id']==$result['ledgerArray'][$l]['ref_id'] && 
                                $ledgerArray[$m]['ref_type']==$result['ledgerArray'][$l]['ref_type'] && 
                                // $ledgerArray[$m]['entry_type']==$result['ledgerArray'][$l]['entry_type'] && 
                                $ledgerArray[$m]['invoice_no']==$result['ledgerArray'][$l]['invoice_no'] && 
                                $ledgerArray[$m]['vendor_id']==$result['ledgerArray'][$l]['vendor_id'] && 
                                $ledgerArray[$m]['acc_id']==$result['ledgerArray'][$l]['acc_id'] && 
                                $ledgerArray[$m]['voucher_id']==$result['ledgerArray'][$l]['voucher_id'] && 
                                $ledgerArray[$m]['ledger_type']==$result['ledgerArray'][$l]['ledger_type']){

                                    $bl_flag = false;
                                    $tot_amount = floatval($ledgerArray[$m]['amount']);
                                    $amount = floatval($result['ledgerArray'][$l]['amount']);
                                    if($ledgerArray[$m]['type']=="Debit"){
                                        $tot_amount = $tot_amount * -1;
                                    }
                                    if($result['ledgerArray'][$l]['type']=="Debit"){
                                        $amount = $amount * -1;
                                    }
                                    $tot_amount = $tot_amount + $amount;

                                    if($tot_amount<0){
                                        $ledgerArray[$m]['type'] = "Debit";
                                        $tot_amount = $tot_amount * -1;
                                    } else {
                                        $ledgerArray[$m]['type'] = "Credit";
                                    }

                                    $ledgerArray[$m]['amount'] = $tot_amount;
                            }
                        }

                        if($bl_flag == true){
                            // $ledgerArray = array_merge($ledgerArray, $result['ledgerArray'][$l]);
                            $temp_array[0]=$result['ledgerArray'][$l];
                            $ledgerArray = array_merge($ledgerArray, $temp_array);
                        }
                    }

                    $result = $this->getSkuEntries($gi_id, $request, $invoice_no_val[$i], 'margindiff', $voucher_id[$i], $narration_val[$i]);
                    $grnAccEntries = array_merge($grnAccEntries, $result['bulkInsertArray']);
                    // $ledgerArray = array_merge($ledgerArray, $result['ledgerArray']);
                    $bl_flag = true;
                    for($l=0; $l<count($result['ledgerArray']); $l++){
                        for($m=0; $m<count($ledgerArray); $m++){
                            if($ledgerArray[$m]['ref_id']==$result['ledgerArray'][$l]['ref_id'] && 
                                $ledgerArray[$m]['ref_type']==$result['ledgerArray'][$l]['ref_type'] && 
                                // $ledgerArray[$m]['entry_type']==$result['ledgerArray'][$l]['entry_type'] && 
                                $ledgerArray[$m]['invoice_no']==$result['ledgerArray'][$l]['invoice_no'] && 
                                $ledgerArray[$m]['vendor_id']==$result['ledgerArray'][$l]['vendor_id'] && 
                                $ledgerArray[$m]['acc_id']==$result['ledgerArray'][$l]['acc_id'] && 
                                $ledgerArray[$m]['voucher_id']==$result['ledgerArray'][$l]['voucher_id'] && 
                                $ledgerArray[$m]['ledger_type']==$result['ledgerArray'][$l]['ledger_type']){

                                    $bl_flag = false;
                                    $tot_amount = floatval($ledgerArray[$m]['amount']);
                                    $amount = floatval($result['ledgerArray'][$l]['amount']);
                                    if($ledgerArray[$m]['type']=="Debit"){
                                        $tot_amount = $tot_amount * -1;
                                    }
                                    if($result['ledgerArray'][$l]['type']=="Debit"){
                                        $amount = $amount * -1;
                                    }
                                    $tot_amount = $tot_amount + $amount;

                                    if($tot_amount<0){
                                        $ledgerArray[$m]['type'] = "Debit";
                                        $tot_amount = $tot_amount * -1;
                                    } else {
                                        $ledgerArray[$m]['type'] = "Credit";
                                    }

                                    $ledgerArray[$m]['amount'] = $tot_amount;
                            }
                        }

                        if($bl_flag == true){
                            // $ledgerArray = array_merge($ledgerArray, $result['ledgerArray'][$l]);
                            $temp_array[0]=$result['ledgerArray'][$l];
                            $ledgerArray = array_merge($ledgerArray, $temp_array);
                        }
                    }

                    $k = count($ledgerArray);
                } else {
                    $ledg_type = "";
                    // $type = "";
                    // $legal_name = "";
                    // $code = "";
                }

                if($ledg_type!="" && $ledg_particular!="Tax"){
                    $bl_flag = true;
                    for($m=0; $m<count($ledgerArray); $m++){
                        if($ledgerArray[$m]['ref_id']==$gi_id && 
                            $ledgerArray[$m]['ref_type']=='purchase' && 
                            $ledgerArray[$m]['entry_type']==$particular[$i] && 
                            $ledgerArray[$m]['invoice_no']==$invoice_no_val[$i] && 
                            $ledgerArray[$m]['vendor_id']==$vendor_id && 
                            $ledgerArray[$m]['acc_id']==$acc_id[$i] && 
                            $ledgerArray[$m]['voucher_id']==$voucher_id[$i] && 
                            $ledgerArray[$m]['ledger_type']==$ledger_type[$i]){

                                $bl_flag = false;
                                $tot_amount = floatval($ledgerArray[$m]['amount']);
                                $amount = floatval($mycomponent->format_number($edited_val[$i],2));
                                if($ledgerArray[$m]['type']=="Debit"){
                                    $tot_amount = $tot_amount * -1;
                                }
                                if($ledg_type=="Debit"){
                                    $amount = $amount * -1;
                                }
                                $tot_amount = $tot_amount + $amount;

                                if($tot_amount<0){
                                    $ledgerArray[$m]['type'] = "Debit";
                                    $tot_amount = $tot_amount * -1;
                                } else {
                                    $ledgerArray[$m]['type'] = "Credit";
                                }

                                $ledgerArray[$m]['amount'] = $tot_amount;
                        }
                    }

                    if($bl_flag == true){
                        $ledgerArray[$k]=[
                                    'ref_id'=>$gi_id,
                                    'ref_type'=>'purchase',
                                    'entry_type'=>$particular[$i],
                                    'invoice_no'=>$invoice_no_val[$i],
                                    'vendor_id'=>$vendor_id,
                                    'acc_id'=>$acc_id[$i],
                                    'ledger_name'=>$ledger_name[$i],
                                    'ledger_code'=>$ledger_code[$i],
                                    'voucher_id'=>$voucher_id[$i],
                                    'ledger_type'=>$ledger_type[$i],
                                    'type'=>$ledg_type,
                                    'amount'=>$mycomponent->format_number($edited_val[$i],2),
                                    'narration'=>$narration_val[$i],
                                    'status'=>'approved',
                                    'is_active'=>'1',
                                    'updated_by'=>$session['session_id'],
                                    'updated_date'=>date('Y-m-d h:i:s'),
                                    'ref_date'=>$gi_date
                                ];

                        $k = $k + 1;
                    }
                    
                    // if($type == "Vendor Goods"){
                    //     $this->setAccCode($type, $legal_name, $code, $vendor_id);
                    // } else {
                    //     $this->setAccCode($type, $legal_name, $code);
                    // }
                }
            }
            
        }

        $data['bulkInsertArray'] = $bulkInsertArray;
        $data['grnAccEntries'] = $grnAccEntries;
        $data['ledgerArray'] = $ledgerArray;

        return $data;
    }

    public function getSkuEntries($gi_id, $request, $invoice_no_val, $ded_type, $voucher_id, $narration){
        // echo $ded_type;
        // echo '<br/>';

        $mycomponent = Yii::$app->mycomponent;
        
        $vendor_id = $request->post('vendor_id');
        $cost_acc_id = $request->post($ded_type.'_cost_acc_id');
        $cost_ledger_name = $request->post($ded_type.'_cost_ledger_name');
        $cost_ledger_code = $request->post($ded_type.'_cost_ledger_code');
        // $cost_voucher_id = $request->post($ded_type.'_cost_voucher_id');
        // $cost_ledger_type = $request->post($ded_type.'_cost_ledger_type');
        $tax_acc_id = $request->post($ded_type.'_tax_acc_id');
        $tax_ledger_name = $request->post($ded_type.'_tax_ledger_name');
        $tax_ledger_code = $request->post($ded_type.'_tax_ledger_code');
        // $tax_voucher_id = $request->post($ded_type.'_tax_voucher_id');
        // $tax_ledger_type = $request->post($ded_type.'_tax_ledger_type');
        $cgst_acc_id = $request->post($ded_type.'_cgst_acc_id');
        $cgst_ledger_name = $request->post($ded_type.'_cgst_ledger_name');
        $cgst_ledger_code = $request->post($ded_type.'_cgst_ledger_code');
        // $cgst_voucher_id = $request->post($ded_type.'_cgst_voucher_id');
        // $cgst_ledger_type = $request->post($ded_type.'_cgst_ledger_type');
        $sgst_acc_id = $request->post($ded_type.'_sgst_acc_id');
        $sgst_ledger_name = $request->post($ded_type.'_sgst_ledger_name');
        $sgst_ledger_code = $request->post($ded_type.'_sgst_ledger_code');
        // $sgst_voucher_id = $request->post($ded_type.'_sgst_voucher_id');
        // $sgst_ledger_type = $request->post($ded_type.'_sgst_ledger_type');
        $igst_acc_id = $request->post($ded_type.'_igst_acc_id');
        $igst_ledger_name = $request->post($ded_type.'_igst_ledger_name');
        $igst_ledger_code = $request->post($ded_type.'_igst_ledger_code');
        // $igst_voucher_id = $request->post($ded_type.'_igst_voucher_id');
        // $igst_ledger_type = $request->post($ded_type.'_igst_ledger_type');
        $invoice_no = $request->post($ded_type.'_invoice_no');
        $state = $request->post($ded_type.'_state');
        $vat_cst = $request->post($ded_type.'_vat_cst');
        $vat_percen = $request->post($ded_type.'_vat_percen');
        $cgst_rate = $request->post($ded_type.'_cgst_rate');
        $sgst_rate = $request->post($ded_type.'_sgst_rate');
        $igst_rate = $request->post($ded_type.'_igst_rate');
        $ean = $request->post($ded_type.'_ean');
        $hsn_code = $request->post($ded_type.'_hsn_code');
        $psku = $request->post($ded_type.'_psku');
        $product_title = $request->post($ded_type.'_product_title');
        $qty = $request->post($ded_type.'_qty');
        $box_price = $request->post($ded_type.'_box_price');
        $cost_excl_tax_per_unit = $request->post($ded_type.'_cost_excl_tax_per_unit');
        $tax_per_unit = $request->post($ded_type.'_tax_per_unit');
        $cgst_per_unit = $request->post($ded_type.'_cgst_per_unit');
        $sgst_per_unit = $request->post($ded_type.'_sgst_per_unit');
        $igst_per_unit = $request->post($ded_type.'_igst_per_unit');
        $total_per_unit = $request->post($ded_type.'_total_per_unit');
        $cost_excl_tax = $request->post($ded_type.'_cost_excl_tax');
        $tax = $request->post($ded_type.'_tax');
        $cgst = $request->post($ded_type.'_cgst');
        $sgst = $request->post($ded_type.'_sgst');
        $igst = $request->post($ded_type.'_igst');
        $total = $request->post($ded_type.'_total');
        $expiry_date = $request->post($ded_type.'_expiry_date');
        $earliest_expected_date = $request->post($ded_type.'_earliest_expected_date');
        $remarks = $request->post($ded_type.'_remarks');
        $gi_date = $request->post('gi_date');

        $po_cost_excl_tax = $request->post($ded_type.'_po_cost_excl_tax');
        $po_tax = $request->post($ded_type.'_po_tax');
        $po_cgst = $request->post($ded_type.'_po_cgst');
        $po_sgst = $request->post($ded_type.'_po_sgst');
        $po_igst = $request->post($ded_type.'_po_igst');
        $po_total = $request->post($ded_type.'_po_total');
        $diff_cost_excl_tax = $request->post($ded_type.'_diff_cost_excl_tax');
        $diff_tax = $request->post($ded_type.'_diff_tax');
        $diff_cgst = $request->post($ded_type.'_diff_cgst');
        $diff_sgst = $request->post($ded_type.'_diff_sgst');
        $diff_igst = $request->post($ded_type.'_diff_igst');
        $diff_total = $request->post($ded_type.'_diff_total');

        // echo json_encode($po_cost_excl_tax);
        // echo '<br/>';
        // echo json_encode($po_tax);
        // echo '<br/>';
        // echo json_encode($po_total);
        // echo '<br/>';
        // echo json_encode($diff_cost_excl_tax);
        // echo '<br/>';
        // echo json_encode($diff_tax);
        // echo '<br/>';
        // echo json_encode($diff_total);
        // echo '<br/>';

        if($gi_date==''){
            $gi_date=NULL;
        } else {
            $gi_date=$mycomponent->formatdate($gi_date);
        }

        $total_deduction_invoice_no = $request->post('invoice_no');
        $total_deduction_voucher_id = $request->post('total_deduction_voucher_id');
        $total_deduction_ledger_type = $request->post('total_deduction_ledger_type');

        $bulkInsertArray = array();
        $ledgerArray = array();
        $mycomponent = Yii::$app->mycomponent;
        $session = Yii::$app->session;
        $k=0;
        $ledg_type='Credit';
        if($ded_type=='shortage'){
            $ledg_particular = "Shortage Taxable Amount";
            $ledg_particular_tax = "Shortage Tax";
            $ledg_particular_cgst = "Shortage CGST";
            $ledg_particular_sgst = "Shortage SGST";
            $ledg_particular_igst = "Shortage IGST";
        } else if($ded_type=='expiry'){
            $ledg_particular = "Expiry Taxable Amount";
            $ledg_particular_tax = "Expiry Tax";
            $ledg_particular_cgst = "Expiry CGST";
            $ledg_particular_sgst = "Expiry SGST";
            $ledg_particular_igst = "Expiry IGST";
        } else if($ded_type=='damaged'){
            $ledg_particular = "Damaged Taxable Amount";
            $ledg_particular_tax = "Damaged Tax";
            $ledg_particular_cgst = "Damaged CGST";
            $ledg_particular_sgst = "Damaged SGST";
            $ledg_particular_igst = "Damaged IGST";
        } else if($ded_type=='margindiff'){
            $ledg_particular = "Margin Diff Taxable Amount";
            $ledg_particular_tax = "Margin Diff Tax";
            $ledg_particular_cgst = "Margin Diff CGST";
            $ledg_particular_sgst = "Margin Diff SGST";
            $ledg_particular_igst = "Margin Diff IGST";
        } else {
            $ledg_particular = "";
            $ledg_particular_tax = "";
            $ledg_particular_cgst = "";
            $ledg_particular_sgst = "";
            $ledg_particular_igst = "";
        }

        // echo json_encode($invoice_no);
        // echo '<br/>';

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
                            // 'cost_voucher_id'=>$voucher_id,
                            // 'cost_ledger_type'=>'Sub Entry',
                            'tax_acc_id'=>$tax_acc_id[$i],
                            'tax_ledger_name'=>$tax_ledger_name[$i],
                            'tax_ledger_code'=>$tax_ledger_code[$i],
                            // 'tax_voucher_id'=>$voucher_id,
                            // 'tax_ledger_type'=>'Sub Entry',
                            'cgst_acc_id'=>$cgst_acc_id[$i],
                            'cgst_ledger_name'=>$cgst_ledger_name[$i],
                            'cgst_ledger_code'=>$cgst_ledger_code[$i],
                            // 'cgst_voucher_id'=>$voucher_id,
                            // 'cgst_ledger_type'=>'Sub Entry',
                            'sgst_acc_id'=>$sgst_acc_id[$i],
                            'sgst_ledger_name'=>$sgst_ledger_name[$i],
                            'sgst_ledger_code'=>$sgst_ledger_code[$i],
                            // 'sgst_voucher_id'=>$voucher_id,
                            // 'sgst_ledger_type'=>'Sub Entry',
                            'igst_acc_id'=>$igst_acc_id[$i],
                            'igst_ledger_name'=>$igst_ledger_name[$i],
                            'igst_ledger_code'=>$igst_ledger_code[$i],
                            // 'igst_voucher_id'=>$voucher_id,
                            // 'igst_ledger_type'=>'Sub Entry',
                            'invoice_no'=>$invoice_no[$i],
                            'state'=>$state[$i],
                            'vat_cst'=>$vat_cst[$i],
                            'vat_percen'=>$vat_percen[$i],
                            'cgst_rate'=>$cgst_rate[$i],
                            'sgst_rate'=>$sgst_rate[$i],
                            'igst_rate'=>$igst_rate[$i],
                            'ean'=>$ean[$i],
                            'hsn_code'=>$hsn_code[$i],
                            'psku'=>$psku[$i],
                            'product_title'=>$product_title[$i],
                            'qty'=>$qty_val,
                            'box_price'=>$mycomponent->format_number($box_price[$i],2),
                            'cost_excl_vat_per_unit'=>$mycomponent->format_number($cost_excl_tax_per_unit[$i],2),
                            'tax_per_unit'=>$mycomponent->format_number($tax_per_unit[$i],2),
                            'cgst_per_unit'=>$mycomponent->format_number($cgst_per_unit[$i],2),
                            'sgst_per_unit'=>$mycomponent->format_number($sgst_per_unit[$i],2),
                            'igst_per_unit'=>$mycomponent->format_number($igst_per_unit[$i],2),
                            'total_per_unit'=>$mycomponent->format_number($total_per_unit[$i],2),
                            'cost_excl_vat'=>$mycomponent->format_number($cost_excl_tax[$i],2),
                            'tax'=>$mycomponent->format_number($tax[$i],2),
                            'cgst'=>$mycomponent->format_number($cgst[$i],2),
                            'sgst'=>$mycomponent->format_number($sgst[$i],2),
                            'igst'=>$mycomponent->format_number($igst[$i],2),
                            'total'=>$mycomponent->format_number($total[$i],2),
                            'expiry_date'=>$expiry_date[$i],
                            'earliest_expected_date'=>$earliest_expected_date[$i],
                            'status'=>'approved',
                            'is_active'=>'1',
                            'remarks'=>$remarks[$i],
                            'po_cost_excl_vat'=>$mycomponent->format_number($po_cost_excl_tax[$i],2),
                            'po_tax'=>$mycomponent->format_number($po_tax[$i],2),
                            'po_cgst'=>$mycomponent->format_number($po_cgst[$i],2),
                            'po_sgst'=>$mycomponent->format_number($po_sgst[$i],2),
                            'po_igst'=>$mycomponent->format_number($po_igst[$i],2),
                            'po_total'=>$mycomponent->format_number($po_total[$i],2)
                        ];

                if($ded_type=='margindiff'){
                    $cost_excl_tax_amt = $mycomponent->format_number($diff_cost_excl_tax[$i],2);
                    $tax_amt = $mycomponent->format_number($diff_tax[$i],2);
                    $cgst_amt = $mycomponent->format_number($diff_cgst[$i],2);
                    $sgst_amt = $mycomponent->format_number($diff_sgst[$i],2);
                    $igst_amt = $mycomponent->format_number($diff_igst[$i],2);
                } else {
                    $cost_excl_tax_amt = $mycomponent->format_number($cost_excl_tax[$i],2);
                    $tax_amt = $mycomponent->format_number($tax[$i],2);
                    $cgst_amt = $mycomponent->format_number($cgst[$i],2);
                    $sgst_amt = $mycomponent->format_number($sgst[$i],2);
                    $igst_amt = $mycomponent->format_number($igst[$i],2);
                }

                if($cost_excl_tax_amt!=0){
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
                                    'voucher_id'=>$voucher_id,
                                    'ledger_type'=>'Sub Entry',
                                    'type'=>$ledg_type,
                                    'amount'=>$cost_excl_tax_amt,
                                    'narration'=>$narration,
                                    'status'=>'approved',
                                    'is_active'=>'1',
                                    'updated_by'=>$session['session_id'],
                                    'updated_date'=>date('Y-m-d h:i:s'),
                                    'ref_date'=>$gi_date
                                ];
                    // $ledgerArray[$k]=[
                    //             'grn_id'=>$gi_id,
                    //             'vendor_id'=>$vendor_id,
                    //             'code'=>$code,
                    //             'invoice_no'=>$invoice_no[$i],
                    //             'particular'=>$ledg_particular,
                    //             'type'=>$ledg_type,
                    //             'amount'=>$mycomponent->format_number($cost_excl_tax[$i],2),
                    //             'status'=>'approved',
                    //             'is_active'=>'1'
                    //         ];
                    $k = $k + 1;
                    // $type = "Goods Purchase";
                    // $legal_name = $ledg_particular;
                    // $code = $code;
                    // $this->setAccCode($type, $legal_name, $code);
                }
                

                // if($tax_amt!=0){
                //     // $code = 'Tax_'.$state[$i].'_'.$vat_cst[$i].'_'.$vat_percen[$i];
                //     $ledgerArray[$k]=[
                //                     'ref_id'=>$gi_id,
                //                     'ref_type'=>'purchase',
                //                     'entry_type'=>$ded_type.'_tax',
                //                     'invoice_no'=>$invoice_no[$i],
                //                     'vendor_id'=>$vendor_id,
                //                     'acc_id'=>$tax_acc_id[$i],
                //                     'ledger_name'=>$tax_ledger_name[$i],
                //                     'ledger_code'=>$tax_ledger_code[$i],
                //                     'voucher_id'=>$voucher_id,
                //                     'ledger_type'=>'Sub Entry',
                //                     'type'=>$ledg_type,
                //                     'amount'=>$tax_amt,
                //                     'narration'=>$narration,
                //                     'status'=>'approved',
                //                     'is_active'=>'1',
                //                     'updated_by'=>$session['session_id'],
                //                     'updated_date'=>date('Y-m-d h:i:s'),
                //                     'ref_date'=>$gi_date
                //                 ];
                //     // $ledgerArray[$k]=[
                //     //             'grn_id'=>$gi_id,
                //     //             'vendor_id'=>$vendor_id,
                //     //             'code'=>$code,
                //     //             'invoice_no'=>$invoice_no[$i],
                //     //             'particular'=>$ledg_particular_tax,
                //     //             'type'=>$ledg_type,
                //     //             'amount'=>$mycomponent->format_number($tax[$i],2),
                //     //             'status'=>'approved',
                //     //             'is_active'=>'1'
                //     //         ];
                //     $k = $k + 1;
                //     // $type = "Tax";
                //     // $legal_name = $ledg_particular_tax;
                //     // $code = $code;
                //     // $this->setAccCode($type, $legal_name, $code);
                // }


                if($cgst_amt!=0){
                    // $code = 'Tax_'.$state[$i].'_'.$vat_cst[$i].'_'.$vat_percen[$i];
                    $ledgerArray[$k]=[
                                    'ref_id'=>$gi_id,
                                    'ref_type'=>'purchase',
                                    'entry_type'=>$ded_type.'_cgst',
                                    'invoice_no'=>$invoice_no[$i],
                                    'vendor_id'=>$vendor_id,
                                    'acc_id'=>$cgst_acc_id[$i],
                                    'ledger_name'=>$cgst_ledger_name[$i],
                                    'ledger_code'=>$cgst_ledger_code[$i],
                                    'voucher_id'=>$voucher_id,
                                    'ledger_type'=>'Sub Entry',
                                    'type'=>$ledg_type,
                                    'amount'=>$cgst_amt,
                                    'narration'=>$narration,
                                    'status'=>'approved',
                                    'is_active'=>'1',
                                    'updated_by'=>$session['session_id'],
                                    'updated_date'=>date('Y-m-d h:i:s'),
                                    'ref_date'=>$gi_date
                                ];
                    // $ledgerArray[$k]=[
                    //             'grn_id'=>$gi_id,
                    //             'vendor_id'=>$vendor_id,
                    //             'code'=>$code,
                    //             'invoice_no'=>$invoice_no[$i],
                    //             'particular'=>$ledg_particular_cgst,
                    //             'type'=>$ledg_type,
                    //             'amount'=>$mycomponent->format_number($cgst[$i],2),
                    //             'status'=>'approved',
                    //             'is_active'=>'1'
                    //         ];
                    $k = $k + 1;
                    // $type = "Tax";
                    // $legal_name = $ledg_particular_cgst;
                    // $code = $code;
                    // $this->setAccCode($type, $legal_name, $code);
                }


                if($sgst_amt!=0){
                    // $code = 'Tax_'.$state[$i].'_'.$vat_cst[$i].'_'.$vat_percen[$i];
                    $ledgerArray[$k]=[
                                    'ref_id'=>$gi_id,
                                    'ref_type'=>'purchase',
                                    'entry_type'=>$ded_type.'_sgst',
                                    'invoice_no'=>$invoice_no[$i],
                                    'vendor_id'=>$vendor_id,
                                    'acc_id'=>$sgst_acc_id[$i],
                                    'ledger_name'=>$sgst_ledger_name[$i],
                                    'ledger_code'=>$sgst_ledger_code[$i],
                                    'voucher_id'=>$voucher_id,
                                    'ledger_type'=>'Sub Entry',
                                    'type'=>$ledg_type,
                                    'amount'=>$sgst_amt,
                                    'narration'=>$narration,
                                    'status'=>'approved',
                                    'is_active'=>'1',
                                    'updated_by'=>$session['session_id'],
                                    'updated_date'=>date('Y-m-d h:i:s'),
                                    'ref_date'=>$gi_date
                                ];
                    // $ledgerArray[$k]=[
                    //             'grn_id'=>$gi_id,
                    //             'vendor_id'=>$vendor_id,
                    //             'code'=>$code,
                    //             'invoice_no'=>$invoice_no[$i],
                    //             'particular'=>$ledg_particular_sgst,
                    //             'type'=>$ledg_type,
                    //             'amount'=>$mycomponent->format_number($sgst[$i],2),
                    //             'status'=>'approved',
                    //             'is_active'=>'1'
                    //         ];
                    $k = $k + 1;
                    // $type = "Tax";
                    // $legal_name = $ledg_particular_sgst;
                    // $code = $code;
                    // $this->setAccCode($type, $legal_name, $code);
                }

                
                if($igst_amt!=0){
                    // $code = 'Tax_'.$state[$i].'_'.$vat_cst[$i].'_'.$vat_percen[$i];
                    $ledgerArray[$k]=[
                                    'ref_id'=>$gi_id,
                                    'ref_type'=>'purchase',
                                    'entry_type'=>$ded_type.'_igst',
                                    'invoice_no'=>$invoice_no[$i],
                                    'vendor_id'=>$vendor_id,
                                    'acc_id'=>$igst_acc_id[$i],
                                    'ledger_name'=>$igst_ledger_name[$i],
                                    'ledger_code'=>$igst_ledger_code[$i],
                                    'voucher_id'=>$voucher_id,
                                    'ledger_type'=>'Sub Entry',
                                    'type'=>$ledg_type,
                                    'amount'=>$igst_amt,
                                    'narration'=>$narration,
                                    'status'=>'approved',
                                    'is_active'=>'1',
                                    'updated_by'=>$session['session_id'],
                                    'updated_date'=>date('Y-m-d h:i:s'),
                                    'ref_date'=>$gi_date
                                ];
                    // $ledgerArray[$k]=[
                    //             'grn_id'=>$gi_id,
                    //             'vendor_id'=>$vendor_id,
                    //             'code'=>$code,
                    //             'invoice_no'=>$invoice_no[$i],
                    //             'particular'=>$ledg_particular_igst,
                    //             'type'=>$ledg_type,
                    //             'amount'=>$mycomponent->format_number($igst[$i],2),
                    //             'status'=>'approved',
                    //             'is_active'=>'1'
                    //         ];
                    $k = $k + 1;
                    // $type = "Tax";
                    // $legal_name = $ledg_particular_igst;
                    // $code = $code;
                    // $this->setAccCode($type, $legal_name, $code);
                }
            }
        }

        $result['bulkInsertArray'] = $bulkInsertArray;
        $result['ledgerArray'] = $ledgerArray;

        // echo json_encode($result);
        // echo '<br/>';

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
                                'status' => 'approved',
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

        // $psku = 'HPCA11346';
        // $grn_id = '6293';

        // $sql = "select A.tax_zone_code, A.tax_zone_name, B.* from 
        //         (select AA.*, CC.tax_zone_code, CC.tax_zone_name from grn AA 
        //         left join internal_warehouse_master BB on (AA.warehouse_id = BB.warehouse_code and AA.company_id = BB.company_id) 
        //         left join tax_zone_master CC on (BB.tax_zone_id = CC.id) 
        //         where AA.grn_id = '$grn_id' and AA.is_active = '1' and BB.is_active = '1' and CC.is_active = '1' limit 1) A 
        //         left join 
        //         (select * from grn_entries where grn_id = '$grn_id' and psku = '$psku' and is_active = '1') B 
        //         on(A.grn_id = B.grn_id)";

        $sql = "select AA.*, BB.tax_rate, BB.cgst_rate, BB.sgst_rate, BB.igst_rate from 
                (select A.vat_cst as tx_zn_cd, B.*, D.tax_zone_code, D.tax_zone_name, E.invoice_date, A.gi_date, 
                    date_add(A.gi_date, interval ifnull(min_no_of_months_shelf_life_required,0) month) as earliest_expected_date, 
                    null as cost_acc_id, null as cost_ledger_name, null as cost_ledger_code, 
                    null as tax_acc_id, null as tax_ledger_name, null as tax_ledger_code, 
                    null as cgst_acc_id, null as cgst_ledger_name, null as cgst_ledger_code, 
                    null as sgst_acc_id, null as sgst_ledger_name, null as sgst_ledger_code, 
                    null as igst_acc_id, null as igst_ledger_name, null as igst_ledger_code, 
                    F.purchase_order_id, G.quantity as po_quantity, G.cost_price_exc_tax as po_unit_rate_excl_tax, 
                    G.unit_tax_amount as po_unit_tax, G.cost_price_inc_tax as po_unit_rate_incl_tax, 
                    ifnull((B.invoice_qty*B.cost_incl_vat_cst),0)-ifnull((B.invoice_qty*G.cost_price_inc_tax),0) as margindiff_amount 
                from grn A 
                left join grn_entries B on (A.grn_id = B.grn_id) 
                left join tax_zone_master D on (A.vat_cst = D.tax_zone_code) 
                left join goods_inward_outward_invoices E on (A.gi_id = E.gi_go_ref_no and B.invoice_no = E.invoice_no) 
                left join purchase_order F on (A.po_no = F.po_no) 
                left join purchase_order_items G on (F.purchase_order_id = G.purchase_order_id and B.psku = G.psku) 
                where A.grn_id = '$grn_id' and B.grn_id = '$grn_id' and B.psku = '$psku' and 
                        A.is_active = '1' and B.is_active = '1' and D.is_active = '1') AA 
                
                left join 

                (select id, tax_zone_code, tax_zone_name, parent_id, tax_rate, 
                    sum(case when child_tax_type_code = 'CGST' then child_tax_rate else 0 end) as cgst_rate, 
                    sum(case when child_tax_type_code = 'SGST' then child_tax_rate else 0 end) as sgst_rate, 
                    sum(case when child_tax_type_code = 'IGST' then child_tax_rate else 0 end) as igst_rate from 
                (select A.id, A.tax_zone_code, A.tax_zone_name, B.id as parent_id, B.tax_type_id as parent_tax_type_id, 
                    B.tax_rate, C.child_id, D.tax_type_id as child_tax_type_id, D.tax_rate as child_tax_rate, 
                    E.tax_category as child_tax_category, E.tax_type_code as child_tax_type_code, 
                    E.tax_type_name as child_tax_type_name 
                from tax_zone_master A 
                left join tax_rate_master B on (A.id = B.tax_zone_id) 
                left join tax_component C on (B.id = C.parent_id) 
                left join tax_rate_master D on (C.child_id = D.id) 
                left join tax_type_master E on (D.tax_type_id = E.id)) C 
                where child_tax_rate != 0
                group by id, tax_zone_code, tax_zone_name, parent_id, tax_rate) BB 
                on (AA.tax_zone_code = BB.tax_zone_code and round(AA.vat_percen,4)=round(BB.tax_rate,4))";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getDebitNoteDetails($invoice_id){
        $sql = "select A.invoice_no, A.invoice_date, B.grn_id, B.vendor_id, C.edited_val as total_deduction, C.gi_date 
                from goods_inward_outward_invoices A 
                left join grn B on (A.gi_go_ref_no = B.gi_id) left join acc_grn_entries C 
                on (B.grn_id = C.grn_id and A.invoice_no = C.invoice_no) 
                where A.gi_go_invoice_id = '$invoice_id' and B.status = 'approved' and B.is_active = '1' and 
                C.status = 'approved' and C.is_active = '1' and C.particular = 'Total Deduction'";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        $invoice_details = $reader->readAll();

        if(count($invoice_details)>0) {
            $invoice_no = $invoice_details[0]['invoice_no'];
            $invoice_date = $invoice_details[0]['invoice_date'];
            $grn_id = $invoice_details[0]['grn_id'];
            $vendor_id = $invoice_details[0]['vendor_id'];
            $total_deduction = $invoice_details[0]['total_deduction'];
            $total_qty = 0;
            $ded_type = '';

            $sql = "select * from acc_grn_sku_entries where status = 'approved' and is_active = '1' and 
                    grn_id = '$grn_id' and invoice_no = '$invoice_no' order by ded_type";
            $command = Yii::$app->db->createCommand($sql);
            $reader = $command->query();
            $deduction_details = $reader->readAll();

            $sql = "select C.*, D.contact_name, D.contact_email, D.contact_phone, D.contact_mobile, D.contact_fax from 
                    (select A.*, B.* from 
                    (select AA.*, BB.legal_entity_name from vendor_master AA left join legal_entity_type_master BB 
                        on (AA.legal_entity_type_id = BB.id) where AA.id = '$vendor_id' and BB.is_active = '1') A 
                    left join 
                    (select AA.vendor_id, AA.office_address_line_1, AA.office_address_line_2, AA.office_address_line_3, 
                            AA.pincode, BB.city_code, BB.city_name, CC.state_code, CC.state_name, 
                            DD.country_code, DD.country_name from 
                    vendor_office_address AA left join city_master BB on (AA.city_id = BB.id) left join 
                    state_master CC on (AA.state_id = CC.id) left join country_master DD on (AA.country_id = DD.id) 
                    where AA.vendor_id = '$vendor_id' and AA.is_active = '1' and BB.is_active = '1' 
                            and CC.is_active = '1' and DD.is_active = '1') B 
                    on (A.id = B.vendor_id)) C 
                    left join 
                    (select * from vendor_contacts where vendor_id = '$vendor_id' and is_active = '1' and 
                        (is_purchase_related = 'yes' or is_accounts_related = 'yes') limit 1) D 
                    on (C.vendor_id = D.vendor_id)";
            $command = Yii::$app->db->createCommand($sql);
            $reader = $command->query();
            $vendor_details = $reader->readAll();

            $sql = "select sum(A.qty) as total_qty, sum(A.total) as total_deduction, 
                    group_concat(distinct(CONCAT(UCASE(SUBSTRING(A.ded_type, 1, 1)),LOWER(SUBSTRING(A.ded_type, 2))))) as ded_type from 
                    (select qty, case when ded_type='margindiff' then round(total-po_total,2) else total end as total, 
                        case when ded_type='margindiff' then 'margin difference' else ded_type end as ded_type from 
                        acc_grn_sku_entries where status = 'approved' and is_active = '1' and 
                        grn_id = '$grn_id' and invoice_no = '$invoice_no' order by ded_type) A order by A.ded_type";
            $command = Yii::$app->db->createCommand($sql);
            $reader = $command->query();
            $deductions = $reader->readAll();
            if(count($deductions)>0){
                $total_qty = $deductions[0]['total_qty'];
                $total_deduction = $deductions[0]['total_deduction'];
                $ded_type = $deductions[0]['ded_type'];
            }

            $session = Yii::$app->session;

            $array=[
                'grn_id'=>$grn_id,
                'vendor_id'=>$vendor_id,
                'invoice_id'=>$invoice_id,
                'invoice_no'=>$invoice_no,
                'invoice_date'=>$invoice_date,
                'ded_type'=>$ded_type,
                'total_qty'=>$total_qty,
                'total_deduction'=>$total_deduction,
                'status'=>'approved',
                'is_active'=>'1',
                'updated_by'=>$session['session_id'],
                'updated_date'=>date('Y-m-d h:i:s')
            ];

            $tableName = "acc_grn_debit_notes";

            $sql = "select * from acc_grn_debit_notes where status = 'approved' and is_active = '1' and 
                    grn_id = '$grn_id' and invoice_no = '$invoice_no'";
            $command = Yii::$app->db->createCommand($sql);
            $reader = $command->query();
            $debit_note = $reader->readAll();
            if(count($debit_note)==0){
                $count = Yii::$app->db->createCommand()
                            ->insert($tableName, $array)
                            ->execute();
                $ref_id = Yii::$app->db->getLastInsertID();
            } else {
                $ref_id = $debit_note[0]['id'];
                $count = Yii::$app->db->createCommand()
                            ->update($tableName, $array, "id = '".$ref_id."'")
                            ->execute();
            }

            $mycomponent = Yii::$app->mycomponent;
            $financial_year = $mycomponent->get_financial_year($invoice_date);
            $debit_note_ref = $financial_year . "/" . $ref_id;

            $sql = "update acc_grn_debit_notes set debit_note_ref = '$debit_note_ref' where id = '$ref_id'";
            $command = Yii::$app->db->createCommand($sql);
            $count = $command->execute();

            $sql = "select * from acc_grn_debit_notes where id = '$ref_id'";
            $command = Yii::$app->db->createCommand($sql);
            $reader = $command->query();
            $debit_note = $reader->readAll();

            $sql = "select A.*, B.warehouse_name, B.gst_id from grn A left join internal_warehouse_master B 
                    on (A.warehouse_id = B.warehouse_code and A.company_id = B.company_id) 
                    where A.grn_id = '$grn_id'";
            $command = Yii::$app->db->createCommand($sql);
            $reader = $command->query();
            $grn_details = $reader->readAll();
            
            $mpdf=new mPDF();
            $mpdf->WriteHTML(Yii::$app->controller->renderPartial('debit_note', [
                'invoice_details' => $invoice_details, 'debit_note' => $debit_note, 
                'deduction_details' => $deduction_details, 'vendor_details' => $vendor_details, 
                'grn_details' => $grn_details
            ]));

            $upload_path = './uploads';
            if(!is_dir($upload_path)) {
                mkdir($upload_path, 0777, TRUE);
            }
            $upload_path = './uploads/debit_notes';
            if(!is_dir($upload_path)) {
                mkdir($upload_path, 0777, TRUE);
            }
            $upload_path = './uploads/debit_notes/'.$grn_id;
            if(!is_dir($upload_path)) {
                mkdir($upload_path, 0777, TRUE);
            }

            $file_name = $upload_path . '/debit_note_invoice_' . $invoice_id . '.pdf';
            $file_path = 'uploads/debit_notes/' . $grn_id . '/debit_note_invoice_' . $invoice_id . '.pdf';

            // $mpdf->Output('MyPDF.pdf', 'D');
            $mpdf->Output($file_name, 'F');
            // exit;

            $sql = "update acc_grn_debit_notes set debit_note_path = '$file_path' where id = '$ref_id'";
            $command = Yii::$app->db->createCommand($sql);
            $count = $command->execute();

            $sql = "select * from acc_grn_debit_notes where status = 'approved' and is_active = '1' and 
                    grn_id = '$grn_id' and invoice_no = '$invoice_no'";
            $command = Yii::$app->db->createCommand($sql);
            $reader = $command->query();
            $debit_note = $reader->readAll();

            
        } else {
            $debit_note = array();
            $deduction_details = array();
            $vendor_details = array();
            $grn_details = array();
        }

        $data['invoice_details'] = $invoice_details;
        $data['debit_note'] = $debit_note;
        $data['deduction_details'] = $deduction_details;
        $data['vendor_details'] = $vendor_details;
        $data['grn_details'] = $grn_details;
        
        return $data;
    }
    
    public function setLog($module_name, $sub_module, $action, $vendor_id, $description, $table_name, $table_id) {
        $session = Yii::$app->session;
        $curusr = $session['session_id'];
        $now = date('Y-m-d H:i:s');

        $array = array('module_name' => $module_name, 
                        'sub_module' => $sub_module, 
                        'action' => $action, 
                        'vendor_id' => $vendor_id, 
                        'user_id' => $curusr, 
                        'description' => $description, 
                        'log_activity_date' => $now, 
                        'table_name' => $table_name, 
                        'table_id' => $table_id);
        $count = Yii::$app->db->createCommand()
                            ->insert("acc_user_log", $array)
                            ->execute();

        return true;
    }
}