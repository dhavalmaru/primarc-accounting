<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

// if($action == "Create") {
// 	$this->title = 'Create Payment Receipt';
// } else {
// 	$this->title = 'Update Payment Receipt: ' . $data[0]['id'];
// }

$this->title = ucfirst($action) . ' Amazon State Master' . (isset($data[0]['id'])?': '.$data[0]['id']:'');

$this->params['breadcrumbs'][] = ['label' => 'Amazon State Master', 'url' => ['index']];
$this->params['breadcrumbs'][] = ucfirst($action);
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
</style>

<div class="grn-index">
	<div class=" col-md-12 ">
		<form id="amazon_state_form" class="form-horizontal" action="<?php echo Url::base(); ?>index.php?r=amazonstate%2Fsave" method="post" onkeypress="return event.keyCode != 13;"> 
			<input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
			<input type="hidden" id="action" name="action" value="<?php if(isset($action)) echo $action; ?>">
			<div class="form-group">
				<div class="col-md-3 col-sm-12 col-xs-12">
					<label class="control-label">Amazon State</label>
					<div class="">
						<input type="hidden" id="id" name="id" value="<?php if(isset($data[0])) echo $data[0]['id']; ?>" />
						<input type="hidden" id="company_id" name="company_id" value="<?php if(isset($data)) echo $data[0]['company_id']; else if(isset($session['company_id'])) echo $session['company_id']; ?>" />
						<input type="text" id="amazon_state" class="form-control"  name="amazon_state" value="<?php if(isset($data[0])) echo $data[0]['amazon_state']; ?>" />
					</div>
				</div>
			
				<div class="col-md-6 col-sm-12 col-xs-12">
					<label class="control-label">Erp State</label>
					<div class="">
						<div class="">  
							<input id="erp_state" name="erp_state" class="form-control" type="text" value="<?php if(isset($data[0])) echo $data[0]['erp_state']; ?>"  />
						</div>
					</div>
				</div>
			</div>
			

			<div class="form-group">
	         	<div class="col-md-6 col-sm-6 col-xs-6">
					<label class="control-label">Remarks</label>
					<textarea id="remarks" name="remarks" class="form-control" rows="2" maxlength="1000"><?php if(isset($data)) echo $data[0]['approver_comments']; ?></textarea>
				</div>
				<!-- <div class="col-md-3 col-sm-3 col-xs-6">
					<label class="control-label">Approver</label>
					<div>
						<select id="approver_id" name="approver_id" class="form-control select2" data-error="#approver_error">
							<option value="">Select</option>
							<?php //for($i=0; $i<count($approver_list); $i++) { ?>
								<option value="<?php //echo $approver_list[$i]['id']; ?>" <?php //if(isset($data[0])) { if($data[0]['approver_id']==$approver_list[$i]['id']) echo "selected"; } ?>><?php //echo $approver_list[$i]['username']; ?></option>
							<?php //} ?>
						</select>
						<span id="approver_error"></span>
					</div>
				</div> -->
			</div>

			<!-- Button -->
			<div class="btn-container "> 
				<div class="col-md-12">
					<!-- <button type="submit" class="btn btn-success btn-sm">Submit For Approval</button> -->
					<input type="submit" class="btn btn-success btn-sm" id="btn_submit" name="btn_submit" value="Submit For Approval" />
					<!-- <input type="submit" class="btn btn-danger btn-sm" id="btn_reject" name="btn_reject" value="Reject" /> -->
					<a href="<?php echo Url::base(); ?>index.php?r=amazonstate%2Findex" class="btn btn-primary btn-sm pull-right" >Cancel</a>
					<!-- <button type="submit" class="btn btn-danger btn-sm" >Cancel </button> -->
				</div>
			</div>
		</form>

	
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
        '@web/js/amazon_state_master.js',
        ['depends' => [\yii\web\JqueryAsset::className()]]
    );
	// $this->registerJsFile(
	    // '@web/plugins/select2/js/select2.full.min.js',
	    // ['depends' => [\yii\web\JqueryAsset::className()]]
	// );
	// $this->registerJsFile(
	//     'https://code.jquery.com/ui/1.11.4/jquery-ui.min.js',
	//     ['depends' => [\yii\web\JqueryAsset::className()]]
	// );
?>