

<!-- <link href="css/updated_css.css" rel="stylesheet"> -->
<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\GrnSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Account Master';
$this->params['breadcrumbs'][] = $this->title;
?>

<!-- <div class="grn-index">

	

	<div class=" col-md-10   col-sm-9 main-content ">

		<section class="row  ">	

			<div class="main-wrapper">
				<div class="col-md-12 "> -->
					<div class=" col-md-12">  
						<a href="<?php echo Url::base(); ?>index.php?r=accountmaster%2Fcreate"> <button type="button" class="btn btn-grid btn-success btn-sm pull-right">Add New Account Details </button></a>
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
										<div class="bs-example grn-index table-container" data-example-id="bordered-table"  >  
											<table id="example" class="display" cellspacing="0" width="100%">
												<thead> 
													<tr> 
														<th width="58" align="center">Sr. No.</th> 
														<th>Action</th> 
														<th>Type</th>
														<th>Code</th> 
														<th>Account Type</th> 
														<th>Legal Name</th>
														<th>Category_1</th>
														<th>Category_2</th>
														<th>Category_3</th>
														<th>Business Category</th>
														<th>Status</th> 
														<th>Updated By</th> 
														<th>Approved By</th> 
													</tr>  
												</thead>
												<tbody id="grn_details"> 
													<?php for($i=0; $i<count($pending); $i++) { ?>
													<tr> 
														<td scope="row" align="center"><?php echo $i+1; ?></td> 
														<td>
															<a href="<?php echo Url::base() .'index.php?r=accountmaster%2Fedit&id='.$pending[$i]['id']; ?>" >Edit </a> &nbsp; &nbsp;
															<a href="<?php echo Url::base() .'index.php?r=accountmaster%2Fview&id='.$pending[$i]['id']; ?>" >View </a>
														</td> 
														<td><?php echo $pending[$i]['type']; ?></td> 
														<td><?php echo $pending[$i]['code']; ?></td> 
														<td><?php echo $pending[$i]['account_type']; ?></td> 
														<td><?php echo $pending[$i]['legal_name']; ?></td> 
														<td><?php echo $pending[$i]['category_1']; ?></td> 
														<td><?php echo $pending[$i]['category_2']; ?></td> 
														<td><?php echo $pending[$i]['category_3']; ?></td> 
														<td><?php echo $pending[$i]['bus_category']; ?></td> 
														<td><?php echo $pending[$i]['status']; ?></td> 
														<td><?php echo $pending[$i]['updated_by']; ?></td> 
														<td><?php echo $pending[$i]['approved_by']; ?></td> 
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
														<th width="58" align="center">Sr. No.</th> 
														<th>Action</th> 
														<th>Type</th>
														<th>Code</th> 
														<th>Account Type</th> 
														<th>Legal Name</th>
														<th>Category_1</th>
														<th>Category_2</th>
														<th>Category_3</th>
														<th>Business Category</th>
														<th>Status</th> 
														<th>Updated By</th> 
														<th>Approved By</th>
													</tr>  
												</thead>
												<tbody> 
													<?php for($i=0; $i<count($approved); $i++) { ?>
													<tr> 
														<td scope="row"><?php echo $i+1; ?></td> 
														<td><a href="<?php echo Url::base() .'index.php?r=accountmaster%2Fupdate&id='.$approved[$i]['id']; ?>" >Edit </a></td> 
														<td><?php echo $approved[$i]['type']; ?></td> 
														<td><?php echo $approved[$i]['code']; ?></td> 
														<td><?php echo $approved[$i]['account_type']; ?></td> 
														<td><?php echo $approved[$i]['legal_name']; ?></td> 
														<td><?php echo $approved[$i]['category_1']; ?></td> 
														<td><?php echo $approved[$i]['category_2']; ?></td> 
														<td><?php echo $approved[$i]['category_3']; ?></td> 
														<td><?php echo $approved[$i]['bus_category']; ?></td> 
														<td><?php echo $approved[$i]['status']; ?></td> 
														<td><?php echo $approved[$i]['updated_by']; ?></td> 
														<td><?php echo $approved[$i]['approved_by']; ?></td> 
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
				<!-- </div> 
			</div >	

		</section>

	</div>

</div> -->

<script type="text/javascript">
    var BASE_URL="<?php echo Url::base(); ?>";
</script>

<?php 
    $this->registerJsFile(
        '@web/js/datatable.js',
        ['depends' => [\yii\web\JqueryAsset::className()]]
    );
?>