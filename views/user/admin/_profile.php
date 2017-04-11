<?php

/*
 * This file is part of the Dektrium project
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/**
 * @var yii\web\View 					$this
 * @var dektrium\user\models\User 		$user
 * @var dektrium\user\models\Profile 	$profile
 */
$session = Yii::$app->session;
$userPermission=  json_decode($session->get('userPermission'));

$userimagepath=Yii::$app->params['userimagesPath'];
$userimage=Yii::$app->request->baseUrl.$userimagepath;
$user_image=$session->get("user_image");
?>

<?php $this->beginContent('@dektrium/user/views/admin/update.php', ['user' => $user]) ?>

<?php $form = ActiveForm::begin([
    'layout' => 'horizontal',
    'enableAjaxValidation' => true,
    'enableClientValidation' => false,
    'options' => ['enctype' => 'multipart/form-data'],
    'fieldConfig' => [
        'horizontalCssClasses' => [
            'wrapper' => 'col-sm-9',
        ],
    ],
]); ?>

<?= $form->field($profile, 'name') ?>
<?php // $form->field($profile, 'public_email') ?>
<?php // $form->field($profile, 'website') ?>
<?php // $form->field($profile, 'location') ?>
<?php // $form->field($profile, 'gravatar_email') ?>
<?php // $form->field($profile, 'bio')->textarea() ?>
<?= $form->field($profile, 'mobile') ?>
<?= $form->field($profile, 'phone') ?>
<?= $form->field($profile, 'fax') ?>
<?= $form->field($profile, 'user_image')->fileInput() ?>
<?php if(isset($profile->user_image) && !empty($profile->user_image)) 
    {echo '<center><img src= "'.$userimage.$profile->user_image.'" height="30"/></center>'; ?>
<?php }else{ ?>
   <center> <img src="<?= $userimage?>noimage.png" class="user-image" height="30" alt="User Image"/> </center>  
           <?php }?>    
    
<div class="row">
            <div class="col-lg-12">
                <?php
                if(isset($error))
                {?>
              <div style="color: red;"><?php echo $error?></div>    
                <?php }
                ?>
            </div>
</div>
<br>
<div class="form-group">
    <div class="col-lg-offset-3 col-lg-9">
        <?= Html::submitButton(Yii::t('user', 'Update'), ['class' => 'btn btn-block btn-success']) ?>
    </div>
</div>

<?php ActiveForm::end(); ?>

<?php $this->endContent() ?>
