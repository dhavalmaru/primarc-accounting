<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Permission */

$this->title = 'Update Permission: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Permissions', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="permission-update">

  

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
