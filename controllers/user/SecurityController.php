<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace app\controllers\user;

use dektrium\user\controllers\SecurityController as BaseSecurityController;
//namespace dektrium\user\controllers;
use yii\helpers\Url;
//use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\authclient\ClientInterface;
use app\models\User;
use yii;

/**
 * Controller that manages user authentication process.
 *
 * @property \dektrium\user\Module $module
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class SecurityController extends BaseSecurityController {

   

    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            $this->goHome();
        }

        $model = \Yii::createObject(\app\models\LoginForm::className());

        $this->performAjaxValidation($model);
        
        
       // var_dump(Yii::$app->getRequest()->post());die;
       
        if ($model->load(Yii::$app->getRequest()->post()) && $model->login()) 
        {
            
            $userinfo=  \Yii::$app->user->identity;            
            $usersession=new \common\models\CommonCode();
            $usersession->setUserPermission($userinfo);   
            
            $session = Yii::$app->session;
            $userPermission=  json_decode($session->get('userPermission'));
            if(isset($userPermission->company_id))
            {
            $company_id=$userPermission->company_id; 
        
            //set log history
            $userlog=new \app\models\UserLogHistory();
            $userlog->company_id=$company_id;
            $userlog->user_id=\Yii::$app->user->id;
            $userlog->user_action="User Login";
            $userlog->action_date=date("Y-m-d H:i:s");
            $userlog->save(false);
            
            }
            
            return $this->redirect(['/dashboard/index']);
            //return $this->goBack();
        }

        return $this->render('login', [
            'model'  => $model,
            'module' => $this->module,
        ]);
    }
    
     /**
     * Logs the user out and then redirects to the homepage.
     *
     * @return Response
     */
    public function actionLogout()
    {
        
        $session = Yii::$app->session;
        $userPermission=  json_decode($session->get('userPermission'));
        if(isset($userPermission->company_id))
        {
        $company_id=$userPermission->company_id; 
        $user_id=Yii::$app->user->id;
        $event = $this->getUserEvent(Yii::$app->user->identity);

        $this->trigger(self::EVENT_BEFORE_LOGOUT, $event);

        Yii::$app->getUser()->logout();

        $this->trigger(self::EVENT_AFTER_LOGOUT, $event);
        
       
        
         //set log history
            $userlog=new \app\models\UserLogHistory();
            $userlog->company_id=$company_id;
            $userlog->user_id=$user_id;
            $userlog->user_action="User Logout";
            $userlog->action_date=date("Y-m-d H:i:s");
            $userlog->save(false);
        }else{
        
          $user_id=Yii::$app->user->id;
          $event = $this->getUserEvent(Yii::$app->user->identity);

          $this->trigger(self::EVENT_BEFORE_LOGOUT, $event);

          Yii::$app->getUser()->logout();

          $this->trigger(self::EVENT_AFTER_LOGOUT, $event);
        
        }
        

        return $this->goHome();
    }
   
    

   

    

}
