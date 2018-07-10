<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use dektrium\user\models\UserSearch;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\web\View;
use yii\widgets\Pjax;

/**
 * @var View $this
 * @var ActiveDataProvider $dataProvider
 * @var UserSearch $searchModel
 */

$this->title = Yii::t('user', 'Manage users');
$this->params['breadcrumbs'][] = $this->title;
$permission=new \common\models\CommonCode();
$session = Yii::$app->session;
$userPermission=  json_decode($session->get('userPermission'));
if($permission->canAccess("user-update") || $userPermission->isSystemAdmin){
    
    $update='{update}';
     
}else{
    $update="";
}


  
?>

<div class="purchase-order-list">
    <div id="wrapper">
        <section>
            <div id="forAlert">

            </div>

            <div id="page-wrapper_remove" class="well1">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h2 class="pane-title">User <a href="/backend/web/usermanuals/UserManagement-usermanual.pdf" target="_blank"><sup><i class="glyphicon glyphicon-question-sign"></i></sup></a></h2>
                            </div>
                            <div class="panel-body">
                                <div class="filter-wrapper">
									<?= $this->render('/_alert', [
										'module' => Yii::$app->getModule('user'),
									]) ?>

									<?= $this->render('/admin/_menu') ?>

									<?php Pjax::begin() ?>

									<?= GridView::widget([
										'dataProvider' 	=> $dataProvider,
										'filterModel'  	=> $searchModel,
										'layout'  		=> "{items}\n{pager}",
										'columns' => [
											 [  
											'class' => 'yii\grid\ActionColumn',
										   //'contentOptions' => ['style' => 'width:260px;'],
											'header'=>'',
										   'template' => $update,
											'buttons' => [

												//view button
												/*'delete' => function ($url, $model) {
													//echo $model->id;
												   // echo "##".Yii::$app->user->id;die;
													 if($model->id!=Yii::$app->user->id){
													return Html::a('<span class="glyphicon glyphicon-trash" style="cursor:pointer"></span>', $url, [
																		            'title' => Yii::t('app', 'delete'), 'data-method' => 'post', 'data-confirm' => 'Are you sure you want to delete this item?'
																		]);
													}
				
								
												},*/
											],

											 'urlCreator' => function ($action, $model, $key, $index) {
																
																	if ($action === 'update') {
																		$url = Yii::$app->urlManager->createUrl(['user/admin/update', 'id' => $key]); // your own url generation logic
																		return $url;
																	}
																	/*if ($action == 'delete') {
																		$url = Yii::$app->urlManager->createUrl(['user/admin/delete', 'id' => $key]); // your own url generation logic
																		return $url;
																	}*/
																}
						
		
										   ],           
											'username',
											'email:email',
											 [
													'class' => '\yii\grid\DataColumn',
													'attribute' => 'role_id',
													'label'=>'Role Name',
													'value' => 'roleName',
													'filter' => $roleArray,
										],
										/*    [
												'attribute' => 'registration_ip',
												'value' => function ($model) {
													return $model->registration_ip == null
														? '<span class="not-set">' . Yii::t('user', '(not set)') . '</span>'
														: $model->registration_ip;
												},
												'format' => 'html',
											],*/
											[
												'attribute' => 'created_at',
												'value' => function ($model) {
													if (extension_loaded('intl')) {
														return Yii::t('user', '{0, date, MMMM dd, YYYY HH:mm}', [$model->created_at]);
													} else {
														return date('Y-m-d G:i:s', $model->created_at);
													}
												},
												'filter' => DatePicker::widget([
													'model'      => $searchModel,
													'attribute'  => 'created_at',
													'dateFormat' => 'php:Y-m-d',
													'options' => [
														'class' => 'form-control',
													],
												]),
											],
										  /*  [
												'header' => Yii::t('user', 'Confirmation'),
												'value' => function ($model) {
													if ($model->isConfirmed) {
														return '<div class="text-center"><span class="text-success">' . Yii::t('user', 'Confirmed') . '</span></div>';
													} else {
														return Html::a(Yii::t('user', 'Confirm'), ['confirm', 'id' => $model->id], [
															'class' => 'btn btn-xs btn-success btn-block',
															'data-method' => 'post',
															'data-confirm' => Yii::t('user', 'Are you sure you want to confirm this user?'),
														]);
													}
												},
												'format' => 'raw',
												'visible' => Yii::$app->getModule('user')->enableConfirmation,
											],
											[
												'header' => Yii::t('user', 'Block status'),
												'value' => function ($model) {
													if ($model->isBlocked) {
														return Html::a(Yii::t('user', 'Unblock'), ['block', 'id' => $model->id], [
															'class' => 'btn btn-xs btn-success btn-block',
															'data-method' => 'post',
															'data-confirm' => Yii::t('user', 'Are you sure you want to unblock this user?'),
														]);
													} else {
														return Html::a(Yii::t('user', 'Block'), ['block', 'id' => $model->id], [
															'class' => 'btn btn-xs btn-danger btn-block',
															'data-method' => 'post',
															'data-confirm' => Yii::t('user', 'Are you sure you want to block this user?'),
														]);
													}
												},
												'format' => 'raw',
											],*/
										   
										],
									]); ?>

								<?php Pjax::end() ?>
								 </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
