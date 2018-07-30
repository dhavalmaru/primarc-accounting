<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Grn */

$this->title = 'Create Go';
$this->params['breadcrumbs'][] = ['label' => 'Grns', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="grn-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
