<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Resource */

$this->title = 'Create Resource';
$this->params['breadcrumbs'][] = ['label' => 'Resources', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="resource-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
