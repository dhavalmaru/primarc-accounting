<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

$this->title = 'Ledger Report';
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
.form-devident h3 { border-bottom: 1px dashed #ddd; padding-bottom: 10px; }

</style>

<div class="grn-index"> 
 
					<div class=" col-md-12 ">  
						<form id="payment_receipt" class="form-horizontal"> 
							<input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
							<div class="form-group">
								<div class=" col-md-2 col-sm-2 col-xs-6">
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
						  
								<div class=" col-md-3 col-sm-2 col-xs-6">
									<label class="control-label">Account Name</label>
									<div class=" ">
										<div class=" ">
											<select class="form-control" id="account" name="account">
												<option value="">Select</option>
												<?php for($i=0; $i<count($acc_details); $i++) { ?>
													<option value="<?php echo $acc_details[$i]['id']; ?>"><?php echo $acc_details[$i]['legal_name']; ?></option>
												<?php } ?>
											</select>
										</div>
									</div>
								</div>

							<div class=" col-md-1 col-sm-2 col-xs-6">
									<label class="control-label">Narration</label>
									<div class=" ">
										<div class=" ">  
											<input type="checkbox" class=" " id="narration" name="narration" />
										</div>
									</div>
								</div>
							 

							<div class="col-md-2 col-sm-2 col-xs-6 "> 
								<label class="control-label"> </label>
								<div class="btn-container ">
									<input type="button" class="form-control btn btn-success" id="generate" name="generate" value="Generate Report" />
								</div>
							</div>

							<div class="form-devident">
								<div class=" col-md-12 col-sm-12 col-xs-12">
									<h3>Output</h3>
								</div>
							</div>

							<div class="form-devident">
								<div class="col-md-3 col-sm-3 col-xs-6">
									<label class="control-label">Company Name</label>
									<div class=" ">
										<div class=" ">  
											<input type="text" class="form-control" id="company_name" name="company_name" value="" readonly />
										</div>
									</div>
								</div>
								<div class="col-md-3 col-sm-3 col-xs-6">
									<label class="control-label">Account Name</label>
									<div class=" ">
										<div class="input-group">  
											<input type="text" class="form-control" id="account_name" name="account_name" readonly />
										</div>
									</div>
								</div>
							 
								<div class="col-md-3 col-sm-3 col-xs-6">
									<label class="control-label">From</label>
									<div class=" ">
										<div class=" ">  
											<input type="text" class="form-control" id="from" name="from" readonly />
										</div>
									</div>
								</div>
							    <div class="col-md-3 col-sm-3 col-xs-6">
									<label class="control-label">To</label>
									<div class=" ">
										<div class=" ">  
											<input type="text" class="form-control" id="to" name="to" readonly />
										</div>
									</div>
								</div>
							</div>

<br clear="all"/>
							<div class="form-devident">
							
								<div class="col-md-12"> 
									<table class="table table-bordered table-hover" id="tab_report">
										<thead>
											<tr>
												<th class="text-center"> Sr No </th>
												<th class="text-center"> Ref ID (Voucher No) </th>
												<th class="text-center"> Date </th>
												<th class="text-center">  Ledger Code </th>
												<th class="text-center"> Ledger Name </th>
												<th class="text-center"> Ref 1 </th>
												<th class="text-center"> Ref 2 </th>
												<th class="text-center" style="display: none;"> DB/CR </th>
												<th class="text-center"> Debit </th>
												<th class="text-center"> Credit </th>
												<th class="text-center"> Balance </th>
												<th class="text-center"> DB/CR </th>
												<th class="text-center"> Knock Off Ref </th>
												<th class="text-center show_narration"> Narration </th>
											</tr>
										</thead>
										<tbody>
											
										</tbody>
									</table>
								</div>
								<br/>
							</div>
						</form>
					 
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
        '@web/js/ledger_report.js',
        ['depends' => [\yii\web\JqueryAsset::className()]]
    );
?>