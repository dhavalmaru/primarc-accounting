<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class JournalVoucher extends Model
{
    public function getAccess(){
        $session = Yii::$app->session;
        $session_id = $session['session_id'];
        $role_id = $session['role_id'];

        $sql = "select A.*, '".$session_id."' as session_id from acc_user_role_options A 
                where A.role_id = '$role_id' and A.r_section = 'S_Journal_Voucher'";
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
                where C.r_section = 'S_Journal_Voucher' and 
                        C.r_approval = '1' and C.r_approval is not null" . $cond;
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getJournalVoucherDetails($id="", $status=""){
        $cond = "";
        if($id!=""){
            $cond = " and A.id = '$id'";
        }
        if($status!=""){
            $cond = $cond . " and A.status = '$status'";
        }

        $sql = "select A.*, B.username as updater, C.username as approver from 
                acc_jv_details A left join user B on (A.updated_by = B.id) left join user C on (A.approved_by = C.id) 
                where A.is_active='1'" . $cond . " order by UNIX_TIMESTAMP(A.updated_date) desc, A.id desc";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function gerJournalVoucherEntries($id){
        $sql = "select * from acc_jv_entries where jv_id='$id' and is_active='1' order by id";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function gerJournalVoucherDocs($id){
        $sql = "select * from acc_jv_docs where jv_id='$id' and is_active='1' order by id";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getAccountDetails($id=""){
        $cond = "";
        if($id!=""){
            $cond = " and id = '$id'";
        }

        $sql = "select * from acc_master where is_active = '1' and status = 'approved'".$cond." order by legal_name";
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
        $entry_id = $request->post('entry_id');
        $acc_id = $request->post('acc_id');
        $legal_name = $request->post('legal_name');
        $acc_code = $request->post('acc_code');
        $transaction = $request->post('transaction');
        $debit_amt = $request->post('debit_amt');
        $credit_amt = $request->post('credit_amt');
        $total_debit_amt = $request->post('total_debit_amt');
        $total_credit_amt = $request->post('total_credit_amt');
        $diff_amt = $request->post('diff_amt');
        $reference = $request->post('reference');
        $narration = $request->post('narration');
        $jv_date = $request->post('jv_date');
        if($jv_date==''){
            $jv_date=NULL;
        } else {
            $jv_date=$mycomponent->formatdate($jv_date);
        }
        $remarks = $request->post('remarks');
        $approver_id = $request->post('approver_id');

        $debit_acc = "";
        $credit_acc = "";
        for($i=0; $i<count($legal_name); $i++){
            if($transaction[$i]=='Debit'){
                $debit_acc = $debit_acc . $legal_name[$i] . ', ';
            } else {
                $credit_acc = $credit_acc . $legal_name[$i] . ', ';
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
            $ledger_type = 'Main Entry';
        }
        
        $array = array('voucher_id' => $voucher_id, 
                        'ledger_type' => $ledger_type, 
                        'reference' => $reference, 
                        'narration' => $narration, 
                        'debit_acc' => $debit_acc, 
                        'credit_acc' => $credit_acc, 
                        'debit_amt' => $mycomponent->format_number($total_debit_amt,2), 
                        'credit_amt' => $mycomponent->format_number($total_credit_amt,2), 
                        'diff_amt' => $mycomponent->format_number($diff_amt,2),
                        'status' => 'pending',
                        'is_active' => '1',
                        'updated_by'=>$curusr,
                        'updated_date'=>$now,
                        'jv_date'=>$jv_date,
                        'approver_comments'=>$remarks,
                        'approver_id'=>$approver_id
                        );

        if(count($array)>0){
            if (isset($id) && $id!=""){
                $count = Yii::$app->db->createCommand()
                            ->update("acc_jv_details", $array, "id = '".$id."'")
                            ->execute();

                $this->setLog('JournalVoucher', '', 'Save', '', 'Update Journal Voucher Details', 'acc_jv_details', $id);
            } else {
                $array['created_by'] = $curusr;
                $array['created_date'] = $now;
                $count = Yii::$app->db->createCommand()
                            ->insert("acc_jv_details", $array)
                            ->execute();
                $id = Yii::$app->db->getLastInsertID();

                $this->setLog('JournalVoucher', '', 'Save', '', 'Insert Journal Voucher Details', 'acc_jv_details', $id);
            }
        }



        $acc_jv_entries = array();

        $sql = "delete from acc_jv_entries where jv_id = '$id'";
        Yii::$app->db->createCommand($sql)->execute();

        $sql = "delete from acc_ledger_entries where ref_id = '".$id."' and ref_type = 'journal_voucher'";
        Yii::$app->db->createCommand($sql)->execute();

        for($i=0; $i<count($acc_id); $i++){
            $acc_jv_entries = array('jv_id' => $id, 
                                    'account_id' => $acc_id[$i], 
                                    'account_name' => $legal_name[$i], 
                                    'account_code' => $acc_code[$i], 
                                    'transaction' => $transaction[$i], 
                                    'debit_amt' => $mycomponent->format_number($debit_amt[$i],2), 
                                    'credit_amt' => $mycomponent->format_number($credit_amt[$i],2),
                                    'status' => 'pending',
                                    'is_active' => '1',
                                    'updated_by'=>$curusr,
                                    'updated_date'=>$now,
                                    'approver_comments'=>$remarks
                                );

            $acc_jv_entries['created_by'] = $curusr;
            $acc_jv_entries['created_date'] = $now;
            $count = Yii::$app->db->createCommand()
                        ->insert("acc_jv_entries", $acc_jv_entries)
                        ->execute();
            $entry_id[$i] = Yii::$app->db->getLastInsertID();

            // if (isset($entry_id[$i]) && $entry_id[$i]!=""){
            //     $count = Yii::$app->db->createCommand()
            //                 ->update("acc_jv_entries", $acc_jv_entries, "id = '".$entry_id[$i]."'")
            //                 ->execute();
            // } else {
            //     $acc_jv_entries['created_by'] = $curusr;
            //     $acc_jv_entries['created_date'] = $now;

            //     $count = Yii::$app->db->createCommand()
            //                 ->insert("acc_jv_entries", $acc_jv_entries)
            //                 ->execute();
            //     $entry_id[$i] = Yii::$app->db->getLastInsertID();
            // }

            if($transaction[$i]=="Debit"){
                $amount = $debit_amt[$i];
            } else {
                $amount = $credit_amt[$i];
            }

            $ledgerArray=[
                                'ref_id'=>$id,
                                'sub_ref_id'=>$entry_id[$i],
                                'ref_type'=>'journal_voucher',
                                'entry_type'=>'Journal Voucher',
                                'invoice_no'=>$reference,
                                // 'vendor_id'=>$vendor_id,
                                'voucher_id' => $voucher_id, 
                                'ledger_type' => $ledger_type, 
                                'acc_id'=>$acc_id[$i],
                                'ledger_name'=>$legal_name[$i],
                                'ledger_code'=>$acc_code[$i],
                                'type'=>$transaction[$i],
                                'amount'=>$mycomponent->format_number($amount,2),
                                'status'=>'pending',
                                'is_active'=>'1',
                                'updated_by'=>$curusr,
                                'updated_date'=>$now,
                                'ref_date'=>$jv_date,
                                'approver_comments'=>$remarks
                            ];

            $ledgerArray['created_by'] = $curusr;
            $ledgerArray['created_date'] = $now;
            $count = Yii::$app->db->createCommand()
                        ->insert("acc_ledger_entries", $ledgerArray)
                        ->execute();

            // $count = Yii::$app->db->createCommand()
            //             ->update("acc_ledger_entries", $ledgerArray, "ref_id = '".$id."' and sub_ref_id = '".$entry_id[$i]."' and ref_type = 'journal_voucher'")
            //             ->execute();

            // if ($count==0){
            //     $ledgerArray['created_by'] = $curusr;
            //     $ledgerArray['created_date'] = $now;

            //     $count = Yii::$app->db->createCommand()
            //                 ->insert("acc_ledger_entries", $ledgerArray)
            //                 ->execute();
            // }
        }


        $jv_doc_id = $request->post('jv_doc_id');
        $doc_path = $request->post('doc_path');
        $description = $request->post('description');
        if(count($jv_doc_id)>0){
            $upload_path = './uploads';
            if(!is_dir($upload_path)) {
                mkdir($upload_path, 0777, TRUE);
            }
            $upload_path = './uploads/journal_voucher';
            if(!is_dir($upload_path)) {
                mkdir($upload_path, 0777, TRUE);
            }
            $upload_path = './uploads/journal_voucher/'.$id;
            if(!is_dir($upload_path)) {
                mkdir($upload_path, 0777, TRUE);
            }

            $acc_jv_docs = array();

            $sql = "delete from acc_jv_docs where jv_id = '$id'";
            Yii::$app->db->createCommand($sql)->execute();
            
            $doc_cnt = 0;
            for($i=0; $i<count($jv_doc_id); $i++){
                $file_nm='doc_file_'.$doc_cnt;
                while (!isset($_FILES[$file_nm])) {
                    $doc_cnt = $doc_cnt + 1;
                    $file_nm = 'doc_file_'.$doc_cnt;
                }

                $uploadedFile = UploadedFile::getInstanceByName($file_nm);
                if(!empty($uploadedFile)){
                    $src_filename= $_FILES[$file_nm];
                    $filePath = $upload_path.'/'.$src_filename['name'];
                    $uploadedFile->saveAs($filePath);
                    $doc_path[$i] = 'uploads/journal_voucher/'.$id.'/'.$src_filename['name'];
                }

                if(isset($doc_path[$i]) && $doc_path[$i]!=''){
                    $acc_jv_docs = array('jv_id' => $id, 
                                        'doc_path' => $doc_path[$i], 
                                        'description' => $description[$i], 
                                        'status' => 'pending',
                                        'is_active' => '1',
                                        'updated_by'=>$curusr,
                                        'updated_date'=>$now,
                                        'approver_comments'=>$remarks
                                    );

                    $acc_jv_docs['created_by'] = $curusr;
                    $acc_jv_docs['created_date'] = $now;
                    $count = Yii::$app->db->createCommand()
                                ->insert("acc_jv_docs", $acc_jv_docs)
                                ->execute();
                    $jv_doc_id[$i] = Yii::$app->db->getLastInsertID();

                    // if (isset($jv_doc_id[$i]) && $jv_doc_id[$i]!=""){
                    //     $count = Yii::$app->db->createCommand()
                    //                 ->update("acc_jv_docs", $acc_jv_docs, "id = '".$jv_doc_id[$i]."'")
                    //                 ->execute();
                    // } else {
                    //     $acc_jv_docs['created_by'] = $curusr;
                    //     $acc_jv_docs['created_date'] = $now;

                    //     $count = Yii::$app->db->createCommand()
                    //                 ->insert("acc_jv_docs", $acc_jv_docs)
                    //                 ->execute();
                    //     $jv_doc_id[$i] = Yii::$app->db->getLastInsertID();
                    // }
                }

                $doc_cnt = $doc_cnt + 1;
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
        $remarks = $request->post('remarks');

        $array = array('status' => $status, 
                        'approved_by' => $curusr, 
                        'approved_date' => $now,
                        'approver_comments'=>$remarks);

        $count = Yii::$app->db->createCommand()
                            ->update("acc_jv_details", $array, "id = '".$id."'")
                            ->execute();

        $count = Yii::$app->db->createCommand()
                            ->update("acc_jv_entries", $array, "jv_id = '".$id."'")
                            ->execute();

        $count = Yii::$app->db->createCommand()
                            ->update("acc_ledger_entries", $array, "ref_id = '".$id."' and ref_type = 'journal_voucher'")
                            ->execute();

        $count = Yii::$app->db->createCommand()
                            ->update("acc_jv_docs", $array, "jv_id = '".$id."'")
                            ->execute();

        if($status=='approved'){
            $this->setLog('JournalVoucher', '', 'Approve', '', 'Approve Journal Voucher Details', 'acc_jv_details', $id);
        } else {
            $this->setLog('JournalVoucher', '', 'Reject', '', 'Reject Journal Voucher Details', 'acc_jv_details', $id);
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
}