<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace backend\controllers\user;

use dektrium\user\controllers\RecoveryController as BaseRecoveryController;
use yii\web\Controller;
use backend\models\RecoveryForm;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use backend\models\User;
use dektrium\user\models\Token;
use yii\base\InvalidParamException;
use yii;


/**
 * RecoveryController manages password recovery process.
 *
 * @property \dektrium\user\Module $module
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class RecoveryController extends BaseRecoveryController
{
    
    /**
     * Displays page where user can request new recovery message.
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    
    public function actionRequest()
    { 
        if (!$this->module->enablePasswordRecovery) {
            throw new NotFoundHttpException();
        }

        /** @var RecoveryForm $model */
        $model = Yii::createObject([
            'class'    => RecoveryForm::className(),
            'scenario' => 'request',
        ]);

        $this->performAjaxValidation($model);

        if ($model->load(Yii::$app->request->post()) && $model->sendRecoveryMessage()) {
            return $this->render('/message', [
                'title'  => Yii::t('user', 'Recovery message sent'),
                'module' => $this->module,
            ]);
        }

        return $this->render('request', [
            'model' => $model,
        ]);
    }

}
