<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\GrnSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Goods Outward';
$this->params['breadcrumbs'][] = $this->title;
$mycomponent = Yii::$app->mycomponent;
?>
<style type="text/css">
	.tab-content table tr td { border:1px solid #eee; }
</style>
<!-- <link href="http://localhost/primarc_pecan/web/css/export.css" rel="stylesheet"> -->
<link href="<?php echo Url::base(); ?>/css/export.css" rel="stylesheet">

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
											<th>Go Id</th> 
											<th>Go No</th>
											<th>Warehouse</th> 
											<th>Vendor Name</th> 
											<!-- <th>Idt Warehouse</th>  -->
											<th>Total Value</th> 
											<th>Go Date</th> 
											<th>Updated By</th> 
										</tr>  
									</thead>
									<tbody> 
										
									</tbody> 
								</table>
							</div>
						</div>

						<div class="tab-pane fade" id="tab3primary">
							<div class="bs-example grn-index" data-example-id="bordered-table"> 
								<table id="example2" class="table datatable table-bordered display" cellspacing="0" width="100%">
									<thead> 
										<tr> 
											<th style="text-align: center;">Sr. No.</th> 
											<th>Action</th> 
											<th>Go Id</th> 
											<th>Go No</th> 
											<th>Warehouse</th>  
											<th>Vendor Name</th> 
											<!-- <th>Idt Warehouse</th>  -->
											<th>Total Value</th> 
											<th>Go Date</th> 
											<th>Updated By</th> 
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
											<th>Go Id</th> 
											<th>Go No</th>
											<th>Warehouse</th> 
											<th>Vendor Name</th> 
											<!-- <th>Idt Warehouse</th>  -->
											<th>Total Value</th> 
											<th>Go Date</th> 
											<th>Updated By</th> 
										</tr>  
									</thead>
									<tbody> 
										
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
        '@web/js/goods_outward.js',
        ['depends' => [\yii\web\JqueryAsset::className()]]
    );
?>