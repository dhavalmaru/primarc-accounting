<?php
use yii\helpers\Html;
?>
<p>You have entered the following information:</p>

<!-- <ul>
    <li><label>Name</label>: <?//= Html::encode($model->name) ?></li>
    <li><label>Email</label>: <?//= Html::encode($model->email) ?></li>
</ul> -->


<ul>
    <li><label>Name</label>: <?= Html::encode($model[0]['vendor_name']) ?></li>
    <li><label>Email</label>: <?= Html::encode($model[0]['grn_id']) ?></li>
</ul>