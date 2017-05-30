<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace app\controllers;

use dektrium\user\controllers\SecurityController as BaseSecurityController;
//namespace dektrium\user\controllers;
use yii\helpers\Url;
//use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\authclient\ClientInterface;
use app\models\User;
use app\models\Login;
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
        // if (!\Yii::$app->user->isGuest) {
        //     $this->goHome();
        // }

        $model = \Yii::createObject(\app\models\LoginForm::className());

        $this->performAjaxValidation($model);
        
        
       // var_dump(Yii::$app->getRequest()->post());die;
       
        if ($model->load(Yii::$app->getRequest()->post()) && $model->login()) 
        {
            
            // $userinfo=  \Yii::$app->user->identity;            
            // $usersession=new \common\models\CommonCode();
            // $usersession->setUserPermission($userinfo);   
            
            // $session = Yii::$app->session;
            // $userPermission=  json_decode($session->get('userPermission'));
            // if(isset($userPermission->company_id))
            // {
            // $company_id=$userPermission->company_id; 
        
            // //set log history
            // $userlog=new \backend\models\UserLogHistory();
            // $userlog->company_id=$company_id;
            // $userlog->user_id=\Yii::$app->user->id;
            // $userlog->user_action="User Login";
            // $userlog->action_date=date("Y-m-d H:i:s");
            // $userlog->save(false);
            
            // }
            
            // return $this->redirect(['/dashboard/index']);
            // //return $this->goBack();

            $user_id = \Yii::$app->user->id;
            // echo $user_id;
            $login = new Login();
            $data = $login->getUser($user_id);

            if(count($data)>0){
                $session = \Yii::$app->session;
                $session->open();
                $session['session_id'] = $data[0]['id'];
                $session['username'] = $data[0]['username'];
                $session['role_id'] = $data[0]['role_id'];

                for($i=0; $i<count($data); $i++){
                    $session[$data[$i]['r_section']] = $data[$i]['r_view'];
                }

                $login->log("User Login");
                $this->redirect(array('welcome/index'));
            }
        }

        $this->layout = 'other';
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

        $login = new Login();
        $login->log("User Logout");
        
        if(isset($userPermission->company_id))
        {
        $company_id=$userPermission->company_id; 
        $user_id=Yii::$app->user->id;
        $event = $this->getUserEvent(Yii::$app->user->identity);

        $this->trigger(self::EVENT_BEFORE_LOGOUT, $event);

        Yii::$app->getUser()->logout();


        $this->trigger(self::EVENT_AFTER_LOGOUT, $event);
        
       
        
            //set log history
            // $userlog=new \backend\models\UserLogHistory();
            // $userlog->company_id=$company_id;
            // $userlog->user_id=$user_id;
            // $userlog->user_action="User Logout";
            // $userlog->action_date=date("Y-m-d H:i:s");
            // $userlog->save(false);


        }else{

            $user_id=Yii::$app->user->id;
            $event = $this->getUserEvent(Yii::$app->user->identity);

            $this->trigger(self::EVENT_BEFORE_LOGOUT, $event);

            Yii::$app->getUser()->logout();

            $this->trigger(self::EVENT_AFTER_LOGOUT, $event);
            
            $session = \Yii::$app->session;
            $session->close();
            
        }
        


        return $this->goHome();
    }
   
    

   

    

}
