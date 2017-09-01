<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use yii\helpers\Url;
use yii\web\Session;

AppAsset::register($this);
$session = Yii::$app->session;
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>

    <?php $this->head() ?>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<?php $this->beginBody() ?>
<div class="wrapper">
<header class="main-header">
    <!-- Logo -->
    <a href="<?php echo Url::base(); ?>index.php?r=welcome" class="logo">
        <img src="dist/img/logo.png" class=" "  >
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <!-- User Account: style can be found in dropdown.less -->
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="dist/img/boxed-bg.jpg" class="user-image" alt="User Image">
                        <span class="hidden-xs"><?php echo $session['username'];?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">
                            <img src="dist/img/boxed-bg.jpg" class="img-circle" alt="User Image">

                            <p>
                                <?php echo $session['username'];?>
                                <small>Member since March. 2016</small>
                            </p>
                        </li>

                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="#" class="btn btn-default btn-flat">Profile</a>
                            </div>
                            <div class="pull-right">
                                <a href="<?php echo Url::base(); ?>index.php?r=security%2Flogout" class="btn btn-default btn-flat" data-method="post">Sign out</a>
                            </div>
                        </li>
                    </ul>
                </li>
                <!-- Control Sidebar Toggle Button -->
            </ul>
        </div>
    </nav>
</header>

<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
    <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="dist/img/boxed-bg.jpg" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p><?php echo $session['username'];?></p>
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>
        <!-- search form -->
        <form action="#" method="get" class="sidebar-form" style="display: none;">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search...">
                <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
                </span>
            </div>
        </form>
        <!-- /.search form -->
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <!-- <ul class="sidebar-menu"> 
            <li style="<?php //if(isset($access['S_Account_Master']['r_view'])) {if($access['S_Account_Master']['r_view']=='0') echo 'display: none;';} else  echo 'display: none;'; ?>">
                <a href="<?php //echo Url::base(); ?>index.php?r=accountmaster%2Findex"><i class="fa fa-book"></i> <span>Account Master</span></a>
            </li> 
            <li style="<?php //if(isset($access['S_Purchase']['r_view'])) {if($access['S_Purchase']['r_view']=='0') echo 'display: none;';} else  echo 'display: none;'; ?>">
                <a href="<?php //echo Url::base(); ?>index.php?r=pendinggrn%2Findex"><i class="fa fa-credit-card"></i> <span>Purchase</span></a>
            </li>
            <li style="<?php //if(isset($access['S_Journal_Voucher']['r_view'])) {if($access['S_Journal_Voucher']['r_view']=='0') echo 'display: none;';} else  echo 'display: none;'; ?>">
                <a href="<?php //echo Url::base(); ?>index.php?r=journalvoucher%2Findex"><i class="fa fa-book"></i> <span>Journal Voucher</span></a>
            </li>
            <li style="<?php //if(isset($access['S_Payment_Receipt']['r_view'])) {if($access['S_Payment_Receipt']['r_view']=='0') echo 'display: none;';} else  echo 'display: none;'; ?>">
                <a href="<?php //echo Url::base(); ?>index.php?r=paymentreceipt%2Findex"><i class="fa fa-money"></i> <span>Payment / Receipt</span></a>
            </li> 
            <li style="<?php //if(isset($access['S_User_Roles']['r_view'])) {if($access['S_User_Roles']['r_view']=='0') echo 'display: none;';} else  echo 'display: none;'; ?>">
                <a href="<?php //echo Url::base(); ?>index.php?r=userrole%2Findex"><i class="fa fa-book"></i> <span>User Roles</span></a>
            </li>
            <li class="treeview" style="<?php //if(isset($access['S_Reports']['r_view'])) {if($access['S_Reports']['r_view']=='0') echo 'display: none;';} else  echo 'display: none;'; ?>">
                <a href="#">
                    <i class="fa fa-bug"></i> <span>Reports</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li class="active"><a href="<?php //echo Url::base(); ?>index.php?r=accreport%2Fledgerreport"><i class="fa fa-circle-o"></i> Ledger</a></li>
                    <li><a href="<?php //echo Url::base(); ?>index.php?r=accreport%2Ftrialbalancereport"><i class="fa fa-circle-o"></i> Trial Balance </a></li>
                </ul>
            </li>
        </ul> -->

        <ul class="sidebar-menu"> 
            <li style="<?php if(isset($session['S_Account_Master'])) {if($session['S_Account_Master']=='0') echo 'display: none;';} else  echo 'display: none;'; ?>">
                <a href="<?php echo Url::base(); ?>index.php?r=accountmaster%2Findex"><i class="fa fa-book"></i> <span>Account Master</span></a>
            </li> 
            <li style="<?php if(isset($session['S_Purchase'])) {if($session['S_Purchase']=='0') echo 'display: none;';} else  echo 'display: none;'; ?>">
                <a href="<?php echo Url::base(); ?>index.php?r=pendinggrn%2Findex"><i class="fa fa-credit-card"></i> <span>Purchase</span></a>
            </li>
            <li style="<?php if(isset($session['S_Journal_Voucher'])) {if($session['S_Journal_Voucher']=='0') echo 'display: none;';} else  echo 'display: none;'; ?>">
                <a href="<?php echo Url::base(); ?>index.php?r=journalvoucher%2Findex"><i class="fa fa-book"></i> <span>Journal Voucher</span></a>
            </li>
            <li style="<?php if(isset($session['S_Payment_Receipt'])) {if($session['S_Payment_Receipt']=='0') echo 'display: none;';} else  echo 'display: none;'; ?>">
                <a href="<?php echo Url::base(); ?>index.php?r=paymentreceipt%2Findex"><i class="fa fa-money"></i> <span>Payment / Receipt</span></a>
            </li> 
            <li style="<?php if(isset($session['S_Email_Log'])) {if($session['S_Email_Log']=='0') echo 'display: none;';} else  echo 'display: none;'; ?>">
                <a href="<?php echo Url::base(); ?>index.php?r=emaillog%2Findex"><i class="fa fa-money"></i> <span>Email Log</span></a>
            </li> 
            <li class="treeview" style="<?php if(isset($session['S_User_Roles'])) {if($session['S_User_Roles']=='0') echo 'display: none;';} else  echo 'display: none;'; ?>">
                <a href="#">
                    <i class="fa fa-bug"></i> <span>User Roles</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li class="active"><a href="<?php echo Url::base(); ?>index.php?r=userrole%2Findex"><i class="fa fa-circle-o"></i> Create</a></li>
                    <li><a href="<?php echo Url::base(); ?>index.php?r=assignrole%2Findex"><i class="fa fa-circle-o"></i> Assign </a></li>
                </ul>
            </li>
            <!-- <li style="<?php //if(isset($session['S_User_Roles'])) {if($session['S_User_Roles']=='0') echo 'display: none;';} else  echo 'display: none;'; ?>">
                <a href="<?php //echo Url::base(); ?>index.php?r=userrole%2Findex"><i class="fa fa-book"></i> <span>User Roles</span></a>
            </li> -->
            <li class="treeview" style="<?php if(isset($session['S_Reports'])) {if($session['S_Reports']=='0') echo 'display: none;';} else  echo 'display: none;'; ?>">
                <a href="#">
                    <i class="fa fa-bug"></i> <span>Reports</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li class="active"><a href="<?php echo Url::base(); ?>index.php?r=accreport%2Fledgerreport"><i class="fa fa-circle-o"></i> Ledger</a></li>
                    <li><a href="<?php echo Url::base(); ?>index.php?r=accreport%2Ftrialbalancereport"><i class="fa fa-circle-o"></i> Trial Balance </a></li>
                </ul>
            </li>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>

