<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use app\models\UserRole;

class WelcomeController extends Controller
{
    public function actionIndex()
    {
        return $this->render('welcome');
    }
}