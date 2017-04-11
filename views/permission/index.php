<?php

use yii\helpers\Html;
//use yii\grid\GridView; 



use kartik\widgets\DynaGrid;
use kartik\grid\GridView;



$this->title = 'Permissions';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="permission-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

   

    <?php 
   
   $columns = [
          //   ['class' => 'kartik\grid\ActionColumn','template'=>'{update}'],
            ['class' => 'kartik\grid\SerialColumn'],

            //'id',
            'permission_name',
            'permission_code',
            //'created_at',
            //'updated_at',


       ];

      

    
    echo \kartik\dynagrid\DynaGrid::widget([
        
        'columns' => [
           ['class' => 'kartik\grid\ActionColumn','template'=>'{update}'],
            ['class' => 'kartik\grid\SerialColumn'],

            //'id',
            'permission_name',
            'permission_code',
            //'created_at',
            //'updated_at',

           
        ],
        
        'gridOptions'=>
        [
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
           
          //  'showPageSummary'=>true,
            'panel'=>
            [
                'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-book"></i>  Permission</h3>',
                'before' =>  '<div style="padding-top: 7px;"></div>',
                'after' => false
            ],   
            'toolbar' =>  
            [
                ['content'=>
                    Html::a('<i class="glyphicon glyphicon-plus"></i>', ['create'], ['class' => 'btn btn-success']). ' '
                    //Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['dynagrid-demo'], ['data-pjax'=>0, 'class' => 'btn btn-default', 'title'=>'Reset Grid'])
                ],
               // ['content'=>'{dynagrid}'],
            ],
                
        ],    
          'options'=>['id'=>'dynagrid-brand'],
    ]); ?>
</div>
