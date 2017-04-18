<?php

namespace app\controllers;

use Yii;
use app\models\PaymentReceipt;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;

class PaymentreceiptController extends Controller
{
    public function actionIndex()
    {
        $payment_receipt = new PaymentReceipt();
        $pending = $payment_receipt->getDetails("", "pending");
        $approved = $payment_receipt->getDetails("", "approved");

        return $this->render('payment_receipt_list', ['pending' => $pending, 'approved' => $approved]);
    }

    public function actionGetaccdetails(){
        $payment_receipt = new PaymentReceipt();
        $request = Yii::$app->request;
        $acc_id = $request->post('acc_id');
        // $acc_id = '43';
        // $acc_code = '';
        $data = $payment_receipt->getAccountDetails($acc_id);
        // if(count($data)>0){
        //     $acc_code = $data[0]['code'];
        // }
        echo json_encode($data);
    }

    public function actionCreate()
    {
        $payment_receipt = new PaymentReceipt();
        $transaction = "Create";
        $acc_details = $payment_receipt->getAccountDetails();
        $bank = $payment_receipt->getBanks();
        return $this->render('payment_receipt_details', ['transaction'=>$transaction, 'acc_details' => $acc_details, 'bank' => $bank]);
    }

    public function actionEdit($id)
    {
        $payment_receipt = new PaymentReceipt();
        $data = $payment_receipt->getDetails($id, "");
        $debit = null;
        $credit = null;
        $transaction = "Update";
        $acc_details = $payment_receipt->getAccountDetails();
        $bank = $payment_receipt->getBanks();

        return $this->render('payment_receipt_details', ['transaction'=>$transaction, 'data' => $data, 'acc_details' => $acc_details, 'bank' => $bank]);
    }

