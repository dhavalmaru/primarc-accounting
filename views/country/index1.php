<?php
use yii\helpers\Html;
use yii\widgets\LinkPager;
?>
<h1>Countries</h1>
<ul>
<?php foreach ($country as $countries): ?>
    <li>
        <?= Html::encode("{$countries->name} ({$countries->code})") ?>:
        <?= $countries->population ?>
    </li>
<?php endforeach; ?>
</ul>

<?= LinkPager::widget(['pagination' => $pagination]) ?>