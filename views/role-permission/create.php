<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\RolePermission */

$this->title = 'Assign Role Permission';
$this->params['breadcrumbs'][] = ['label' => 'Role Permissions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="role-permission-create">



    <?=
    $this->render('_form', [
        'model' => $model,
        'orgnzationArray' => $orgnzationArray,
        'permissionArray' => $permissionArray,
        'resourceArray' => $resourceArray,
        'marketplaceArray' => $marketplaceArray,
        'productCategoryArray' => $productCategoryArray,
        'warehouseArray'=>$warehouseArray,
        "permissions"=>$permissions,
        "companyArray"=>$companyArray,
        'reportpermissionMasterArray'=>$reportpermissionMasterArray,
        'authpermissionObj'=>$authpermissionObj
    ])
    ?>

</div>
