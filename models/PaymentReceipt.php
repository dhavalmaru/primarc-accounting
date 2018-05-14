<?php

namespace app\models;

use Yii;
use yii\base\Model;
use mPDF;

class PaymentReceipt extends Model
{
    public function getAccess(){
        $session = Yii::$app->session;
        $session_id = $session['session_id'];
        $role_id = $session['role_id'];

        $sql = "select A.*, '".$session_id."' as session_id from acc_user_role_options A 
                where A.role_id = '$role_id' and A.r_section = 'S_Payment_Receipt'";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getApprover($action){
        $session = Yii::$app->session;
        $session_id = $session['session_id'];

        $cond = "";
        if($action!="authorise" && $action!="view"){
            $cond = " and A.id!='".$session_id."'";
        } 

        $sql = "select distinct A.id, A.username, C.r_approval from user A 
                left join acc_user_roles B on (A.id = B.user_id) 
                left join acc_user_role_options C on (B.role_id = C.role_id) 
                where C.r_section = 'S_Payment_Receipt' and 
                        C.r_approval = '1' and C.r_approval is not null" . $cond;
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getDetails($trans_id="", $status=""){
        $cond = "";
        if($trans_id!=""){
            $cond = " Where A.id = '$trans_id'";
        }
        if($status!=""){
            if($cond==""){
                $cond = " Where A.status = '$status'";
            } else {
                $cond = $cond . " and A.status = '$status'";
            }
        }

        $sql = "select A.*, B.username as updater, C.username as approver from acc_payment_receipt A 
                left join user B on (A.updated_by = B.id) 
                left join user C on (A.approved_by = C.id)" . $cond . " 
                order by UNIX_TIMESTAMP(A.updated_date) desc, A.id desc";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getAccountDetails($id=""){
        $cond = "";
        if($id!=""){
            $cond = " and id = '$id'";
        }

        $sql = "select * from acc_master where is_active = '1' and status = 'approved' and 
                type = 'Vendor Goods'".$cond." order by legal_name";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getVendors(){
        $sql = "select * from vendor_master where is_active = '1' and company_id = '1'";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getBanks($id=""){
        $cond = "";
        if($id!=""){
            $cond = " and id = '$id'";
        }

        $sql = "select * from acc_master where is_active = '1' and status = 'approved' and 
                type = 'Bank Account'".$cond." order by legal_name";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getLedger($id, $acc_id){
        $status = 'approved';

        $sql = "select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, A.type, A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, A.voucher_id, A.ledger_type, 
                    A.narration, A.ref_date, A.gi_date, A.invoice_date, A.due_date, A.cp_acc_id, A.cp_ledger_name, A.cp_ledger_code, 
                    case when B.id is not null then '1' else '' end as is_paid, B.ref_id as payment_ref from 
                (select * from 
                (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when B.cp_acc_id = '$acc_id' then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                    A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                    A.gi_date, A.invoice_date, A.due_date, 
                    B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                (select A.*, B.gi_date, C.invoice_date, D.due_date from acc_ledger_entries A 
                    left join grn B on(A.ref_id = B.grn_id and A.ref_type = 'purchase') 
                    left join goods_inward_outward_invoices C on(A.invoice_no = C.invoice_no and 
                     A.ref_type = 'purchase' and B.gi_id = C.gi_go_ref_no) 
                    left join invoice_tracker D on(A.ref_id=D.gi_id and A.invoice_no=D.invoice_id) 
                    where A.status = '$status' and A.is_active = '1' and B.status = 'Approved' and B.is_active = '1' and 
                          A.ref_type = 'purchase' and A.ledger_type != 'Main Entry') A 
                left join 
                (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                    ledger_code as cp_ledger_code from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    ref_type = 'purchase' and ledger_type = 'Main Entry') B 
                on (A.voucher_id = B.cp_voucher_id) 
                union all 
                select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount, A.status, 
                    A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                    null as gi_date, null as invoice_date, null as due_date, 
                    B.acc_id as cp_acc_id, B.ledger_name as cp_ledger_name, B.ledger_code as cp_ledger_code from 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    ref_type = 'journal_voucher' and acc_id!='$acc_id') A 
                left join 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    ref_type = 'journal_voucher' and acc_id='$acc_id') B 
                on (A.ref_id=B.ref_id) 
                union all 
                select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when B.cp_acc_id = '$acc_id' then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                    A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                    null as gi_date, null as invoice_date, null as due_date, 
                    B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    ref_type = 'payment_receipt' and ledger_type = 'Sub Entry') A 
                left join 
                (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                    ledger_code as cp_ledger_code from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    ref_type = 'payment_receipt' and ledger_type = 'Main Entry') B 
                on (A.voucher_id = B.cp_voucher_id)) AA 
                where (AA.acc_id = '$acc_id' or AA.cp_acc_id = '$acc_id') and AA.amount!=0 and 
                        (AA.ref_type!='payment_receipt' or AA.entry_type='Bank Entry' or AA.entry_type='Payment' or AA.entry_type='Receipt') and 
                        (AA.is_paid is null or AA.is_paid!='1' or AA.payment_ref='$id')) A 
                left join 
                (select * from acc_ledger_entries where is_active = '1' and ref_id = '$id' and ref_type = 'payment_receipt') B 
                on (A.id = B.sub_ref_id) 
                order by A.ref_date, A.id";
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

