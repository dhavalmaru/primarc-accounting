<?php

namespace app\controllers;

use Yii;
use app\models\Taxtype;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;

class TaxtypeController extends Controller 
{
    public function actionIndex() {
        $Taxtype = new Taxtype();
        $access = $Taxtype->getAccess();
        if(count($access)>0) {
            if($access[0]['r_view']==1) {
                $pending = $Taxtype->getDetails("", "pending");
                $approved = $Taxtype->getDetails("", "approved");
                $rejected = $Taxtype->getDetails("", "rejected");

                $Taxtype->setLog('Taxtype', '', 'View', '', 'View Tax type List', 'acc_gst_tax_type_master', '');
                return $this->render('tax_type_list', ['access' => $access, 'pending' => $pending, 'approved' => $approved, 
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
        $Taxtype = new Taxtype();
        $access = $Taxtype->getAccess();
        if(count($access)>0) {
            if($access[0]['r_insert']==1) {
                $action = 'insert';
                //$acc_details = $Taxtype->getAccountDetails();
              
                $approver_list = $Taxtype->getApprover($action);

                $Taxtype->setLog('Taxtype', '', 'Insert', '', 'Insert Tax type Details', 'acc_gst_tax_type_master', '');
                return $this->render('tax_type_details', ['action' => $action, 
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
		$Taxtype = new Taxtype();
        $data = $Taxtype->getDetails($id, "");
        //$acc_details = $Taxtype->getAccountDetails();
    
        $approver_list = $Taxtype->getApprover($action);

        return $this->render('tax_type_details', ['action' => $action, 'data' => $data,'approver_list' => $approver_list]);
    }

    public function actionView($id) {
        $Taxtype = new Taxtype();
        $access = $Taxtype->getAccess();
        if(count($access)>0) {
            if($access[0]['r_view']==1) {
                $Taxtype->setLog(' Taxtype', '', 'View', '', 'View Tax type Details', 'acc_gst_tax_type_master', $id);
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
        $Taxtype = new Taxtype();
        $access = $Taxtype->getAccess();
        $data = $Taxtype->getDetails($id, "");
        if(count($access)>0) {
            if($access[0]['r_edit']==1 && $access[0]['session_id']==$data[0]['updated_by']) {
                $Taxtype->setLog('Taxtype', '', 'Edit', '', 'Edit Tax type Details', 'acc_gst_tax_type_master', $id);
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
        $Taxtype = new Taxtype();
        $access = $Taxtype->getAccess();
        $data = $Taxtype->getDetails($id, "");
        if(count($access)>0) {
            if($access[0]['r_approval']==1 && $access[0]['session_id']!=$data[0]['updated_by']) {
                $Taxtype->setLog('Taxtype', '', 'Authorise', '', 'Authorise Tax type Details', 'acc_gst_tax_type_master', $id);
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
        $Taxtype = new Taxtype();
        $result = $Taxtype->save();
        $this->redirect(array('taxtype/index'));
    }

    public function actionChecktaxtypeavailablity() {
        $Taxtype = new Taxtype();
        $result = $Taxtype->checkTaxTypeAvailablity();
        echo $result;
    }
}