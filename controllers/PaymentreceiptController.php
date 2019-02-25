<?php

namespace app\controllers;

use Yii;
use app\models\PaymentReceipt;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet as Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as Xlsx;
/*use PhpOffice\PhpSpreadsheet\IOFactory as IOFactory;*/
use PhpOffice\PhpSpreadsheet\Cell\DataValidation as DataValidation;
/*use PhpOffice\PhpSpreadsheet\Style\Protection as Protection;*/
use Box\Spout\Reader\ReaderFactory as ReaderFactory;
use Box\Spout\Writer\WriterFactory as WriterFactory;
use Box\Spout\Common\Type as Type;
use phpoffice\phpexcel\Classes\PHPExcel as PHPExcel;
use phpoffice\phpexcel\Classes\PHPExcel\PHPExcel_IOFactory as PHPExcel_IOFactory;
use phpoffice\phpexcel\Classes\PHPExcel\Cell\PHPExcel_Cell_DataValidation as PHPExcel_Cell_DataValidation;
use phpoffice\phpexcel\Classes\PHPExcel\PHPExcel_Worksheet as PHPExcel_Worksheet;

/*require_once __DIR__ . "/vendor/autoload.php";*/
/*require_once __DIR__ . "../../vendor/phpoffice\phpexcel\Classes\PHPExcel\IOFactory.php";*/


