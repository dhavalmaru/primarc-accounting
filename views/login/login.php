<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\jui\Autocomplete;

use yii\jui\DatePicker;
use yii\web\JsExpression;
use yii\db\Query;
use yii\bootstrap\Alert;

// use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel app\models\GrnSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Primarc Pecan';
// $this->params['breadcrumbs'][] = $this->title;
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title> Primarc Pecan </title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="<?php echo Url::base(); ?>bootstrap/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo Url::base(); ?>dist/css/AdminLTE.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="<?php echo Url::base(); ?>plugins/iCheck/square/blue.css">


  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
<style>
.form-control { height:auto;}
.icheck>label { padding-left:20px;}
</style>
</head>
<body class="hold-transition login-page">
	<div class="login-box">
		<div class="login-logo">
			<a href="#"><b>Primarc</b> Pecan</a>
		</div>
	  	<!-- /.login-logo -->
	  	<div class="login-box-body">
	  		<form id="login_form" action="<?php echo Url::base(); ?>index.php?r=login%2Flogin" method="post">
	  			<input type="hidden" id="csrf_token" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
	  			<div class="form-group has-feedback">
	  				<span class="glyphicon glyphicon-envelope form-control-feedback"></span>
	  				<input type="email" name="uname" id="uname" class="form-control" placeholder="Email">
	  			</div>
	  			<div class="form-group has-feedback">
	  				<span class="glyphicon glyphicon-lock form-control-feedback"></span>
	  				<input type="password" name="upass" id="upass" class="form-control" placeholder="Password">
	  				<a href="#">I forgot my password</a><br>
	  			</div>
	  			<div class="row">
	  				<div class="col-xs-8">
	  					<div class="checkbox icheck">
	  						<label>
	  							<input type="checkbox"> Remember Me
	  						</label>
	  					</div>
	  				</div>
	  				<!-- /.col -->
	  				<div class="col-xs-4">
	  					<button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
	  				</div>
	  				<!-- /.col -->
	  			</div>
	  		</form>
	    	<!-- /.social-auth-links -->
	  	</div>
	  	<!-- /.login-box-body -->
	</div>
	<!-- /.login-box -->

<script type="text/javascript">
    var BASE_URL="<?php echo Url::base(); ?>";
</script>

<!-- jQuery 2.2.3 -->
<script src="<?php echo Url::base(); ?>plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="<?php echo Url::base(); ?>bootstrap/js/bootstrap.min.js"></script>
<!-- iCheck -->
<script src="<?php echo Url::base(); ?>plugins/iCheck/icheck.min.js"></script>

<script src="<?php echo Url::base(); ?>js/plugins/jquery-validation/jquery.validate.js"></script>
<script src="<?php echo Url::base(); ?>js/login.js"></script>

<script>
  $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' // optional
    });
  });
</script>
</body>
</html>

<?php 
	// $this->registerJsFile(
	//     '@web/js/jquery-ui-1.11.2/jquery-ui.min.js',
	//     ['depends' => [\yii\web\JqueryAsset::className()]]
	// );
 //    $this->registerJsFile(
 //        '@web/js/account_master.js',
 //        ['depends' => [\yii\web\JqueryAsset::className()]]
 //    );
    // $this->registerJsFile(
    //     '@web/js/datatable.js',
    //     ['depends' => [\yii\web\JqueryAsset::className()]]
    // );
?>
