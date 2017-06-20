<?php

namespace app\controllers;

use Yii;
use app\models\JournalVoucher;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;

class JournalvoucherController extends Controller
{
    public function actionIndex(){
        $journal_voucher = new JournalVoucher();
        $access = $journal_voucher->getAccess();
        if(count($access)>0) {
            if($access[0]['r_view']==1) {
                $pending = $journal_voucher->getJournalVoucherDetails("", "pending");
                $approved = $journal_voucher->getJournalVoucherDetails("", "approved");
                $rejected = $journal_voucher->getJournalVoucherDetails("", "rejected");

                $journal_voucher->setLog('JournalVoucher', '', 'View', '', 'View Journal Voucher List', 'acc_jv_details', '');
                return $this->render('journalvoucher_list', ['access' => $access, 'pending' => $pending, 'approved' => $approved, 
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
        $journal_voucher = new JournalVoucher();
        $access = $journal_voucher->getAccess();
        if(count($access)>0) {
            if($access[0]['r_insert']==1) {
                $action = 'insert';
                $acc_details = $journal_voucher->getAccountDetails();
                $approver_list = $journal_voucher->getApprover($action);

                $journal_voucher->setLog('JournalVoucher', '', 'Insert', '', 'Insert Journal Voucher Details', 'acc_jv_details', '');
                return $this->render('journalvoucher_details', ['action' => $action, 'acc_details' => $acc_details, 
                                                                'approver_list' => $approver_list]);
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
        $journal_voucher = new JournalVoucher();
        $data = $journal_voucher->getJournalVoucherDetails($id, "");
        $acc_details = $journal_voucher->getAccountDetails();
        $jv_entries = $journal_voucher->gerJournalVoucherEntries($id);
        $jv_docs = $journal_voucher->gerJournalVoucherDocs($id);
        $approver_list = $journal_voucher->getApprover($action);

        return $this->render('journalvoucher_details', ['action' => $action, 'data' => $data, 'acc_details' => $acc_details, 
                                                        'jv_entries' => $jv_entries, 'jv_docs' => $jv_docs, 
                                                        'approver_list' => $approver_list]);
    }

    public function actionView($id) {
        $journal_voucher = new JournalVoucher();
        $access = $journal_voucher->getAccess();
        if(count($access)>0) {
            if($access[0]['r_view']==1) {
                $journal_voucher->setLog('JournalVoucher', '', 'View', '', 'View Journal Voucher Details', 'acc_jv_details', $id);
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
        $journal_voucher = new JournalVoucher();
        $access = $journal_voucher->getAccess();
        $data = $journal_voucher->getJournalVoucherDetails($id, "");
        if(count($access)>0) {
            if($access[0]['r_edit']==1 && $access[0]['session_id']!=$data[0]['approver_id']) {
                $journal_voucher->setLog('JournalVoucher', '', 'Edit', '', 'Edit Journal Voucher Details', 'acc_jv_details', $id);
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
        $journal_voucher = new JournalVoucher();
        $access = $journal_voucher->getAccess();
        $data = $journal_voucher->getJournalVoucherDetails($id, "");
        if(count($access)>0) {
            if($access[0]['r_approval']==1 && $access[0]['session_id']==$data[0]['approver_id']) {
                $journal_voucher->setLog('JournalVoucher', '', 'Authorise', '', 'Authorise Journal Voucher Details', 'acc_jv_details', $id);
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
        $journal_voucher = new JournalVoucher();
        $result = $journal_voucher->save();
        $this->redirect(array('journalvoucher/index'));
    }

    public function actionGetaccdetails(){
        $journal_voucher = new JournalVoucher();
        $request = Yii::$app->request;
        $acc_id = $request->post('acc_id');
        $data = $journal_voucher->getAccountDetails($acc_id);
        echo json_encode($data);
    }

    public function actionGetcode(){
        $journal_voucher = new JournalVoucher();
        echo $journal_voucher->getCode();
    }

    public function actionSavecategories(){
        $journal_voucher = new JournalVoucher();
        $result = $journal_voucher->saveCategories();
        $category = $journal_voucher->getAccountCategories();
        echo json_encode($category);
    }

    public function actionGetcategories(){
        $journal_voucher = new JournalVoucher();
        $category = $journal_voucher->getAccountCategories();
        echo json_encode($category);
    }

    public function actionGetvendors(){
        $journal_voucher = new JournalVoucher();
        $vendor = $journal_voucher->getVendors();

        echo json_encode($vendor);
    }

    public function actionGetvendordetails(){
        $journal_voucher = new JournalVoucher();
        $data = $journal_voucher->getVendorDetails();
        echo json_encode($data);
    }
}
