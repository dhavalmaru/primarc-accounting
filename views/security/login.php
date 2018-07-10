<?php

// use yii\helpers\Html;
// use yii\grid\GridView;
// use yii\helpers\Url;

use dektrium\user\widgets\Connect;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\GrnSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Login Master';
// $this->params['breadcrumbs'][] = $this->title;
?>

<div class="login-box">
    <div class="login-logo">
        <a href="#"><b>Primarc</b> Pecan</a>
    </div>
    <div class="login-box-body">
        <?php $form = ActiveForm::begin([
            'id' => 'login-form',
            'enableAjaxValidation' => true,
            'enableClientValidation' => false,
            'validateOnBlur' => false,
            'validateOnType' => false,
            'validateOnChange' => false,
        ]) ?>

        <?= $form->field($model, 'login',
                ['inputOptions' => ['autofocus' => 'autofocus', 'class' => 'form-control', 'tabindex' => '1']]
            );
            ?>

        <?= $form->field(
            $model,
            'password',
            ['inputOptions' => ['class' => 'form-control', 'tabindex' => '2']])
            ->passwordInput()
            // ->label(
            //     Yii::t('user', 'Password')
            //     . ' (' . Html::a(
            //             Yii::t('user', 'Forgot password?'),
            //             ['/recovery/request'],
            //             ['data-method' => 'post'],
            //             ['tabindex' => '5']
            //         )
            //         . ')'
            // ) ?>

        

        <?= $form->field($model, 'rememberMe')->checkbox(['tabindex' => '3']) ?>

        <?= Html::submitButton(
            Yii::t('user', 'Sign in'),
            ['class' => 'btn btn-primary btn-block', 'tabindex' => '4']
        ) ?>

        <?php ActiveForm::end(); ?>

        <!-- <p class="text-center">
            <?//= Html::a(Yii::t('user', 'Didn\'t receive confirmation message?'), ['/registration/resend']) ?>
        </p>
        <p class="text-center">
            <?//= Html::a(Yii::t('user', 'Don\'t have an account? Sign up!'), ['/registration/register']) ?>
        </p> -->

        <?= Connect::widget([
            'baseAuthUrl' => ['/security/auth'],
        ]) ?>

    </div>
</div>