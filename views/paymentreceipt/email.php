<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\jui\Autocomplete;

use yii\jui\DatePicker;
use yii\web\JsExpression;
use yii\db\Query;

$this->title = 'Email Payment Advice: ' . $payment_advice[0]['payment_id'];
$this->params['breadcrumbs'][] = ['label' => 'Payment Receipt', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $payment_advice[0]['payment_id'], 'url' => ['edit', 'id' => $payment_advice[0]['payment_id']]];
$this->params['breadcrumbs'][] = 'Email';
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
		<form id="account_master" class="form-horizontal" action="<?php echo Url::base(); ?>index.php?r=paymentreceipt%2Femail" method="post" enctype="multipart/form-data" onkeypress="return event.keyCode != 13;"> 
			<input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
			<input type="hidden" name="id" value="<?php echo $payment_advice[0]['id']; ?>" />
			<input type="hidden" name="payment_id" value="<?php echo $payment_advice[0]['payment_id']; ?>" />
			<input type="hidden" name="vendor_name" value="<?php echo $vendor_details[0]['vendor_name']; ?>" />
			<input type="hidden" name="company_id" value="<?php echo $vendor_details[0]['company_id']; ?>" />
			<div class="form-group">
				<div class="col-md-6 col-sm-6 col-xs-6">
					<label class="control-label">To</label>
					<input type="text" class="form-control" id="to" name="to" value="<?php echo $vendor_details[0]['contact_email']; ?>" />
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-12 col-sm-12 col-xs-6">
					<label class="control-label">Subject</label>
					<input type="text" class="form-control" id="subject" name="subject" value="Payment Advice For Payment No - <?php echo $payment_advice[0]['payment_id']; ?> - Primarc Pecan Retail Pvt Ltd" />
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-6 col-sm-6 col-xs-6">
					<label class="control-label">Attachment</label>
					<input type="hidden" name="attachment" value="<?php echo $payment_advice[0]['payment_advice_path']; ?>" />
					<a class="form-control" href="<?php echo Url::base(); ?>index.php?r=paymentreceipt%2Fdownload&id=<?php echo $payment_advice[0]['payment_id']; ?>" target="_blank">
						<?php 
						$payment_advice_path = $payment_advice[0]['payment_advice_path'];
						if(isset($payment_advice_path)) { 
							if(strrpos($payment_advice_path, "/")!==false) {
								echo substr($payment_advice_path, strrpos($payment_advice_path, "/")+1);
							}
						} ?>
					</a>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-12 col-sm-12 col-xs-6">
					<label class="control-label">Body</label>
					<textarea class="form-control" id="body" name="body" rows="12">Hi, 

PFA with payment advice of payment no - <?php echo $payment_advice[0]['payment_id']; ?>. 

Regards, 
Team Primarc</textarea>
				</div>
			</div>

			<div class="form-group btn-container"> 
				<div class="col-md-12">
					<button type="submit" class="btn btn-success btn-sm" id="btn_submit"> Send </button>
					<a href="<?php echo Url::base(); ?>index.php?r=paymentreceipt%2Fedit&id=<?php echo $payment_advice[0]['payment_id']; ?>" class="btn btn-danger btn-sm" >Cancel</a>
					<!-- <button type="submit" class="btn btn-danger btn-sm" >Cancel </button> -->
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