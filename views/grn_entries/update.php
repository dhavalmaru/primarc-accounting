<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\GrnEntries */

$this->title = 'Update Grn Entries: ' . $model->grn_entries_id;
$this->params['breadcrumbs'][] = ['label' => 'Grn Entries', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->grn_entries_id, 'url' => ['view', 'id' => $model->grn_entries_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="grn-entries-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
