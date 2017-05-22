<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\GrnSearch */
/* @var $data[0]Provider yii\data\ActiveDataProvider */

if($transaction == "Create") {
	$this->title = 'Create Payment Receipt';
} else {
	$this->title = 'Update Payment Receipt: ' . $data[0]['id'];
}

// $this->title = 'Payment Receipt Details';
// $this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = ['label' => 'Payment Receipt', 'url' => ['index']];
// $this->params['breadcrumbs'][] = ['label' => $grn_details[0]['grn_id'], 'url' => ['update', 'id' => $grn_details[0]['grn_id']]];
$this->params['breadcrumbs'][] = $transaction;
$mycomponent = Yii::$app->mycomponent;
?>

<style>
#payment_receipt .error {color: #dd4b39!important;}
.table-head { font-weight:100;  
    background: #41ace9; 
    color: #fff;
    border-bottom: 1px solid #41ace9;
    background-image: linear-gradient(#54b4eb, #2fa4e7 60%, #1d9ce5);
}
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
.form-devident h4 { border-bottom: 1px dashed #ddd; padding-bottom: 10px; }
</style>

<div class="grn-index">
	<div class=" col-md-12 ">
		<form id="payment_receipt" class="form-horizontal" action="<?php echo Url::base(); ?>index.php?r=paymentreceipt%2Fsave" method="post" onkeypress="return event.keyCode != 13;"> 
			<input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
			<div class="form-group">
				<div class="col-md-3 col-sm-12 col-xs-12">
					<label class="control-label">Transaction Type</label>
					<div class="">
						<input type="hidden" id="id" name="id" value="<?php if(isset($data[0])) echo $data[0]['id']; ?>" />
						<input type="hidden" id="voucher_id" name="voucher_id" value="<?php if(isset($data[0])) echo $data[0]['voucher_id']; ?>" />
						<input type="hidden" id="ledger_type" name="ledger_type" value="<?php if(isset($data[0])) echo $data[0]['ledger_type']; ?>" />
						<select id="trans_type" class="form-control" name="trans_type">
							<option value="">Select</option>
							<option value="Receipt" <?php if(isset($data[0])) { if($data[0]['trans_type']=="Receipt") echo "selected"; } ?>>Receipt</option>
							<option value="Payment" <?php if(isset($data[0])) { if($data[0]['trans_type']=="Payment") echo "selected"; } ?>>Payment</option>
						</select>
					</div>
				</div>
				<div class="col-md-3 col-sm-12 col-xs-12">
					<label class="control-label">Account Name</label>
					<div class="">
						<div class="">
							<select class="form-control" name="acc_id" id="acc_id">
								<option value="">Select</option>
								<?php for($j=0; $j<count($acc_details); $j++) { ?>
								<option value="<?php echo $acc_details[$j]['id']; ?>" <?php if(isset($data[0])) { if($data[0]['account_id']==$acc_details[$j]['id']) echo 'selected'; } ?>><?php echo $acc_details[$j]['legal_name']; ?></option>
								<?php } ?>
							</select>
							<input class="form-control" type="hidden" name="legal_name" id="legal_name" value="<?php if(isset($data[0])) { echo $data[0]['account_name']; } ?>" />
        					
        					<!-- <select id="vendor_id" class="form-control" name="vendor_id">
								<option value="">Select</option>
								<?php //for($i=0; $i<count($vendor); $i++) { ?>
									<option value="<?php //echo $vendor[$i]['id']; ?>" <?php //if(isset($data[0])) { if($data[0]['vendor_id']==$vendor[$i]['id']) echo "selected"; } ?>><?php //echo $vendor[$i]['vendor_name']; ?></option>
								<?php //} ?>
							</select>
							<input id="vendor_name" name="vendor_name" class="form-control" type="hidden" value="<?php //if(isset($data[0])) echo $data[0]['vendor_name']; ?>" /> -->
						</div>
					</div>
				</div>
				<div class="col-md-3 col-sm-12 col-xs-12">
					<label class="control-label">Account Code</label>
					<div class="">
						<div class="">  
							<input id="acc_code" name="acc_code" class="form-control" type="text" value="<?php if(isset($data[0])) echo $data[0]['account_code']; ?>" readonly />
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-3 col-sm-12 col-xs-12">
					<label class="control-label">Bank Name</label>
					<div class="">
						<div class="">
							<select id="bank_id" class="form-control" name="bank_id">
								<option value="">Select</option>
								<?php for($i=0; $i<count($bank); $i++) { ?>
									<option value="<?php echo $bank[$i]['id']; ?>" <?php if(isset($data[0])) { if($data[0]['bank_id']==$bank[$i]['id']) echo "selected"; } ?>><?php echo $bank[$i]['legal_name']; ?></option>
								<?php } ?>
							</select>
							<input id="bank_name" name="bank_name" class="form-control" type="hidden" value="<?php if(isset($data[0])) echo $data[0]['bank_name']; ?>" />
						</div>
					</div>
				</div>
				<div class="col-md-3 col-sm-12 col-xs-12">
					<label class="control-label">Payment Type</label>
					<div class="">
						<div class=""> 
							<select id="payment_type" class="form-control" name="payment_type">
								<option value="">Select</option>
								<option value="Adhoc" <?php if(isset($data[0])) { if($data[0]['payment_type']=="Adhoc") echo "selected"; } ?>>Adhoc</option>
								<option value="Knock off" <?php if(isset($data[0])) { if($data[0]['payment_type']=="Knock off") echo "selected"; } ?>>Knock off</option>
							</select>
						</div>
					</div>
				</div>
				<div class="col-md-3 col-sm-12 col-xs-12">
					<label class="control-label">Payment Date</label>
					<div class="">
						<div class=""> 
							<input class="form-control datepicker" type="text" id="payment_date" name="payment_date" value="<?php if(isset($data)) echo (($data[0]['payment_date']!=null && $data[0]['payment_date']!='')?date('d/m/Y',strtotime($data[0]['payment_date'])):date('d/m/Y')); else echo date('d/m/Y'); ?>" readonly />
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-3 col-sm-12 col-xs-12 ad_hock">
					<label class="control-label">Amount</label>
					<div class="">
						<div class="">  
							<input name="amount" class="form-control" type="text" value="<?php if(isset($data[0])) echo $data[0]['amount']; ?>" />
						</div>
					</div>
				</div>
				<div class="col-md-3 col-sm-12 col-xs-12">
					<label class="control-label">Ref No / Cheque No</label>
					<div class="">
						<div class="">  
							<input name="ref_no" class="form-control" type="text" value="<?php if(isset($data[0])) echo $data[0]['ref_no']; ?>" />
						</div>
					</div>
				</div>
				<div class="col-md-6 col-sm-12 col-xs-12">
					<label class="control-label">Narration</label>
					<div class="">
						<div class="">  
							<input name="narration" class="form-control" type="text" value="<?php if(isset($data[0])) echo $data[0]['narration']; ?>" />
						</div>
					</div>
				</div>
			</div>

			<br/>

			<div id="knock_off">
				<div class="table-container "> 
					<table class="stripe table table-bordered" id="tab_logic">
						<thead>
							<tr >
								<th class="text-center"  width="60"> 
									<div class="  ">
										<input type="checkbox" id="check_all" value="" />
									</div>
								</th> 
								<th class="text-center">  Particular </th>
								<th class="text-center">  Ref No </th>
								<th class="text-center">  GI Date </th>
								<th class="text-center">  Invoice Date </th>
								<th class="text-center">  Due Date </th>
								<th class="text-center" width="120"> Debit </th>
								<th class="text-center" width="120">  Credit </th> 
							</tr>
						</thead>
						<tbody id="ledger_details">
							<?php //for($i=0; $i<count($debit_credit); $i++) { ?>
							<!-- <tr>
								<td class="text-center"> 
									<div class="checkbox"> 
										<input type="checkbox" value="" class="check" id="chk1"> 
									</div> 
								</td>
								<td> To Purchase </td>
								<td class="text-right" > 10,000 </td>
								<td class="text-right" > 0</td> 
							</tr> -->
							<? //} ?>

							<!-- <tr class="bold-text">
								<th  class="text-center" > &nbsp; </th>
								<th class="text-right"  >  Total Amount   </th>
								<th class="text-right" > 10,100 </th>
								<th class="text-right"  > 10,100</th>  
							</tr>
							<tr class="bold-text">
								<th  class="text-center" > &nbsp; </th>
								<th class="text-right"  > Amount paying  </th>
								<th class="text-right dbamountpaying"> 0 </th>
								<th class="text-right cramountpaying"  > 0</th> 
							</tr>
							<tr class="bold-text">
								<th  class="text-center" > &nbsp; </th>
								<th class="text-right"  > Net Total Amount   </th>
								<th class="text-right dbtotamount" > 10,100 </th>
								<th class="text-right crtotamount"  > 10,100</th> 
							</tr> -->
						</tbody>
					</table>
				</div>
			</div>

			<!-- Button -->
			<div class="btn-container "> 
				<div class="col-md-12">
					<button type="submit" class="btn btn-success btn-sm">Submit For Approval</button>
					<a href="<?php echo Url::base(); ?>index.php?r=paymentreceipt%2Findex" class="btn btn-danger btn-sm">Cancel</a>
					<!-- <button type="submit" class="btn btn-danger btn-sm" >Cancel </button> -->
				</div>
			</div>
		</form>

		<?php if(isset($data[0])) { ?>
        <div class="table-container">
        <h4>Payment Advice</h4>
        <table id="debit_note" class="table table-bordered">
            <tr class="table-head">
                <th>Sr. No.</th>
                <th>Account Name</th>
                <th>Bank Name</th>
                <th>Payment Type</th>
                <th>Amount</th>
                <th>View</th>
                <th>Download</th>
                <th>Email</th>
            </tr>
            <tr>
                <td><?php echo 1; ?></td>
                <td><?php echo $data[0]['account_name']; ?></td>
                <td><?php echo $data[0]['bank_name']; ?></td>
                <td><?php echo $data[0]['payment_type']; ?></td>
                <td><?php echo $mycomponent->format_money($data[0]['amount'],2); ?></td>
                <td><a href="<?php echo Url::base(); ?>index.php?r=paymentreceipt%2Fviewpaymentadvice&id=<?php echo $data[0]['id']; ?>" target="_blank">View</a></td>
                <td><a href="<?php echo Url::base(); ?>index.php?r=paymentreceipt%2Fdownload&id=<?php echo $data[0]['id']; ?>" target="_blank">Download</a></td>
                <td><a href="<?php echo Url::base(); ?>index.php?r=paymentreceipt%2Femailpaymentadvice&id=<?php echo $data[0]['id']; ?>">Email</a></td>
            </tr>
        </table>
        </div>
        <?php } ?>
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
        '@web/js/payment_receipt.js',
        ['depends' => [\yii\web\JqueryAsset::className()]]
    );
	// $this->registerJsFile(
	//     '@web/plugins/jQuery/jquery-2.2.3.min.js',
	//     ['depends' => [\yii\web\JqueryAsset::className()]]
	// );
	// $this->registerJsFile(
	//     'https://code.jquery.com/ui/1.11.4/jquery-ui.min.js',
	//     ['depends' => [\yii\web\JqueryAsset::className()]]
	// );
?>