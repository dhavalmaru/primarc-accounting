<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\web\Session;

$this->title = 'Sales Upload';
$this->params['breadcrumbs'][] = $this->title;
$session = Yii::$app->session;
?>

<div class="grn-index">
	<div class=" col-md-12">
		<form id="sales_upload_form" class="form-horizontal" action="<?php echo Url::base(); ?>index.php?r=salesupload%2Fupload" method="post" enctype="multipart/form-data" onkeypress="return event.keyCode != 13;"> 
			<input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
			<input type="hidden" id="company_id" name="company_id" value="<?php if(isset($session['company_id'])) echo $session['company_id']; ?>" />
			<div class=" col-md-12">
				<div class="form-group">
					<div class="col-md-3 col-sm-3 col-xs-3"></div>
					<div class="col-md-6 col-sm-6 col-xs-6">
						<div class="col-md-3 col-sm-3 col-xs-3">
							<label class="control-label">Select File</label>
						</div>
						<div class="col-md-9 col-sm-9 col-xs-9">
			                <input style="padding:1px;" type="file" class="fileinput form-control" name="sales_file" id="sales_file" data-error="#sales_file_error" />
			                <div id="sales_file_error"></div>
						</div>
					</div>
					<div class="col-md-3 col-sm-3 col-xs-3">
						<input type="submit" class="btn btn-success btn-sm" id="btn_upload" name="btn_upload" value="Upload" />
					</div>
				</div>
			</div>
		</form>

		<div class=" col-md-12">&nbsp;</div>

		<div class="panel with-nav-tabs panel-primary">
			<div class="panel-heading">
				<ul class="nav nav-tabs">
					<!-- <li><a href="#tab1primary" data-toggle="tab"> Pending (<?php //echo count($pending); ?>)</a></li> -->
					<li class="active"><a href="#tab2primary" data-toggle="tab"> Upload Details (<?php echo count($approved); ?>)</a></li>
					<!-- <li><a href="#tab3primary" data-toggle="tab"> Rejected (<?php //echo count($rejected); ?>)</a></li> -->
				</ul>
			</div>
			<div class="panel-body">
				<div  id="loader" > </div>
					<div class="loading">
					<div class="tab-content">
						<!-- <div class="tab-pane fade" id="tab1primary">
							<div class="bs-example grn-index table-container containner" data-example-id="bordered-table"  >  
								<table id="example" class="table datatable table-bordered display" cellspacing="0" width="100%">
									<thead> 
										<tr> 
											<th width="58" align="center">Sr. No.</th> 
											<th>Action</th> 
											<th>Type</th>
											<th>Code</th> 
											<th>Account Type</th> 
											<th>Legal Name</th>
											<th>Category</th>
											<th>Business Category</th>
											<th>Status</th> 
											<th>Updated By</th> 
											<th style="display: none;">Approved By</th> 
										</tr>  
									</thead>
									<tbody> 
										<?php //for($i=0; $i<count($pending); $i++) { ?>
										<tr> 
											<td scope="row" align="center"><?php //echo $i+1; ?></td> 
											<td>
												<a href="<?php //echo Url::base() .'index.php?r=accountmaster%2Fview&id='.$pending[$i]['id']; ?>" >View </a> <br/>
												<a href="<?php //echo Url::base() .'index.php?r=accountmaster%2Fedit&id='.$pending[$i]['id']; ?>" style="<?php //if(isset($access[0]['r_edit'])) { if($access[0]['r_edit']=='1' && $access[0]['session_id']!=$pending[$i]['approver_id']) echo ''; else echo 'display: none;'; } else { echo 'display: none;'; } ?>">Edit </a> <br/>
												<a href="<?php //echo Url::base() .'index.php?r=accountmaster%2Fauthorise&id='.$pending[$i]['id']; ?>" style="<?php //if(isset($access[0]['r_approval'])) { if($access[0]['r_approval']=='1' && $access[0]['session_id']==$pending[$i]['approver_id']) echo ''; else echo 'display: none;'; } else { echo 'display: none;'; } ?>">Authorise </a>
											</td> 
											<td><?php //echo $pending[$i]['type']; ?></td> 
											<td><?php //echo $pending[$i]['code']; ?></td> 
											<td><?php //echo $pending[$i]['account_type']; ?></td>
											<td><?php //echo $pending[$i]['legal_name']; ?></td> 
											<td><?php //echo $pending[$i]['acc_category']; ?></td>
											<td><?php //echo $pending[$i]['bus_category']; ?></td> 
											<td><?php //echo $pending[$i]['status']; ?></td> 
											<td><?php //echo $pending[$i]['updater']; ?></td> 
											<td style="display: none;"><?php //echo $pending[$i]['approver']; ?></td> 
										</tr> 
										<?php //} ?>
									</tbody> 
								</table>
							</div>
						</div> -->
						<div class="tab-pane fade in active" id="tab2primary">
							<div class="bs-example grn-index" data-example-id="bordered-table"> 
								<table id="example1" class="table datatable table-bordered display" cellspacing="0" width="100%">
									<thead> 
										<tr> 
											<th width="58" align="center">Sr. No.</th> 
											<th>Date Of Upload</th> 
											<th>File Name</th>
											<th>View</th> 
											<th>Download Original File</th> 
											<th>Download Error Highlighted File</th>
											<th>Download Error Rejected File</th>
											<th>Freeze File</th>
											<th>Uploaded By</th> 
											<th>Check HSN</th>
										</tr>  
									</thead>
									<tbody> 
										<?php for($i=0; $i<count($approved); $i++) { ?>
										<tr> 
											<td scope="row"><?php echo $i+1; ?></td> 
											<td><?php if($approved[$i]['date_of_upload']!=null && $approved[$i]['date_of_upload']!='') echo date('d/m/Y',strtotime($approved[$i]['date_of_upload'])); ?></td> 
											<td><?php echo $approved[$i]['file_name']; ?></td> 
											<td>
												<a href="<?php echo Url::base() .'index.php?r=salesupload%2Fedit&id='.$approved[$i]['id']; ?>" >View </a> 
											</td> 
											<td class="text-center">
												<?php if($approved[$i]['original_file']!= '') { ?>
												<a target="_blank" class="download_file" href="<?php echo Url::base().$approved[$i]['original_file']; ?>">
													<span class="fa download fa-download" ></span>
												</a>
												<?php } ?>
											</td> 
											<td class="text-center">
												<?php if($approved[$i]['error_highlighted_file']!= '') { ?>
												<a target="_blank" class="download_file" href="<?php echo Url::base().$approved[$i]['error_highlighted_file']; ?>">
													<span class="fa download fa-download" ></span>
												</a>
												<?php } ?>
											</td> 
											<td class="text-center">
												<?php if($approved[$i]['error_rejected_file']!= '') { ?>
												<a target="_blank" class="download_file" href="<?php echo Url::base().$approved[$i]['error_rejected_file']; ?>">
													<span class="fa download fa-download" ></span>
												</a>
												<?php } ?>
											</td> 
											<td><button type="button" class="btn btn-default btn-sm">Freeze</button></td> 
											<td><?php echo $approved[$i]['creator']; ?></td> 
											<td><button type="button" class="btn btn-default btn-sm">Check HSN</button></td> 
										</tr> 
										<?php } ?>
									</tbody> 
								</table>
							</div>
						</div>
						<!-- <div class="tab-pane fade" id="tab3primary">
							<div class="bs-example grn-index table-container containner" data-example-id="bordered-table"  >  
								<table id="example2" class="table datatable table-bordered display" cellspacing="0" width="100%">
									<thead> 
										<tr> 
											<th width="58" align="center">Sr. No.</th> 
											<th>Action</th> 
											<th>Type</th>
											<th>Code</th> 
											<th>Account Type</th> 
											<th>Legal Name</th>
											<th>Category</th>
											<th>Business Category</th>
											<th>Status</th> 
											<th>Updated By</th> 
											<th>Rejected By</th> 
										</tr>  
									</thead>
									<tbody> 
										<?php //for($i=0; $i<count($rejected); $i++) { ?>
										<tr> 
											<td scope="row" align="center"><?php //echo $i+1; ?></td> 
											<td>
												<a href="<?php //echo Url::base() .'index.php?r=accountmaster%2Fview&id='.$rejected[$i]['id']; ?>" >View </a> <br/>
												<a href="<?php //echo Url::base() .'index.php?r=accountmaster%2Fedit&id='.$rejected[$i]['id']; ?>" style="<?php //if(isset($access[0]['r_edit'])) { if($access[0]['r_edit']=='1' && $access[0]['session_id']!=$rejected[$i]['approver_id']) echo ''; else echo 'display: none;'; } else { echo 'display: none;'; } ?>">Edit </a> <br/>
											</td> 
											<td><?php //echo $rejected[$i]['type']; ?></td> 
											<td><?php //echo $rejected[$i]['code']; ?></td> 
											<td><?php //echo $rejected[$i]['account_type']; ?></td> 
											<td><?php //echo $rejected[$i]['legal_name']; ?></td> 
											<td><?php //echo $rejected[$i]['acc_category']; ?></td>
											<td><?php //echo $rejected[$i]['bus_category']; ?></td> 
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