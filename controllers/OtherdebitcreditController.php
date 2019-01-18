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
                $vendor = $model->getVendors();
                $warehouse_gst = $model->getWarehouseDetails();
<<<<<<< HEAD
                $vendor_gst = $model->getVendorGSTNos();

                $model->setLog('OtherDebitCredit', '', 'Insert', '', 'Insert Other Debit Credit Details', 'acc_other_debit_credit_details', '');
                return $this->render('otherdebitcredit_details', ['action' => $action, 'acc_details' => $acc_details, 
                                                                'vendor' => $vendor, 'warehouse_gst' => $warehouse_gst, 
                                                                'vendor_gst' => $vendor_gst]);
=======

                $model->setLog('OtherDebitCredit', '', 'Insert', '', 'Insert Other Debit Credit Details', 'acc_other_debit_credit_details', '');
                return $this->render('otherdebitcredit_details', ['action' => $action, 'acc_details' => $acc_details, 
                                                                'vendor' => $vendor, 'warehouse_gst' => $warehouse_gst]);
>>>>>>> 40251667d20641f61579b49c4e0131e7351baf6f
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
        $vendor = $model->getVendors();
        $acc_details = $model->getAccountDetails();
        $warehouse_gst = $model->getWarehouseDetails();
<<<<<<< HEAD
        $vendor_id = '';
        if(count($data)>0){
            $vendor_id = $data[0]['vendor_id'];
        }
        $vendor_gst = $model->getVendorGSTNos($vendor_id);
=======
>>>>>>> 40251667d20641f61579b49c4e0131e7351baf6f
        $other_debit_credit_entries = $model->gerOtherDebitCreditEntries($id);
        // $approver_list = $model->getApprover($action);

        return $this->render('otherdebitcredit_details', ['action' => $action, 'data' => $data, 'acc_details' => $acc_details, 
                                                        'vendor' => $vendor, 'warehouse_gst' => $warehouse_gst, 
<<<<<<< HEAD
                                                        'vendor_gst' => $vendor_gst, 
=======
>>>>>>> 40251667d20641f61579b49c4e0131e7351baf6f
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

    public function actionViewtaxinvoice($id){
        $model = new OtherDebitCredit();
        $data = $model->getDebitNoteDetails($id);
        
        $this->layout = false;
        return $this->render('tax_invoice', ['debit_note' => $data['debit_note'], 'vendor_details' => $data['vendor_details'], 
<<<<<<< HEAD
                                            'invoice_details' => $data['invoice_details'], 'inv_tax_details' => $data['inv_tax_details'], 
                                            'warehouse_details' => $data['warehouse_details'], 
                                            'vendor_warehouse_details' => $data['vendor_warehouse_details']]);
=======
                                            'invoice_details' => $data['invoice_details'], 'inv_tax_details' => $data['inv_tax_details']]);
>>>>>>> 40251667d20641f61579b49c4e0131e7351baf6f
    }

    public function actionDownloadtaxinvoice($id){
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

    public function actionEmailtaxinvoice($id){
        $model = new OtherDebitCredit();
        $data = $model->getDebitNoteDetails($id);
        $file = "";

        return $this->render('email', ['debit_note' => $data['debit_note'], 'vendor_details' => $data['vendor_details'], 
<<<<<<< HEAD
                                        'invoice_details' => $data['invoice_details'], 'inv_tax_details' => $data['inv_tax_details'], 
                                        'warehouse_details' => $data['warehouse_details'], 
                                        'vendor_warehouse_details' => $data['vendor_warehouse_details']]);
=======
                                        'invoice_details' => $data['invoice_details'], 'inv_tax_details' => $data['inv_tax_details']]);
>>>>>>> 40251667d20641f61579b49c4e0131e7351baf6f
    }

    public function actionViewdebitcreditnote($id){
        $model = new OtherDebitCredit();
        $data = $model->getDebitNoteDetails($id);
        
        $this->layout = false;
        return $this->render('debit_note', ['debit_note' => $data['debit_note'], 'vendor_details' => $data['vendor_details'], 
                                        'invoice_details' => $data['invoice_details'], 'inv_tax_details' => $data['inv_tax_details'], 
                                        'warehouse_details' => $data['warehouse_details'], 
                                        'vendor_warehouse_details' => $data['vendor_warehouse_details']]);
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
            'grn_details' => $data['grn_details'], 'warehouse_details' => $data['warehouse_details'], 
            'vendor_warehouse_details' => $data['vendor_warehouse_details']
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

    public function actionGetvendorgstid(){
        $model = new OtherDebitCredit();
        $request = Yii::$app->request;
        $vendor_warehouse_id = $request->post('vendor_warehouse_id');
        $data = $model->getVendorGSTId($vendor_warehouse_id);

        $vendor_gst_id = '';
        if(isset($data)) { 
            $vendor_gst_id = $data[0]['warehouse_gst'];
        }
        echo $vendor_gst_id;
    }

    public function actionGetvendorgstnos(){
        $model = new OtherDebitCredit();
        $request = Yii::$app->request;
        $vendor_id = $request->post('vendor_id');
        $data = $model->getVendorGSTNos($vendor_id);

        $vendor_gst = '<option value="">Select</option>';
        if(isset($data)) { 
            for($i=0; $i<count($data); $i++) { 
                $vendor_gst = $vendor_gst . '<option value="'.$data[$i]['id'].'">'.$data[$i]['warehouse_gst'].'</option>';
            }
        }
        echo $vendor_gst;
    }

}
