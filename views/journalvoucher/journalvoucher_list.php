<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\GrnSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Journal Voucher List';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="grn-index">	
					<div class=" col-md-12 ">  
						<a href="<?php echo Url::base(); ?>index.php?r=journalvoucher%2Fcreate"> <button type="button" class="btn btn-grid btn-success btn-sm pull-right">Add New Journal Voucher </button></a>
						<div class="panel with-nav-tabs panel-primary">
							<div class="panel-heading">
								<ul class="nav nav-tabs">
									<li class="active"><a href="#tab1primary" data-toggle="tab"> Pending (<?php echo count($pending); ?>)</a></li>
									<li><a href="#tab2primary" data-toggle="tab"> Approved (<?php echo count($approved); ?>)</a></li>
								</ul>
							</div>
							<div class="panel-body">
								<div class="tab-content">
									<div class="tab-pane fade in active" id="tab1primary">
										<div class="bs-example grn-index" data-example-id="bordered-table"  >  
											<table id="example" class="display" cellspacing="0" width="100%">
												<thead> 
													<tr> 
														<th width="45">Sr. No.</th> 
														<th>Action</th> 
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
												<tbody id="grn_details"> 
													<?php for($i=0; $i<count($pending); $i++) { ?>
													<tr> 
														<td scope="row"><?php echo $i+1; ?></td> 
														<td><a href="<?php echo Url::base() .'index.php?r=journalvoucher%2Fedit&id='.$pending[$i]['id']; ?>" >Edit </a></td> 
														<td><?php echo $pending[$i]['reference']; ?></td> 
														<td><?php echo $pending[$i]['updated_date']; ?></td> 
														<td><?php echo $pending[$i]['debit_acc']; ?></td> 
														<td><?php echo $pending[$i]['credit_acc']; ?></td> 
														<td><?php echo $pending[$i]['debit_amt']; ?></td> 
														<td><?php echo $pending[$i]['credit_amt']; ?></td> 
														<td><?php echo $pending[$i]['narration']; ?></td> 
														<td><?php echo $pending[$i]['status']; ?></td> 
														<th><?php echo $pending[$i]['updated_by']; ?></th> 
														<th><?php echo $pending[$i]['approved_by']; ?></th> 
													</tr> 
													<?php } ?>
												</tbody> 
											</table>
										</div>
									</div>
									<div class="tab-pane fade" id="tab2primary">
										<div class="bs-example grn-index" data-example-id="bordered-table"> 
											<table id="example1" class="display" cellspacing="0" width="100%">
												<thead> 
													<tr> 
														<th width="45">Sr. No.</th> 
														<th>Action</th> 
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
													<?php for($i=0; $i<count($approved); $i++) { ?>
													<tr> 
														<td scope="row"><?php echo $i+1; ?></td> 
														<td><a href="<?php echo Url::base() .'index.php?r=journalvoucher%2Fedit&id='.$approved[$i]['id']; ?>" >Edit </a></td> 
														<td><?php echo $approved[$i]['reference']; ?></td> 
														<td><?php echo $approved[$i]['updated_date']; ?></td> 
														<td><?php echo $approved[$i]['debit_acc']; ?></td> 
														<td><?php echo $approved[$i]['credit_acc']; ?></td> 
														<td><?php echo $approved[$i]['debit_amt']; ?></td> 
														<td><?php echo $approved[$i]['credit_amt']; ?></td> 
														<td><?php echo $approved[$i]['narration']; ?></td> 
														<td><?php echo $approved[$i]['status']; ?></td> 
														<th><?php echo $approved[$i]['updated_by']; ?></th> 
														<th><?php echo $approved[$i]['approved_by']; ?></th> 
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
<script type="text/javascript">
    var BASE_URL="<?php echo Url::base(); ?>";
</script>

<?php 
    $this->registerJsFile(
        '@web/js/datatable.js',
        ['depends' => [\yii\web\JqueryAsset::className()]]
    );
?>