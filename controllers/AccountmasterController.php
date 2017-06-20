<?php

namespace app\controllers;

use Yii;
use app\models\AccountMaster;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;

class AccountmasterController extends Controller
{
    public function actionIndex() {
        $acc_master = new AccountMaster();
        $access = $acc_master->getAccess();
        if(count($access)>0) {
            if($access[0]['r_view']==1) {
                $pending = $acc_master->getAccountDetails("", "pending");
                $approved = $acc_master->getAccountDetails("", "approved");
                $rejected = $acc_master->getAccountDetails("", "rejected");

                $acc_master->setLog('AccountMaster', '', 'View', '', 'View Account Master List', 'acc_master', '');
                return $this->render('account_list', ['access' => $access, 'pending' => $pending, 'approved' => $approved, 
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

    public function actionCreate() {
        $acc_master = new AccountMaster();
        $access = $acc_master->getAccess();
        if(count($access)>0) {
            if($access[0]['r_insert']==1) {
                $action = 'insert';
                $category = $acc_master->getAccountCategories();
                $vendor = $acc_master->getVendors();
                $category_list = $acc_master->getBusinessCategories();
                $approver_list = $acc_master->getApprover($action);

                $acc_master->setLog('AccountMaster', '', 'Insert', '', 'Insert Account Master Details', 'acc_master', '');
                return $this->render('account_details', ['action' => $action, 'category' => $category, 'vendor' => $vendor, 
                                                         'category_list' => $category_list, 'approver_list' => $approver_list]);
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
        $acc_master = new AccountMaster();
        $data = $acc_master->getAccountDetails($id, "");
        $category = $acc_master->getAccountCategories();
        $vendor = $acc_master->getVendors();
        $acc_category = $acc_master->getAccCategories($id);
        $category_list = $acc_master->getBusinessCategories();
        $approver_list = $acc_master->getApprover($action);

        if(count($data)>0){
            if($data[0]['type']=="Vendor Goods"){
                $vendor_id = $data[0]['vendor_id'];
                $vendor = $acc_master->getVendors($vendor_id);
            }
        }
        return $this->render('account_details', ['action' => $action, 'category' => $category, 'vendor' => $vendor, 
                                                    'category_list' => $category_list, 'data' => $data, 
                                                    'acc_category' => $acc_category, 'approver_list' => $approver_list]);
    }

    public function actionView($id) {
        $acc_master = new AccountMaster();
        $access = $acc_master->getAccess();
        if(count($access)>0) {
            if($access[0]['r_view']==1) {
                $acc_master->setLog('AccountMaster', '', 'View', '', 'View Account Master Details', 'acc_master', $id);
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
        $acc_master = new AccountMaster();
        $access = $acc_master->getAccess();
        $data = $acc_master->getAccountDetails($id, "");
        if(count($access)>0) {
            if($access[0]['r_edit']==1 && $access[0]['session_id']!=$data[0]['approver_id']) {
                $acc_master->setLog('AccountMaster', '', 'Edit', '', 'Edit Account Master Details', 'acc_master', $id);
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
        $acc_master = new AccountMaster();
        $access = $acc_master->getAccess();
        $data = $acc_master->getAccountDetails($id, "");
        if(count($access)>0) {
            if($access[0]['r_approval']==1 && $access[0]['session_id']==$data[0]['approver_id']) {
                $acc_master->setLog('AccountMaster', '', 'Authorise', '', 'Authorise Account Master Details', 'acc_master', $id);
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
        $acc_master = new AccountMaster();
        $result = $acc_master->save();
        $this->redirect(array('accountmaster/index'));
    }

    public function actionSavecategories() {
        $acc_master = new AccountMaster();
        $result = $acc_master->saveCategories();
        $category = $acc_master->getAccountCategories();
        echo json_encode($category);
    }

    public function actionGetcode() {
        $acc_master = new AccountMaster();
        echo $acc_master->getCode();
    }

    public function actionGetcategories() {
        $acc_master = new AccountMaster();
        $category = $acc_master->getAccountCategories();
        echo json_encode($category);
    }

    public function actionGetvendors() {
        $acc_master = new AccountMaster();
        $vendor = $acc_master->getVendors();
        echo json_encode($vendor);
    }

    public function actionGetvendordetails() {
        $acc_master = new AccountMaster();
        $data = $acc_master->getVendorDetails();
        echo json_encode($data);
    }

    public function actionChecklegalnameavailablity() {
        $acc_master = new AccountMaster();
        $result = $acc_master->checkLegalNameAvailablity();
        echo $result;
    }
}