class PaymentreceiptController extends Controller
{
    public function actionIndex() {
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

    public function actionGetotheraccdetails() {
		$payment_receipt = new PaymentReceipt();
		$request = Yii::$app->request;
		$trans_type = $request->post('trans_type');
		$data = $payment_receipt->getaccbank1($trans_type);

        $result='<option value="">Select</option>';
        for($i=0; $i<count($data); $i++){
            $result = $result . '<option value="'.$data[$i]['id'].'">'.$data[$i]['legal_name'].'</option>';
        }

		echo $result;
	}
	
    public function actionGetotheraccdetails1() {
        $payment_receipt = new PaymentReceipt();
        $request = Yii::$app->request;
        $trans_type = $request->post('trans_type');
        $data = $payment_receipt->getacc1($trans_type);
        echo json_encode($data);
    }
	
    public function actionCreate() {
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

    public function actionSave() {
        $payment_receipt = new PaymentReceipt();
        $result = $payment_receipt->save();
        $this->redirect(array('paymentreceipt/index'));
    }

    public function actionGetaccdetails() {
        $payment_receipt = new PaymentReceipt();
        $request = Yii::$app->request;
        $acc_id = $request->post('acc_id');
        $data = $payment_receipt->getAccountDetails($acc_id);
        echo json_encode($data);
    }
	
	public function actionGetaccbankdetails() {
        $payment_receipt = new PaymentReceipt();
        $request = Yii::$app->request;
        $bank_id = $request->post('bank_id');
        $data = $payment_receipt->getBanks($bank_id);
        echo json_encode($data);
    }
	
    public function actionGetledger() {
        $request = Yii::$app->request;

        $id = $request->post('id');
        $acc_id = $request->post('acc_id');

        $payment_receipt = new PaymentReceipt();
        $data = $payment_receipt->getLedger($id, $acc_id);
        $mycomponent = Yii::$app->mycomponent;
        $tbody = "";

        if(count($data)>0){
            $total_debit_amt = 0;
            $total_credit_amt = 0;
            $paying_debit_amt = 0;
            $paying_credit_amt = 0;
            $net_debit_amt = 0;
            $net_credit_amt = 0;
            $total_transaction = '';
            $total_amount_total = 0;
            $total_paid_transaction = '';
            $total_paid_amount_total = 0;
            $paying_transaction = '';
            $paying_amount_total = 0;
            $bal_transaction = '';
            $bal_amount_total = 0;

            for($i=0; $i<count($data); $i++){
                $ledger_code = '';
                $ledger_name = '';
                $transaction = '';

                $transaction = $data[$i]['type'];
                $amount = $data[$i]['amount'];
                $total_paid_amount = $data[$i]['total_paid_amount'];
                $amount_to_pay = $data[$i]['amount_to_pay'];
                $bal_amount = $data[$i]['bal_amount'];

                $bal_amount = ($amount-$total_paid_amount-$amount_to_pay);
                
                if(strtoupper(trim($transaction))=="DEBIT"){
                    $amount = $amount*-1;
                    $total_paid_amount = $total_paid_amount*-1;
                    $amount_to_pay = $amount_to_pay*-1;
                    $bal_amount = $bal_amount*-1;
                }

                // if(strtoupper(trim($transaction))=="DEBIT"){
                //     $total_amount_total = $total_amount_total-$amount;
                //     $total_paid_amount_total = $total_paid_amount_total-$total_paid_amount;
                //     $paying_amount_total = $paying_amount_total-$amount_to_pay;
                //     $bal_amount_total = $bal_amount_total-$bal_amount;
                // } else {
                //     $total_amount_total = $total_amount_total+$amount;
                //     $total_paid_amount_total = $total_paid_amount_total+$total_paid_amount;
                //     $paying_amount_total = $paying_amount_total+$amount_to_pay;
                //     $bal_amount_total = $bal_amount_total+$bal_amount;
                // }

                $total_amount_total = $total_amount_total+$amount;
                $total_paid_amount_total = $total_paid_amount_total+$total_paid_amount;
                $paying_amount_total = $paying_amount_total+$amount_to_pay;
                $bal_amount_total = $bal_amount_total+$bal_amount;

                if(strtoupper(trim($transaction))=="DEBIT"){
                    $debit_amt = $amount;
                    $credit_amt = 0;
                } else {
                    $debit_amt = 0;
                    $credit_amt = $amount;
                }

                if(isset($data[$i]['cp_acc_id'])){
                    if($data[$i]['cp_acc_id']!=$acc_id){
                        $ledger_code = $data[$i]['cp_ledger_code'];
                        $ledger_name = $data[$i]['cp_ledger_name'];
                    }
                }
                if($ledger_code == ''){
                    $ledger_code = $data[$i]['ledger_code'];
                    // $ledger_name = $data[$i]['new_ledger_name'];
                    $ledger_name = $data[$i]['ref_type'];
                }

                $tbody = $tbody . '<tr>
                                        <td class="text-center"> 
                                            <div class="checkbox"> 
                                                <input type="checkbox" class="check" id="chk_'.$i.'" value="1" onChange="setAmount(this);" />
                                                <input type="hidden" class="form-control" name="chk[]" id="chk_val_'.$i.'" value="" />
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
                                        <!-- <td class="text-right">
                                            <input type="text" class="form-control text-right" id="debit_amt_'.$i.'" name="debit_amt[]" value="'.$mycomponent->format_money($debit_amt,2).'" readonly />
                                        </td>
                                        <td class="text-right">
                                            <input type="text" class="form-control text-right" id="credit_amt_'.$i.'" name="credit_amt[]" value="'.$mycomponent->format_money($credit_amt,2).'" readonly />
                                        </td> -->
                                        <td>
                                            <input type="text" class="form-control text-right" id="transaction_'.$i.'" name="transaction[]" value="'.$transaction.'" readonly />
                                        </td>
                                        <td class="text-right">
                                            <input type="text" class="form-control text-right" id="total_amount_'.$i.'" name="total_amount[]" value="'.$mycomponent->format_money($amount,2).'" readonly />
                                        </td>
                                        <td class="text-right">
                                            <input type="text" class="form-control text-right" id="total_paid_amount_'.$i.'" name="total_paid_amount[]" value="'.$mycomponent->format_money($total_paid_amount,2).'" readonly />
                                        </td>
                                        <td class="text-right">
                                            <input type="text" class="form-control text-right" id="amount_to_pay_'.$i.'" name="amount_to_pay[]" value="'.$mycomponent->format_money($amount_to_pay,2).'" onChange="getTotal();" />
                                        </td>
                                        <td class="text-right">
                                            <input type="text" class="form-control text-right" id="bal_amount_'.$i.'" name="bal_amount[]" value="'.$mycomponent->format_money($bal_amount,2).'" readonly />
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
                // $payable_debit_amt = ($paying_credit_amt-$paying_debit_amt)*-1;
                $payable_debit_amt = $paying_credit_amt-$paying_debit_amt;
                $payable_credit_amt = 0;
            }

            if($total_amount_total<0){
                // $total_amount_total = $total_amount_total*-1;
                $total_transaction = 'Debit';
            } else {
                $total_transaction = 'Credit';
            }
            if($total_paid_amount_total<0){
                // $total_paid_amount_total = $total_paid_amount_total*-1;
                $total_paid_transaction = 'Debit';
            } else {
                $total_paid_transaction = 'Credit';
            }
            if($paying_amount_total<0){
                // $paying_amount_total = $paying_amount_total*-1;
                $paying_transaction = 'Debit';
            } else {
                $paying_transaction = 'Credit';
            }
            if($bal_amount_total<0){
                // $bal_amount_total = $bal_amount_total*-1;
                $bal_transaction = 'Debit';
            } else {
                $bal_transaction = 'Credit';
            }

            $tbody = $tbody . '<tr class="bold-text">
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <!-- <td class="text-center"></td> -->
                                    <td class="text-right" colspan="2">Total Amount</td>
                                    <!-- <td class="text-right">
                                        <input type="text" class="form-control text-right" id="total_debit_amt" name="total_debit_amt" value="'.$mycomponent->format_money($total_debit_amt,2).'" readonly />
                                    </td>
                                    <td class="text-right">
                                        <input type="text" class="form-control text-right" id="total_credit_amt" name="total_credit_amt" value="'.$mycomponent->format_money($total_credit_amt,2).'" readonly />
                                    </td> -->
                                    <td class="text-right">
                                        <input type="text" class="form-control text-right" id="total_transaction" name="total_transaction" value="'.$total_transaction.'" readonly />
                                    </td>
                                    <td class="text-right">
                                        <input type="text" class="form-control text-right" id="total_amount_total" name="total_amount_total" value="'.$mycomponent->format_money($total_amount_total,2).'" readonly />
                                    </td>
                                    <td class="text-right"></td>
                                    <td class="text-right"></td>
                                    <td class="text-right"></td>
                                </tr>
                                <tr class="bold-text">
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <!-- <td class="text-center"></td> -->
                                    <td class="text-right" colspan="2">Total Paid Amount</td>
                                    <!-- <td class="text-right">
                                        <input type="text" class="form-control text-right" id="paying_debit_amt" name="paying_debit_amt" value="'.$mycomponent->format_money($paying_debit_amt,2).'" readonly />
                                    </td>
                                    <td class="text-right">
                                        <input type="text" class="form-control text-right" id="paying_credit_amt" name="paying_credit_amt" value="'.$mycomponent->format_money($paying_credit_amt,2).'" readonly />
                                    </td> -->
                                    <td class="text-right">
                                        <input type="text" class="form-control text-right" id="total_paid_transaction" name="total_paid_transaction" value="'.$total_paid_transaction.'" readonly />
                                    </td>
                                    <td class="text-right">
                                        <input type="text" class="form-control text-right" id="total_paid_amount_total" name="total_paid_amount_total" value="'.$mycomponent->format_money($total_paid_amount_total,2).'" readonly />
                                    </td>
                                    <td class="text-right"></td>
                                    <td class="text-right"></td>
                                    <td class="text-right"></td>
                                </tr>
                                <tr class="bold-text">
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <!-- <td class="text-center"></td> -->
                                    <td class="text-right" colspan="2">Amount Paying</td>
                                    <!-- <td class="text-right">
                                        <input type="text" class="form-control text-right" id="paying_debit_amt" name="paying_debit_amt" value="'.$mycomponent->format_money($paying_debit_amt,2).'" readonly />
                                    </td>
                                    <td class="text-right">
                                        <input type="text" class="form-control text-right" id="paying_credit_amt" name="paying_credit_amt" value="'.$mycomponent->format_money($paying_credit_amt,2).'" readonly />
                                    </td> -->
                                    <td class="text-right">
                                        <input type="text" class="form-control text-right" id="paying_transaction" name="paying_transaction" value="'.$paying_transaction.'" readonly />
                                    </td>
                                    <td class="text-right">
                                        <input type="text" class="form-control text-right" id="paying_amount_total" name="paying_amount_total" value="'.$mycomponent->format_money($paying_amount_total,2).'" readonly />
                                    </td>
                                    <td class="text-right"></td>
                                    <td class="text-right"></td>
                                    <td class="text-right"></td>
                                </tr>
                                <tr class="bold-text">
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <!-- <td class="text-center"></td> -->
                                    <td class="text-right" colspan="2">Balance Amount</td>
                                    <!-- <td class="text-right">
                                        <input type="text" class="form-control text-right" id="net_debit_amt" name="net_debit_amt" value="'.$mycomponent->format_money($net_debit_amt,2).'" readonly />
                                    </td>
                                    <td class="text-right">
                                        <input type="text" class="form-control text-right" id="net_credit_amt" name="net_credit_amt" value="'.$mycomponent->format_money($net_credit_amt,2).'" readonly />
                                    </td> -->
                                    <td class="text-right">
                                        <input type="text" class="form-control text-right" id="bal_transaction" name="bal_transaction" value="'.$bal_transaction.'" readonly />
                                    </td>
                                    <td class="text-right">
                                        <input type="text" class="form-control text-right" id="bal_amount_total" name="bal_amount_total" value="'.$mycomponent->format_money($bal_amount_total,2).'" readonly />
                                    </td>
                                    <td class="text-right"></td>
                                    <td class="text-right"></td>
                                    <td class="text-right"></td>
                                </tr>
                                <!-- <tr class="bold-text">
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
                                </tr> -->';
        } else {
            $tbody = '<tr class="bold-text">
                        <td class="text-center" colspan="11">No data found.</td>
                    </tr>';
        }

        echo $tbody;
    }

    public function actionViewpaymentadvice($id) {
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

    public function actionDownload($id) {
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

    public function actionEmailpaymentadvice($id) {
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

    public function actionEmail() {
        $request = Yii::$app->request;
        $mycomponent = Yii::$app->mycomponent;
        $session = Yii::$app->session;

        $id = $request->post('id');
        $payment_id = $request->post('payment_id');
        // $from = $request->post('from');
        $from = 'accounts@primarcpecan.com';
        $to = $request->post('to');
        // $to = 'prasad.bhisale@otbconsulting.co.in';
        $subject = $request->post('subject');
        $attachment = $request->post('attachment');
        $body = $request->post('body');

        // $grn_id = '28';
        // $from = 'accounts@primarcpecan.com';
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

    public function actionCreatedpayment($value='') {
        // $payment_receipt = new PaymentReceipt();
        // $access = $payment_receipt->getAccess();
        // if(count($access)>0) {
        //     if($access[0]['r_insert']==1) {
        //         $action = 'insert';
        //         $acc_details = $payment_receipt->getAccountDetails();
        //         $payment_upload = $payment_receipt->getPayment_upload();
        //         return $this->render('payment_upload', ['action' => $action,'acc_details' => $acc_details ,'payment_upload'=>$payment_upload]);
        //     }
        // }

        $payment_receipt = new PaymentReceipt();
        $access = $payment_receipt->getAccess();
        if(count($access)>0) {
            if($access[0]['r_insert']==1) {
                $action = 'insert';
                $acc_details = $payment_receipt->getAccountDetails();
                $payment_upload = $payment_receipt->getPayment_upload();

                return $this->render('payment_upload', ['action' => $action,'acc_details' => $acc_details ,'payment_upload'=>$payment_upload]);
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

    public function actionDownloadleadger() {
        $request = Yii::$app->request;
        $mycomponent = Yii::$app->mycomponent;
        $payment_receipt = new PaymentReceipt();
        // $id = $request->post('id');
        $acc_id = $request->post('acc_id');
        $to_date = $request->post('to_date');
        if($to_date!='') {
           $to_date=$mycomponent->formatdate($to_date);
        }

        $acc_id_list = '';
        if(in_array('ALL',$acc_id)) {   
            $acc_id = array();
            $acc_details = $payment_receipt->getAccounts($to_date);
            for ($i=0; $i <count($acc_details) ; $i++) { 
                $acc_id[]=$acc_details[$i]['acc_id'];
            }
        }

        $acc_id_list  = implode($acc_id, ",");

        $mycomponent = Yii::$app->mycomponent;
        $tbody = "";
        $r_row = 1;
        $original_file = 'uploads/templates/sample_payment.xlsx';
        /*$objPHPExcel = IOFactory::load($original_file);*/
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel = \PHPExcel_IOFactory::load($original_file);
        $objPHPExcel->setActiveSheetIndex(0);
        /*$highestrow = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow(); */
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$r_row,$to_date);
        $r_row = 3;

        // for ($j=0; $j<count($acc_id); $j++) {
        //     $acc_id1 = $acc_id[$j];

        //     $data = $payment_receipt->getLedger($id, $acc_id1, $to_date);
        //     $data1 = $payment_receipt->getAccountDetails($acc_id1, "");

            $data = $payment_receipt->getLedger('', $acc_id_list, $to_date);
            if(count($data)>0) {
                $total_debit_amt = 0;
                $total_credit_amt = 0;
                $paying_debit_amt = 0;
                $paying_credit_amt = 0;
                $net_debit_amt = 0;
                $net_credit_amt = 0;
                $total_transaction = '';
                $total_amount_total = 0;
                $total_paid_transaction = '';
                $total_paid_amount_total = 0;
                $paying_transaction = '';
                $paying_amount_total = 0;
                $bal_transaction = '';
                $bal_amount_total = 0;
                $col=0;

                for($i=0; $i<count($data); $i++) {
                    // $ledger_code = '';
                    // $ledger_name = '';
                    // $transaction = '';

                    $transaction = $data[$i]['type'];
                    $amount = $data[$i]['amount'];
                    $total_paid_amount = $data[$i]['total_paid_amount'];
                    $amount_to_pay = $data[$i]['amount_to_pay'];
                    $bal_amount = $data[$i]['bal_amount'];

                    $bal_amount = ($amount-$total_paid_amount-$amount_to_pay);
                    
                    if(strtoupper(trim($transaction))=="DEBIT"){
                        $amount = $amount*-1;
                        $total_paid_amount = $total_paid_amount*-1;
                        $amount_to_pay = $amount_to_pay*-1;
                        $bal_amount = $bal_amount*-1;
                    }

                    // $total_amount_total = $total_amount_total+$amount;
                    // $total_paid_amount_total = $total_paid_amount_total+$total_paid_amount;
                    // $paying_amount_total = $paying_amount_total+$amount_to_pay;
                    // $bal_amount_total = $bal_amount_total+$bal_amount;

                    // if(strtoupper(trim($transaction))=="DEBIT"){
                    //     $debit_amt = $amount;
                    //     $credit_amt = 0;
                    // } else {
                    //     $debit_amt = 0;
                    //     $credit_amt = $amount;
                    // }

                    // if(isset($data[$i]['cp_acc_id'])){
                    //     if($data[$i]['cp_acc_id']!=$acc_id1){
                    //         $ledger_code = $data[$i]['cp_ledger_code'];
                    //         $ledger_name = $data[$i]['cp_ledger_name'];
                    //     }
                    // }
                    // if($ledger_code == ''){
                    //     $ledger_code = $data[$i]['ledger_code'];
                    //     // $ledger_name = $data[$i]['new_ledger_name'];
                    //     $ledger_name = $data[$i]['ref_type'];
                    // }

                    $ids = $data[$i]['id'].' &&'.$data[$i]['ledger_type'].' &&'.$data[$i]['vendor_id'].' &&';

                    $objPHPExcel->getActiveSheet()->setCellValue('A'.$r_row,($r_row-2));
                    // $objPHPExcel->getActiveSheet()->setCellValue('B'.$r_row,$data1[0]['legal_name']);
                    // $objPHPExcel->getActiveSheet()->setCellValue('C'.$r_row,$data1[0]['code']);

                    if($data[$i]['ref_date']=='2018-03-31'){
                        $objPHPExcel->getActiveSheet()->setCellValue('B'.$r_row,$data[$i]['ledger_name']);
                        $objPHPExcel->getActiveSheet()->setCellValue('C'.$r_row,$data[$i]['ledger_code']);
                    } else {
                        $objPHPExcel->getActiveSheet()->setCellValue('B'.$r_row,$data[$i]['cp_ledger_name']);
                        $objPHPExcel->getActiveSheet()->setCellValue('C'.$r_row,$data[$i]['cp_ledger_code']);
                    }
                    
                    $objPHPExcel->getActiveSheet()->setCellValue('D'.$r_row,$ids);
                    $objPHPExcel->getActiveSheet()->setCellValue('E'.$r_row,$data[$i]['voucher_id']);
                    $objPHPExcel->getActiveSheet()->setCellValue('F'.$r_row,$data[$i]['ref_type']);
                    $objPHPExcel->getActiveSheet()->setCellValue('G'.$r_row,$data[$i]['entry_type']);
                    $objPHPExcel->getActiveSheet()->setCellValue('H'.$r_row,$data[$i]['invoice_no']);

                    if($data[$i]['gi_date']!='')
                        $gi_date = date("d-m-Y",strtotime($data[$i]['gi_date']));
                    else
                        $gi_date = '';
                    $objPHPExcel->getActiveSheet()->setCellValue('I'.$r_row,$gi_date);

                    if($data[$i]['invoice_date']!='')
                        $invoice_date = date("d-m-Y",strtotime($data[$i]['invoice_date']));
                    else
                        $invoice_date = '';
                    $objPHPExcel->getActiveSheet()->setCellValue('J'.$r_row,$invoice_date);

                    if($data[$i]['due_date']!='')
                        $due_date = date("d-m-Y",strtotime($data[$i]['due_date']));
                    else
                        $due_date = '';
                    $objPHPExcel->getActiveSheet()->setCellValue('K'.$r_row,$due_date);
                    
                    $objPHPExcel->getActiveSheet()->setCellValue('L'.$r_row,$data[$i]['type']);
                    $objPHPExcel->getActiveSheet()->setCellValue('M'.$r_row,round($amount,2));
                    $objPHPExcel->getActiveSheet()->setCellValue('N'.$r_row,round($total_paid_amount,2));
                    $objPHPExcel->getActiveSheet()->setCellValue('O'.$r_row,round($bal_amount,2));
                    $r_row = $r_row+1;
                }
            }
        // }

        $r_row = $r_row - 1;
        if($r_row!=2){
            $objPHPExcel->getActiveSheet()->getStyle('P3:U'.$r_row)->getProtection()->setLocked(\PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
        }
        $objPHPExcel->getActiveSheet()->getProtection()->setPassword('dhaval1234');
        $objPHPExcel->getActiveSheet()->getProtection()->setSheet(true);
        
        $bank = $payment_receipt->getBanks();
        $row_t = 1;
        $objPHPExcel->setActiveSheetIndex(1);
        for($i=0; $i<count($bank); $i++) { 
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$row_t,$bank[$i]['legal_name']);
            $row_t = $row_t+1;
        }

        $objPHPExcel->setActiveSheetIndex(0);
        for($j=3; $j<=$r_row; $j++) {
            $objValidation = $objPHPExcel->getActiveSheet(0)->getCell('Q'.$j)->getDataValidation();
            $this->common_excel($objValidation);
            $objValidation->setFormula1('\'Sheet2\'!$A$1:$A$'.($row_t-1));
        }

        for($k=3; $k<=$r_row; $k++) {
            $objValidation = $objPHPExcel->getActiveSheet(0)->getCell('T'.$k)->getDataValidation();
            $this->common_excel($objValidation);
            $objValidation->setFormula1('\'Sheet2\'!$B$1:$B$2');
        }

        $objPHPExcel->setActiveSheetIndex(2);
        $primarpecan = $objPHPExcel->getActiveSheet(2)->setCellValue('A1','Primarc Pecan');

        /*
        */
        /* $objPHPExcel->setActiveSheetIndex(2);
        $r_row= 1;
        for ($j=0; $j <count($acc_id) ; $j++) {
            $acc_id1 = $acc_id[$j];
            $data = $payment_receipt->getLedger($id, $acc_id1,$to_date);
            $data1 = $payment_receipt->getAccountDetails($acc_id1, "");
            if(count($data)>0){
                for($p=0; $p<count($data); $p++){
                    $ids = $data[$p]['id'].' &&'.$data[$p]['ledger_type'].' &&'.$data[$p]['vendor_id'].' &&';
                    $objPHPExcel->getActiveSheet(2)->setCellValue('A'.$r_row,$ids);
                    $objPHPExcel->getActiveSheet(2)->getStyle('A'.$r_row)->getProtection()->setLocked(\PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
                    $r_row = $r_row+1;
                }
            }
        }*/

        $objPHPExcel->getSheetByName('Sheet3')->setSheetState(\PHPExcel_Worksheet::SHEETSTATE_VERYHIDDEN);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="download_payment.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header ('Cache-Control: cache, must-revalidate');
        header ('Pragma: public');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');   
    }
    
    public function actionUploadpayment() {
        $payment_receipt = new PaymentReceipt();
        $result = $payment_receipt->uploadPayment();
         if($result==false)
        {
            echo '<script>alert("Not a Valid File");window.open("'.Url::base().'?r=paymentreceipt%2Fcreatedpayment","_self")</script>';
        }
        else{
            $this->redirect(array('paymentreceipt/createdpayment'));
        }
    }

    public function upload_sales() {
        $mycomponent = Yii::$app->mycomponent;
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $curusr = $session['session_id'];
        $now = date('Y-m-d H:i:s');

        $sql = "select * from acc_sales_files where is_active = '1' and (upload_status = 'pending' or upload_status is null)";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        $data = $reader->readAll();

        for($i=0; $i<count($data); $i++) {
            $ref_file_id = $data[$i]['id'];
            $fileName = $data[$i]['original_file'];
            $objPHPExcel = \moonland\phpexcel\Excel::import($fileName);
            $reject_file = false;
            $highlight_file = false;

            $r_row = 1;
            $reject_spreadsheet = new Spreadsheet();
            $reject_sheet = $reject_spreadsheet->getActiveSheet();
            $reject_sheet->setCellValue('A'.$r_row, 'Market place');
            $reject_sheet->setCellValue('B'.$r_row, 'Ship from GSTin');
            $reject_sheet->setCellValue('C'.$r_row, 'Ship from State');
            $reject_sheet->setCellValue('D'.$r_row, 'Ship to GSTin');
            $reject_sheet->setCellValue('E'.$r_row, 'Amazon state');
            $reject_sheet->setCellValue('F'.$r_row, 'Pin code');
            $reject_sheet->setCellValue('G'.$r_row, 'Invoice no');
            $reject_sheet->setCellValue('H'.$r_row, 'Invoice date');
            $reject_sheet->setCellValue('I'.$r_row, 'Customer name');
            $reject_sheet->setCellValue('J'.$r_row, 'SKU');
            $reject_sheet->setCellValue('K'.$r_row, 'Item description');
            $reject_sheet->setCellValue('L'.$r_row, 'HSN Code');
            $reject_sheet->setCellValue('M'.$r_row, 'Quantity');
            $reject_sheet->setCellValue('N'.$r_row, 'Rate');
            $reject_sheet->setCellValue('O'.$r_row, 'Sales incl GST');
            $reject_sheet->setCellValue('P'.$r_row, 'Sales excl GST');
            $reject_sheet->setCellValue('Q'.$r_row, 'Total GST');
            $reject_sheet->setCellValue('R'.$r_row, 'IGST Rate');
            $reject_sheet->setCellValue('S'.$r_row, 'IGST Amount');
            $reject_sheet->setCellValue('T'.$r_row, 'CGST Rate');
            $reject_sheet->setCellValue('U'.$r_row, 'CGST Amount');
            $reject_sheet->setCellValue('V'.$r_row, 'SGST Rate');
            $reject_sheet->setCellValue('W'.$r_row, 'SGST Amount');
            $reject_sheet->setCellValue('X'.$r_row, 'Flag');
            $reject_sheet->setCellValue('Y'.$r_row, 'Remarks');

            $h_row = 1;
            $highlight_spreadsheet = new Spreadsheet();
            $highlight_sheet = $highlight_spreadsheet->getActiveSheet();
            $highlight_sheet->setCellValue('A'.$h_row, 'Market place');
            $highlight_sheet->setCellValue('B'.$h_row, 'Ship from GSTin');
            $highlight_sheet->setCellValue('C'.$h_row, 'Ship from State');
            $highlight_sheet->setCellValue('D'.$h_row, 'Ship to GSTin');
            $highlight_sheet->setCellValue('E'.$h_row, 'Amazon state');
            $highlight_sheet->setCellValue('F'.$h_row, 'Pin code');
            $highlight_sheet->setCellValue('G'.$h_row, 'Invoice no');
            $highlight_sheet->setCellValue('H'.$h_row, 'Invoice date');
            $highlight_sheet->setCellValue('I'.$h_row, 'Customer name');
            $highlight_sheet->setCellValue('J'.$h_row, 'SKU');
            $highlight_sheet->setCellValue('K'.$h_row, 'Item description');
            $highlight_sheet->setCellValue('L'.$h_row, 'HSN Code');
            $highlight_sheet->setCellValue('M'.$h_row, 'Quantity');
            $highlight_sheet->setCellValue('N'.$h_row, 'Rate');
            $highlight_sheet->setCellValue('O'.$h_row, 'Sales incl GST');
            $highlight_sheet->setCellValue('P'.$h_row, 'Sales excl GST');
            $highlight_sheet->setCellValue('Q'.$h_row, 'Total GST');
            $highlight_sheet->setCellValue('R'.$h_row, 'IGST Rate');
            $highlight_sheet->setCellValue('S'.$h_row, 'IGST Amount');
            $highlight_sheet->setCellValue('T'.$h_row, 'CGST Rate');
            $highlight_sheet->setCellValue('U'.$h_row, 'CGST Amount');
            $highlight_sheet->setCellValue('V'.$h_row, 'SGST Rate');
            $highlight_sheet->setCellValue('W'.$h_row, 'SGST Amount');
            $highlight_sheet->setCellValue('X'.$h_row, 'Flag');
            $highlight_sheet->setCellValue('Y'.$h_row, 'Remarks');

            for($j=0; $j<count($objPHPExcel); $j++) {
                $bl_reject = false;
                $bl_highlight = false;
                $bl_pincode = false;
                $ship_to_state[$j] = '';
                $remarks[$j] = '';
                $highlight_remarks[$j] = '';

                $marketplace_id[$j] = '';
                $market_place[$j] = $objPHPExcel[$j]['Market place'];
                $ship_from_gstin[$j] = $objPHPExcel[$j]['Ship from GSTin'];
                $ship_from_state[$j] = $objPHPExcel[$j]['Ship from State'];
                $ship_to_gstin[$j] = $objPHPExcel[$j]['Ship to GSTin'];
                $amazon_state[$j] = $objPHPExcel[$j]['Amazon state'];
                $pin_code[$j] = $objPHPExcel[$j]['Pin code'];
                $invoice_no[$j] = $objPHPExcel[$j]['Invoice no'];
                $invoice_date[$j] = $objPHPExcel[$j]['Invoice date'];
                $customer_name[$j] = $objPHPExcel[$j]['Customer name'];
                $sku[$j] = $objPHPExcel[$j]['SKU'];
                $item_desc[$j] = $objPHPExcel[$j]['Item description'];
                $hsn_code[$j] = $objPHPExcel[$j]['HSN Code'];
                $quantity[$j] = $objPHPExcel[$j]['Quantity'];
                $rate[$j] = $objPHPExcel[$j]['Rate'];
                $sales_incl_gst[$j] = $objPHPExcel[$j]['Sales incl GST'];
                $sales_excl_gst[$j] = $objPHPExcel[$j]['Sales excl GST'];
                $total_gst[$j] = $objPHPExcel[$j]['Total GST'];
                $igst_rate[$j] = $objPHPExcel[$j]['IGST Rate'];
                $igst_amount[$j] = $objPHPExcel[$j]['IGST Amount'];
                $cgst_rate[$j] = $objPHPExcel[$j]['CGST Rate'];
                $cgst_amount[$j] = $objPHPExcel[$j]['CGST Amount'];
                $sgst_rate[$j] = $objPHPExcel[$j]['SGST Rate'];
                $sgst_amount[$j] = $objPHPExcel[$j]['SGST Amount'];
                $flag[$j] = $objPHPExcel[$j]['Flag'];

                $r_row += 1;
                $reject_sheet->setCellValue('A'.$r_row, $objPHPExcel[$j]['Market place']);
                $reject_sheet->setCellValue('B'.$r_row, $objPHPExcel[$j]['Ship from GSTin']);
                $reject_sheet->setCellValue('C'.$r_row, $objPHPExcel[$j]['Ship from State']);
                $reject_sheet->setCellValue('D'.$r_row, $objPHPExcel[$j]['Ship to GSTin']);
                $reject_sheet->setCellValue('E'.$r_row, $objPHPExcel[$j]['Amazon state']);
                $reject_sheet->setCellValue('F'.$r_row, $objPHPExcel[$j]['Pin code']);
                $reject_sheet->setCellValue('G'.$r_row, $objPHPExcel[$j]['Invoice no']);
                $reject_sheet->setCellValue('H'.$r_row, $objPHPExcel[$j]['Invoice date']);
                $reject_sheet->setCellValue('I'.$r_row, $objPHPExcel[$j]['Customer name']);
                $reject_sheet->setCellValue('J'.$r_row, $objPHPExcel[$j]['SKU']);
                $reject_sheet->setCellValue('K'.$r_row, $objPHPExcel[$j]['Item description']);
                $reject_sheet->setCellValue('L'.$r_row, $objPHPExcel[$j]['HSN Code']);
                $reject_sheet->setCellValue('M'.$r_row, $objPHPExcel[$j]['Quantity']);
                $reject_sheet->setCellValue('N'.$r_row, $objPHPExcel[$j]['Rate']);
                $reject_sheet->setCellValue('O'.$r_row, $objPHPExcel[$j]['Sales incl GST']);
                $reject_sheet->setCellValue('P'.$r_row, $objPHPExcel[$j]['Sales excl GST']);
                $reject_sheet->setCellValue('Q'.$r_row, $objPHPExcel[$j]['Total GST']);
                $reject_sheet->setCellValue('R'.$r_row, $objPHPExcel[$j]['IGST Rate']);
                $reject_sheet->setCellValue('S'.$r_row, $objPHPExcel[$j]['IGST Amount']);
                $reject_sheet->setCellValue('T'.$r_row, $objPHPExcel[$j]['CGST Rate']);
                $reject_sheet->setCellValue('U'.$r_row, $objPHPExcel[$j]['CGST Amount']);
                $reject_sheet->setCellValue('V'.$r_row, $objPHPExcel[$j]['SGST Rate']);
                $reject_sheet->setCellValue('W'.$r_row, $objPHPExcel[$j]['SGST Amount']);
                $reject_sheet->setCellValue('X'.$r_row, $objPHPExcel[$j]['Flag']);

                $h_row += 1;
                $highlight_sheet->setCellValue('A'.$h_row, $objPHPExcel[$j]['Market place']);
                $highlight_sheet->setCellValue('B'.$h_row, $objPHPExcel[$j]['Ship from GSTin']);
                $highlight_sheet->setCellValue('C'.$h_row, $objPHPExcel[$j]['Ship from State']);
                $highlight_sheet->setCellValue('D'.$h_row, $objPHPExcel[$j]['Ship to GSTin']);
                $highlight_sheet->setCellValue('E'.$h_row, $objPHPExcel[$j]['Amazon state']);
                $highlight_sheet->setCellValue('F'.$h_row, $objPHPExcel[$j]['Pin code']);
                $highlight_sheet->setCellValue('G'.$h_row, $objPHPExcel[$j]['Invoice no']);
                $highlight_sheet->setCellValue('H'.$h_row, $objPHPExcel[$j]['Invoice date']);
                $highlight_sheet->setCellValue('I'.$h_row, $objPHPExcel[$j]['Customer name']);
                $highlight_sheet->setCellValue('J'.$h_row, $objPHPExcel[$j]['SKU']);
                $highlight_sheet->setCellValue('K'.$h_row, $objPHPExcel[$j]['Item description']);
                $highlight_sheet->setCellValue('L'.$h_row, $objPHPExcel[$j]['HSN Code']);
                $highlight_sheet->setCellValue('M'.$h_row, $objPHPExcel[$j]['Quantity']);
                $highlight_sheet->setCellValue('N'.$h_row, $objPHPExcel[$j]['Rate']);
                $highlight_sheet->setCellValue('O'.$h_row, $objPHPExcel[$j]['Sales incl GST']);
                $highlight_sheet->setCellValue('P'.$h_row, $objPHPExcel[$j]['Sales excl GST']);
                $highlight_sheet->setCellValue('Q'.$h_row, $objPHPExcel[$j]['Total GST']);
                $highlight_sheet->setCellValue('R'.$h_row, $objPHPExcel[$j]['IGST Rate']);
                $highlight_sheet->setCellValue('S'.$h_row, $objPHPExcel[$j]['IGST Amount']);
                $highlight_sheet->setCellValue('T'.$h_row, $objPHPExcel[$j]['CGST Rate']);
                $highlight_sheet->setCellValue('U'.$h_row, $objPHPExcel[$j]['CGST Amount']);
                $highlight_sheet->setCellValue('V'.$h_row, $objPHPExcel[$j]['SGST Rate']);
                $highlight_sheet->setCellValue('W'.$h_row, $objPHPExcel[$j]['SGST Amount']);
                $highlight_sheet->setCellValue('X'.$h_row, $objPHPExcel[$j]['Flag']);

                $sql = "select * from internal_warehouse_master where is_active = '1' and company_id = '$company_id' 
                        and gst_id = '".$ship_from_gstin[$j]."'";
                $command = Yii::$app->db->createCommand($sql);
                $reader = $command->query();
                $data2 = $reader->readAll();
                if(count($data2)==0){
                    $bl_reject = true;
                    $remarks[$j] = $remarks[$j] . "Ship from GSTin not found in warehouse master. ";
                }

                $sql = "select * from acc_master where legal_name = '".$market_place[$j]."' and type = 'Marketplace'";
                $command = Yii::$app->db->createCommand($sql);
                $reader = $command->query();
                $data2 = $reader->readAll();
                if(count($data2)==0){
                    $bl_reject = true;
                    $remarks[$j] = $remarks[$j] . "Marketplace not found. ";
                } else {
                    $marketplace_id[$j] = $data2[0]['id'];
                }

                $sql = "select * from pincode_master where pincode = '".$pin_code[$j]."'";
                $command = Yii::$app->db->createCommand($sql);
                $reader = $command->query();
                $data2 = $reader->readAll();
                if(count($data2)>0){
                    $ship_to_state[$j] = $data2[0]['state_name'];
                }

                if($ship_to_state[$j]==''){
                    $sql = "select * from acc_amazon_state_master where is_active = '1' and company_id = '$company_id' 
                            and erp_state = '".$ship_to_state[$j]."'";
                    $command = Yii::$app->db->createCommand($sql);
                    $reader = $command->query();
                    $data2 = $reader->readAll();
                    if(count($data2)>0){
                        $ship_to_state[$j] = $data2[0]['amazon_state'];
                        if($ship_to_state[$j]!='' && $pin_code[$j]!=''){
                            $this->insert_pincode($ship_to_state[$j], $pin_code[$j]);
                        }
                    }
                }

                if($ship_to_state[$j]==''){
                    $bl_reject = true;
                    $remarks[$j] = $remarks[$j] . "Pincode not found. ";
                }

                if($ship_to_gstin[$j]==''){
                    $sales_type[$j] = 'B2C';
                } else {
                    $sales_type[$j] = 'B2B';

                    $highlight_remarks[$j] = $this->check_gst_no_format($ship_to_gstin[$j], $ship_to_state[$j]);
                    if($highlight_remarks[$j]!=''){
                        $bl_highlight = true;
                    }
                }

                $new_hsn_code[$j] = '';
                if($sku[$j]!=''){
                    $sql = "select * from product_master where is_active = '1' and company_id = '$company_id' 
                            and sku_internal_code = '".$sku[$j]."'";
                    $command = Yii::$app->db->createCommand($sql);
                    $reader = $command->query();
                    $data2 = $reader->readAll();
                    if(count($data2)>0){
                        $new_hsn_code[$j] = $data2[0]['hsn_code'];
                    }
                }

                if($hsn_code[$j] == '' || $hsn_code[$j]==null){
                    $bl_highlight = true;
                    $highlight_remarks[$j] = $highlight_remarks[$j] . "HSN Code is empty. ";
                } else if($new_hsn_code[$j] == '' || $new_hsn_code[$j]==null){
                    $bl_highlight = true;
                    $highlight_remarks[$j] = $highlight_remarks[$j] . "HSN Code not found in Product Master. ";
                } else if($new_hsn_code[$j] != $hsn_code[$j]){
                    $bl_highlight = true;
                    $highlight_remarks[$j] = $highlight_remarks[$j] . "HSN Code is different. ";
                }

                if($rate[$j]!=''){
                    if($this->check_no($rate[$j])==false){
                        $bl_reject = true;
                        $remarks[$j] = $remarks[$j] . "Rate is not number. ";
                    }
                }
                if($quantity[$j]!=''){
                    if($this->check_no($quantity[$j])==false){
                        $bl_reject = true;
                        $remarks[$j] = $remarks[$j] . "Quantity is not number. ";
                    }
                }
                if($sales_incl_gst[$j]!=''){
                    if($this->check_no($sales_incl_gst[$j])==false){
                        $bl_reject = true;
                        $remarks[$j] = $remarks[$j] . "Sales Incl GST is not number. ";
                    }
                }
                if($sales_excl_gst[$j]!=''){
                    if($this->check_no($sales_excl_gst[$j])==false){
                        $bl_reject = true;
                        $remarks[$j] = $remarks[$j] . "Sales Excl GST is not number. ";
                    }
                }
                if($total_gst[$j]!=''){
                    if($this->check_no($total_gst[$j])==false){
                        $bl_reject = true;
                        $remarks[$j] = $remarks[$j] . "Total GST is not number. ";
                    }
                }
                if($igst_rate[$j]!=''){
                    if($this->check_no($igst_rate[$j])==false){
                        $bl_reject = true;
                        $remarks[$j] = $remarks[$j] . "IGST Rate is not number. ";
                    }
                }
                if($igst_amount[$j]!=''){
                    if($this->check_no($igst_amount[$j])==false){
                        $bl_reject = true;
                        $remarks[$j] = $remarks[$j] . "IGST Amount is not number. ";
                    }
                }
                if($cgst_rate[$j]!=''){
                    if($this->check_no($cgst_rate[$j])==false){
                        $bl_reject = true;
                        $remarks[$j] = $remarks[$j] . "CGST Rate is not number. ";
                    }
                }
                if($cgst_amount[$j]!=''){
                    if($this->check_no($cgst_amount[$j])==false){
                        $bl_reject = true;
                        $remarks[$j] = $remarks[$j] . "CGST Amount is not number. ";
                    }
                }
                if($sgst_rate[$j]!=''){
                    if($this->check_no($sgst_rate[$j])==false){
                        $bl_reject = true;
                        $remarks[$j] = $remarks[$j] . "SGST Rate is not number. ";
                    }
                }
                if($sgst_amount[$j]!=''){
                    if($this->check_no($sgst_amount[$j])==false){
                        $bl_reject = true;
                        $remarks[$j] = $remarks[$j] . "SGST Amount is not number. ";
                    }
                }

                if($flag[$j]!='0' && $flag[$j]!='1'){
                    $bl_reject = true;
                    $remarks[$j] = $remarks[$j] . "Flag should be 0 or 1. ";
                }

                if($bl_reject==true) {
                    // echo 'rejected';
                    // echo '<br/>';
                    // echo $remarks[$j];
                    // echo '<br/>';
                    $reject_sheet->setCellValue('Y'.$r_row, $remarks[$j]);
                    $reject_file = true;
                }
                if($bl_highlight==true) {
                    // echo $highlight_remarks[$j].'<br/>';
                    $highlight_sheet->setCellValue('Y'.$h_row, $highlight_remarks[$j]);
                    $highlight_file = true;
                }
            }

            if($reject_file==false) {
                for($j=0; $j<count($market_place); $j++) {
                    if($invoice_date[$j]==''){
                        $invoice_date[$j]=NULL;
                    } else {
                        $invoice_date[$j]=$mycomponent->formatdate($invoice_date[$j]);
                    }

                    $array = array('ref_file_id' => $ref_file_id, 
                                    'market_place' => $market_place[$j], 
                                    'marketplace_id' => $marketplace_id[$j], 
                                    'ship_from_gstin' => $ship_from_gstin[$j], 
                                    'ship_from_state' => $ship_from_state[$j], 
                                    'ship_to_gstin' => $ship_to_gstin[$j], 
                                    'ship_to_state' => $ship_to_state[$j], 
                                    'amazon_state' => $amazon_state[$j], 
                                    'pin_code' => $pin_code[$j], 
                                    'invoice_no' => $invoice_no[$j], 
                                    'invoice_date' => $invoice_date[$j], 
                                    'customer_name' => $customer_name[$j], 
                                    'sku' => $sku[$j], 
                                    'item_desc' => $item_desc[$j], 
                                    'hsn_code' => $hsn_code[$j], 
                                    'quantity' => $mycomponent->format_number($quantity[$j],2), 
                                    'rate' => $mycomponent->format_number($rate[$j],2),
                                    'sales_incl_gst' => $mycomponent->format_number($sales_incl_gst[$j],2),
                                    'sales_excl_gst' => $mycomponent->format_number($sales_excl_gst[$j],2),
                                    'total_gst' => $mycomponent->format_number($total_gst[$j],2),
                                    'igst_rate' => $mycomponent->format_number($igst_rate[$j],2),
                                    'igst_amount' => $mycomponent->format_number($igst_amount[$j],2),
                                    'cgst_rate' => $mycomponent->format_number($cgst_rate[$j],2),
                                    'cgst_amount' => $mycomponent->format_number($cgst_amount[$j],2),
                                    'sgst_rate' => $mycomponent->format_number($sgst_rate[$j],2),
                                    'sgst_amount' => $mycomponent->format_number($sgst_amount[$j],2),
                                    'flag' => $flag[$j], 
                                    'status' => 'pending',
                                    'is_active' => '1',
                                    'updated_by'=>$curusr,
                                    'updated_date'=>$now,
                                    'approver_comments'=>$remarks[$j],
                                    'company_id'=>$company_id
                                );

                    $array['created_by'] = $curusr;
                    $array['created_date'] = $now;
                    $count = Yii::$app->db->createCommand()
                                ->insert("acc_sales_file_items", $array)
                                ->execute();

                    // echo json_encode($array);
                    // echo '<br/><br/>';
                }

                $sql = "update acc_sales_files set upload_status = 'uploaded' where id = '$ref_file_id'";
                $command = Yii::$app->db->createCommand($sql);
                $count = $command->execute();
            } else {
                $filename='sales_rejected_file_'.$ref_file_id.'.xlsx';
                $upload_path = './uploads';
                if(!is_dir($upload_path)) {
                    mkdir($upload_path, 0777, TRUE);
                }
                $upload_path = './uploads/sales';
                if(!is_dir($upload_path)) {
                    mkdir($upload_path, 0777, TRUE);
                }
                $upload_path = './uploads/sales/'.$ref_file_id;
                if(!is_dir($upload_path)) {
                    mkdir($upload_path, 0777, TRUE);
                }

                $file_name = $upload_path . '/' . $filename;
                $file_path = 'uploads/sales/' . $ref_file_id . '/' . $filename;

                $writer = new Xlsx($reject_spreadsheet);
                $writer->save($file_name);

                $sql = "update acc_sales_files set error_rejected_file = '$file_path', upload_status = 'rejected', 
                        updated_by = '$curusr', updated_date = '$now' 
                        where id = '$ref_file_id'";
                $command = Yii::$app->db->createCommand($sql);
                $count = $command->execute();
            }

            if($highlight_file==true){
                $filename='sales_highlighted_file_'.$ref_file_id.'.xlsx';
                $upload_path = './uploads';
                if(!is_dir($upload_path)) {
                    mkdir($upload_path, 0777, TRUE);
                }
                $upload_path = './uploads/sales';
                if(!is_dir($upload_path)) {
                    mkdir($upload_path, 0777, TRUE);
                }
                $upload_path = './uploads/sales/'.$ref_file_id;
                if(!is_dir($upload_path)) {
                    mkdir($upload_path, 0777, TRUE);
                }

                $file_name = $upload_path . '/' . $filename;
                $file_path = 'uploads/sales/' . $ref_file_id . '/' . $filename;

                $writer = new Xlsx($highlight_spreadsheet);
                $writer->save($file_name);

                $sql = "update acc_sales_files set error_highlighted_file = '$file_path' 
                        where id = '$ref_file_id'";
                $command = Yii::$app->db->createCommand($sql);
                $count = $command->execute();
            }
        }
    }

    public function getBanks($id="" ,$legal_name="") {
        $cond = "";
        if($id!=""){
            $cond = " and id = '$id'";
        }

        if($legal_name!="")
        {
            if($cond!='')
                $cond.= " and legal_name='$legal_name'";
            else
                $cond= " and legal_name='$legal_name'";
        }

        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select * from acc_master where is_active = '1' and status = 'approved' and 
                company_id = '$company_id' and type = 'Bank Account'".$cond." order by bank_name";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function common_excel($objValidation) {
        $objValidation->setType(\PHPExcel_Cell_DataValidation::TYPE_LIST );
        $objValidation->setErrorStyle(\PHPExcel_Cell_DataValidation::STYLE_INFORMATION );
        $objValidation->setAllowBlank(false);
        $objValidation->setShowInputMessage(true);
        $objValidation->setShowErrorMessage(true);
        $objValidation->setShowDropDown(true);
        $objValidation->getShowDropDown(true);
        $objValidation->setErrorTitle('Input error');
        $objValidation->setError('Value is not in list.');
        $objValidation->setPromptTitle('Pick from list');
        $objValidation->setPrompt('Please pick a value from the drop-down list.');/*
        $objValidation->setFormula1('"'.$distname.'"');*/
    }
}