<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

$this->title = 'Payment Receipt';
$this->params['breadcrumbs'][] = $this->title;
$mycomponent = Yii::$app->mycomponent;
?>

<div class="grn-index"> 
	<div class=" col-md-12">  
		<a href="<?php echo Url::base(); ?>index.php?r=paymentreceipt%2Fcreate"> <button type="button" class="btn btn-grid btn-success btn-sm pull-right">Add New Payment Details </button></a>
		<div class="panel with-nav-tabs panel-primary">
			<div class="panel-heading">
				<ul class="nav nav-tabs">
					<li class="active"><a href="#tab1primary" data-toggle="tab"> Pending (<?php echo count($pending); ?>)</a></li>
					<li><a href="#tab2primary" data-toggle="tab"> Approved (<?php echo count($approved); ?>)</a></li>
					<li><a href="#tab3primary" data-toggle="tab"> Rejected (<?php echo count($rejected); ?>)</a></li>
				</ul>
			</div>
			<div class="panel-body">
				<div  id="loader" > </div>
   				<div class="loading">
					<div class="tab-content">
						<div class="tab-pane fade in active" id="tab1primary">
							<div class="bs-example grn-index" data-example-id="bordered-table"  >  
								<table id="example" class="table datatable table-bordered display" cellspacing="0" width="100%">
									<thead> 
										<tr> 
											<th width="45">Sr. No.</th> 
											<th>Action</th> 
											<th>P_Id</th> 
											<th>Transaction Type</th> 
											<th>Account Name</th> 
											<th>Bank Name</th> 
											<th>Payment Type</th> 
											<th>Payment Date</th> 
											<th>Amount</th> 
											<th>Status</th> 
											<th>Updated By</th> 
											<th style="display: none;">Approved By</th> 
										</tr>  
									</thead>
									<tbody id="grn_details"> 
										<?php for($i=0; $i<count($pending); $i++) { ?>
										<tr> 
											<td scope="row"><?php echo $i+1; ?></td> 
											<td>
												<a href="<?php echo Url::base() .'index.php?r=paymentreceipt%2Fview&id='.$pending[$i]['id']; ?>" >View </a> <br/>
												<a href="<?php echo Url::base() .'index.php?r=paymentreceipt%2Fedit&id='.$pending[$i]['id']; ?>" style="<?php if(isset($access[0]['r_edit'])) { if($access[0]['r_edit']=='1' && $access[0]['session_id']!=$pending[$i]['approver_id']) echo ''; else echo 'display: none;'; } else { echo 'display: none;'; } ?>">Edit </a> <br/>
												<a href="<?php echo Url::base() .'index.php?r=paymentreceipt%2Fauthorise&id='.$pending[$i]['id']; ?>" style="<?php if(isset($access[0]['r_approval'])) { if($access[0]['r_approval']=='1' && $access[0]['session_id']==$pending[$i]['approver_id']) echo ''; else echo 'display: none;'; } else { echo 'display: none;'; } ?>">Authorise </a>
											</td> 
											<td><?php echo $pending[$i]['id']; ?></td> 
											<td><?php echo $pending[$i]['trans_type']; ?></td> 
											<td><?php echo $pending[$i]['account_name']; ?></td> 
											<td><?php echo $pending[$i]['bank_name']; ?></td> 
											<td><?php echo $pending[$i]['payment_type']; ?></td> 
											<td><?php echo (($pending[$i]['payment_date']!=null && $pending[$i]['payment_date']!='')?date('d/m/Y',strtotime($pending[$i]['payment_date'])):''); ?></td> 
											<td class="text-right"><?php echo $mycomponent->format_money($pending[$i]['amount'],2); ?></td> 
											<td><?php echo $pending[$i]['status']; ?></td> 
											<td><?php echo $pending[$i]['updater']; ?></td> 
											<td style="display: none;"><?php echo $pending[$i]['approver']; ?></td> 
										</tr> 
										<?php } ?>
									</tbody> 
								</table>
							</div>
						</div>
						<div class="tab-pane fade" id="tab2primary">
							<div class="bs-example grn-index" data-example-id="bordered-table"> 
								<table id="example1" class="table datatable table-bordered display" cellspacing="0" width="100%">
									<thead> 
										<tr> 
											<th width="45">Sr. No.</th> 
											<th>Action</th> 
											<th>P_Id</th> 
											<th>Transaction Type</th> 
											<th>Account Name</th> 
											<th>Bank Name</th> 
											<th>Payment Type</th> 
											<th>Payment Date</th> 
											<th>Amount</th> 
											<th>Status</th> 
											<th>Updated By</th> 
											<th>Approved By</th> 
										</tr>  
									</thead>
									<tbody id="grn_details"> 
										<?php for($i=0; $i<count($approved); $i++) { ?>
										<tr> 
											<td scope="row"><?php echo $i+1; ?></td> 
											<td>
												<a href="<?php echo Url::base() .'index.php?r=paymentreceipt%2Fview&id='.$approved[$i]['id']; ?>" >View </a>
											</td> 
											<td><?php echo $approved[$i]['id']; ?></td> 
											<td><?php echo $approved[$i]['trans_type']; ?></td> 
											<td><?php echo $approved[$i]['account_name']; ?></td> 
											<td><?php echo $approved[$i]['bank_name']; ?></td> 
											<td><?php echo $approved[$i]['payment_type']; ?></td> 
											<td><?php echo (($approved[$i]['payment_date']!=null && $approved[$i]['payment_date']!='')?date('d/m/Y',strtotime($approved[$i]['payment_date'])):''); ?></td> 
											<td class="text-right"><?php echo $mycomponent->format_money($approved[$i]['amount'],2); ?></td> 
											<td><?php echo $approved[$i]['status']; ?></td> 
											<td><?php echo $approved[$i]['updater']; ?></td> 
											<td><?php echo $approved[$i]['approver']; ?></td> 
										</tr> 
										<?php } ?>
									</tbody> 
								</table>
							</div>
						</div> 
						<div class="tab-pane fade" id="tab3primary">
							<div class="bs-example grn-index table-container containner" data-example-id="bordered-table"  >  
								<table id="example2" class="table datatable table-bordered display" cellspacing="0" width="100%">
									<thead> 
										<tr> 
											<th width="45">Sr. No.</th> 
											<th>Action</th> 
											<th>P_Id</th> 
											<th>Transaction Type</th> 
											<th>Account Name</th> 
											<th>Bank Name</th> 
											<th>Payment Type</th> 
											<th>Payment Date</th> 
											<th>Amount</th> 
											<th>Status</th> 
											<th>Updated By</th> 
											<th>Rejected By</th> 
										</tr>  
									</thead>
									<tbody> 
										<?php for($i=0; $i<count($rejected); $i++) { ?>
										<tr> 
											<td scope="row"><?php echo $i+1; ?></td> 
											<td>
												<a href="<?php echo Url::base() .'index.php?r=paymentreceipt%2Fview&id='.$rejected[$i]['id']; ?>" >View </a> <br/>
												<a href="<?php echo Url::base() .'index.php?r=paymentreceipt%2Fedit&id='.$rejected[$i]['id']; ?>" style="<?php if(isset($access[0]['r_edit'])) { if($access[0]['r_edit']=='1' && $access[0]['session_id']==$rejected[$i]['approver_id']) echo ''; else echo 'display: none;'; } else { echo 'display: none;'; } ?>">Edit </a> <br/>
											</td> 
											<td><?php echo $rejected[$i]['id']; ?></td> 
											<td><?php echo $rejected[$i]['trans_type']; ?></td> 
											<td><?php echo $rejected[$i]['account_name']; ?></td> 
											<td><?php echo $rejected[$i]['bank_name']; ?></td> 
											<td><?php echo $rejected[$i]['payment_type']; ?></td> 
											<td><?php echo (($rejected[$i]['payment_date']!=null && $rejected[$i]['payment_date']!='')?date('d/m/Y',strtotime($rejected[$i]['payment_date'])):''); ?></td> 
											<td class="text-right"><?php echo $mycomponent->format_money($rejected[$i]['amount'],2); ?></td> 
											<td><?php echo $rejected[$i]['status']; ?></td> 
											<td><?php echo $rejected[$i]['updater']; ?></td> 
											<td><?php echo $rejected[$i]['approver']; ?></td> 
										</tr> 
										<?php } ?>
									</tbody> 
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div> 
</div>

<script type="text/javascript">
    var BASE_URL="<?php echo Url::base(); ?>";
</script>

<?php 
    $this->registerJsFile(
        '@web/js/datatable.js',
        ['depends' => [\yii\web\JqueryAsset::className()]]
    );
?>