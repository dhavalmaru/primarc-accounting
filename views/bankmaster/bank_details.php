<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\GrnSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Bank Details';
$this->params['breadcrumbs'][] = $this->title;
$mycomponent = Yii::$app->mycomponent;
?>

<div class="grn-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<div class=" col-md-10   col-sm-9 main-content ">

		<section class="row  ">	

			<div class="main-wrapper">
				<div class="col-md-12 ">
					<div class=" col-md-12  media-clmn">  
						<form id="bank_master" class="form-horizontal" action="<?php echo Url::base(); ?>index.php?r=bankmaster%2Fsave" method="post"> 
							<input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
							<!-- Text input-->
							<div class="form-devident">
								<div class="form-group col-md-3 col-sm-3 col-xs-4">
									<label class="control-label">Bank Name</label>
									<div class="inputGroupContainer">
										<div class="input-group"> 
											<input type="hidden" name="id" value="<?php if(isset($data)) echo $data[0]['id']; ?>" />
											<input name="bank_name" class="form-control" type="text" value="<?php if(isset($data)) echo $data[0]['bank_name']; ?>" />
										</div>
									</div>
								</div>
								<div class="form-group col-md-3 col-sm-3 col-xs-4">
									<label class="control-label">Branch</label>
									<div class="inputGroupContainer">
										<div class="input-group">  
											<input name="branch" class="form-control" type="text" value="<?php if(isset($data)) echo $data[0]['branch']; ?>" />
										</div>
									</div>
								</div>
								<div class="form-group col-md-3 col-sm-3 col-xs-4">
									<label class="control-label">Address</label>
									<div class="inputGroupContainer">
										<div class="input-group">  
											<input name="address" class="form-control" type="text" value="<?php if(isset($data)) echo $data[0]['address']; ?>" />
										</div>
									</div>
								</div>
								<div class="form-group col-md-3 col-sm-3 col-xs-4">
									<label class="control-label">Landmark</label>
									<div class="inputGroupContainer">
										<div class="input-group">  
											<input name="landmark" class="form-control" type="text" value="<?php if(isset($data)) echo $data[0]['landmark']; ?>" />
										</div>
									</div>
								</div>
							</div>

							<div class="form-devident">
								<div class="form-group col-md-3 col-sm-3 col-xs-4">
									<label class="control-label">City</label>
									<div class="inputGroupContainer">
										<div class="input-group">  
											<input name="city" class="form-control" type="text" value="<?php if(isset($data)) echo $data[0]['city']; ?>" />
										</div>
									</div>
								</div>
								<div class="form-group col-md-3 col-sm-3 col-xs-4">
									<label class="control-label">Pincode</label>
									<div class="inputGroupContainer">
										<div class="input-group">  
											<input name="pincode" class="form-control" type="text" value="<?php if(isset($data)) echo $data[0]['pincode']; ?>" />
										</div>
									</div>
								</div>
								<div class="form-group col-md-3 col-sm-3 col-xs-4">
									<label class="control-label">State</label>
									<div class="inputGroupContainer">
										<div class="input-group">  
											<input name="state" class="form-control" type="text" value="<?php if(isset($data)) echo $data[0]['state']; ?>" />
										</div>
									</div>
								</div>
								<div class="form-group col-md-3 col-sm-3 col-xs-4">
									<label class="control-label">Country</label>
									<div class="inputGroupContainer">
										<div class="input-group">  
											<input name="country" class="form-control" type="text" value="<?php if(isset($data)) echo $data[0]['country']; ?>" />
										</div>
									</div>
								</div>
							</div>

							<div class="form-devident">
								<div class="form-group col-md-3 col-sm-3 col-xs-4">
									<label class="control-label">Acc Type</label>
									<div class="inputGroupContainer">
										<div class="input-group">  
											<select class="form-control" name="acc_type">
												<option value="">Select</option>
												<option value="Credit" <?php if(isset($data)) { if($data[0]['acc_type']=="Credit") echo "selected"; } ?>>Credit</option>
												<option value="Debit" <?php if(isset($data)) { if($data[0]['acc_type']=="Debit") echo "selected"; } ?>>Debit</option>
											</select>
										</div>
									</div>
								</div>
								<div class="form-group col-md-3 col-sm-3 col-xs-4">
									<label class="control-label">Acc No</label>
									<div class="inputGroupContainer">
										<div class="input-group">  
											<input name="acc_no" class="form-control" type="text" value="<?php if(isset($data)) echo $data[0]['acc_no']; ?>" />
										</div>
									</div>
								</div>
								<div class="form-group col-md-3 col-sm-3 col-xs-4">
									<label class="control-label">Ifsc Code</label>
									<div class="inputGroupContainer">
										<div class="input-group">  
											<input name="ifsc_code" class="form-control" type="text" value="<?php if(isset($data)) echo $data[0]['ifsc_code']; ?>" />
										</div>
									</div>
								</div>
								<div class="form-group col-md-3 col-sm-3 col-xs-4">
									<label class="control-label">Customer Id</label>
									<div class="inputGroupContainer">
										<div class="input-group">  
											<input name="customer_id" class="form-control" type="text" value="<?php if(isset($data)) echo $data[0]['customer_id']; ?>" />
										</div>
									</div>
								</div>
							</div>

							<div class="form-devident">
								<div class="form-group col-md-3 col-sm-3 col-xs-4">
									<label class="control-label">Phone No</label>
									<div class="inputGroupContainer">
										<div class="input-group">  
											<input name="phone_no" class="form-control" type="text" value="<?php if(isset($data)) echo $data[0]['phone_no']; ?>" />
										</div>
									</div>
								</div>
								<div class="form-group col-md-3 col-sm-3 col-xs-4">
									<label class="control-label">Relationship Manager</label>
									<div class="inputGroupContainer">
										<div class="input-group">  
											<input name="relationship_manager" class="form-control" type="text" value="<?php if(isset($data)) echo $data[0]['relationship_manager']; ?>" />
										</div>
									</div>
								</div>
								<div class="form-group col-md-3 col-sm-3 col-xs-4">
									<label class="control-label">Opening Balance</label>
									<div class="inputGroupContainer">
										<div class="input-group">  
											<input name="opening_balance" class="form-control" type="text" value="<?php if(isset($data)) echo $mycomponent->format_money($data[0]['opening_balance'],2); ?>" />
										</div>
									</div>
								</div>
								<div class="form-group col-md-3 col-sm-3 col-xs-4">
									<label class="control-label">Balance Ref Date</label>
									<div class="inputGroupContainer">
										<div class="input-group">  
											<input name="balance_ref_date" class="form-control" type="text" value="<?php if(isset($data)) echo $data[0]['balance_ref_date']; ?>" />
										</div>
									</div>
								</div>
							</div>

							<!-- Button -->
							<div class="form-group btn-container"> 
								<div class="col-md-12">
									<button type="submit" class="btn btn-success btn-sm" >Submit For Approval  </button>
									<a href="<?php echo Url::base(); ?>index.php?r=bankmaster%2Findex" class="btn btn-danger btn-sm" >Cancel</a>
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