        $curusr = $session['session_id'];
        $now = date('Y-m-d H:i:s');

        $id = $request->post('id');
        $voucher_id = $request->post('voucher_id');
        $ledger_type = $request->post('ledger_type');
        $trans_type = $request->post('trans_type');
        $acc_id = $request->post('acc_id');
        $legal_name = $request->post('legal_name');
        $acc_code = $request->post('acc_code');
        $bank_id = $request->post('bank_id');
        $bank_name = $request->post('bank_name');
        $payment_type = $request->post('payment_type');
        $narration = $request->post('narration');
        $payment_date = $request->post('payment_date');
        $remarks = $request->post('remarks');
        $approver_id = $request->post('approver_id');
        
        if($payment_date==''){
            $payment_date=NULL;
        } else {
            $payment_date=$mycomponent->formatdate($payment_date);
        }
        $ref_no = $request->post('ref_no');

        $amount = 0;
        if($payment_type == "Adhoc"){
            $amount = $mycomponent->format_number($request->post('amount'),2);
        } else {
            $payable_debit_amt = $mycomponent->format_number($request->post('payable_debit_amt'),2);
            $payable_credit_amt = $mycomponent->format_number($request->post('payable_credit_amt'),2);

            if($payable_debit_amt>0){
                $amount = $payable_debit_amt*-1;
            } else {
                $amount = $payable_credit_amt;
            }
        }

        $transaction_id = "";

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
        
        $array=[
                'trans_type'=>$trans_type,
                'voucher_id' => $voucher_id, 
                'ledger_type' => 'Sub Entry', 
                'account_id'=>$acc_id,
                'account_name'=>$legal_name,
                'account_code'=>$acc_code,
                'bank_id'=>$bank_id,
                'bank_name'=>$bank_name,
                'payment_type'=>$payment_type,
                'amount'=>($amount<0?$amount*-1:$amount),
                'ref_no'=>$ref_no,
                'narration'=>$narration,
                'status'=>'pending',
                'is_active'=>'1',
                'updated_by'=>$curusr,
                'updated_date'=>$now,
                'payment_date'=>$payment_date,
                'approver_comments'=>$remarks,
                'approver_id'=>$approver_id
            ];

        if (isset($id) && $id!=""){
            $count = Yii::$app->db->createCommand()
                        ->update("acc_payment_receipt", $array, "id = '".$id."'")
                        ->execute();

            $this->setLog('PaymentReceipt', '', 'Save', '', 'Update Payment Receipt Details', 'acc_payment_receipt', $id);
        } else {
            $array['created_by']=$curusr;
            $array['created_date']=$now;

            $count = Yii::$app->db->createCommand()
                        ->insert("acc_payment_receipt", $array)
                        ->execute();
            $id = Yii::$app->db->getLastInsertID();

            $this->setLog('PaymentReceipt', '', 'Save', '', 'Insert Payment Receipt Details', 'acc_payment_receipt', $id);
        }