    public function actionGetledger()
    {   
        $request = Yii::$app->request;

        $id = $request->post('id');
        $acc_id = $request->post('acc_id');

        // $id = "";
        // $acc_id = 4;
        
        $payment_receipt = new PaymentReceipt();
        $data = $payment_receipt->getLedger($id, $acc_id);
        $mycomponent = Yii::$app->mycomponent;
        $tbody = "";
        // $table = "";

        if(count($data)>0){
            $total_debit_amt = 0;
            $total_credit_amt = 0;
            $paying_debit_amt = 0;
            $paying_credit_amt = 0;
            $net_debit_amt = 0;
            $net_credit_amt = 0;

            for($i=0; $i<count($data); $i++){
                if($data[$i]['type']=="Debit"){
                    $debit_amt = $data[$i]['amount'];
                    $credit_amt = 0;
                } else {
                    $debit_amt = 0;
                    $credit_amt = $data[$i]['amount'];
                }
                if($data[$i]['is_paid']=="1"){
                    if($data[$i]['type']=="Debit"){
                        $paying_debit_amt = $paying_debit_amt + $data[$i]['amount'];
                    } else {
                        $paying_credit_amt = $paying_credit_amt + $data[$i]['amount'];
                    }
                }

                $tbody = $tbody . '<tr>
                                        <td class="text-center"> 
                                            <div class="checkbox"> 
                                                <input type="checkbox" class="check" id="chk_'.$i.'" value="1" '.(($data[$i]['is_paid']=="1")?"checked":"").' onChange="getLedgerTotal();" /> 
                                                <input type="hidden" class="form-control" name="chk[]" id="chk_val_'.$i.'" value="'.(($data[$i]['is_paid']=="1")?"1":"0").'" />
                                            </div> 
                                        </td>
                                        <td>
                                            <input type="hidden" class="form-control" id="ledger_id_'.$i.'" name="ledger_id[]" value="'.$data[$i]['id'].'" />
                                            <input type="hidden" class="form-control" id="ledger_type_'.$i.'" name="ledger_type[]" value="'.$data[$i]['ledger_type'].'" />
                                            <input type="hidden" class="form-control" id="vendor_id_'.$i.'" name="vendor_id[]" value="'.$data[$i]['vendor_id'].'" />
                                            <input type="text" class="form-control" id="account_name_'.$i.'" name="account_name[]" value="'.$data[$i]['account_name'].'" readonly />
                                        </td>
                                        <td> 
                                            <input type="text" class="form-control" id="invoice_no_'.$i.'" name="invoice_no[]" value="'.$data[$i]['invoice_no'].'" readonly />
                                        </td>
                                        <td> 
                                            <input type="text" class="form-control" id="gi_date_'.$i.'" name="gi_date[]" value="'.(($data[$i]['gi_date']!=null && $data[$i]['gi_date']!='')?date('d/m/Y',strtotime($data[$i]['gi_date'])):'').'" readonly />
                                        </td>
                                        <td> 
                                            <input type="text" class="form-control" id="invoice_date_'.$i.'" name="invoice_date[]" value="'.(($data[$i]['invoice_date']!=null && $data[$i]['invoice_date']!='')?date('d/m/Y',strtotime($data[$i]['invoice_date'])):'').'" readonly />
                                        </td>
                                        <td> 
                                            <input type="text" class="form-control" id="due_date_'.$i.'" name="due_date[]" value="'.(($data[$i]['due_date']!=null && $data[$i]['due_date']!='')?date('d/m/Y',strtotime($data[$i]['due_date'])):'').'" readonly />
                                        </td>
                                        <td class="text-right">
                                            <input type="text" class="form-control text-right" id="debit_amt_'.$i.'" name="debit_amt[]" value="'.$mycomponent->format_money($debit_amt,2).'" readonly />
                                        </td>
                                        <td class="text-right">
                                            <input type="text" class="form-control text-right" id="credit_amt_'.$i.'" name="credit_amt[]" value="'.$mycomponent->format_money($credit_amt,2).'" readonly />
                                        </td> 
                                    </tr>';

                $total_debit_amt = $total_debit_amt + $debit_amt;
                $total_credit_amt = $total_credit_amt + $credit_amt;
            }

            $net_debit_amt = $total_debit_amt - $paying_debit_amt;
            $net_credit_amt = $total_credit_amt - $paying_credit_amt;

            if(($paying_credit_amt-$paying_debit_amt)>=0){
                $payable_credit_amt = $paying_credit_amt-$paying_debit_amt;
                $payable_debit_amt = 0;
            } else {
                $payable_debit_amt = ($paying_credit_amt-$paying_debit_amt)*-1;
                $payable_credit_amt = 0;
            }

            $tbody = $tbody . '<tr class="bold-text">
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-right">Total Amount</td>
                                    <td class="text-right">
                                        <input type="text" class="form-control text-right" id="total_debit_amt" name="total_debit_amt" value="'.$mycomponent->format_money($total_debit_amt,2).'" readonly />
                                    </td>
                                    <td class="text-right">
                                        <input type="text" class="form-control text-right" id="total_credit_amt" name="total_credit_amt" value="'.$mycomponent->format_money($total_credit_amt,2).'" readonly />
                                    </td> 
                                </tr>
                                <tr class="bold-text">
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-right">Amount Paying</td>
                                    <td class="text-right">
                                        <input type="text" class="form-control text-right" id="paying_debit_amt" name="paying_debit_amt" value="'.$mycomponent->format_money($paying_debit_amt,2).'" readonly />
                                    </td>
                                    <td class="text-right">
                                        <input type="text" class="form-control text-right" id="paying_credit_amt" name="paying_credit_amt" value="'.$mycomponent->format_money($paying_credit_amt,2).'" readonly />
                                    </td> 
                                </tr>
                                <tr class="bold-text">
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-right">Net Total Amount</td>
                                    <td class="text-right">
                                        <input type="text" class="form-control text-right" id="net_debit_amt" name="net_debit_amt" value="'.$mycomponent->format_money($net_debit_amt,2).'" readonly />
                                    </td>
                                    <td class="text-right">
                                        <input type="text" class="form-control text-right" id="net_credit_amt" name="net_credit_amt" value="'.$mycomponent->format_money($net_credit_amt,2).'" readonly />
                                    </td> 
                                </tr>
                                <tr class="bold-text">
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-right">Payable Amount</td>
                                    <td class="text-right">
                                        <input type="text" class="form-control text-right" id="payable_debit_amt" name="payable_debit_amt" value="'.$mycomponent->format_money($payable_debit_amt,2).'" readonly />
                                    </td>
                                    <td class="text-right">
                                        <input type="text" class="form-control text-right" id="payable_credit_amt" name="payable_credit_amt" value="'.$mycomponent->format_money($payable_credit_amt,2).'" readonly />
                                    </td> 
                                </tr>';

            // $tbody = '<tbody>'. $tbody . '</tbody>';

            // $thead = '<thead>
            //                 <tr>
            //                     <th class="text-center" width="60"> 
            //                         <div class="  ">
            //                             <input type="checkbox" class="check" id="checkAll" value="">
            //                         </div>
            //                     </th> 
            //                     <th class="text-center">  Particular </th>
            //                     <th class="text-center" width="120"> Debit </th>
            //                     <th class="text-center" width="120">  Credit </th> 
            //                 </tr>
            //             </thead>';

            // $table = '<table class="table table-bordered table-hover" id="tab_logic">' . $thead . $tbody . '</table>';
        }

        echo $tbody;
    }

