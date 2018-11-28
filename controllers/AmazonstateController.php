<?php

namespace app\controllers;

use Yii;
use app\models\Amazonstate;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;

class AmazonstateController extends Controller 
{
    public function actionIndex() {
        $amazonstate = new Amazonstate();
        $access = $amazonstate->getAccess();
        if(count($access)>0) {
            if($access[0]['r_view']==1) {
                $pending = $amazonstate->getDetails("", "pending");
                $approved = $amazonstate->getDetails("", "approved");
                $rejected = $amazonstate->getDetails("", "rejected");

                $amazonstate->setLog('Amazonstate', '', 'View', '', 'View Tax type List', 'acc_amazon_state_master', '');
                return $this->render('amazon_state_list', ['access' => $access, 'pending' => $pending, 'approved' => $approved, 
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
        $amazonstate = new Amazonstate();
        $access = $amazonstate->getAccess();
        if(count($access)>0) {
            if($access[0]['r_insert']==1) {
                $action = 'insert';
                //$acc_details = $amazonstate->getAccountDetails();
              
                $approver_list = $amazonstate->getApprover($action);
                
                $amazonstate->setLog('Amazonstate', '', 'Insert', '', 'Insert Tax type Details', 'acc_amazon_state_master', '');
                // return $this->render('amazon_state_details', ['action' => $action, 'approver_list' => $approver_list]);
                return $this->render('amazon_state_details', ['action' => $action]);
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
		$amazonstate = new Amazonstate();
        $data = $amazonstate->getDetails($id, "");
        //$acc_details = $amazonstate->getAccountDetails();
    
        // $approver_list = $amazonstate->getApprover($action);
        // return $this->render('amazon_state_details', ['action' => $action, 'data' => $data,'approver_list' => $approver_list]);

        return $this->render('amazon_state_details', ['action' => $action, 'data' => $data]);
    }

    public function actionView($id) {
        $amazonstate = new Amazonstate();
        $access = $amazonstate->getAccess();
        if(count($access)>0) {
            if($access[0]['r_view']==1) {
                $amazonstate->setLog(' Amazonstate', '', 'View', '', 'View Tax type Details', 'acc_amazon_state_master', $id);
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
        $amazonstate = new Amazonstate();
        $access = $amazonstate->getAccess();
        $data = $amazonstate->getDetails($id, "");
        if(count($access)>0) {
            if($access[0]['r_edit']==1 && $access[0]['session_id']==$data[0]['updated_by']) {
                $amazonstate->setLog('Amazonstate', '', 'Edit', '', 'Edit Tax type Details', 'acc_amazon_state_master', $id);
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
        $amazonstate = new Amazonstate();
        $access = $amazonstate->getAccess();
        $data = $amazonstate->getDetails($id, "");
        if(count($access)>0) {
            if($access[0]['r_approval']==1 && $access[0]['session_id']!=$data[0]['updated_by']) {
                $amazonstate->setLog('Amazonstate', '', 'Authorise', '', 'Authorise Tax type Details', 'acc_amazon_state_master', $id);
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
        $amazonstate = new Amazonstate();
        $result = $amazonstate->save();
        $this->redirect(array('amazonstate/index'));
    }

    public function actionCheckamazonstateavailablity() {
        $amazonstate = new Amazonstate();
        $result = $amazonstate->checkAmazonstateAvailablity();
        echo $result;
    }
}