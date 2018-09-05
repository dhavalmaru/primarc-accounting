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
                // $category = $acc_master->getAccountCategories();
                $vendor = $acc_master->getVendors();
                $Customers = $acc_master->getCustomers();
				$tax = $acc_master->getTax();
                $tax_per = $acc_master->getTaxPercent();
               	$state = $acc_master->getState();
                $category_list = $acc_master->getBusinessCategories();
                $approver_list = $acc_master->getApprover($action);

                $acc_master->setLog('AccountMaster', '', 'Insert', '', 'Insert Account Master Details', 'acc_master', '');
                // return $this->render('account_details', ['action' => $action, 'category' => $category, 'vendor' => $vendor, 
                //                                          'category_list' => $category_list, 'approver_list' => $approver_list]);
                return $this->render('account_details', ['action' => $action, 'vendor' => $vendor, 'tax' => $tax, 
                                                        'tax_per' => $tax_per, 'Customers' => $Customers,'state' => $state, 
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
        // $category = $acc_master->getAccountCategories();
        $vendor = $acc_master->getVendors();
		$state = $acc_master->getState();
		$tax = $acc_master->getTax();
        $tax_per = $acc_master->getTaxPercent();
        $Customers = $acc_master->getCustomers();
        $acc_category = $acc_master->getAccCategories($id);
        $category_list = $acc_master->getBusinessCategories();
        $approver_list = $acc_master->getApprover($action);

        // if(count($data)>0){
        //     if($data[0]['type']=="Vendor Goods"){
        //         $vendor_id = $data[0]['vendor_id'];
        //         $vendor = $acc_master->getVendors($vendor_id);
        //     } else if($data[0]['type']=="GST Tax") {
        //         $tax_id = $data[0]['tax_id'];
        //         $tax = $acc_master->getTax($tax_id);
        //     } else if($data[0]['type']=="Customer") {
        //         $customer_id = $data[0]['customer_id'];
        //         $Customers = $acc_master->getCustomers($customer_id);
        //     } else if($data[0]['type']=="Goods Purchase" || $data[0]['type']=="Goods Sales"){
        //         $state_id = $data[0]['state_id'];
        //         $state = $acc_master->getState($state);
        //     }
        // }

        // return $this->render('account_details', ['action' => $action, 'category' => $category, 'vendor' => $vendor, 
        //                                             'category_list' => $category_list, 'data' => $data, 
        //                                             'acc_category' => $acc_category, 'approver_list' => $approver_list]);

        return $this->render('account_details', ['action' => $action, 'vendor' => $vendor, 'Customers' => $Customers,'state' => $state,  
                                                    'category_list' => $category_list, 'data' => $data, 'tax' => $tax, 'tax_per' => $tax_per, 
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
	
    public function actionGetcode1() {
        $acc_master = new AccountMaster();
        echo $acc_master->getCode1();
    }

    public function actionTest() {
        $acc_master = new AccountMaster();
        $account_types = $acc_master->getSubAccountPath();
        echo $account_types;
    }

    public function actionGetsubaccountpath() {
        $acc_master = new AccountMaster();
        $path = $acc_master->getSubAccountPath();
        echo $path;
    }

    public function actionGetsubaccounttypes() {
        $acc_master = new AccountMaster();
        $account_types = $acc_master->getSubAccountTypes();
        echo $account_types;
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

	public function actionGetstate() {
        $acc_master = new AccountMaster();
        $state = $acc_master->getState();
        echo json_encode($state);
    }
    
	public function actionGettax() {
        $acc_master = new AccountMaster();
        $tax = $acc_master->getTax();
        echo json_encode($tax);
    }
	
	public function actionGetCustomers() {
        $acc_master = new AccountMaster();
        $Customers = $acc_master->getCustomers();
        echo json_encode($Customers);
    }

    public function actionGetcustomerdetails() {
        $acc_master = new AccountMaster();
        $data = $acc_master->getCustomerDetails();
        echo json_encode($data);
    }

    public function actionChecklegalnameavailablity() {
        $acc_master = new AccountMaster();
        $result = $acc_master->checkLegalNameAvailablity();
        echo $result;
    }

    public function actionChecklegalnameavailablityinaccmaster() {
        $acc_master = new AccountMaster();
        $result = $acc_master->checkLegalNameAvailablityInAccMaster();
        echo $result;
    }
}
