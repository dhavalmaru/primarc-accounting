

<!-- <link href="css/updated_css.css" rel="stylesheet"> -->
<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\GrnSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Role';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="grn-index">
	<div class=" col-md-12">  
		<a href="<?php echo Url::base(); ?>index.php?r=assignrole%2Fcreate" style="<?php if(isset($access[0]['r_insert'])) { if($access[0]['r_insert']=='1') echo ''; else echo 'display: none;'; } else { echo 'display: none;'; } ?>"> 
			<button type="button" class="btn btn-grid btn-success btn-sm pull-right">Assign Role </button>
		</a>
		<div class="panel with-nav-tabs panel-primary">
			<div class="panel-heading">
				<ul class="nav nav-tabs">
					<li class="active"><a href="#tab1primary" data-toggle="tab"> Approved (<?php echo count($data); ?>)</a></li>
					<!-- <li><a href="#tab2primary" data-toggle="tab"> Pending (<?php //echo count($pending); ?>)</a></li> -->
				</ul>
			</div>
			<div class="panel-body">
				<div  id="loader" > </div>
				<div class="loading">
					<div class="tab-content">
						<div class="tab-pane fade in active" id="tab1primary">
							<div class="bs-example grn-index table-container containner" data-example-id="bordered-table"  >  
								<table id="example" class="table datatable table-bordered display" cellspacing="0" width="100%">
									<thead> 
										<tr> 
											<th width="58" align="center">Sr. No.</th> 
											<th style="<?php if(isset($access[0]['r_edit'])) { if($access[0]['r_edit']=='1') echo ''; else echo 'display: none;'; } else { echo 'display: none;'; } ?>">
												Action
											</th> 
											<th>User</th>
											<th>Role</th> 
											<th>Status</th> 
											<th>Updated By</th> 
											<th>Updated Date</th> 
										</tr>  
									</thead>
									<tbody id="grn_details"> 
										<?php for($i=0; $i<count($data); $i++) { ?>
										<tr> 
											<td scope="row" align="center"><?php echo $i+1; ?></td> 
											<td style="<?php if(isset($access[0]['r_edit'])) { if($access[0]['r_edit']=='1') echo ''; else echo 'display: none;'; } else { echo 'display: none;'; } ?>">
												<a href="<?php echo Url::base() .'index.php?r=assignrole%2Fedit&id=' . $data[$i]['id']; ?>" >Edit </a>
											</td> 
											<td><?php echo $data[$i]['user_name']; ?></td> 
											<td><?php echo $data[$i]['role']; ?></td> 
											<td><?php echo $data[$i]['status']; ?></td> 
											<td><?php echo $data[$i]['username']; ?></td> 
											<td><?php if(isset($data)) echo (($data[$i]['updated_date']!=null && $data[$i]['updated_date']!='')?date('d/m/Y',strtotime($data[$i]['updated_date'])):date('d/m/Y')); else echo date('d/m/Y'); ?></td>
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