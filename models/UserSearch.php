<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace backend\models;


use dektrium\user\models\UserSearch as BaseUserSearch;
use yii\data\ActiveDataProvider;
/**
 * UserSearch represents the model behind the search form about User.
 */
class UserSearch extends BaseUserSearch
{
   
 
    
    /** @inheritdoc */
    public $role_id;
    public $company_id;
    
  
    /** @inheritdoc */
    public function rules()
    {
       // $rules = parent::rules();
        // add some rules
        return [
            [['username','email', 'registration_ip', 'created_at','role_id','company_id'], 'safe']            
            
        ];
      

      
        
        
    }

    /** @inheritdoc */
    public function attributeLabels()
    {
        return [
            'username'        => Yii::t('user', 'Username'),
            'email'           => Yii::t('user', 'Email'),
            'created_at'      => Yii::t('user', 'Registration time'),
            'registration_ip' => Yii::t('user', 'Registration ip'),
            'role_id' => Yii::t('user', 'Role Name'),
            'company_id' => Yii::t('user', 'Company Name'),
        ];
    }

    

    /**
     * @param $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
       // $query = $this->finder->getUserQuery();
        $session = \Yii::$app->session;
        $userPermission=  json_decode($session->get('userPermission'));
        $company=$session->get('user.company');
        
//var_dump($company);die;
        $userId=array();
         $connection = \Yii::$app->db;
          
       if(!$userPermission->isSystemAdmin)
       {
            $command = $connection->createCommand("select group_concat(user_id) as user_id from user_company_rel where company_id=" . $company['id'] . " group by company_id"
            );

            $dataReader = $command->queryAll();
            if($dataReader)
            $userId=explode(",",$dataReader[0]['user_id']);
       } 
        
        
       $query = User::find('username,email,registration_ip,created_at,roles.id as role_id,roles.role_name');
      
       $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => array('pageSize' => 20),
           
             'sort'=> ['defaultOrder' => ['updated_at'=>SORT_DESC]]
        ]);
       
       $query->innerJoin('user_roles', 'user_roles.user_id=user.id');
       
       $query->innerJoin('roles', 'roles.id=user_roles.role_id');
		
     /*   $dataProvider->sort->attributes['roleName'] = [
            'asc' => ["roles.role_name" => SORT_ASC],
            'desc' => ["roles.role_name" => SORT_DESC],
        ];*/
        
       if(count($userId))
          $query->andFilterWhere(['user.id'=>$userId]);
        
       if($userPermission->isSystemAdmin)
       {
           $query->andFilterWhere(['user.is_superadmin'=>1]);
           $query->orFilterWhere(['user.id'=>$userPermission->user_id]);
           
       }
       //var_dump($dataProvider->query);die;
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        if ($this->created_at !== "") {
	
            $date = strtotime($this->created_at);
            $query->andFilterWhere(['between', 'created_at', $date, $date + 3600 * 24]);
		
        }
        
         $query->andFilterWhere(['roles.id'=>$this->role_id]);

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'email', $this->email])
       //   ->andFilterWhere(['like', 'roles.id', $this->rol])
            ->andFilterWhere(['registration_ip' => $this->registration_ip]);
    		
        return $dataProvider;
    }
}
