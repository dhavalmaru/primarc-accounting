<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\GrnSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Debit Credit Note';
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
						<a href="<?php echo Url::base(); ?>index.php?r=debitcreditnote%2Fcreate"> <button type="button" class="btn btn-grid btn-success btn-sm pull-right">Add New Account Details </button></a>
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
														<th>Trans Id</th>
														<th>Type</th> 
														<th>Transaction</th> 
														<th>Due Date</th> 
														<th>Ref No</th> 
														<th>Other Ref No</th> 
														<th>Amount</th> 
														<th>Status</th> 
														<th>Updated By</th> 
														<th>Approved By</th> 
													</tr>  
												</thead>
												<tbody id="grn_details"> 
													<?php for($i=0; $i<count($pending); $i++) { ?>
													<tr> 
														<td scope="row"><?php echo $i+1; ?></td> 
														<td><a href="<?php echo Url::base() .'index.php?r=debitcreditnote%2Fedit&trans_id='.$pending[$i]['trans_id']; ?>" >Edit </a></td> 
														<td><?php echo $pending[$i]['trans_id']; ?></td> 
														<td><?php echo $pending[$i]['type']; ?></td> 
														<td><?php echo $pending[$i]['transaction']; ?></td> 
														<td><?php echo $pending[$i]['due_date']; ?></td> 
														<td><?php echo $pending[$i]['ref_no']; ?></td> 
														<td><?php echo $pending[$i]['other_ref_no']; ?></td> 
														<td class="text-right"><?php echo $mycomponent->format_money($pending[$i]['amount'],2); ?></td> 
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
														<th>Trans Id</th>
														<th>Type</th> 
														<th>Transaction</th> 
														<th>Due Date</th> 
														<th>Ref No</th> 
														<th>Other Ref No</th> 
														<th>Amount</th> 
														<th>Status</th> 
														<th>Updated By</th> 
														<th>Approved By</th> 
													</tr>  
												</thead>
												<tbody> 
													<?php for($i=0; $i<count($approved); $i++) { ?>
													<tr> 
														<td scope="row"><?php echo $i+1; ?></td> 
														<td><a href="<?php echo Url::base() .'index.php?r=debitcreditnote%2Fupdate&trans_id='.$approved[$i]['trans_id']; ?>" >Edit </a></td> 
														<td><?php echo $approved[$i]['trans_id']; ?></td> 
														<td><?php echo $approved[$i]['type']; ?></td> 
														<td><?php echo $approved[$i]['transaction']; ?></td> 
														<td><?php echo $approved[$i]['due_date']; ?></td> 
														<td><?php echo $approved[$i]['ref_no']; ?></td> 
														<td><?php echo $approved[$i]['other_ref_no']; ?></td> 
														<td class="text-right"><?php echo $mycomponent->format_money($approved[$i]['amount'],2); ?></td> 
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