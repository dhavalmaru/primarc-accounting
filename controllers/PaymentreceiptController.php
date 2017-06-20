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
    public function actionIndex(){
        $payment_receipt = new PaymentReceipt();
        $access = $payment_receipt->getAccess();
        if(count($access)>0) {
            if($access[0]['r_view']==1) {
                $pending = $payment_receipt->getDetails("", "pending");
                $approved = $payment_receipt->getDetails("", "approved");
                $rejected = $payment_receipt->getDetails("", "rejected");

                $payment_receipt->setLog('PaymentReceipt', '', 'View', '', 'View Payment Receipt List', 'acc_payment_receipt', '');
                return $this->render('payment_receipt_list', ['access' => $access, 'pending' => $pending, 'approved' => $approved, 
                                                                'rejected' => $rejected]);
            } else {
                return $this->render('/message', [
                    'title'  => \Yii::t('user', 'Access Denied'),
                    'module' => $this->module,
                    'msg' => '<h4>You donot have access to this page.</h4>'
                ]);
            }
        } else {
            $this->layout = 'other';
            return $this->render('/message', [
                'title'  => \Yii::t('user', 'Session Expired'),
                'module' => $this->module,
                'msg' => 'Session Expired. Please <a href="'.Url::base().'index.php">Login</a> again.'
            ]);
        }
    }

    public function actionCreate(){
        $payment_receipt = new PaymentReceipt();
        $access = $payment_receipt->getAccess();
        if(count($access)>0) {
            if($access[0]['r_insert']==1) {
                $action = 'insert';
                $acc_details = $payment_receipt->getAccountDetails();
                $bank = $payment_receipt->getBanks();
                $approver_list = $payment_receipt->getApprover($action);

                $payment_receipt->setLog('PaymentReceipt', '', 'Insert', '', 'Insert Payment Receipt Details', 'acc_payment_receipt', '');
                return $this->render('payment_receipt_details', ['action' => $action, 'acc_details' => $acc_details, 
                                                                 'bank' => $bank, 'approver_list' => $approver_list]);
            } else {
                return $this->render('/message', [
                    'title'  => \Yii::t('user', 'Access Denied'),
                    'module' => $this->module,
                    'msg' => '<h4>You donot have access to this page.</h4>'
                ]);
            }
        } else {
            $this->layout = 'other';
            return $this->render('/message', [
                'title'  => \Yii::t('user', 'Session Expired'),
                'module' => $this->module,
                'msg' => 'Session Expired. Please <a href="'.Url::base().'index.php">Login</a> again.'
            ]);
        }
    }

    public function actionRedirect($action, $id) {
        $payment_receipt = new PaymentReceipt();
        $data = $payment_receipt->getDetails($id, "");
        $acc_details = $payment_receipt->getAccountDetails();
        $bank = $payment_receipt->getBanks();
        $approver_list = $payment_receipt->getApprover($action);

        return $this->render('payment_receipt_details', ['action' => $action, 'data' => $data, 'acc_details' => $acc_details, 
                                                         'bank' => $bank, 'approver_list' => $approver_list]);
    }

    public function actionView($id) {
        $payment_receipt = new PaymentReceipt();
        $access = $payment_receipt->getAccess();
        if(count($access)>0) {
            if($access[0]['r_view']==1) {
                $payment_receipt->setLog('PaymentReceipt', '', 'View', '', 'View Payment Receipt Details', 'acc_payment_receipt', $id);
                return $this->actionRedirect('view', $id);
            } else {
                return $this->render('/message', [
                    'title'  => \Yii::t('user', 'Access Denied'),
                    'module' => $this->module,
                    'msg' => '<h4>You donot have access to this page.</h4>'
                ]);
            }
        } else {
            $this->layout = 'other';
            return $this->render('/message', [
                'title'  => \Yii::t('user', 'Session Expired'),
                'module' => $this->module,
                'msg' => 'Session Expired. Please <a href="'.Url::base().'index.php">Login</a> again.'
            ]);
        }
    }

    public function actionEdit($id) {
        $payment_receipt = new PaymentReceipt();
        $access = $payment_receipt->getAccess();
        $data = $payment_receipt->getDetails($id, "");
        if(count($access)>0) {
            if($access[0]['r_edit']==1 && $access[0]['session_id']==$data[0]['updated_by']) {
                $payment_receipt->setLog('PaymentReceipt', '', 'Edit', '', 'Edit Payment Receipt Details', 'acc_payment_receipt', $id);
                return $this->actionRedirect('edit', $id);
            } else {
                return $this->render('/message', [
                    'title'  => \Yii::t('user', 'Access Denied'),
                    'module' => $this->module,
                    'msg' => '<h4>You donot have access to this page.</h4>'
                ]);
            }
        } else {
            $this->layout = 'other';
            return $this->render('/message', [
                'title'  => \Yii::t('user', 'Session Expired'),
                'module' => $this->module,
                'msg' => 'Session Expired. Please <a href="'.Url::base().'index.php">Login</a> again.'
            ]);
        }
    }

    public function actionAuthorise($id) {
        $payment_receipt = new PaymentReceipt();
        $access = $payment_receipt->getAccess();
        $data = $payment_receipt->getDetails($id, "");
        if(count($access)>0) {
            if($access[0]['r_approval']==1 && $access[0]['session_id']!=$data[0]['updated_by']) {
                $payment_receipt->setLog('PaymentReceipt', '', 'Authorise', '', 'Authorise Payment Receipt Details', 'acc_payment_receipt', $id);
                return $this->actionRedirect('authorise', $id);
            } else {
                return $this->render('/message', [
                    'title'  => \Yii::t('user', 'Access Denied'),
                    'module' => $this->module,
                    'msg' => '<h4>You donot have access to this page.</h4>'
                ]);
            }
        } else {
            $this->layout = 'other';
            return $this->render('/message', [
                'title'  => \Yii::t('user', 'Session Expired'),
                'module' => $this->module,
                'msg' => 'Session Expired. Please <a href="'.Url::base().'index.php">Login</a> again.'
            ]);
        }
    }

    public function actionSave(){   
        $payment_receipt = new PaymentReceipt();
        $result = $payment_receipt->save();
        $this->redirect(array('paymentreceipt/index'));
    }

    public function actionGetaccdetails(){
        $payment_receipt = new PaymentReceipt();
        $request = Yii::$app->request;
        $acc_id = $request->post('acc_id');
        $data = $payment_receipt->getAccountDetails($acc_id);
        echo json_encode($data);
    }

    public function actionGetledger(){   
        $request = Yii::$app->request;

        $id = $request->post('id');
        $acc_id = $request->post('acc_id');

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
                $ledger_code = '';
                $ledger_name = '';
                
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
                if(isset($data[$i]['cp_acc_id'])){
                    if($data[$i]['cp_acc_id']!=$acc_id){
                        $ledger_code = $data[$i]['cp_ledger_code'];
                        $ledger_name = $data[$i]['cp_ledger_name'];
                    }
                }
                if($ledger_code == ''){
                    $ledger_code = $data[$i]['ledger_code'];
                    $ledger_name = $data[$i]['ledger_name'];
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
                                            <input type="text" class="form-control" id="account_name_'.$i.'" name="account_name[]" value="'.$ledger_name.'" readonly />
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
        }

        echo $tbody;
    }

    public function actionViewpaymentadvice($id){
        $payment_receipt = new PaymentReceipt();
        $access = $payment_receipt->getAccess();
        if(count($access)>0) {
            if($access[0]['r_view']==1) {
                $data = $payment_receipt->getPaymentAdviceDetails($id);
        
                $payment_receipt->setLog('PaymentReceipt', '', 'View', '', 'View Payment Advice Details', 'acc_payment_receipt', $id);
                $this->layout = false;
                return $this->render('payment_advice', [
                    'payment_details' => $data['payment_details'],
                    'entry_details' => $data['entry_details'],
                    'vendor_details' => $data['vendor_details']
                ]);
            } else {
                return $this->render('/message', [
                    'title'  => \Yii::t('user', 'Access Denied'),
                    'module' => $this->module,
                    'msg' => '<h4>You donot have access to this page.</h4>'
                ]);
            }
        } else {
            $this->layout = 'other';
            return $this->render('/message', [
                'title'  => \Yii::t('user', 'Session Expired'),
                'module' => $this->module,
                'msg' => 'Session Expired. Please <a href="'.Url::base().'index.php">Login</a> again.'
            ]);
        }
    }

    public function actionDownload($id){
        $payment_receipt = new PaymentReceipt();
        $access = $payment_receipt->getAccess();
        if(count($access)>0) {
            if($access[0]['r_view']==1) {
                $data = $payment_receipt->getPaymentAdviceDetails($id);
                $file = "";

                $payment_receipt->setLog('PaymentReceipt', '', 'Download', '', 'Download Payment Advice Details', 'acc_payment_receipt', $id);
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
            } else {
                return $this->render('/message', [
                    'title'  => \Yii::t('user', 'Access Denied'),
                    'module' => $this->module,
                    'msg' => '<h4>You donot have access to this page.</h4>'
                ]);
            }
        } else {
            $this->layout = 'other';
            return $this->render('/message', [
                'title'  => \Yii::t('user', 'Session Expired'),
                'module' => $this->module,
                'msg' => 'Session Expired. Please <a href="'.Url::base().'index.php">Login</a> again.'
            ]);
        }
    }

    public function actionEmailpaymentadvice($id){
        $payment_receipt = new PaymentReceipt();
        $access = $payment_receipt->getAccess();
        if(count($access)>0) {
            if($access[0]['r_view']==1) {
                $data = $payment_receipt->getPaymentAdviceDetails($id);
                
                $payment_receipt->setLog('PaymentReceipt', '', 'Email', '', 'Email Payment Advice Details', 'acc_payment_receipt', $id);
                return $this->render('email', [
                    'payment_details' => $data['payment_details'],
                    'entry_details' => $data['entry_details'],
                    'vendor_details' => $data['vendor_details'],
                    'payment_advice' => $data['payment_advice']
                ]);
            } else {
                return $this->render('/message', [
                    'title'  => \Yii::t('user', 'Access Denied'),
                    'module' => $this->module,
                    'msg' => '<h4>You donot have access to this page.</h4>'
                ]);
            }
        } else {
            $this->layout = 'other';
            return $this->render('/message', [
                'title'  => \Yii::t('user', 'Session Expired'),
                'module' => $this->module,
                'msg' => 'Session Expired. Please <a href="'.Url::base().'index.php">Login</a> again.'
            ]);
        }
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
            $email_sent_status = '1';
            $error_message = '';
        } else {
            $data['response'] = 'Mail Sending Failed.';
            $email_sent_status = '0';
            $error_message = $response;
        }

        $attachment_type = 'PDF';
        $vendor_name = $request->post('vendor_name');
        $company_id = $request->post('company_id');
        $payment_receipt = new PaymentReceipt();
        $payment_receipt->setEmailLog($vendor_name, $from, $to, $id, $body, $attachment, 
                                        $attachment_type, $email_sent_status, $error_message, $company_id);


        $data['id'] = $id;
        $data['payment_id'] = $payment_id;

        return $this->render('email_response', ['data' => $data]);
    }
}