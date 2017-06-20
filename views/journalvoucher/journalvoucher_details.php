<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\jui\Autocomplete;

use yii\jui\DatePicker;
use yii\web\JsExpression;
use yii\db\Query;

$this->title = 'Journal Voucher';
$this->params['breadcrumbs'][] = $this->title;
$mycomponent = Yii::$app->mycomponent;
?>
<style type="text/css">
#journal_voucher .error {color: #dd4b39!important;}
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
		<form id="journal_voucher" class="form-horizontal" action="<?php echo Url::base(); ?>index.php?r=journalvoucher%2Fsave" method="post" enctype="multipart/form-data" onkeypress="return event.keyCode != 13;"> 
			<input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />

			<div class="form-devident">
				<div class=" ">
					<div class=" ">
						<input type="hidden" id="action" name="action" value="<?php if(isset($action)) echo $action; ?>">
						<input type="hidden" name="id" value="<?php if(isset($data)) echo $data[0]['id']; ?>" />
						<input type="hidden" id="status" name="status" value="<?php if(isset($data)) echo $data[0]['status']; ?>" />
						<input type="hidden" name="voucher_id" value="<?php if(isset($data)) echo $data[0]['voucher_id']; ?>" />
						<input type="hidden" name="ledger_type" value="<?php if(isset($data)) echo $data[0]['ledger_type']; ?>" />
						<table class="table table-bordered" id="acc_jv_details">
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
								if(isset($jv_entries)) { 
									if(count($jv_entries)>0) { $blFlag = true;
										for($i=0; $i<count($jv_entries); $i++) { ?>
										<tr id="row_<?php echo $i; ?>">
											<td style="text-align: center;" class="action_delete"><button type="button" class="btn btn-sm btn-success" id="delete_row_<?php echo $i; ?>" onClick="delete_row(this);">-</button></td>
											<td  style="text-align: center; display: none;" id="sr_no_<?php echo $i; ?>"><?php echo $i+1; ?></td>
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
											<td><input class="form-control" type="text debit_amt" name="debit_amt[]" id="debit_amt_<?php echo $i; ?>" value="<?php echo $mycomponent->format_money($jv_entries[$i]['debit_amt'],2); ?>" onChange="get_total();" <?php if($jv_entries[$i]['transaction']=="Credit") echo 'readonly'; ?> /></td>
											<td><input class="form-control" type="text credit_amt" name="credit_amt[]" id="credit_amt_<?php echo $i; ?>" value="<?php echo $mycomponent->format_money($jv_entries[$i]['credit_amt'],2); ?>" onChange="get_total();" <?php if($jv_entries[$i]['transaction']=="Debit") echo 'readonly'; ?> /></td>
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

			<div class="form-group"  >
				<div class="row ">
					<div class=" col-md-3 col-sm-3 col-xs-6">
						<label class="control-label">JV Date</label>
						<div class=" ">
							<div class=" "> 
								<input class="form-control datepicker" type="text" id="jv_date" name="jv_date" value="<?php if(isset($data)) echo (($data[0]['jv_date']!=null && $data[0]['jv_date']!='')?date('d/m/Y',strtotime($data[0]['jv_date'])):date('d/m/Y')); else echo date('d/m/Y'); ?>" readonly /> 
							</div>
						</div>
					</div>
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

			<!-- <div class="form-group" id="repeat_attachment">
				<div class="row ">
					<div class="col-md-3 col-sm-3 col-xs-6">
						<label class="control-label">Attachment</label>
						<div class="">
							<div class="">  
								<div class=" " >
									<input type="hidden" class="form-control" name="doc_name" value="<?php //if(isset($data)) echo $data[0]['doc_name']; ?>" />
									<input type="hidden" class="form-control" name="doc_path" value="<?php //if(isset($data)) echo $data[0]['doc_path']; ?>" />
									<input  style="padding:1px;" type="file" class="fileinput form-control     doc_file" name="doc_file" id="doc_file" data-error="#doc_file_error"/>
									<div id="doc_file_error"></div>
								</div>          
								<div class="col-md-1 col-sm-1 col-xs-12 download-width" >
									<?php //if(isset($data)) { if($data[0]['doc_path']!= '') { ?>
									<a target="_blank" id="doc_file_download" href="<?php //if(isset($data)) echo base_url().$data[0]['doc_path']; ?>">
									<span class="fa download fa-download" ></span>
									</a>
									<?php //}} ?>
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
			</div> -->

			<div class="form-devident" id="jv_doc_details">
				<div class="form-group col-md-9 col-sm-12 col-xs-12">
					<table class="table table-bordered" id="jv_doc">
                		<thead>
                			<tr>
	                			<th width="55px;" style="text-align: center;" class="action_delete">Action</th>
	                			<th width="500px;">Attachment</th>
	                			<th width="750px;">Description</th>
                			</tr>
                		</thead>
                		<tbody>
                			<?php $blFlag = false; if(isset($jv_docs)) { if(count($jv_docs)>0) { $blFlag = true;
                					for($i=0; $i<count($jv_docs); $i++) { ?>
		                				<tr id="jv_doc_<?php echo $i; ?>">
		                					<td style="text-align: center;" class="action_delete"><button type="button" class="btn btn-sm btn-success" id="delete_jv_doc_<?php echo $i; ?>" onClick="delete_jv_doc(this);">-</button></td>
				                			<td>
				                				<div class="col-md-10 col-sm-10 col-xs-10">
													<input class="form-control" type="hidden" name="jv_doc_id[]" value="<?php if(isset($jv_docs)) echo $jv_docs[$i]['id']; ?>" />
													<input type="hidden" class="form-control" name="doc_path[]" value="<?php if(isset($jv_docs)) echo $jv_docs[$i]['doc_path']; ?>" />
													<input  style="padding:1px;" type="file" class="fileinput form-control doc_file" name="doc_file_<?php echo $i; ?>" id="doc_file_<?php echo $i; ?>" data-error="#doc_file_<?php echo $i; ?>_error"/>
													<div id="doc_file_<?php echo $i; ?>_error"></div>
												</div>          
												<div class="col-md-2 col-sm-2 col-xs-2">
													<?php if(isset($jv_docs)) { if($jv_docs[$i]['doc_path']!= '') { ?>
													<a target="_blank" id="doc_file_<?php echo $i; ?>_download" href="<?php if(isset($jv_docs)) echo Url::base().$jv_docs[$i]['doc_path']; ?>">
														<span class="fa download fa-download" ></span>
													</a>
													<?php }} ?>
												</div>
				                			</td>
				                			<td>
				                				<div class="">  
													<input name="description[]" id="description_<?php echo $i; ?>" class="form-control" type="text" value="<?php if(isset($jv_docs)) echo $jv_docs[$i]['description']; ?>" />
												</div>
			                				</td>
			                			</tr>
                			<?php }}} if($blFlag == false) { ?>
                						<tr id="jv_doc_0">
				                			<td style="text-align: center;" class="action_delete"><button type="button" class="btn btn-sm btn-success" id="delete_jv_doc_0" onClick="delete_jv_doc(this);">-</button></td>
				                			<td>
				                				<div class="col-md-10 col-sm-10 col-xs-10">
													<input class="form-control" type="hidden" name="jv_doc_id[]" value="" />
													<input type="hidden" class="form-control" name="doc_path[]" value="" />
													<input  style="padding:1px;" type="file" class="fileinput form-control doc_file" name="doc_file_0" id="doc_file_0" data-error="#doc_file_0_error"/>
													<div id="doc_file_0_error"></div>
												</div>          
												<div class="col-md-2 col-sm-2 col-xs-2">
												</div>
				                			</td>
				                			<td>
				                				<div class="">  
													<input name="description[]" id="description_0" class="form-control" type="text" value="" />
												</div>
			                				</td>
			                			</tr>
                			<?php } ?>
                		</tbody>
                		<tfoot>
                			<tr>
                				<td style="text-align: center;"><button type="button" class="btn btn-success btn-sm" id="repeat_doc">+</button></td>
                				<td colspan="3"> </td>
                			</tr>
                		</tfoot>
                	</table>
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

			<div class="  btn-container"> 
				<div class=" ">
					<!-- <button type="submit" class="btn btn-success btn-sm" >Submit For Approval</button> -->
					<input type="submit" class="btn btn-success btn-sm" id="btn_submit" name="btn_submit" value="Submit For Approval" />
					<input type="submit" class="btn btn-danger btn-sm" id="btn_reject" name="btn_reject" value="Reject" />
					<a href="<?php echo Url::base(); ?>index.php?r=journalvoucher%2Findex" class="btn btn-primary btn-sm pull-right" >Cancel</a>
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