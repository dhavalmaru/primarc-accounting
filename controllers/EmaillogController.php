<?php

namespace app\controllers;

use Yii;
use app\models\EmailLog;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;

class EmaillogController extends Controller
{
    public function actionIndex(){
        $email_log = new EmailLog();
        $access = $email_log->getAccess();
        if(count($access)>0) {
            if($access[0]['r_view']==1) {
                $data = $email_log->getDetails();

                $email_log->setLog('EmailLog', '', 'View', '', 'View Email Log List', 'acc_email_log', '');
                return $this->render('email_log_list', ['access' => $access, 'data' => $data]);
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
}