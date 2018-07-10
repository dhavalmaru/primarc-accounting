<?php

/*
 * This file is part of the Dektrium project
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use yii\bootstrap\Nav;
$items = ['label'   => Yii::t('user', 'Users'),    'url'     => ['/user/admin/index']];
$permission=new \common\models\CommonCode();
$session = Yii::$app->session;
$userPermission=  json_decode($session->get('userPermission'));
if($permission->canAccess("user-create") || $userPermission->isSystemAdmin)
{
  $create=[
            'label' => Yii::t('user', 'Create'),
            'items' => [
                [
                    'label'   => Yii::t('user', 'New user'),
                    'url'     => ['/user/admin/create'],
                ],
          
            ],
        ];
    
}else{
    $create="";
    $items = [
            'label'   => Yii::t('user', 'Users'),
        ];
}
 

?>

<?= Nav::widget([
    'options' => [
        'class' => 'nav-tabs',
        'style' => 'margin-bottom: 15px',
    ],
    'items' => [
        $items,
      /*  [
            'label'   => Yii::t('user', 'Roles'),
            'url'     => ['/rbac/role/index'],
            'visible' => isset(Yii::$app->extensions['dektrium/yii2-rbac']),
        ],
        [
            'label' => Yii::t('user', 'Permissions'),
            'url'   => ['/rbac/permission/index'],
            'visible' => isset(Yii::$app->extensions['dektrium/yii2-rbac']),
        ],*/
       $create,
    ],
]) ?>
