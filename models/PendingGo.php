<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\Session;
use mPDF;

class PendingGo extends Model
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

    public function getNewGoDetails(){
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
                    $wheresearch = " and (A.gi_go_id like '%$search%' or A.gi_go_ref_no like '%$search%' or 
                        A.warehouse_name like '%$search%' or A.warehouse_name like '%$search%' or 
                        A.customerName like '%$search%' or A.value_at_cost like '%$search%' or 
                        A.gi_go_date_time like '%$search%' or A.gi_go_status like '%$search%' or 
                        A.updated_by  like '%$search%' or invoice_number  like '%$search%' )";
            } 
        }

        $company_id = $session['company_id'];

        $sql = "select A.* from 
                (select A.*, B.*, C.dcno from 
                (select A.*, B.username from goods_inward_outward A left join user B on(A.updated_by_id = B.id) 
                where date(A.gi_go_date_time) > date('2018-03-31') and A.company_id='$company_id' and 
                    A.type_outward='CUSTOMER' and A.inward_outward='OUTWARD' and A.customer_type='B2B' and 
                    A.gi_go_status='COMPLETE') A 
                left join 
                (select prepare_go_id, from_warehouse_name, destination_warehouse_company_name, total_qty, 
                    invoice_number from prepare_go Where company_id='1') B 
                on (A.pre_go_ref=B.prepare_go_id) 
                left join 
                (select distinct delivery_challan_no as dcno,prepare_go_id from delivery_challan) C 
                on (A.pre_go_ref = C.prepare_go_id)) A 
                left join 
                (select distinct gi_go_id from acc_go_entries) B 
                on (A.gi_go_id = B.gi_go_id) 
                Where B.gi_go_id IS  NULL".$wheresearch.$len;
                
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
                    $wheresearch = " and (A.gi_go_id like '%$search%' or A.gi_go_ref_no like '%$search%' or A.warehouse_name like '%$search%' or A.warehouse_name like '%$search%' or  A.customerName like '%$search%' or A.value_at_cost like '%$search%' or 
                        A.gi_go_date_time like '%$search%' or
                         A.gi_go_status like '%$search%' or A.updated_by  like '%$search%' or invoice_number  like '%$search%')";
            } 
        }

        $company_id = $session['company_id'];

        $sql = "Select count(*) as count from (Select A.*,B.* from 
                (select A.*, B.username from goods_inward_outward A left join user B on(A.updated_by_id = B.id) 
                where  date(A.gi_go_date_time) > date('2018-03-31') and A.company_id='$company_id'
                AND A.type_outward='CUSTOMER' AND A.inward_outward='OUTWARD' AND A.customer_type='B2B' AND A.gi_go_status='COMPLETE' AND 
                A.company_id='$company_id') A
                left join 
                (SELECT prepare_go_id,from_warehouse_name,destination_warehouse_company_name,total_qty,invoice_number from prepare_go Where company_id='1') B on
                A.pre_go_ref=B.prepare_go_id ) A 
                left join 
                (select distinct gi_go_id from acc_go_entries) B 
                on (A.gi_go_id = B.gi_go_id)
                Where B.gi_go_id IS  NULL ".$wheresearch;
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
                    $wheresearch = " and (A.gi_go_id like '%$search%' or A.gi_go_ref_no like '%$search%' or A.warehouse_name like '%$search%' or  A.customerName like '%$search%' or A.value_at_cost like '%$search%' or 
                        A.gi_go_date_time like '%$search%' or
                         A.gi_go_status like '%$search%' or A.updated_by  like '%$search%')";
            } 
        }

        $session = Yii::$app->session;
        $company_id = $session['company_id'];

       
        $sql = "Select A.*, D.is_paid,C.prepare_go_id from (Select A.* from 
                (select A.*, B.username from goods_inward_outward A left join user B on(A.updated_by_id = B.id) 
                where  date(A.gi_go_date_time) > date('2018-03-31') and A.company_id='$company_id'
                AND A.type_outward='CUSTOMER' AND A.inward_outward='OUTWARD' AND A.customer_type='B2B' AND A.gi_go_status='COMPLETE' AND 
                A.company_id='$company_id') A
                left join 
                (SELECT prepare_go_id,from_warehouse_name,destination_warehouse_company_name,total_qty,invoice_number from prepare_go Where company_id='1') B on
                A.pre_go_ref=B.prepare_go_id ) A
                left join 
                (select distinct delivery_challan_no as dcno,prepare_go_id from delivery_challan) C
                on (A.pre_go_ref = C.prepare_go_id)
                left join 
                (select distinct gi_go_id from acc_go_entries) B 
                on (A.gi_go_id = B.gi_go_id)
                left join 
                (select distinct ref_id, is_paid from acc_ledger_entries 
                    where ref_type = 'B2B Sales' and is_active = '1' and status = 'Approved' and is_paid = '1') D 
                on (A.gi_go_id = D.ref_id)".$wheresearch.$len;

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
                    $wheresearch = " and (A.gi_go_id like '%$search%' or A.gi_go_ref_no like '%$search%'  or A.warehouse_name like '%$search%' or  A.customerName like '%$search%' or A.value_at_cost like '%$search%' or 
                        A.gi_go_date_time like '%$search%' or
                         A.gi_go_status like '%$search%' or A.updated_by  like '%$search%')";
            } 
        }

        $session = Yii::$app->session;
        $company_id = $session['company_id'];

       
        $sql = "Select count(*) as count from (Select A.*,B.* from 
                (select A.*, B.username from goods_inward_outward A left join user B on(A.updated_by_id = B.id) 
                where  date(A.gi_go_date_time) > date('2018-03-31') and A.company_id='$company_id'
                AND A.type_outward='CUSTOMER' AND A.inward_outward='OUTWARD' AND A.customer_type='B2B' AND A.gi_go_status='COMPLETE' AND 
                A.company_id='$company_id') A
                left join 
                (SELECT prepare_go_id,from_warehouse_name,destination_warehouse_company_name,total_qty,invoice_number from prepare_go Where company_id='1') B on
                A.pre_go_ref=B.prepare_go_id ) A 
                left join 
                (select distinct gi_go_id from acc_go_entries) B 
                on (A.gi_go_id = B.gi_go_id)
                left join 
                (select distinct ref_id, is_paid from acc_ledger_entries 
                    where ref_type = 'purchase' and is_active = '1' and status = 'Approved' and is_paid = '1') D 
                on (A.gi_go_id = D.ref_id)".$wheresearch;
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        $result =  $reader->readAll();
        return $result[0]['count'];
    }

    public function getGrnDetails($id){
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        /*$sql = "select A.*, B.vendor_code, C.gi_date as grn_date from grn A left join vendor_master B on (A.vendor_id = B.id) 
                left join acc_grn_entries C on (A.grn_id = C.grn_id and C.status = 'approved' and C.is_active='1' and 
                C.particular = 'Total Amount') where A.grn_id = '$id' and A.status = 'approved' and A.is_active='1' and A.company_id='$company_id'";*/
        $sql = "Select A.* from (
                select Case When A.warehouse_state=A.customerState Then 'Same States' Else 'Different States' end as tax_zone_name,
                Case When A.warehouse_state=A.customerState Then 'INTRA' Else 'INTER' end as tax_zone_code, 
                A.*, B.customer_id as vendor_id1, D.dcno, E.invoice_created_date 
                from goods_inward_outward A left join prepare_go E on (A.pre_go_ref=E.prepare_go_id) 
                left join 
                (select distinct delivery_challan_no as dcno,prepare_go_id from delivery_challan) D on (A.pre_go_ref = D.prepare_go_id)
                left join 
                (select * from acc_master Where company_id='$company_id') B on trim(A.customerName)=trim(B.legal_name)
                left join 
                acc_go_entries C on (A.gi_go_id = C.gi_go_id and C.status = 'approved' and 
                                    C.is_active='1' and C.particular = 'Total Amount')
                where date(A.gi_go_date_time) > date('2018-03-31') and A.company_id='$company_id'
                AND A.type_outward='CUSTOMER' AND A.inward_outward='OUTWARD' AND A.customer_type='B2B' AND 
                A.gi_go_status='COMPLETE' AND A.company_id='$company_id' AND A.gi_go_id=$id ) A";         
        
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getPurchaseDetails(){
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
                    $wheresearch = " and (A.gi_go_id like '%$search%' or A.gi_go_ref_no like '%$search%' or A.warehouse_name like '%$search%' or A.warehouse_name like '%$search%' or  A.customerName like '%$search%' or A.value_at_cost like '%$search%' or 
                        A.gi_go_date_time like '%$search%' or
                         A.gi_go_status like '%$search%' or A.updated_by  like '%$search%')";
            } 
        }

        $company_id = $session['company_id'];

        $sql = "Select * from (Select A.*,B.* from 
                (select A.*, B.username from goods_inward_outward A left join user B on(A.updated_by_id = B.id) 
                where  date(A.gi_go_date_time) > date('2018-03-31') and A.company_id='$company_id'
                AND A.type_outward='CUSTOMER' AND A.inward_outward='OUTWARD' AND A.customer_type='B2B' AND A.gi_go_status='COMPLETE' AND 
                A.company_id='$company_id') A
                left join 
                (SELECT prepare_go_id,from_warehouse_name,destination_warehouse_company_name,total_qty,invoice_number from prepare_go Where company_id='1') B on
                A.pre_go_ref=B.prepare_go_id ) A
                left join 
                (select distinct gi_go_id from acc_go_entries) B 
                on (A.gi_go_id = B.gi_go_id)
                left join 
                (select distinct delivery_challan_no as dcno,prepare_go_id from delivery_challan) D
                on (A.pre_go_ref = D.prepare_go_id)
                left join 
                (select distinct ref_id, is_paid from acc_ledger_entries where ref_type = 'B2B Sales' and is_active = '1' and status = 'Approved' and is_paid = '1') C
                on (A.gi_go_id = C.ref_id)
                Where B.gi_go_id IS  NOT NULL".$wheresearch.$len;
                
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getCountPurchaseDetails($status=""){

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
                    $wheresearch = " and (A.gi_go_id like '%$search%' or A.gi_go_ref_no like '%$search%' or A.warehouse_name like '%$search%' or A.warehouse_name like '%$search%' or  A.customerName like '%$search%' or A.value_at_cost like '%$search%' or 
                        A.gi_go_date_time like '%$search%' or
                         A.gi_go_status like '%$search%' or A.updated_by  like '%$search%')";
            } 
        }

        $company_id = $session['company_id'];

        $sql = "Select count(*) as count from (Select A.*,B.* from 
                (select A.*, B.username from goods_inward_outward A left join user B on(A.updated_by_id = B.id) 
                where  date(A.gi_go_date_time) > date('2018-03-31') and A.company_id='$company_id'
                AND A.type_outward='CUSTOMER' AND A.inward_outward='OUTWARD' AND A.customer_type='B2B' AND A.gi_go_status='COMPLETE' AND 
                A.company_id='$company_id') A
                left join 
                (SELECT prepare_go_id,from_warehouse_name,destination_warehouse_company_name,total_qty,invoice_number from prepare_go Where company_id='1') B on
                A.pre_go_ref=B.prepare_go_id ) A 
                left join 
                (select distinct gi_go_id from acc_go_entries) B 
                on (A.gi_go_id = B.gi_go_id)
                left join 
                (select distinct ref_id, is_paid from acc_ledger_entries where ref_type = 'purchase' and is_active = '1' and status = 'Approved' and is_paid = '1') C
                on (A.gi_go_id = C.ref_id)
                Where B.gi_go_id IS NOT  NULL ".$wheresearch;
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        $result =  $reader->readAll();
        return $result[0]['count'];
    }

    public function getGrnPostingDetails($id){
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        // $sql = "Select AA.*,BB.tax_zone_code, BB.tax_zone_name, BB.cgst_rate, BB.sgst_rate, BB.igst_rate, 
        //     ifnull(round((cost_excl_vat),0) ,2)as total_cost,
        //     ifnull(round(AA.cost_excl_vat*AA.vat_percent/100,2),0) as total_tax, 
        //     ifnull(round(AA.cost_excl_vat*BB.cgst_rate/100,2),0) as total_cgst, 
        //     ifnull(round(AA.cost_excl_vat*BB.sgst_rate/100,2),0) as total_sgst, 
        //     ifnull(round(AA.cost_excl_vat*BB.igst_rate/100,2),0) as total_igst, 
        //     ifnull((AA.value_incl_vat),0) as total_amount from (
        //      SELECT  A.*,
        //      B.value_at_cost as value_incl_vat,B.vat_percent,B.quantity as invoice_qty,
        //      ROUND(B.value_at_cost/(1+(B.vat_percent/100)),2) as cost_excl_vat from 
        //     (Select * from 
        //     (select A.*,Case When A.warehouse_state=A.customerState Then 'INTRA' Else 'INTER' end as vat_cst ,B.username from goods_inward_outward A left join user B on(A.updated_by_id = B.id) 
        //     where  date(A.gi_go_date_time) > date('2018-03-31') and A.company_id='$company_id'
        //     AND A.type_outward='CUSTOMER' AND A.inward_outward='OUTWARD' AND A.customer_type='B2B' AND A.gi_go_status='COMPLETE' AND 
        //     A.company_id='$company_id') A
        //     left join 
        //     (SELECT prepare_go_id,from_warehouse_name,destination_warehouse_company_name,total_qty,invoice_number,invoice_created_date from prepare_go Where company_id='1') B on
        //     A.pre_go_ref=B.prepare_go_id )A  
        //     left join
        //     (Select * from  prepare_go_items ) B on A.pre_go_ref=B.prepare_go_id 
        //     )AA
        //     left join
        //     (select id, tax_zone_code, tax_zone_name, parent_id, tax_rate, 
        //                         max(case when child_tax_type_code = 'CGST' then child_tax_rate else 0 end) as cgst_rate, 
        //                         max(case when child_tax_type_code = 'SGST' then child_tax_rate else 0 end) as sgst_rate, 
        //                         max(case when child_tax_type_code = 'IGST' then child_tax_rate else 0 end) as igst_rate from (select A.id, A.tax_zone_code, A.tax_zone_name, B.id as parent_id, B.tax_type_id as parent_tax_type_id, 
        //                         B.tax_rate, C.child_id, D.tax_type_id as child_tax_type_id, D.tax_rate as child_tax_rate, 
        //                         E.tax_category as child_tax_category, E.tax_type_code as child_tax_type_code, 
        //                         E.tax_type_name as child_tax_type_name 
        //                     from tax_zone_master A 
        //                         left join tax_rate_master B on (A.id = B.tax_zone_id) 
        //                         left join tax_component C on (B.id = C.parent_id) 
        //                         left join tax_rate_master D on (C.child_id = D.id) 
        //                         left join tax_type_master E on (D.tax_type_id = E.id) ) C
        //     where child_tax_rate != 0
        //     group by id, tax_zone_code, tax_zone_name, parent_id, tax_rate
        //     ) BB
        //     on (AA.vat_cst COLLATE utf8_unicode_ci = BB.tax_zone_code and round(AA.vat_percent,4)=round(BB.tax_rate,4))
        //     Where AA.gi_go_id=$id;";

        $sql = "select AA.*, BB.tax_zone_code, BB.tax_zone_name, BB.cgst_rate, BB.sgst_rate, BB.igst_rate, 
                    ifnull(round((cost_excl_vat),2) ,2)as total_cost,
                    ifnull(round(AA.cost_excl_vat*AA.vat_percent/100,2),0) as total_tax, 
                    ifnull(round(AA.cost_excl_vat*BB.cgst_rate/100,2),0) as total_cgst, 
                    ifnull(round(AA.cost_excl_vat*BB.sgst_rate/100,2),0) as total_sgst, 
                    ifnull(round(AA.cost_excl_vat*BB.igst_rate/100,2),0) as total_igst, 
                    ifnull((AA.value_incl_vat),0) as total_amount from 
                (select I.*, (cost_excl_vat+(cost_excl_vat*vat_percent/100)) as value_incl_vat from 
                (select G.*, H.purchase_additional_discount_percentage, 
                    (ifnull(G.quantity,0)*(ifnull(G.mrp,0)-((ifnull(G.mrp,0)*ifnull(H.purchase_additional_discount_percentage,0))/100))) as cost_excl_vat from 
                (select E.*, F.customer_order_Id from 
                (select C.*, D.mrp, D.vat_percent, D.quantity, D.psku, D.quantity as invoice_qty from 
                (select A.*, B.prepare_go_id, B.from_warehouse_name, B.destination_warehouse_company_name, B.total_qty, 
                    B.invoice_number, B.invoice_created_date from 
                (select A.*, Case When A.warehouse_state=A.customerState Then 'INTRA' Else 'INTER' end as vat_cst, B.username 
                from goods_inward_outward A left join user B on(A.updated_by_id = B.id) 
                where A.gi_go_id='$id' and date(A.gi_go_date_time) > date('2018-03-31') and A.company_id='$company_id' AND A.type_outward='CUSTOMER' AND 
                    A.inward_outward='OUTWARD' AND A.customer_type='B2B' AND A.gi_go_status='COMPLETE' AND A.company_id='$company_id') A 
                left join 
                (select prepare_go_id, from_warehouse_name, destination_warehouse_company_name, total_qty, invoice_number, invoice_created_date 
                from prepare_go where company_id='$company_id' and is_active='1') B 
                on (A.pre_go_ref=B.prepare_go_id)) C 
                left join 
                (select * from prepare_go_items where is_active='1') D 
                on (C.pre_go_ref=D.prepare_go_id)) E 
                left join 
                (select * from customer_order where company_id='$company_id' and is_active='1') F 
                on (E.order_id=F.co_no)) G 
                left join 
                (select * from customer_order_items) H 
                on (G.customer_order_Id=H.customer_order_Id and G.psku=H.psku)) I) AA 
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
                on (AA.vat_cst COLLATE utf8_unicode_ci = BB.tax_zone_code and round(AA.vat_percent,4)=round(BB.tax_rate,4))";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getMargin($psku, $vendor_id){
        $bl_flag = false;
        $margin_from_po = 0;

        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select * from product_master where is_active = '1' and sku_internal_code = '$psku' and 
                preferred_vendor_id = '$vendor_id' and company_id = '$company_id' and 
                id = (select max(id) from product_master where is_active = '1' and sku_internal_code = '$psku' and 
                        preferred_vendor_id = '$vendor_id' and company_id = '$company_id' and 
                        updated_at = (select max(updated_at) from product_master WHERE is_active = '1' and 
                        and company_id = '$company_id' and sku_internal_code = '$psku' and preferred_vendor_id = '$vendor_id'))";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        $result = $reader->readAll();

        if(count($result)>0){
            $bl_flag = true;
            $product_mrp = floatval($result[0]['product_mrp']);
            $landed_cost = floatval($result[0]['landed_cost']);
            if($product_mrp==0){
                $margin_from_po = 0;
            } else {
                $margin_from_po = intval((($product_mrp-$landed_cost)/$product_mrp*100)*100)/100;
            }
        }

        if($bl_flag == false){
            $sql = "select * from product_master where is_active = '1' and sku_internal_code = '$psku' and company_id = '$company_id' and 
                    id = (select max(id) from product_master where is_active = '1' and sku_internal_code = '$psku' and company_id = '$company_id' and 
                            updated_at = (select max(updated_at) from product_master WHERE is_active = '1' and 
                                company_id = '$company_id' and sku_internal_code = '$psku'))";
            $command = Yii::$app->db->createCommand($sql);
            $reader = $command->query();
            $result = $reader->readAll();

            if(count($result)>0){
                $bl_flag = true;
                $product_mrp = floatval($result[0]['product_mrp']);
                $landed_cost = floatval($result[0]['landed_cost']);
                if($product_mrp==0){
                    $margin_from_po = 0;
                } else {
                    $margin_from_po = intval((($product_mrp-$landed_cost)/$product_mrp*100)*100)/100;
                }
            }
        }

        return $margin_from_po;
    }

    public function getGrnAccEntries($id){
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "Select A.*, B.* from (
                select Case When warehouse_state=customerState Then 'Same States' Else 'Different States' end as tax_zone_name,
                Case When warehouse_state=customerState Then 'INTRA' Else 'INTER' end as tax_zone_code ,A.gi_go_id
                from goods_inward_outward A 
                where  date(A.gi_go_date_time) > date('2018-03-31') and A.company_id='$company_id'
                AND A.type_outward='CUSTOMER' AND A.inward_outward='OUTWARD' AND A.customer_type='B2B' AND A.gi_go_status='COMPLETE' AND 
                A.company_id='$company_id' AND A.gi_go_id=$id ) A
                Left Join
                (select * from acc_go_entries Where gi_go_id='$id' and status = 'approved' and is_active = '1' 
                        order by gi_go_id, invoice_no, id, vat_percen, vat_cst) B 
                on (A.gi_go_id = B.gi_go_id)
                where B.id is not null
                order by B.id";

        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getGrnAccSku($id){
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select * from acc_grn_sku_entries where grn_id = '$id' and is_active = '1' and company_id = '$company_id' order by invoice_no, vat_percen";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getGrnAccLedgerEntries($id){
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        /*$sql = "select B.*, A.tax_zone_code, A.tax_zone_name from 
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
                order by B.ref_id, B.invoice_no, B.id";*/

        $sql = "Select A.*,B.* from 
                (Select A.* ,B.* from 
                (select A.*,Case When warehouse_state=customerState Then 'Same States' Else 'Different States' end as tax_zone_name,
                                Case When warehouse_state=customerState Then 'INTRA' Else 'INTER' end as tax_zone_code  from goods_inward_outward A  
                where  date(A.gi_go_date_time) > date('2018-03-31') and A.company_id='$company_id'
                AND A.type_outward='CUSTOMER' AND A.inward_outward='OUTWARD' AND A.customer_type='B2B' AND A.gi_go_status='COMPLETE' AND 
                A.company_id='$company_id') A
                left join 
                (SELECT prepare_go_id,from_warehouse_name,destination_warehouse_company_name,total_qty,invoice_number,invoice_created_date from prepare_go Where company_id='$company_id') B 
                on  A.pre_go_ref=B.prepare_go_id ) A
                left join 
                (select * from acc_ledger_entries) B 
                on (A.gi_go_id = B.ref_id and B.ref_type = 'B2B Sales') 
                where B.ref_id = '$id'
                order by B.ref_id, B.invoice_no, B.id";

        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getGoParticulars(){
        $request = Yii::$app->request;
        $mycomponent = Yii::$app->mycomponent;
        $session = Yii::$app->session;

        $company_id = $session['company_id'];

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
                'company_id'=>$company_id
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
                            $ledgerArray[$m]['ref_type']=='B2B Sales' && 
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
                                    'ref_type'=>'B2B Sales',
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

        $code = $year . "/" . $month . "/" . $state_code;

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