<div class="content-wrapper">
    <?php
    // NavBar::begin([
    //     'brandLabel' => 'My Company',
    //     'brandUrl' => Yii::$app->homeUrl,
    //     'options' => [
    //         'class' => 'navbar-inverse navbar-fixed-top',
    //     ],
    // ]);
    // echo Nav::widget([
    //     'options' => ['class' => 'navbar-nav navbar-right'],
    //     'items' => [
    //         ['label' => 'Home', 'url' => ['/site/index']],
    //         ['label' => 'About', 'url' => ['/site/about']],
    //         ['label' => 'Contact', 'url' => ['/site/contact']],
    //         Yii::$app->user->isGuest ? (
    //             ['label' => 'Login', 'url' => ['/site/login']]
    //         ) : (
    //             '<li>'
    //             . Html::beginForm(['/site/logout'], 'post')
    //             . Html::submitButton(
    //                 'Logout (' . Yii::$app->user->identity->username . ')',
    //                 ['class' => 'btn btn-link logout']
    //             )
    //             . Html::endForm()
    //             . '</li>'
    //         )
    //     ],
    // ]);
    // NavBar::end();
    ?>

    <div class="">
        <section class="content-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        </section>
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-body">
                            <div class="row">
                                <?= $content ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<footer class="main-footer">
    <div class="pull-right hidden-xs">
        <b>Version</b> 2.3.12
    </div>
    &copy; Primarc Pecan <?= date('Y') ?>
</footer>

</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
