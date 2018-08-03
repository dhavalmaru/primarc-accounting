<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

$this->title = 'Vendor Credit/Debit';
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
</style>
<div class="grn-index"> 
	<div class=" col-md-12 ">  
		<form id="ledger_report" class="form-horizontal" action="<?php echo Url::base(); ?>index.php?r=accreport%2Fgetledgertotalreport" method="post" enctype="multipart/form-data" onkeypress="return event.keyCode != 13;"> 
			<input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
			<div class="form-group" id="report_filter">
				<div class=" col-md-2 col-sm-2 col-xs-6">
					<label class="control-label">Transaction Type</label>
					<div class=" ">
						<div class=" "> 
							<select class="form-control select2" id="date_criteria" name="date_criteria">
								<option value="Financial Year">Financial Year</option>
								<option value="By Date">By Date</option>
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
						<label id="account_name" class="text-center">&nbsp;</label><br>
						<label class="pull-left"><span id="from"><?php if(isset($from_date)) echo (($from_date!=null && $from_date!='')?'From: ' . date('d/m/Y',strtotime($from_date)):date('d/m/Y')); else echo date('d/m/Y'); ?></span></label>
						<label class="pull-right"><span id="to"><?php if(isset($to_date)) echo (($to_date!=null && $to_date!='')?'To: ' . date('d/m/Y',strtotime($to_date)):date('d/m/Y')); else echo date('d/m/Y'); ?></span></label>
					</div>
				</div>

				<div  id="loader" > </div>
				<div class="loading">
					<div class="form-group">
						<div class="col-md-12"> 
							<table id="example" class="table table-bordered display">
								<thead>
									<tr>
										<th class="text-center"> Sr No </th>
										<th class="text-center">Account Name</th>
										<th class="text-center"> Debit </th>
										<th class="text-center"> Credit </th>
										<th class="text-center"> Balance </th>
										<th class="text-center"> DB/CR </th>
									</tr>
								</thead>
								<tbody>
							        <?php
								        $balance = 0;
								        $debit_amt = 0;
								        $credit_amt = 0;
								        $cur_total = 0;
								        
								        if(isset($data)){
									        if(count($data)>0){
									            for($i=0; $i<count($data); $i++){
									                $ledger_code = '';
									                $ledger_name = '';

									                $credit_amt = floatval($data[$i]['debit_amount']);
									                $debit_amt = floatval($data[$i]['credit_amount']);
									                $balance_amt = round($credit_amt-$debit_amt,2);

									                if($balance_amt<0){
									                    $balance_type = 'Dr';
									                    $balance_val = $balance_amt * -1;
									                } else {
									                    $balance_type = 'Cr';
									                    $balance_val = $balance_amt;
									                }
									               

									                echo '<tr>
						                                    <td>'.($i+1).'</td>
						                                    <td>'.$data[$i]['ledger_name'].'</td>
						                                    <td style="text-align:right;">'.$mycomponent->format_money($debit_amt,2).'</td>
						                                    <td style="text-align:right;">'.$mycomponent->format_money($credit_amt,2).'</td>
						                                    <td style="text-align:right;">'.$mycomponent->format_money($balance_val,2).'</td>
						                                    <td>'.$balance_type.'</td>
						                                   
					                                  	</tr>';
									            }
									        }
									    }

								        if($balance<0){
								            $balance_type = 'Dr';
								            $balance_val = $balance * -1;
								        } else {
								            $balance_type = 'Cr';
								            $balance_val = $balance;
								        }

								        if($cur_total<0){
								            $cur_total_type = 'Dr';
								            $cur_total_val = $cur_total * -1;
								        } else {
								            $cur_total_type = 'Cr';
								            $cur_total_val = $cur_total;
								        }
							        ?>
								</tbody>
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