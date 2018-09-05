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

    public function getAccountDetails($id="", $status="", $tax_code=""){
        $cond = "";
        $cond2 = "";
        if($id!=""){
            $cond = " and id = '$id'";
            $cond2 = " and acc_id = '$id'";
        }

        if($status!=""){
            $cond = $cond . " and status = '$status'";
        }

        if($tax_code!=""){
            $cond = $cond . " and legal_name like '%".$tax_code."%'";
        }

        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select A.*, concat_ws(',', A.category_1, A.category_2, A.category_3) as acc_category, B.bus_category from 
                (select * from acc_master where is_active = '1' and company_id = '$company_id' " . $cond . ") A 
                left join 
                (select acc_id, GROUP_CONCAT(category_name) as bus_category from acc_categories where is_active = '1'" . $cond2 . " 
                    group by acc_id) B 
                on (A.id = B.acc_id) order by legal_name";

        // echo $sql;
        // echo '<br/>';
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getState($warehouse_code){
        $sql = "select A.warehouse_code, B.state_name from 
                (select * from internal_warehouse_master where warehouse_code like '%".$warehouse_code."%') A 
                left join 
                (select * from state_master) B 
                on (A.state_id=B.id)";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getNewGrnDetails(){
        $request = Yii::$app->request;
        $mycomponent = Yii::$app->mycomponent;
        $session = Yii::$app->session;
        $start = $request->post('start');
        $length = $request->post('length');
        $len= "";
        if($request->post('start')!="" && $request->post('length')!='-1')
        {
            $mycomponent = Yii::$app->mycomponent;   
            $start = $request->post('start');
            $length = $request->post('length'); 
            $len = " LIMIT ".$start.", ".$length;  
        }
        $wheresearch = '';
        if($request->post('search')!=null)
        {
           $search_value1 =  $request->post('search');
           $search = $search_value1['value'];
            if($search!="") {
                    $wheresearch = " and (C.grn_id like '%$search%' or C.gi_id like '%$search%' or C.location like '%$search%' or 
                                    C.vendor_name like '%$search%' or C.invoice_val_after_tax like '%$search%' or 
                                    C.gi_date like '%$search%' or C.status like '%$search%' or C.user_name  like '%$search%' or 
                                    C.approver_name like '%$search%') ";
            } 
        }

        $company_id = $session['company_id'];

        $sql = "select * from 
                (select A.*, B.grn_id as b_grn_id from 
                (select A.*, B.username from grn A left join user B on(A.updated_by = B.id) 
                    where A.is_active = '1' and A.status = 'approved' and date(A.gi_date) > date('2017-07-01') and A.company_id='$company_id') A 
                left join 
                (select distinct grn_id from acc_grn_entries) B 
                on (A.grn_id = B.grn_id)) C 
                where C.b_grn_id is null ".$wheresearch." order by UNIX_TIMESTAMP(C.updated_date) desc ".$len;
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getCountGrnDetails(){
        $request = Yii::$app->request;
        $mycomponent = Yii::$app->mycomponent;
        $session = Yii::$app->session;
        $start = $request->post('start');
        $length = $request->post('length');

        $wheresearch = '';
        if($request->post('search')!=null)
        {
           $search_value1 =  $request->post('search');
           $search = $search_value1['value'];
            if($search!="") {
                    $wheresearch = " and (C.grn_id like '%$search%' or C.gi_id like '%$search%' or C.location like '%$search%' or 
                                    C.vendor_name like '%$search%' or C.invoice_val_after_tax like '%$search%' or 
                                    C.gi_date like '%$search%' or C.status like '%$search%' or C.user_name  like '%$search%' or 
                                    C.approver_name like '%$search%')";
            } 
        }

        $company_id = $session['company_id'];

        $sql = "select count(*) as count  from 
                (select A.*, B.grn_id as b_grn_id from 
                (select A.*, B.username from grn A left join user B on(A.updated_by = B.id) 
                    where A.is_active = '1' and A.status = 'approved' and date(A.gi_date) > date('2017-07-01') and A.company_id='$company_id') A 
                left join 
                (select distinct grn_id from acc_grn_entries) B 
                on (A.grn_id = B.grn_id)) C 
                where C.b_grn_id is null ".$wheresearch." order by UNIX_TIMESTAMP(C.updated_date) desc";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        $result =  $reader->readAll();
        return $result[0]['count'];
    }

    public function getAllGrnDetails(){
        $request = Yii::$app->request;
        $len= "";
        if($request->post('start')!="" && $request->post('length')!='-1')
        {
            $mycomponent = Yii::$app->mycomponent;   
            $start = $request->post('start');
            $length = $request->post('length'); 
            $len = "LIMIT ".$start.", ".$length;  
        } 

        $wheresearch = '';
        if($request->post('search')!=null)
        {
           $search_value1 =  $request->post('search');
           $search = $search_value1['value'];
            if($search!="") {
                $wheresearch = " where (C.grn_id like '%$search%' or C.gi_id like '%$search%' or C.location like '%$search%' or 
                                C.vendor_name like '%$search%' or C.invoice_val_after_tax like '%$search%' or 
                                C.gi_date like '%$search%' or C.status like '%$search%' or C.user_name  like '%$search%' or 
                                C.approver_name like '%$search%') ";
            } 
        }

        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        // $sql = "select * from 
        //         (select A.*, B.status as grn_status from 
        //         (select distinct A.*, B.username, C.is_paid from grn A left join user B on(A.updated_by = B.id) 
        //             left join acc_ledger_entries C on (A.grn_id = C.ref_id and 'purchase' = C.ref_type and 
        //                 '1' = C.is_active and 'Approved' = C.status and '1' = C.is_paid) 
        //             where A.is_active = '1' and A.status = 'approved' and date(A.gi_date) > date('2017-07-01') and A.company_id='$company_id') A 
        //         left join 
        //         (select distinct grn_id, status from acc_grn_entries) B 
        //         on (A.grn_id = B.grn_id)) c ".$wheresearch." order by UNIX_TIMESTAMP(updated_date) desc ".$len;

        $sql = "select C.*, D.is_paid from 
                (select A.*, B.status as grn_status from 
                (select distinct A.*, B.username from grn A left join user B on(A.updated_by = B.id) 
                where A.is_active = '1' and A.status = 'approved' and date(A.gi_date) > date('2017-07-01') and A.company_id='$company_id') A 
                left join 
                (select distinct grn_id, status from acc_grn_entries) B 
                on (A.grn_id = B.grn_id)) C 
                left join 
                (select distinct ref_id, is_paid from acc_ledger_entries 
                    where ref_type = 'purchase' and is_active = '1' and status = 'Approved' and is_paid = '1') D 
                on (C.grn_id = D.ref_id) ".$wheresearch." 
                order by UNIX_TIMESTAMP(C.updated_date) desc ".$len;

        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getCountAllGrnDetails(){
        $request = Yii::$app->request;
        $wheresearch = '';
        if($request->post('search')!=null)
        {
           $search_value1 =  $request->post('search');
           $search = $search_value1['value'];
            if($search!="") {
                    $wheresearch = " where (C.grn_id like '%$search%' or C.gi_id like '%$search%' or C.location like '%$search%' or 
                                    C.vendor_name like '%$search%' or C.invoice_val_after_tax like '%$search%' or 
                                    C.gi_date like '%$search%' or C.status like '%$search%' or C.user_name  like '%$search%' or 
                                    C.approver_name like '%$search%') ";
            } 
        }

        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        // $sql = "select count(*) as count  from 
        //     (select A.*, B.status as grn_status from 
        //     (select distinct A.*, B.username, C.is_paid from grn A left join user B on(A.updated_by = B.id) 
        //         left join acc_ledger_entries C on (A.grn_id = C.ref_id and 'purchase' = C.ref_type and 
        //             '1' = C.is_active and 'Approved' = C.status and '1' = C.is_paid) 
        //         where A.is_active = '1' and A.status = 'approved' and date(A.gi_date) > date('2017-07-01') and A.company_id='$company_id') A 
        //     left join 
        //     (select distinct grn_id, status from acc_grn_entries) B 
        //     on (A.grn_id = B.grn_id)) c ".$wheresearch." order by UNIX_TIMESTAMP(updated_date) desc ";

        $sql = "select count(*) as count  from 
                (select A.*, B.status as grn_status from 
                (select distinct A.*, B.username from grn A left join user B on(A.updated_by = B.id) 
                where A.is_active = '1' and A.status = 'approved' and date(A.gi_date) > date('2017-07-01') and A.company_id='$company_id') A 
                left join 
                (select distinct grn_id, status from acc_grn_entries) B 
                on (A.grn_id = B.grn_id)) C  ".$wheresearch;
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        $result =  $reader->readAll();
        return $result[0]['count'];
    }

    public function getGrnDetails($id){
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select A.*, B.vendor_code, C.gi_date as grn_date from grn A left join vendor_master B on (A.vendor_id = B.id) 
                left join acc_grn_entries C on (A.grn_id = C.grn_id and C.status = 'approved' and C.is_active='1' and 
                C.particular = 'Total Amount') where A.grn_id = '$id' and A.status = 'approved' and A.is_active='1' and A.company_id='$company_id'";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getPurchaseDetails($status=""){
        $cond = "";
        $len='';
        if($status!=""){
            $cond = " and A.status = '$status'";
        }
        $request = Yii::$app->request;
        if($request->post('start')!="" && $request->post('length')!='-1')
        {
            $mycomponent = Yii::$app->mycomponent;   
            $start = $request->post('start');
            $length = $request->post('length'); 
            $len = "LIMIT ".$start.", ".$length;  
        }

        $wheresearch = '';
        if($request->post('search')!=null)
        {
           $search_value1 =  $request->post('search');
           $search = $search_value1['value'];
            if($search!="") {
                    $wheresearch = " where (G.grn_no like '%$search%' or G.vendor_name like '%$search%' or 
                                    G.category_name like '%$search%' or G.po_no like '%$search%' or G.inv_nos like '%$search%' or 
                                    G.ded_amt like '%$search%' or G.net_amt  like '%$search%' or G.username like '%$search%') ";
            } 
        }

        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select * from 
                (select E.*, F.username from 
                (select C.*, D.grn_no, D.vendor_name, D.category_name, D.po_no from 
                (select A.*, B.is_paid from 
                (select grn_id, inv_nos, (taxable_amt+tax_amt+other_amt) as net_amt, 
                    (shortage_amt+expiry_amt+damaged_amt+magrin_diff_amt) as ded_amt, 
                    updated_date, updated_by, approved_by from 
                (select distinct A.grn_id, GROUP_CONCAT(distinct A.invoice_no) as inv_nos, 
                        sum(case when A.particular='Taxable Amount' then A.edited_val else 0 end) as taxable_amt, 
                        sum(case when A.particular='Tax' then A.edited_val else 0 end) as tax_amt, 
                        sum(case when A.particular='Other Charges' then A.edited_val else 0 end) as other_amt, 
                        sum(case when A.particular='Shortage Amount' then A.edited_val else 0 end) as shortage_amt, 
                        sum(case when A.particular='Expiry Amount' then A.edited_val else 0 end) as expiry_amt, 
                        sum(case when A.particular='Damaged Amount' then A.edited_val else 0 end) as damaged_amt, 
                        sum(case when A.particular='Margin Diff Amount' then A.edited_val else 0 end) as magrin_diff_amt, 
                        max(A.updated_date) as updated_date, max(A.updated_by) as updated_by, max(A.approved_by) as approved_by 
                from acc_grn_entries A where A.is_active = '1' and A.company_id = '$company_id' ".$cond." group by A.grn_id) AA) A 
                left join 
                (select distinct ref_id, is_paid from acc_ledger_entries where ref_type = 'purchase' and is_active = '1' and status = 'Approved' and is_paid = '1') B 
                on (A.grn_id = B.ref_id)) C 
                left join 
                (select * from grn where status = 'approved') D 
                on (C.grn_id = D.grn_id)) E 
                left join 
                (select * from user) F 
                on (E.updated_by = F.id)) G ".$wheresearch."
                order by UNIX_TIMESTAMP(G.updated_date) desc ".$len;
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getCountPurchaseDetails($status=""){
        $cond = "";
        $len='';
        if($status!=""){
            $cond = " and A.status = '$status'";
        }
        $request = Yii::$app->request;
        if($request->post('start'))
        {
            $mycomponent = Yii::$app->mycomponent;   
            $start = $request->post('start');
            $length = $request->post('length'); 
            $len = "LIMIT ".$start.", ".$length;  
        }

        $wheresearch = '';
        if($request->post('search')!=null)
        {
           $search_value1 =  $request->post('search');
           $search = $search_value1['value'];
            if($search!="") {
                $wheresearch = " where (G.grn_no like '%$search%' or G.vendor_name like '%$search%' or 
                                G.category_name like '%$search%' or G.po_no like '%$search%' or G.inv_nos like '%$search%' or 
                                G.ded_amt like '%$search%' or G.net_amt  like '%$search%' or G.username like '%$search%') ";
            } 
        }

        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select count(*) as count from 
                (select E.*, F.username from 
                (select C.*, D.grn_no, D.vendor_name, D.category_name, D.po_no from 
                (select A.*, B.is_paid from 
                (select grn_id, inv_nos, (taxable_amt+tax_amt+other_amt) as net_amt, 
                    (shortage_amt+expiry_amt+damaged_amt+magrin_diff_amt) as ded_amt, 
                    updated_date, updated_by, approved_by from 
                (select distinct A.grn_id, GROUP_CONCAT(distinct A.invoice_no) as inv_nos, 
                        sum(case when A.particular='Taxable Amount' then A.edited_val else 0 end) as taxable_amt, 
                        sum(case when A.particular='Tax' then A.edited_val else 0 end) as tax_amt, 
                        sum(case when A.particular='Other Charges' then A.edited_val else 0 end) as other_amt, 
                        sum(case when A.particular='Shortage Amount' then A.edited_val else 0 end) as shortage_amt, 
                        sum(case when A.particular='Expiry Amount' then A.edited_val else 0 end) as expiry_amt, 
                        sum(case when A.particular='Damaged Amount' then A.edited_val else 0 end) as damaged_amt, 
                        sum(case when A.particular='Margin Diff Amount' then A.edited_val else 0 end) as magrin_diff_amt, 
                        max(A.updated_date) as updated_date, max(A.updated_by) as updated_by, max(A.approved_by) as approved_by 
                from acc_grn_entries A where A.is_active = '1' and A.company_id = '$company_id' ".$cond." group by A.grn_id) AA) A 
                left join 
                (select distinct ref_id, is_paid from acc_ledger_entries where ref_type = 'purchase' and is_active = '1' and status = 'Approved' and is_paid = '1') B 
                on (A.grn_id = B.ref_id)) C 
                left join 
                (select * from grn where status = 'approved') D 
                on (C.grn_id = D.grn_id)) E 
                left join 
                (select * from user) F 
                on (E.updated_by = F.id)) G ".$wheresearch;
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        $result =  $reader->readAll();
        return $result[0]['count'];
    }

    public function getGrnPostingDetails($id){
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        // $sql = "select AA.*, BB.tax_zone_code, BB.tax_zone_name, BB.cgst_rate, BB.sgst_rate, BB.igst_rate, 
        //             ifnull((AA.invoice_qty*AA.cost_excl_vat),0) as total_cost, ifnull(AA.invoice_qty*round(AA.cost_excl_vat*AA.vat_percen/100,2),0) as total_tax, 
        //             ifnull(AA.invoice_qty*round(AA.cost_excl_vat*BB.cgst_rate/100,2),0) as total_cgst, ifnull(AA.invoice_qty*round(AA.cost_excl_vat*BB.sgst_rate/100,2),0) as total_sgst, 
        //             ifnull(AA.invoice_qty*round(AA.cost_excl_vat*BB.igst_rate/100,2),0) as total_igst, ifnull((AA.invoice_qty*AA.cost_incl_vat_cst),0) as total_amount, 
        //             ifnull((AA.excess_qty*AA.cost_excl_vat),0) as excess_cost, ifnull((AA.excess_qty*AA.cost_excl_vat*AA.vat_percen)/100,0) as excess_tax, 
        //             ifnull((AA.excess_qty*AA.cost_excl_vat*BB.cgst_rate)/100,0) as excess_cgst, ifnull((AA.excess_qty*AA.cost_excl_vat*BB.sgst_rate)/100,0) as excess_sgst, 
        //             ifnull((AA.excess_qty*AA.cost_excl_vat*BB.igst_rate)/100,0) as excess_igst, 
        //             ifnull((AA.shortage_qty*AA.cost_excl_vat),0) as shortage_cost, ifnull((AA.shortage_qty*AA.cost_excl_vat*AA.vat_percen)/100,0) as shortage_tax, 
        //             ifnull((AA.shortage_qty*AA.cost_excl_vat*BB.cgst_rate)/100,0) as shortage_cgst, ifnull((AA.shortage_qty*AA.cost_excl_vat*BB.sgst_rate)/100,0) as shortage_sgst, 
        //             ifnull((AA.shortage_qty*AA.cost_excl_vat*BB.igst_rate)/100,0) as shortage_igst, 
        //             ifnull((AA.expiry_qty*AA.cost_excl_vat),0) as expiry_cost, ifnull((AA.expiry_qty*AA.cost_excl_vat*AA.vat_percen)/100,0) as expiry_tax, 
        //             ifnull((AA.expiry_qty*AA.cost_excl_vat*BB.cgst_rate)/100,0) as expiry_cgst, ifnull((AA.expiry_qty*AA.cost_excl_vat*BB.sgst_rate)/100,0) as expiry_sgst, 
        //             ifnull((AA.expiry_qty*AA.cost_excl_vat*BB.igst_rate)/100,0) as expiry_igst, 
        //             ifnull((AA.damaged_qty*AA.cost_excl_vat),0) as damaged_cost, ifnull((AA.damaged_qty*AA.cost_excl_vat*AA.vat_percen)/100,0) as damaged_tax, 
        //             ifnull((AA.damaged_qty*AA.cost_excl_vat*BB.cgst_rate)/100,0) as damaged_cgst, ifnull((AA.damaged_qty*AA.cost_excl_vat*BB.sgst_rate)/100,0) as damaged_sgst, 
        //             ifnull((AA.damaged_qty*AA.cost_excl_vat*BB.igst_rate)/100,0) as damaged_igst, 
        //             case when AA.margindiff_cost<=0 then 0 else AA.margindiff_cost end as margindiff_cost, 
        //             case when AA.margindiff_cost<=0 then 0 else ifnull((AA.margindiff_cost*AA.vat_percen)/100,0) end as margindiff_tax, 
        //             case when AA.margindiff_cost<=0 then 0 else ifnull((AA.margindiff_cost*BB.cgst_rate)/100,0) end as margindiff_cgst, 
        //             case when AA.margindiff_cost<=0 then 0 else ifnull((AA.margindiff_cost*BB.sgst_rate)/100,0) end as margindiff_sgst, 
        //             case when AA.margindiff_cost<=0 then 0 else ifnull((AA.margindiff_cost*BB.igst_rate)/100,0) end as margindiff_igst from 
        //         (select *, case when ifnull(box_price,0)=0 then 0 
        //                         when ifnull(invoice_qty,0)=0 then 0 
        //                         when (ifnull(invoice_qty,0)-ifnull(shortage_qty,0)-ifnull(expiry_qty,0)-ifnull(damaged_qty,0))<=0 then 0 
        //                         else ifnull(ifnull(((margin_from_po-margin_from_scan)/100*box_price*(ifnull(invoice_qty,0)-ifnull(shortage_qty,0)-ifnull(expiry_qty,0)-ifnull(damaged_qty,0))) / (1+(ifnull(vat_percen,0)/100)),0),0) end as margindiff_cost from 
        //         (select A.grn_id, A.vendor_id, A.warehouse_id, B.psku, A.vat_cst, B.invoice_no, B.box_price, B.cost_excl_vat, B.vat_percen, 
        //                 B.cost_incl_vat_cst, B.invoice_qty, B.excess_qty, B.shortage_qty, B.expiry_qty, B.damaged_qty, 
        //                 D.purchase_order_id, D.mrp as po_mrp, D.cost_price_inc_tax as po_cost_price_inc_tax, E.other_charge, 
        //                 case when ifnull(B.box_price,0) = 0 then 0 
        //                     else truncate((ifnull(B.box_price,0)-ifnull(B.cost_incl_vat_cst,0))/ifnull(B.box_price,0)*100,2) end as margin_from_scan, 
        //                 case when D.purchase_order_id is not null then 
        //                         case when ifnull(D.mrp,0) = 0 then 0 
        //                             else truncate((ifnull(D.mrp,0)-ifnull(D.cost_price_inc_tax,0))/ifnull(D.mrp,0)*100,2) end 
        //                     else null 
        //                 end as margin_from_po 
        //         from grn A 
        //             left join grn_entries B on (A.grn_id = B.grn_id) 
        //             left join purchase_order C on (A.po_no = C.po_no and A.vendor_id = C.vendor_id) 
        //             left join purchase_order_items D on (C.purchase_order_id = D.purchase_order_id and B.psku = D.psku) 
        //             left join goods_inward_outward_invoices E on (A.gi_id = E.gi_go_ref_no and B.invoice_no = E.invoice_no) 
        //         where A.status = 'approved' and A.is_active = '1' and A.grn_id = '$id' and A.company_id = '$company_id' and 
        //                 B.is_active = '1' and B.grn_id = '$id' and B.invoice_no is not null and 
        //                 C.is_active = '1') A) AA 
        //         left join 
        //         (select id, tax_zone_code, tax_zone_name, parent_id, tax_rate, 
        //             max(case when child_tax_type_code = 'CGST' then child_tax_rate else 0 end) as cgst_rate, 
        //             max(case when child_tax_type_code = 'SGST' then child_tax_rate else 0 end) as sgst_rate, 
        //             max(case when child_tax_type_code = 'IGST' then child_tax_rate else 0 end) as igst_rate from 
        //         (select A.id, A.tax_zone_code, A.tax_zone_name, B.id as parent_id, B.tax_type_id as parent_tax_type_id, 
        //             B.tax_rate, C.child_id, D.tax_type_id as child_tax_type_id, D.tax_rate as child_tax_rate, 
        //             E.tax_category as child_tax_category, E.tax_type_code as child_tax_type_code, 
        //             E.tax_type_name as child_tax_type_name 
        //         from tax_zone_master A 
        //             left join tax_rate_master B on (A.id = B.tax_zone_id) 
        //             left join tax_component C on (B.id = C.parent_id) 
        //             left join tax_rate_master D on (C.child_id = D.id) 
        //             left join tax_type_master E on (D.tax_type_id = E.id)) C 
        //         where child_tax_rate != 0
        //         group by id, tax_zone_code, tax_zone_name, parent_id, tax_rate) BB 
        //         on (AA.vat_cst = BB.tax_zone_code and round(AA.vat_percen,4)=round(BB.tax_rate,4)) 
        //         order by BB.tax_zone_code, AA.vat_percen, AA.vat_cst, BB.cgst_rate, BB.sgst_rate, BB.igst_rate";


        $sql = "select AA.*, BB.tax_zone_code, BB.tax_zone_name, BB.cgst_rate, BB.sgst_rate, BB.igst_rate, 
                    ifnull((AA.invoice_qty*AA.cost_excl_vat),0) as total_cost, ifnull(AA.invoice_qty*round(AA.cost_excl_vat*AA.vat_percen/100,2),0) as total_tax, 
                    ifnull(AA.invoice_qty*round(AA.cost_excl_vat*BB.cgst_rate/100,2),0) as total_cgst, ifnull(AA.invoice_qty*round(AA.cost_excl_vat*BB.sgst_rate/100,2),0) as total_sgst, 
                    ifnull(AA.invoice_qty*round(AA.cost_excl_vat*BB.igst_rate/100,2),0) as total_igst, ifnull((AA.invoice_qty*AA.cost_incl_vat_cst),0) as total_amount, 
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
                    case when AA.margindiff_cost<=0 then 0 else AA.margindiff_cost end as margindiff_cost, 
                    case when AA.margindiff_cost<=0 then 0 else ifnull((AA.margindiff_cost*AA.vat_percen)/100,0) end as margindiff_tax, 
                    case when AA.margindiff_cost<=0 then 0 else ifnull((AA.margindiff_cost*BB.cgst_rate)/100,0) end as margindiff_cgst, 
                    case when AA.margindiff_cost<=0 then 0 else ifnull((AA.margindiff_cost*BB.sgst_rate)/100,0) end as margindiff_sgst, 
                    case when AA.margindiff_cost<=0 then 0 else ifnull((AA.margindiff_cost*BB.igst_rate)/100,0) end as margindiff_igst from 
                (select *, case when ifnull(box_price,0)=0 then 0 
                                when ifnull(invoice_qty,0)=0 then 0 
                                when (ifnull(invoice_qty,0)-ifnull(shortage_qty,0)-ifnull(expiry_qty,0)-ifnull(damaged_qty,0))<=0 then 0 
                                else ifnull(ifnull(((margin_from_po-margin_from_scan)/100*box_price*(ifnull(invoice_qty,0)-ifnull(shortage_qty,0)-ifnull(expiry_qty,0)-ifnull(damaged_qty,0))) / (1+(ifnull(vat_percen,0)/100)),0),0) end as margindiff_cost from 
                (select A.*, B.margin_understanding, 
                        case when B.margin_understanding='GMP' then 
                                case when ifnull(A.box_price,0) = 0 then 0 
                                else truncate((ifnull(A.box_price,0)-ifnull(A.cost_excl_vat,0))/ifnull(A.box_price,0)*100,2) 
                                end 
                            when B.margin_understanding='NMP' then 
                                case when ifnull(A.box_price,0) = 0 then 0 
                                else truncate((ifnull(A.box_price,0)-ifnull(A.cost_incl_vat_cst,0))/ifnull(A.box_price,0)*100,2) 
                                end 
                            when B.margin_understanding='NMS' then 
                                case when ifnull(A.box_price,0) = 0 then 0 
                                else truncate((ifnull(A.box_price,0)-ifnull(A.cost_excl_vat,0)-ifnull((ifnull(A.box_price,0)-(ifnull(A.box_price,0)/(1+(ifnull(A.vat_percen,0)/100)))),0))/ifnull(A.box_price,0)*100,2) 
                                end 
                            else 0 
                        end as margin_from_scan, 
                        case when B.margin_understanding='GMP' then 
                                case when ifnull(A.po_mrp,0) = 0 then 0 
                                else truncate((ifnull(A.po_mrp,0)-ifnull(A.po_cost_price_exc_tax,0))/ifnull(A.po_mrp,0)*100,2) 
                                end 
                            when B.margin_understanding='NMP' then 
                                case when ifnull(A.po_mrp,0) = 0 then 0 
                                else truncate((ifnull(A.po_mrp,0)-ifnull(A.po_cost_price_inc_tax,0))/ifnull(A.po_mrp,0)*100,2) 
                                end 
                            when B.margin_understanding='NMS' then 
                                case when ifnull(A.po_mrp,0) = 0 then 0 
                                else truncate((ifnull(A.po_mrp,0)-ifnull(A.po_cost_price_exc_tax,0)-ifnull((ifnull(A.po_mrp,0)-(ifnull(A.po_mrp,0)/(1+(ifnull(A.po_vat_percen,0)/100)))),0))/ifnull(A.po_mrp,0)*100,2) 
                                end 
                            else 0 
                        end as margin_from_po from 
                (select A.grn_id, A.vendor_id, A.warehouse_id, B.psku, A.vat_cst, B.invoice_no, B.box_price, B.cost_excl_vat, B.vat_percen, 
                        B.cost_incl_vat_cst, B.invoice_qty, B.excess_qty, B.shortage_qty, B.expiry_qty, B.damaged_qty, 
                        D.purchase_order_id, D.mrp as po_mrp, D.vat_tax_percentage as po_vat_percen, D.cost_price_exc_tax as po_cost_price_exc_tax, 
                        D.cost_price_inc_tax as po_cost_price_inc_tax, E.other_charge 
                from grn A 
                    left join grn_entries B on (A.grn_id = B.grn_id) 
                    left join purchase_order C on (A.po_no = C.po_no and A.vendor_id = C.vendor_id) 
                    left join purchase_order_items D on (C.purchase_order_id = D.purchase_order_id and B.psku = D.psku) 
                    left join goods_inward_outward_invoices E on (A.gi_id = E.gi_go_ref_no and B.invoice_no = E.invoice_no) 
                where A.status = 'approved' and A.is_active = '1' and A.grn_id = '$id' and A.company_id = '$company_id' and 
                        B.is_active = '1' and B.grn_id = '$id' and B.invoice_no is not null and 
                        C.is_active = '1') A 
                left join 
                (select distinct sku_internal_code, margin_understanding from product_master where is_active = '1' and company_id = '$company_id') B 
                on (A.psku = B.sku_internal_code)) A) AA 
                left join 
                (select id, tax_zone_code, tax_zone_name, parent_id, tax_rate, 
                    max(case when child_tax_type_code = 'CGST' then child_tax_rate else 0 end) as cgst_rate, 
                    max(case when child_tax_type_code = 'SGST' then child_tax_rate else 0 end) as sgst_rate, 
                    max(case when child_tax_type_code = 'IGST' then child_tax_rate else 0 end) as igst_rate from 
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
                on (AA.vat_cst = BB.tax_zone_code and round(AA.vat_percen,4)=round(BB.tax_rate,4)) 
                order by BB.tax_zone_code, AA.vat_percen, AA.vat_cst, BB.cgst_rate, BB.sgst_rate, BB.igst_rate";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getGrnAccEntries($id){
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select A.tax_zone_code, A.tax_zone_name, B.* from 
                (select AA.*, BB.tax_zone_code, BB.tax_zone_name from grn AA 
                    left join tax_zone_master BB on (AA.vat_cst = BB.tax_zone_code) 
                where AA.grn_id = '$id' and AA.is_active = '1' and AA.company_id = '$company_id' and BB.is_active = '1' limit 1) A 
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
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select distinct psku from grn_entries where grn_id = '$id' and is_active = '1' and company_id = '$company_id'";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getGrnInvoices($id){
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select distinct invoice_no from grn_entries where grn_id = '$id' and is_active = '1' and company_id = '$company_id'";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getInvoiceDeductionDetails($id, $col_qty){
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        if($col_qty!='margindiff_qty'){
            $sql = "select AA.*, BB.cgst_rate, BB.sgst_rate, BB.igst_rate from 
                    (select * from 
                    (select K.*, case when K.margindiff_cost<=0 then 0 else K.proper_qty end as margindiff_qty from 
                    (select J.*, case when ifnull(box_price,0)=0 then 0 
                                    when ifnull(invoice_qty,0)=0 then 0 
                                    when (ifnull(invoice_qty,0)-ifnull(shortage_qty,0)-ifnull(expiry_qty,0)-ifnull(damaged_qty,0))<=0 then 0 
                                    else ifnull(ifnull(((margin_from_po-margin_from_scan)/100*box_price*(ifnull(invoice_qty,0)-ifnull(shortage_qty,0)-ifnull(expiry_qty,0)-ifnull(damaged_qty,0))) / (1+(ifnull(vat_percen,0)/100)),0),0) end as margindiff_cost from 
                    (select A.vendor_id, A.warehouse_id, B.*, D.tax_zone_code, D.tax_zone_name, E.invoice_date, A.gi_date, 
                        date_add(A.gi_date, interval ifnull(B.min_no_of_months_shelf_life_required,0) month) as earliest_expected_date, 
                        null as cost_acc_id, null as cost_ledger_name, null as cost_ledger_code, 
                        null as tax_acc_id, null as tax_ledger_name, null as tax_ledger_code, 
                        null as cgst_acc_id, null as cgst_ledger_name, null as cgst_ledger_code, 
                        null as sgst_acc_id, null as sgst_ledger_name, null as sgst_ledger_code, 
                        null as igst_acc_id, null as igst_ledger_name, null as igst_ledger_code, 
                        G.purchase_order_id, 
                        case when G.purchase_order_id is not null then G.mrp else null end as po_mrp, 
                        case when G.purchase_order_id is not null then G.cost_price_exc_tax else null end as po_unit_rate_excl_tax, 
                        case when G.purchase_order_id is not null then G.unit_tax_amount else null end as po_unit_tax, 
                        case when G.purchase_order_id is not null then G.cost_price_inc_tax else null end as po_unit_rate_incl_tax, 
                        case when ifnull(B.box_price,0) = 0 then 0 
                            else truncate((ifnull(B.box_price,0)-ifnull(B.cost_incl_vat_cst,0))/ifnull(B.box_price,0)*100,2) end as margin_from_scan, 
                        case when G.purchase_order_id is not null then 
                                case when ifnull(G.mrp,0) = 0 then 0 
                                    else truncate((ifnull(G.mrp,0)-ifnull(G.cost_price_inc_tax,0))/ifnull(G.mrp,0)*100,2) end 
                            else null 
                        end as margin_from_po 
                    from grn A 
                    left join grn_entries B on (A.grn_id = B.grn_id) 
                    left join tax_zone_master D on (A.vat_cst = D.tax_zone_code) 
                    left join goods_inward_outward_invoices E on (A.gi_id = E.gi_go_ref_no and B.invoice_no = E.invoice_no) 
                    left join purchase_order F on (A.po_no = F.po_no and A.vendor_id = F.vendor_id) 
                    left join purchase_order_items G on (F.purchase_order_id = G.purchase_order_id and B.psku = G.psku) 
                    where A.grn_id = '$id' and B.grn_id = '$id' and A.is_active = '1' and A.company_id = '$company_id' and B.is_active = '1' and 
                            D.is_active = '1' and F.is_active = '1' and B.invoice_no is not null) J) K) L 
                    where L." . $col_qty . " > 0 
                    order by L.invoice_no, L.vat_percen) AA 
                    left join 
                    (select id, tax_zone_code, tax_zone_name, parent_id, tax_rate, 
                        max(case when child_tax_type_code = 'CGST' then child_tax_rate else 0 end) as cgst_rate, 
                        max(case when child_tax_type_code = 'SGST' then child_tax_rate else 0 end) as sgst_rate, 
                        max(case when child_tax_type_code = 'IGST' then child_tax_rate else 0 end) as igst_rate from 
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
            $result = $reader->readAll();
        } else {
            $sql = "select AA.*, BB.cgst_rate, BB.sgst_rate, BB.igst_rate from 
                    (select A.vendor_id, A.warehouse_id, B.*, D.tax_zone_code, D.tax_zone_name, E.invoice_date, A.gi_date, 
                        date_add(A.gi_date, interval ifnull(B.min_no_of_months_shelf_life_required,0) month) as earliest_expected_date, 
                        null as cost_acc_id, null as cost_ledger_name, null as cost_ledger_code, 
                        null as tax_acc_id, null as tax_ledger_name, null as tax_ledger_code, 
                        null as cgst_acc_id, null as cgst_ledger_name, null as cgst_ledger_code, 
                        null as sgst_acc_id, null as sgst_ledger_name, null as sgst_ledger_code, 
                        null as igst_acc_id, null as igst_ledger_name, null as igst_ledger_code, 
                        G.purchase_order_id, 
                        case when G.purchase_order_id is not null then G.mrp else null end as po_mrp, 
                        case when G.purchase_order_id is not null then G.cost_price_exc_tax else null end as po_unit_rate_excl_tax, 
                        case when G.purchase_order_id is not null then G.unit_tax_amount else null end as po_unit_tax, 
                        case when G.purchase_order_id is not null then G.cost_price_inc_tax else null end as po_unit_rate_incl_tax 
                    from grn A 
                    left join grn_entries B on (A.grn_id = B.grn_id) 
                    left join tax_zone_master D on (A.vat_cst = D.tax_zone_code) 
                    left join goods_inward_outward_invoices E on (A.gi_id = E.gi_go_ref_no and B.invoice_no = E.invoice_no) 
                    left join purchase_order F on (A.po_no = F.po_no and A.vendor_id = F.vendor_id) 
                    left join purchase_order_items G on (F.purchase_order_id = G.purchase_order_id and B.psku = G.psku) 
                    where A.grn_id = '$id' and B.grn_id = '$id' and A.is_active = '1' and A.company_id = '$company_id' and B.is_active = '1' and 
                            D.is_active = '1' and F.is_active = '1' and B.invoice_no is not null) AA 
                    left join 
                    (select id, tax_zone_code, tax_zone_name, parent_id, tax_rate, 
                        max(case when child_tax_type_code = 'CGST' then child_tax_rate else 0 end) as cgst_rate, 
                        max(case when child_tax_type_code = 'SGST' then child_tax_rate else 0 end) as sgst_rate, 
                        max(case when child_tax_type_code = 'IGST' then child_tax_rate else 0 end) as igst_rate from 
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
            $result = $reader->readAll();
        }

        $data = array();
        $j = 0;

        if(count($result)>0){
            for($i=0; $i<count($result); $i++){
                $warehouse_code = $result[$i]['warehouse_id'];
                $state_name = '';
                $result2 = $this->getState($warehouse_code);
                if(count($result2)>0){
                    $state_name = $result2[0]['state_name'];
                }

                if($result[$i]['igst_rate']==0){
                    $vat_percen = $result[$i]['vat_percen'];
                    if(is_numeric($vat_percen)){
                        $vat_percen = floatval($vat_percen);
                    }
                    $tax_code = 'Purchase-'.$state_name.'-Local-'.$vat_percen;
                    // echo $tax_code;
                    // echo '<br/>';
                    $result2 = $this->getAccountDetails('','',$tax_code);
                    if(count($result2)>0){
                        // echo json_encode($result2);
                        // echo '<br/>';
                        $result[$i]['cost_acc_id'] = $result2[0]['id'];
                        $result[$i]['cost_ledger_name'] = $result2[0]['legal_name'];
                        $result[$i]['cost_ledger_code'] = $result2[0]['code'];
                    }

                    $cgst_rate = $result[$i]['cgst_rate'];
                    if(is_numeric($cgst_rate)){
                        $cgst_rate = floatval($cgst_rate);
                    }
                    $tax_code = 'Input-'.$state_name.'-CGST-'.$cgst_rate;
                    // echo $tax_code;
                    // echo '<br/>';
                    $result2 = $this->getAccountDetails('','',$tax_code);
                    if(count($result2)>0){
                        // echo json_encode($result2);
                        // echo '<br/>';
                        $result[$i]['cgst_acc_id'] = $result2[0]['id'];
                        $result[$i]['cgst_ledger_name'] = $result2[0]['legal_name'];
                        $result[$i]['cgst_ledger_code'] = $result2[0]['code'];
                    }

                    $sgst_rate = $result[$i]['sgst_rate'];
                    if(is_numeric($sgst_rate)){
                        $sgst_rate = floatval($sgst_rate);
                    }
                    $tax_code = 'Input-'.$state_name.'-SGST-'.$sgst_rate;
                    // echo $tax_code;
                    // echo '<br/>';
                    $result2 = $this->getAccountDetails('','',$tax_code);
                    if(count($result2)>0){
                        // echo json_encode($result2);
                        // echo '<br/>';
                        $result[$i]['sgst_acc_id'] = $result2[0]['id'];
                        $result[$i]['sgst_ledger_name'] = $result2[0]['legal_name'];
                        $result[$i]['sgst_ledger_code'] = $result2[0]['code'];
                    }
                } else {
                    $vat_percen = $result[$i]['vat_percen'];
                    if(is_numeric($vat_percen)){
                        $vat_percen = floatval($vat_percen);
                    }
                    $tax_code = 'Purchase-'.$state_name.'-Inter State-'.$vat_percen;
                    // echo $tax_code;
                    // echo '<br/>';
                    $result2 = $this->getAccountDetails('','',$tax_code);
                    if(count($result2)>0){
                        $result[$i]['cost_acc_id'] = $result2[0]['id'];
                        $result[$i]['cost_ledger_name'] = $result2[0]['legal_name'];
                        $result[$i]['cost_ledger_code'] = $result2[0]['code'];
                    }

                    $igst_rate = $result[$i]['igst_rate'];
                    if(is_numeric($igst_rate)){
                        $igst_rate = floatval($igst_rate);
                    }
                    $tax_code = 'Input-'.$state_name.'-IGST-'.$igst_rate;
                    // echo $tax_code;
                    // echo '<br/>';
                    $result2 = $this->getAccountDetails('','',$tax_code);
                    if(count($result2)>0){
                        $result[$i]['igst_acc_id'] = $result2[0]['id'];
                        $result[$i]['igst_ledger_name'] = $result2[0]['legal_name'];
                        $result[$i]['igst_ledger_code'] = $result2[0]['code'];
                    }
                }

                if($col_qty!='margindiff_qty'){
                    $data[$j]=$result[$i];
                    $j = $j + 1;
                } else {
                    if($result[$i]['purchase_order_id']!=null && $result[$i]['purchase_order_id']!=''){
                        $box_price = floatval($result[$i]['box_price']);
                        $cost_incl_vat_cst = floatval($result[$i]['cost_incl_vat_cst']);
                        $proper_qty = floatval($result[$i]['proper_qty']);
                        $invoice_qty = floatval($result[$i]['invoice_qty']);
                        $shortage_qty = floatval($result[$i]['shortage_qty']);
                        $expiry_qty = floatval($result[$i]['expiry_qty']);
                        $damaged_qty = floatval($result[$i]['damaged_qty']);
                        $vat_percen = floatval($result[$i]['vat_percen']);
                        $cgst_rate = floatval($result[$i]['cgst_rate']);
                        $sgst_rate = floatval($result[$i]['sgst_rate']);
                        $igst_rate = floatval($result[$i]['igst_rate']);
                        $product_mrp = floatval($result[$i]['po_mrp']);
                        $cost_price_exc_tax = floatval($result[$i]['po_unit_rate_excl_tax']);
                        $vat_cst_tax_amount = floatval($result[$i]['po_unit_tax']);
                        $landed_cost = floatval($result[$i]['po_unit_rate_incl_tax']);

                        if($box_price==0){
                            $margin_from_scan = 0;
                        } else {
                            // $margin_from_scan = ($box_price-$cost_incl_vat_cst)/$box_price*100;
                            // $margin_from_scan = intval($margin_from_scan*100)/100;
                            $margin_from_scan = intval((($box_price-$cost_incl_vat_cst)/$box_price*100)*100)/100;
                        }
                        // echo $box_price;
                        // echo '<br/>';
                        // echo $cost_incl_vat_cst;
                        // echo '<br/>';
                        // echo $margin_from_scan;
                        // echo '<br/>';

                        if($product_mrp==0){
                            $margin_from_po = 0;
                        } else {
                            // $margin_from_po = ($product_mrp-$landed_cost)/$product_mrp*100;
                            // $margin_from_po = intval($margin_from_po*100)/100;
                            $margin_from_po = intval((($product_mrp-$landed_cost)/$product_mrp*100)*100)/100;
                        }

                        if($box_price==0){
                            $margindiff_cost = 0;
                        } else if($invoice_qty==0){
                            $margindiff_cost = 0;
                        } else if(($invoice_qty-$shortage_qty-$expiry_qty-$damaged_qty)<=0){
                            $margindiff_cost = 0;
                        // } else if($margin_from_po==0){
                        //     $margindiff_cost = 0;
                        // } else if($margin_from_scan==0){
                        //     $margindiff_cost = 0;
                        } else {
                            $margindiff_cost = round((($margin_from_po-$margin_from_scan)/100*$box_price*($invoice_qty-$shortage_qty-$expiry_qty-$damaged_qty)) / (1+($vat_percen/100)),2);
                        }

                        if(round($margindiff_cost,4)>0){
                            $data[$j]=$result[$i];
                            $data[$j]['po_mrp']=$product_mrp;
                            $data[$j]['po_unit_rate_excl_tax']=$cost_price_exc_tax;
                            $data[$j]['po_unit_tax']=$vat_cst_tax_amount;
                            $data[$j]['po_unit_rate_incl_tax']=$landed_cost;
                            $data[$j]['margindiff_qty']=$invoice_qty-$shortage_qty-$expiry_qty-$damaged_qty;
                            $data[$j]['margin_from_scan']=$margin_from_scan;
                            $data[$j]['margin_from_po']=$margin_from_po;
                            $data[$j]['margindiff_cost']=$margindiff_cost;

                            $margindiff_cgst=round(($margindiff_cost*$cgst_rate)/100,2);
                            $margindiff_sgst=round(($margindiff_cost*$sgst_rate)/100,2);
                            $margindiff_igst=round(($margindiff_cost*$igst_rate)/100,2);

                            $data[$j]['margindiff_cgst']=$margindiff_cgst;
                            $data[$j]['margindiff_sgst']=$margindiff_sgst;
                            $data[$j]['margindiff_igst']=$margindiff_igst;
                            $data[$j]['margindiff_tax']=$margindiff_cgst+$margindiff_sgst+$margindiff_igst;

                            $j = $j + 1;
                        }
                    }
                }
                
            }
        }

        // echo json_encode($data);
        
        return $data;
    }

    public function getGrnAccSku($id){
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select * from acc_grn_sku_entries where grn_id = '$id' and is_active = '1' and company_id = '$company_id' order by invoice_no, vat_percen";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getGrnAccSkuEntries($id, $ded_type){
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select A.grn_id, B.*, B.qty as ".$ded_type."_qty, D.tax_zone_code, D.tax_zone_name, E.invoice_date 
                from grn A 
                left join acc_grn_sku_entries B on (A.grn_id = B.grn_id) 
                left join tax_zone_master D on (A.vat_cst = D.tax_zone_code) 
                left join goods_inward_outward_invoices E on (A.gi_id = E.gi_go_ref_no and B.invoice_no = E.invoice_no) 
                where A.grn_id = '$id' and B.grn_id = '$id' and A.is_active = '1' and A.company_id = '$company_id' and B.is_active = '1' and 
                    D.is_active = '1' and B.invoice_no is not null and B.ded_type = '$ded_type' and B.qty > 0 
                order by B.invoice_no, B.vat_percen";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getGrnAccLedgerEntries($id){
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select B.*, A.tax_zone_code, A.tax_zone_name from 
                (select AA.*, CC.tax_zone_code, CC.tax_zone_name from grn AA 
                left join internal_warehouse_master BB on (AA.warehouse_id = BB.warehouse_code and AA.company_id = BB.company_id) 
                left join tax_zone_master CC on (BB.tax_zone_id = CC.id) 
                where AA.grn_id = '$id' and AA.is_active = '1' and AA.company_id = '$company_id' and BB.is_active = '1' and CC.is_active = '1' limit 1) A 
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

        $company_id = $session['company_id'];

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
                'acc_id'=>($acc_id[$i]!='')?$acc_id[$i]:null,
                'ledger_name'=>$ledger_name[$i],
                'ledger_code'=>$ledger_code[$i],
                'voucher_id'=>$voucher_id[$i],
                'ledger_type'=>$ledger_type[$i],
                'vat_cst'=>$vat_cst_val[$i],
                'vat_percen'=>$mycomponent->format_number($vat_percen_val[$i],4),
                'invoice_no'=>$invoice_no_val[$i],
                'total_val'=>$mycomponent->format_number($total_val[$i],4),
                'invoice_val'=>$mycomponent->format_number($invoice_val[$i],4),
                'edited_val'=>$mycomponent->format_number($edited_val[$i],4),
                'difference_val'=>$mycomponent->format_number($difference_val[$i],4),
                'narration'=>$narration_val[$i],
                'status'=>'approved',
                'is_active'=>'1',
                'updated_by'=>$session['session_id'],
                'updated_date'=>date('Y-m-d h:i:s'),
                'gi_date'=>$gi_date,
                'company_id'=>$company_id
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
                    // $bl_flag = true;
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

                        $bl_flag = true;
                        
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
                    // $bl_flag = true;
                    // echo json_encode($result['ledgerArray']);
                    // echo '<br/>';
                    // echo count($result['ledgerArray']);
                    // echo '<br/>';
                    // echo json_encode($ledgerArray);
                    // echo '<br/>';
                    // echo count($ledgerArray);
                    // echo '<br/>';

                    for($l=0; $l<count($result['ledgerArray']); $l++){

                        $bl_flag = true;
                        
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
                    // $bl_flag = true;
                    for($l=0; $l<count($result['ledgerArray']); $l++){

                        $bl_flag = true;
                        
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
                    // $bl_flag = true;
                    for($l=0; $l<count($result['ledgerArray']); $l++){

                        $bl_flag = true;

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
                        // echo $particular[$i];
                        // echo '<br/>';
                        // echo $edited_val[$i];
                        // echo '<br/>';
                        // echo $mycomponent->format_number($edited_val[$i],2);
                        // echo '<br/>';
                        
                        $ledgerArray[$k]=[
                                    'ref_id'=>$gi_id,
                                    'ref_type'=>'purchase',
                                    'entry_type'=>$particular[$i],
                                    'invoice_no'=>$invoice_no_val[$i],
                                    'vendor_id'=>$vendor_id,
                                    'acc_id'=>($acc_id[$i]!='')?$acc_id[$i]:null,
                                    'ledger_name'=>$ledger_name[$i],
                                    'ledger_code'=>$ledger_code[$i],
                                    'voucher_id'=>$voucher_id[$i],
                                    'ledger_type'=>$ledger_type[$i],
                                    'type'=>$ledg_type,
                                    'amount'=>$mycomponent->format_number($edited_val[$i],4),
                                    'narration'=>$narration_val[$i],
                                    'status'=>'approved',
                                    'is_active'=>'1',
                                    'updated_by'=>$session['session_id'],
                                    'updated_date'=>date('Y-m-d h:i:s'),
                                    'ref_date'=>$gi_date,
                                    'company_id'=>$company_id
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

        $po_mrp = $request->post($ded_type.'_po_mrp');
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

        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        for($i=0; $i<count($invoice_no); $i++){
            $qty_val = $mycomponent->format_number($qty[$i],2);
            if($qty_val>0 && $invoice_no_val==$invoice_no[$i]){
                $bulkInsertArray[$i]=[
                            'grn_id'=>$gi_id,
                            'vendor_id'=>$vendor_id,
                            'ded_type'=>$ded_type,
                            'cost_acc_id'=>($cost_acc_id[$i]!='')?$cost_acc_id[$i]:null,
                            'cost_ledger_name'=>$cost_ledger_name[$i],
                            'cost_ledger_code'=>$cost_ledger_code[$i],
                            // 'cost_voucher_id'=>$voucher_id,
                            // 'cost_ledger_type'=>'Sub Entry',
                            'tax_acc_id'=>($tax_acc_id[$i]!='')?$tax_acc_id[$i]:null,
                            'tax_ledger_name'=>$tax_ledger_name[$i],
                            'tax_ledger_code'=>$tax_ledger_code[$i],
                            // 'tax_voucher_id'=>$voucher_id,
                            // 'tax_ledger_type'=>'Sub Entry',
                            'cgst_acc_id'=>($cgst_acc_id[$i]!='')?$cgst_acc_id[$i]:null,
                            'cgst_ledger_name'=>$cgst_ledger_name[$i],
                            'cgst_ledger_code'=>$cgst_ledger_code[$i],
                            // 'cgst_voucher_id'=>$voucher_id,
                            // 'cgst_ledger_type'=>'Sub Entry',
                            'sgst_acc_id'=>($sgst_acc_id[$i]!='')?$sgst_acc_id[$i]:null,
                            'sgst_ledger_name'=>$sgst_ledger_name[$i],
                            'sgst_ledger_code'=>$sgst_ledger_code[$i],
                            // 'sgst_voucher_id'=>$voucher_id,
                            // 'sgst_ledger_type'=>'Sub Entry',
                            'igst_acc_id'=>($igst_acc_id[$i]!='')?$igst_acc_id[$i]:null,
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
                            'box_price'=>$mycomponent->format_number($box_price[$i],4),
                            'cost_excl_vat_per_unit'=>$mycomponent->format_number($cost_excl_tax_per_unit[$i],4),
                            'tax_per_unit'=>$mycomponent->format_number($tax_per_unit[$i],4),
                            'cgst_per_unit'=>$mycomponent->format_number($cgst_per_unit[$i],4),
                            'sgst_per_unit'=>$mycomponent->format_number($sgst_per_unit[$i],4),
                            'igst_per_unit'=>$mycomponent->format_number($igst_per_unit[$i],4),
                            'total_per_unit'=>$mycomponent->format_number($total_per_unit[$i],4),
                            'cost_excl_vat'=>$mycomponent->format_number($cost_excl_tax[$i],4),
                            'tax'=>$mycomponent->format_number($tax[$i],4),
                            'cgst'=>$mycomponent->format_number($cgst[$i],4),
                            'sgst'=>$mycomponent->format_number($sgst[$i],4),
                            'igst'=>$mycomponent->format_number($igst[$i],4),
                            'total'=>$mycomponent->format_number($total[$i],4),
                            'expiry_date'=>($expiry_date[$i]!='')?$expiry_date[$i]:null,
                            'earliest_expected_date'=>($earliest_expected_date[$i]!='')?$earliest_expected_date[$i]:null,
                            'status'=>'approved',
                            'is_active'=>'1',
                            'remarks'=>$remarks[$i],
                            'po_mrp'=>$mycomponent->format_number($po_mrp[$i],4),
                            'po_cost_excl_vat'=>$mycomponent->format_number($po_cost_excl_tax[$i],4),
                            'po_tax'=>$mycomponent->format_number($po_tax[$i],4),
                            'po_cgst'=>$mycomponent->format_number($po_cgst[$i],4),
                            'po_sgst'=>$mycomponent->format_number($po_sgst[$i],4),
                            'po_igst'=>$mycomponent->format_number($po_igst[$i],4),
                            'po_total'=>$mycomponent->format_number($po_total[$i],4),
                            'margin_diff_excl_tax'=>$mycomponent->format_number($diff_cost_excl_tax[$i],4),
                            'margin_diff_cgst'=>$mycomponent->format_number($diff_cgst[$i],4),
                            'margin_diff_sgst'=>$mycomponent->format_number($diff_sgst[$i],4),
                            'margin_diff_igst'=>$mycomponent->format_number($diff_igst[$i],4),
                            'margin_diff_tax'=>$mycomponent->format_number($diff_tax[$i],4),
                            'margin_diff_total'=>$mycomponent->format_number($diff_total[$i],4),
                            'company_id'=>$company_id
                        ];

                if($ded_type=='margindiff'){
                    $cost_excl_tax_amt = $mycomponent->format_number($diff_cost_excl_tax[$i],4);
                    $tax_amt = $mycomponent->format_number($diff_tax[$i],4);
                    $cgst_amt = $mycomponent->format_number($diff_cgst[$i],4);
                    $sgst_amt = $mycomponent->format_number($diff_sgst[$i],4);
                    $igst_amt = $mycomponent->format_number($diff_igst[$i],4);
                } else {
                    $cost_excl_tax_amt = $mycomponent->format_number($cost_excl_tax[$i],4);
                    $tax_amt = $mycomponent->format_number($tax[$i],4);
                    $cgst_amt = $mycomponent->format_number($cgst[$i],4);
                    $sgst_amt = $mycomponent->format_number($sgst[$i],4);
                    $igst_amt = $mycomponent->format_number($igst[$i],4);
                }

                if($cost_excl_tax_amt!=0){
                    // $code = 'Purchase_'.$state[$i].'_'.$vat_cst[$i].'_'.$vat_percen[$i];

                    $ledgerArray[$k]=[
                                    'ref_id'=>$gi_id,
                                    'ref_type'=>'purchase',
                                    'entry_type'=>$ded_type.'_cost',
                                    'invoice_no'=>$invoice_no[$i],
                                    'vendor_id'=>$vendor_id,
                                    'acc_id'=>($cost_acc_id[$i]!='')?$cost_acc_id[$i]:null,
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
                                    'ref_date'=>$gi_date,
                                    'company_id'=>$company_id
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
                                    'acc_id'=>($cgst_acc_id[$i]!='')?$cgst_acc_id[$i]:null,
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
                                    'ref_date'=>$gi_date,
                                    'company_id'=>$company_id
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
                                    'acc_id'=>($sgst_acc_id[$i]!='')?$sgst_acc_id[$i]:null,
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
                                    'ref_date'=>$gi_date,
                                    'company_id'=>$company_id
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
                                    'acc_id'=>($igst_acc_id[$i]!='')?$igst_acc_id[$i]:null,
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
                                    'ref_date'=>$gi_date,
                                    'company_id'=>$company_id
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
        // echo json_encode($result['ledgerArray']);
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

        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        // $sql = "select AA.*, BB.tax_rate, BB.cgst_rate, BB.sgst_rate, BB.igst_rate from 
        //         (select A.*, case when ifnull(box_price,0)=0 then 0 
        //                          when ifnull(invoice_qty,0)=0 then 0 
        //                          when (ifnull(invoice_qty,0)-ifnull(shortage_qty,0)-ifnull(expiry_qty,0)-ifnull(damaged_qty,0))<=0 then 0 
        //                          else ifnull(ifnull(((margin_from_po-margin_from_scan)/100*box_price*(ifnull(invoice_qty,0)-ifnull(shortage_qty,0)-ifnull(expiry_qty,0)-ifnull(damaged_qty,0))) / (1+(ifnull(vat_percen,0)/100)),0),0) end as margindiff_cost from 
        //         (select A.vat_cst as tx_zn_cd, B.*, D.tax_zone_code, D.tax_zone_name, E.invoice_date, A.gi_date, 
        //             date_add(A.gi_date, interval ifnull(min_no_of_months_shelf_life_required,0) month) as earliest_expected_date, 
        //             null as cost_acc_id, null as cost_ledger_name, null as cost_ledger_code, 
        //             null as tax_acc_id, null as tax_ledger_name, null as tax_ledger_code, 
        //             null as cgst_acc_id, null as cgst_ledger_name, null as cgst_ledger_code, 
        //             null as sgst_acc_id, null as sgst_ledger_name, null as sgst_ledger_code, 
        //             null as igst_acc_id, null as igst_ledger_name, null as igst_ledger_code, 
        //             F.purchase_order_id, G.mrp as po_mrp, G.quantity as po_quantity, G.cost_price_exc_tax as po_unit_rate_excl_tax, 
        //             G.unit_tax_amount as po_unit_tax, G.cost_price_inc_tax as po_unit_rate_incl_tax, 
        //             case when ifnull(B.box_price,0) = 0 then 0 
        //                 else truncate((ifnull(B.box_price,0)-ifnull(B.cost_incl_vat_cst,0))/ifnull(B.box_price,0)*100,2) end as margin_from_scan, 
        //             case when G.purchase_order_id is not null then 
        //                     case when ifnull(G.mrp,0) = 0 then 0 
        //                         else truncate((ifnull(G.mrp,0)-ifnull(G.cost_price_inc_tax,0))/ifnull(G.mrp,0)*100,2) end 
        //                 else null 
        //             end as margin_from_po 
        //         from grn A 
        //         left join grn_entries B on (A.grn_id = B.grn_id) 
        //         left join tax_zone_master D on (A.vat_cst = D.tax_zone_code) 
        //         left join goods_inward_outward_invoices E on (A.gi_id = E.gi_go_ref_no and B.invoice_no = E.invoice_no) 
        //         left join purchase_order F on (A.po_no = F.po_no and A.vendor_id = F.vendor_id) 
        //         left join purchase_order_items G on (F.purchase_order_id = G.purchase_order_id and B.psku = G.psku) 
        //         where A.grn_id = '$grn_id' and B.grn_id = '$grn_id' and B.psku = '$psku' and A.is_active = '1' and A.company_id = '$company_id' and 
        //                 B.is_active = '1' and D.is_active = '1' and F.is_active = '1') A) AA 
                
        //         left join 

        //         (select id, tax_zone_code, tax_zone_name, parent_id, tax_rate, 
        //             max(case when child_tax_type_code = 'CGST' then child_tax_rate else 0 end) as cgst_rate, 
        //             max(case when child_tax_type_code = 'SGST' then child_tax_rate else 0 end) as sgst_rate, 
        //             max(case when child_tax_type_code = 'IGST' then child_tax_rate else 0 end) as igst_rate from 
        //         (select A.id, A.tax_zone_code, A.tax_zone_name, B.id as parent_id, B.tax_type_id as parent_tax_type_id, 
        //             B.tax_rate, C.child_id, D.tax_type_id as child_tax_type_id, D.tax_rate as child_tax_rate, 
        //             E.tax_category as child_tax_category, E.tax_type_code as child_tax_type_code, 
        //             E.tax_type_name as child_tax_type_name 
        //         from tax_zone_master A 
        //         left join tax_rate_master B on (A.id = B.tax_zone_id) 
        //         left join tax_component C on (B.id = C.parent_id) 
        //         left join tax_rate_master D on (C.child_id = D.id) 
        //         left join tax_type_master E on (D.tax_type_id = E.id)) C 
        //         where child_tax_rate != 0
        //         group by id, tax_zone_code, tax_zone_name, parent_id, tax_rate) BB 
        //         on (AA.tax_zone_code = BB.tax_zone_code and round(AA.vat_percen,4)=round(BB.tax_rate,4))";

                // "select A.*, B.margin_understanding, 
                //         case when B.margin_understanding='GMP' then 
                //                 case when ifnull(A.box_price,0) = 0 then 0 
                //                 else truncate((ifnull(A.box_price,0)-ifnull(A.cost_excl_vat,0))/ifnull(A.box_price,0)*100,2) 
                //                 end 
                //             when B.margin_understanding='NMP' then 
                //                 case when ifnull(A.box_price,0) = 0 then 0 
                //                 else truncate((ifnull(A.box_price,0)-ifnull(A.cost_incl_vat_cst,0))/ifnull(A.box_price,0)*100,2) 
                //                 end 
                //             when B.margin_understanding='NMS' then 
                //                 case when ifnull(A.box_price,0) = 0 then 0 
                //                 else truncate((ifnull(A.box_price,0)-ifnull(A.cost_excl_vat,0)-ifnull((ifnull(A.box_price,0)-(ifnull(A.box_price,0)/(1+(ifnull(A.vat_percen,0)/100)))),0))/ifnull(A.box_price,0)*100,2) 
                //                 end 
                //             else 0 
                //         end as margin_from_scan, 
                //         case when B.margin_understanding='GMP' then 
                //                 case when ifnull(A.po_mrp,0) = 0 then 0 
                //                 else truncate((ifnull(A.po_mrp,0)-ifnull(A.po_cost_price_exc_tax,0))/ifnull(A.po_mrp,0)*100,2) 
                //                 end 
                //             when B.margin_understanding='NMP' then 
                //                 case when ifnull(A.po_mrp,0) = 0 then 0 
                //                 else truncate((ifnull(A.po_mrp,0)-ifnull(A.po_cost_price_inc_tax,0))/ifnull(A.po_mrp,0)*100,2) 
                //                 end 
                //             when B.margin_understanding='NMS' then 
                //                 case when ifnull(A.po_mrp,0) = 0 then 0 
                //                 else truncate((ifnull(A.po_mrp,0)-ifnull(A.po_cost_price_exc_tax,0)-ifnull((ifnull(A.po_mrp,0)-(ifnull(A.po_mrp,0)/(1+(ifnull(A.po_vat_percen,0)/100)))),0))/ifnull(A.po_mrp,0)*100,2) 
                //                 end 
                //             else 0 
                //         end as margin_from_po 
                // (select A.grn_id, A.vendor_id, A.warehouse_id, B.psku, A.vat_cst, B.invoice_no, B.box_price, B.cost_excl_vat, B.vat_percen, 
                //         B.cost_incl_vat_cst, B.invoice_qty, B.excess_qty, B.shortage_qty, B.expiry_qty, B.damaged_qty, 
                //         D.purchase_order_id, D.mrp as po_mrp, D.vat_tax_percentage as po_vat_percen, D.cost_price_exc_tax as po_cost_price_exc_tax, 
                //         D.cost_price_inc_tax as po_cost_price_inc_tax, E.other_charge 
                // from grn A 
                //     left join grn_entries B on (A.grn_id = B.grn_id) 
                //     left join purchase_order C on (A.po_no = C.po_no and A.vendor_id = C.vendor_id) 
                //     left join purchase_order_items D on (C.purchase_order_id = D.purchase_order_id and B.psku = D.psku) 
                //     left join goods_inward_outward_invoices E on (A.gi_id = E.gi_go_ref_no and B.invoice_no = E.invoice_no) 
                // where A.status = 'approved' and A.is_active = '1' and A.grn_id = '$id' and A.company_id = '$company_id' and 
                //         B.is_active = '1' and B.grn_id = '$id' and B.invoice_no is not null and 
                //         C.is_active = '1') A 
                // left join 
                // (select distinct sku_internal_code, margin_understanding from product_master where is_active = '1' and company_id = '$company_id') B 
                // on (A.psku = B.sku_internal_code)"


        $sql = "select AA.*, BB.tax_rate, BB.cgst_rate, BB.sgst_rate, BB.igst_rate from 
                (select A.*, case when ifnull(box_price,0)=0 then 0 
                                 when ifnull(invoice_qty,0)=0 then 0 
                                 when (ifnull(invoice_qty,0)-ifnull(shortage_qty,0)-ifnull(expiry_qty,0)-ifnull(damaged_qty,0))<=0 then 0 
                                 else ifnull(ifnull(((margin_from_po-margin_from_scan)/100*box_price*(ifnull(invoice_qty,0)-ifnull(shortage_qty,0)-ifnull(expiry_qty,0)-ifnull(damaged_qty,0))) / (1+(ifnull(vat_percen,0)/100)),0),0) end as margindiff_cost from 
                (select A.*, B.margin_understanding, 
                        case when B.margin_understanding='GMP' then 
                                case when ifnull(A.box_price,0) = 0 then 0 
                                else truncate((ifnull(A.box_price,0)-ifnull(A.cost_excl_vat,0))/ifnull(A.box_price,0)*100,2) 
                                end 
                            when B.margin_understanding='NMP' then 
                                case when ifnull(A.box_price,0) = 0 then 0 
                                else truncate((ifnull(A.box_price,0)-ifnull(A.cost_incl_vat_cst,0))/ifnull(A.box_price,0)*100,2) 
                                end 
                            when B.margin_understanding='NMS' then 
                                case when ifnull(A.box_price,0) = 0 then 0 
                                else truncate((ifnull(A.box_price,0)-ifnull(A.cost_excl_vat,0)-ifnull((ifnull(A.box_price,0)-(ifnull(A.box_price,0)/(1+(ifnull(A.vat_percen,0)/100)))),0))/ifnull(A.box_price,0)*100,2) 
                                end 
                            else 0 
                        end as margin_from_scan, 
                        case when B.margin_understanding='GMP' then 
                                case when ifnull(A.po_mrp,0) = 0 then 0 
                                else truncate((ifnull(A.po_mrp,0)-ifnull(A.po_unit_rate_excl_tax,0))/ifnull(A.po_mrp,0)*100,2) 
                                end 
                            when B.margin_understanding='NMP' then 
                                case when ifnull(A.po_mrp,0) = 0 then 0 
                                else truncate((ifnull(A.po_mrp,0)-ifnull(A.po_unit_rate_incl_tax,0))/ifnull(A.po_mrp,0)*100,2) 
                                end 
                            when B.margin_understanding='NMS' then 
                                case when ifnull(A.po_mrp,0) = 0 then 0 
                                else truncate((ifnull(A.po_mrp,0)-ifnull(A.po_unit_rate_excl_tax,0)-ifnull((ifnull(A.po_mrp,0)-(ifnull(A.po_mrp,0)/(1+(ifnull(A.po_vat_percen,0)/100)))),0))/ifnull(A.po_mrp,0)*100,2) 
                                end 
                            else 0 
                        end as margin_from_po 
                (select A.vat_cst as tx_zn_cd, B.*, D.tax_zone_code, D.tax_zone_name, E.invoice_date, A.gi_date, 
                    date_add(A.gi_date, interval ifnull(min_no_of_months_shelf_life_required,0) month) as earliest_expected_date, 
                    null as cost_acc_id, null as cost_ledger_name, null as cost_ledger_code, 
                    null as tax_acc_id, null as tax_ledger_name, null as tax_ledger_code, 
                    null as cgst_acc_id, null as cgst_ledger_name, null as cgst_ledger_code, 
                    null as sgst_acc_id, null as sgst_ledger_name, null as sgst_ledger_code, 
                    null as igst_acc_id, null as igst_ledger_name, null as igst_ledger_code, 
                    F.purchase_order_id, G.mrp as po_mrp, G.vat_tax_percentage as po_vat_percen, G.quantity as po_quantity, 
                    G.cost_price_exc_tax as po_unit_rate_excl_tax, G.unit_tax_amount as po_unit_tax, 
                    G.cost_price_inc_tax as po_unit_rate_incl_tax 
                from grn A 
                left join grn_entries B on (A.grn_id = B.grn_id) 
                left join tax_zone_master D on (A.vat_cst = D.tax_zone_code) 
                left join goods_inward_outward_invoices E on (A.gi_id = E.gi_go_ref_no and B.invoice_no = E.invoice_no) 
                left join purchase_order F on (A.po_no = F.po_no and A.vendor_id = F.vendor_id) 
                left join purchase_order_items G on (F.purchase_order_id = G.purchase_order_id and B.psku = G.psku) 
                where A.grn_id = '$grn_id' and B.grn_id = '$grn_id' and B.psku = '$psku' and A.is_active = '1' and A.company_id = '$company_id' and 
                        B.is_active = '1' and D.is_active = '1' and F.is_active = '1') A 
                left join 
                (select distinct sku_internal_code, margin_understanding from product_master where is_active = '1' and company_id = '$company_id') B 
                on (A.psku = B.sku_internal_code)) A) AA 
                
                left join 

                (select id, tax_zone_code, tax_zone_name, parent_id, tax_rate, 
                    max(case when child_tax_type_code = 'CGST' then child_tax_rate else 0 end) as cgst_rate, 
                    max(case when child_tax_type_code = 'SGST' then child_tax_rate else 0 end) as sgst_rate, 
                    max(case when child_tax_type_code = 'IGST' then child_tax_rate else 0 end) as igst_rate from 
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
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select A.invoice_no, A.invoice_date, B.grn_id, B.vendor_id, C.edited_val as total_deduction, C.gi_date 
                from goods_inward_outward_invoices A 
                left join grn B on (A.gi_go_ref_no = B.gi_id) left join acc_grn_entries C 
                on (B.grn_id = C.grn_id and A.invoice_no = C.invoice_no) 
                where A.gi_go_invoice_id = '$invoice_id' and B.status = 'approved' and B.is_active = '1' and B.company_id = '$company_id' and 
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
            $total_cgst = 0;
            $total_sgst = 0;
            $total_igst = 0;
            $total_tax = 0;
            $ded_type = '';

            $sql = "select * from acc_grn_sku_entries where status = 'approved' and is_active = '1' and company_id = '$company_id' and 
                    grn_id = '$grn_id' and invoice_no = '".str_replace('\\','\\\\',$invoice_no)."' order by ded_type";
            $command = Yii::$app->db->createCommand($sql);
            $reader = $command->query();
            $deduction_details = $reader->readAll();

            $sql = "select C.*, D.contact_name, D.contact_email, D.contact_phone, D.contact_mobile, D.contact_fax from 
                    (select A.*, B.* from 
                    (select AA.*, BB.legal_entity_name from vendor_master AA left join legal_entity_type_master BB 
                        on (AA.legal_entity_type_id = BB.id) where AA.id = '$vendor_id' and AA.company_id = '$company_id' and BB.is_active = '1') A 
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

            $sql = "select sum(A.qty) as total_qty, sum(A.cost_excl_tax) as total_without_tax, sum(A.total) as total_deduction, 
                        sum(A.cgst) as total_cgst, sum(A.sgst) as total_sgst, sum(A.igst) as total_igst, sum(A.tax) as total_tax, 
                        group_concat(distinct(CONCAT(UCASE(SUBSTRING(A.ded_type, 1, 1)),LOWER(SUBSTRING(A.ded_type, 2))))) as ded_type from 
                    (select qty, case when ded_type='margindiff' then margin_diff_excl_tax else cost_excl_vat end as cost_excl_tax, 
                        case when ded_type='margindiff' then margin_diff_total else total end as total, 
                        case when ded_type='margindiff' then margin_diff_cgst else cgst end as cgst, 
                        case when ded_type='margindiff' then margin_diff_sgst else sgst end as sgst, 
                        case when ded_type='margindiff' then margin_diff_igst else igst end as igst, 
                        case when ded_type='margindiff' then margin_diff_tax else tax end as tax, 
                        case when ded_type='margindiff' then 'margin difference' else ded_type end as ded_type from 
                        acc_grn_sku_entries where status = 'approved' and is_active = '1' and company_id = '$company_id' and 
                        grn_id = '$grn_id' and invoice_no = '".str_replace('\\','\\\\',$invoice_no)."' order by ded_type) A order by A.ded_type";
            $command = Yii::$app->db->createCommand($sql);
            $reader = $command->query();
            $deductions = $reader->readAll();
            if(count($deductions)>0){
                $total_qty = $deductions[0]['total_qty'];
                $total_without_tax = $deductions[0]['total_without_tax'];
                $total_cgst = $deductions[0]['total_cgst'];
                $total_sgst = $deductions[0]['total_sgst'];
                $total_igst = $deductions[0]['total_igst'];
                $total_tax = $deductions[0]['total_tax'];
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
                'total_without_tax'=>$total_without_tax,
                'total_cgst'=>$total_cgst,
                'total_sgst'=>$total_sgst,
                'total_igst'=>$total_igst,
                'total_tax'=>$total_tax,
                'total_deduction'=>$total_deduction,
                'status'=>'approved',
                'is_active'=>'1',
                'updated_by'=>$session['session_id'],
                'updated_date'=>date('Y-m-d h:i:s'),
                'company_id'=>$company_id
            ];

            $tableName = "acc_grn_debit_notes";
            $debit_note_ref = "";

            $sql = "select * from acc_grn_debit_notes where status = 'approved' and is_active = '1' and company_id = '$company_id' and 
                    grn_id = '$grn_id' and invoice_no = '".str_replace('\\','\\\\',$invoice_no)."'";
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
                $debit_note_ref = $debit_note[0]['debit_note_ref'];
                $count = Yii::$app->db->createCommand()
                            ->update($tableName, $array, "id = '".$ref_id."'")
                            ->execute();
            }

            // $mycomponent = Yii::$app->mycomponent;
            // $financial_year = $mycomponent->get_financial_year($invoice_date);
            // $debit_note_ref = $financial_year . "/" . $ref_id;

            $sql = "select A.*, B.warehouse_name, B.gst_id, B.address_line_1, B.address_line_2, B.address_line_3, 
                        B.city_id, B.state_id, B.pincode, C.city_name, D.state_name from grn A 
                    left join internal_warehouse_master B on (A.warehouse_id = B.warehouse_code and A.company_id = B.company_id) 
                    left join city_master C on (B.city_id = C.id) 
                    left join state_master D on (B.state_id = D.id) 
                    where A.grn_id = '$grn_id' and A.company_id = '$company_id'";
            $command = Yii::$app->db->createCommand($sql);
            $reader = $command->query();
            $grn_details = $reader->readAll();
            
            if(count($grn_details)>0){
                $warehouse_state = $grn_details[0]['state_name'];
            } else {
                $warehouse_state = '';
            }
            if($debit_note_ref==''){
                $debit_note_ref = $this->getDebitNoteRef($invoice_date, $warehouse_state);
            }
            
            $sql = "update acc_grn_debit_notes set debit_note_ref = '$debit_note_ref' where id = '$ref_id'";
            $command = Yii::$app->db->createCommand($sql);
            $count = $command->execute();

            $sql = "select * from acc_grn_debit_notes where id = '$ref_id'";
            $command = Yii::$app->db->createCommand($sql);
            $reader = $command->query();
            $debit_note = $reader->readAll();

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

            $sql = "select * from acc_grn_debit_notes where status = 'approved' and is_active = '1' and company_id = '$company_id' and 
                    grn_id = '$grn_id' and invoice_no = '".str_replace('\\','\\\\',$invoice_no)."'";
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
    
    public function getDebitNoteRef($date_of_transaction, $warehouse_state){
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        // $date_of_transaction = "2018-05-25";
        // $warehouse_state = 'Maharashtra';
        $code = '';

        $dateTime = \DateTime::createFromFormat('Y-m-d', $date_of_transaction);
        $from = $dateTime->format('Y');
        $to = $dateTime->format('Y');
        if (date('m') > 3) {
            $to = (int)($dateTime->format('Y')) +1;
        } else {
            $from = (int)($dateTime->format('Y')) -1;
        }
        $year = substr($from, 2) . substr($to, 2);

        $month = $dateTime->format('M');

        $state_code = '';
        $sql = "select * from state_master where state_name = '$warehouse_state'";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        $data = $reader->readAll();
        if (count($data)>0){
            $state_code = $data[0]['state_code'];
        }

        $code = $year . "/Debit Note/" . $month . "/" . $state_code;

        $sql = "select * from acc_series_master where type = 'debit_note' and company_id = '$company_id'";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        $data = $reader->readAll();
        if (count($data)>0){
            $series = intval($data[0]['series']) + 1;

            $sql = "update acc_series_master set series = '$series' where type = 'debit_note' and company_id = '$company_id'";
            $command = Yii::$app->db->createCommand($sql);
            $count = $command->execute();
        } else {
            $series = 1;

            $sql = "insert into acc_series_master (type, series, company_id) values ('debit_note', '".$series."', '".$company_id."')";
            $command = Yii::$app->db->createCommand($sql);
            $count = $command->execute();
        }

        $code = $code . "/" . str_pad($series, 3, "0", STR_PAD_LEFT);

        // echo $code;
        return $code;
    }

    public function setLog($module_name, $sub_module, $action, $vendor_id, $description, $table_name, $table_id) {
        $session = Yii::$app->session;
        $curusr = $session['session_id'];
        $now = date('Y-m-d H:i:s');
        $company_id = $session['company_id'];

        $array = array('module_name' => $module_name, 
                        'sub_module' => $sub_module, 
                        'action' => $action, 
                        'vendor_id' => $vendor_id, 
                        'user_id' => $curusr, 
                        'description' => $description, 
                        'log_activity_date' => $now, 
                        'table_name' => $table_name, 
                        'table_id' => $table_id,
                        'company_id' => $company_id);
        $count = Yii::$app->db->createCommand()
                            ->insert("acc_user_log", $array)
                            ->execute();

        return true;
    }

    public function setEmailLog($vendor_name, $from_email_id, $to_recipient, $reference_number, $email_content, 
                                $email_attachment, $attachment_type, $email_sent_status, $error_message, $company_id)
    {
        $session = Yii::$app->session;
        $curusr = $session['session_id'];
        $username = $session['username'];
        $now = date('Y-m-d H:i:s');
        $company_id = $session['company_id'];

        $array = array('module' => 'DN', 
                        'email_type' => 'Debit Note Email', 
                        'warehouse_code' => '', 
                        'vendor_name' => $vendor_name, 
                        'from_email_id' => $from_email_id, 
                        'to_recipient' => $to_recipient, 
                        'cc_recipient' => '', 
                        'supporting_user' => $username, 
                        'reference_number' => $reference_number, 
                        'reference_status' => 'approved', 
                        'email_date' => $now, 
                        'email_content' => $email_content, 
                        'email_attachment' => $email_attachment, 
                        'attachment_type' => $attachment_type, 
                        'email_sent_status' => $email_sent_status, 
                        'error_message' => $error_message, 
                        'company_id' => $company_id);
        $count = Yii::$app->db->createCommand()
                            ->insert("acc_email_log", $array)
                            ->execute();

        return true;
    }
}