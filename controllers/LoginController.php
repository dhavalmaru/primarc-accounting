<?php

namespace app\controllers;

use Yii;
use app\models\Login;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Session;


/**
 * GrnController implements the CRUD actions for Grn model.
 */
class LoginController extends Controller
{
    public function actionIndex()
    {   
        $this->layout = false;
        return $this->render('login');
    }

    public function actionCheckcredentials() {
        $request = Yii::$app->request;
        $uname = $request->post('uname');
        $upass = $request->post('upass');

        // $uname = "prasad.bhisale@otbconsulting.co.in";
        // $upass = "pass@123";

        $login = new Login();
        $data = $login->getDetails($uname, $upass);

        if(count($data)>0){
            $result = 0;
        } else {
            $result = 1;
        }

        echo $result;
    }

    public function actionLogin() {
        // $uname = "prasad.bhisale@otbconsulting.co.in";
        // $upass = "pass@123";

        $request = Yii::$app->request;
        $uname = $request->post('uname');
        $upass = $request->post('upass');

        // $hash = Yii::$app->getSecurity()->generatePasswordHash($upass);
        // echo $hash;

        // if (Yii::$app->getSecurity()->validatePassword($upass, $hash)) {
        //     echo $upass;
        // }

        $login = new Login();
        $data = $login->getDetails($uname, $upass);

        if(count($data)>0){
            $session = Yii::$app->session;
            $session->open();
            $session['session_id'] = $data[0]['id'];
            $session['temp_temp'] = $data[0]['id'];
            $session['username'] = $data[0]['username'];
            $session['role_id'] = $data[0]['role_id'];
            $session['company_id'] = $data[0]['company_id'];

            $company = $login->getCompany();
            $session['company'] = $company;

            // $user_role = new UserRole();
            // $data = $user_role->getAccess();

            for($i=0; $i<count($data); $i++){
                $session[$data[$i]['r_section']] = $data[$i]['r_view'];
            }

            // echo json_encode($session);
            $this->redirect(array('welcome/index'));
        } else {
            $this->redirect(array('login/index'));
        }
    }

    public function actionSetcompany() {
        $request = Yii::$app->request;
        $company_id = $request->post('company_id');

        $session = Yii::$app->session;
        $curusr = $session['session_id'];

        $login = new Login();
        $data = $login->getDetailsByCompany($curusr, $company_id);

        if(count($data)>0){
            $session['session_id'] = $data[0]['id'];
            $session['temp_temp'] = $data[0]['id'];
            $session['username'] = $data[0]['username'];
            $session['role_id'] = $data[0]['role_id'];
            $session['company_id'] = $data[0]['company_id'];

            $company = $login->getCompany();
            $session['company'] = $company;

            // $user_role = new UserRole();
            // $data = $user_role->getAccess();

            for($i=0; $i<count($data); $i++){
                $session[$data[$i]['r_section']] = $data[$i]['r_view'];
            }

            echo 1;
        }
    }

}
