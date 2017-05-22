<?php

namespace app\controllers;

use Yii;
// use app\models\AccountMaster;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;

class TestController extends Controller 
{
    public function actionIndex() {
        // $acc_master = new AccountMaster();
        // $pending = $acc_master->getAccountDetails("", "pending");
        // $approved = $acc_master->getAccountDetails("", "approved");
        return $this->render('stream');
    }
}