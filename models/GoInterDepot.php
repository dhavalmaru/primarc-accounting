<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\Session;
use mPDF;

class GoInterDepot extends Model
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

    public function getCompanyDetails(){
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select * from company_master A where A.id = '$company_id'";
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
                $wheresearch = " and (C.gi_go_id like '%$search%' or C.gi_go_ref_no like '%$search%' or 
                                C.warehouse_name like '%$search%' or C.vendor_name like '%$search%' or 
                                C.idt_warehouse_name like '%$search%' or 
                                C.gi_go_date_time like '%$search%' or C.updated_by like '%$search%') ";
            } 
        }

        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select C.gi_go_id, C.gi_go_ref_no, C.warehouse_name, C.vendor_name, C.idt_warehouse_name, 
                    C.gi_go_date_time, C.updated_by, C.updated_date, C.b_gi_go_id, 
                    sum(C.total_amount) as total_amount from 
                (select A.gi_go_id, A.gi_go_ref_no, A.warehouse_name, A.vendor_name, A.idt_warehouse_name, 
                    A.gi_go_date_time, A.updated_by, A.updated_date, B.gi_go_id as b_gi_go_id, A.total_amount from 
                (select A.gi_go_id, A.gi_go_ref_no, A.warehouse_name, A.vendor_name, A.idt_warehouse_name, A.gi_go_date_time, 
                    A.updated_by, A.updated_date, B.psku, B.value_at_cost, B.vat_percent, B.grn_no, C.grn_id, D.id, 
                    ifnull(ifnull(round(B.value_at_cost*B.vat_percent/100,2),0)+B.value_at_cost,0) as total_amount 
                from goods_inward_outward A 
                left join prepare_go_items B on (A.pre_go_ref=B.prepare_go_id) 
                left join grn C on (B.grn_no=C.gi_id) 
                left join acc_grn_sku_entries D on (C.grn_id=D.grn_id and B.psku=D.psku) 
                where A.is_active = '1' and A.company_id='$company_id' and A.warehouse_state<>A.idt_warehouse_state and 
                    A.inward_outward = 'outward' and date(A.gi_go_date_time) > date('2018-03-31') and 
                    A.type_outward = 'INTER-DEPOT' and D.id is null) A 
                left join 
                (select distinct gi_go_id from acc_go_debit_entries) B 
                on (A.gi_go_id = B.gi_go_id)) C 
                where C.b_gi_go_id is null ".$wheresearch." 
                group by C.gi_go_id, C.gi_go_ref_no, C.warehouse_name, C.vendor_name, C.idt_warehouse_name, 
                    C.gi_go_date_time, C.updated_by, C.updated_date, C.b_gi_go_id 
                order by UNIX_TIMESTAMP(C.updated_date) desc ".$len;
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
                $wheresearch = " and (C.gi_go_id like '%$search%' or C.gi_go_ref_no like '%$search%' or 
                                C.warehouse_name like '%$search%' or C.vendor_name like '%$search%' or 
                                C.idt_warehouse_name like '%$search%' or 
                                C.gi_go_date_time like '%$search%' or C.updated_by like '%$search%') ";
            } 
        }

        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select count(*) as count from 
                (select C.gi_go_id, C.gi_go_ref_no, C.warehouse_name, C.vendor_name, C.idt_warehouse_name, 
                    C.gi_go_date_time, C.updated_by, C.updated_date, C.b_gi_go_id, 
                    sum(C.total_amount) as total_amount from 
                (select A.gi_go_id, A.gi_go_ref_no, A.warehouse_name, A.vendor_name, A.idt_warehouse_name, 
                    A.gi_go_date_time, A.updated_by, A.updated_date, B.gi_go_id as b_gi_go_id, A.total_amount from 
                (select A.gi_go_id, A.gi_go_ref_no, A.warehouse_name, A.vendor_name, A.idt_warehouse_name, A.gi_go_date_time, 
                    A.updated_by, A.updated_date, B.psku, B.value_at_cost, B.vat_percent, B.grn_no, C.grn_id, D.id, 
                    ifnull(ifnull(round(B.value_at_cost*B.vat_percent/100,2),0)+B.value_at_cost,0) as total_amount 
                from goods_inward_outward A 
                left join prepare_go_items B on (A.pre_go_ref=B.prepare_go_id) 
                left join grn C on (B.grn_no=C.gi_id) 
                left join acc_grn_sku_entries D on (C.grn_id=D.grn_id and B.psku=D.psku) 
                where A.is_active = '1' and A.company_id='$company_id' and A.warehouse_state<>A.idt_warehouse_state and 
                    A.inward_outward = 'outward' and date(A.gi_go_date_time) > date('2018-03-31') and 
                    A.type_outward = 'INTER-DEPOT' and D.id is null) A 
                left join 
                (select distinct gi_go_id from acc_go_debit_entries) B 
                on (A.gi_go_id = B.gi_go_id)) C 
                where C.b_gi_go_id is null ".$wheresearch." 
                group by C.gi_go_id, C.gi_go_ref_no, C.warehouse_name, C.vendor_name, C.idt_warehouse_name, 
                    C.gi_go_date_time, C.updated_by, C.updated_date, C.b_gi_go_id 
                order by UNIX_TIMESTAMP(C.updated_date) desc) D";
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
                $wheresearch = " and (C.gi_go_id like '%$search%' or C.gi_go_ref_no like '%$search%' or 
                                C.warehouse_name like '%$search%' or C.vendor_name like '%$search%' or 
                                C.idt_warehouse_name like '%$search%' or 
                                C.gi_go_date_time like '%$search%' or C.updated_by like '%$search%' or 
                                C.updated_date like '%$search%' or C.username like '%$search%') ";
            } 
        }

        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select D.*, E.is_paid from 
                (select C.gi_go_id, C.gi_go_ref_no, C.warehouse_name, C.vendor_name, C.idt_warehouse_name, 
                    C.gi_go_date_time, C.updated_by, C.updated_date, C.username, C.b_gi_go_id, 
                    sum(C.total_amount) as total_amount from 
                (select A.gi_go_id, A.gi_go_ref_no, A.warehouse_name, A.vendor_name, A.idt_warehouse_name, 
                    A.gi_go_date_time, A.updated_by, A.updated_date, A.username, B.gi_go_id as b_gi_go_id, 
                    A.total_amount from 
                (select A.gi_go_id, A.gi_go_ref_no, A.warehouse_name, A.vendor_name, A.idt_warehouse_name, 
                    A.gi_go_date_time, A.updated_by, A.updated_date, E.username, 
                    B.psku, B.value_at_cost, B.vat_percent, B.grn_no, C.grn_id, D.id, 
                    ifnull(ifnull(round(B.value_at_cost*B.vat_percent/100,2),0)+B.value_at_cost,0) as total_amount 
                from goods_inward_outward A 
                left join prepare_go_items B on (A.pre_go_ref=B.prepare_go_id) 
                left join grn C on (B.grn_no=C.gi_id) 
                left join acc_grn_sku_entries D on (C.grn_id=D.grn_id and B.psku=D.psku) 
                left join user E on(A.updated_by = E.id) 
                where A.is_active = '1' and A.company_id='$company_id' and A.warehouse_state<>A.idt_warehouse_state and 
                    A.inward_outward = 'outward' and date(A.gi_go_date_time) > date('2018-03-31') and 
                    A.type_outward = 'INTER-DEPOT' and D.id is null) A 
                left join 
                (select distinct gi_go_id from acc_go_debit_entries) B 
                on (A.gi_go_id = B.gi_go_id)) C 
                where C.b_gi_go_id is not null ".$wheresearch." 
                group by C.gi_go_id, C.gi_go_ref_no, C.warehouse_name, C.vendor_name, C.idt_warehouse_name, 
                    C.gi_go_date_time, C.updated_by, C.updated_date, C.b_gi_go_id) D 
                left join 
                (select distinct ref_id, is_paid from acc_ledger_entries 
                    where ref_type = 'go_debit_details' and company_id='$company_id' and is_active = '1' and status = 'Approved' and is_paid = '1') E 
                on (D.gi_go_id = E.ref_id) 
                order by UNIX_TIMESTAMP(D.updated_date) desc ".$len;
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
                $wheresearch = " and (C.gi_go_id like '%$search%' or C.gi_go_ref_no like '%$search%' or 
                                C.warehouse_name like '%$search%' or C.vendor_name like '%$search%' or 
                                C.idt_warehouse_name like '%$search%' or 
                                C.gi_go_date_time like '%$search%' or C.updated_by like '%$search%' or 
                                C.updated_date like '%$search%' or C.username like '%$search%') ";
            } 
        }

        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select count(*) as count from 
                (select D.*, E.is_paid from 
                (select C.gi_go_id, C.gi_go_ref_no, C.warehouse_name, C.vendor_name, C.idt_warehouse_name, 
                    C.gi_go_date_time, C.updated_by, C.updated_date, C.username, C.b_gi_go_id, 
                    sum(C.total_amount) as total_amount from 
                (select A.gi_go_id, A.gi_go_ref_no, A.warehouse_name, A.vendor_name, A.idt_warehouse_name, 
                    A.gi_go_date_time, A.updated_by, A.updated_date, A.username, B.gi_go_id as b_gi_go_id, 
                    A.total_amount from 
                (select A.gi_go_id, A.gi_go_ref_no, A.warehouse_name, A.vendor_name, A.idt_warehouse_name, 
                    A.gi_go_date_time, A.updated_by, A.updated_date, E.username, 
                    B.psku, B.value_at_cost, B.vat_percent, B.grn_no, C.grn_id, D.id, 
                    ifnull(ifnull(round(B.value_at_cost*B.vat_percent/100,2),0)+B.value_at_cost,0) as total_amount 
                from goods_inward_outward A 
                left join prepare_go_items B on (A.pre_go_ref=B.prepare_go_id) 
                left join grn C on (B.grn_no=C.gi_id) 
                left join acc_grn_sku_entries D on (C.grn_id=D.grn_id and B.psku=D.psku) 
                left join user E on(A.updated_by = E.id) 
                where A.is_active = '1' and A.company_id='$company_id' and A.warehouse_state<>A.idt_warehouse_state and 
                    A.inward_outward = 'outward' and date(A.gi_go_date_time) > date('2018-03-31') and 
                    A.type_outward = 'INTER-DEPOT' and D.id is null) A 
                left join 
                (select distinct gi_go_id from acc_go_debit_entries) B 
                on (A.gi_go_id = B.gi_go_id)) C 
                where C.b_gi_go_id is not null ".$wheresearch." 
                group by C.gi_go_id, C.gi_go_ref_no, C.warehouse_name, C.vendor_name, C.idt_warehouse_name, 
                    C.gi_go_date_time, C.updated_by, C.updated_date, C.b_gi_go_id) D 
                left join 
                (select distinct ref_id, is_paid from acc_ledger_entries 
                    where ref_type = 'go_debit_details' and company_id='$company_id' and is_active = '1' and status = 'Approved' and is_paid = '1') E 
                on (D.gi_go_id = E.ref_id) 
                order by UNIX_TIMESTAMP(D.updated_date) desc) F";
                
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
                $wheresearch = " where (C.gi_go_id like '%$search%' or C.gi_go_ref_no like '%$search%' or 
                                C.warehouse_name like '%$search%' or C.vendor_name like '%$search%' or 
                                C.idt_warehouse_name like '%$search%' or 
                                C.gi_go_date_time like '%$search%' or C.updated_by like '%$search%' or 
                                C.updated_date like '%$search%' or C.username like '%$search%') ";
            } 
        }

        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select D.*, E.is_paid from 
                (select C.gi_go_id, C.gi_go_ref_no, C.warehouse_name, C.vendor_name, C.idt_warehouse_name, 
                    C.gi_go_date_time, C.updated_by, C.updated_date, C.username, C.b_gi_go_id, 
                    sum(C.total_amount) as total_amount from 
                (select A.gi_go_id, A.gi_go_ref_no, A.warehouse_name, A.vendor_name, A.idt_warehouse_name, 
                    A.gi_go_date_time, A.updated_by, A.updated_date, A.username, B.gi_go_id as b_gi_go_id, 
                    A.total_amount from 
                (select A.gi_go_id, A.gi_go_ref_no, A.warehouse_name, A.vendor_name, A.idt_warehouse_name, 
                    A.gi_go_date_time, A.updated_by, A.updated_date, E.username, 
                    B.psku, B.value_at_cost, B.vat_percent, B.grn_no, C.grn_id, D.id, 
                    ifnull(ifnull(round(B.value_at_cost*B.vat_percent/100,2),0)+B.value_at_cost,0) as total_amount 
                from goods_inward_outward A 
                left join prepare_go_items B on (A.pre_go_ref=B.prepare_go_id) 
                left join grn C on (B.grn_no=C.gi_id) 
                left join acc_grn_sku_entries D on (C.grn_id=D.grn_id and B.psku=D.psku) 
                left join user E on(A.updated_by = E.id) 
                where A.is_active = '1' and A.company_id='$company_id' and A.warehouse_state<>A.idt_warehouse_state and 
                    A.inward_outward = 'outward' and date(A.gi_go_date_time) > date('2018-03-31') and 
                    A.type_outward = 'INTER-DEPOT' and D.id is null) A 
                left join 
                (select distinct gi_go_id from acc_go_debit_entries) B 
                on (A.gi_go_id = B.gi_go_id)) C 
                ".$wheresearch." 
                group by C.gi_go_id, C.gi_go_ref_no, C.warehouse_name, C.vendor_name, C.idt_warehouse_name, 
                    C.gi_go_date_time, C.updated_by, C.updated_date, C.b_gi_go_id) D 
                left join 
                (select distinct ref_id, is_paid from acc_ledger_entries 
                    where ref_type = 'go_debit_details' and company_id='$company_id' and is_active = '1' and 
                    status = 'Approved' and is_paid = '1') E 
                on (D.gi_go_id = E.ref_id) 
                order by UNIX_TIMESTAMP(D.updated_date) desc ".$len;
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
                $wheresearch = " where (C.gi_go_id like '%$search%' or C.gi_go_ref_no like '%$search%' or 
                                C.warehouse_name like '%$search%' or C.vendor_name like '%$search%' or 
                                C.idt_warehouse_name like '%$search%' or 
                                C.gi_go_date_time like '%$search%' or C.updated_by like '%$search%' or 
                                C.updated_date like '%$search%' or C.username like '%$search%') ";
            } 
        }

        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select count(*) as count from 
                (select D.*, E.is_paid from 
                (select C.gi_go_id, C.gi_go_ref_no, C.warehouse_name, C.vendor_name, C.idt_warehouse_name, 
                    C.gi_go_date_time, C.updated_by, C.updated_date, C.username, C.b_gi_go_id, 
                    sum(C.total_amount) as total_amount from 
                (select A.gi_go_id, A.gi_go_ref_no, A.warehouse_name, A.vendor_name, A.idt_warehouse_name, 
                    A.gi_go_date_time, A.updated_by, A.updated_date, A.username, B.gi_go_id as b_gi_go_id, 
                    A.total_amount from 
                (select A.gi_go_id, A.gi_go_ref_no, A.warehouse_name, A.vendor_name, A.idt_warehouse_name, 
                    A.gi_go_date_time, A.updated_by, A.updated_date, E.username, 
                    B.psku, B.value_at_cost, B.vat_percent, B.grn_no, C.grn_id, D.id, 
                    ifnull(ifnull(round(B.value_at_cost*B.vat_percent/100,2),0)+B.value_at_cost,0) as total_amount 
                from goods_inward_outward A 
                left join prepare_go_items B on (A.pre_go_ref=B.prepare_go_id) 
                left join grn C on (B.grn_no=C.gi_id) 
                left join acc_grn_sku_entries D on (C.grn_id=D.grn_id and B.psku=D.psku) 
                left join user E on(A.updated_by = E.id) 
                where A.is_active = '1' and A.company_id='$company_id' and A.warehouse_state<>A.idt_warehouse_state and 
                    A.inward_outward = 'outward' and date(A.gi_go_date_time) > date('2018-03-31') and 
                    A.type_outward = 'INTER-DEPOT' and D.id is null) A 
                left join 
                (select distinct gi_go_id from acc_go_debit_entries) B 
                on (A.gi_go_id = B.gi_go_id)) C 
                ".$wheresearch." 
                group by C.gi_go_id, C.gi_go_ref_no, C.warehouse_name, C.vendor_name, C.idt_warehouse_name, 
                    C.gi_go_date_time, C.updated_by, C.updated_date, C.b_gi_go_id) D 
                left join 
                (select distinct ref_id, is_paid from acc_ledger_entries 
                    where ref_type = 'go_debit_details' and company_id='$company_id' and is_active = '1' and status = 'Approved' and is_paid = '1') E 
                on (D.gi_go_id = E.ref_id) 
                order by UNIX_TIMESTAMP(D.updated_date) desc) F";
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
        $from_state = $request->post('from_state');
        $to_state = $request->post('to_state');

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
                'vendor_id'=>($vendor_id==''?null:$vendor_id),
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
                'warehouse_no'=>($warehouse_id==''?null:$warehouse_id),
                'from_state'=>$from_state,
                'to_state'=>$to_state,
                'entry_type'=>'Purchase'
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
                            $ledgerArray[$m]['ref_type']=='go_debit_details' && 
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
                                    'vendor_id'=>($vendor_id==''?null:$vendor_id),
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


        // $taxable_amount = $request->post('sales_taxable_amount');
        // $invoice_taxable_amount = $request->post('sales_invoice_taxable_amount');
        // $edited_taxable_amount = $request->post('sales_edited_taxable_amount');
        // $diff_taxable_amount = $request->post('sales_diff_taxable_amount');
        // $narration_taxable_amount = $request->post('sales_narration_taxable_amount');
        // $total_tax = $request->post('sales_total_tax');
        // $invoice_total_tax = $request->post('sales_invoice_total_tax');
        // $edited_total_tax = $request->post('sales_edited_total_tax');
        // $diff_total_tax = $request->post('sales_diff_total_tax');
        // $narration_total_tax = $request->post('sales_narration_total_tax');

        $vat_cst = $request->post('sales_vat_cst');
        $vat_percen = $request->post('sales_vat_percen');
        $sub_particular_cost = $request->post('sales_sub_particular_cost');
        $sub_particular_tax = $request->post('sales_sub_particular_tax');
        $sub_particular_cgst = $request->post('sales_sub_particular_cgst');
        $sub_particular_sgst = $request->post('sales_sub_particular_sgst');
        $sub_particular_igst = $request->post('sales_sub_particular_igst');
        $invoice_cost_acc_id = $request->post('sales_invoice_cost_acc_id');
        $invoice_cost_ledger_name = $request->post('sales_invoice_cost_ledger_name');
        $invoice_cost_ledger_code = $request->post('sales_invoice_cost_ledger_code');
        // $invoice_cost_voucher_id = $request->post('sales_invoice_cost_voucher_id');
        // $invoice_cost_ledger_type = $request->post('sales_invoice_cost_ledger_type');
        $invoice_tax_acc_id = $request->post('sales_invoice_tax_acc_id');
        $invoice_tax_ledger_name = $request->post('sales_invoice_tax_ledger_name');
        $invoice_tax_ledger_code = $request->post('sales_invoice_tax_ledger_code');
        // $invoice_tax_voucher_id = $request->post('sales_invoice_tax_voucher_id');
        // $invoice_tax_ledger_type = $request->post('sales_invoice_tax_ledger_type');
        $invoice_cgst_acc_id = $request->post('sales_invoice_cgst_acc_id');
        $invoice_cgst_ledger_name = $request->post('sales_invoice_cgst_ledger_name');
        $invoice_cgst_ledger_code = $request->post('sales_invoice_cgst_ledger_code');
        // $invoice_cgst_voucher_id = $request->post('sales_invoice_cgst_voucher_id');
        // $invoice_cgst_ledger_type = $request->post('sales_invoice_cgst_ledger_type');
        $invoice_sgst_acc_id = $request->post('sales_invoice_sgst_acc_id');
        $invoice_sgst_ledger_name = $request->post('sales_invoice_sgst_ledger_name');
        $invoice_sgst_ledger_code = $request->post('sales_invoice_sgst_ledger_code');
        // $invoice_sgst_voucher_id = $request->post('sales_invoice_sgst_voucher_id');
        // $invoice_sgst_ledger_type = $request->post('sales_invoice_sgst_ledger_type');
        $invoice_igst_acc_id = $request->post('sales_invoice_igst_acc_id');
        $invoice_igst_ledger_name = $request->post('sales_invoice_igst_ledger_name');
        $invoice_igst_ledger_code = $request->post('sales_invoice_igst_ledger_code');
        // $invoice_igst_voucher_id = $request->post('sales_invoice_igst_voucher_id');
        // $invoice_igst_ledger_type = $request->post('sales_invoice_igst_ledger_type');

        for($i=0; $i<count($vat_cst); $i++){
            $total_cost[$i] = $request->post('sales_total_cost_'.$i);
            $invoice_cost[$i] = $request->post('sales_invoice_cost_'.$i);
            $edited_cost[$i] = $request->post('sales_edited_cost_'.$i);
            $diff_cost[$i] = $request->post('sales_diff_cost_'.$i);
            $narration_cost[$i] = $request->post('sales_narration_cost_'.$i);

            $total_tax[$i] = $request->post('sales_total_tax_'.$i);
            $invoice_tax[$i] = $request->post('sales_invoice_tax_'.$i);
            $edited_tax[$i] = $request->post('sales_edited_tax_'.$i);
            $diff_tax[$i] = $request->post('sales_diff_tax_'.$i);
            $narration_tax[$i] = $request->post('sales_narration_tax_'.$i);

            $total_cgst[$i] = $request->post('sales_total_cgst_'.$i);
            $invoice_cgst[$i] = $request->post('sales_invoice_cgst_'.$i);
            $edited_cgst[$i] = $request->post('sales_edited_cgst_'.$i);
            $diff_cgst[$i] = $request->post('sales_diff_cgst_'.$i);
            $narration_cgst[$i] = $request->post('sales_narration_cgst_'.$i);

            $total_sgst[$i] = $request->post('sales_total_sgst_'.$i);
            $invoice_sgst[$i] = $request->post('sales_invoice_sgst_'.$i);
            $edited_sgst[$i] = $request->post('sales_edited_sgst_'.$i);
            $diff_sgst[$i] = $request->post('sales_diff_sgst_'.$i);
            $narration_sgst[$i] = $request->post('sales_narration_sgst_'.$i);

            $total_igst[$i] = $request->post('sales_total_igst_'.$i);
            $invoice_igst[$i] = $request->post('sales_invoice_igst_'.$i);
            $edited_igst[$i] = $request->post('sales_edited_igst_'.$i);
            $diff_igst[$i] = $request->post('sales_diff_igst_'.$i);
            $narration_igst[$i] = $request->post('sales_narration_igst_'.$i);
        }

        $other_charges_acc_id = $request->post('sales_other_charges_acc_id');
        $other_charges_ledger_name = $request->post('sales_other_charges_ledger_name');
        $other_charges_ledger_code = $request->post('sales_other_charges_ledger_code');
        // $other_charges_voucher_id = $request->post('sales_other_charges_voucher_id');
        // $other_charges_ledger_type = $request->post('sales_other_charges_ledger_type');
        $other_charges = $request->post('sales_other_charges');
        $invoice_other_charges = $request->post('sales_invoice_other_charges');
        $edited_other_charges = $request->post('sales_edited_other_charges');
        $diff_other_charges = $request->post('sales_diff_other_charges');
        $narration_other_charges = $request->post('sales_narration_other_charges');

        $total_amount_acc_id = $request->post('sales_total_amount_acc_id');
        $total_amount_ledger_name = $request->post('sales_total_amount_ledger_name');
        $total_amount_ledger_code = $request->post('sales_total_amount_ledger_code');
        $total_amount_voucher_id = $request->post('sales_total_amount_voucher_id');
        $total_amount_ledger_type = $request->post('sales_total_amount_ledger_type');
        $total_amount = $request->post('sales_total_amount');
        $invoice_total_amount = $request->post('sales_invoice_total_amount');
        $edited_total_amount = $request->post('sales_edited_total_amount');
        $diff_total_amount = $request->post('sales_diff_total_amount');
        $narration_total_amount = $request->post('sales_narration_total_amount');
        $edited_total_payable_amount = $request->post('sales_edited_total_payable_amount');

        $particular = array();
        $sub_particular_val = array();
        $acc_id = array();
        $ledger_name = array();
        $ledger_code = array();
        $voucher_id = array();
        $ledger_type = '';
        $vat_cst_val = array();
        $vat_percen_val = array();
        $invoice_no_val = array();
        $total_val = array();
        $invoice_val = array();
        $edited_val = array();
        $difference_val = array();
        $narration_val = array();

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
        $bulkInsertArray2 = array();
        $grnAccEntries2 = array();
        $ledgerArray2 = array();
        $j = 0;
        $k = 0;
        $l = 0;

        for($i=0; $i<count($particular); $i++){
            $bulkInsertArray2[$j]=[
                'gi_go_id'=>$gi_id,
                'vendor_id'=>($vendor_id==''?null:$vendor_id),
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
                'warehouse_no'=>($warehouse_id==''?null:$warehouse_id),
                'from_state'=>$from_state,
                'to_state'=>$to_state,
                'entry_type'=>'Sales'
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
                    for($m=0; $m<count($ledgerArray2); $m++){
                        if($ledgerArray2[$m]['ref_id']==$gi_id && 
                            $ledgerArray2[$m]['ref_type']=='go_debit_details' && 
                            $ledgerArray2[$m]['entry_type']==$particular[$i] && 
                            $ledgerArray2[$m]['invoice_no']==$invoice_no_val[$i] && 
                            $ledgerArray2[$m]['vendor_id']==$vendor_id && 
                            $ledgerArray2[$m]['acc_id']==$acc_id[$i] && 
                            $ledgerArray2[$m]['voucher_id']==$voucher_id[$i] && 
                            $ledgerArray2[$m]['ledger_type']==$ledger_type[$i]){

                                $bl_flag = false;
                                $tot_amount = floatval($ledgerArray2[$m]['amount']);
                                $amount = floatval($mycomponent->format_number($edited_val[$i],2));
                                if($ledgerArray2[$m]['type']=="Debit"){
                                    $tot_amount = $tot_amount * -1;
                                }
                                if($ledg_type=="Debit"){
                                    $amount = $amount * -1;
                                }
                                $tot_amount = $tot_amount + $amount;

                                if($tot_amount<0){
                                    $ledgerArray2[$m]['type'] = "Debit";
                                    $tot_amount = $tot_amount * -1;
                                } else {
                                    $ledgerArray2[$m]['type'] = "Credit";
                                }

                                $ledgerArray2[$m]['amount'] = $tot_amount;
                        }
                    }

                    if($bl_flag == true){
                        // echo $particular[$i];
                        // echo '<br/>';
                        // echo $edited_val[$i];
                        // echo '<br/>';
                        // echo $mycomponent->format_number($edited_val[$i],2);
                        // echo '<br/>';
                        
                        $ledgerArray2[$k]=[
                                    'ref_id'=>$gi_id,
                                    'ref_type'=>'go_debit_details',
                                    'entry_type'=>$particular[$i],
                                    'invoice_no'=>$invoice_no_val[$i],
                                    'vendor_id'=>($vendor_id==''?null:$vendor_id),
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


        $sales_stock_transfer_acc_id = $request->post('sales_stock_transfer_acc_id');
        $sales_stock_transfer_ledger_name = $request->post('sales_stock_transfer_ledger_name');
        $sales_stock_transfer_ledger_code = $request->post('sales_stock_transfer_ledger_code');
        $sales_stock_transfer_voucher_id = $request->post('sales_stock_transfer_voucher_id');
        $sales_stock_transfer_ledger_type = $request->post('sales_stock_transfer_ledger_type');
        $sales_stock_transfer = $request->post('sales_stock_transfer');
        $invoice_sales_stock_transfer = $request->post('invoice_sales_stock_transfer');
        $edited_sales_stock_transfer = $request->post('edited_sales_stock_transfer');
        $diff_sales_stock_transfer = $request->post('diff_sales_stock_transfer');
        $narration_sales_stock_transfer = $request->post('narration_sales_stock_transfer');

        $purchase_stock_transfer_acc_id = $request->post('purchase_stock_transfer_acc_id');
        $purchase_stock_transfer_ledger_name = $request->post('purchase_stock_transfer_ledger_name');
        $purchase_stock_transfer_ledger_code = $request->post('purchase_stock_transfer_ledger_code');
        $purchase_stock_transfer_voucher_id = $request->post('purchase_stock_transfer_voucher_id');
        $purchase_stock_transfer_ledger_type = $request->post('purchase_stock_transfer_ledger_type');
        $purchase_stock_transfer = $request->post('purchase_stock_transfer');
        $invoice_purchase_stock_transfer = $request->post('invoice_purchase_stock_transfer');
        $edited_purchase_stock_transfer = $request->post('edited_purchase_stock_transfer');
        $diff_purchase_stock_transfer = $request->post('diff_purchase_stock_transfer');
        $narration_purchase_stock_transfer = $request->post('narration_purchase_stock_transfer');

        $particular = array();
        $sub_particular_val = array();
        $acc_id = array();
        $ledger_name = array();
        $ledger_code = array();
        $voucher_id = array();
        $ledger_type = '';
        $vat_cst_val = array();
        $vat_percen_val = array();
        $invoice_no_val = array();
        $total_val = array();
        $invoice_val = array();
        $edited_val = array();
        $difference_val = array();
        $narration_val = array();

        $num = 0;

        for($i=0; $i<count($invoice_no); $i++){
            $particular[$num] = "Sales Stock Transfer";
            $sub_particular_val[$num] = null;
            $acc_id[$num] = $sales_stock_transfer_acc_id;
            $ledger_name[$num] = $sales_stock_transfer_ledger_name;
            $ledger_code[$num] = $sales_stock_transfer_ledger_code;
            $voucher_id[$num] = $sales_stock_transfer_voucher_id[$i];
            $ledger_type[$num] = 'Main Entry';
            $vat_cst_val[$num] = null;
            $vat_percen_val[$num] = null;
            $invoice_no_val[$num] = $invoice_no[$i];
            $total_val[$num] = $sales_stock_transfer;
            $invoice_val[$num] = $invoice_sales_stock_transfer[$i];
            $edited_val[$num] = $edited_sales_stock_transfer[$i];
            $difference_val[$num] = $diff_sales_stock_transfer[$i];
            $narration_val[$num] = $narration_sales_stock_transfer;
            $num = $num + 1;

            $particular[$num] = "Purchase Stock Transfer";
            $sub_particular_val[$num] = null;
            $acc_id[$num] = $purchase_stock_transfer_acc_id;
            $ledger_name[$num] = $purchase_stock_transfer_ledger_name;
            $ledger_code[$num] = $purchase_stock_transfer_ledger_code;
            $voucher_id[$num] = $purchase_stock_transfer_voucher_id[$i];
            $ledger_type[$num] = 'Main Entry';
            $vat_cst_val[$num] = null;
            $vat_percen_val[$num] = null;
            $invoice_no_val[$num] = $invoice_no[$i];
            $total_val[$num] = $purchase_stock_transfer;
            $invoice_val[$num] = $invoice_purchase_stock_transfer[$i];
            $edited_val[$num] = $edited_purchase_stock_transfer[$i];
            $difference_val[$num] = $diff_purchase_stock_transfer[$i];
            $narration_val[$num] = $narration_purchase_stock_transfer;
            $num = $num + 1;
        }

        // echo count($particular);
        // echo '<br/>';
        $bulkInsertArray3 = array();
        $grnAccEntries3 = array();
        $ledgerArray3 = array();
        $j = 0;
        $k = 0;
        $l = 0;

        for($i=0; $i<count($particular); $i++){
            $bulkInsertArray3[$j]=[
                'gi_go_id'=>$gi_id,
                'vendor_id'=>($vendor_id==''?null:$vendor_id),
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
                'warehouse_no'=>($warehouse_id==''?null:$warehouse_id),
                'from_state'=>$from_state,
                'to_state'=>$to_state,
                'entry_type'=>'Stock Transfer'
            ];

            $j = $j + 1;

            $ledg_particular = $particular[$i];
            if($mycomponent->format_number($edited_val[$i],2)!=0){
                if($ledg_particular=="Sales Stock Transfer"){
                    $ledg_type = "Debit";
                    // $type = "Goods Purchase";
                    // $legal_name = $particular[$i];
                    // $code = $sub_particular_val[$i];
                } else if($ledg_particular=="Purchase Stock Transfer"){
                    $ledg_type = "Credit";
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
                    for($m=0; $m<count($ledgerArray3); $m++){
                        if($ledgerArray3[$m]['ref_id']==$gi_id && 
                            $ledgerArray3[$m]['ref_type']=='go_debit_details' && 
                            $ledgerArray3[$m]['entry_type']==$particular[$i] && 
                            $ledgerArray3[$m]['invoice_no']==$invoice_no_val[$i] && 
                            $ledgerArray3[$m]['vendor_id']==$vendor_id && 
                            $ledgerArray3[$m]['acc_id']==$acc_id[$i] && 
                            $ledgerArray3[$m]['voucher_id']==$voucher_id[$i] && 
                            $ledgerArray3[$m]['ledger_type']==$ledger_type[$i]){

                                $bl_flag = false;
                                $tot_amount = floatval($ledgerArray3[$m]['amount']);
                                $amount = floatval($mycomponent->format_number($edited_val[$i],2));
                                if($ledgerArray3[$m]['type']=="Debit"){
                                    $tot_amount = $tot_amount * -1;
                                }
                                if($ledg_type=="Debit"){
                                    $amount = $amount * -1;
                                }
                                $tot_amount = $tot_amount + $amount;

                                if($tot_amount<0){
                                    $ledgerArray3[$m]['type'] = "Debit";
                                    $tot_amount = $tot_amount * -1;
                                } else {
                                    $ledgerArray3[$m]['type'] = "Credit";
                                }

                                $ledgerArray3[$m]['amount'] = $tot_amount;
                        }
                    }

                    if($bl_flag == true){
                        // echo $particular[$i];
                        // echo '<br/>';
                        // echo $edited_val[$i];
                        // echo '<br/>';
                        // echo $mycomponent->format_number($edited_val[$i],2);
                        // echo '<br/>';
                        
                        $ledgerArray3[$k]=[
                                    'ref_id'=>$gi_id,
                                    'ref_type'=>'go_debit_details',
                                    'entry_type'=>$particular[$i],
                                    'invoice_no'=>$invoice_no_val[$i],
                                    'vendor_id'=>($vendor_id==''?null:$vendor_id),
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

        $data['bulkInsertArray2'] = $bulkInsertArray2;
        $data['grnAccEntries2'] = $grnAccEntries2;
        $data['ledgerArray2'] = $ledgerArray2;

        $data['bulkInsertArray3'] = $bulkInsertArray3;
        $data['grnAccEntries3'] = $grnAccEntries3;
        $data['ledgerArray3'] = $ledgerArray3;

        return $data;
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

    public function setLog($module_name, $sub_module, $action, $vendor_id, $description, $table_name, $table_id) {
        $session = Yii::$app->session;
        $curusr = $session['session_id'];
        $company_id = $session['company_id'];
        $now = date('Y-m-d H:i:s');

        $array = array('module_name' => $module_name, 
                        'sub_module' => $sub_module, 
                        'action' => $action, 
                        'vendor_id' => ($vendor_id==''?null:$vendor_id), 
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

    public function getGrnAccEntries($id){
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select A.*, B.*, A.vendor_id as vendor_id1 from (
                select A.*,Case When A.warehouse_state=B.to_state Then 'INTRA' Else 'INTER' end as vat_cst,
                    Case When warehouse_state=B.to_state Then 'Same States' Else 'Different States' end as tax_zone_name,
                    Case When A.warehouse_state=B.to_state Then 'INTRA' Else 'INTER' end as tax_zone_code, 
                    E.idt_warehouse, E.warehouse_id,B.to_state 
                from goods_inward_outward A 
                left join 
                (select (Case When type_outward='VENDOR' Then vendor_state When type_outward='INTER-DEPOT' Then idt_warehouse_state end) as to_state, 
                        gi_go_id from goods_inward_outward) B 
                on (A.gi_go_id = B.gi_go_id) 
                left join 
                (select distinct warehouse_name as idt_warehouse,id as warehouse_id, warehouse_code from internal_warehouse_master) E 
                on (A.idt_warehouse_code = E.warehouse_code) 
                where date(A.gi_go_date_time) > date('2018-03-31') and A.warehouse_state<>A.idt_warehouse_state and 
                    A.company_id='$company_id' and A.inward_outward = 'outward' and A.type_outward = 'INTER-DEPOT') A 
                left join 
                (select * from acc_go_debit_entries Where gi_go_id='$id' and status = 'approved' and is_active = '1' 
                        order by gi_go_id, invoice_no, id, vat_percen, vat_cst) B 
                on (A.gi_go_id = B.gi_go_id)
                where B.id is not null
                order by B.invoice_no, B.id";
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
                    A.*, A.vendor_id as vendor_id1,H.to_state,E.idt_warehouse,E.warehouse_id
                from goods_inward_outward A
                left join 
                (select (Case When type_outward='VENDOR' Then vendor_state When type_outward='INTER-DEPOT' 
                            Then idt_warehouse_state end) as to_state,gi_go_id from  goods_inward_outward ) H 
                on(A.gi_go_id = H.gi_go_id) 
                left join 
                (select distinct warehouse_name as idt_warehouse,id as warehouse_id, warehouse_code from internal_warehouse_master) E
                on (A.idt_warehouse_code = E.warehouse_code)
                left join 
                acc_go_debit_entries C on (A.gi_go_id = C.gi_go_id and C.status = 'approved' and C.is_active='1' and 
                C.particular = 'Total Amount')
                where  date(A.gi_go_date_time) > date('2018-03-31') and A.company_id='$company_id' and 
                    A.warehouse_state<>A.idt_warehouse_state and A.company_id='$company_id' and 
                    A.inward_outward='OUTWARD' and A.type_outward = 'INTER-DEPOT' and A.gi_go_id='$id') A";         
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getGrnPostingDetails($id ,$skuentries){
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        if($skuentries)
            $fromtable = 'acc_go_debit_sku_items';
        else
            $fromtable = 'prepare_go_items';

        $sql="select AA.*,BB.tax_zone_code, BB.tax_zone_name, BB.cgst_rate, BB.sgst_rate, BB.igst_rate, 
                ifnull(round((value_at_cost),0) ,2)as total_cost, 
                ifnull(round(AA.value_at_cost*AA.vat_percent/100,2),0) as total_tax, 
                ifnull(round(AA.value_at_cost*BB.cgst_rate/100,2),0) as total_cgst, 
                ifnull(round(AA.value_at_cost*BB.sgst_rate/100,2),0) as total_sgst, 
                ifnull(round(AA.value_at_cost*BB.igst_rate/100,2),0) as total_igst, 
                (ifnull(round(AA.value_at_cost*AA.vat_percent/100,2),0)+value_at_cost) as total_amount, 
                per_unit as per_unit_exc_tax from 
            (select A.*, A.idt_warehouse_state as to_state, 
                Case When A.warehouse_state=A.idt_warehouse_state Then 'INTRA' Else 'INTER' end as vat_cst, 
                E.prepare_go_id, E.from_warehouse_name, E.destination_warehouse_company_name, E.total_qty, E.invoice_number, 
                B.cost as per_unit, B.product_title, B.mrp, B.psku, B.fnsku, B.hsn_code, B.batch_code, B.asin, 
                B.expiry_date, B.ean, B.sku_code, B.grn_no, B.shipment_plan_name, B.isa, B.po_no, B.go_no, 
                B.grn_entries_id, B.product_id, B.is_combo_items, B.order_qty, B.manual_discount, B.value_at_mrp, 
                B.vat_percent, B.quantity as invoice_qty, B.value_at_cost as cost_excl_vat 
            from goods_inward_outward A 
            left join prepare_go E on (A.pre_go_ref=E.prepare_go_id)
            left join (select * from acc_go_debit_sku_items Union select * from prepare_go_items) B on (A.pre_go_ref=B.prepare_go_id) 
            left join grn C on (B.grn_no=C.gi_id) 
            left join acc_grn_sku_entries D on (C.grn_id=D.grn_id and B.psku=D.psku) 
            where A.is_active = '1' and A.company_id='$company_id' and A.gi_go_id='$id' and 
                A.warehouse_state<>A.idt_warehouse_state and A.inward_outward = 'outward' and 
                date(A.gi_go_date_time) > date('2018-03-31') and A.type_outward = 'INTER-DEPOT' and D.id is null) AA 
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
                left join tax_type_master E on (D.tax_type_id = E.id) ) C
            where child_tax_rate != 0
            group by id, tax_zone_code, tax_zone_name, parent_id, tax_rate) BB
            on (AA.vat_cst COLLATE utf8_unicode_ci = BB.tax_zone_code and round(AA.vat_percent,4)=round(BB.tax_rate,4))
            Where AA.gi_go_id='$id' Order By vat_percent ASC;";
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

        $sql = "select AA.*,BB.tax_zone_code, BB.tax_zone_name, BB.cgst_rate, BB.sgst_rate, BB.igst_rate, 
                ifnull(round((value_at_cost),0) ,2)as total_cost,
                ifnull(round(AA.value_at_cost*AA.vat_percent/100,2),0) as total_tax, 
                ifnull(round(AA.value_at_cost*BB.cgst_rate/100,2),0) as total_cgst, 
                ifnull(round(AA.value_at_cost*BB.sgst_rate/100,2),0) as total_sgst, 
                ifnull(round(AA.value_at_cost*BB.igst_rate/100,2),0) as total_igst, 
                (ifnull(round(AA.value_at_cost*AA.vat_percent/100,2),0)+value_at_cost) as total_amount, per_unit as per_unit_exc_tax from 
            (select A.*, A.idt_warehouse_state as to_state, 
                Case When A.warehouse_state=A.idt_warehouse_state Then 'INTRA' Else 'INTER' end as vat_cst, 
                E.prepare_go_id, E.from_warehouse_name, E.destination_warehouse_company_name, E.total_qty, E.invoice_number, 
                B.cost as per_unit, B.product_title, B.mrp, B.psku, B.fnsku, B.hsn_code, B.batch_code, B.asin, 
                B.expiry_date, B.ean, B.sku_code, B.grn_no, B.shipment_plan_name, B.isa, B.po_no, B.go_no, 
                B.grn_entries_id, B.product_id, B.is_combo_items, B.order_qty, B.manual_discount, B.value_at_mrp, 
                B.vat_percent, B.quantity as invoice_qty, B.value_at_cost as cost_excl_vat 
            from goods_inward_outward A 
            left join prepare_go E on (A.pre_go_ref=E.prepare_go_id)
            left join $fromtable B on (A.pre_go_ref=B.prepare_go_id) 
            left join grn C on (B.grn_no=C.gi_id) 
            left join acc_grn_sku_entries D on (C.grn_id=D.grn_id and B.psku=D.psku) 
            where A.is_active = '1' and A.company_id='$company_id' and A.warehouse_state<>A.idt_warehouse_state and 
                A.inward_outward = 'outward' and date(A.gi_go_date_time) > date('2018-03-31') and 
                A.type_outward = 'INTER-DEPOT' and D.id is null) AA 
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
                where  date(A.gi_go_date_time) > date('2018-03-31') and A.company_id='$company_id'
                AND A.inward_outward = 'outward' and A.type_outward = 'INTER-DEPOT') A
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

       $ded_type = 'gointerdepot';
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

       // echo count($company_id);
       // echo '<br/>';
       // echo json_encode($company_id);
       // echo '<br/>';
       // echo count($hsn_code);
       // echo '<br/>';
       // echo json_encode($hsn_code);
       // echo '<br/>';
       // echo count($cost);
       // echo '<br/>';
       // echo json_encode($cost);
       // echo '<br/>';
       // echo count($vat_percent);
       // echo '<br/>';
       // echo json_encode($vat_percent);
       // echo '<br/>';
       // echo count($ean);
       // echo '<br/>';
       // echo json_encode($ean);
       // echo '<br/>';

       $bulkInsertArray = array();

       for($i=0; $i<count($company_id); $i++){
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
                                'product_id'=>$product_id[$i],
                                'bucket_name'=>$bucket_name[$i],
                                'prepare_go_id'=>$prepare_go_id[$i],
                                'company_id'=>$company_id[$i],
                                'created_date'=>$created_date[$i],
                                'updated_date'=>$updated_date[$i],
                                'is_active'=>$is_active[$i],
                                'is_combo_items'=>$is_combo_items[$i],
                                'order_qty'=>($order_qty[$i]==''?null:$order_qty[$i]),
                                'order_id'=>$order_id[$i],
                                ];
        }

        // echo count($bulkInsertArray);
        // echo '<br/>';
        // echo json_encode($bulkInsertArray);
        // echo '<br/>';

        if(count($bulkInsertArray)>0){
            $sql = "delete from acc_go_debit_sku_items where prepare_go_id = '$prepare_go_id[0]'";
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