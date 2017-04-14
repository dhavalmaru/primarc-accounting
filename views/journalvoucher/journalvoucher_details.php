<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\jui\Autocomplete;

use yii\jui\DatePicker;
use yii\web\JsExpression;
use yii\db\Query;

// use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel app\models\GrnSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Journal Voucher';
$this->params['breadcrumbs'][] = $this->title;
$mycomponent = Yii::$app->mycomponent;
?>
<style type="text/css">
input:-webkit-autofill {
    background-color: white !important;
}
select{
	width: 100%;
}
.form-devident { margin-top: 10px; }
.form-horizontal .control-label {font-size: 12px; letter-spacing: .5px; margin-top:5px; }
.form-devident { margin-top: 10px; }
.table-hover>tbody>tr:hover {
    background:none!important;
}
table tr td { border: 1px solid #eee!important; }
</style>

<div class="grn-index"> 
	<div class=" col-md-12 ">  
		<form id="journal_voucher" class="form-horizontal" action="<?php echo Url::base(); ?>index.php?r=journalvoucher%2Fsave" method="post" enctype="multipart/form-data"> 
			<input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />

			<div class="form-devident">
				<div class=" ">
					<div class=" ">
						<input type="hidden" name="id" value="<?php if(isset($data)) echo $data[0]['id']; ?>" />
						<table class="table table-bordered" id="journal_voucher_details">
							<thead>
								<tr>
									<th width="55" style="text-align: center;">Sr. No.</th>
									<th width="200">Account</th>
									<th width="150">Account Code</th>
									<th width="150">Transaction</th>
									<th width="150">Debit Amt</th>
									<th width="150">Credit Amt</th>
									<th width="50">Delete</th>
								</tr>
							</thead>
							<tbody>
								<?php $blFlag = false;
								if(isset($jv_entries)) { 
									if(count($jv_entries)>0) { $blFlag = true;
										for($i=0; $i<count($jv_entries); $i++) { ?>
										<tr id="row_<?php echo $i; ?>">
											<td  style="text-align: center;" id="sr_no_<?php echo $i; ?>"><?php echo $i+1; ?></td>
											<td>
												<input class="form-control" type="hidden" name="entry_id[]" id="entry_id_<?php echo $i; ?>" value="<?php if(isset($jv_entries)) echo $jv_entries[$i]['id']; ?>" />
												<select class="form-control" name="acc_id[]" id="acc_id_<?php echo $i; ?>" onchange="get_acc_details(this);">
													<option value="">Select</option>
													<?php for($j=0; $j<count($acc_details); $j++) { ?>
													<option value="<?php echo $acc_details[$j]['id']; ?>" <?php if($jv_entries[$i]['account_id']==$acc_details[$j]['id']) echo 'selected'; ?>><?php echo $acc_details[$j]['legal_name']; ?></option>
													<?php } ?>
												</select>
												<input class="form-control" type="hidden" name="legal_name[]" id="legal_name_<?php echo $i; ?>" value="<?php echo $jv_entries[$i]['account_name']; ?>" />
											</td>
											<td><input class="form-control" type="text" name="acc_code[]" id="acc_code_<?php echo $i; ?>" value="<?php echo $jv_entries[$i]['account_code']; ?>" readonly /></td>
											<td>
												<select class="form-control" name="transaction[]" id="trans_<?php echo $i; ?>" onchange="set_transaction(this);">
													<option value="">Select</option>
													<option value="Debit" <?php if($jv_entries[$i]['transaction']=="Debit") echo 'selected'; ?>>Debit</option>
													<option value="Credit" <?php if($jv_entries[$i]['transaction']=="Credit") echo 'selected'; ?>>Credit</option>
												</select>
											</td>
											<td><input class="form-control" type="text" name="debit_amt[]" id="debit_amt_<?php echo $i; ?>" value="<?php echo $mycomponent->format_money($jv_entries[$i]['debit_amt'],2); ?>" onChange="get_total();" /></td>
											<td><input type="text" name="credit_amt[]" id="credit_amt_<?php echo $i; ?>" value="<?php echo $mycomponent->format_money($jv_entries[$i]['credit_amt'],2); ?>" onChange="get_total();" /></td>
											<td style="text-align: center;"><button type="button" class="btn btn-sm btn-success" id="delete_row_<?php echo $i; ?>" onClick="delete_row(this);">-</button></td>
										</tr>
								<?php }}} if($blFlag == false) { ?>
										<tr id="row_0">
											<td   style="text-align: center;" id="sr_no_0">1</td>
											<td>
												<input class="form-control" type="hidden" name="entry_id[]" id="entry_id_0" value="" />
												<select class="form-control" name="acc_id[]" id="acc_id_0" onchange="get_acc_details(this);">
													<option value="">Select</option>
													<?php for($j=0; $j<count($acc_details); $j++) { ?>
													<option value="<?php echo $acc_details[$j]['id']; ?>"><?php echo $acc_details[$j]['legal_name']; ?></option>
													<?php } ?>
												</select>
												<input class="form-control" type="hidden" name="legal_name[]" id="legal_name_0" value="" />
											</td>
											<td><input class="form-control" type="text" name="acc_code[]" id="acc_code_0" value="" readonly /></td>
											<td>
												<select class="form-control" name="transaction[]" id="trans_0" onchange="set_transaction(this);">
													<option value="">Select</option>
													<option value="Debit">Debit</option>
													<option value="Credit">Credit</option>
												</select>
											</td>
											<td><input class="form-control" type="text" name="debit_amt[]" id="debit_amt_0" value="" onChange="get_total();" /></td>
											<td><input class="form-control" type="text" class="form-control" name="credit_amt[]" id="credit_amt_0" value="" onChange="get_total();" /></td>
											<td style="text-align: center;"><button type="button" class="btn btn-sm btn-success" id="delete_row_0" onClick="delete_row(this);">-</button></td>
										</tr>
								<?php } ?>
							</tbody>
							<tfoot>
								<tr>
									<th style="text-align: center;"><button type="button" class="btn btn-sm btn-success" id="repeat_row">+</button></th>
									<th colspan="3">Total</th>
									<th><input class="form-control" type="text" id="total_debit_amt" name="total_debit_amt" value="<?php if(isset($data)) echo $data[0]['debit_amt']; ?>" readonly /></th>
									<th><input class="form-control" type="text" id="total_credit_amt" name="total_credit_amt" value="<?php if(isset($data)) echo $data[0]['credit_amt']; ?>" readonly /></th>
								</tr>
								<tr>
									<th></th>
									<th colspan="4">Difference</th>
									<th><input class="form-control" type="text" id="diff_amt" name="diff_amt" value="<?php if(isset($data)) echo $data[0]['diff_amt']; ?>" readonly /></th>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>

			<div class="form-group"  >
				<div class="row ">
					<div class=" col-md-3 col-sm-3 col-xs-6">
						<label class="control-label">Reference</label>
						<div class=" ">
							<div class=" ">  
								<input name="reference" class="form-control" type="text" value="<?php if(isset($data)) echo $data[0]['reference']; ?>" />
							</div>
						</div>
					</div>

					<div class="col-md-3 col-sm-3 col-xs-6">
						<label class="control-label">Narration</label>
						<div class="">
							<div class="">  
								<input name="narration" class="form-control" type="text" value="<?php if(isset($data)) echo $data[0]['narration']; ?>" />
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="form-group" id="repeat_attachment">
				<div class="row ">
					<div class="col-md-3 col-sm-3 col-xs-6">
						<label class="control-label">Attachment</label>
						<div class="">
							<div class="">  
								<div class=" " >
									<input type="hidden" class="form-control" name="doc_name" value="<?php if(isset($data)) echo $data[0]['doc_name']; ?>" />
									<input type="hidden" class="form-control" name="doc_path" value="<?php if(isset($data)) echo $data[0]['doc_path']; ?>" />
									<input  style="padding:1px;" type="file" class="fileinput form-control     doc_file" name="doc_file" id="doc_file" data-error="#doc_file_error"/>
									<!-- <input type="file" accept="image/*;capture=camera" class="fileinput btn btn-info btn-small doc_file" name="doc_file" id="doc_file" data-error="#doc_file_error"/> -->
									<div id="doc_file_error"></div>
								</div>          
								<div class="col-md-1 col-sm-1 col-xs-12 download-width" >
									<?php if(isset($data)) { if($data[0]['doc_path']!= '') { ?><a target="_blank" id="doc_file_download" href="<?php if(isset($data)) echo base_url().$data[0]['doc_path']; ?>">
									<span class="fa download fa-download" ></span></a></a><?php }} ?>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-3 col-sm-3 col-xs-6">
						<label class="control-label">Description</label>
						<div class="">
							<div class="">  
								<input name="description" class="form-control" type="text" value="<?php //if(isset($data)) echo $data[0]['description']; ?>" />
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="  btn-container"> 
				<div class=" ">
					<button type="submit" class="btn btn-success btn-sm" >Submit For Approval</button>
					<a href="<?php echo Url::base(); ?>index.php?r=journalvoucher%2Findex" class="btn btn-danger btn-sm" >Cancel</a>
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
        '@web/js/journal_voucher.js',
        ['depends' => [\yii\web\JqueryAsset::className()]]
    );
    // $this->registerJsFile(
    //     '@web/js/datatable.js',
    //     ['depends' => [\yii\web\JqueryAsset::className()]]
    // );
?>