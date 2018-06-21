<?php

namespace app\controllers;

use Yii;
use app\models\OtherDebitCredit;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;

class OtherdebitcreditController extends Controller
{
    public function actionIndex(){
        $model = new OtherDebitCredit();
        $access = $model->getAccess();
        if(count($access)>0) {
            if($access[0]['r_view']==1) {
                $approved = $model->getOtherDebitCreditDetails("", "approved");

                $model->setLog('OtherDebitCredit', '', 'View', '', 'View Other Debit Credit List', 'acc_other_debit_credit_details', '');
                return $this->render('otherdebitcredit_list', ['access' => $access, 'approved' => $approved]);
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
        $model = new OtherDebitCredit();
        $access = $model->getAccess();
        if(count($access)>0) {
            if($access[0]['r_insert']==1) {
                $action = 'insert';
                $acc_details = $model->getAccountDetails();

                $model->setLog('OtherDebitCredit', '', 'Insert', '', 'Insert Other Debit Credit Details', 'acc_other_debit_credit_details', '');
                return $this->render('otherdebitcredit_details', ['action' => $action, 'acc_details' => $acc_details]);
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
        $model = new OtherDebitCredit();
        $data = $model->getOtherDebitCreditDetails($id, "");
        $acc_details = $model->getAccountDetails();
        $other_debit_credit_entries = $model->gerOtherDebitCreditEntries($id);
        // $approver_list = $model->getApprover($action);

        return $this->render('otherdebitcredit_details', ['action' => $action, 'data' => $data, 'acc_details' => $acc_details, 
                                                        'other_debit_credit_entries' => $other_debit_credit_entries]);
    }

    public function actionView($id) {
        $model = new OtherDebitCredit();
        $access = $model->getAccess();
        if(count($access)>0) {
            if($access[0]['r_view']==1) {
                $model->setLog('OtherDebitCredit', '', 'View', '', 'View Other Debit Credit Details', 'acc_other_debit_credit_details', $id);
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
        $model = new OtherDebitCredit();
        $access = $model->getAccess();
        $data = $model->getOtherDebitCreditDetails($id, "");
        if(count($access)>0) {
            if($access[0]['r_edit']==1 && $access[0]['session_id']!=$data[0]['approver_id']) {
                $model->setLog('OtherDebitCredit', '', 'Edit', '', 'Edit Other Debit Credit Details', 'acc_other_debit_credit_details', $id);
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

    public function actionGetaccdetails(){
        $model = new OtherDebitCredit();
        $request = Yii::$app->request;
        $acc_id = $request->post('acc_id');
        $data = $model->getAccountDetails($acc_id);
        echo json_encode($data);
    }

    public function actionSave(){   
        $model = new OtherDebitCredit();
        $result = $model->save();
        $this->redirect(array('otherdebitcredit/index'));
    }

    public function actionViewdebitcreditnote($id){
        $model = new OtherDebitCredit();
        $data = $model->getDebitNoteDetails($id);
        
        $this->layout = false;
        return $this->render('debit_note', ['debit_note' => $data['debit_note'], 'vendor_details' => $data['vendor_details']]);
    }

    public function actionDownloaddebitcreditnote($id){
        $model = new OtherDebitCredit();
        $data = $model->getDebitNoteDetails($id);
        $file = "";

        if(isset($data['debit_note'])){
            if(count($data['debit_note'])>0){
                $debit_note = $data['debit_note'];
                $file = $debit_note[0]['debit_credit_note_path'];
            }
        }

        if( file_exists( $file ) ){
            Yii::$app->response->sendFile($file);
        } else {
            echo $file;
        }
    }

    public function actionEmaildebitcreditnote($id){
        $model = new OtherDebitCredit();
        $data = $model->getDebitNoteDetails($id);
        $file = "";

        return $this->render('email', [
            'invoice_details' => $data['invoice_details'], 'debit_note' => $data['debit_note'], 
            'deduction_details' => $data['deduction_details'], 'vendor_details' => $data['vendor_details'], 
            'grn_details' => $data['grn_details']
        ]);
    }

    public function actionEmail(){
        $request = Yii::$app->request;
        $mycomponent = Yii::$app->mycomponent;
        $session = Yii::$app->session;

        $id = $request->post('id');
        // $grn_id = $request->post('grn_id');
        // $invoice_id = $request->post('invoice_id');
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
        $data['id'] = $id;
        // $data['grn_id'] = $grn_id;
        // $data['invoice_id'] = $invoice_id;

        
        $attachment_type = 'PDF';
        $vendor_name = $request->post('vendor_name');
        $company_id = $request->post('company_id');
        $model = new OtherDebitCredit();
        $model->setEmailLog($vendor_name, $from, $to, $id, $body, $attachment, 
                                $attachment_type, $email_sent_status, $error_message, $company_id);

        return $this->render('email_response', ['data' => $data]);
    }

}
