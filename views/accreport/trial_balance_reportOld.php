<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

$this->title = 'Ledger Report';
$this->params['breadcrumbs'][] = ['label' => 'Report', 'url' => ['index']];
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
</style>

<div class="grn-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<div class=" col-md-10   col-sm-9 main-content ">

		<section class="row  ">	

			<div class="main-wrapper">
				<div class="col-md-12 ">
					<div class=" col-md-12  media-clmn">  
						<form id="payment_receipt" class="form-horizontal"> 
							<input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
							<div class="form-devident">
								<div class="form-group col-md-3 col-sm-12 col-xs-12">
									<label class="control-label">Type</label>
									<div class="inputGroupContainer">
										<div class="input-group"> 
											<select class="form-control select2" id="type" name="type">
												<option value="Date Range">Date Range</option>
												<option value="As Of Date">As Of Date</option>
											</select>
										</div>
									</div>
								</div>
								<div class="form-group col-md-3 col-sm-12 col-xs-12" id="date_div">
									<label class="control-label">Date</label>
									<div class="inputGroupContainer">
										<div class="input-group">  
											<input class="form-control datepicker" type="text" id="as_of_date" name="as_of_date" value="" readonly />
										</div>
									</div>
								</div>
							</div>
							<div class="form-devident" id="date_range_div">
								<div class="form-group col-md-3 col-sm-12 col-xs-12">
									<label class="control-label">Transaction Type</label>
									<div class="inputGroupContainer">
										<div class="input-group"> 
											<select class="form-control" id="date_criteria" name="date_criteria">
												<option value="By Date">By Date</option>
												<option value="Financial Year">Financial Year</option>
											</select>
										</div>
									</div>
								</div>
								<div class="form-group col-md-3 col-sm-12 col-xs-12">
									<label class="control-label">From Date</label>
									<div class="inputGroupContainer">
										<div class="input-group">  
											<input class="form-control datepicker" type="text" id="from_date" name="from_date" value="" readonly />
										</div>
									</div>
								</div>
								<div class="form-group col-md-3 col-sm-12 col-xs-12">
									<label class="control-label">To Date</label>
									<div class="inputGroupContainer">
										<div class="input-group">  
											<input class="form-control datepicker" type="text" id="to_date" name="to_date" value="" readonly />
										</div>
									</div>
								</div>
							</div>

							<div class="form-devident">
								<div class="form-group col-md-2 col-sm-12 col-xs-12">
									<label class="control-label">Business Category</label>
									<div class="inputGroupContainer">
										<div class="input-group">  
											<input type="checkbox" class="form-control" id="business_category" name="business_category" />
										</div>
									</div>
								</div>
								<div class="form-group col-md-2 col-sm-12 col-xs-12">
									<label class="control-label">Accounts Category</label>
									<div class="inputGroupContainer">
										<div class="input-group">  
											<input type="checkbox" class="form-control" id="accounts_category" name="accounts_category" />
										</div>
									</div>
								</div>
								<div class="form-group col-md-2 col-sm-12 col-xs-12">
									<label class="control-label">Display Zero Balance Accounts</label>
									<div class="inputGroupContainer">
										<div class="input-group">  
											<input type="checkbox" class="form-control" id="zero_balance_category" name="zero_balance_category" />
										</div>
									</div>
								</div>
							</div>

							<div class="form-devident">
								<br/>
								<div class="form-group col-md-4 col-sm-12 col-xs-12">
									<input type="button" class="form-control btn btn-success" id="generate" name="generate" value="Generate Report" />
								</div>
							</div>

							<div class="form-devident">
								<div class="form-group col-md-4 col-sm-12 col-xs-12">
									<h3>Output</h3>
								</div>
							</div>

							<div class="form-devident">
								<div class="form-group col-md-4 col-sm-12 col-xs-12">
									<label class="control-label">Company Name</label>
									<div class="inputGroupContainer">
										<div class="input-group">  
											<input type="text" class="form-control" id="company_name" name="company_name" value="" readonly />
										</div>
									</div>
								</div>
								<div class="form-group col-md-4 col-sm-12 col-xs-12">
									<label class="control-label">Account Name</label>
									<div class="inputGroupContainer">
										<div class="input-group">  
											<input type="text" class="form-control" id="account_name" name="account_name" readonly />
										</div>
									</div>
								</div>
							</div>

							<div class="form-devident">
								<div class="form-group col-md-4 col-sm-12 col-xs-12">
									<label class="control-label">From</label>
									<div class="inputGroupContainer">
										<div class="input-group">  
											<input type="text" class="form-control" id="from" name="from" readonly />
										</div>
									</div>
								</div>
								<div class="form-group col-md-4 col-sm-12 col-xs-12">
									<label class="control-label">To</label>
									<div class="inputGroupContainer">
										<div class="input-group">  
											<input type="text" class="form-control" id="to" name="to" readonly />
										</div>
									</div>
								</div>
							</div>

							<div class="form-devident">
								<br/>
								<div class="col-md-12"> 
									<table class="table table-bordered table-hover" id="tab_report">
										<thead>
											<tr>
												<th class="text-center"> Transaction id </th>
												<th class="text-center"> Date </th>
												<th class="text-center"> Ledger Code </th>
												<th class="text-center"> Ledger Name </th>
												<th class="text-center"> Ref ID </th>
												<th class="text-center"> DB/CR </th>
												<th class="text-center"> Debit </th>
												<th class="text-center"> Credit </th>
												<th class="text-center"> Balance </th>
												<th class="text-center"> Dr/Cr </th>
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
				</div> 
			</div>	
		</section>
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