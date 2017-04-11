<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\GrnSearch */
/* @var $debitProvider yii\data\ActiveDataProvider */

if($transaction == "Create") {
	$this->title = 'Create Debit Credit Note';
} else {
	$this->title = 'Update Debit Credit Note: ' . $debit['trans_id'];
}

// $this->title = 'Debit Credit Note Details';
// $this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = ['label' => 'Debit Credit Note', 'url' => ['index']];
// $this->params['breadcrumbs'][] = ['label' => $grn_details[0]['grn_id'], 'url' => ['update', 'id' => $grn_details[0]['grn_id']]];
$this->params['breadcrumbs'][] = $transaction;
?>

<div class="grn-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<div class=" col-md-10   col-sm-9 main-content ">

		<section class="row  ">	

			<div class="main-wrapper">
				<div class="col-md-12 ">
					<div class=" col-md-12  media-clmn">  
						<form id="debit_credit_note" class="form-horizontal" action="<?php echo Url::base(); ?>index.php?r=debitcreditnote%2Fsave" method="post"> 
							<input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
							<!-- Text input-->
							<div class="col-md-6 col-sm-12 col-xs-12">
								<h4 class="text-center">Debit</h4>
								<div class="form-devident">
									<div class="form-group col-md-6 col-sm-12 col-xs-12">
										<label class="control-label">Transaction</label>
										<div class="inputGroupContainer">
											<div class="input-group"> 
												<input type="hidden" name="id[]" value="<?php if(isset($debit)) echo $debit['id']; ?>" />
												<input type="hidden" name="trans_id[]" value="<?php if(isset($debit)) echo $debit['trans_id']; ?>" />
												<input type="hidden" name="type[]" value="Debit" />
												<select id="debit_transaction" class="form-control transaction" name="transaction[]">
													<option value="">Select</option>
													<option value="Purchase" <?php if(isset($debit)) { if($debit['transaction']=="Purchase") echo "selected"; } ?>>Purchase</option>
													<option value="Sale" <?php if(isset($debit)) { if($debit['transaction']=="Sale") echo "selected"; } ?>>Sale</option>
													<option value="Promotion" <?php if(isset($debit)) { if($debit['transaction']=="Promotion") echo "selected"; } ?>>Promotion</option>
													<option value="Discount" <?php if(isset($debit)) { if($debit['transaction']=="Discount") echo "selected"; } ?>>Discount</option>
													<option value="Others" <?php if(isset($debit)) { if($debit['transaction']=="Others") echo "selected"; } ?>>Others</option>
												</select>
											</div>
										</div>
									</div>
									<div class="form-group col-md-6 col-sm-12 col-xs-12">
										<label class="control-label">Account Code</label>
										<div class="inputGroupContainer">
											<div class="input-group">  
												<input id="debit_acc_code" name="acc_code[]" class="form-control" type="text" value="<?php if(isset($debit)) echo $debit['acc_code']; ?>" readonly />
											</div>
										</div>
									</div>
								</div>

								<div class="form-devident">
									<div class="form-group col-md-6 col-sm-12 col-xs-12">
										<label class="control-label">Due Date</label>
										<div class="inputGroupContainer">
											<div class="input-group">
												<input name="due_date[]" class="form-control datepicker1" type="text" value="<?php if(isset($debit)) echo $debit['due_date']; ?>" />
											</div>
										</div>
									</div>
									<div class="form-group col-md-6 col-sm-12 col-xs-12">
										<label class="control-label">Amount</label>
										<div class="inputGroupContainer">
											<div class="input-group">  
												<input name="amount[]" class="form-control" type="text" value="<?php if(isset($debit)) echo $debit['amount']; ?>" />
											</div>
										</div>
									</div>
								</div>

								<div class="form-devident">
									<div class="form-group col-md-6 col-sm-12 col-xs-12">
										<label class="control-label">Ref No</label>
										<div class="inputGroupContainer">
											<div class="input-group">  
												<input name="ref_no[]" class="form-control" type="text" value="<?php if(isset($debit)) echo $debit['ref_no']; ?>" />
											</div>
										</div>
									</div>
									<div class="form-group col-md-6 col-sm-12 col-xs-12">
										<label class="control-label">Other Ref No</label>
										<div class="inputGroupContainer">
											<div class="input-group">  
												<input name="other_ref_no[]" class="form-control" type="text" value="<?php if(isset($debit)) echo $debit['other_ref_no']; ?>" />
											</div>
										</div>
									</div>
								</div>

								<div class="form-devident">
									<div class="form-group col-md-12 col-sm-12 col-xs-12">
										<label class="control-label">Narration</label>
										<div class="inputGroupContainer">
											<div class="input-group">  
												<input name="narration[]" class="form-control" type="text" value="<?php if(isset($debit)) echo $debit['narration']; ?>" />
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="col-md-6 col-sm-12 col-xs-12">
								<h4 class="text-center">Credit</h4>
								<div class="form-devident">
									<div class="form-group col-md-6 col-sm-12 col-xs-12">
										<label class="control-label">Transaction</label>
										<div class="inputGroupContainer">
											<div class="input-group"> 
												<input type="hidden" name="id[]" value="<?php if(isset($credit)) echo $credit['id']; ?>" />
												<input type="hidden" name="trans_id[]" value="<?php if(isset($credit)) echo $credit['trans_id']; ?>" />
												<input type="hidden" name="type[]" value="Credit" />
												<select id="credit_transaction" class="form-control transaction" name="transaction[]">
													<option value="">Select</option>
													<option value="Purchase" <?php if(isset($credit)) { if($credit['transaction']=="Purchase") echo "selected"; } ?>>Purchase</option>
													<option value="Sale" <?php if(isset($credit)) { if($credit['transaction']=="Sale") echo "selected"; } ?>>Sale</option>
													<option value="Promotion" <?php if(isset($credit)) { if($credit['transaction']=="Promotion") echo "selected"; } ?>>Promotion</option>
													<option value="Discount" <?php if(isset($credit)) { if($credit['transaction']=="Discount") echo "selected"; } ?>>Discount</option>
													<option value="Others" <?php if(isset($credit)) { if($credit['transaction']=="Others") echo "selected"; } ?>>Others</option>
												</select>
											</div>
										</div>
									</div>
									<div class="form-group col-md-6 col-sm-12 col-xs-12">
										<label class="control-label">Account Code</label>
										<div class="inputGroupContainer">
											<div class="input-group">  
												<input id="credit_acc_code" name="acc_code[]" class="form-control" type="text" value="<?php if(isset($credit)) echo $credit['acc_code']; ?>" readonly />
											</div>
										</div>
									</div>
								</div>

								<div class="form-devident">
									<div class="form-group col-md-6 col-sm-12 col-xs-12">
										<label class="control-label">Due Date</label>
										<div class="inputGroupContainer">
											<div class="input-group">  
												<input name="due_date[]" class="form-control datepicker1" type="text" value="<?php if(isset($credit)) echo $credit['due_date']; ?>" />
											</div>
										</div>
									</div>
									<div class="form-group col-md-6 col-sm-12 col-xs-12">
										<label class="control-label">Amount</label>
										<div class="inputGroupContainer">
											<div class="input-group">  
												<input name="amount[]" class="form-control" type="text" value="<?php if(isset($credit)) echo $credit['amount']; ?>" />
											</div>
										</div>
									</div>
								</div>

								<div class="form-devident">
									<div class="form-group col-md-6 col-sm-12 col-xs-12">
										<label class="control-label">Ref No</label>
										<div class="inputGroupContainer">
											<div class="input-group">  
												<input name="ref_no[]" class="form-control" type="text" value="<?php if(isset($credit)) echo $credit['ref_no']; ?>" />
											</div>
										</div>
									</div>
									<div class="form-group col-md-6 col-sm-12 col-xs-12">
										<label class="control-label">Other Ref No</label>
										<div class="inputGroupContainer">
											<div class="input-group">  
												<input name="other_ref_no[]" class="form-control" type="text" value="<?php if(isset($credit)) echo $credit['other_ref_no']; ?>" />
											</div>
										</div>
									</div>
								</div>

								<div class="form-devident">
									<div class="form-group col-md-12 col-sm-12 col-xs-12">
										<label class="control-label">Narration</label>
										<div class="inputGroupContainer">
											<div class="input-group">  
												<input name="narration[]" class="form-control" type="text" value="<?php if(isset($credit)) echo $credit['narration']; ?>" />
											</div>
										</div>
									</div>
								</div>
							</div>

							<!-- Button -->
							<div class="form-group btn-container"> 
								<div class="col-md-12">
									<button type="submit" class="btn btn-success btn-sm" >Submit For Approval  </button>
									<a href="<?php echo Url::base(); ?>index.php?r=debitcreditnote%2Findex" class="btn btn-danger btn-sm" >Cancel</a>
									<!-- <button type="submit" class="btn btn-danger btn-sm" >Cancel </button> -->
								</div>
							</div>
						</form>
					</div>
				</div> 
			</div >	

		</section>

	</div>

</div>

<script type="text/javascript">
    var BASE_URL="<?php echo Url::base(); ?>";
    // var url = 'url' => ['edit', 'trans_id' => $ledger[0]['trans_id']]
</script>

<?php 
    $this->registerJsFile(
        '@web/js/debit_credit_note.js',
        ['depends' => [\yii\web\JqueryAsset::className()]]
    );
?>