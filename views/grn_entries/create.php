<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\GrnEntries */

$this->title = 'Create Grn Entries';
$this->params['breadcrumbs'][] = ['label' => 'Grn Entries', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="grn-entries-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
