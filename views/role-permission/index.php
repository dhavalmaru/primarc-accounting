<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\RolePermissionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Role Permissions';
$this->params['breadcrumbs'][] = $this->title;
$permission=new \common\models\CommonCode();
$export="";

$session = Yii::$app->session;
$userPermission=  json_decode($session->get('userPermission'));

if($permission->canAccess("user-create") || $userPermission->isSystemAdmin)
{
   $create=
                Html::a('<i class="glyphicon glyphicon-plus"></i>', ['create'], ['class' => 'btn btn-success']). ' ';
                //Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['dynagrid-demo'], ['data-pjax'=>0, 'class' => 'btn btn-default', 'title'=>'Reset Grid'])
           
   // $export='{export}';
}else{
   $create="";
}
if($permission->canAccess("user-update")){
    
     $update=['class' => 'yii\grid\ActionColumn','template'=>'{update}'];
     
}else{
    $update=[];
}
?>
<div class="role-permission-index">
<?php echo $create?>

    <?php  echo GridView::widget([
          'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
        'columns' => [
           $update,
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
          //  'role_id',
            'role_name',
            'organizationName',
           // 'permission_id',
        //    'resourceName',
            'marketplaceName',
            'categoryName',
            // 'created_by',
            // 'updated_by',
            // 'created_at',
            // 'updated_at',
           
        ],
      
      
    ]); ?>

</div>
