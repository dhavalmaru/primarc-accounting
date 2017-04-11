<?php

namespace app\controllers;

use Yii;
use app\models\AccReport;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;

class WelcomeController extends Controller
{
    public function actionIndex()
    {
        return $this->render('welcome');
    }
}