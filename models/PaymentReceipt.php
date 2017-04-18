<?php

namespace app\models;

use Yii;
use yii\base\Model;
use mPDF;

class PaymentReceipt extends Model
{
    public function getDetails($trans_id="", $status=""){
        $cond = "";
        if($trans_id!=""){
            $cond = " Where id = '$trans_id'";
        }
        if($status!=""){
            if($cond==""){
                $cond = " Where status = '$status'";
            } else {
                $cond = $cond . " and status = '$status'";
            }
        }

        $sql = "select * from payment_receipt_details" . $cond;
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getAccountDetails($id=""){
        $cond = "";
        if($id!=""){
            $cond = " and id = '$id'";
        }

        $sql = "select * from acc_master where is_active = '1'".$cond." order by legal_name";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getLedger($id, $account_id){
        // $sql = "select * from 
        //         (select id, acc_id as account_id, ledger_name as account_name, ledger_code as account_code, 
        //             type, amount, is_paid, payment_ref, ref_type as ledger_type from ledger_entries 
        //         where acc_id = '$account_id' and amount!=0 and 
        //             (is_paid is null or is_paid!='1' or payment_ref='$id') and is_active='1' 
        //         union all 
        //         select id, account_id, account_name, account_code, 'Debit' as type, amount, is_paid, payment_ref, 'payment_entry' as ledger_type 
        //         from payment_receipt_details where account_id = '$account_id' and payment_type = 'Adhoc' and 
        //             (is_paid is null or is_paid!='1' or payment_ref='$id') and is_active='1') A 
        //         order by ledger_type desc, type desc, id";

        $sql = "select * from 
                (select A.id, A.invoice_no, A.vendor_id, A.acc_id as account_id, A.ledger_name as account_name, A.ledger_code as account_code, 
                    A.type, A.amount, A.is_paid, A.payment_ref, A.ref_type as ledger_type, 
                    B.gi_date, C.invoice_date, D.due_date from ledger_entries A 
                left join grn B on(A.ref_id = B.grn_id and A.ref_type = 'purchase') 
                left join  goods_inward_outward_invoices C on(A.invoice_no = C.invoice_no and 
                    A.ref_type = 'purchase' and B.gi_id = C.gi_go_ref_no) 
                left join invoice_tracker D on(A.ref_id=D.gi_id and A.invoice_no=D.invoice_id) 
                where A.acc_id = '$account_id' and A.amount!=0 and A.ref_type!='payment_receipt' and 
                    (A.is_paid is null or A.is_paid!='1' or A.payment_ref='$id') and A.is_active='1' 
                union all 
                select id, null as invoice_no, null as vendor_id, account_id, account_name, account_code, 'Debit' as type, amount, is_paid, payment_ref, 
                    'payment_entry' as ledger_type, null as gi_date, null as invoice_date, null as due_date
                from payment_receipt_details where account_id = '$account_id' and payment_type = 'Adhoc' and 
                    (is_paid is null or is_paid!='1' or payment_ref='$id') and is_active='1') A 
                order by ledger_type desc, type desc, id";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getVendors(){
        $sql = "select * from vendor_master where is_active = '1'";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getBanks(){
        $sql = "select * from acc_master where is_active = '1' and type = 'Bank Account'";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function save(){
        $request = Yii::$app->request;
        $mycomponent = Yii::$app->mycomponent;
        $session = Yii::$app->session;

        $id = $request->post('id');
        $trans_type = $request->post('trans_type');
        $acc_id = $request->post('acc_id');
        $legal_name = $request->post('legal_name');
        $acc_code = $request->post('acc_code');
        $bank_id = $request->post('bank_id');
        $bank_name = $request->post('bank_name');
        $payment_type = $request->post('payment_type');
        $narration = $request->post('narration');

        if($payment_type == "Adhoc"){
            $amount = $mycomponent->format_number($request->post('amount'),2);
            $ref_no = $request->post('ref_no');
        } else {
            $payable_debit_amt = $mycomponent->format_number($request->post('payable_debit_amt'),2);
            $payable_credit_amt = $mycomponent->format_number($request->post('payable_credit_amt'),2);

            if($payable_debit_amt>0){
                $amount = $payable_debit_amt*-1;
            } else {
                $amount = $payable_credit_amt;
            }
            
            $ref_no = null;
        }

        $tableName = "payment_receipt_details";
        $transaction_id = "";

        $array=[
                'trans_type'=>$trans_type,
                'account_id'=>$acc_id,
                'account_name'=>$legal_name,
                'account_code'=>$acc_code,
                'bank_id'=>$bank_id,
                'bank_name'=>$bank_name,
                'payment_type'=>$payment_type,
                'amount'=>$amount,
                'ref_no'=>$ref_no,
                'narration'=>$narration,
                'status'=>'pending',
                'is_active'=>'1',
                'updated_by'=>$session['session_id'],
                'updated_date'=>date('Y-m-d h:i:s')
            ];

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

        if($payment_type == "Knock off"){
            $chk = $request->post('chk');
            $ledger_id = $request->post('ledger_id');
            $ledger_type = $request->post('ledger_type');
            $debit_amt = $request->post('debit_amt');
            $credit_amt = $request->post('credit_amt');
            $invoice_no = $request->post('invoice_no');
            $vendor_id = $request->post('vendor_id');

            if (isset($ledger_id)){
                for($i=0; $i<count($ledger_id); $i++){
                    if($ledger_type[$i]=="payment_entry"){
                        $table_name = "payment_receipt_details";
                    } else {
                        $table_name = "ledger_entries";
                    }

                    if($chk[$i]=="1"){
                        $sql = "update ".$table_name." set is_paid = '1', payment_ref = '".$id."' 
                                where id = '".$ledger_id[$i]."'";
                        $command = Yii::$app->db->createCommand($sql);
                        $count = $command->execute();

                        if($mycomponent->format_number($debit_amt[$i],2)>0){
                            $type = 'Credit';
                            $amount = $mycomponent->format_number($debit_amt[$i],2);
                        } else {
                            $type = 'Debit';
                            $amount = $mycomponent->format_number($credit_amt[$i],2);
                        }

                        $ledgerArray=[
                                            'ref_id'=>$id,
                                            'sub_ref_id'=>$ledger_id[$i],
                                            'ref_type'=>'payment_receipt',
                                            'entry_type'=>$ledger_type[$i],
                                            'invoice_no'=>$invoice_no[$i],
                                            'vendor_id'=>$vendor_id[$i],
                                            'acc_id'=>$acc_id,
                                            'ledger_name'=>$legal_name,
                                            'ledger_code'=>$acc_code,
                                            'type'=>$type,
                                            'amount'=>$amount,
                                            'status'=>'pending',
                                            'is_active'=>'1',
                                            'updated_by'=>$session['session_id'],
                                            'updated_date'=>date('Y-m-d h:i:s')
                                        ];

                        $count = Yii::$app->db->createCommand()
                                    ->update("ledger_entries", $ledgerArray, "ref_id = '".$id."' and sub_ref_id = '".$ledger_id[$i]."' and ref_type = 'payment_receipt'")
                                    ->execute();

                        if ($count==0){
                            $count = Yii::$app->db->createCommand()
                                        ->insert("ledger_entries", $ledgerArray)
                                        ->execute();
                        }
                    } else {
                        $sql = "update ".$table_name." set is_paid = null, payment_ref = null 
                                where id = '".$ledger_id[$i]."'";
                        $command = Yii::$app->db->createCommand($sql);
                        $count = $command->execute();

                        $count = Yii::$app->db->createCommand()
                                    ->delete("ledger_entries", "ref_id = '".$id."' and sub_ref_id = '".$ledger_id[$i]."' and ref_type = 'payment_receipt'")
                                    ->execute();
                    }

                    
                }
            }
        }
        
        return true;
    }

    public function getPaymentAdviceDetails($id){
        $sql = "select * from payment_receipt_details where id = '$id'";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        $payment_details = $reader->readAll();

        if(count($payment_details)>0) {
            $acc_details = $this->getAccountDetails($payment_details[0]['account_id']);

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
                $sql = "select A.id, A.invoice_no, A.vendor_id, A.acc_id as account_id, A.ledger_name as account_name, 
                            A.ledger_code as account_code, A.ledger_name, 
                            A.type, A.amount, A.is_paid, A.payment_ref, A.ref_type as ledger_type, 
                            C.gi_date, D.invoice_date from ledger_entries A 
                        left join ledger_entries B on(A.sub_ref_id=B.id) 
                        left join grn C on(B.ref_id = C.grn_id and B.ref_type = 'purchase') 
                        left join goods_inward_outward_invoices D on(A.invoice_no = D.invoice_no and 
                            A.entry_type = 'purchase' and C.gi_id = D.gi_go_ref_no) 
                        where A.status = 'pending' and A.is_active = '1' and A.ref_type = 'payment_receipt' and 
                            A.ref_id = '$id' and A.entry_type != 'payment_entry' and B.status = 'pending' and B.is_active = '1'";
            } else {
                $sql = "select A.id, null as invoice_no, null as vendor_id, A.account_id, A.account_name, 
                            A.account_code, A.account_name as ledger_name, 
                            null as type, A.amount, A.is_paid, A.payment_ref, null as ledger_type, 
                            null as gi_date, null as invoice_date from payment_receipt_details A 
                        where A.status = 'pending' and A.is_active = '1' and A.id = '$id'";
            }
            $command = Yii::$app->db->createCommand($sql);
            $reader = $command->query();
            $entry_details = $reader->readAll();
            

            $sql = "select * from payment_advices where status = 'pending' and is_active = '1' and payment_id = '$id'";
            $command = Yii::$app->db->createCommand($sql);
            $reader = $command->query();
            $payment_advice = $reader->readAll();
            if(count($payment_advice)==0){
                $session = Yii::$app->session;

                $array=[
                    'payment_id'=>$id,
                    'account_id'=>$payment_details[0]['account_id'],
                    'status'=>'pending',
                    'is_active'=>'1',
                    'updated_by'=>$session['session_id'],
                    'updated_date'=>date('Y-m-d h:i:s')
                ];

                $tableName = "payment_advices";
                $count = Yii::$app->db->createCommand()
                            ->insert($tableName, $array)
                            ->execute();
                $ref_id = Yii::$app->db->getLastInsertID();

                $sql = "select * from payment_advices where status = 'pending' and is_active = '1' and id = '$ref_id'";
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
                $upload_path = './uploads/payment_advices';
                if(!is_dir($upload_path)) {
                    mkdir($upload_path, 0777, TRUE);
                }

                $file_name = $upload_path . '/payment_advice_' . $id . '.pdf';
                $file_path = 'uploads/payment_advices/payment_advice_' . $id . '.pdf';

                // $mpdf->Output('MyPDF.pdf', 'D');
                $mpdf->Output($file_name, 'F');
                // exit;

                $sql = "update payment_advices set payment_advice_path = '$file_path' where id = '$ref_id'";
                $command = Yii::$app->db->createCommand($sql);
                $count = $command->execute();

                $sql = "select * from payment_advices where status = 'pending' and is_active = '1' and id = '$ref_id'";
                $command = Yii::$app->db->createCommand($sql);
                $reader = $command->query();
                $payment_advice = $reader->readAll();
            }
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
}