<?php

namespace app\controllers;

use Yii;
use app\models\GroupMaster;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;

class GroupmasterController extends Controller
{
    public function actionIndex() {
        $model = new GroupMaster();
        $access = $model->getAccess();
        if(count($access)>0) {
            if($access[0]['r_view']==1) {
                $list = $model->getGroupDetails(0);

                $model->setLog('GroupMaster', '', 'View', '', 'View Group Master Details', 'group_master', '');
                return $this->render('group_details', ['list' => $list]);
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

    public function actionSetaccounttype(){
        $model = new GroupMaster();
        $result = $model->setAccountType();
        return $result;
    }

    public function actionGetchildaccounttype(){
        $model = new GroupMaster();
        $result = $model->getChildAccountType();
        echo $result;
    }
}
