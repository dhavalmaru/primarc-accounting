<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\jui\Autocomplete;

use yii\jui\DatePicker;
use yii\web\JsExpression;
use yii\db\Query;

// use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel app\models\GrnSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Email Debit Note: ' . $data['id'];
$this->params['breadcrumbs'][] = ['label' => 'Grns', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $data['grn_id'], 'url' => ['update', 'id' => $data['grn_id']]];
$this->params['breadcrumbs'][] = ['label' => 'Email', 'url' => ['emaildebitnote', 'invoice_id' => $data['invoice_id']]];;
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
					<!-- <label class="control-label">To</label>
					<input type="text" class="form-control" id="to" name="to" value="" /> -->
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