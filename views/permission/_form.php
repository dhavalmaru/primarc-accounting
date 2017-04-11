<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Permission */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="permission-form">

    <?php $form = ActiveForm::begin(); ?>
    
     <div class="row">
       
      <div class="col-lg-6">
          <?= $form->field($model, 'permission_name')->textInput(['maxlength' => true]) ?>
      </div>
       
      <div class="col-lg-6">
          <?= $form->field($model, 'permission_code')->textInput(['maxlength' => true]) ?>
      </div>   
            
    </div>


    <?php // $form->field($model, 'created_at')->textInput() ?>

    <?php // $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
