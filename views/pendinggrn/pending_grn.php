<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\GrnSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Pending Grns';
$this->params['breadcrumbs'][] = $this->title;
$mycomponent = Yii::$app->mycomponent;
?>
<style type="text/css">
	.tab-content table tr td { border:1px solid #eee; }
</style>

<div class="grn-index">
	<div class=" col-md-12">  
		<div class="panel with-nav-tabs panel-primary">
			<div class="panel-heading">
				<ul class="nav nav-tabs">
					<li class="active"><a href="#tab1primary" data-toggle="tab"> Not Posted (<?php echo count($grn); ?>)</a></li>
					<!-- <li><a href="#tab2primary" data-toggle="tab">Pending For Approval (<?php //echo count($pending); ?>)</a></li> -->
					<li><a href="#tab3primary" data-toggle="tab"> Posted (<?php echo count($approved); ?>)</a></li>
					<li><a href="#tab4primary" data-toggle="tab"> All (<?php echo count($all); ?>)</a></li>
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
											<th width="45" style="text-align: center;">Sr. No.</th> 
											<th>Action</th> 
											<th>Grn Id</th> 
											<th>Gi Id</th>
											<th>Location</th> 
											<th>Vendor Name</th> 
											<th>Scanned Qty</th> 
											<th>Payable Val After Tax</th> 
											<th>Gi Date</th> 
											<th>Status</th> 
											<th>Updated By</th> 
											<th>Approved By</th> 
										</tr>  
									</thead>
									<tbody id="grn_details"> 
										<?php for($i=0; $i<count($grn); $i++) { ?>
										<tr> 
											<td scope="row" style="text-align: center;"><?php echo $i+1; ?></td> 
											<td>
												<!-- <a href="<?php //echo Url::base() .'index.php?r=pendinggrn%2Fview&id='.$grn[$i]['grn_id']; ?>" >View </a> -->
												<a href="<?php echo Url::base() .'index.php?r=pendinggrn%2Fupdate&id='.$grn[$i]['grn_id']; ?>" >Post </a>
											</td> 
											<td><?php echo $grn[$i]['grn_id']; ?></td> 
											<td><?php echo $grn[$i]['gi_id']; ?></td> 
											<td><?php echo $grn[$i]['location']; ?></td> 
											<td><?php echo $grn[$i]['vendor_name']; ?></td> 
											<td class="text-right"><?php echo $grn[$i]['scanned_qty']; ?></td> 
											<td class="text-right"><?php echo $mycomponent->format_money($grn[$i]['payable_val_after_tax'], 2); ?></td> 
											<td><?php echo $grn[$i]['gi_date']; ?></td> 
											<td><?php echo $grn[$i]['status']; ?></td> 
											<td><?php echo $grn[$i]['username']; ?></td> 
											<td><?php echo $grn[$i]['approver_name']; ?></td> 
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
											<th style="text-align: center;">Sr. No.</th> 
											<th>Action</th> 
											<th>Grn Id</th> 
											<th>Gi Id</th> 
											<th>Vendor</th>  
											<th>Category</th>  
											<th>Po No</th> 
											<th>Invoice No</th>
											<th>Net Amount</th> 
											<th>Ded Amount</th> 
											<th>Updated By</th> 
											<th>Approved By</th> 
											<th>Ledger</th> 
										</tr>  
									</thead>
									<tbody> 
										<?php //for($i=0; $i<count($pending); $i++) { ?>
										<tr> 
											<td style="text-align: center;" scope="row"><?php //echo $i+1; ?></td> 
											<td>
												<a href="<?php //echo Url::base() .'index.php?r=pendinggrn%2Fview&id='.$pending[$i]['grn_id']; ?>" >View </a>
												<a href="<?php //echo Url::base() .'index.php?r=pendinggrn%2Fupdate&id='.$pending[$i]['grn_id']; ?>" style="<?php //if($pending[$i]['is_paid']=='1') echo 'display: none;'; ?>" >Edit </a>
											</td> 
											<td><?php //echo $pending[$i]['grn_id']; ?></td> 
											<td><?php //echo $pending[$i]['grn_no']; ?></td> 
											<td><?php //echo $pending[$i]['vendor_name']; ?></td> 
											<td><?php //echo $pending[$i]['category_name']; ?></td> 
											<td><?php //echo $pending[$i]['po_no']; ?></td> 
											<td><?php //echo $pending[$i]['inv_nos']; ?></td> 
											<td class="text-right"><?php //echo $mycomponent->format_money($pending[$i]['net_amt'], 2); ?></td> 
											<td class="text-right"><?php //echo $mycomponent->format_money($pending[$i]['ded_amt'], 2); ?></td> 
											<td><?php //echo $pending[$i]['username']; ?></td> 
											<td><?php //echo $pending[$i]['approved_by']; ?></td> 
											<td><a href="<?php //echo Url::base() .'index.php?r=pendinggrn%2Fledger&id='.$pending[$i]['grn_id']; ?>" target="_new"> <span class="fa fa-file-pdf-o"></span> </a></td> 
										</tr> 
										<?php //} ?>
									</tbody> 
								</table>
							</div>
						</div> -->
						<div class="tab-pane fade" id="tab3primary">
							<div class="bs-example grn-index" data-example-id="bordered-table"> 
								<table id="example2" class="table datatable table-bordered display" cellspacing="0" width="100%">
									<thead> 
										<tr> 
											<th style="text-align: center;">Sr. No.</th> 
											<th>Action</th> 
											<th>Grn Id</th> 
											<th>Gi Id</th> 
											<th>Vendor</th>  
											<th>Category</th>  
											<th>Po No</th> 
											<th>Invoice No</th>
											<th>Net Amount</th> 
											<th>Ded Amount</th> 
											<th>Updated By</th> 
											<th style="display: none;">Approved By</th> 
											<th>Ledger</th> 
										</tr>  
									</thead>
									<tbody> 
										<?php for($i=0; $i<count($approved); $i++) { ?>
										<tr> 
											<td style="text-align: center;" scope="row"><?php echo $i+1; ?></td> 
											<td>
												<a href="<?php echo Url::base() .'index.php?r=pendinggrn%2Fview&id='.$approved[$i]['grn_id']; ?>" >View </a>
												<a href="<?php echo Url::base() .'index.php?r=pendinggrn%2Fupdate&id='.$approved[$i]['grn_id']; ?>" style="<?php if($approved[$i]['is_paid']=='1') echo 'display: none;'; ?>" >Edit </a>
											</td> 
											<td><?php echo $approved[$i]['grn_id']; ?></td> 
											<td><?php echo $approved[$i]['grn_no']; ?></td> 
											<td><?php echo $approved[$i]['vendor_name']; ?></td> 
											<td><?php echo $approved[$i]['category_name']; ?></td> 
											<td><?php echo $approved[$i]['po_no']; ?></td> 
											<td><?php echo $approved[$i]['inv_nos']; ?></td> 
											<td class="text-right"><?php echo $mycomponent->format_money($approved[$i]['net_amt'], 2); ?></td> 
											<td class="text-right"><?php echo $mycomponent->format_money($approved[$i]['ded_amt'], 2); ?></td> 
											<td><?php echo $approved[$i]['username']; ?></td> 
											<td style="display: none;"><?php echo $approved[$i]['approved_by']; ?></td> 
											<td><a href="<?php echo Url::base() .'index.php?r=pendinggrn%2Fledger&id='.$approved[$i]['grn_id']; ?>" target="_new"> <span class="fa fa-file-pdf-o"></span> </a></td> 
										</tr> 
										<?php } ?>
									</tbody>  
								</table>
							</div>
						</div>
						<div class="tab-pane fade in" id="tab4primary">
							<div class="bs-example grn-index" data-example-id="bordered-table"  >  
								<table id="example3" class="table datatable table-bordered display" cellspacing="0" width="100%">
									<thead> 
										<tr> 
											<th width="45" style="text-align: center;">Sr. No.</th> 
											<th>Action</th> 
											<th>Grn Id</th> 
											<th>Gi Id</th>
											<th>Location</th> 
											<th>Vendor Name</th> 
											<th>Scanned Qty</th> 
											<th>Payable Val After Tax</th> 
											<th>Gi Date</th> 
											<th>Status</th> 
											<th>Updated By</th> 
											<th>Approved By</th> 
										</tr>  
									</thead>
									<tbody id="grn_details"> 
										<?php for($i=0; $i<count($all); $i++) { ?>
										<tr> 
											<td scope="row" style="text-align: center;"><?php echo $i+1; ?></td> 
											<td>
												<a href="<?php echo Url::base() .'index.php?r=pendinggrn%2Fview&id='.$all[$i]['grn_id']; ?>" >View </a>
												<a href="<?php echo Url::base() .'index.php?r=pendinggrn%2Fupdate&id='.$all[$i]['grn_id']; ?>" style="<?php if($all[$i]['is_paid']=='1') echo 'display: none;'; ?>">Post </a>
											</td> 
											<td><?php echo $all[$i]['grn_id']; ?></td> 
											<td><?php echo $all[$i]['grn_no']; ?></td> 
											<td><?php echo $all[$i]['location']; ?></td> 
											<td><?php echo $all[$i]['vendor_name']; ?></td> 
											<td class="text-right"><?php echo $all[$i]['scanned_qty']; ?></td> 
											<td class="text-right"><?php echo $mycomponent->format_money($all[$i]['payable_val_after_tax'], 2); ?></td> 
											<td><?php echo $all[$i]['gi_date']; ?></td> 
											<td><?php echo $all[$i]['grn_status']; ?></td> 
											<td><?php echo $all[$i]['username']; ?></td> 
											<td><?php echo $all[$i]['approver_name']; ?></td> 
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
	<!-- /*--------------------*/ -->
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