        if($payment_type == "Adhoc") {
            $data = $this->getAccountDetails($acc_id);
            if(count($data)>0){
                $vendor_id = $data[0]['vendor_id'];
            }
            if($trans_type="Payment"){
                $type = "Debit";
            } else {
                $type = "Credit";
            }

            $ledgerArray=[
                            'ref_id'=>$id,
                            'sub_ref_id'=>null,
                            'ref_type'=>'payment_receipt',
                            'entry_type'=>$trans_type,
                            'invoice_no'=>$ref_no,
                            'vendor_id'=>$vendor_id,
                            'voucher_id' => $voucher_id, 
                            'ledger_type' => 'Main Entry', 
                            'acc_id'=>$acc_id,
                            'ledger_name'=>$legal_name,
                            'ledger_code'=>$acc_code,
                            'type'=>$type,
                            'amount'=>$amount,
                            'narration'=>$narration,
                            'status'=>'pending',
                            'is_active'=>'1',
                            'updated_by'=>$curusr,
                            'updated_date'=>$now,
                            'ref_date'=>$payment_date,
                            'approver_comments'=>$remarks
                        ];

            $count = Yii::$app->db->createCommand()
                        ->update("acc_ledger_entries", $ledgerArray, "ref_id = '".$id."' and ref_type = 'payment_receipt' and 
                                    entry_type = '".$trans_type."' and voucher_id = '".$voucher_id."'")
                        ->execute();

            if ($count==0){
                $ledgerArray['created_by']=$curusr;
                $ledgerArray['created_date']=$now;

                $count = Yii::$app->db->createCommand()
                            ->insert("acc_ledger_entries", $ledgerArray)
                            ->execute();
            }

            $data = $this->getBanks($bank_id);
            if(count($data)>0){
                $bank_legal_name = $data[0]['legal_name'];
                $bank_acc_code = $data[0]['code'];
            } else {
                $bank_legal_name = '';
                $bank_acc_code = '';
            }
            if($trans_type="Payment"){
                $type = "Credit";
            } else {
                $type = "Debit";
            }
            $ledgerArray=[
                            'ref_id'=>$id,
                            'sub_ref_id'=>null,
                            'ref_type'=>'payment_receipt',
                            'entry_type'=>'Bank Entry',
                            'invoice_no'=>$ref_no,
                            'vendor_id'=>null,
                            'voucher_id' => $voucher_id, 
                            'ledger_type' => 'Sub Entry', 
                            'acc_id'=>$bank_id,
                            'ledger_name'=>$bank_legal_name,
                            'ledger_code'=>$bank_acc_code,
                            'type'=>$type,
                            'amount'=>$amount,
                            'narration'=>$narration,
                            'status'=>'pending',
                            'is_active'=>'1',
                            'updated_by'=>$curusr,
                            'updated_date'=>$now,
                            'ref_date'=>$payment_date,
                            'approver_comments'=>$remarks
                        ];

            $count = Yii::$app->db->createCommand()
                        ->update("acc_ledger_entries", $ledgerArray, "ref_id = '".$id."' and ref_type = 'payment_receipt' and 
                                    entry_type = 'Bank Entry' and voucher_id = '".$voucher_id."'")
                        ->execute();

            if ($count==0){
                $ledgerArray['created_by']=$curusr;
                $ledgerArray['created_date']=$now;

                $count = Yii::$app->db->createCommand()
                            ->insert("acc_ledger_entries", $ledgerArray)
                            ->execute();
            }
        } else {
            $chk = $request->post('chk');
            $ledger_id = $request->post('ledger_id');
            $ledger_type = $request->post('ledger_type');
            $debit_amt = $request->post('debit_amt');
            $credit_amt = $request->post('credit_amt');
            $invoice_no = $request->post('invoice_no');
            $vendor_id = $request->post('vendor_id');

            if (isset($ledger_id)){
                for($i=0; $i<count($ledger_id); $i++){
                    if($chk[$i]=="1"){
                        // $sql = "update acc_ledger_entries set is_paid = '1', payment_ref = '".$id."' 
                        //         where id = '".$ledger_id[$i]."'";
                        // $command = Yii::$app->db->createCommand($sql);
                        // $count = $command->execute();

                        if($mycomponent->format_number($debit_amt[$i],2)>0){
                            $type = 'Credit';
                            $amt = $mycomponent->format_number($debit_amt[$i],2);
                        } else {
                            $type = 'Debit';
                            $amt = $mycomponent->format_number($credit_amt[$i],2);
                        }

                        $ledgerArray=[
                                            'ref_id'=>$id,
                                            'sub_ref_id'=>$ledger_id[$i],
                                            'ref_type'=>'payment_receipt',
                                            'entry_type'=>$ledger_type[$i],
                                            'invoice_no'=>$invoice_no[$i],
                                            'vendor_id'=>$vendor_id[$i],
                                            'voucher_id' => $voucher_id, 
                                            'ledger_type' => 'Sub Entry', 
                                            'acc_id'=>$acc_id,
                                            'ledger_name'=>$legal_name,
                                            'ledger_code'=>$acc_code,
                                            'type'=>$type,
                                            'amount'=>$amt,
                                            'narration'=>$narration,
                                            'status'=>'pending',
                                            'is_active'=>'1',
                                            'updated_by'=>$curusr,
                                            'updated_date'=>$now,
                                            'ref_date'=>$payment_date,
                                            'approver_comments'=>$remarks
                                        ];

                        $count = Yii::$app->db->createCommand()
                                    ->update("acc_ledger_entries", $ledgerArray, "ref_id = '".$id."' and sub_ref_id = '".$ledger_id[$i]."' and ref_type = 'payment_receipt'")
                                    ->execute();

                        if ($count==0){
                            $ledgerArray['created_by']=$curusr;
                            $ledgerArray['created_date']=$now;

                            $count = Yii::$app->db->createCommand()
                                        ->insert("acc_ledger_entries", $ledgerArray)
                                        ->execute();
                        }
                    } else {
                        // $sql = "update acc_ledger_entries set is_paid = null, payment_ref = null 
                        //         where id = '".$ledger_id[$i]."'";
                        // $command = Yii::$app->db->createCommand($sql);
                        // $count = $command->execute();

                        $count = Yii::$app->db->createCommand()
                                    ->delete("acc_ledger_entries", "ref_id = '".$id."' and sub_ref_id = '".$ledger_id[$i]."' and ref_type = 'payment_receipt'")
                                    ->execute();
                    }
                }
            }

            $data = $this->getBanks($bank_id);
            if(count($data)>0){
                $bank_legal_name = $data[0]['legal_name'];
                $bank_acc_code = $data[0]['code'];
            } else {
                $bank_legal_name = '';
                $bank_acc_code = '';
            }
            if($amount>0){
                $type = 'Credit';
                $amount = $amount;
            } else {
                $type = 'Debit';
                $amount = $amount*-1;
            }
            $ledgerArray=[
                            'ref_id'=>$id,
                            'sub_ref_id'=>null,
                            'ref_type'=>'payment_receipt',
                            'entry_type'=>'Bank Entry',
                            'invoice_no'=>$ref_no,
                            'vendor_id'=>null,
                            'voucher_id' => $voucher_id, 
                            'ledger_type' => 'Main Entry', 
                            'acc_id'=>$bank_id,
                            'ledger_name'=>$bank_legal_name,
                            'ledger_code'=>$bank_acc_code,
                            'type'=>$type,
                            'amount'=>$amount,
                            'narration'=>$narration,
                            'status'=>'pending',
                            'is_active'=>'1',
                            'updated_by'=>$curusr,
                            'updated_date'=>$now,
                            'ref_date'=>$payment_date,
                            'payment_ref'=>$id,
                            'approver_comments'=>$remarks
                        ];

            $count = Yii::$app->db->createCommand()
                        ->update("acc_ledger_entries", $ledgerArray, "ref_id = '".$id."' and ref_type = 'payment_receipt' and entry_type = 'Bank Entry'")
                        ->execute();

            if ($count==0){
                $ledgerArray['created_by']=$curusr;
                $ledgerArray['created_date']=$now;

                $count = Yii::$app->db->createCommand()
                            ->insert("acc_ledger_entries", $ledgerArray)
                            ->execute();
            }
        }
        
        return true;
    }

    public function authorise($status){
        $request = Yii::$app->request;
        $session = Yii::$app->session;

        $curusr = $session['session_id'];
        $now = date('Y-m-d H:i:s');
        $id = $request->post('id');
        $voucher_id = $request->post('voucher_id');
        $remarks = $request->post('remarks');
        // $payment_type = $request->post('payment_type');

        $sql = "select * from acc_payment_receipt where id = '$id'";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        $result = $reader->readAll();
        if(count($result)>0){
            $payment_type = $result[0]['payment_type'];
        } else {
            $payment_type = '';
        }

        $array = array('status' => $status, 
                        'approved_by' => $curusr, 
                        'approved_date' => $now,
                        'approver_comments'=>$remarks);

        $count = Yii::$app->db->createCommand()
                        ->update("acc_payment_receipt", $array, "id = '".$id."'")
                        ->execute();

        $count = Yii::$app->db->createCommand()
                        ->update("acc_ledger_entries", $array, "ref_id = '".$id."' and ref_type = 'payment_receipt' and 
                                    voucher_id = '".$voucher_id."'")
                        ->execute();

        if($status=='approved'){
            if($payment_type=='Knock off'){
                $sql = "select group_concat(distinct sub_ref_id) as pay_id from acc_ledger_entries 
                        where ref_id = '".$id."' and ref_type = 'payment_receipt' and 
                        voucher_id = '".$voucher_id."' and sub_ref_id is not null";
                $command = Yii::$app->db->createCommand($sql);
                $reader = $command->query();
                $result = $reader->readAll();

                if(count($result)>0){
                    $pay_id = $result[0]['pay_id'];
                    $sql = "update acc_ledger_entries set is_paid = '1', payment_ref = '".$id."' 
                            where id in (".$pay_id.")";
                    $command = Yii::$app->db->createCommand($sql);
                    $count = $command->execute();
                }
            }
            
            $this->setLog('PaymentReceipt', '', 'Approve', '', 'Approve Payment Receipt Details', 'acc_payment_receipt', $id);
        } else {
            $this->setLog('PaymentReceipt', '', 'Reject', '', 'Reject Payment Receipt Details', 'acc_payment_receipt', $id);
        }
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

    public function getPaymentAdviceDetails($id){
        $sql = "select * from acc_payment_receipt where id = '$id'";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        $payment_details = $reader->readAll();

        if(count($payment_details)>0) {
            $acc_details = $this->getAccountDetails($payment_details[0]['account_id']);
            $status = $payment_details[0]['status'];

            if(count($acc_details)>0){
                $vendor_id = $acc_details[0]['vendor_id'];
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
            } else {
                $vendor_details = array();
            }

            if($payment_details[0]['payment_type']=='Knock off'){
                // $sql = "select B.id, B.invoice_no, B.vendor_id, B.acc_id as account_id, B.ledger_name as account_name, 
                //             B.ledger_code as account_code, B.ledger_name, 
                //             B.type, B.amount, B.is_paid, B.payment_ref, B.ref_type as ledger_type, 
                //             C.gi_date, D.invoice_date from acc_ledger_entries A 
                //         left join acc_ledger_entries B on(A.sub_ref_id=B.id) 
                //         left join grn C on(B.ref_id = C.grn_id and B.ref_type = 'purchase') 
                //         left join goods_inward_outward_invoices D on(B.invoice_no = D.invoice_no and 
                //             B.ref_type = 'purchase' and C.gi_id = D.gi_go_ref_no) 
                //         where A.is_active = '1' and A.ref_type = 'payment_receipt' and 
                //             A.ref_id = '$id' and A.entry_type != 'payment_entry' and B.is_active = '1'";

                $sql = "select sr_no, ledger_type, type, ref_no, ref_date, sum(amount) as tot_amount from 
                        (select B.id, B.invoice_no as ref_no, B.vendor_id, B.acc_id as account_id, B.ledger_name as account_name, 
                            B.ledger_code as account_code, B.ledger_name, 
                            B.type, B.amount, B.is_paid, B.payment_ref, B.ref_type as ledger_type, C.gi_date, 
                            case when B.ref_type ='purchase' then D.invoice_date when B.ref_type ='journal_voucher' then E.jv_date 
                                when B.ref_type ='payment_receipt' then F.payment_date else D.invoice_date end as ref_date, 
                            case when B.ref_type ='purchase' then 1 when B.ref_type ='journal_voucher' then 2 
                                when B.ref_type ='payment_receipt' then 3 else 4 end as sr_no 
                        from acc_ledger_entries A 
                        left join acc_ledger_entries B on(A.sub_ref_id=B.id) 
                        left join grn C on(B.ref_id = C.grn_id and B.ref_type = 'purchase') 
                        left join goods_inward_outward_invoices D on(B.invoice_no = D.invoice_no and 
                            B.ref_type = 'purchase' and C.gi_id = D.gi_go_ref_no) 
                        left join acc_jv_details E on(B.ref_id = E.id and B.ref_type = 'journal_voucher') 
                        left join acc_payment_receipt F on(B.ref_id = F.id and B.ref_type = 'payment_receipt') 
                        where A.is_active = '1' and A.ref_type = 'payment_receipt' and 
                            A.ref_id = '$id' and A.entry_type != 'payment_entry' and B.is_active = '1') C 
                        group by sr_no, ledger_type, type, ref_no, ref_date 
                        order by sr_no, ledger_type, type, ref_no, ref_date";
            } else {
                // $sql = "select A.id, null as invoice_no, null as vendor_id, A.account_id, A.account_name, 
                //             A.account_code, A.account_name as ledger_name, 
                //             null as type, A.amount, A.is_paid, A.payment_ref, null as ledger_type, 
                //             null as gi_date, null as invoice_date from acc_payment_receipt A 
                //         where A.is_active = '1' and A.id = '$id'";

                $sql = "select A.id as sr_no, 'payment_receipt' as ledger_type, 
                                case when A.trans_type='Payment' then 'Debit' else 'Credit' end as type, 
                                A.ref_no, A.payment_date as ref_date, A.amount as tot_amount from acc_payment_receipt A 
                        where A.is_active = '1' and A.id = '$id'";
            }
            $command = Yii::$app->db->createCommand($sql);
            $reader = $command->query();
            $entry_details = $reader->readAll();
            

            $sql = "select * from acc_payment_advices where is_active = '1' and payment_id = '$id'";
            $command = Yii::$app->db->createCommand($sql);
            $reader = $command->query();
            $payment_advice = $reader->readAll();
            if(count($payment_advice)==0){
                $session = Yii::$app->session;

                $curusr = $session['session_id'];
                $now = date('Y-m-d H:i:s');

                $array=[
                    'payment_id'=>$id,
                    'account_id'=>$payment_details[0]['account_id'],
                    'status'=>$status,
                    'is_active'=>'1',
                    'updated_by'=>$curusr,
                    'updated_date'=>$now
                ];

                $count = Yii::$app->db->createCommand()
                            ->insert("acc_payment_advices", $array)
                            ->execute();
                $ref_id = Yii::$app->db->getLastInsertID();
            } else {
                $ref_id = $payment_advice[0]['id'];

                $sql = "update acc_payment_advices set status = '$status' where is_active = '1' and payment_id = '$id'";
                $command = Yii::$app->db->createCommand($sql);
                $count = $command->execute();
            }

            $sql = "select * from acc_payment_advices where is_active = '1' and id = '$ref_id'";
            $command = Yii::$app->db->createCommand($sql);
            $reader = $command->query();
            $payment_advice = $reader->readAll();
            
            $mpdf=new mPDF();
            $mpdf->WriteHTML(Yii::$app->controller->renderPartial('payment_advice', [
                'payment_details' => $payment_details,
                'entry_details' => $entry_details,
                'vendor_details' => $vendor_details
            ]));

            $upload_path = './uploads';
            if(!is_dir($upload_path)) {
                mkdir($upload_path, 0777, TRUE);
            }
            $upload_path = './uploads/acc_payment_advices';
            if(!is_dir($upload_path)) {
                mkdir($upload_path, 0777, TRUE);
            }

            $file_name = $upload_path . '/payment_advice_' . $id . '.pdf';
            $file_path = 'uploads/acc_payment_advices/payment_advice_' . $id . '.pdf';

            // $mpdf->Output('MyPDF.pdf', 'D');
            $mpdf->Output($file_name, 'F');
            // exit;

            $sql = "update acc_payment_advices set payment_advice_path = '$file_path' where id = '$ref_id'";
            $command = Yii::$app->db->createCommand($sql);
            $count = $command->execute();

            $sql = "select * from acc_payment_advices where is_active = '1' and id = '$ref_id'";
            $command = Yii::$app->db->createCommand($sql);
            $reader = $command->query();
            $payment_advice = $reader->readAll();

        } else {
            $payment_advice = array();
            $vendor_details = array();
            $entry_details = array();
        }

        $data['payment_details'] = $payment_details;
        $data['vendor_details'] = $vendor_details;
        $data['entry_details'] = $entry_details;
        $data['payment_advice'] = $payment_advice;
        
        return $data;
    }

    public function setEmailLog($vendor_name, $from_email_id, $to_recipient, $reference_number, $email_content, 
                                $email_attachment, $attachment_type, $email_sent_status, $error_message, $company_id)
    {
        $session = Yii::$app->session;
        $curusr = $session['session_id'];
        $username = $session['username'];
        $now = date('Y-m-d H:i:s');

        $array = array('module' => 'PA', 
                        'email_type' => 'Payment Advice Email', 
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