<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

$this->title = 'Ledger Report';
$mycomponent = Yii::$app->mycomponent;
?>

<style>
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
#report_header label { display: block; }
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
</style>
<div class="grn-index"> 
	<div class=" col-md-12 ">  
		<form id="ledger_report" class="form-horizontal" action="<?php echo Url::base(); ?>index.php?r=accreport%2Fgetdetailledgerreport" method="post" enctype="multipart/form-data" onkeypress="return event.keyCode != 13;"> 
			<input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
			<div class="form-group" id="report_filter">
			
		  
				<div class="col-md-2 col-sm-2 col-xs-6">
					<label class="control-label">Account Name</label>
					<div class=" ">
						<div class=" ">
							<select class="form-control" id="account" name="account[]" multiple="multiple">
								<option value="">Select</option>
								<?php for($i=0; $i<count($acc_details); $i++) { ?>
									<option value="<?php echo $acc_details[$i]['id']; ?>" <?php if(isset($account)) {if($acc_details[$i]['id']==$account) echo 'selected';} ?>><?php echo $acc_details[$i]['legal_name']; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
				</div>

				<div class=" col-md-2 col-sm-2 col-xs-6">
					<label class="control-label">Voucher Type</label>
					<div class=" ">
						<div class=" ">
							<select class="form-control" id="vouchertype" name="vouchertype" multiple="multiple">
								<option value="">Select</option>
								<option value="purchase">Purchase</option>
								<option value="journal_voucher"> Journal Voucher</option>
								<option value="payment_receipt"> Payment/Receipt</option>
								<option value="go_debit_details">Good Debit Details</option>
								<option value="other_debit_credit">Other Debit Details</option>
							</select>
						</div>
					</div>
			   </div>
			 <!-- 	<div class=" col-md-2 col-sm-2 col-xs-6">
					<label class="control-label">Select State</label>
					<div class=" ">
						<div class=" "> 
							<select class="form-control" id="date_criteria" name="date_criteria" multiple="multiple"><option value="">Select</option>
								<?php for($i=0; $i<count($state_detail); $i++) { ?>
									<option value="<?php echo $state_detail[$i]['id']; ?>" >
										<?php echo $state_detail[$i]['state_name']; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
				</div> -->	
				<div class=" col-md-2 col-sm-2 col-xs-6">
					<label class="control-label">Date Type</label>
					<div class=" ">
						<div class=" "> 
							<select class="form-control" id="date_criteria" name="date_criteria">
								<option value="Invoice Date">Invoice Date</option>
								<option value="GRN_Approved">Grn Approved Date</option>
								<option value="GI">GI date</option>
								<option value="Posting">Posting Date</option>
							</select>
						</div>
					</div>
				</div>
			   	<div class=" col-md-2 col-sm-2 col-xs-6">
					<label class="control-label">From Date</label>
					<div class=" ">
						<div class=" ">
							<input class="form-control datepicker" type="text" id="from_date" name="from_date" value="<?php if(isset($from_date)) echo (($from_date!=null && $from_date!='')?date('d/m/Y',strtotime($from_date)):''); ?>" readonly />
						</div>
					</div>
				</div>
				<div class=" col-md-2 col-sm-2 col-xs-6">
					<label class="control-label">To Date</label>
				      <div class=" ">
						<div class=" ">
							<input class="form-control datepicker" type="text" id="to_date" name="to_date" value="<?php if(isset($to_date)) echo (($to_date!=null && $to_date!='')?date('d/m/Y',strtotime($to_date)):''); ?>" readonly />
						</div>
					</div>
				</div>
				<div class=" col-md-2 col-sm-2 col-xs-6">
					<label class="control-label">View</label>
					<div class=" ">
						<div class=" "> 
							<select class="form-control" id="view" name="view">
								<option value="default">Default</option>
								<option value="tax">With tax rate wise bifercation</option>
								<option value="state">With State Wise rate  bifercation</option>	
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

			<div id="report">
				<div class="form-group">
					<div class="col-md-12 col-sm-12 col-xs-12" id="report_header">
						<!-- <button type="button" class="btn btn-sm btn-info pull-right" id="btn_print" onclick="javascript:window.print();">Print</button> -->
						<label id="company_name" class="text-center">Primarc Pecan Retail Pvt Ltd</label>
						<label id="account_name" class="text-center">&nbsp;</label>
						<label class="pull-left"><span id="from"><?php if(isset($from_date)) echo (($from_date!=null && $from_date!='')?'From: ' . date('d/m/Y',strtotime($from_date)):date('d/m/Y')); else echo date('d/m/Y'); ?></span></label>
						<label class="pull-right"><span id="to"><?php if(isset($to_date)) echo (($to_date!=null && $to_date!='')?'To: ' . date('d/m/Y',strtotime($to_date)):date('d/m/Y')); else echo date('d/m/Y'); ?></span></label>
					</div>
				</div>

				<div  id="loader" > </div>
				<div class="loading">
					<div class="form-group">
						<div class="col-md-12"> 
							<table id="example" class="table table-bordered display">
								<?php 
								if(isset($table))
								{
									echo $table;
								}
								

								?>
							</table>
						</div>
					</div>
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
    // $this->registerJsFile(
    //     '@web/js/datatable.js',
    //     ['depends' => [\yii\web\JqueryAsset::className()]]
    // );
    $this->registerJsFile(
        '@web/js/ledger_report.js',
        ['depends' => [\yii\web\JqueryAsset::className()]]
    );
?>
