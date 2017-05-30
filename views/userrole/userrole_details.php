<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\jui\Autocomplete;

use yii\jui\DatePicker;
use yii\web\JsExpression;
use yii\db\Query;

// use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel app\models\GrnSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Role';
$this->params['breadcrumbs'][] = $this->title;
$mycomponent = Yii::$app->mycomponent;
?>
<style type="text/css">
#user_role .error {color: #dd4b39!important;}
input:-webkit-autofill {
    background-color: white !important;
}
select{
	width: 100%;
}
.form-devident { margin-top: 10px; }
.form-horizontal .control-label {font-size: 12px; letter-spacing: .5px; margin-top:5px; }
.form-devident { margin-top: 10px; }
.table-hover>tbody>tr:hover {
    background:none!important;
}
table tr td { border: 1px solid #eee!important; }
</style>

<div class="grn-index"> 
	<div class=" col-md-12 ">  
		<form id="user_role" class="form-horizontal" action="<?php echo Url::base(); ?>index.php?r=userrole%2Fsave" method="post" enctype="multipart/form-data" onkeypress="return event.keyCode != 13;"> 
			<input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />

			<div class="form-group"  >
				<div class="row ">
					<div class=" col-md-3 col-sm-3 col-xs-12">
						<label class="control-label">Role</label>
						<div class=" ">
							<div class=" "> 
								<input type="hidden" id="id" name="id" value="<?php if(isset($data)) echo $data[0]['id']; ?>" />
								<input class="form-control" type="text" id="role" name="role" value="<?php if(isset($data)) echo $data[0]['role']; ?>" /> 
							</div>
						</div>
					</div>
					<div class=" col-md-6 col-sm-6 col-xs-12">
						<label class="control-label">Role Description</label>
						<div class=" ">
							<div class=" ">  
								<input class="form-control" type="text" id="description" name="description" value="<?php if(isset($data)) echo $data[0]['description']; ?>" />
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="form-devident">
				<div class=" ">
					<div class=" ">
						<table class="table table-bordered" id="user_role_details">
							<thead>
								<tr>
									<th width="50">Module</th>
									<th width="50"><input type="checkbox" id="view" onchange="selectall(1);" value="1" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;View</th>
									<th width="50"><input type="checkbox" id="insert" onchange="selectall(2);" value="1" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Insert</th>
									<th width="50"><input type="checkbox" id="update" onchange="selectall(3);" value="1" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Update</th>
									<th width="75"><input type="checkbox" id="delete" onchange="selectall(4);" value="1" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Delete</th>
									<th width="75"><input type="checkbox" id="approval" onchange="selectall(5);" value="1" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Approval</th>
									<th width="75" class="hide"><input type="checkbox" id="export" onchange="selectall(6);" value="1" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Download</th>
								</tr>
							</thead>
							<tbody>
								<tr id="trow_2">
									<td>Account Master</td>
									<td class="center"><input type="checkbox" class="cls_chk" id="account_master_vw" name="view[]" value="0"  <?php if(isset($editoptions[0])) { if($editoptions[0]['r_view'] == 1) { echo 'checked';} } ?> /></td>
									<td class="center"><input type="checkbox" class="cls_chk" id="account_master_ins" name="insert[]" value="0" <?php if(isset($editoptions[0])) { if($editoptions[0]['r_insert'] == 1) { echo 'checked';} } ?>  /></td>
									<td class="center"><input type="checkbox" class="cls_chk" id="account_master_upd" name="update[]" value="0" <?php if(isset($editoptions[0])) { if($editoptions[0]['r_edit'] == 1) { echo 'checked';} } ?>  /></td>
									<td class="center"><input type="checkbox" class="cls_chk" id="account_master_del" name="delete[]" value="0" <?php if(isset($editoptions[0])) { if($editoptions[0]['r_delete'] == 1) { echo 'checked';} } ?>  /></td>
									<td class="center"><input type="checkbox" class="cls_chk" id="account_master_app" name="approval[]" value="0" <?php if(isset($editoptions[0])) { if($editoptions[0]['r_approval'] == 1) { echo 'checked';} } ?> /></td>
									<td class="center hide"><input type="checkbox" class="cls_chk" id="account_master_exp" name="export[]" value="0" <?php if(isset($editoptions[0])) { if($editoptions[0]['r_export'] == 1) { echo 'checked';} } ?> /></td>
								</tr>
								<tr id="trow_3">
									<td>Purchase</td>
									<td class="center"><input type="checkbox" class="cls_chk" id="purchase_vw" name="view[]" value="1" <?php if(isset($editoptions[1])) { if($editoptions[1]['r_view'] == 1) { echo 'checked';} } ?> /></td>
									<td class="center"><input type="checkbox" class="cls_chk" id="purchase_ins" name="insert[]" value="1" <?php if(isset($editoptions[1])) { if($editoptions[1]['r_insert'] == 1) { echo 'checked';} } ?> /></td>
									<td class="center"><input type="checkbox" class="cls_chk" id="purchase_upd" name="update[]" value="1" <?php if(isset($editoptions[1])) { if($editoptions[1]['r_edit'] == 1) { echo 'checked';} } ?> /></td>
									<td class="center"><input type="checkbox" class="cls_chk" id="purchase_del" name="delete[]" value="1" <?php if(isset($editoptions[1])) { if($editoptions[1]['r_delete'] == 1) { echo 'checked';} } ?>  /></td>
									<td class="center"><input type="checkbox" class="cls_chk" id="purchase_app" name="approval[]" value="1" <?php if(isset($editoptions[1])) { if($editoptions[1]['r_approval'] == 1) { echo 'checked';} } ?> /></td>
									<td class="center hide"><input type="checkbox" class="cls_chk" id="purchase_exp" name="export[]" value="1" <?php if(isset($editoptions[1])) { if($editoptions[1]['r_export'] == 1) { echo 'checked';} } ?> /></td>
									</td>
								</tr>
								<tr id="trow_4">
									<td>Journal Voucher</td>
									<td class="center"><input type="checkbox" class="cls_chk" id="journal_voucher_vw" name="view[]" value="2" <?php if(isset($editoptions[2])) { if($editoptions[2]['r_view'] == 1) { echo 'checked';} } ?>  /></td>
									<td class="center"><input type="checkbox" class="cls_chk" id="journal_voucher_ins" name="insert[]" value="2" <?php if(isset($editoptions[2])) { if($editoptions[2]['r_insert'] == 1) { echo 'checked';} } ?>  /></td>
									<td class="center"><input type="checkbox" class="cls_chk" id="journal_voucher_upd" name="update[]" value="2" <?php if(isset($editoptions[2])) { if($editoptions[2]['r_edit'] == 1) { echo 'checked';} } ?> /></td>
									<td class="center"><input type="checkbox" class="cls_chk" id="journal_voucher_del" name="delete[]" value="2" <?php if(isset($editoptions[2])) { if($editoptions[2]['r_delete'] == 1) { echo 'checked';} } ?>  /></td>
									<td class="center"><input type="checkbox" class="cls_chk" id="journal_voucher_app" name="approval[]" value="2" <?php if(isset($editoptions[2])) { if($editoptions[2]['r_approval'] == 1) { echo 'checked';} } ?> /></td>
									<td class="center hide"><input type="checkbox" class="cls_chk" id="journal_voucher_exp" name="export[]" value="2" <?php if(isset($editoptions[2])) { if($editoptions[2]['r_export'] == 1) { echo 'checked';} } ?> /></td>
								</tr>
								<tr id="trow_5">
									<td>Payment/Receipt</td>
									<td class="center"><input type="checkbox" class="cls_chk" id="payment_receipt_vw" name="view[]" value="3" <?php if(isset($editoptions[3])) { if($editoptions[3]['r_view'] == 1) { echo 'checked';} } ?>  /></td>
									<td class="center"><input type="checkbox" class="cls_chk" id="payment_receipt_ins" name="insert[]" value="3" <?php if(isset($editoptions[3])) { if($editoptions[3]['r_insert'] == 1) { echo 'checked';} } ?>  /></td>
									<td class="center"><input type="checkbox" class="cls_chk" id="payment_receipt_upd" name="update[]" value="3" <?php if(isset($editoptions[3])) { if($editoptions[3]['r_edit'] == 1) { echo 'checked';} } ?>  /></td>
									<td class="center"><input type="checkbox" class="cls_chk" id="payment_receipt_del" name="delete[]" value="3" <?php if(isset($editoptions[3])) { if($editoptions[3]['r_delete'] == 1) { echo 'checked';} } ?>  /></td>
									<td class="center"><input type="checkbox" class="cls_chk" id="payment_receipt_app" name="approval[]" value="3" <?php if(isset($editoptions[3])) { if($editoptions[3]['r_approval'] == 1) { echo 'checked';} } ?> /></td>
									<td class="center hide"><input type="checkbox" class="cls_chk" id="payment_receipt_exp" name="export[]" value="3" <?php if(isset($editoptions[3])) { if($editoptions[3]['r_export'] == 1) { echo 'checked';} } ?> /></td>
								</tr>
								<tr id="trow_6">
									<td>User Roles</td>
									<td class="center"><input type="checkbox" class="cls_chk" id="user_roles_vw" name="view[]" value="4" <?php if(isset($editoptions[4])) { if($editoptions[4]['r_view'] == 1) { echo 'checked';} } ?> /></td>
									<td class="center"><input type="checkbox" class="cls_chk" id="user_roles_ins" name="insert[]" value="4" <?php if(isset($editoptions[4])) { if($editoptions[4]['r_insert'] == 1) { echo 'checked';} } ?> /></td>
									<td class="center"><input type="checkbox" class="cls_chk" id="user_roles_upd" name="update[]" value="4" <?php if(isset($editoptions[4])) { if($editoptions[4]['r_edit'] == 1) { echo 'checked';} } ?>  /></td>
									<td class="center"><input type="checkbox" class="cls_chk" id="user_roles_del" name="delete[]" value="4" <?php if(isset($editoptions[4])) { if($editoptions[4]['r_delete'] == 1) { echo 'checked';} } ?> /></td>
									<td class="center"><input type="checkbox" class="cls_chk" id="user_roles_app" name="approval[]" value="4" <?php if(isset($editoptions[4])) { if($editoptions[4]['r_approval'] == 1) { echo 'checked';} } ?> /></td>
									<td class="center hide"><input type="checkbox" class="cls_chk" id="user_roles_exp" name="export[]" value="4" <?php if(isset($editoptions[4])) { if($editoptions[4]['r_export'] == 1) { echo 'checked';} } ?> /></td>
								</tr>
								<tr id="trow_7">
									<td> <span class="">   Reports </span> <!-- <a class="reports" href="javascript:void(0);"><span class="badge badge-info pull-right"> View Reports</span></a> --> </td>
									<td class="center"><input type="checkbox" class="cls_chk" id="rep_vw" name="view[]" value="5" <?php if(isset($editoptions[5])) { if($editoptions[5]['r_view'] == 1) { echo 'checked';} } ?>  /></td>
									<td colspan="5" class="center">&nbsp;</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<div class="  btn-container"> 
				<div class=" ">
					<button type="submit" class="btn btn-success btn-sm" >Submit For Approval</button>
					<a href="<?php echo Url::base(); ?>index.php?r=userrole%2Findex" class="btn btn-danger btn-sm" >Cancel</a>
					<!-- <button type="submit" class="btn btn-danger btn-sm" >Cancel </button> -->
				</div>
			</div>
		</form>
	</div>
</div>

<script type="text/javascript">
    var BASE_URL="<?php echo Url::base(); ?>";
</script>

<?php 
	$this->registerJsFile(
	    '@web/js/jquery-ui-1.11.2/jquery-ui.min.js',
	    ['depends' => [\yii\web\JqueryAsset::className()]]
	);
    $this->registerJsFile(
        '@web/js/user_role.js',
        ['depends' => [\yii\web\JqueryAsset::className()]]
    );
    // $this->registerJsFile(
    //     '@web/js/datatable.js',
    //     ['depends' => [\yii\web\JqueryAsset::className()]]
    // );
?>