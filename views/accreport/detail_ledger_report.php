<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

$this->title = 'Purchase Register Report';
$mycomponent = Yii::$app->mycomponent;
?>

<style>
<<<<<<< HEAD
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
	.table-hover>tbody>tr:hover {
	    background:none!important;
	}
	table tr td { border: 1px solid #eee!important; }
	/*.form-devident h3 { border-bottom: 1px dashed #ddd; padding-bottom: 10px; }*/
	#report_filter { border-bottom: 1px dashed #ddd; }
	#report_header label { display: block; padding-bottom: 0px; margin-bottom: 0px;}
	.ui-datepicker {z-index: 1000!important;}
	.show_narration {word-break: break-all;}
	@media print {
		#report_filter, #btn_print {
			display:none;
		}
		@page {size: landscape;}
		.btn-group {display: none;}
	}
	#example_wrapper .row:first-child .col-md-6:last-child .btn-group { margin-right: 0px; }
	#example_wrapper .row:nth-child(1) {margin-top: -60px;}
	#example_wrapper .row:nth-child(2) {margin-top: 20px;}
	#example {
		width: 1040px !important;
	}

	/*tbody{
	   	height:150px;display:block;overflow:scroll
	}*/
	/*table {
	    width: 100%;
	    display:block;
	}
	thead {
	    display: inline-block;
	    width: 100%;
	    height: 30px;
	}
	tbody {
	    height: 300px;
	    display: inline-block;
	    width: 100%;
	    overflow: auto;
	}*/

	#example_wrapper .row:first-child .col-md-6:last-child .btn-group {
		margin-bottom:94px; 
		float: right;
		margin-right: -178px;
		float: left!important;
	}
	.form-control{
		height: 32px!important;
	}
	.select2-container--default .select2-selection--multiple .select2-selection__choice {
		background-color: #3c8dbc!important;
	}
	.error {
		font-weight: 500;
		color: #f95353!important;
		font-size: 12px;
		letter-spacing: .5px;
		border: 0px solid #f95353;
		margin: 0; 
	}
	.dataTables_scroll {
		overflow:auto;
	}
	#example_wrapper .row:first-child .col-md-6:last-child .btn-group {
		margin-bottom: 0px!important; 
		float: right!important; 
		margin-right: 0px!important; 
	}
	#example_wrapper .row:first-child{
		margin-top: 0px;
	}
	#example_wrapper .row:nth-child(2){
		margin-top: 0px;
	}
</style>
<!-- 
 <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.min.css" /> -->

=======
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
.table-hover>tbody>tr:hover {
    background:none!important;
}
table tr td { border: 1px solid #eee!important; }
/*.form-devident h3 { border-bottom: 1px dashed #ddd; padding-bottom: 10px; }*/
#report_filter { border-bottom: 1px dashed #ddd; }
#report_header label { display: block; padding-bottom: 0px; margin-bottom: 0px;}
.ui-datepicker {z-index: 1000!important;}
.show_narration {word-break: break-all;}
@media print {
	#report_filter, #btn_print {
		display:none;
	}
	@page {size: landscape;}
	.btn-group {display: none;}
}
#example_wrapper .row:first-child .col-md-6:last-child .btn-group { margin-right: 0px; }
#example_wrapper .row:nth-child(1) {margin-top: -60px;}
#example_wrapper .row:nth-child(2) {margin-top: 20px;}
#example {
	width: 1040px !important;
}

/*tbody{
   	height:150px;display:block;overflow:scroll
}*/
/*table {
    width: 100%;
    display:block;
}
thead {
    display: inline-block;
    width: 100%;
    height: 30px;
}
tbody {
    height: 300px;
    display: inline-block;
    width: 100%;
    overflow: auto;
}*/

