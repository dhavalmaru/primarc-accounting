<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

$this->title = 'Email Log';
$this->params['breadcrumbs'][] = $this->title;
$mycomponent = Yii::$app->mycomponent;
?>

<div class="grn-index"> 
	<div class=" col-md-12">
		<div class="panel with-nav-tabs panel-primary">
			<!-- <div class="panel-heading">
				<ul class="nav nav-tabs">
					<li class="active"><a href="#tab1primary" data-toggle="tab"> Pending (<?php //echo count($data); ?>)</a></li>
					<li><a href="#tab2primary" data-toggle="tab"> Approved (<?php //echo count($approved); ?>)</a></li>
					<li><a href="#tab3primary" data-toggle="tab"> Rejected (<?php //echo count($rejected); ?>)</a></li>
				</ul>
			</div> -->
			<div class="panel-body" style="padding-top: 0px;">
				<div  id="loader" > </div>
   				<div class="loading">
					<!-- <div class="tab-content">
						<div class="tab-pane fade in active" id="tab1primary"> -->
							<div class="bs-example grn-index" data-example-id="bordered-table"  >  
								<table id="example" class="table datatable table-bordered display" cellspacing="0" width="100%">
									<thead> 
										<tr> 
											<th width="45">Sr. No.</th> 
											<th>Email Type</th> 
											<th>vendor_name</th> 
											<th>From</th> 
											<th>To</th> 
											<th>Email Content</th> 
											<th>Email Status</th> 
											<th>Email By</th> 
											<th>Email Date</th>
										</tr>  
									</thead>
									<tbody id="grn_details"> 
										<?php for($i=0; $i<count($data); $i++) { ?>
										<tr> 
											<td scope="row"><?php echo $i+1; ?></td>
											<td><?php echo $data[$i]['email_type']; ?></td> 
											<td><?php echo $data[$i]['vendor_name']; ?></td> 
											<td><?php echo $data[$i]['from_email_id']; ?></td> 
											<td><?php echo $data[$i]['to_recipient']; ?></td> 
											<td><?php echo $data[$i]['email_content']; ?></td> 
											<td><?php echo (($data[$i]['email_sent_status']=='1')?'Sent':'Failed'); ?></td> 
											<td><?php echo $data[$i]['supporting_user']; ?></td> 
											<td><?php if(isset($data[$i]['email_date'])) echo (($data[$i]['email_date']!=null && $data[$i]['email_date']!='')?date('d/m/Y',strtotime($data[$i]['email_date'])):''); ?></td> 
										</tr> 
										<?php } ?>
									</tbody> 
								</table>
							</div>
						<!-- </div>
					</div> -->
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