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
#assign_role .error {color: #dd4b39!important;}
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
		<form id="assign_role" class="form-horizontal" action="<?php echo Url::base(); ?>index.php?r=assignrole%2Fsave" method="post" enctype="multipart/form-data" onkeypress="return event.keyCode != 13;"> 
			<input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />

			<div class="form-group"  >
				<div class="row ">
					<div class=" col-md-3 col-sm-3 col-xs-12">
						<label class="control-label">User</label>
						<div class=" ">
							<div class=" "> 
								<input type="hidden" id="id" name="id" value="<?php if(isset($data)) echo $data[0]['id']; ?>" />
								<select id="user_id" name="user_id" class="form-control">
									<option value="">Select</option>
									<?php for($i=0; $i<count($users); $i++) { ?>
										<option value="<?php echo $users[$i]['id']; ?>" <?php if(isset($data[0])) { if($data[0]['user_id']==$users[$i]['id']) echo "selected"; } ?>><?php echo $users[$i]['username']; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
					</div>
					<div class=" col-md-6 col-sm-6 col-xs-12">
						<label class="control-label">Role</label>
						<div class=" ">
							<div class=" ">  
								<select id="role_id" name="role_id" class="form-control">
									<option value="">Select</option>
									<?php for($i=0; $i<count($roles); $i++) { ?>
										<option value="<?php echo $roles[$i]['id']; ?>" <?php if(isset($data[0])) { if($data[0]['role_id']==$roles[$i]['id']) echo "selected"; } ?>><?php echo $roles[$i]['role']; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="  btn-container"> 
				<div class=" ">
					<button type="submit" class="btn btn-success btn-sm" >Submit For Approval</button>
					<a href="<?php echo Url::base(); ?>index.php?r=assignrole%2Findex" class="btn btn-danger btn-sm" >Cancel</a>
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
?>