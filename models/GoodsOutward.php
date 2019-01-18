<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\Session;
use mPDF;

class Goodsoutward extends Model
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
    
    public function getTaxPercent(){
        $session = Yii::$app->session;

        $sql = "select distinct tax_rate from tax_rate_master where is_active = '1' and entity_type = 'child' order by tax_rate";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getNewGoDetails() {
        $request = Yii::$app->request;
        $len= "";
        if($request->post('start')!="" && $request->post('length')!='-1') {
            $mycomponent = Yii::$app->mycomponent;   
            $start = $request->post('start');
            $length = $request->post('length'); 
            $len = " limit ".$start.", ".$length;  
        }
        $wheresearch = '';
        if($request->post('search')!=null) {
           $search_value1 = $request->post('search');
           $search = $search_value1['value'];
            if($search!="") {
                $wheresearch = " where (HH.gi_go_id like '%$search%' or HH.gi_go_ref_no like '%$search%' or 
                                HH.warehouse_name like '%$search%' or HH.vendor_name like '%$search%' or 
                                HH.idt_warehouse_name like '%$search%' or 
                                HH.gi_go_final_commit_date like '%$search%' or HH.updated_by like '%$search%' or 
                                HH.updated_date like '%$search%' or HH.username like '%$search%') ";
            } 
        }

        $session = Yii::$app->session;
        $company_id = $session['company_id'];

<<<<<<< HEAD
        $sql = "select II.* from 
                (select HH.* from 
                (select GG.gi_go_id, GG.gi_go_ref_no, GG.warehouse_name, GG.vendor_name, GG.idt_warehouse_name, GG.gi_go_final_commit_date, GG.updated_by, GG.updated_date, GG.username, sum(GG.total_amount) as total_amount from 
                (select FF.*, ifnull(ifnull(round(FF.value_at_cost*FF.vat_percent/100,2),0)+FF.value_at_cost,0) as total_amount from 
                (select EE.*, ifnull(EE.invoice_qty,0)*ifnull(EE.cost,0) as value_at_cost from 
                (select CC.*, ifnull(CC.quantity,0)-ifnull(DD.qty,0) as invoice_qty from 
                (select AA.*, BB.grn_id from 
                (select A.gi_go_id, A.gi_go_ref_no, A.warehouse_name, A.vendor_name, A.idt_warehouse_name, A.gi_go_final_commit_date, A.updated_by, A.updated_date, B.psku, B.quantity, B.cost, B.vat_percent, B.grn_no, C.username 
                from goods_inward_outward A 
                left join prepare_go_items B on (A.pre_go_ref=B.prepare_go_id) 
                left join user C on(A.updated_by = C.id) 
                where A.is_active = '1' and A.company_id='$company_id' and B.is_active = '1' and B.company_id='$company_id' and 
                    A.inward_outward = 'outward' and date(A.gi_go_final_commit_date) > date('2017-07-01') and 
                    A.type_outward = 'VENDOR' and A.gi_go_status = 'COMPLETE') AA 
                left join 
                (select distinct grn_id, gi_id from grn where status = 'approved' and is_active = '1' and 
                    company_id = '$company_id' and (date(gi_date)<date('2018-03-31') or gi_type <> 'VENDOR')
                union 
                select distinct A.grn_id, B.gi_id from acc_grn_entries A left join grn B on (A.grn_id=B.grn_id) 
                where A.status = 'approved' and A.is_active = '1' and A.company_id = '$company_id' and 
                    B.status = 'approved' and B.is_active = '1' and B.company_id = '$company_id') BB 
                on (AA.grn_no=BB.gi_id) where BB.grn_id is not null) CC 
=======
        $sql = "select * from 
                (select A.*, B.gi_go_id as b_gi_go_id from 
                (select A.* from goods_inward_outward A where A.is_active = '1' and A.company_id='$company_id' and 
                    A.inward_outward = 'outward' and date(A.gi_go_date_time) > date('2017-07-01')) A 
>>>>>>> 40251667d20641f61579b49c4e0131e7351baf6f
                left join 
                (select grn_id, psku, qty, ded_type from acc_grn_sku_entries where status='approved' and is_active='1' and company_id='$company_id'and ded_type in ('expiry', 'damaged')) DD 
                on (CC.grn_id=DD.grn_id and CC.psku=DD.psku)) EE 
                where EE.invoice_qty>0) FF) GG 
                group by GG.gi_go_id, GG.gi_go_ref_no, GG.warehouse_name, GG.vendor_name, GG.idt_warehouse_name, GG.gi_go_final_commit_date, GG.updated_by, GG.updated_date, GG.username) HH ".$wheresearch.") II 
                left join 
                (select distinct gi_go_id from acc_go_debit_entries where status = 'approved' and is_active = '1' and company_id = '$company_id') JJ 
                on (II.gi_go_id=JJ.gi_go_id) 
                where JJ.gi_go_id is null 
                order by UNIX_TIMESTAMP(II.updated_date) desc ".$len;
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getCountGoDetails(){
        $request = Yii::$app->request;
        $wheresearch = '';
        if($request->post('search')!=null) {
           $search_value1 =  $request->post('search');
           $search = $search_value1['value'];
            if($search!="") {
                $wheresearch = " where (HH.gi_go_id like '%$search%' or HH.gi_go_ref_no like '%$search%' or 
                                HH.warehouse_name like '%$search%' or HH.vendor_name like '%$search%' or 
                                HH.idt_warehouse_name like '%$search%' or 
                                HH.gi_go_final_commit_date like '%$search%' or HH.updated_by like '%$search%' or 
                                HH.updated_date like '%$search%' or HH.username like '%$search%') ";
            } 
        }

        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select count(*) as count from 
<<<<<<< HEAD
                (select II.* from 
                (select HH.* from 
                (select GG.gi_go_id, GG.gi_go_ref_no, GG.warehouse_name, GG.vendor_name, GG.idt_warehouse_name, GG.gi_go_final_commit_date, GG.updated_by, GG.updated_date, GG.username, sum(GG.total_amount) as total_amount from 
                (select FF.*, ifnull(ifnull(round(FF.value_at_cost*FF.vat_percent/100,2),0)+FF.value_at_cost,0) as total_amount from 
                (select EE.*, ifnull(EE.invoice_qty,0)*ifnull(EE.cost,0) as value_at_cost from 
                (select CC.*, ifnull(CC.quantity,0)-ifnull(DD.qty,0) as invoice_qty from 
                (select AA.*, BB.grn_id from 
                (select A.gi_go_id, A.gi_go_ref_no, A.warehouse_name, A.vendor_name, A.idt_warehouse_name, A.gi_go_final_commit_date, 
                    A.updated_by, A.updated_date, B.psku, B.quantity, B.cost, B.vat_percent, B.grn_no, C.username 
                from goods_inward_outward A 
                left join prepare_go_items B on (A.pre_go_ref=B.prepare_go_id) 
                left join user C on(A.updated_by = C.id) 
                where A.is_active = '1' and A.company_id='$company_id' and B.is_active = '1' and B.company_id='$company_id' and 
                    A.inward_outward = 'outward' and date(A.gi_go_final_commit_date) > date('2017-07-01') and 
                    A.type_outward = 'VENDOR' and A.gi_go_status = 'COMPLETE') AA 
                left join 
                (select distinct grn_id, gi_id from grn where status = 'approved' and is_active = '1' and 
                    company_id = '$company_id' and (date(gi_date)<date('2018-03-31') or gi_type <> 'VENDOR') 
                union 
                select distinct A.grn_id, B.gi_id from acc_grn_entries A left join grn B on (A.grn_id=B.grn_id) 
                where A.status = 'approved' and A.is_active = '1' and A.company_id = '$company_id' and 
                    B.status = 'approved' and B.is_active = '1' and B.company_id = '$company_id') BB 
                on (AA.grn_no=BB.gi_id) where BB.grn_id is not null) CC 
                left join 
                (select grn_id, psku, qty, ded_type from acc_grn_sku_entries where status='approved' and is_active='1' and company_id='$company_id'and ded_type in ('expiry', 'damaged')) DD 
                on (CC.grn_id=DD.grn_id and CC.psku=DD.psku)) EE 
                where EE.invoice_qty>0) FF) GG 
                group by GG.gi_go_id, GG.gi_go_ref_no, GG.warehouse_name, GG.vendor_name, GG.idt_warehouse_name, GG.gi_go_final_commit_date, GG.updated_by, GG.updated_date, GG.username) HH ".$wheresearch.") II 
=======
                (select A.*, B.gi_go_id as b_gi_go_id from 
                (select A.* from goods_inward_outward A where is_active = '1' and A.company_id='$company_id' and 
                    A.inward_outward = 'outward' and date(A.gi_go_date_time) > date('2017-07-01')) A 
>>>>>>> 40251667d20641f61579b49c4e0131e7351baf6f
                left join 
                (select distinct gi_go_id from acc_go_debit_entries where status = 'approved' and is_active = '1' and company_id = '$company_id') JJ 
                on (II.gi_go_id=JJ.gi_go_id) 
                where JJ.gi_go_id is null) KK";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        $result =  $reader->readAll();
        return $result[0]['count'];
    }

    public function getPostedGoDebitDetails($status=""){
        $request = Yii::$app->request;
        $len= "";
        if($request->post('start')!="" && $request->post('length')!='-1') {
            $mycomponent = Yii::$app->mycomponent;   
            $start = $request->post('start');
            $length = $request->post('length'); 
            $len = "LIMIT ".$start.", ".$length;  
        } 

        $wheresearch = '';
        if($request->post('search')!=null) {
           $search_value1 =  $request->post('search');
           $search = $search_value1['value'];
            if($search!="") {
                $wheresearch = " where (HH.gi_go_id like '%$search%' or HH.gi_go_ref_no like '%$search%' or 
                                HH.warehouse_name like '%$search%' or HH.vendor_name like '%$search%' or 
                                HH.idt_warehouse_name like '%$search%' or 
                                HH.gi_go_final_commit_date like '%$search%' or HH.updated_by like '%$search%' or 
                                HH.updated_date like '%$search%' or HH.username like '%$search%') ";
            } 
        }

        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select KK.*, LL.is_paid from 
                (select II.* from 
                (select HH.* from 
                (select GG.gi_go_id, GG.gi_go_ref_no, GG.warehouse_name, GG.vendor_name, GG.idt_warehouse_name, GG.gi_go_final_commit_date, GG.updated_by, GG.updated_date, GG.username, sum(GG.total_amount) as total_amount from 
                (select FF.*, ifnull(ifnull(round(FF.value_at_cost*FF.vat_percent/100,2),0)+FF.value_at_cost,0) as total_amount from 
                (select EE.*, ifnull(EE.invoice_qty,0)*ifnull(EE.cost,0) as value_at_cost from 
                (select CC.*, ifnull(CC.quantity,0)-ifnull(DD.qty,0) as invoice_qty from 
                (select AA.*, BB.grn_id from 
                (select A.gi_go_id, A.gi_go_ref_no, A.warehouse_name, A.vendor_name, A.idt_warehouse_name, A.gi_go_final_commit_date, A.updated_by, A.updated_date, B.psku, B.quantity, B.cost, B.vat_percent, B.grn_no, C.username 
                from goods_inward_outward A 
                left join prepare_go_items B on (A.pre_go_ref=B.prepare_go_id) 
                left join user C on(A.updated_by = C.id) 
                where A.is_active = '1' and A.company_id='$company_id' and B.is_active = '1' and B.company_id='$company_id' and 
                    A.inward_outward = 'outward' and date(A.gi_go_final_commit_date) > date('2017-07-01') and 
                    A.type_outward = 'VENDOR' and A.gi_go_status = 'COMPLETE') AA 
                left join 
                (select distinct grn_id, gi_id from grn where status = 'approved' and is_active = '1' and 
                    company_id = '$company_id' and (date(gi_date)<date('2018-03-31') or gi_type <> 'VENDOR') 
                union 
                select distinct A.grn_id, B.gi_id from acc_grn_entries A left join grn B on (A.grn_id=B.grn_id) 
                where A.status = 'approved' and A.is_active = '1' and A.company_id = '$company_id' and 
                    B.status = 'approved' and B.is_active = '1' and B.company_id = '$company_id') BB 
                on (AA.grn_no=BB.gi_id) where BB.grn_id is not null) CC 
                left join 
                (select grn_id, psku, qty, ded_type from acc_grn_sku_entries where status='approved' and is_active='1' and company_id='$company_id'and ded_type in ('expiry', 'damaged')) DD 
                on (CC.grn_id=DD.grn_id and CC.psku=DD.psku)) EE 
                where EE.invoice_qty>0) FF) GG 
                group by GG.gi_go_id, GG.gi_go_ref_no, GG.warehouse_name, GG.vendor_name, GG.idt_warehouse_name, GG.gi_go_final_commit_date, GG.updated_by, GG.updated_date, GG.username) HH ".$wheresearch.") II 
                left join 
                (select distinct gi_go_id from acc_go_debit_entries where status = 'approved' and is_active = '1' and company_id = '$company_id') JJ 
                on (II.gi_go_id=JJ.gi_go_id) 
                where JJ.gi_go_id is not null) KK 
                left join 
                (select distinct ref_id, is_paid from acc_ledger_entries 
                    where ref_type = 'go_debit_details' and company_id='$company_id' and is_active = '1' and status = 'Approved' and is_paid = '1') LL 
                on (KK.gi_go_id = LL.ref_id) 
                order by UNIX_TIMESTAMP(KK.updated_date) desc ".$len;
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getCountPostedGoDebitDetails($status=""){
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
                $wheresearch = " where (HH.gi_go_id like '%$search%' or HH.gi_go_ref_no like '%$search%' or 
                                HH.warehouse_name like '%$search%' or HH.vendor_name like '%$search%' or 
                                HH.idt_warehouse_name like '%$search%' or 
                                HH.gi_go_final_commit_date like '%$search%' or HH.updated_by like '%$search%' or 
                                HH.updated_date like '%$search%' or HH.username like '%$search%') ";
            } 
        }

        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select count(*) as count from 
                (select KK.*, LL.is_paid from 
                (select II.* from 
                (select HH.* from 
                (select GG.gi_go_id, GG.gi_go_ref_no, GG.warehouse_name, GG.vendor_name, GG.idt_warehouse_name, GG.gi_go_final_commit_date, GG.updated_by, GG.updated_date, GG.username, sum(GG.total_amount) as total_amount from 
                (select FF.*, ifnull(ifnull(round(FF.value_at_cost*FF.vat_percent/100,2),0)+FF.value_at_cost,0) as total_amount from 
                (select EE.*, ifnull(EE.invoice_qty,0)*ifnull(EE.cost,0) as value_at_cost from 
                (select CC.*, ifnull(CC.quantity,0)-ifnull(DD.qty,0) as invoice_qty from 
                (select AA.*, BB.grn_id from 
                (select A.gi_go_id, A.gi_go_ref_no, A.warehouse_name, A.vendor_name, A.idt_warehouse_name, A.gi_go_final_commit_date, A.updated_by, A.updated_date, B.psku, B.quantity, B.cost, B.vat_percent, B.grn_no, C.username 
                from goods_inward_outward A 
                left join prepare_go_items B on (A.pre_go_ref=B.prepare_go_id) 
                left join user C on(A.updated_by = C.id) 
                where A.is_active = '1' and A.company_id='$company_id' and B.is_active = '1' and B.company_id='$company_id' and 
                    A.inward_outward = 'outward' and date(A.gi_go_final_commit_date) > date('2017-07-01') and 
                    A.type_outward = 'VENDOR' and A.gi_go_status = 'COMPLETE') AA 
                left join 
                (select distinct grn_id, gi_id from grn where status = 'approved' and is_active = '1' and 
                    company_id = '$company_id' and (date(gi_date)<date('2018-03-31') or gi_type <> 'VENDOR') 
                union 
                select distinct A.grn_id, B.gi_id from acc_grn_entries A left join grn B on (A.grn_id=B.grn_id) 
                where A.status = 'approved' and A.is_active = '1' and A.company_id = '$company_id' and 
                    B.status = 'approved' and B.is_active = '1' and B.company_id = '$company_id') BB 
                on (AA.grn_no=BB.gi_id) where BB.grn_id is not null) CC 
                left join 
                (select grn_id, psku, qty, ded_type from acc_grn_sku_entries where status='approved' and is_active='1' and company_id='$company_id'and ded_type in ('expiry', 'damaged')) DD 
                on (CC.grn_id=DD.grn_id and CC.psku=DD.psku)) EE 
                where EE.invoice_qty>0) FF) GG 
                group by GG.gi_go_id, GG.gi_go_ref_no, GG.warehouse_name, GG.vendor_name, GG.idt_warehouse_name, GG.gi_go_final_commit_date, GG.updated_by, GG.updated_date, GG.username) HH ".$wheresearch.") II 
                left join 
                (select distinct gi_go_id from acc_go_debit_entries where status = 'approved' and is_active = '1' and company_id = '$company_id') JJ 
                on (II.gi_go_id=JJ.gi_go_id) 
                where JJ.gi_go_id is not null) KK 
                left join 
                (select distinct ref_id, is_paid from acc_ledger_entries 
                    where ref_type = 'go_debit_details' and company_id='$company_id' and is_active = '1' and status = 'Approved' and is_paid = '1') LL 
                on (KK.gi_go_id = LL.ref_id)) MM";
                
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        $result =  $reader->readAll();
        return $result[0]['count'];
    }

    public function getAllGoDetails(){
        $request = Yii::$app->request;
        $len= "";
        if($request->post('start')!="" && $request->post('length')!='-1') {
            $mycomponent = Yii::$app->mycomponent;   
            $start = $request->post('start');
            $length = $request->post('length'); 
            $len = "LIMIT ".$start.", ".$length;  
        } 

        $wheresearch = '';
        if($request->post('search')!=null) {
           $search_value1 =  $request->post('search');
           $search = $search_value1['value'];
            if($search!="") {
                $wheresearch = " where (HH.gi_go_id like '%$search%' or HH.gi_go_ref_no like '%$search%' or 
                                HH.warehouse_name like '%$search%' or HH.vendor_name like '%$search%' or 
                                HH.idt_warehouse_name like '%$search%' or 
                                HH.gi_go_final_commit_date like '%$search%' or HH.updated_by like '%$search%' or 
                                HH.updated_date like '%$search%' or HH.username like '%$search%') ";
            } 
        }

        $session = Yii::$app->session;
        $company_id = $session['company_id'];

