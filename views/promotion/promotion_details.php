<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\jui\Autocomplete;

use yii\jui\DatePicker;
use yii\web\JsExpression;
use yii\db\Query;

$this->title = 'Promotion';
$this->params['breadcrumbs'][] = $this->title;
$mycomponent = Yii::$app->mycomponent;
?>
<style type="text/css">
#promotion .error {color: #dd4b39!important;}
input:-webkit-autofill {
    background-color: white !important;
}
/*select{
	width: 100%;
}*/
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
		<form id="promotion" class="form-horizontal" action="<?php echo Url::base(); ?>index.php?r=promotion%2Fsave" method="post" enctype="multipart/form-data" onkeypress="return event.keyCode != 13;"> 
			<input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />

			<div class="form-group">
				<div class="row ">
					<div class=" col-md-3 col-sm-3 col-xs-6">
						<label class="control-label">Vendor</label>
						<div class=" ">
							<div class=" "> 
								<input type="hidden" id="action" name="action" value="<?php if(isset($action)) echo $action; ?>">
								<input type="hidden" id="id" name="id" value="<?php if(isset($data)) echo $data[0]['id']; ?>" />
								<input type="hidden" id="status" name="status" value="<?php if(isset($data)) echo $data[0]['status']; ?>" />
								<input type="hidden" name="voucher_id" value="<?php if(isset($data)) echo $data[0]['voucher_id']; ?>" />
								<input type="hidden" name="ledger_type" value="<?php if(isset($data)) echo $data[0]['ledger_type']; ?>" />
								<input type="hidden" name="debit_note_ref" value="<?php if(isset($data)) echo $data[0]['debit_note_ref']; ?>" />
								<select id="vendor_id" name="vendor_id" class="form-control" onChange="get_promo_types();">
									<option value="">Select</option>
									<?php if(isset($vendor)) { for($i=0; $i<count($vendor); $i++) { ?>
										<option value="<?php echo $vendor[$i]['id']; ?>" <?php if(isset($data[0])) { if($data[0]['vendor_id']==$vendor[$i]['id']) echo "selected"; } ?>><?php echo $vendor[$i]['vendor_name']; ?></option>
									<?php }} ?>
								</select>
							</div>
						</div>
					</div>
					<div class=" col-md-3 col-sm-3 col-xs-6">
						<label class="control-label">Promotion Type</label>
						<div class=" ">
							<div class=" ">
								<select id="promotion_type" class="form-control" name="promotion_type" onChange="get_promo_codes();">
									<option value="">Select</option>
									<?php if(isset($promotion_type)) { for($i=0; $i<count($promotion_type); $i++) { ?>
										<option value="<?php echo $promotion_type[$i]['promotion_type']; ?>" <?php if(isset($data[0])) { if($data[0]['promotion_type']==$promotion_type[$i]['promotion_type']) echo "selected"; } ?>><?php echo $promotion_type[$i]['promotion_type']; ?></option>
									<?php }} ?>
								</select>
							</div>
						</div>
					</div>
					<div class=" col-md-3 col-sm-3 col-xs-6">
						<label class="control-label">Promotion Code</label>
						<div class=" ">
							<div class=" ">
								<select id="promotion_code" class="form-control" name="promotion_code" multiple>
									<option value="">Select</option>
									<?php if(isset($promotion_code)) { for($i=0; $i<count($promotion_code); $i++) { ?>
										<option value="<?php echo $promotion_code[$i]['promotion_code']; ?>" <?php if(isset($data[0])) { if($data[0]['promotion_code']==$promotion_code[$i]['promotion_code']) echo "selected"; } ?>><?php echo $promotion_code[$i]['promotion_code']; ?></option>
									<?php }} ?>
								</select>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="row ">
					<div class=" col-md-3 col-sm-3 col-xs-6">
						<label class="control-label">Transaction</label>
						<div class=" ">
							<div class=" ">
								<select id="trans_type" class="form-control" name="trans_type" onChange="set_trans_type();">
									<option value="">Select</option>
									<option value="Debit Note" <?php if(isset($data[0])) { if($data[0]['trans_type']=="Debit Note") echo "selected"; } ?>>Debit Note</option>
									<option value="Invoice" <?php if(isset($data[0])) { if($data[0]['trans_type']=="Invoice") echo "selected"; } ?>>Invoice</option>
								</select>
							</div>
						</div>
					</div>
					<div id="warehouse_gst_div" class="col-md-3 col-sm-3 col-xs-6" style="<?php if(isset($data[0])) { if($data[0]['trans_type'] != 'Invoice') echo 'display: none;'; } else { echo 'display: none;'; } ?>">
						<label class="control-label">GSTIN</label>
						<div class=" ">
							<div class=" ">  
								<select id="warehouse_id" name="warehouse_id" class="form-control">
									<option value="">Select</option>
									<?php if(isset($warehouse_gst)) { for($i=0; $i<count($warehouse_gst); $i++) { ?>
										<option value="<?php echo $warehouse_gst[$i]['id']; ?>" <?php if(isset($data[0])) { if($data[0]['warehouse_id']==$warehouse_gst[$i]['id']) echo "selected"; } ?>><?php echo $warehouse_gst[$i]['warehouse_gst']; ?></option>
									<?php }} ?>
								</select>
							</div>
						</div>
					</div>
					<div class="col-md-3 col-sm-3 col-xs-6">
						<label class="control-label">Transaction Date</label>
						<div class="">
							<div class="">  
								<input class="form-control datepicker" type="text" id="date_of_transaction" name="date_of_transaction" value="<?php if(isset($data)) echo (($data[0]['date_of_transaction']!=null && $data[0]['date_of_transaction']!='')?date('d/m/Y',strtotime($data[0]['date_of_transaction'])):date('d/m/Y')); else echo date('d/m/Y'); ?>" readonly /> 
							</div>
						</div>
					</div>
					<div class="col-md-3 col-sm-3 col-xs-6">
						<label class="control-label">&nbsp;</label>
						<div class="">
							<div class="">  
								<input type="button" class="btn btn-success btn-sm" id="btn_get_details" name="btn_get_details" value="Get Details" />
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="form-devident">
				<div class=" ">
					<div class=" ">
						<table class="table table-bordered" id="acc_promotion_details">
							<thead>
								<tr>
									<th width="50" class="action_delete">Action</th>
									<th width="55" style="text-align: center; display: none;">Sr. No.</th>
									<th width="200">Account</th>
									<th width="150">Account Code</th>
									<th width="150">Transaction</th>
									<th width="150">Debit Amt</th>
									<th width="150">Credit Amt</th>
								</tr>
							</thead>
							<tbody>
								<?php $blFlag = false;
								if(isset($promotion_entries)) { 
									if(count($promotion_entries)>0) { $blFlag = true;
										for($i=0; $i<count($promotion_entries); $i++) { ?>
										<tr id="row_<?php echo $i; ?>">
											<td style="text-align: center;" class="action_delete"><button type="button" class="btn btn-sm btn-success" id="delete_row_<?php echo $i; ?>" onClick="delete_row(this);">-</button></td>
											<td  style="text-align: center; display: none;" id="sr_no_<?php echo $i; ?>"><?php echo $i+1; ?></td>
											<td>
												<input class="form-control" type="hidden" name="entry_id[]" id="entry_id_<?php echo $i; ?>" value="<?php if(isset($promotion_entries)) echo $promotion_entries[$i]['id']; ?>" />
												<select class="form-control" name="acc_id[]" id="acc_id_<?php echo $i; ?>" onchange="get_acc_details(this);">
													<option value="">Select</option>
													<?php for($j=0; $j<count($acc_details); $j++) { ?>
													<option value="<?php echo $acc_details[$j]['id']; ?>" <?php if($promotion_entries[$i]['account_id']==$acc_details[$j]['id']) echo 'selected'; ?>><?php echo $acc_details[$j]['legal_name']; ?></option>
													<?php } ?>
												</select>
												<input class="form-control" type="hidden" name="legal_name[]" id="legal_name_<?php echo $i; ?>" value="<?php echo $promotion_entries[$i]['account_name']; ?>" />
											</td>
											<td><input class="form-control" type="text" name="acc_code[]" id="acc_code_<?php echo $i; ?>" value="<?php echo $promotion_entries[$i]['account_code']; ?>" readonly /></td>
											<td>
												<select class="form-control" name="transaction[]" id="trans_<?php echo $i; ?>" onchange="set_transaction(this);">
													<option value="">Select</option>
													<option value="Debit" <?php if($promotion_entries[$i]['transaction']=="Debit") echo 'selected'; ?>>Debit</option>
													<option value="Credit" <?php if($promotion_entries[$i]['transaction']=="Credit") echo 'selected'; ?>>Credit</option>
												</select>
											</td>
											<td><input class="form-control debit_amt" type="text" name="debit_amt[]" id="debit_amt_<?php echo $i; ?>" value="<?php echo $mycomponent->format_money($promotion_entries[$i]['debit_amt'],2); ?>" onChange="get_total();" <?php if($promotion_entries[$i]['transaction']=="Credit") echo 'readonly'; ?> /></td>
											<td><input class="form-control credit_amt" type="text" name="credit_amt[]" id="credit_amt_<?php echo $i; ?>" value="<?php echo $mycomponent->format_money($promotion_entries[$i]['credit_amt'],2); ?>" onChange="get_total();" <?php if($promotion_entries[$i]['transaction']=="Debit") echo 'readonly'; ?> /></td>
										</tr>
								<?php }}} if($blFlag == false) { ?>
										<tr id="row_0">
											<td style="text-align: center;" class="action_delete"><button type="button" class="btn btn-sm btn-success" id="delete_row_0" onClick="delete_row(this);">-</button></td>
											<td   style="text-align: center; display: none;" id="sr_no_0">1</td>
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
											<td><input class="form-control debit_amt" type="text" name="debit_amt[]" id="debit_amt_0" value="" onChange="get_total();" /></td>
											<td><input class="form-control credit_amt" type="text" class="form-control" name="credit_amt[]" id="credit_amt_0" value="" onChange="get_total();" /></td>
										</tr>
										<tr id="row_1">
											<td style="text-align: center;" class="action_delete"><button type="button" class="btn btn-sm btn-success" id="delete_row_1" onClick="delete_row(this);">-</button></td>
											<td   style="text-align: center; display: none;" id="sr_no_1">1</td>
											<td>
												<input class="form-control" type="hidden" name="entry_id[]" id="entry_id_1" value="" />
												<select class="form-control" name="acc_id[]" id="acc_id_1" onchange="get_acc_details(this);">
													<option value="">Select</option>
													<?php for($j=0; $j<count($acc_details); $j++) { ?>
													<option value="<?php echo $acc_details[$j]['id']; ?>"><?php echo $acc_details[$j]['legal_name']; ?></option>
													<?php } ?>
												</select>
												<input class="form-control" type="hidden" name="legal_name[]" id="legal_name_1" value="" />
											</td>
											<td><input class="form-control" type="text" name="acc_code[]" id="acc_code_1" value="" readonly /></td>
											<td>
												<select class="form-control" name="transaction[]" id="trans_1" onchange="set_transaction(this);">
													<option value="">Select</option>
													<option value="Debit">Debit</option>
													<option value="Credit">Credit</option>
												</select>
											</td>
											<td><input class="form-control debit_amt" type="text" name="debit_amt[]" id="debit_amt_1" value="" onChange="get_total();" /></td>
											<td><input class="form-control credit_amt" type="text" class="form-control" name="credit_amt[]" id="credit_amt_1" value="" onChange="get_total();" /></td>
										</tr>
								<?php } ?>
							</tbody>
							<tfoot>
								<tr>
									<th style="text-align: center;" class="action_delete"><button type="button" class="btn btn-sm btn-success" id="repeat_row">+</button></th>
									<th colspan="3">Total</th>
									<th><input class="form-control" type="text" id="total_debit_amt" name="total_debit_amt" value="<?php if(isset($data)) echo $data[0]['debit_amt']; ?>" readonly /></th>
									<th><input class="form-control" type="text" id="total_credit_amt" name="total_credit_amt" value="<?php if(isset($data)) echo $data[0]['credit_amt']; ?>" readonly /></th>
								</tr>
								<tr>
									<th class="action_delete"></th>
									<th colspan="4">Difference</th>
									<th><input class="form-control" type="text" id="diff_amt" name="diff_amt" value="<?php if(isset($data)) echo $data[0]['diff_amt']; ?>" readonly /></th>
								</tr>
							</tfoot>
						</table>
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
					<select id="approver_id" name="approver_id" class="form-control">
						<option value="">Select</option>
						<?php //for($i=0; $i<count($approver_list); $i++) { ?>
							<option value="<?php //echo $approver_list[$i]['id']; ?>" <?php //if(isset($data[0])) { if($data[0]['approver_id']==$approver_list[$i]['id']) echo "selected"; } ?>><?php //echo $approver_list[$i]['username']; ?></option>
						<?php //} ?>
					</select>
				</div> -->
			</div>

			<div class="  btn-container"> 
				<div class=" ">
					<input type="submit" class="btn btn-success btn-sm" id="btn_submit" name="btn_submit" value="Submit" />
					<!-- <input type="submit" class="btn btn-success btn-sm" id="btn_submit" name="btn_submit" value="Submit For Approval" />
					<input type="submit" class="btn btn-danger btn-sm" id="btn_reject" name="btn_reject" value="Reject" /> -->
					<a href="<?php echo Url::base(); ?>index.php?r=promotion%2Findex" class="btn btn-primary btn-sm pull-right" >Cancel</a>
					<!-- <button type="submit" class="btn btn-danger btn-sm" >Cancel </button> -->
				</div>
			</div>
		</form>
	</div>
</div>

<script type="text/javascript">
    var BASE_URL="<?php echo Url::base(); ?>";

    var acc_details = '<option value="">Select</option>' + 
						'<?php for($j=0; $j<count($acc_details); $j++) { ?>' + 
						'<option value="<?php echo $acc_details[$j]['id']; ?>"><?php echo str_replace("'", "\'", $acc_details[$j]['legal_name']); ?></option>' + 
					'<?php } ?>';
</script>

<?php 
	$this->registerJsFile(
	    '@web/js/jquery-ui-1.11.2/jquery-ui.min.js',
	    ['depends' => [\yii\web\JqueryAsset::className()]]
	);
    $this->registerJsFile(
        '@web/js/promotion.js',
        ['depends' => [\yii\web\JqueryAsset::className()]]
    );
    // $this->registerJsFile(
    //     '@web/js/datatable.js',
    //     ['depends' => [\yii\web\JqueryAsset::className()]]
    // );
?>