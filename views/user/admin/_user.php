<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

/**
 * @var yii\widgets\ActiveForm 		$form
 * @var dektrium\user\models\User 	$user
 */


?>


<?= $form->field($user, 'role_id[]')->dropDownList($roleArray,['multiple' => true])->label("Role Name")?>
<?= $form->field($user, 'company_id')->dropDownList($companyArray)->label("Company Name")?>
<?= $form->field($user, 'email')->textInput(['maxlength' => 255]) ?>
<?php if($user->isNewRecord) {?>
<?= $form->field($user, 'name') ?>
<?php } else {?>
<?php } ?>
<?php
if(isset($isupdate))
{?>
<?= $form->field($user, 'username')->textInput(['maxlength' => 255,'readonly' => true]) ?>
<?php    
}else{
?>
<?= $form->field($user, 'username')->textInput(['maxlength' => 255]) ?>
<?php }?>
<?= $form->field($user, 'password')->passwordInput() ?>
<?php


$this->registerJs('$("document").ready(function(){ '
        ."$('#user-role_id').val([".$selectedRoleArray."]);"        
        . ' $("#user-role_id").multipleSelect({ filter: true });'
       
      .'});');

 
?>