<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

// if($action == "Create") {
// 	$this->title = 'Create Payment Receipt';
// } else {
// 	$this->title = 'Update Payment Receipt: ' . $data[0]['id'];
// }

$this->title = 'Upload Payment Receipt' . (isset($data[0]['id'])?': '.$data[0]['id']:'');

$this->params['breadcrumbs'][] = ['label' => 'Payment Receipt', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Upload';//ucfirst($action);
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
	.select2-container--default .select2-selection--multiple .select2-selection__choice {
		background-color: #3c8dbc!important;
	}
</style>

<div class="grn-index">
	<div class=" col-md-12 ">
		<form id="payment_upload" class="form-horizontal" action="<?php echo Url::base(); ?>index.php?r=paymentreceipt%2Fdownloadleadger" method="post" onkeypress="return event.keyCode != 13;"> 
			<input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
			<div class="form-group">
				<div class="col-md-3 col-sm-12 col-xs-12">
					<label class="control-label" id="acc_label" >Account Name</label>
					<div class="">
						<div class="">
							<select class="form-control select2" name="acc_id[]" id="acc_id" multiple="" data-error="#account_name">
								<option value="ALL">ALL</option>
								<?php for($j=0; $j<count($acc_details); $j++) { ?>
								<option value="<?php echo $acc_details[$j]['id']; ?>" <?php if(isset($data[0])) { if($data[0]['account_id']==$acc_details[$j]['id']) echo 'selected'; } ?>><?php echo $acc_details[$j]['legal_name']; ?></option>
								<?php } ?>
							</select>
							<span id="account_name"></span>
							<input class="form-control" type="hidden" name="legal_name" id="legal_name" value="<?php if(isset($data[0])) { echo $data[0]['account_name']; } ?>" />
        				</div>
					</div>
				</div>
				<div class=" col-md-3  col-sm-2 col-xs-6">
					<label class="control-label">To Date</label>
				      <div class=" ">
						<div class=" ">
							<input class="form-control datepicker" type="text" id="to_date" name="to_date" value="<?php if(isset($to_date)) echo (($to_date!=null && $to_date!='')?$to_date:'');  ?>" readonly />
						</div>
					</div>
				</div>
				<div class=" col-md-3  col-sm-2 col-xs-6">
					<label class="control-label"></label>
					<div >
						<input type="submit" class="btn btn-success btn-sm" id="btn_submit" name="generate" value="Generate Report" />
					</div>
					
				</div>
			</div>
		</form>
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
						
					</tbody>
				</table>
			</div>
		</div>
		<div class="btn-container ">
			<div class="col-md-12">
				<button class="btn btn-danger btn-sm" id="btn_submit" name="generate" data-toggle="modal" data-target="#myModal" />Upload Payment</button>
			</div>
		</div>
		
		<?php if(count($payment_upload)>0) { ?>
        <div class="table-container">
        <h4>Payment Upload List</h4>
        <table id="example_payment"  class="table datatable table-bordered display" cellspacing="0" width="100%">
        	<thead> 
        		<tr class="table-head">
	                <th>Sr. No.</th>
	                <th>File name</th>
	                <th>Date of file upload</th>
	                <th>Download error file</th>
	                <th>Status</th>
	                <th>User</th>
	                <th>Bank / Cash Ledger</th>
	                <th>Final Amount</th>
	                <th>Type (Receipt / Payment)</th>
           		</tr>
            </thead>
            <?php 

            $b_url =  Url::base();

            for ($i=0; $i <count($payment_upload) ; $i++) { 
            	echo "<tr>
            			<td>".($i+1)."</td>
	            		<td> <a href='".$b_url."/uploads/payment_file/".$payment_upload[$i]['uploaded_file']."' download>".$payment_upload[$i]['uploaded_file']."</a></td>
	            		<td>".date("d-m-Y",strtotime($payment_upload[$i]['date_of_upload']))."</td>
	            		<td><a href='".$b_url."/uploads/payment_file/".$payment_upload[$i]['error_file']."' download>".$payment_upload[$i]['error_file']."</a></td>
	            		<td>".$payment_upload[$i]['status']."</td>
	            		<td>".$payment_upload[$i]['username']."</td>
	            		<td>".$payment_upload[$i]['bank_cash_ledger']."</td>
	            		<td>".$payment_upload[$i]['final_amount']."</td>
	            		<td>".$payment_upload[$i]['payment_receipt']."</td>
	            	</tr>";
            }
            ?> 
        </table>
        </div>
        <?php } ?>
	</div>
</div> 

<div class="modal fade" id="myModal" role="dialog" style="">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content" style="">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">
                           Upload Excel
                        </h4>
                    </div>
                    <form method="POST" action="<?php echo Url::base(); ?>index.php?r=paymentreceipt%2Fuploadpayment" class="form-horizontal excelform" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="form-group">
                          <label class="col-md-4 col-sm-4 col-xs-12 control-label">Add Excel <span class="asterisk_sign"></span></label>
                          <div class="col-md-4 col-sm-4 col-xs-12">
                            <input type="file" class="form-control" name="payment_file" id="image" placeholder="image" value=""/>
                            <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
                            <input type="hidden" id="company_id" name="company_id" value="<?php if(isset($session['company_id'])) echo $session['company_id']; ?>" />
                          </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                        <input type="submit"  class="btn btn-success pull-right"  value="Save" />
                    </div>
                    </form>
                </div>
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
        '@web/js/datatable.js',
        ['depends' => [\yii\web\JqueryAsset::className()]]
    );
    $this->registerJsFile(
        '@web/js/payment_upload.js',
        ['depends' => [\yii\web\JqueryAsset::className()]]
    );

   
?>