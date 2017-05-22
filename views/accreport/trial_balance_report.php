<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

$this->title = 'Trial Balance Report';
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
@media print {
	#report_filter, #btn_print {
		display:none;
	}
	@page {size: landscape;}
	.btn-group {display: none;}
}

#example_wrapper .row:first-child .col-md-6:last-child { padding-top: 0px; }
#example_wrapper .row:first-child .col-md-6:last-child .btn-group { margin-right: 0px; margin-top: 0px; }
#example_wrapper .row:nth-child(1) {margin-top: -30px;}
/*#example_wrapper .row:nth-child(2) {margin-top: 20px;}*/

#example2_wrapper .row:first-child .col-md-6:last-child { padding-top: 0px; }
#example2_wrapper .row:first-child .col-md-6:last-child .btn-group { margin-right: 0px; margin-top: 0px; }
#example2_wrapper .row:nth-child(1) {margin-top: -30px;}
/*#example2_wrapper .row:nth-child(2) {margin-top: 20px;}*/
</style>

<div class="grn-index">
	<div class=" col-md-12 ">  
		<form id="payment_receipt" class="form-horizontal"> 
			<input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
			<div class="form-group" id="report_filter1">
				<div class="col-md-2 col-sm-12 col-xs-12">
					<label class="control-label">Type</label>
					<div class="">
						<div class=""> 
							<select class="form-control" id="date_type">
								<option value="As Of Date">As Of Date</option>
								<option value="Date Range">Date Range</option>
							</select>
						</div>
					</div>
				</div>
				<div class="col-md-2 col-sm-12 col-xs-12 as_of_date_div" id="date_div">
					<label class="control-label">Date</label>
					<div class="">
						<div class="">  
							<input class="form-control datepicker" type="text" id="as_of_date" name="as_of_date" value="<?php echo date('d/m/Y'); ?>" readonly />
						</div>
					</div>
				</div>
				<div class=" col-md-2 col-sm-2 col-xs-6 date_range_div" id="tran_type_div" style="display: none;">
					<label class="control-label">Transaction Type</label>
					<div class=" ">
						<div class=" "> 
							<select class="form-control" id="date_criteria" name="date_criteria">
								<option value="By Date">By Date</option>
								<option value="Financial Year">Financial Year</option>
							</select>
						</div>
					</div>
				</div>
				<div class="date_range_div" id="date_range_div" style="display: none;">
					<div class=" col-md-2 col-sm-2 col-xs-6">
						<label class="control-label">From Date</label>
						<div class=" ">
							<div class=" ">
								<input class="form-control datepicker" type="text" id="from_date" name="from_date" value="" readonly />
							</div>
						</div>
					</div>
					<div class=" col-md-2 col-sm-2 col-xs-6">
						<label class="control-label">To Date</label>
						<div class=" ">
							<div class=" ">
								<input class="form-control datepicker" type="text" id="to_date" name="to_date" value="" readonly />
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="form-group" id="report_filter">
				<div class="col-md-2 col-sm-12 col-xs-12">
					<label class="control-label">Business Category</label>
					<div class="">
						<div class="">  
							<input type="checkbox" class="" id="business_category" name="business_category" checked />
						</div>
					</div>
				</div>
				<div class="col-md-2 col-sm-12 col-xs-12">
					<label class="control-label">Accounts Category</label>
					<div class="">
						<div class="">  
							<input type="checkbox" class="" id="accounts_category" name="accounts_category" checked />
						</div>
					</div>
				</div>
				<div class="col-md-3 col-sm-12 col-xs-12">
					<label class="control-label">Display Zero Balance Accounts</label>
					<div class="">
						<div class="">  
							<input type="checkbox" class="" id="zero_balance_category" name="zero_balance_category" />
						</div>
					</div>
				</div>
				<div class="col-md-2 col-sm-2 col-xs-6 "> 
					<label class="control-label"> </label>
					<div class="btn-container ">
						<input type="button" class="form-control btn btn-success" id="generate" name="generate" value="Generate Report" />
					</div>
				</div>
			</div>

			<!-- <div class="form-group">
				<div class=" col-md-12 col-sm-12 col-xs-12">
					<h3>Output</h3>
				</div>
			</div> -->

			<div id="report">
				<div class="form-group">
					<div class="col-md-12 col-sm-12 col-xs-12" id="report_header">
						<!-- <button type="button" class="btn btn-sm btn-info pull-right" id="btn_print" onclick="javascript:window.print();">Print</button> -->
						<label id="company_name" class="text-center"></label>
						<!-- <label class="pull-left date_range_div" style="display: none;"><span id="from"></span></label>
						<label class="pull-right date_range_div" style="display: none;"><span id="to"></span></label>
						<label class="pull-left as_of_date_div"><span id="as_of"></span></label> -->
					</div>
					<!-- <div class="col-md-3 col-sm-3 col-xs-6">
						<label class="control-label">Company Name</label>
						<div class=" ">
							<div class=" ">  
								<input type="text" class="form-control" id="company_name" name="company_name" value="" readonly />
							</div>
						</div>
					</div>
					<div class="col-md-3 col-sm-3 col-xs-6 date_range_div" style="display: none;">
						<label class="control-label">From</label>
						<div class=" ">
							<div class=" ">  
								<input type="text" class="form-control" id="from" name="from" readonly />
							</div>
						</div>
					</div>
				    <div class="col-md-3 col-sm-3 col-xs-6 date_range_div" style="display: none;">
						<label class="control-label">To</label>
						<div class=" ">
							<div class=" ">  
								<input type="text" class="form-control" id="to" name="to" readonly />
							</div>
						</div>
					</div>
				    <div class="col-md-3 col-sm-3 col-xs-6 as_of_date_div">
						<label class="control-label">As Of Date</label>
						<div class=" ">
							<div class=" ">  
								<input type="text" class="form-control" id="as_of" name="as_of" readonly />
							</div>
						</div>
					</div> -->
				</div>

				<div class="form-devident date_range_div" id="date_range_report" style="display: none;">
					<div class="col-md-12"> 
						<table class="table table-bordered table-hover" id="example">
							<!-- <thead>
								<tr class="sticky-row">
									<th class="text-center"> Sr No </th>
									<th class="text-center"> Particulars </th>
									<th class="text-center"> Accounts Level 1 Category </th>
									<th class="text-center"> Account Name </th>
									<th class="text-center" colspan="2"> Opening Balance </th>
									<th class="text-center" colspan="2"> Transaction </th>
									<th class="text-center" colspan="2"> Closing Balance </th>
									<th class="text-center bus_cat"> Business Category </th>
									<th class="text-center acc_cat"> Accounts Level 1 </th>
									<th class="text-center acc_cat"> Accounts Level 2 </th>
									<th class="text-center acc_cat"> Accounts Level 3 </th>
								</tr>
								<tr class="sticky-row">
									<th class="text-center"> </th>
									<th class="text-center"> </th>
									<th class="text-center"> </th>
									<th class="text-center"> </th>
									<th class="text-center"> Debit </th>
									<th class="text-center"> Credit </th>
									<th class="text-center"> Debit </th>
									<th class="text-center"> Credit </th>
									<th class="text-center"> Debit </th>
									<th class="text-center"> Credit </th>
									<th class="text-center bus_cat"> </th>
									<th class="text-center acc_cat"> </th>
									<th class="text-center acc_cat"> </th>
									<th class="text-center acc_cat"> </th>
								</tr>
							</thead>
							<tbody>
								
							</tbody> -->
						</table>
					</div>
				</div>

				<div class="form-devident as_of_date_div" id="as_of_date_report">
					<div class="col-md-12"> 
						<table class="table table-bordered table-hover" id="example2">
							<!-- <thead>
								<tr>
									<th class="text-center"> Sr No </th>
									<th class="text-center"> Particulars </th>
									<th class="text-center"> Accounts Level 1 Category </th>
									<th class="text-center"> Account Name </th>
									<th class="text-center" colspan="2"> Balance </th>
									<th class="text-center bus_cat"> Business Category </th>
									<th class="text-center acc_cat"> Accounts Level 1 </th>
									<th class="text-center acc_cat"> Accounts Level 2 </th>
									<th class="text-center acc_cat"> Accounts Level 3 </th>
								</tr>
								<tr>
									<th class="text-center"> </th>
									<th class="text-center"> </th>
									<th class="text-center"> </th>
									<th class="text-center"> </th>
									<th class="text-center"> Debit </th>
									<th class="text-center"> Credit </th>
									<th class="text-center bus_cat"> </th>
									<th class="text-center acc_cat"> </th>
									<th class="text-center acc_cat"> </th>
									<th class="text-center acc_cat"> </th>
								</tr>
							</thead>
							<tbody>
								
							</tbody> -->
						</table>
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
    $this->registerJsFile(
        '@web/js/trial_balance_report.js',
        ['depends' => [\yii\web\JqueryAsset::className()]]
    );
?>