#example_wrapper .row:first-child .col-md-6:last-child .btn-group {
	margin-bottom:94px; 
	float: right;
	margin-right: -178px;
	float: left!important;
}
.form-control{
	height: 32px!important;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice {
	background-color: #3c8dbc!important;
}
.error {
	font-weight: 500;
	color: #f95353!important;
	font-size: 12px;
	letter-spacing: .5px;
	border: 0px solid #f95353;
	margin: 0; 
}
.dataTables_scroll {
	overflow:auto;
}
#example_wrapper .row:first-child .col-md-6:last-child .btn-group {
	margin-bottom: 0px!important; 
	float: right!important; 
	margin-right: 0px!important; 
}
#example_wrapper .row:first-child{
	margin-top: 0px;
}
#example_wrapper .row:nth-child(2){
	margin-top: 0px;
}

</style><!-- 
 <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.min.css" /> -->
>>>>>>> 40251667d20641f61579b49c4e0131e7351baf6f
<?php 
$this->registerCssFile(
        '@web/css/select2.min.css',
        ['depends' => [\yii\web\JqueryAsset::className()]]
    );
?>
<div class="grn-index container">
	<form id="detailledger_report" class="form-horizontal" action="<?php echo Url::base(); ?>index.php?r=accreport%2Fgetdetailledgerreport" method="post" enctype="multipart/form-data" onkeypress="return event.keyCode != 13;">
		<input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
		<div id="report_filter">
		<div class="row">
			<div class="col-md-4 col-sm-2 col-xs-6">
				<label class="control-label">Account Name</label>
				<select class="form-control" id="account" name="account[]" multiple="multiple" data-error="#accounterror">
					<!-- <option value="">Select</option> -->
					<?php for($i=0; $i<count($acc_details); $i++) { ?>
					<option value="<?php echo $acc_details[$i]['id']; ?>" 
					<?php 
					if(isset($account)) 
						{
						   echo (in_array($acc_details[$i]['id'],$account)?'selected':'');	
						}?> > <?php echo $acc_details[$i]['legal_name']; ?>

					</option>
					<?php  } ?>
				</select>
				<span id="accounterror"></span>
			</div>
			<div class="col-md-4 col-sm-2 col-xs-6">
					<label class="control-label">Voucher Type</label>
					<select  id="vouchertype" class="form-control select2-container" name="vouchertype[]" multiple="multiple" data-error="#vouchererror">
						<option value="purchase" <?=(in_array('purchase',$vouchertype)?'selected':'')?> >Purchase</option>
						<option value="journal_voucher" <?=(in_array('journal_voucher',$vouchertype)?'selected':'')?>> Journal Voucher</option>
						<option value="payment_receipt" <?=(in_array('payment_receipt',$vouchertype)?'selected':'')?>> Payment/Receipt</option>
						<option value="go_debit_details" <?=(in_array('go_debit_details',$vouchertype)?'selected':'')?>>Good Debit Details</option>
						<option value="other_debit_credit" <?=(in_array('other_debit_credit',$vouchertype)?'selected':'')?>>Other Debit Details</option>
						<option value="promotion" <?=(in_array('promotion',$vouchertype)?'selected':'')?>>Promotion</option>
					</select>
					<span id="vouchererror"></span>
			</div>
			<div class="col-md-3 col-sm-2 col-xs-6">
				<label class="control-label">Select State</label>
				<select class="form-control" id="state" name="state[]" multiple="multiple" data-error="#stateerror">
					<!-- <option value="">Select</option> -->
				<?php for($i=0; $i<count($state_detail); $i++) { ?>
					<option value="<?php echo $state_detail[$i]['id']; ?>" 
					<?php 
					if(isset($state_detail)) 
						{
						   echo (in_array($state_detail[$i]['id'],$state)?'selected':'');	
						}?> > <?php echo $state_detail[$i]['state_name']; ?>
						</option>
				<?php } ?>
				</select>
				<span id="stateerror"></span>
			</div>	
		</div>
		<div class="row">
			<div class=" col-md-4  col-sm-2 col-xs-6">
				<label class="control-label">Date Type</label>
				<div class=" ">

					<div class=" "> 
						<select class="form-control" id="date_criteria" name="date_criteria">
							<option value="updated_date" <?=($date_criteria=='updated_date'?'selected':'')?>>Posting Date</option>
							<option value="invoice_date"  <?=($date_criteria=='invoice_date'?'selected':'')?>>Invoice Date</option>
							<option value="grn_approved_date_time" <?=($date_criteria=='grn_approved_date_time'?'selected':'')?>>Grn Approved Date</option>
							<option value="gi_date" <?=($date_criteria=='gi_date'?'selected':'')?>>GI Date</option>
						</select>
					</div>
				</div>
			</div>
		   	<div class=" col-md-4  col-sm-2 col-xs-6">
				<label class="control-label">From Date</label>
				<div class=" ">
					<div class=" ">
						<input class="form-control datepicker" type="text" id="from_date" name="from_date" value="<?php if(isset($from_date)) echo (($from_date!=null && $from_date!='')?$from_date:''); ?>" readonly />
					</div>
				</div>
			</div>
			<div class=" col-md-3  col-sm-2 col-xs-6">
				<label class="control-label">To Date</label>
			      <div class=" ">
					<div class=" ">
						<input class="form-control datepicker" type="text" id="to_date" name="to_date" value="<?php if(isset($to_date)) echo (($to_date!=null && $to_date!='')?$to_date:'');  ?>" readonly />
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class=" col-md-4 col-sm-2 col-xs-6">
				<label class="control-label">View</label>
				<div class=" ">
					<div class=" "> 
						<select class="form-control" id="view" name="view">
							<option value="default" <?=($view=='default'?'selected':'')?>>Default</option>
							<option value="tax" <?=($view=='tax'?'selected':'')?>>With tax rate wise bifercation</option>
							<option value="state" <?=($view=='state'?'selected':'')?>>With State Wise rate  bifercation</option>	
						</select>
					</div>
				</div>
			</div>
			<div class="col-md-2 col-sm-2 col-xs-6 "> 
				<label class="control-label"> </label>
				<div class="btn-container ">
					<input type="submit" class="form-control btn btn-success" id="generate" name="generate" value="Generate Report" />
				</div>
			</div>
		</div>
		</div>
	</form>
	<!-- <div class="clearfix"><br></div>
	<div class="clearfix"><br></div> -->
	<div id="report">
		<div class="form-group">
			<div class="col-md-11 col-sm-10 col-xs-12" id="report_header">
				<!-- <button type="button" class="btn btn-sm btn-info pull-right" id="btn_print" onclick="javascript:window.print();">Print</button> -->
				<label id="company_name" class="text-center">Primarc Pecan Retail Pvt Ltd</label>
				<label id="account_name" class="text-center">&nbsp;</label>
				<!-- <label id="voucher_type_name" class="text-center">&nbsp;</label>
				<label id="state_name" class="pull-right">&nbsp;</label> -->
				<label class="pull-left"><span id="from"><?php if(isset($from_date)) echo (($from_date!=null && $from_date!='')?$from_date:''); ?></span></label>
				<label class="pull-right"><span id="to"><?php if(isset($to_date)) echo (($to_date!=null && $to_date!='')?$to_date:'');  ?></span></label>
			</div>
		</div>

		<div class="row">
			<div class="col-md-11 col-sm-10 col-xs-12">
				<div  id="loader" ></div>
				<div class="loading">
					<div class="form-group">
						<div class="col-md-12 " > 
							<?php 
								if(isset($table))
								{ ?>
							<table id="example" class="table table-bordered display ">
								<?php 
								if(isset($table))
								{
									echo $table;
								}
								

								?>
							</table>
							<?php } ?>
						</div>
					</div>
				</div>
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
    // $this->registerJsFile(
    //     '@web/js/datatable.js',
    //     ['depends' => [\yii\web\JqueryAsset::className()]]
    // );
    $this->registerJsFile(
        '@web/js/purchase_registration.js',
        ['depends' => [\yii\web\JqueryAsset::className()]]
    );
?>

<script type="text/javascript" src="//code.jquery.com/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js"></script> 
<script type="text/javascript">
$("#vouchertype").select2();
$("#account").select2();
$("#state").select2();
</script>
