<?php

/* @var $this \yii\web\View */
/* @var $content string */

// use dektrium\user\widgets\Connect;
use dektrium\user\models\LoginForm;
// use yii\helpers\Html;
// use yii\widgets\ActiveForm;

use yii\grid\GridView;
// use yii\helpers\Url;
use yii\jui\Autocomplete;

use yii\jui\DatePicker;
use yii\web\JsExpression;
use yii\db\Query;
use yii\bootstrap\Alert;

use yii\helpers\Html;
// use yii\bootstrap\Nav;
// use yii\bootstrap\NavBar;
// use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use yii\helpers\Url;

AppAsset::register($this);
// $session = Yii::$app->session;
// $this->title = Yii::t('user', 'Sign in');
?>
<?= $this->render('/_alert', ['module' => Yii::$app->getModule('user')]) ?>

<?php $this->beginPage() ?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title> <?php echo $this->title; ?> </title>
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
<?php $this->beginBody() ?>

<?= $content ?>
                                
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
