<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\RolePermission */

$this->title = 'Update Role Permission: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Role Permissions', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="role-permission-update">

   

    <?= $this->render('_form', [
        'model' => $model,
                'orgnzationArray'=>$orgnzationArray,
                'permissionArray'=>$permissionArray,
                'resourceArray'=>$resourceArray,
                'marketplaceArray'=>$marketplaceArray,
                'productCategoryArray'=>$productCategoryArray,
                'warehouseArray'=>$warehouseArray,
                "permissions"=>$permissions,
                'reportpermissionMasterArray'=>$reportpermissionMasterArray,
                'authpermissionObj'=>$authpermissionObj
              
    ]) ?>

</div>
