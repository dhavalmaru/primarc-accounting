<?php

namespace backend\models;

use yii\base\ErrorException;

use Yii;

/**
 * This is the model class for table "role_permission".
 *
 * @property integer $id
 * @property integer $role_id
 * @property string $role_name
 * @property string $orgnazation_id
 * @property string $operation_id
 * @property string $resource_id
 * @property string $marketplace_id
 * @property string $category_id
 * @property integer $created_by
 * @property integer $updated_by
 * @property string $created_at
 * @property string $updated_at
 */
class RolePermission extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'role_permission';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['role_id', 'role_name', 'created_by', 'updated_by', 'created_at'], 'required'],
            [['role_id', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at','is_superadmin'], 'safe'],
            [['role_name'], 'string', 'max' => 150],
            [['role_name'], 'unique'],
            [['organization_id'], 'required', 'isEmpty' => function ($value) {
            return empty($value);
        }],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'role_id' => 'Role ID',
            'role_name' => 'Role Name',
            'organization_id' => 'Organization',
            'permission_id' => 'Permission',
            'reports'=>"Authorize",
            'transactions'=>"Authorize",
            'resource_id' => 'Resource',
            'authorize_b2c_sales'=>"Authorize B2C Sale",
            'authorize_live_dashboard'=>'Authorize Live Dashboard',
            'marketplace_id' => 'Marketplace',
            'auth_permission_id' => 'Authorize Permission',            
            'category_id' => 'Category',
            'warehouse_id' => 'Location',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
    
    /** get current organization name if set otherwise "-"
    *
     */

    public function getOrganizationName() {
        
        if ($this->organization_id) {
           $connection = \Yii::$app->db;

            $command = $connection->createCommand("select group_concat(organization_name) as name,'1' as custom from organization  where id in (" . $this->organization_id . ") group by custom"
            );

            $dataReader = $command->queryAll();

            // var_dump($dataReader);die;

            return $dataReader[0]['name'];
        } else {

            return "-";
        }
    }
    
    /** get current market place name if set otherwise "-"
    *
    */

    public function getMarketplaceName() {

        if ($this->marketplace_id) {
           $connection = \Yii::$app->db;

            $command = $connection->createCommand("select group_concat(market_name) as name,'1' as custom from market_master where id in (" . $this->marketplace_id . ") group by custom"
            );

            $dataReader = $command->queryAll();
            return $dataReader[0]['name'];
        } else {

            return "-";
        }
    }
    
     /** get current permission name if set otherwise "-"
    *
    */

    public function getPermissionName() {
        
        if ($this->permission_id) {
           $connection = \Yii::$app->db;
            $command = $connection->createCommand("select group_concat(permission_name) as name,'1' as custom from permission where id in (" . $this->permission_id . ") group by custom"
            );

            $dataReader = $command->queryAll();
            return $dataReader[0]['name'];
        } else {

            return "-";
        }
    }
    
    
    /** get current resource name if set otherwise "-"
    *
    */

    public function getResourceName() {
        
        if ($this->resource_id) {
            $connection = \Yii::$app->db;

            $command = $connection->createCommand("select group_concat(resource_name) as name,'1' as custom from resource where id in (" . $this->resource_id . ") group by custom"
            );

            $dataReader = $command->queryAll();
            return $dataReader[0]['name'];
        } else {

            return "-";
        }
    }
    
     /** get current category name if set otherwise "-"
    *
    */

    public function getCategoryName() {

        if ($this->category_id) {
            $connection = \Yii::$app->db;

            $command = $connection->createCommand("select group_concat(category_code) as name,'1' as custom from product_main_category where id in (" . $this->category_id . ") group by custom"
            );

            $dataReader = $command->queryAll();
            return $dataReader[0]['name'];
        } else {

            return "-";
        }
    }
    
    /** fetch category id from category name
    * @param array  $stringArray 
    * @return array $categoryIdArray
    */
    
    
    public function getCategoryMKTWareHouseId($stringArray)
    {
        $categoryIdArray=array();
        foreach($stringArray as $string)
        {
            $id=explode("-",$string);
            array_push($categoryIdArray,trim($id[count($id)-1]));
            
        }
        
       $categoryIdArray=array_unique($categoryIdArray);
       
       return $categoryIdArray;
    }
    
    /** get screen url from input permission
    * @param array  $param 
    * @return array $resourcescreen resource screen name
    */
    
    function getScreenUrl($param)
    {
        
        $getresource=  Resource::find()->asArray()->all();
        $resourcescreen=array();
        foreach($getresource as $res)
        {
            $resource_code=$res['resource_code'];
            if(isset($param[$resource_code]))
            {
                 $resourcescreen=array_merge($resourcescreen,$param[$resource_code]);
                
            }
            
            
        }
        
        if(isset($param['product-category']))
        {
             $resourcescreen=array_merge($resourcescreen,$param['product-category']);
            
        }
        if(isset($param['market-place-product-master']))
        {
             $resourcescreen=array_merge($resourcescreen,$param['market-place-product-master']);
            
        }
         if(isset($param['warehouse-master']))
        {
             $resourcescreen=array_merge($resourcescreen,$param['warehouse-master']);
            
        }
        
         if(isset($param['purchase-order-category']))
        {
             $resourcescreen=array_merge($resourcescreen,$param['purchase-order-category']);
            
        }
        
        if(isset($param['reportpermission-master']))
        {
             $resourcescreen=array_merge($resourcescreen,$param['reportpermission-master']);
            
        }
       
        return $resourcescreen;
    
    }
    
    /** set user permission screen 
    * @param string $roleName
    * @param array  $screens    
    */
    
    public function setUserScreen($roleName,$screens)
    {
        
        $screensObj= explode(",",$screens);
        
        $session = Yii::$app->session;
        $company=$session->get('user.company');
        
        $auth = Yii::$app->authManager;
        
        $roleName=$roleName."-".$company['company_code'];
        $role = $auth->createRole($roleName);
        try{
            
            $auth->add($role);
        
         }catch(\yii\db\Exception $e){
           
             Yii::warning("role already exist");
        } 
        
        foreach($screensObj as $res)
        {
            
                $screen=$res;
                $assignScreen = $auth->createPermission($screen);
                $assignScreen->description = $res;
                try{
                    
                 $auth->add($assignScreen);
              
                 
                }catch(\yii\db\Exception $e){
                    
                     Yii::warning("screen already exist");
                }
                
                try{
                       $auth->addChild($role, $assignScreen);
                    
                }catch(\yii\db\Exception $e){
                    
                    Yii::warning("screen already exist");
                }
               
                
        }
        
    }
    
     /** update user permission screen 
    * @param string $roleName
    * @param array  $screens    
    */
    
    public function updateUserScreen($roleName,$screens)
    {
        
        $session = Yii::$app->session;
        $company=$session->get('user.company');
        $screensObj= explode(",",$screens);
        
        $auth = Yii::$app->authManager;
        
         $roleName=$roleName."-".$company['company_code'];
        $permission=$auth->getPermissionsByRole($roleName);
        
       
        $role = $auth->createRole($roleName);
        try{
            
            $auth->add($role);
        
         }catch(\yii\db\Exception $e){
           
             Yii::warning("role already exist");
        } 
        $screenArray=array();
        foreach($screensObj as $perm)
        {
                
                $screen=$perm;
                array_push($screenArray,$screen);
                if(!array_key_exists($screen,$permission))
                {
                    $assignScreen = $auth->createPermission($screen);
                    $assignScreen->description = $perm;
                    try{

                    $auth->add($assignScreen);

                    }catch(\yii\db\Exception $e){
                         Yii::warning("screen already exist");
                    }
                    try{

                    $auth->addChild($role, $assignScreen);
                    }catch(\yii\db\Exception $e){
                         Yii::warning("role allready assign");
                    }
                }
            
            
            
        }
        
       
        //remove the screen 
        $permission=array_keys($permission);
         //var_dump($permission);die;
        foreach($permission as $key)
        {
            if(!in_array($key,$screenArray))
            {
                $assignScreen = $auth->createPermission($key); 
                
                $auth->removeChild($role,$assignScreen);
            }
            
        }
        
    }
    
    /** set the purchase workflow
    * @param string $purchase_order_category
    * @param integer $role_id
    * @param string $roleName
    * @param array  $productcat    
    */
    
    public function setPurchaseWorkflow($purchase_order_category,$role_id,$roleName,$productcat)
    {
        
         $connection = \Yii::$app->db;
       //  var_dump($purchase_order_category);die;
         $productcatmap= \yii\helpers\ArrayHelper::map($productcat, 'id', 'category_code');
        foreach($purchase_order_category as $purcat)
        {
           
                $idArray=explode("-",$purcat);
                $id=trim($idArray[count($idArray)-1]);
                $flag=0;
                $needle="purchase-order-category-A1-".$id;
                if ($needle==$purcat)
                {
                    $approval_level=1;
                    $desc="First Level Approval for ".$productcatmap[$id];
                    $flag=1;
                }
                $needle="purchase-order-category-A2-".$id;
                if ($needle==$purcat)
                {
                    $approval_level=2;
                    $desc="Second Level Approval for ".$productcatmap[$id];
                    $flag=1;
                }
                    
              if($flag==1)
              {
                  
                    $connection->createCommand()
                                ->insert('purchase_order_workflow', [
                                                'approval_level' => $approval_level,
                                                'approval_description' =>$desc,
                                                'approver_role' => $roleName,
                                                'approver_role_id' => $role_id,
                                                'category_id' => $id,
                                    ])
                                ->execute();
              }
                    
               
            
        }
        
        
    }
    
    /** update the purchase workflow
    * @param string $purchase_order_category
    * @param integer $role_id
    * @param string $roleName
    * @param array  $productcat    
    */
    
    public function updatePurchaseWorkflow($purchase_order_category,$role_id,$roleName,$productcat)
    {
        
        $connection = \Yii::$app->db;
        $productcatmap= \yii\helpers\ArrayHelper::map($productcat, 'id', 'category_code');
       
        
        $connection->createCommand()
			->delete('purchase_order_workflow', 'approver_role_id = '.$role_id)
			->execute();
        
        foreach($purchase_order_category as $purcat)
        {
           
                $idArray=explode("-",$purcat);
                $id=trim($idArray[count($idArray)-1]);
                $flag=0;
                
                $needle="purchase-order-category-A1-".$id;
                if ($needle==$purcat)
                {
                    $approval_level=1;
                    $desc="First Level Approval for ".$productcatmap[$id];
                    $flag=1;
                    
                }
                
                $needle="purchase-order-category-A2-".$id;
                if ($needle==$purcat)
                {
                    $approval_level=2;
                    $desc="Second Level Approval for ".$productcatmap[$id];
                    $flag=1;
                }
                    
              if($flag==1)
              {
                 
               
                    $connection->createCommand()
                                ->insert('purchase_order_workflow', [
                                                'approval_level' => $approval_level,
                                                'approval_description' =>$desc,
                                                'approver_role' => $roleName,
                                                'approver_role_id' => $role_id,
                                                'category_id' => $id,
                                    ])
                                ->execute();
                
                 
              } 
               
            
        }
        
        
    }
    
    /** set the customer workflow
    * @param array $screens
    * @param integer $role_id
    * @param string $roleName  
    */
    
     public function setCustomerWorkflow($screens,$role_id,$roleName)
    {
        
        $connection = \Yii::$app->db;
        
        $session = Yii::$app->session;
        $userPermission=  json_decode($session->get('userPermission'));
        
        if(in_array("customer-order-A1",$screens))
        {
               
             $approval_level=1;
             $desc="First Level Approval";
             $flag=1;
             $connection->createCommand()
                                ->insert('customer_order_workflow', [
                                                'approval_level' => $approval_level,
                                                'approval_description' =>$desc,
                                                'approver_role' => $roleName,
                                                'approver_role_id' => $role_id, 
                                                'company_id'=>$userPermission->company_id
                                    ])
                                ->execute();
                    
        }
        
       if(in_array("customer-order-A2",$screens))
       {
               
             $approval_level=2;
               $desc="Second Level Approval";
                    $flag=1;
            $connection->createCommand()
                                ->insert('customer_order_workflow', [
                                                'approval_level' => $approval_level,
                                                'approval_description' =>$desc,
                                                'approver_role' => $roleName,
                                                'approver_role_id' => $role_id, 
                                                'company_id'=>$userPermission->company_id
                                    ])
                                ->execute();
                    
        }
        
        
    }
    
    /** update the customer workflow
    * @param array $screens
    * @param integer $role_id
    * @param string $roleName  
    */
    
    public function updateCustomerWorkflow($screens,$role_id,$roleName)
    {   
        
        $connection = \Yii::$app->db;
        $session = Yii::$app->session;
        $userPermission=  json_decode($session->get('userPermission'));
        
        
        $connection->createCommand()
			->delete('customer_order_workflow', 'approver_role_id = '.$role_id)
			->execute();
       if(in_array("customer-order-A1",$screens))
       {
               
             $approval_level=1;
             $desc="First Level Approval";
             $flag=1;
             $connection->createCommand()
                                ->insert('customer_order_workflow', [
                                                'approval_level' => $approval_level,
                                                'approval_description' =>$desc,
                                                'approver_role' => $roleName,
                                                'approver_role_id' => $role_id, 
                                                'company_id'=>$userPermission->company_id
                                    ])
                                ->execute();
                    
        }
        
       if(in_array("customer-order-A2",$screens))
       {
               
             $approval_level=2;
               $desc="Second Level Approval";
                    $flag=1;
            $connection->createCommand()
                                ->insert('customer_order_workflow', [
                                                'approval_level' => $approval_level,
                                                'approval_description' =>$desc,
                                                'approver_role' => $roleName,
                                                'approver_role_id' => $role_id,
                                                'company_id'=>$userPermission->company_id
                                    ])
                                ->execute();
                    
        }
                
            
               
            
        
        
        
    }
    

    /** set the approval level workflow
    * @param array $screens
    * @param integer $role_id
    * @param string $roleName  
    */
    
     public function setApproveLevelWorkflow($screens,$role_id,$roleName,$module_name)
    {
        
        $connection = \Yii::$app->db;
        
        $session = Yii::$app->session;
        $userPermission=  json_decode($session->get('userPermission'));
        
        if(in_array("sku-adjustments-A1",$screens))
        {
               
             $approval_level=1;
             $desc="First Level Approval";
             $flag=1;
             $connection->createCommand()
                                ->insert('approve_level_permission', [
                                                'approval_level' => $approval_level,
                                                'approval_description' =>$desc,
                                                'approver_role' => $roleName,
                                                'approver_role_id' => $role_id, 
                                                'module_name'=>$module_name,
                                                'company_id'=>$userPermission->company_id
                                    ])
                                ->execute();
                    
        }
        
       if(in_array("sku-adjustments-A2",$screens))
       {
               
             $approval_level=2;
               $desc="Second Level Approval";
                    $flag=1;
            $connection->createCommand()
                                ->insert('approve_level_permission', [
                                                'approval_level' => $approval_level,
                                                'approval_description' =>$desc,
                                                'approver_role' => $roleName,
                                                'approver_role_id' => $role_id, 
                                                'module_name'=>$module_name,
                                                'company_id'=>$userPermission->company_id
                                    ])
                                ->execute();
                    
        }
        
        
    }
    
    /** update the approval level workflow
    * @param array $screens
    * @param integer $role_id
    * @param string $roleName  
    */
    
    public function updateApproveLevelWorkflow($screens,$role_id,$roleName,$module_name)
    {   
        
        $connection = \Yii::$app->db;
        $session = Yii::$app->session;
        $userPermission=  json_decode($session->get('userPermission'));
        
        
        $connection->createCommand()
			->delete('approve_level_permission', 'approver_role_id = '.$role_id)
			->execute();
        
        if(in_array("sku-adjustments-A1",$screens))
        {
               
             $approval_level=1;
             $desc="First Level Approval";
             $flag=1;
             $connection->createCommand()
                                ->insert('approve_level_permission', [
                                                'approval_level' => $approval_level,
                                                'approval_description' =>$desc,
                                                'approver_role' => $roleName,
                                                'approver_role_id' => $role_id, 
                                                'module_name'=>$module_name,
                                                'company_id'=>$userPermission->company_id
                                    ])
                                ->execute();
                    
        }
        
       if(in_array("sku-adjustments-A2",$screens))
       {
               
             $approval_level=2;
               $desc="Second Level Approval";
                    $flag=1;
            $connection->createCommand()
                                ->insert('approve_level_permission', [
                                                'approval_level' => $approval_level,
                                                'approval_description' =>$desc,
                                                'approver_role' => $roleName,
                                                'approver_role_id' => $role_id, 
                                                'module_name'=>$module_name,
                                                'company_id'=>$userPermission->company_id
                                    ])
                                ->execute();
                    
        }
       
                
            
               
            
        
        
        
    }

    
    
    
    /** set the GRN workflow
    * @param array $param
    * @param integer $role_id
    * @param string $roleName  
    * @param integer $companyId
    */
    
    public  function setGrnWorkflow($param, $roleId, $roleName, $companyId){
        $connection = \Yii::$app->db;
        if(in_array('goods-receive-notification-A1',$param)){
            $connection->createCommand()
                ->insert('grn_workflow', [
                'approval_level' => 1,
                'approval_level_description' =>'Approval level 1 for GRN',
                'approver_user_role' => $roleName,
                'approver_user_role_id' => $roleId,
                'company_id'=>$companyId
            ])->execute();
        }
    }
    
    /** update the GRN workflow
    * @param array $param
    * @param integer $role_id
    * @param string $roleName  
    * @param integer $companyId
    */

    public function updateGrnWorkflow($param, $roleId, $roleName, $companyId){
        $connection = \Yii::$app->db;
        $connection->createCommand()
            ->delete('grn_workflow', 'approver_user_role_id = '.$roleId)
            ->execute();
        if(in_array('goods-receive-notification-A1',$param)){
            $connection->createCommand()
                ->insert('grn_workflow', [
                'approval_level' => 1,
                'approval_level_description' =>'Approval level 1 for GRN',
                'approver_user_role' => $roleName,
                'approver_user_role_id' => $roleId,
                'company_id'=>$companyId
            ])->execute();
        }
    }
    
    
    public function setAuthPermission($authpermissionObj,$param)
    {
        $authpermString="";
        $i=0;
     
        foreach($authpermissionObj as $authparam)
        {
            $code=$authparam['authorize_code'];
            if(isset($param[$code]))
            {
                if($i==0)
                {
                    $authpermString=$authparam["id"];
                    $i++;
                }
                else
                {
                    $authpermString=$authpermString.",".$authparam["id"];                 
                   
                }
            }
           
            
        }
        
        return $authpermString;
    }

}
