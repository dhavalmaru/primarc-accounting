<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Resource */

$this->title = 'Update Resource: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Resources', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="resource-update">

    

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
