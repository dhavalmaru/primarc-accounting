<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\jui\Autocomplete;

use yii\jui\DatePicker;
use yii\web\JsExpression;
use yii\db\Query;
use yii\web\Session;

$this->title = 'Group Details';
$this->params['breadcrumbs'][] = $this->title;
$session = Yii::$app->session;
?>
<style type="text/css">
input:-webkit-autofill {
    background-color: white !important;
}
/*select {
	width: 100%;
}*/
.form-horizontal .control-label { font-size: 12px; letter-spacing: .5px; margin-top:5px; }
.form-devident { margin-top: 10px; }
.form-devident h4 { border-bottom: 1px dashed #ddd; padding-bottom: 10px; }
.download_file {display: block;}
</style>
<link href="http://www.jqueryscript.net/css/jquerysctipttop.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.3.4/css/bootstrap.min.css" crossorigin="anonymous">
<link rel="stylesheet" href="http://cdn.bootcss.com/font-awesome/4.5.0/css/font-awesome.min.css" type="text/css" media="screen" title="no title" charset="utf-8"/>

<div class="grn-index">
	<div class=" col-md-12">  
		<form id="account_master" class="form-horizontal" action="<?php echo Url::base(); ?>index.php?r=accountmaster%2Fsave" method="post" enctype="multipart/form-data" onkeypress="return event.keyCode != 13;"> 
			<input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
			<div class="form-group">
				<div class="col-md-3 col-sm-3 col-xs-6">
					<label class="control-label">Type</label>
					<input type="hidden" id="action" name="action" value="<?php if(isset($action)) echo $action; ?>">
					<input type="hidden" id="id" name="id" value="<?php if(isset($data)) echo $data[0]['id']; ?>" />
					<input type="hidden" id="company_id" name="company_id" value="<?php if(isset($data)) echo $data[0]['company_id']; else if(isset($session['company_id'])) echo $session['company_id']; ?>" />
					<input type="hidden" id="status" name="status" value="<?php if(isset($data)) echo $data[0]['status']; ?>" />
					<input type="hidden" id="type_val" name="type_val" value="<?php if(isset($data)) echo $data[0]['type']; ?>" />
					<select class="form-control" id="type" name="type" <?php if(isset($data)) echo 'disabled'; ?>>
						<option value="">Select</option>
						<option value="Vendor Goods" <?php if(isset($data)) { if($data[0]['type']=="Vendor Goods") echo "selected"; } ?>>Vendor Goods</option>
						<option value="Vendor Expenses" <?php if(isset($data)) { if($data[0]['type']=="Vendor Expenses") echo "selected"; } ?>>Vendor Expenses</option>
						<option value="Bank Account" <?php if(isset($data)) { if($data[0]['type']=="Bank Account") echo "selected"; } ?>>Bank Account</option>
						<option value="Goods Purchase" <?php if(isset($data)) { if($data[0]['type']=="Goods Purchase") echo "selected"; } ?>>Goods Purchase</option>
						<option value="Tax" <?php if(isset($data)) { if($data[0]['type']=="Tax") echo "selected"; } ?>>Tax</option>
						<option value="CGST" <?php if(isset($data)) { if($data[0]['type']=="CGST") echo "selected"; } ?>>CGST</option>
						<option value="SGST" <?php if(isset($data)) { if($data[0]['type']=="SGST") echo "selected"; } ?>>SGST</option>
						<option value="IGST" <?php if(isset($data)) { if($data[0]['type']=="IGST") echo "selected"; } ?>>IGST</option>
						<option value="Goods Sales" <?php if(isset($data)) { if($data[0]['type']=="Goods Sales") echo "selected"; } ?>>Goods Sales</option>
						<option value="Employee" <?php if(isset($data)) { if($data[0]['type']=="Employee") echo "selected"; } ?>>Employee</option>
						<option value="Others" <?php if(isset($data)) { if($data[0]['type']=="Others") echo "selected"; } ?>>Others</option>
					</select>
				</div>

				<div id="bs-treeetable" class="treetable">
					Loading ...
				</div>
			</div>

			<div class="form-group">
	         	<div class="col-md-6 col-sm-6 col-xs-6">
					<label class="control-label">Remarks</label>
					<textarea id="remarks" name="remarks" class="form-control" rows="2" maxlength="1000"><?php if(isset($data)) echo $data[0]['approver_comments']; ?></textarea>
				</div>
				<div class="col-md-3 col-sm-3 col-xs-6">
					<label class="control-label">Approver</label>
					<select id="approver_id" name="approver_id" class="form-control">
						<option value="">Select</option>
						<?php for($i=0; $i<count($approver_list); $i++) { ?>
							<option value="<?php echo $approver_list[$i]['id']; ?>" <?php if(isset($data[0])) { if($data[0]['approver_id']==$approver_list[$i]['id']) echo "selected"; } ?>><?php echo $approver_list[$i]['username']; ?></option>
						<?php } ?>
					</select>
				</div>
			</div>

			<div class="form-group btn-container"> 
				<div class="col-md-12">
					<!-- <button type="submit" class="btn btn-success btn-sm" id="btn_submit">Submit For Approval  </button> -->
					<input type="submit" class="btn btn-success btn-sm" id="btn_submit" name="btn_submit" value="Submit For Approval" />
					<input type="submit" class="btn btn-danger btn-sm" id="btn_reject" name="btn_reject" value="Reject" />
					<a href="<?php echo Url::base(); ?>index.php?r=accountmaster%2Findex" class="btn btn-primary btn-sm pull-right">Cancel</a>
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
    $this->registerJsFile(
        '@web/js/account_master.js',
        ['depends' => [\yii\web\JqueryAsset::className()]]
    );

?>
