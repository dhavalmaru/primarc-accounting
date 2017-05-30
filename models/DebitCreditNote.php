<?php

namespace app\models;

use Yii;
use yii\base\Model;

class DebitCreditNote extends Model
{
    public function getDetails($trans_id="", $status=""){
        $cond = "";
        if($trans_id!=""){
            $cond = " Where trans_id = '$trans_id'";
        }
        if($status!=""){
            if($cond==""){
                $cond = " Where status = '$status'";
            } else {
                $cond = $cond . " and status = '$status'";
            }
        }

        $sql = "select * from debit_credit_entries" . $cond;
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getAccountDetails($type){
        $sql = "select * from acc_master where type = '$type'";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getLedger($transaction_id){
        $sql = "select * from debit_credit_ledger where trans_id = '$transaction_id'";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function save(){
        $request = Yii::$app->request;
        $mycomponent = Yii::$app->mycomponent;

        $id = $request->post('id');
        $trans_id = $request->post('trans_id');
        $type = $request->post('type');
        $transaction = $request->post('transaction');
        $acc_code = $request->post('acc_code');
        $due_date = $request->post('due_date');
        $amount = $request->post('amount');
        $ref_no = $request->post('ref_no');
        $other_ref_no = $request->post('other_ref_no');
        $narration = $request->post('narration');

        $tableName = "debit_credit_entries";
        $transaction_id = "";

        if(isset($trans_id[0]) && $trans_id[0]!=""){
            $transaction_id = $trans_id[0];
        } else {
            $sql = "select * from acc_series_master where type = 'Transaction'";
            $command = Yii::$app->db->createCommand($sql);
            $reader = $command->query();
            $data = $reader->readAll();

            if(count($data)>0){
                $series = intVal($data[0]['series']) + 1;

                $sql = "update acc_series_master set series = '$series' where type = 'Transaction'";
                $command = Yii::$app->db->createCommand($sql);
                $count = $command->execute();
            } else {
                $series = 1;

                $sql = "insert into acc_series_master (type, series) values ('Transaction', '".$series."')";
                $command = Yii::$app->db->createCommand($sql);
                $count = $command->execute();
            }

            $transaction_id = $series;
        }

        for($i=0; $i<count($type); $i++){
            $amount_val = $mycomponent->format_number($amount[$i],2);

            $array=[
                'trans_id'=>$transaction_id,
                'type'=>$type[$i],
                'transaction'=>$transaction[$i],
                'acc_code'=>$acc_code[$i],
                'due_date'=>$due_date[$i],
                'amount'=>$amount_val,
                'ref_no'=>$ref_no[$i],
                'other_ref_no'=>$other_ref_no[$i],
                'narration'=>$narration[$i],
                'status'=>'pending'
            ];

            if (isset($id[$i]) && $id[$i]!=""){
                $count = Yii::$app->db->createCommand()
                            ->update($tableName, $array, "id = '".$id[$i]."'")
                            ->execute();

                $sql = "update debit_credit_ledger set particular = '".$transaction[$i]."-".$acc_code[$i]."-".$transaction_id."', 
                        type = '".$type[$i]."', amount = '".$amount_val."' 
                        where trans_id = '".$transaction_id."' and type = '".$type[$i]."'";
                $command = Yii::$app->db->createCommand($sql);
                $count = $command->execute();
            } else {
                $count = Yii::$app->db->createCommand()
                            ->insert($tableName, $array)
                            ->execute();

                $sql = "insert into debit_credit_ledger (trans_id, particular, type, amount, status) values 
                        ('".$transaction_id."', '".$transaction[$i]."-".$acc_code[$i]."-".$transaction_id."', '".$type[$i]."', 
                            '".$amount_val."', 'pending')";
                $command = Yii::$app->db->createCommand($sql);
                $count = $command->execute();
            }
        }

        return $transaction_id;
    }
}