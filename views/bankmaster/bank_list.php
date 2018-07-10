<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\GrnSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Bank Master';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="grn-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<div class=" col-md-10   col-sm-9 main-content ">

		<section class="row  ">	

			<div class="main-wrapper">
				<div class="col-md-12 ">
					<div class=" col-md-12  media-clmn">  
						<a href="<?php echo Url::base(); ?>index.php?r=bankmaster%2Fcreate"> <button type="button" class="btn btn-grid btn-success btn-sm pull-right">Add New Bank Details </button></a>
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
														<th>Bank Name</th>
														<th>Branch</th> 
														<!-- <th>Address</th> 
														<th>Landmark</th> 
														<th>City</th> 
														<th>Pincode</th>  
														<th>State</th>  
														<th>Country</th>  --> 
														<th>Acc Type</th>  
														<th>Acc No</th>  
														<th>Ifsc Code</th>  
														<!-- <th>Customer Id</th>  
														<th>Phone No</th>  
														<th>Relationship Manager</th>  --> 
														<th>Opening Balance</th>  
														<th>Balance Ref Date</th> 
														<th>Status</th> 
														<th>Updated By</th> 
														<th>Approved By</th> 
													</tr>  
												</thead>
												<tbody id="grn_details"> 
													<?php for($i=0; $i<count($pending); $i++) { ?>
													<tr> 
														<td scope="row"><?php echo $i+1; ?></td> 
														<td><a href="<?php echo Url::base() .'index.php?r=bankmaster%2Fedit&id='.$pending[$i]['id']; ?>" >Edit </a></td> 
														<td><?php echo $pending[$i]['bank_name']; ?></td> 
														<td><?php echo $pending[$i]['branch']; ?></td> 
														<!-- <td><?php //echo $pending[$i]['address']; ?></td> 
														<td><?php //echo $pending[$i]['landmark']; ?></td> 
														<td><?php //echo $pending[$i]['city']; ?></td> 
														<td><?php //echo $pending[$i]['pincode']; ?></td> 
														<td><?php //echo $pending[$i]['state']; ?></td> 
														<td><?php //echo $pending[$i]['country']; ?></td>  -->
														<td><?php echo $pending[$i]['acc_type']; ?></td> 
														<td><?php echo $pending[$i]['acc_no']; ?></td> 
														<td><?php echo $pending[$i]['ifsc_code']; ?></td> 
														<!-- <td><?php //echo $pending[$i]['customer_id']; ?></td> 
														<td><?php //echo $pending[$i]['phone_no']; ?></td> 
														<td><?php //echo $pending[$i]['relationship_manager']; ?></td>  -->
														<td><?php echo $pending[$i]['opening_balance']; ?></td> 
														<td><?php echo $pending[$i]['balance_ref_date']; ?></td> 
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
														<th>Bank Name</th>
														<th>Branch</th> 
														<!-- <th>Address</th> 
														<th>Landmark</th> 
														<th>City</th> 
														<th>Pincode</th>  
														<th>State</th>  
														<th>Country</th>   -->
														<th>Acc Type</th>  
														<th>Acc No</th>  
														<th>Ifsc Code</th>  
														<!-- <th>Customer Id</th>  
														<th>Phone No</th>  
														<th>Relationship Manager</th>   -->
														<th>Opening Balance</th>  
														<th>Balance Ref Date</th> 
														<th>Status</th> 
														<th>Updated By</th> 
														<th>Approved By</th> 
													</tr>  
												</thead>
												<tbody> 
													<?php for($i=0; $i<count($approved); $i++) { ?>
													<tr> 
														<td scope="row"><?php echo $i+1; ?></td> 
														<td><a href="<?php echo Url::base() .'index.php?r=bankmaster%2Fupdate&id='.$approved[$i]['id']; ?>" >Edit </a></td> 
														<td><?php echo $approved[$i]['bank_name']; ?></td> 
														<td><?php echo $approved[$i]['branch']; ?></td> 
														<!-- <td><?php //echo $approved[$i]['address']; ?></td> 
														<td><?php //echo $approved[$i]['landmark']; ?></td> 
														<td><?php //echo $approved[$i]['city']; ?></td> 
														<td><?php //echo $approved[$i]['pincode']; ?></td> 
														<td><?php //echo $approved[$i]['state']; ?></td> 
														<td><?php //echo $approved[$i]['country']; ?></td>  -->
														<td><?php echo $approved[$i]['acc_type']; ?></td> 
														<td><?php echo $approved[$i]['acc_no']; ?></td> 
														<td><?php echo $approved[$i]['ifsc_code']; ?></td> 
														<!-- <td><?php //echo $approved[$i]['customer_id']; ?></td> 
														<td><?php //echo $approved[$i]['phone_no']; ?></td> 
														<td><?php //echo $approved[$i]['relationship_manager']; ?></td>  -->
														<td><?php echo $mycomponent->format_money($approved[$i]['opening_balance'],2); ?></td> 
														<td><?php echo $approved[$i]['balance_ref_date']; ?></td> 
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
			</div >	

		</section>

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