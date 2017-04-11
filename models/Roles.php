<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "roles".
 *
 * @property integer $id
 * @property string $role_name
 * @property string $role_desc
 * @property string $cre_date
 * @property string $mod_date
 * @property integer $cre_user
 * @property integer $mod_user
 * @property string $role_code
 */
class Roles extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'roles';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['role_name', 'cre_user', 'mod_user', 'role_code'], 'required'],
            [['cre_date', 'mod_date'], 'safe'],
            [['cre_user', 'mod_user'], 'integer'],
            [['role_name'], 'string', 'max' => 100],
            [['role_desc'], 'string', 'max' => 500],
            [['role_code'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'role_name' => 'Role Name',
            'role_desc' => 'Role Desc',
            'cre_date' => 'Cre Date',
            'mod_date' => 'Mod Date',
            'cre_user' => 'Cre User',
            'mod_user' => 'Mod User',
            'role_code' => 'Role Code',
        ];
    }
    
    /** get role id 
     * @param string $roleName
     * @param integer $company_id
     * @return integer $role_id   
     */
    
    public function getRoleId($roleName,$company_id)
    {
        $session = Yii::$app->session;        
        $userPermission=  json_decode($session->get('userPermission'));
        $company_id=$userPermission->company_id;
        if($userPermission->isSystemAdmin)
        {
           // $company= CompanyMaster::find()->where(['id'=>$company_id])->asArray()->one();
            $is_superadmin=1;
            
        }else
        {
            $is_superadmin=0;
       // $company=$session->get('user.company');
        }
        
        $roleObj=  Roles::find()->where(['role_name'=>$roleName,'company_id'=>$company_id])->one();
        
        if($roleObj)
        {
            return $roleObj->id;
            
        }else{
            
            $roleObj=new Roles();
            $roleObj->role_desc=$roleName." for ".$userPermission->company_name;
            $roleObj->role_name=$roleName;
            $roleObj->company_id=$userPermission->company_id;
            $roleObj->role_code=$roleName."-".$userPermission->company_code;
            $roleObj->cre_date=date("Y-m-d H:i:s");
            $roleObj->mod_date=date("Y-m-d H:i:s");
            $roleObj->cre_user=Yii::$app->user->id;
            $roleObj->mod_user=Yii::$app->user->id;
            $roleObj->is_superadmin=$is_superadmin;
            $roleObj->save();
            
            return $roleObj->id;
            
        }
        
        
        
    }
    
    /** update role id
     * @param string $updateRoleName
     * @param integer $role_id     
     */
    
    
    public function getUpdateRole($role_id,$updateRoleName)
    {
            $session = Yii::$app->session;
            $company=$session->get('user.company');
             
            
            $connection = \Yii::$app->db;
            
            $role_code=$updateRoleName."-".$company['company_code'];		
            $connection->createCommand()
			->update('roles', ['role_name' => $updateRoleName,'role_code'=>$role_code], 'id ='.$role_id)
			->execute();

         
        
    }
}
