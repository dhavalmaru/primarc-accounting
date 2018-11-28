<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\GrnSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Pending Go';
$this->params['breadcrumbs'][] = $this->title;
$mycomponent = Yii::$app->mycomponent;
?>
<style type="text/css">
	.tab-content table tr td { border:1px solid #eee; }
</style>
<link href="http://localhost/primarc_pecan/web/css/export.css" rel="stylesheet">

<div class="grn-index">
	<div class=" col-md-12">  
		<div class="panel with-nav-tabs panel-primary">
			<div class="panel-heading">
				<ul class="nav nav-tabs">
					<li class="active"><a href="#tab1primary" data-toggle="tab" class="tab1primary"> Not Posted </a></li>
					<!-- <li><a href="#tab2primary" data-toggle="tab">Pending For Approval (<?php //echo count($pending); ?>)</a></li> -->
					<li><a href="#tab3primary" data-toggle="tab" class="tab3primary"> Posted </a></li>
					<li><a href="#tab4primary" data-toggle="tab" class="tab4primary"> All </a></li>
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
											<th>GO No</th>
											<th>Po Number</th> 
											<th>So Number</th>  
											<th>DC No</th>
											<th>Invoice No</th>
											<th>Location (From)</th> 
											<th>Location (To)</th> 
											<th>Customer name</th> 
											<th>Scanned Qty</th> 
											<!-- <th>Receivable after tax</th>  -->
											<th>GO Date (dd/mm/yyyy)</th> 
											<th>Status</th> 
											<th>Updated By</th> 
											<th>Approved By</th> 
										</tr>  
									</thead>
									<tbody id="grn_details"> 
										
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
											<th width="45" style="text-align: center;">Sr. No.</th> 
											<th>Action</th> 
											<th>GO id</th> 
											<th>Po Number</th> 
											<th>So Number</th> 
											<th>Location From</th>
											<th>Location To</th>
											<th>Customer name</th>
											<!-- <th>Receivable after tax</th>  -->
											<th>GO Date (dd/mm/yyyy)</th> 
											<th>Status</th> 
											<th>Updated By</th> 
											<th>Approved By</th> 
											<th>Ledger</th> 
										</tr>    
									</thead>
									<tbody> 
										
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
											<th>GO id</th>
											<th>Po Number</th> 
											<th>So Number</th>  
											<th>Location (From)</th> 
											<th>Location To</th>
											<th>Customer name</th>
											<!-- <th>Receivable after tax</th>  -->
											<th>GO Date (dd/mm/yyyy)</th> 
											<th>Status</th> 
											<th>Updated By</th> 
											<th>Approved By</th>
										</tr>  
									</thead>
									<tbody id="grn_details"> 
										
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
        '@web/js/pending_go.js',
        ['depends' => [\yii\web\JqueryAsset::className()]]
    );
?>