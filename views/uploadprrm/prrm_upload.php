<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

// if($action == "Create") {
// 	$this->title = 'Create Payment Receipt';
// } else {
// 	$this->title = 'Update Payment Receipt: ' . $data[0]['id'];
// }

$this->title = 'Jabong Reconciliation Upload' . (isset($data[0]['id'])?': '.$data[0]['id']:'');

$this->params['breadcrumbs'][] = ['label' => 'Jabong Reconciliation', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Upload';//ucfirst($action);
$mycomponent = Yii::$app->mycomponent;
?>

<style>
	#payment_receipt .error {color: #dd4b39!important;}
	.table-head { font-weight:100;  
	    background: #41ace9; 
	    color: #fff;
	    border-bottom: 1px solid #41ace9;
	    background-image: linear-gradient(#54b4eb, #2fa4e7 60%, #1d9ce5);
	}
	.form-horizontal .checkbox, .form-horizontal .radio { padding:0;  margin:0; min-height:auto; line-height:20px;}
	.checkbox input[type=checkbox], .checkbox-inline input[type=checkbox], .radio input[type=radio], .radio-inline input[type=radio] { position:relative; margin:0;}
	.table>thead>tr>th {   vertical-align: middle;  border-bottom: 2px solid #ddd;}
	.checkbox, .radio { margin:0; padding:0;}
	.bold-text {    background-color: #f1f1f1; text-align:right;}
	.bold-text th {text-align:right!important;}
	.ad_hock{display:none;}
	#knock_off{display:none;} 
	.form-horizontal .control-label {font-size: 12px; letter-spacing: .5px; margin-top:5px; }
	.form-devident { margin-top: 10px; }
	.form-devident h4 { border-bottom: 1px dashed #ddd; padding-bottom: 10px; }
	.select2-container--default .select2-selection--multiple .select2-selection__choice {
		background-color: #3c8dbc!important;
	}
</style>

<div class="grn-index">
	<div class=" col-md-12 ">
		<div class="btn-container ">
			<div class="col-md-12">
			<form id="prrm_upload" class="form-horizontal" action="#" method="post" onkeypress="return event.keyCode != 13;"  enctype="multipart/form-data"> 
				<input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
				<div class="form-group">
					<div class=" col-md-4 col-sm-4 col-xs-4">
						<label class="control-label">Select File Type</label>
						<div class=" ">
							<select class="form-control " id="file_type" name="file_type">
								<option value="">Select</option>
								<option value="DN">DN</option>
								<option value="GRN">GRN</option>
								
							</select>
						</div>
					</div>
					<div class=" col-md-4 col-sm-4 col-xs-4">
						<label class="control-label">Upload File</label>
						<div class=" ">
							<div class=" "> 
								<input type="file" class="form-control" name="prrm_file" id="image" placeholder="image" value=""/>
							</div>
						</div>
					</div>
					<div class="col-md-2 col-sm-2 col-xs-6 "> 
						<label class="control-label"> </label>
						<div class="btn-container ">
							<input type="submit" class="form-control btn btn-success" id="generate" name="generate" value="Upload Report" />
						</div>
					</div>
				</div>
			</form>
			</div>
		</div>
	</div>
</div> 

<script type="text/javascript">
    var BASE_URL="<?php echo Url::base(); ?>";
		
</script>


<?php 
    $this->registerJsFile(
	    '@web/js/jquery-ui-1.11.2/jquery-ui.min.js',
	    ['depends' => [\yii\web\JqueryAsset::className()]]
	);
	$this->registerJsFile(
        '@web/js/datatable.js',
        ['depends' => [\yii\web\JqueryAsset::className()]]
    );
?>