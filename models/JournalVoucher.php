<?php

namespace app\models;

use Yii;
use yii\base\Model;

class JournalVoucher extends Model
{
    public function getJournalVoucherDetails($id="", $status=""){
        $cond = "";
        if($id!=""){
            $cond = " and id = '$id'";
        }
        if($status!=""){
            // if($cond==""){
            //     $cond = " Where status = '$status'";
            // } else {
            //     $cond = $cond . " and status = '$status'";
            // }
            $cond = $cond . " and status = '$status'";
        }

        $sql = "select * from journal_voucher_details where is_active='1'" . $cond . " order by id desc";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function gerJournalVoucherEntries($id){
        $sql = "select * from journal_voucher_entries where jv_id='$id' and is_active='1' order by id";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getAccountDetails($id=""){
        $cond = "";
        if($id!=""){
            $cond = " and id = '$id'";
        }

        // $sql = "select * from 
        //         (select id, case when legal_name = 'Taxable Amount' then code when legal_name = 'Tax' then code else legal_name end as acc_name, code 
        //         from acc_master where is_active = '1'".$cond.") A order by A.acc_name";
        
        $sql = "select * from acc_master where is_active = '1'".$cond." order by legal_name";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function save(){
        $request = Yii::$app->request;
        $mycomponent = Yii::$app->mycomponent;
        $session = Yii::$app->session;

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
        // $doc_file = $request->post('doc_file');

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
            $sql = "select * from series_master where type = 'Voucher'";
            $command = Yii::$app->db->createCommand($sql);
            $reader = $command->query();
            $data = $reader->readAll();
            if (count($data)>0){
                $series = intval($data[0]['series']) + 1;

                $sql = "update series_master set series = '$series' where type = 'Voucher'";
                $command = Yii::$app->db->createCommand($sql);
                $count = $command->execute();
            } else {
                $series = 1;

                $sql = "insert into series_master (type, series) values ('Voucher', '".$series."')";
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
                        'updated_by'=>$session['session_id'],
                        'updated_date'=>date('Y-m-d h:i:s')
                        );

        if(count($array)>0){
            $tableName = "journal_voucher_details";

            if (isset($id) && $id!=""){
                $count = Yii::$app->db->createCommand()
                            ->update($tableName, $array, "id = '".$id."'")
                            ->execute();
            } else {
                $count = Yii::$app->db->createCommand()
                            ->insert($tableName, $array)
                            ->execute();
                $id = Yii::$app->db->getLastInsertID();
            }
        }

        $journal_voucher_entries = array();
        for($i=0; $i<count($acc_id); $i++){
            $journal_voucher_entries = array('jv_id' => $id, 
                                                'account_id' => $acc_id[$i], 
                                                'account_name' => $legal_name[$i], 
                                                'account_code' => $acc_code[$i], 
                                                'transaction' => $transaction[$i], 
                                                'debit_amt' => $mycomponent->format_number($debit_amt[$i],2), 
                                                'credit_amt' => $mycomponent->format_number($credit_amt[$i],2),
                                                'status' => 'pending',
                                                'is_active' => '1',
                                                'updated_by'=>$session['session_id'],
                                                'updated_date'=>date('Y-m-d h:i:s')
                                            );

            $tableName = "journal_voucher_entries";

            if (isset($entry_id[$i]) && $entry_id[$i]!=""){
                $count = Yii::$app->db->createCommand()
                            ->update($tableName, $journal_voucher_entries, "id = '".$entry_id[$i]."'")
                            ->execute();
            } else {
                $count = Yii::$app->db->createCommand()
                            ->insert($tableName, $journal_voucher_entries)
                            ->execute();
                $entry_id[$i] = Yii::$app->db->getLastInsertID();
            }

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
                                // 'invoice_no'=>$invoice_no_val[$i],
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
                                'updated_by'=>$session['session_id'],
                                'updated_date'=>date('Y-m-d h:i:s')
                            ];

            $tableName = "ledger_entries";

            $count = Yii::$app->db->createCommand()
                        ->update($tableName, $ledgerArray, "ref_id = '".$id."' and sub_ref_id = '".$entry_id[$i]."' and ref_type = 'journal_voucher'")
                        ->execute();

            if ($count==0){
                $count = Yii::$app->db->createCommand()
                            ->insert($tableName, $ledgerArray)
                            ->execute();
            }
        }

        // $sql = "delete from journal_voucher_entries where jv_id = '$id'";
        // Yii::$app->db->createCommand($sql)->execute();

        // if(count($journal_voucher_entries)>0){
        //     $columnNameArray=['jv_id','account_id','account_name', 'account_code', 'transaction', 'debit_amt', 'credit_amt', 'status', 'is_active'];
        //     $tableName = "journal_voucher_entries";
        //     $insertCount = Yii::$app->db->createCommand()
        //                     ->batchInsert(
        //                         $tableName, $columnNameArray, $journal_voucher_entries
        //                     )
        //                     ->execute();
        // }
        
        return true;
    }
}