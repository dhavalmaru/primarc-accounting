<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\jui\Autocomplete;

use yii\jui\DatePicker;
use yii\web\JsExpression;
use yii\db\Query;

$this->title = 'Email Payment Advice: ' . $data['payment_id'];
$this->params['breadcrumbs'][] = ['label' => 'Payment Receipt', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $data['payment_id'], 'url' => ['edit', 'id' => $data['payment_id']]];
$this->params['breadcrumbs'][] = ['label' => 'Email', 'url' => ['emailpaymentadvice', 'id' => $data['payment_id']]];;
$mycomponent = Yii::$app->mycomponent;
?>
<style type="text/css">
input:-webkit-autofill {
    background-color: white !important;
}
select {
	width: 100%;
}
.form-horizontal .control-label { font-size: 12px; letter-spacing: .5px; margin-top:5px; }
.form-devident { margin-top: 10px; }
.form-devident h4 { border-bottom: 1px dashed #ddd; padding-bottom: 10px; }
.download_file {display: block;}
</style>
<div class="grn-index">
	<div class=" col-md-12">  
		<form id="account_master" class="form-horizontal" action="" method="post" enctype="multipart/form-data" onkeypress="return event.keyCode != 13;"> 
			<div class="form-group">
				<div class="col-md-12 col-sm-12 col-xs-12">
					<h4><?php echo $data['response']; ?></h4>
				</div>
			</div>
		</form>
	</div>
</div>

<?php 
	$this->registerJsFile(
	    '@web/js/jquery-ui-1.11.2/jquery-ui.min.js',
	    ['depends' => [\yii\web\JqueryAsset::className()]]
	);
?>