<<<<<<< HEAD
        $sql = "select KK.*, LL.is_paid, case when KK.posted_gi_go_id is not null then 'GO Posted' 
                    when KK.grn_id is not null then 'GRN Posted & GO Balance' else 'GRN Not Posted' end as go_status from 
                (select II.*, JJ.gi_go_id as posted_gi_go_id from 
                (select HH.* from 
                (select GG.gi_go_id, GG.gi_go_ref_no, GG.warehouse_name, GG.vendor_name, GG.idt_warehouse_name, GG.gi_go_final_commit_date, GG.updated_by, GG.updated_date, GG.username, GG.grn_id, GG.grn_no, sum(GG.total_amount) as total_amount from 
                (select FF.*, ifnull(ifnull(round(FF.value_at_cost*FF.vat_percent/100,2),0)+FF.value_at_cost,0) as total_amount from 
                (select EE.*, ifnull(EE.invoice_qty,0)*ifnull(EE.cost,0) as value_at_cost from 
                (select CC.*, ifnull(CC.quantity,0)-ifnull(DD.qty,0) as invoice_qty from 
                (select AA.*, BB.grn_id from 
                (select A.gi_go_id, A.gi_go_ref_no, A.warehouse_name, A.vendor_name, A.idt_warehouse_name, A.gi_go_final_commit_date, A.updated_by, A.updated_date, B.psku, B.quantity, B.cost, B.vat_percent, B.grn_no, C.username 
                from goods_inward_outward A 
                left join prepare_go_items B on (A.pre_go_ref=B.prepare_go_id) 
                left join user C on(A.updated_by = C.id) 
                where A.is_active = '1' and A.company_id='$company_id' and B.is_active = '1' and B.company_id='$company_id' and 
                    A.inward_outward = 'outward' and date(A.gi_go_final_commit_date) > date('2017-07-01') and 
                    A.type_outward = 'VENDOR' and A.gi_go_status = 'COMPLETE') AA 
=======
        $sql = "select C.*, D.is_paid from 
                (select A.*, B.gi_go_id as b_gi_go_id, B.status as go_debit_status from 
                (select A.* from goods_inward_outward A where is_active = '1' and A.company_id='$company_id' and 
                    A.inward_outward = 'outward' and date(A.gi_go_date_time) > date('2017-07-01')) A 
>>>>>>> 40251667d20641f61579b49c4e0131e7351baf6f
                left join 
                (select distinct grn_id, gi_id from grn where status = 'approved' and is_active = '1' and 
                    company_id = '$company_id' and (date(gi_date)<date('2018-03-31') or gi_type <> 'VENDOR') 
                union 
                select distinct A.grn_id, B.gi_id from acc_grn_entries A left join grn B on (A.grn_id=B.grn_id) 
                where A.status = 'approved' and A.is_active = '1' and A.company_id = '$company_id' and 
                    B.status = 'approved' and B.is_active = '1' and B.company_id = '$company_id') BB 
                on (AA.grn_no=BB.gi_id)) CC 
                left join 
                (select grn_id, psku, qty, ded_type from acc_grn_sku_entries where status='approved' and is_active='1' and company_id='$company_id'and ded_type in ('expiry', 'damaged')) DD 
                on (CC.grn_id=DD.grn_id and CC.psku=DD.psku)) EE 
                where EE.invoice_qty>0) FF) GG 
                group by GG.gi_go_id, GG.gi_go_ref_no, GG.warehouse_name, GG.vendor_name, GG.idt_warehouse_name, GG.gi_go_final_commit_date, GG.updated_by, GG.updated_date, GG.username, GG.grn_id, GG.grn_no) HH ".$wheresearch.") II 
                left join 
                (select distinct gi_go_id from acc_go_debit_entries where status = 'approved' and is_active = '1' and company_id = '$company_id') JJ 
                on (II.gi_go_id=JJ.gi_go_id)) KK 
                left join 
                (select distinct ref_id, is_paid from acc_ledger_entries 
                    where ref_type = 'go_debit_details' and company_id='$company_id' and is_active = '1' and status = 'Approved' and is_paid = '1') LL 
                on (KK.gi_go_id = LL.ref_id) 
                order by UNIX_TIMESTAMP(KK.updated_date) desc ".$len;
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getCountAllGoDetails(){
        $request = Yii::$app->request;
        
        $wheresearch = '';
        if($request->post('search')!=null) {
           $search_value1 =  $request->post('search');
           $search = $search_value1['value'];
            if($search!="") {
                $wheresearch = " where (HH.gi_go_id like '%$search%' or HH.gi_go_ref_no like '%$search%' or 
                                HH.warehouse_name like '%$search%' or HH.vendor_name like '%$search%' or 
                                HH.idt_warehouse_name like '%$search%' or 
                                HH.gi_go_final_commit_date like '%$search%' or HH.updated_by like '%$search%' or 
                                HH.updated_date like '%$search%' or HH.username like '%$search%') ";
            } 
        }

        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select count(*) as count from 
<<<<<<< HEAD
                (select KK.*, LL.is_paid from 
                (select II.* from 
                (select HH.* from 
                (select GG.gi_go_id, GG.gi_go_ref_no, GG.warehouse_name, GG.vendor_name, GG.idt_warehouse_name, GG.gi_go_final_commit_date, GG.updated_by, GG.updated_date, GG.username, sum(GG.total_amount) as total_amount from 
                (select FF.*, ifnull(ifnull(round(FF.value_at_cost*FF.vat_percent/100,2),0)+FF.value_at_cost,0) as total_amount from 
                (select EE.*, ifnull(EE.invoice_qty,0)*ifnull(EE.cost,0) as value_at_cost from 
                (select CC.*, ifnull(CC.quantity,0)-ifnull(DD.qty,0) as invoice_qty from 
                (select AA.*, BB.grn_id from 
                (select A.gi_go_id, A.gi_go_ref_no, A.warehouse_name, A.vendor_name, A.idt_warehouse_name, A.gi_go_final_commit_date, 
                    A.updated_by, A.updated_date, B.psku, B.quantity, B.cost, B.vat_percent, B.grn_no, C.username 
                from goods_inward_outward A 
                left join prepare_go_items B on (A.pre_go_ref=B.prepare_go_id) 
                left join user C on(A.updated_by = C.id) 
                where A.is_active = '1' and A.company_id='$company_id' and B.is_active = '1' and B.company_id='$company_id' and 
                    A.inward_outward = 'outward' and date(A.gi_go_final_commit_date) > date('2017-07-01') and 
                    A.type_outward = 'VENDOR' and A.gi_go_status = 'COMPLETE') AA 
                left join 
                (select distinct grn_id, gi_id from grn where status = 'approved' and is_active = '1' and 
                    company_id = '$company_id' and (date(gi_date)<date('2018-03-31') or gi_type <> 'VENDOR') 
                union 
                select distinct A.grn_id, B.gi_id from acc_grn_entries A left join grn B on (A.grn_id=B.grn_id) 
                where A.status = 'approved' and A.is_active = '1' and A.company_id = '$company_id' and 
                    B.status = 'approved' and B.is_active = '1' and B.company_id = '$company_id') BB 
                on (AA.grn_no=BB.gi_id)) CC 
=======
                (select A.*, B.gi_go_id as b_gi_go_id, B.status as go_debit_status from 
                (select A.* from goods_inward_outward A where is_active = '1' and A.company_id='$company_id' and 
                    A.inward_outward = 'outward' and date(A.gi_go_date_time) > date('2017-07-01')) A 
>>>>>>> 40251667d20641f61579b49c4e0131e7351baf6f
                left join 
                (select grn_id, psku, qty, ded_type from acc_grn_sku_entries where status='approved' and is_active='1' and company_id='$company_id'and ded_type in ('expiry', 'damaged')) DD 
                on (CC.grn_id=DD.grn_id and CC.psku=DD.psku)) EE 
                where EE.invoice_qty>0) FF) GG 
                group by GG.gi_go_id, GG.gi_go_ref_no, GG.warehouse_name, GG.vendor_name, GG.idt_warehouse_name, GG.gi_go_final_commit_date, GG.updated_by, GG.updated_date, GG.username) HH ".$wheresearch.") II 
                left join 
                (select distinct gi_go_id from acc_go_debit_entries where status = 'approved' and is_active = '1' and company_id = '$company_id') JJ 
                on (II.gi_go_id=JJ.gi_go_id)) KK 
                left join 
                (select distinct ref_id, is_paid from acc_ledger_entries 
                    where ref_type = 'go_debit_details' and company_id='$company_id' and is_active = '1' and status = 'Approved' and is_paid = '1') LL 
                on (KK.gi_go_id = LL.ref_id)) MM";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        $result =  $reader->readAll();
        return $result[0]['count'];
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

    public function getGoDetails($id){
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select * from goods_inward_outward A where A.gi_go_id = '$id'";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    // public function getPostedGoDetails($id=""){
    //     $session = Yii::$app->session;
    //     $company_id = $session['company_id'];

    //     $sql = "select C.*, D.is_paid from 
    //             (select A.*, B.gi_go_ref_no, B.warehouse_name, B.warehouse_state, B.vendor_id, B.vendor_name, B.idt_warehouse_name, 
    //                 B.total_quantity, B.type_outward, B.customerName, C.username
    //             from acc_go_debit_details A 
    //                 left join goods_inward_outward B on (A.gi_go_id=B.gi_go_id) 
    //                 left join user C on(A.updated_by = C.id) 
    //             where A.gi_go_id = '$id' and A.is_active = '1' and A.company_id='$company_id' and B.is_active = '1' and B.company_id='$company_id') C 
    //             left join 
    //             (select distinct ref_id, is_paid from acc_ledger_entries 
    //                 where ref_type = 'go_debit_details' and company_id='$company_id' and is_active = '1' and status = 'Approved' and is_paid = '1') D 
    //             on (C.gi_go_id = D.ref_id) 
    //             order by UNIX_TIMESTAMP(C.updated_date) desc";
    //     $command = Yii::$app->db->createCommand($sql);
    //     $reader = $command->query();
    //     return $reader->readAll();
    // }

    public function getPostedGoEntries($id=""){
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select * from acc_go_debit_entries where gi_go_id = '$id'";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function save(){
        $request = Yii::$app->request;

        $action = $request->post('action');
        if($action=="authorise"){
            if($request->post('btn_reject')!==null){
                $action = "reject";
            } else {
                $action = "approve";
            }
        }

        if($action=="edit" || $action=="insert"){
            $this->saveEdit();
        } else if($action=="approve"){
            $this->authorise("approved");
        } else if($action=="reject"){
            $this->authorise("rejected");
        }
        
        return true;
    }

    public function saveEdit(){
        $request = Yii::$app->request;
        $mycomponent = Yii::$app->mycomponent;
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $curusr = $session['session_id'];
        $now = date('Y-m-d H:i:s');

        $id = $request->post('id');
        $gi_go_id = $request->post('gi_go_id');
        $voucher_id = $request->post('voucher_id');
        $vendor_id = $request->post('vendor_id');
        $warehouse_state = $request->post('warehouse_state');
        $debit_note_ref = $request->post('debit_note_ref');
        $date_of_transaction = $request->post('date_of_transaction');
        if($date_of_transaction==''){
            $date_of_transaction=NULL;
        } else {
            $date_of_transaction=$mycomponent->formatdate($date_of_transaction);
        }
        $gi_go_ref_no = $request->post('gi_go_ref_no');
        $remarks = $request->post('remarks');
        // $approver_id = $request->post('approver_id');

        // $entry_id = $request->post('entry_id');
        $acc_id = $request->post('acc_id');
        $acc_type = $request->post('acc_type');
        $ledger_name = $request->post('ledger_name');
        $ledger_code = $request->post('ledger_code');
        $ledger_type = $request->post('ledger_type');
        $transaction = $request->post('transaction');
        $debit_amt = $request->post('debit_amt');
        $credit_amt = $request->post('credit_amt');
        $total_debit_amt = $request->post('total_debit_amt');
        $total_credit_amt = $request->post('total_credit_amt');
        $diff_amt = $request->post('diff_amt');
        // $reference = $request->post('reference');
        // $narration = $request->post('narration');

        $debit_acc = "";
        $credit_acc = "";
        for($i=0; $i<count($ledger_name); $i++){
            if($transaction[$i]=='Debit'){
                $debit_acc = $debit_acc . $ledger_name[$i] . ', ';
            } else {
                $credit_acc = $credit_acc . $ledger_name[$i] . ', ';
            }
        }
        if(strlen($debit_acc)>0){
            $debit_acc = substr($debit_acc, 0, strrpos($debit_acc, ', '));
        }
        if(strlen($credit_acc)>0){
            $credit_acc = substr($credit_acc, 0, strrpos($credit_acc, ', '));
        }

        if(!isset($voucher_id) || $voucher_id==''){
            $series = 1;
            $sql = "select * from acc_series_master where type = 'Voucher'";
            $command = Yii::$app->db->createCommand($sql);
            $reader = $command->query();
            $data = $reader->readAll();
            if (count($data)>0){
                $series = intval($data[0]['series']) + 1;

                $sql = "update acc_series_master set series = '$series' where type = 'Voucher'";
                $command = Yii::$app->db->createCommand($sql);
                $count = $command->execute();
            } else {
                $series = 1;

                $sql = "insert into acc_series_master (type, series) values ('Voucher', '".$series."')";
                $command = Yii::$app->db->createCommand($sql);
                $count = $command->execute();
            }

            $voucher_id = $series;
        }

        if(!isset($debit_note_ref) || $debit_note_ref==''){
            $debit_note_ref = $this->getDebitNoteRef($date_of_transaction, $warehouse_state);
        }
        
        $array = array('gi_go_id' => $gi_go_id, 
                        'voucher_id' => $voucher_id, 
                        // 'ledger_type' => $ledger_type, 
                        // 'reference' => $reference, 
                        // 'narration' => $narration, 
                        'debit_acc' => $debit_acc, 
                        'credit_acc' => $credit_acc, 
                        'debit_amt' => $mycomponent->format_number($total_debit_amt,2), 
                        'credit_amt' => $mycomponent->format_number($total_credit_amt,2), 
                        'diff_amt' => $mycomponent->format_number($diff_amt,2),
                        // 'status' => 'pending',
                        'status' => 'approved',
                        'is_active' => '1',
                        'updated_by'=>$curusr,
                        'updated_date'=>$now,
                        'date_of_transaction'=>$date_of_transaction,
                        'approver_comments'=>$remarks,
                        // 'approver_id'=>$approver_id,
                        'company_id'=>$company_id,
                        'debit_note_ref'=>$debit_note_ref
                        );

        if(count($array)>0){
            if (isset($id) && $id!=""){
                $count = Yii::$app->db->createCommand()
                            ->update("acc_go_debit_details", $array, "id = '".$id."'")
                            ->execute();

                $this->setLog('go_debit_details', '', 'Update', '', 'Update Goods Outward Debit Details', 'acc_go_debit_details', $id);
            } else {
                $array['created_by'] = $curusr;
                $array['created_date'] = $now;
                $count = Yii::$app->db->createCommand()
                            ->insert("acc_go_debit_details", $array)
                            ->execute();
                $id = Yii::$app->db->getLastInsertID();

                $this->setLog('go_debit_details', '', 'Save', '', 'Insert Goods Outward Debit Details', 'acc_go_debit_details', $id);
            }
        }



        $acc_go_debit_entries = array();

        $sql = "delete from acc_go_debit_entries where gi_go_id = '$gi_go_id'";
        Yii::$app->db->createCommand($sql)->execute();

        $sql = "delete from acc_ledger_entries where ref_id = '".$gi_go_id."' and ref_type = 'go_debit_details'";
        Yii::$app->db->createCommand($sql)->execute();

        for($i=0; $i<count($acc_id); $i++){
            $acc_go_debit_entries = array('gi_go_id' => $gi_go_id, 
                                    'acc_id' => $acc_id[$i], 
                                    'acc_type' => $acc_type[$i],
                                    'ledger_type' => $ledger_type[$i],  
                                    'ledger_name' => $ledger_name[$i], 
                                    'ledger_code' => $ledger_code[$i], 
                                    'transaction' => $transaction[$i], 
                                    'debit_amt' => $mycomponent->format_number($debit_amt[$i],2), 
                                    'credit_amt' => $mycomponent->format_number($credit_amt[$i],2),
                                    // 'status' => 'pending',
                                    'status' => 'approved',
                                    'is_active' => '1',
                                    'updated_by'=>$curusr,
                                    'updated_date'=>$now,
                                    'approver_comments'=>$remarks,
                                    'company_id'=>$company_id
                                );

            $acc_go_debit_entries['created_by'] = $curusr;
            $acc_go_debit_entries['created_date'] = $now;
            $count = Yii::$app->db->createCommand()
                        ->insert("acc_go_debit_entries", $acc_go_debit_entries)
                        ->execute();
            $entry_id[$i] = Yii::$app->db->getLastInsertID();

            // if (isset($entry_id[$i]) && $entry_id[$i]!=""){
            //     $count = Yii::$app->db->createCommand()
            //                 ->update("acc_go_debit_entries", $acc_go_debit_entries, "id = '".$entry_id[$i]."'")
            //                 ->execute();
            // } else {
            //     $acc_go_debit_entries['created_by'] = $curusr;
            //     $acc_go_debit_entries['created_date'] = $now;

            //     $count = Yii::$app->db->createCommand()
            //                 ->insert("acc_go_debit_entries", $acc_go_debit_entries)
            //                 ->execute();
            //     $entry_id[$i] = Yii::$app->db->getLastInsertID();
            // }

            if($transaction[$i]=="Debit"){
                $amount = $debit_amt[$i];
            } else {
                $amount = $credit_amt[$i];
            }

            $ledgerArray=[
                            'ref_id'=>$gi_go_id,
                            'sub_ref_id'=>$entry_id[$i],
                            'ref_type'=>'go_debit_details',
                            'entry_type'=>'go_debit_details',
                            'invoice_no'=>$gi_go_ref_no,
                            'vendor_id'=>$vendor_id,
                            'voucher_id' => $voucher_id, 
                            'ledger_type' => $ledger_type[$i], 
                            'acc_id'=>$acc_id[$i],
                            'ledger_name'=>$ledger_name[$i],
                            'ledger_code'=>$ledger_code[$i],
                            'type'=>$transaction[$i],
                            'amount'=>$mycomponent->format_number($amount,2),
                            // 'status'=>'pending',
                            'status'=>'approved',
                            'is_active'=>'1',
                            'updated_by'=>$curusr,
                            'updated_date'=>$now,
                            'ref_date'=>$date_of_transaction,
                            'approver_comments'=>$remarks,
                            'company_id'=>$company_id
                        ];

            $ledgerArray['created_by'] = $curusr;
            $ledgerArray['created_date'] = $now;
            $count = Yii::$app->db->createCommand()
                        ->insert("acc_ledger_entries", $ledgerArray)
                        ->execute();

            // $count = Yii::$app->db->createCommand()
            //             ->update("acc_ledger_entries", $ledgerArray, "ref_id = '".$gi_go_id."' and sub_ref_id = '".$entry_id[$i]."' and ref_type = 'go_debit_details'")
            //             ->execute();

            // if ($count==0){
            //     $ledgerArray['created_by'] = $curusr;
            //     $ledgerArray['created_date'] = $now;

            //     $count = Yii::$app->db->createCommand()
            //                 ->insert("acc_ledger_entries", $ledgerArray)
            //                 ->execute();
            // }
        }

        return true;
    }

    public function getGoParticulars(){
        $request = Yii::$app->request;
        $mycomponent = Yii::$app->mycomponent;
        $session = Yii::$app->session;

        $company_id = $session['company_id'];

        $warehouse_id = $request->post('warehouse_id');
        $gi_id = $request->post('gi_go_id');
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
        $edited_total_payable_amount = $request->post('edited_total_payable_amount');

        
        $num = 0;

        for($i=0; $i<count($invoice_no); $i++){
           

            for ($j=0; $j<count($vat_cst); $j++){
                $edited_cost_val = $mycomponent->format_number($edited_cost[$j][$i],2);
                if($edited_cost_val>0 || $invoice_cost[$j][$i]>0){
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
                'gi_go_id'=>$gi_id,
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
                'company_id'=>$company_id,
                'warehouse_no'=>($warehouse_id==''?null:$warehouse_id)
            ];

            $j = $j + 1;

            $ledg_particular = $particular[$i];
            if($mycomponent->format_number($edited_val[$i],2)!=0){
                if($ledg_particular=="Taxable Amount"){
                    $ledg_type = "Credit";
                    // $type = "Goods Purchase";
                    // $legal_name = $particular[$i];
                    // $code = $sub_particular_val[$i];
                } else if($ledg_particular=="Tax"){
                    $ledg_type = "Credit";
                    // $type = "Tax";
                    // $legal_name = $particular[$i];
                    // $code = $sub_particular_val[$i];
                } else if($ledg_particular=="CGST"){
                    $ledg_type = "Credit";
                    // $type = "CGST";
                    // $legal_name = $particular[$i];
                    // $code = $sub_particular_val[$i];
                } else if($ledg_particular=="SGST"){
                    $ledg_type = "Credit";
                    // $type = "SGST";
                    // $legal_name = $particular[$i];
                    // $code = $sub_particular_val[$i];
                } else if($ledg_particular=="IGST"){
                    $ledg_type = "Credit";
                    // $type = "IGST";
                    // $legal_name = $particular[$i];
                    // $code = $sub_particular_val[$i];
                } else if($ledg_particular=="Other Charges"){
                    $ledg_type = "Credit";
                    // $type = "Others";
                    // $legal_name = $particular[$i];
                    // $code = str_replace(" ", "_", $particular[$i]);
                } else if($ledg_particular=="Total Amount"){
                    $ledg_type = "Debit";
                    // $type = "Others";
                    // $legal_name = $particular[$i];
                    // $code = str_replace(" ", "_", $particular[$i]);
                }else {
                    $ledg_type = "";
                    // $type = "";
                    // $legal_name = "";
                    // $code = "";
                }

                if($ledg_type!="" && $ledg_particular!="Tax"){
                    $bl_flag = true;
                    for($m=0; $m<count($ledgerArray); $m++){
                        if($ledgerArray[$m]['ref_id']==$gi_id && 
                            $ledgerArray[$m]['ref_type']=='Purchase' && 
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
                                    'ref_type'=>'go_debit_details',
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
                                    'company_id'=>$company_id,
                                    'warehouse_no'=>($warehouse_id==''?null:$warehouse_id)
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

        $code = $year . "/" . $month . "/" . $state_code;

        $sql = "select * from acc_series_master where type = '$code' and company_id = '$company_id'";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        $data = $reader->readAll();
        if (count($data)>0){
            $series = intval($data[0]['series']) + 1;

            $sql = "update acc_series_master set series = '$series' where type = '$code' and company_id = '$company_id'";
            $command = Yii::$app->db->createCommand($sql);
            $count = $command->execute();
        } else {
            $series = 1;

            $sql = "insert into acc_series_master (type, series, company_id) values ('".$code."', '".$series."', '".$company_id."')";
            $command = Yii::$app->db->createCommand($sql);
            $count = $command->execute();
        }

        $code = $code . "/" . str_pad($series, 3, "0", STR_PAD_LEFT);

        // echo $code;
        return $code;
    }

    public function getWarehouseDetails($warehouse_code=''){
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select A.*, B.city_name, C.state_name, D.contact_email from internal_warehouse_master A 
                left join city_master B on (A.city_id = B.id) 
                left join state_master C on (A.state_id = C.id) 
                left join internal_warehouse_contacts D on (A.id = D.warehouse_id) 
                where A.warehouse_code like '%".$warehouse_code."%' and A.company_id = '$company_id'";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getDebitNoteDetails($id){
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select * from acc_go_debit_details where gi_go_id = '$id' and is_active = '1' and company_id = '$company_id'";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        $debit_note = $reader->readAll();

        if(count($debit_note)>0) {
            $to_party = '';
            $to_party_address = '';
            $to_party_city = '';
            $to_party_state = '';
            $to_party_country = '';
            $to_party_pincode = '';
            $to_party_gst_no = '';
            $to_party_email = '';

            $sql = "select * from goods_inward_outward where gi_go_id = '$id' and is_active = '1' and company_id = '$company_id'";
            $command = Yii::$app->db->createCommand($sql);
            $reader = $command->query();
            $go_details = $reader->readAll();

            if(count($go_details)>0){
                $warehouse_code = $go_details[0]['warehouse_code'];
                $warehouse_details = $this->getWarehouseDetails($warehouse_code);

                $type_outward = $go_details[0]['type_outward'];

                if($type_outward=='VENDOR'){
                    $vendor_id = $go_details[0]['vendor_id'];
                    $to_party = trim($go_details[0]['vendor_name']);
                    $to_party_address = trim($go_details[0]['vendor_address']);
                    $to_party_city = trim($go_details[0]['vendor_city']);
                    $to_party_state = trim($go_details[0]['vendor_state']);
                    $to_party_country = trim($go_details[0]['vendor_country']);
                    $to_party_pincode = trim($go_details[0]['vendor_pincode']);
                    $to_party_email = trim($go_details[0]['vendor_email']);

                    $sql = "select * from vendor_master where id = '$vendor_id' and company_id = '$company_id'";
                    $command = Yii::$app->db->createCommand($sql);
                    $reader = $command->query();
                    $result = $reader->readAll();
                    if(count($result)>0){
                        $to_party_gst_no = $result[0]['gst_id'];
                    }
                }
                if($type_outward=='CUSTOMER'){
                    $to_party = trim($go_details[0]['customerName']);
                    $to_party_address = trim($go_details[0]['customerAddress']);
                    $to_party_city = trim($go_details[0]['customerCity']);
                    $to_party_state = trim($go_details[0]['customerState']);
                    $to_party_country = trim($go_details[0]['customerCountry']);
                    $to_party_pincode = trim($go_details[0]['customerPincode']);
                    $to_party_email = trim($go_details[0]['customerEmail']);
                }
                if($type_outward=='INTER-DEPOT'){
                    $idt_warehouse_code = trim($go_details[0]['idt_warehouse_code']);
                    $to_party = trim($go_details[0]['idt_warehouse_name']);
                    $to_party_address = trim($go_details[0]['idt_warehouse_address']);
                    $to_party_city = trim($go_details[0]['idt_warehouse_city']);
                    $to_party_state = trim($go_details[0]['idt_warehouse_state']);
                    $to_party_country = trim($go_details[0]['idt_warehouse_country']);
                    $to_party_pincode = trim($go_details[0]['idt_warehouse_pincode']);

                    $result = $this->getWarehouseDetails($idt_warehouse_code);
                    if(count($result)>0){
                        $to_party_gst_no = $result[0]['gst_id'];
                        $to_party_email = $result[0]['contact_email'];
                    }
                }
            }

            $go_details[0]['to_party'] = $to_party;
            $go_details[0]['to_party_address'] = $to_party_address;
            $go_details[0]['to_party_city'] = $to_party_city;
            $go_details[0]['to_party_state'] = $to_party_state;
            $go_details[0]['to_party_country'] = $to_party_country;
            $go_details[0]['to_party_pincode'] = $to_party_pincode;
            $go_details[0]['to_party_gst_no'] = $to_party_gst_no;
            $go_details[0]['to_party_email'] = $to_party_email;
            

            $total_amt = 0;
            $amt_without_tax = 0;
            $cgst_amt = 0;
            $sgst_amt = 0;
            $igst_amt = 0;

            $sql = "select * from acc_go_debit_entries where gi_go_id = '$id' and ledger_type = 'Main Entry' and is_active = '1' and company_id = '$company_id'";
            $command = Yii::$app->db->createCommand($sql);
            $reader = $command->query();
            $result = $reader->readAll();
            if(count($result)>0){
                $total_amt = $result[0]['debit_amt'];
            }

            $sql = "select * from acc_go_debit_entries where gi_go_id = '$id' and acc_type = 'Goods Purchase' and ledger_type = 'Sub Entry' and is_active = '1' and company_id = '$company_id'";
            $command = Yii::$app->db->createCommand($sql);
            $reader = $command->query();
            $result = $reader->readAll();
            if(count($result)>0){
                $amt_without_tax = $result[0]['credit_amt'];
            }

            $sql = "select * from acc_go_debit_entries where gi_go_id = '$id' and acc_type = 'CGST' and is_active = '1' and company_id = '$company_id'";
            $command = Yii::$app->db->createCommand($sql);
            $reader = $command->query();
            $result = $reader->readAll();
            if(count($result)>0){
                $cgst_amt = $result[0]['credit_amt'];
            }

            $sql = "select * from acc_go_debit_entries where gi_go_id = '$id' and acc_type = 'SGST' and is_active = '1' and company_id = '$company_id'";
            $command = Yii::$app->db->createCommand($sql);
            $reader = $command->query();
            $result = $reader->readAll();
            if(count($result)>0){
                $sgst_amt = $result[0]['credit_amt'];
            }

            $sql = "select * from acc_go_debit_entries where gi_go_id = '$id' and acc_type = 'IGST' and is_active = '1' and company_id = '$company_id'";
            $command = Yii::$app->db->createCommand($sql);
            $reader = $command->query();
            $result = $reader->readAll();
            if(count($result)>0){
                $igst_amt = $result[0]['credit_amt'];
            }
            
            $mpdf=new mPDF();
            $mpdf->WriteHTML(Yii::$app->controller->renderPartial('debit_note', ['debit_note' => $debit_note, 'go_details' => $go_details, 
                                                                    'total_amt' => $total_amt, 'amt_without_tax' => $amt_without_tax, 
                                                                    'cgst_amt' => $cgst_amt, 'sgst_amt' => $sgst_amt, 'igst_amt' => $igst_amt
            ]));

            $upload_path = './uploads';
            if(!is_dir($upload_path)) {
                mkdir($upload_path, 0777, TRUE);
            }
            $upload_path = './uploads/go_debit_notes';
            if(!is_dir($upload_path)) {
                mkdir($upload_path, 0777, TRUE);
            }
            $upload_path = './uploads/go_debit_notes/'.$id;
            if(!is_dir($upload_path)) {
                mkdir($upload_path, 0777, TRUE);
            }

            $file_name = $upload_path . '/debit_note_' . $id . '.pdf';
            $file_path = 'uploads/go_debit_notes/' . $id . '/debit_note_' . $id . '.pdf';

            // $mpdf->Output('MyPDF.pdf', 'D');
            $mpdf->Output($file_name, 'F');
            // exit;

            $sql = "update acc_go_debit_details set debit_note_path = '$file_path' where gi_go_id = '$id' and is_active = '1' and company_id = '$company_id'";
            $command = Yii::$app->db->createCommand($sql);
            $count = $command->execute();

            $sql = "select * from acc_go_debit_details where gi_go_id = '$id' and is_active = '1' and company_id = '$company_id'";
            $command = Yii::$app->db->createCommand($sql);
            $reader = $command->query();
            $debit_note = $reader->readAll();

        } else {
            $debit_note = array();
            $go_details = array();
            $warehouse_details = array();
            $cgst_amt = 0;
            $sgst_amt = 0;
            $igst_amt = 0;
        }

        $data['debit_note'] = $debit_note;
        $data['go_details'] = $go_details;
        $data['total_amt'] = $total_amt;
        $data['amt_without_tax'] = $amt_without_tax;
        $data['cgst_amt'] = $cgst_amt;
        $data['sgst_amt'] = $sgst_amt;
        $data['igst_amt'] = $igst_amt;
        
        return $data;
    }
    
    public function setLog($module_name, $sub_module, $action, $vendor_id, $description, $table_name, $table_id) {
        $session = Yii::$app->session;
        $curusr = $session['session_id'];
        $company_id = $session['company_id'];
        $now = date('Y-m-d H:i:s');

        $array = array('module_name' => $module_name, 
                        'sub_module' => $sub_module, 
                        'action' => $action, 
                        'vendor_id' => $vendor_id, 
                        'user_id' => $curusr, 
                        'description' => $description, 
                        'log_activity_date' => $now, 
                        'table_name' => $table_name, 
                        'table_id' => $table_id,
                        'company_id'=>$company_id);
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
<<<<<<< HEAD

    public function getGrnAccEntries($id){
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select A.*, B.*, A.vendor_id as vendor_id1 from 
                (select A.*, Case When A.warehouse_state=B.to_state Then 'INTRA' Else 'INTER' end as vat_cst,
                    Case When A.warehouse_state=B.to_state Then 'Same States' Else 'Different States' end as tax_zone_name,
                    Case When A.warehouse_state=B.to_state Then 'INTRA' Else 'INTER' end as tax_zone_code, E.idt_warehouse, E.warehouse_id, B.to_state 
                from goods_inward_outward A 
                left join 
                (select (Case When type_outward='VENDOR' Then vendor_state When type_outward='INTER-DEPOT' Then idt_warehouse_state end) as to_state, gi_go_id from goods_inward_outward) B 
                on(A.gi_go_id = B.gi_go_id) 
                left join 
                (select distinct warehouse_name as idt_warehouse, id as warehouse_id, warehouse_code from internal_warehouse_master) E 
                on (A.idt_warehouse_code = E.warehouse_code) 
                where date(A.gi_go_final_commit_date) > date('2017-07-01') and A.company_id='$company_id' 
                    and A.inward_outward = 'outward' and A.type_outward = 'VENDOR' and A.gi_go_status = 'COMPLETE') A
                Left Join
                (select * from acc_go_debit_entries Where gi_go_id='$id' and status = 'approved' and is_active = '1' 
                    order by gi_go_id, invoice_no, id, vat_percen, vat_cst) B 
                on (A.gi_go_id = B.gi_go_id)
                where B.id is not null
                order by B.id";

        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }
    
    public function getGrnDetails($id){
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        /*$sql = "select A.*, B.vendor_code, C.gi_date as grn_date from grn A left join vendor_master B on (A.vendor_id = B.id) 
                left join acc_grn_entries C on (A.grn_id = C.grn_id and C.status = 'approved' and C.is_active='1' and 
                C.particular = 'Total Amount') where A.grn_id = '$id' and A.status = 'approved' and A.is_active='1' and A.company_id='$company_id'";*/
        $sql = "select A.* from (
                select Case When warehouse_state=H.to_state Then 'Same States' Else 'Different States' end as tax_zone_name,
                    Case When A.warehouse_state=H.to_state Then 'INTRA' Else 'INTER' end as  tax_zone_code, 
                    A.*, A.vendor_id as vendor_id1, H.to_state, E.idt_warehouse, E.warehouse_id 
                from goods_inward_outward A 
                left join 
                (select (Case When type_outward='VENDOR' Then vendor_state 
                            When type_outward='INTER-DEPOT' Then idt_warehouse_state end) as to_state, 
                    gi_go_id from goods_inward_outward) H 
                on (A.gi_go_id = H.gi_go_id) 
                left join 
                (select distinct warehouse_name as idt_warehouse,id as warehouse_id, warehouse_code from internal_warehouse_master) E
                on (A.idt_warehouse_code = E.warehouse_code)
                left join acc_go_debit_entries C 
                on (A.gi_go_id = C.gi_go_id and C.status = 'approved' and C.is_active='1' and C.particular = 'Total Amount')
                where date(A.gi_go_final_commit_date) > date('2017-07-01') and A.company_id='$company_id' and
                    A.inward_outward='OUTWARD' and A.type_outward = 'VENDOR' and A.gi_go_status = 'COMPLETE' and 
                    A.company_id='$company_id' and A.gi_go_id=$id ) A";
         
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getGrnPostingDetails($id ,$skuentries){
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        //echo $skuentries;
        if($skuentries)
            $fromtable = 'acc_go_debit_sku_items';
        else
            $fromtable = 'prepare_go_items';

        //ifnull(ROUND(per_unit/(1+(AA.vat_percent/100)),2),0 )
        $sql="select AA.*,BB.tax_zone_code, BB.tax_zone_name, BB.cgst_rate, BB.sgst_rate, BB.igst_rate, 
                ifnull(round((cost_excl_vat),0) ,2)as total_cost,
                ifnull(round(AA.cost_excl_vat*AA.vat_percent/100,2),0) as total_tax, 
                ifnull(round(AA.cost_excl_vat*BB.cgst_rate/100,2),0) as total_cgst, 
                ifnull(round(AA.cost_excl_vat*BB.sgst_rate/100,2),0) as total_sgst, 
                ifnull(round(AA.cost_excl_vat*BB.igst_rate/100,2),0) as total_igst, 
                (ifnull(round(AA.cost_excl_vat*AA.vat_percent/100,2),0)+cost_excl_vat) as total_amount, 
                per_unit as per_unit_exc_tax from 
            (select EE.*, ifnull(EE.invoice_qty,0)*ifnull(EE.per_unit,0) as cost_excl_vat from 
            (select CC.*, ifnull(CC.quantity,0)-ifnull(DD.qty,0) as invoice_qty from 
            (select AA.*, BB.grn_id from 
            (select A.gi_go_id, A.gi_go_ref_no, A.warehouse_code, A.warehouse_name, A.vendor_name, 
                A.idt_warehouse_name, A.gi_go_final_commit_date, A.updated_by, A.updated_date, A.vendor_state as to_state, 
                Case When A.warehouse_state=A.vendor_state Then 'INTRA' Else 'INTER' end as vat_cst, 
                C.prepare_go_id, C.from_warehouse_name, C.destination_warehouse_company_name, C.total_qty, 
                C.invoice_number, B.cost as per_unit, B.product_title, B.mrp, B.psku, B.fnsku, B.hsn_code, 
                B.batch_code, B.asin, B.expiry_date, B.ean, B.sku_code, B.grn_no, B.shipment_plan_name, 
                B.isa, B.po_no, B.go_no, B.grn_entries_id, B.product_id, B.is_combo_items, B.order_qty, 
                B.manual_discount, B.value_at_mrp, B.vat_percent, B.quantity, B.shipment_id, 
                B.bucket_name, B.company_id, B.created_by, B.created_date, B.is_active, B.order_id 
            from goods_inward_outward A 
            left join ".$fromtable." B on (A.pre_go_ref=B.prepare_go_id) 
            left join prepare_go C on(A.pre_go_ref=C.prepare_go_id) 
            where A.is_active = '1' and A.company_id = '$company_id' and A.gi_go_id = '$id' and 
                A.inward_outward = 'outward' and date(A.gi_go_final_commit_date) > date('2017-07-01') and 
                A.type_outward = 'VENDOR' and B.is_active = '1' and B.company_id = '$company_id' and 
                C.is_active = '1' and C.company_id = '$company_id' and A.gi_go_status = 'COMPLETE') AA 
            left join 
            (select distinct grn_id, gi_id from grn where status = 'approved' and is_active = '1' and 
                company_id = '$company_id' and (date(gi_date)<date('2018-03-31') or gi_type <> 'VENDOR') 
            union 
            select distinct A.grn_id, B.gi_id from acc_grn_entries A left join grn B on (A.grn_id=B.grn_id) 
            where A.status = 'approved' and A.is_active = '1' and A.company_id = '$company_id' and 
                B.status = 'approved' and B.is_active = '1' and B.company_id = '$company_id') BB 
            on (AA.grn_no=BB.gi_id) where BB.grn_id is not null) CC 
            left join 
            (select grn_id, psku, qty, ded_type from acc_grn_sku_entries where status='approved' and is_active='1' and company_id='$company_id'and ded_type in ('expiry', 'damaged')) DD 
            on (CC.grn_id=DD.grn_id and CC.psku=DD.psku)) EE 
            where EE.invoice_qty>0)AA

            left join 

            (select id, tax_zone_code, tax_zone_name, parent_id, tax_rate, 
                                max(case when child_tax_type_code = 'CGST' then child_tax_rate else 0 end) as cgst_rate, 
                                max(case when child_tax_type_code = 'SGST' then child_tax_rate else 0 end) as sgst_rate, 
                                max(case when child_tax_type_code = 'IGST' then child_tax_rate else 0 end) as igst_rate from (select A.id, A.tax_zone_code, A.tax_zone_name, B.id as parent_id, B.tax_type_id as parent_tax_type_id, 
                                B.tax_rate, C.child_id, D.tax_type_id as child_tax_type_id, D.tax_rate as child_tax_rate, 
                                E.tax_category as child_tax_category, E.tax_type_code as child_tax_type_code, 
                                E.tax_type_name as child_tax_type_name 
                            from tax_zone_master A 
                                left join tax_rate_master B on (A.id = B.tax_zone_id) 
                                left join tax_component C on (B.id = C.parent_id) 
                                left join tax_rate_master D on (C.child_id = D.id) 
                                left join tax_type_master E on (D.tax_type_id = E.id) ) C
            where child_tax_rate != 0
            group by id, tax_zone_code, tax_zone_name, parent_id, tax_rate
            ) BB
            on (AA.vat_cst COLLATE utf8_unicode_ci = BB.tax_zone_code and round(AA.vat_percent,4)=round(BB.tax_rate,4))
            Where AA.gi_go_id=$id  Order By vat_percent ASC;";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getGrnskuDetails($id ,$skuentries){
            $session = Yii::$app->session;
            $company_id = $session['company_id'];

           
            if($skuentries)
                $fromtable = 'acc_go_debit_sku_items';
            else
                $fromtable = 'prepare_go_items';

            //ifnull(ROUND(per_unit/(1+(AA.vat_percent/100)),2),0 )
            $sql="select AA.*,BB.tax_zone_code, BB.tax_zone_name, BB.cgst_rate, BB.sgst_rate, BB.igst_rate, 
                ifnull(round((value_at_cost),0) ,2)as total_cost,
                ifnull(round(AA.value_at_cost*AA.vat_percent/100,2),0) as total_tax, 
                ifnull(round(AA.value_at_cost*BB.cgst_rate/100,2),0) as total_cgst, 
                ifnull(round(AA.value_at_cost*BB.sgst_rate/100,2),0) as total_sgst, 
                ifnull(round(AA.value_at_cost*BB.igst_rate/100,2),0) as total_igst, 
                (ifnull(round(AA.value_at_cost*AA.vat_percent/100,2),0)+value_at_cost) as total_amount, 
                per_unit as per_unit_exc_tax from 
                (select A.*, A.vendor_state as to_state, 
                    Case When A.warehouse_state=A.vendor_state Then 'INTRA' Else 'INTER' end as vat_cst, 
                    E.prepare_go_id, E.from_warehouse_name, E.destination_warehouse_company_name, E.total_qty, E.invoice_number, 
                    B.cost as per_unit, B.product_title, B.mrp, B.psku, B.fnsku, B.hsn_code, B.batch_code, B.asin, 
                    B.expiry_date, B.ean, B.sku_code, B.grn_no, B.shipment_plan_name, B.isa, B.po_no, B.go_no, 
                    B.grn_entries_id, B.product_id, B.is_combo_items, B.order_qty, B.manual_discount, B.value_at_mrp, 
                    B.vat_percent, B.quantity as invoice_qty, B.value_at_cost as cost_excl_vat 
                from goods_inward_outward A 
                left join prepare_go E on (A.pre_go_ref=E.prepare_go_id)
                left join ".$fromtable." B on (A.pre_go_ref=B.prepare_go_id) 
                left join grn C on (B.grn_no=C.gi_id) 
                left join acc_grn_sku_entries D on (C.grn_id=D.grn_id and B.psku=D.psku) 
                where A.is_active = '1' and A.company_id='$company_id' and A.gi_go_id='$id' and 
                    A.inward_outward = 'outward' and date(A.gi_go_final_commit_date) > date('2017-07-01') and 
                    A.type_outward = 'VENDOR' and D.id is null and A.gi_go_status = 'COMPLETE') AA 
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
                on (AA.vat_cst COLLATE utf8_unicode_ci = BB.tax_zone_code and round(AA.vat_percent,4)=round(BB.tax_rate,4)) 
                Where AA.gi_go_id=$id  Order By vat_percent ASC;";
            $command = Yii::$app->db->createCommand($sql);
            $reader = $command->query();
            return $reader->readAll();
    }

    public function getskugoItems($prepare_go_id) {
        $sql = "select count(*) as skucount  from acc_go_debit_sku_items Where prepare_go_id=$prepare_go_id";
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

    public function getGrnAccLedgerEntries($id){
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select A.*,B.* from 
                (select A.* ,B.* from 
                (select A.*,Case When A.warehouse_state=B.to_state Then 'INTRA' Else 'INTER' end as vat_cst  from goods_inward_outward A 
                    left join (select (Case When type_outward='VENDOR' Then vendor_state When  type_outward='INTER-DEPOT' Then idt_warehouse_state end) as to_state,gi_go_id from  goods_inward_outward ) B on(A.gi_go_id = B.gi_go_id) 
                where  date(A.gi_go_final_commit_date) > date('2017-07-01') and A.company_id='$company_id' and 
                    A.inward_outward = 'outward' and A.type_outward='VENDOR' and A.gi_go_status = 'COMPLETE') A
                left join 
                (select prepare_go_id,from_warehouse_name,destination_warehouse_company_name,total_qty,invoice_number,invoice_created_date from prepare_go Where company_id='$company_id') B 
                on  A.pre_go_ref=B.prepare_go_id ) A
                left join 
                (select * from acc_ledger_entries) B 
                on (A.gi_go_id = B.ref_id and B.ref_type = 'go_debit_details') 
                where B.ref_id = '$id'
                order by B.ref_id, B.invoice_no, B.id";

        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function set_goskuentries(){ 
       $request = Yii::$app->request;
       $mycomponent = Yii::$app->mycomponent;
       $session = Yii::$app->session;
       $ded_type = 'goodsoutwards';
       $ean = $request->post($ded_type.'_ean');
       $hsn_code = $request->post($ded_type.'_hsn_code');
       $batch_code = $request->post($ded_type.'_batch_code');
       $psku = $request->post($ded_type.'_psku');
       $sku_code = $request->post($ded_type.'_sku_code');
       $asin = $request->post($ded_type.'_asin_code');
       $fnsku = $request->post($ded_type.'_fnsku');
       $product_title = $request->post($ded_type.'_product_title');
       $quantity = $request->post($ded_type.'_qty');
       $expiry_date = $request->post($ded_type.'_expiry_date');
       $mrp = $request->post($ded_type.'_mrp');
       $manual_discount = $request->post($ded_type.'_manual_discount');
       $value_at_mrp = $request->post($ded_type.'_value_at_mrp');
       $cost = $request->post($ded_type.'_cost_excl_tax_per_unit');
       $cost_incl_vat_cst = $request->post($ded_type.'_cost_inc_tax');
       $value_at_cost = $request->post($ded_type.'_cost_excl_tax');
       $vat_percent = $request->post($ded_type.'_vat_percen_tax');
       $grn_no = $request->post($ded_type.'_grn_no');
       $shipment_id = $request->post($ded_type.'_shipment_id');
       $shipment_plan_name = $request->post($ded_type.'_shipment_plan_name_id');
       $isa = $request->post($ded_type.'_isa');
       $po_no = $request->post($ded_type.'_po_no');
       $go_no = $request->post($ded_type.'_go_no');
       $grn_entries_id = $request->post($ded_type.'_grn_entries_id');
       $product_id = $request->post($ded_type.'_product_id');
       $bucket_name = $request->post($ded_type.'_bucket_name');
       $prepare_go_id = $request->post($ded_type.'_prepare_go_id');
       $company_id = $request->post($ded_type.'_company_id');
       $created_by = $request->post($ded_type.'_created_by');
       $updated_by = $request->post($ded_type.'_updated_by');
       $created_date = $request->post($ded_type.'_created_date');
       $updated_date = $request->post($ded_type.'_updated_date');
       $is_active = $request->post($ded_type.'_is_active');
       $is_combo_items = $request->post($ded_type.'_is_combo_items');
       $order_qty = $request->post($ded_type.'_order_qty');
       $order_id = $request->post($ded_type.'_order_id');
       $bulkInsertArray = array();

       for($i=0; $i<count($ean); $i++){
           $bulkInsertArray[$i]=[
                                'ean'=>$ean[$i],
                                'hsn_code'=>$hsn_code[$i],
                                'batch_code'=>$batch_code[$i],
                                'psku'=>$psku[$i],
                                'sku_code'=>$sku_code[$i],
                                'asin'=>$asin[$i],
                                'fnsku'=>$fnsku[$i],
                                'product_title'=>$product_title[$i],
                                'quantity'=>$quantity[$i],
                                'expiry_date'=>($expiry_date[$i]==''?null:$expiry_date[$i]),
                                'mrp'=>$mycomponent->format_number($mrp[$i],4),
                                'manual_discount'=>$manual_discount[$i],
                                'value_at_mrp'=>$mycomponent->format_number($value_at_mrp[$i],4),
                                'cost'=>$mycomponent->format_number($cost[$i],4),
                                'value_at_cost'=>$mycomponent->format_number($value_at_cost[$i],4),
                                'cost_incl_vat_cst'=>$mycomponent->format_number($cost_incl_vat_cst[$i],4),
                                'vat_percent'=>$mycomponent->format_number($vat_percent[$i],4),
                                'grn_no'=>$grn_no[$i],
                                'shipment_id'=>$shipment_id[$i],
                                'shipment_plan_name'=>$shipment_plan_name[$i],
                                'isa'=>$isa[$i],
                                'po_no'=>$po_no[$i],
                                'go_no'=>$go_no[$i],
                                'grn_entries_id'=>$grn_entries_id[$i],
                                'product_id'=>($product_id[$i]==''?null:$product_id[$i]),
                                'bucket_name'=>$bucket_name[$i],
                                'prepare_go_id'=>($prepare_go_id[$i]==''?null:$prepare_go_id[$i]),
                                'company_id'=>$company_id[$i],
                                'created_date'=>$created_date[$i],
                                'updated_date'=>$updated_date[$i],
                                'is_active'=>$is_active[$i],
                                'is_combo_items'=>$is_combo_items[$i],
                                'order_qty'=>($order_qty[$i]==''?null:$order_qty[$i]),
                                'order_id'=>$order_id[$i],
                                ];
        }
        if(count($bulkInsertArray)>0){
                $sql = "delete from acc_go_debit_sku_items where prepare_go_id = '".$prepare_go_id[0]."'";
                Yii::$app->db->createCommand($sql)->execute();
                
                $columnNameArray= ['ean','hsn_code','batch_code', 'psku',
                                    'sku_code', 'asin', 'fnsku','product_title','quantity',
                                    'expiry_date','mrp', 'manual_discount','value_at_mrp',
                                    'cost','value_at_cost', 'cost_incl_vat_cst','vat_percent',
                                    'grn_no','shipment_id','shipment_plan_name','isa','po_no'
                                    ,'go_no','grn_entries_id','product_id','bucket_name',
                                    'prepare_go_id','company_id','created_date',
                                    'updated_date','is_active','is_combo_items','order_qty',
                                    'order_id'];
                // below line insert all your record and return number of rows inserted
                $tableName = "acc_go_debit_sku_items";
                $insertCount = Yii::$app->db->createCommand()
                               ->batchInsert(
                                     $tableName, $columnNameArray, $bulkInsertArray
                                 )
                               ->execute();

                // echo $insertCount;
                // echo '<br/>';
                // echo 'hii';
        }
    }
}




/*

if($type_outward=='VENDOR'){
                    $to_state = $vendor_state;
                    $to_party = trim($data[0]['vendor_name']);
                }
                if($type_outward=='CUSTOMER'){
                    $to_state = $customerState;
                    $to_party = trim($data[0]['customerName']);
                }
                if($type_outward=='INTER-DEPOT'){
                    $to_state = $idt_warehouse_state;
                    $to_party = trim($data[0]['idt_warehouse_name']);
                }

*/
=======
}
>>>>>>> 40251667d20641f61579b49c4e0131e7351baf6f