    public function actionSave()
    {   
        $payment_receipt = new PaymentReceipt();
        $transaction_id = $payment_receipt->save();
        $this->redirect(array('paymentreceipt/index'));
    }

    public function actionViewpaymentadvice($id){
        $model = new PaymentReceipt();
        $data = $model->getPaymentAdviceDetails($id);
        
        $this->layout = false;
        return $this->render('payment_advice', [
            'payment_details' => $data['payment_details'],
            'entry_details' => $data['entry_details'],
            'vendor_details' => $data['vendor_details']
        ]);
    }

    public function actionDownload($id){
        $model = new PaymentReceipt();
        $data = $model->getPaymentAdviceDetails($id);
        $file = "";

        if(isset($data['payment_advice'])){
            if(count($data['payment_advice'])>0){
                $payment_advice = $data['payment_advice'];
                $file = $payment_advice[0]['payment_advice_path'];
            }
        }

        if( file_exists( $file ) ){
            Yii::$app->response->sendFile($file);
        } else {
            echo $file;
        }
    }

    public function actionEmailpaymentadvice($id){
        $model = new PaymentReceipt();
        $data = $model->getPaymentAdviceDetails($id);
        $file = "";

        return $this->render('email', [
            'payment_details' => $data['payment_details'],
            'entry_details' => $data['entry_details'],
            'vendor_details' => $data['vendor_details'],
            'payment_advice' => $data['payment_advice']
        ]);
    }

    public function actionEmail(){
        $request = Yii::$app->request;
        $mycomponent = Yii::$app->mycomponent;
        $session = Yii::$app->session;

        $id = $request->post('id');
        $payment_id = $request->post('payment_id');
        // $from = $request->post('from');
        $from = 'dhaval.maru@otbconsulting.co.in';
        $to = $request->post('to');
        // $to = 'prasad.bhisale@otbconsulting.co.in';
        $subject = $request->post('subject');
        $attachment = $request->post('attachment');
        $body = $request->post('body');

        // $grn_id = '28';
        // $from = 'dhaval.maru@otbconsulting.co.in';
        // $to = 'prasad.bhisale@otbconsulting.co.in';
        // $subject = 'Test Email';
        // $attachment = 'uploads/debit_notes/28/debit_note_invoice_90.pdf';
        // $body = 'Testing';

        $message = Yii::$app->mailer->compose();
        $message->setFrom($from);
        $message->setTo($to);
        $message->setSubject($subject);
        $message->setTextBody($body);
        // $message->setHtmlBody($body);
        $message->attach($attachment);

        $response = $message->send();
        if($response=='1'){
            $data['response'] = 'Mail Sent.';
        } else {
            $data['response'] = 'Mail Sending Failed.';
        }
        $data['id'] = $id;
        $data['payment_id'] = $payment_id;

        return $this->render('email_response', ['data' => $data]);
    }
}