<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\RolePermission;

/**
 * RolePermissionSearch represents the model behind the search form about `backend\models\RolePermission`.
 */
class RolePermissionSearch extends RolePermission
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['role_id', 'created_by', 'updated_by'], 'integer'],
            [['role_name', 'organization_id', 'marketplace_id','po_category_id','category_id', 'created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = RolePermission::find();
        $session = Yii::$app->session;
         $company=$session->get('user.company');
        
        $userPermission=  json_decode($session->get('userPermission'));
        
        if($userPermission->isSystemAdmin)
        {
           $query->andFilterWhere([
                  'company_id'=>$company['id'],
                  'is_superadmin'=>1
            //'created_by' => $userPermission->user_id,
           
        ]); 
        }else{
            
            
        $query->andFilterWhere([
            'company_id'=>$company['id'],
            'is_superadmin'=>0
            //'created_by' => $userPermission->user_id,
            
        ]);
        }
       

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'role_id' => $this->role_id,
           // 'company_id'=>$company['id'],
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'role_name', $this->role_name])
            ->andFilterWhere(['like', 'organization_id', $this->organization_id])          
            ->andFilterWhere(['like', 'marketplace_id', $this->marketplace_id])
            ->andFilterWhere(['like', 'category_id', $this->category_id]);

        return $dataProvider;
    }
}
