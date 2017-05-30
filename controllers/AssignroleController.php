<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use app\models\AssignRole;

class AssignroleController extends Controller
{
    public function actionIndex() {
        $assign_role = new AssignRole();
        $access = $assign_role->getAccess();
        if(count($access)>0) {
            if($access[0]['r_view']==1) {
                $data = $assign_role->getDetails();
                
                return $this->render('assignrole_list', ['data' => $data, 'access' => $access]);
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
        $assign_role = new AssignRole();
        $access = $assign_role->getAccess();
        if(count($access)>0) {
            if($access[0]['r_insert']==1) {
                $users = $assign_role->getUsers();
                $roles = $assign_role->getRoles();
                return $this->render('assignrole_details', ['users'=>$users, 'roles'=> $roles]);
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
        $assign_role = new AssignRole();
        $access = $assign_role->getAccess();
        if(count($access)>0) {
            if($access[0]['r_edit']==1) {
                $data = $assign_role->getDetails($id);
                $users = $assign_role->getUsers($id);
                $roles = $assign_role->getRoles();
                
                return $this->render('assignrole_details', ['access' => $access, 'data' => $data, 'users'=>$users, 'roles'=> $roles]);
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
        $assign_role = new AssignRole();
        $access = $assign_role->getAccess();
        if(count($access)>0) {
            if($access[0]['r_insert']==1 || $access[0]['r_edit']==1) {
                $result = $assign_role->save();
                $this->redirect(array('assignrole/index'));
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

    public function actionCheckuserroleavailablity(){
        $assign_role = new AssignRole();
        $result = $assign_role->checkUserRoleAvailablity();
        echo $result;
    }
}
?>