<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

$this->title = 'Promotion List';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="grn-index">	
	<div class=" col-md-12 ">  
		<a href="<?php echo Url::base(); ?>index.php?r=promotion%2Fcreate" style="<?php if(isset($access[0]['r_insert'])) { if($access[0]['r_insert']=='1') echo ''; else echo 'display: none;'; } else { echo 'display: none;'; } ?>"> 
			<button type="button" class="btn btn-grid btn-success btn-sm pull-right">Add New Promotion </button>
		</a>
		<div class="panel with-nav-tabs panel-primary">
			<div class="panel-heading">
				<ul class="nav nav-tabs">
					<li class="active"><a href="#tab1primary" data-toggle="tab"> Approved (<?php echo count($approved); ?>)</a></li>
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
											<th>Transaction</th>
											<th>Date</th> 
											<th>Debit Account</th> 
											<th>Credit Account</th> 
											<th>Debit Amount</th>  
											<th>Credit Amount</th> 
											<th>Debit Credit Note Ref</th> 
											<th>Status</th> 
											<th>Updated By</th> 
											<th>Debit Credit Note</th> 
											<th style="display: none;">Approved By</th> 
										</tr>  
									</thead>
									<tbody> 
										<?php for($i=0; $i<count($approved); $i++) { ?>
										<tr> 
											<td scope="row"><?php echo $i+1; ?></td> 
											<td>
												<a href="<?php echo Url::base() .'index.php?r=promotion%2Fview&id='.$approved[$i]['id']; ?>" >View </a> <br/>
												<a href="<?php echo Url::base() .'index.php?r=promotion%2Fedit&id='.$approved[$i]['id']; ?>" style="<?php if(isset($access[0]['r_edit'])) { if($access[0]['r_edit']=='1' && $access[0]['session_id']!=$approved[$i]['approver_id']) echo ''; else echo 'display: none;'; } else { echo 'display: none;'; } ?>">Edit </a> <br/>
												<!-- <a href="<?php //echo Url::base() .'index.php?r=promotion%2Fauthorise&id='.$approved[$i]['id']; ?>" style="<?php //if(isset($access[0]['r_approval'])) { if($access[0]['r_approval']=='1' && $access[0]['session_id']==$approved[$i]['approver_id']) echo ''; else echo 'display: none;'; } else { echo 'display: none;'; } ?>">Authorise </a> -->
											</td> 
											<td><?php echo $approved[$i]['trans_type']; ?></td> 
											<td><?php if(isset($approved[$i]['date_of_transaction'])) echo (($approved[$i]['date_of_transaction']!=null && $approved[$i]['date_of_transaction']!='')?date('d/m/Y',strtotime($approved[$i]['date_of_transaction'])):''); ?></td> 
											<td><?php echo $approved[$i]['debit_acc']; ?></td> 
											<td><?php echo $approved[$i]['credit_acc']; ?></td> 
											<td><?php echo $approved[$i]['debit_amt']; ?></td> 
											<td><?php echo $approved[$i]['credit_amt']; ?></td> 
											<td><?php echo $approved[$i]['debit_credit_note_ref']; ?></td> 
											<td><?php echo $approved[$i]['status']; ?></td> 
											<td><?php echo $approved[$i]['updater']; ?></td> 
											<td>
												<a href="<?php echo Url::base() .'index.php?r=promotion%2Fviewdebitcreditnote&id='.$approved[$i]['id']; ?>" target="_blank">View </a> <br/>
												<a href="<?php echo Url::base() .'index.php?r=promotion%2Fdownloaddebitcreditnote&id='.$approved[$i]['id']; ?>" target="_blank">Download </a> <br/>
												<a href="<?php echo Url::base() .'index.php?r=promotion%2Femaildebitcreditnote&id='.$approved[$i]['id']; ?>" >Email </a> <br/>
											</td> 
											<td style="display: none;"><?php echo $approved[$i]['approver']; ?></td> 
										</tr> 
										<?php } ?>
									</tbody> 
								</table>
							</div>
						</div>
						<!-- <div class="tab-pane fade" id="tab2primary">
							<div class="bs-example grn-index" data-example-id="bordered-table"> 
								<table id="example1" class="table datatable table-bordered display" cellspacing="0" width="100%">
									<thead> 
										<tr> 
											<th width="45">Sr. No.</th> 
											<th>Action</th> 
											<th>JV_Id</th> 
											<th>Reference</th>
											<th>Date</th> 
											<th>Debit Account</th> 
											<th>Credit Account</th> 
											<th>Debit Amount</th>  
											<th>Credit Amount</th> 
											<th>Narration</th> 
											<th>Status</th> 
											<th>Updated By</th> 
											<th>Approved By</th> 
										</tr>  
									</thead>
									<tbody> 
										<?php //for($i=0; $i<count($approved); $i++) { ?>
										<tr> 
											<td scope="row"><?php //echo $i+1; ?></td> 
											<td>
												<a href="<?php //echo Url::base() .'index.php?r=promotion%2Fview&id='.$approved[$i]['id']; ?>" >View </a>
											</td> 
											<td><?php //echo $approved[$i]['id']; ?></td> 
											<td><?php //echo $approved[$i]['reference']; ?></td> 
											<td><?php //if(isset($approved[$i]['date_of_transaction'])) echo (($approved[$i]['date_of_transaction']!=null && $approved[$i]['date_of_transaction']!='')?date('d/m/Y',strtotime($approved[$i]['date_of_transaction'])):''); ?></td> 
											<td><?php //echo $approved[$i]['debit_acc']; ?></td> 
											<td><?php //echo $approved[$i]['credit_acc']; ?></td> 
											<td><?php //echo $approved[$i]['debit_amt']; ?></td> 
											<td><?php //echo $approved[$i]['credit_amt']; ?></td> 
											<td><?php //echo $approved[$i]['narration']; ?></td> 
											<td><?php //echo $approved[$i]['status']; ?></td> 
											<td><?php //echo $approved[$i]['updater']; ?></td> 
											<td><?php //echo $approved[$i]['approver']; ?></td> 
										</tr> 
										<?php //} ?>
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
											<th>JV_Id</th> 
											<th>Reference</th>
											<th>Date</th> 
											<th>Debit Account</th> 
											<th>Credit Account</th> 
											<th>Debit Amount</th>  
											<th>Credit Amount</th> 
											<th>Narration</th> 
											<th>Status</th> 
											<th>Updated By</th> 
											<th>Rejected By</th> 
										</tr>  
									</thead>
									<tbody> 
										<?php //for($i=0; $i<count($rejected); $i++) { ?>
										<tr> 
											<td scope="row"><?php //echo $i+1; ?></td> 
											<td>
												<a href="<?php //echo Url::base() .'index.php?r=promotion%2Fview&id='.$rejected[$i]['id']; ?>" >View </a> <br/>
												<a href="<?php //echo Url::base() .'index.php?r=promotion%2Fedit&id='.$rejected[$i]['id']; ?>" style="<?php //if(isset($access[0]['r_edit'])) { if($access[0]['r_edit']=='1' && $access[0]['session_id']!=$rejected[$i]['approver_id']) echo ''; else echo 'display: none;'; } else { echo 'display: none;'; } ?>">Edit </a> <br/>
											</td> 
											<td><?php //echo $rejected[$i]['id']; ?></td> 
											<td><?php //echo $rejected[$i]['reference']; ?></td> 
											<td><?php //if(isset($rejected[$i]['date_of_transaction'])) echo (($rejected[$i]['date_of_transaction']!=null && $rejected[$i]['date_of_transaction']!='')?date('d/m/Y',strtotime($rejected[$i]['date_of_transaction'])):''); ?></td> 
											<td><?php //echo $rejected[$i]['debit_acc']; ?></td> 
											<td><?php //echo $rejected[$i]['credit_acc']; ?></td> 
											<td><?php //echo $rejected[$i]['debit_amt']; ?></td> 
											<td><?php //echo $rejected[$i]['credit_amt']; ?></td> 
											<td><?php //echo $rejected[$i]['narration']; ?></td> 
											<td><?php //echo $rejected[$i]['status']; ?></td> 
											<td><?php //echo $rejected[$i]['updater']; ?></td> 
											<td><?php //echo $rejected[$i]['approver']; ?></td> 
										</tr> 
										<?php //} ?>
									</tbody> 
								</table>
							</div>
						</div> -->
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