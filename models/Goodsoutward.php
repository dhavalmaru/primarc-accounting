<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\Session;
use mPDF;

class GoodsOutward extends Model
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
           $search_value1 =  $request->post('search');
           $search = $search_value1['value'];
            if($search!="") {
                $wheresearch = " and (C.gi_go_id like '%$search%' or C.gi_go_ref_no like '%$search%' or 
                                C.warehouse_name like '%$search%' or C.vendor_name like '%$search%' or 
                                C.idt_warehouse_name like like '%$search%' or 
                                C.total_quantity like '%$search%' or C.value_at_cost like '%$search%' or 
                                C.gi_go_date_time like '%$search%' or C.updated_by like '%$search%') ";
            } 
        }

        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select * from 
                (select A.*, B.gi_go_id as b_gi_go_id from 
                (select A.* from goods_inward_outward A where A.is_active = '1' and A.company_id='$company_id' and 
                    A.inward_outward = 'outward' and date(A.gi_go_date_time) > date('2017-07-01')) A 
                left join 
                (select distinct gi_go_id from acc_go_debit_details) B 
                on (A.gi_go_id = B.gi_go_id)) C 
                where C.b_gi_go_id is null ".$wheresearch." order by UNIX_TIMESTAMP(C.updated_date) desc ".$len;
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
                                C.idt_warehouse_name like like '%$search%' or 
                                C.total_quantity like '%$search%' or C.value_at_cost like '%$search%' or 
                                C.gi_go_date_time like '%$search%' or C.updated_by like '%$search%') ";
            } 
        }

        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select count(*) as count from 
                (select A.*, B.gi_go_id as b_gi_go_id from 
                (select A.* from goods_inward_outward A where is_active = '1' and A.company_id='$company_id' and 
                    A.inward_outward = 'outward' and date(A.gi_go_date_time) > date('2017-07-01')) A 
                left join 
                (select distinct gi_go_id from acc_go_debit_details) B 
                on (A.gi_go_id = B.gi_go_id)) C 
                where C.b_gi_go_id is null ".$wheresearch." order by UNIX_TIMESTAMP(C.updated_date) desc ";
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
                $wheresearch = " where (C.gi_go_id like '%$search%' or C.gi_go_ref_no like '%$search%' or 
                                C.warehouse_name like '%$search%' or C.vendor_name like '%$search%' or 
                                C.idt_warehouse_name like like '%$search%' or 
                                C.debit_amt like '%$search%' or C.credit_amt like '%$search%' or 
                                C.updated_date like '%$search%' or C.username like '%$search%') ";
            } 
        }

        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select C.*, D.is_paid from 
                (select A.*, B.gi_go_ref_no, B.warehouse_name, B.vendor_name, B.idt_warehouse_name, B.total_quantity, C.username
                from acc_go_debit_details A 
                    left join goods_inward_outward B on (A.gi_go_id=B.gi_go_id) 
                    left join user C on(A.updated_by = C.id) 
                where A.is_active = '1' and A.company_id='$company_id' and B.is_active = '1' and B.company_id='$company_id') C 
                left join 
                (select distinct ref_id, is_paid from acc_ledger_entries 
                    where ref_type = 'go_debit_details' and company_id='$company_id' and is_active = '1' and status = 'Approved' and is_paid = '1') D 
                on (C.gi_go_id = D.ref_id) ".$wheresearch." 
                order by UNIX_TIMESTAMP(C.updated_date) desc ".$len;
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
                $wheresearch = " where (C.gi_go_id like '%$search%' or C.gi_go_ref_no like '%$search%' or 
                                C.warehouse_name like '%$search%' or C.vendor_name like '%$search%' or 
                                C.idt_warehouse_name like like '%$search%' or 
                                C.debit_amt like '%$search%' or C.credit_amt like '%$search%' or 
                                C.updated_date like '%$search%' or C.username like '%$search%') ";
            } 
        }

        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select count(*) as count from 
                (select A.*, B.gi_go_ref_no, B.warehouse_name, B.vendor_name, B.idt_warehouse_name, B.total_quantity, C.username 
                from acc_go_debit_details A 
                    left join goods_inward_outward B on (A.gi_go_id=B.gi_go_id) 
                    left join user C on(A.updated_by = C.id) 
                where A.is_active = '1' and A.company_id='$company_id' and B.is_active = '1' and B.company_id='$company_id') C 
                left join 
                (select distinct ref_id, is_paid from acc_ledger_entries 
                    where ref_type = 'go_debit_details' and company_id='$company_id' and is_active = '1' and status = 'Approved' and is_paid = '1') D 
                on (C.gi_go_id = D.ref_id) ".$wheresearch." 
                order by UNIX_TIMESTAMP(C.updated_date) desc";
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
                                C.idt_warehouse_name like like '%$search%' or 
                                C.total_quantity like '%$search%' or C.value_at_cost like '%$search%' or 
                                C.gi_go_date_time like '%$search%' or C.updated_by like '%$search%') ";
            } 
        }

        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select C.*, D.is_paid from 
                (select A.*, B.gi_go_id as b_gi_go_id, B.status as go_debit_status from 
                (select A.* from goods_inward_outward A where is_active = '1' and A.company_id='$company_id' and 
                    A.inward_outward = 'outward' and date(A.gi_go_date_time) > date('2017-07-01')) A 
                left join 
                (select distinct gi_go_id, status from acc_go_debit_details) B 
                on (A.gi_go_id = B.gi_go_id)) C 
                left join 
                (select distinct ref_id, is_paid from acc_ledger_entries 
                    where ref_type = 'go_debit_details' and is_active = '1' and status = 'Approved' and is_paid = '1') D 
                on (C.gi_go_id = D.ref_id) ".$wheresearch." 
                order by UNIX_TIMESTAMP(C.updated_date) desc ".$len;
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
                                C.idt_warehouse_name like like '%$search%' or 
                                C.total_quantity like '%$search%' or C.value_at_cost like '%$search%' or 
                                C.gi_go_date_time like '%$search%' or C.updated_by like '%$search%') ";
            } 
        }

        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select count(*) as count from 
                (select A.*, B.gi_go_id as b_gi_go_id, B.status as go_debit_status from 
                (select A.* from goods_inward_outward A where is_active = '1' and A.company_id='$company_id' and 
                    A.inward_outward = 'outward' and date(A.gi_go_date_time) > date('2017-07-01')) A 
                left join 
                (select distinct gi_go_id, status from acc_go_debit_details) B 
                on (A.gi_go_id = B.gi_go_id)) C 
                left join 
                (select distinct ref_id, is_paid from acc_ledger_entries 
                    where ref_type = 'go_debit_details' and is_active = '1' and status = 'Approved' and is_paid = '1') D 
                on (C.gi_go_id = D.ref_id) ".$wheresearch." 
                order by UNIX_TIMESTAMP(C.updated_date) desc";
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

    public function getPostedGoDetails($id=""){
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select C.*, D.is_paid from 
                (select A.*, B.gi_go_ref_no, B.warehouse_name, B.warehouse_state, B.vendor_id, B.vendor_name, B.idt_warehouse_name, 
                    B.total_quantity, B.type_outward, B.customerName, C.username
                from acc_go_debit_details A 
                    left join goods_inward_outward B on (A.gi_go_id=B.gi_go_id) 
                    left join user C on(A.updated_by = C.id) 
                where A.gi_go_id = '$id' and A.is_active = '1' and A.company_id='$company_id' and B.is_active = '1' and B.company_id='$company_id') C 
                left join 
                (select distinct ref_id, is_paid from acc_ledger_entries 
                    where ref_type = 'go_debit_details' and company_id='$company_id' and is_active = '1' and status = 'Approved' and is_paid = '1') D 
                on (C.gi_go_id = D.ref_id) 
                order by UNIX_TIMESTAMP(C.updated_date) desc";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

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
            $sql = "select * from acc_series_master where type = 'Voucher' and company_id = '$company_id'";
            $command = Yii::$app->db->createCommand($sql);
            $reader = $command->query();
            $data = $reader->readAll();
            if (count($data)>0){
                $series = intval($data[0]['series']) + 1;

                $sql = "update acc_series_master set series = '$series' where type = 'Voucher' and company_id = '$company_id'";
                $command = Yii::$app->db->createCommand($sql);
                $count = $command->execute();
            } else {
                $series = 1;

                $sql = "insert into acc_series_master (type, series, company_id) values ('Voucher', '".$series."', '".$company_id."')";
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
}