<?php

namespace backend\controllers;

use Yii;
use backend\models\RolePermission;
use backend\models\RolePermissionSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use common\models\CommonCode;

/**
 * RolePermissionController implements the CRUD actions for RolePermission model.
 */
class RolePermissionController extends Controller
{
    public function behaviors() {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) 
                        {
                          $operation=$action->id;
                            $session = Yii::$app->session;
                            $userPermission=  json_decode($session->get('userPermission'));
                          $permission=new CommonCode();
                          
                          if ($permission->canAccess("user-".$operation)  || $userPermission->isSystemAdmin ) {
                              return TRUE;
                          } else {
                              return FALSE;
                          }
                          
                        }
                     
                    ],                 
                    [
                        'actions' => ['logout', 'login'],
                        'allow' => true,
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all RolePermission models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RolePermissionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single RolePermission model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new RolePermission model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new RolePermission();
        
        
        //$userpermission1=["user_id"=>"user_id","roles"=>["role_id"=>"role_name","role_id"=>"role_name",],'username'=>"username","company_id"=>"company_id","company_name"=>"company_name","product_category"=>["category_id"=>"category_code","category_id"=>"category_code",],"warehouses"=>["warehouse_id"=>"warehouse_code","warehouse_id"=>"warehouse_code"],"market_places"=>["market_id"=>"market_name","market_id"=>"market_name"],"screens"=>["screen1","screen2"]];
      
       // var_dump(json_encode($userpermission1));
       // $userpermission=["user_id"=>1,"roles"=>["1"=>"Superadmin","2"=>"Sale Manager"],'username'=>"superadmin","company_id"=>"1","company_name"=>"Primarc","product_category"=>["1"=>"PETS","2"=>"ETC"],"warehouse"=>["1"=>"Bhiwandi","2"=>"Bombay"],"market_place"=>["1"=>"Amazon","2"=>"Snapdeal"],"screens"=>["product-master-create","product-master-index"]];
         $session = Yii::$app->session;
         $userPermission=  json_decode($session->get('userPermission'));
       
         
        $company=$session->get('user.company');
        
        $companyObj=  \backend\models\CompanyMaster::find()->asArray()->all();
        $companyArray = ArrayHelper::map($companyObj, 'id', 'company_name');
        
        //var_dump($companyArray);die;
        
        $orgnzationObj =  \backend\models\Organization::find()->asArray()->all();
        $orgnzationArray=array();
        //array_push($orgnzationArray,"0"=>"All");
        $orgnzationArray = ArrayHelper::map($orgnzationObj, 'id', 'organization_name');
        
        $permissionObj=  \backend\models\Permission::find()->asArray()->all();
        $permissionArray = ArrayHelper::map($permissionObj, 'permission_code', 'permission_name');
        
        $resourceObj=  \backend\models\Resource::find()->asArray()->orderBy("rank_id asc")->all();
        $resourceArray = ArrayHelper::map($resourceObj, 'resource_code', 'resource_name');
        
        
        $marketplaceObj=  \backend\models\MarketMaster::find()->where(['company_id'=>$company['id']])->asArray()->all();
        
        $marketplaceArray = ArrayHelper::map($marketplaceObj, 'id', 'market_code');
        
        $productCategoryObj=  \backend\models\ProductMainCategory::find()->where(['company_id'=>$company['id']])->asArray()->all();
        $productCategoryArray = ArrayHelper::map($productCategoryObj, 'id', 'category_code');
        
        $warehouseObj= \backend\models\InternalWarehouseMaster::find()->where(['company_id'=>$company['id']])->asArray()->all();
        $warehouseArray = ArrayHelper::map($warehouseObj, 'id', 'warehouse_code');
        
        //report permission master 
        $reportpermissionMaster=  \backend\models\ReportPermissionMaster::find()->orderBy("priority asc")->asArray()->all();
        $reportpermissionMasterArray = ArrayHelper::map($reportpermissionMaster, 'report_code', 'report_name');
        
        //authorize permission
        $authpermissionObj=  \backend\models\AuthorizePermission::find()->asArray()->orderBy("module_name ")->all();
        //var_dump($authpermissionObj);die;
        $permissions=array();
        
        $validation=0;
        if ($model->load(Yii::$app->request->post())) {
            
           $param=Yii::$app->request->post();
          //var_dump($model);die;
           
           $model->created_at=date("Y-m-d H:i:s");
           $model->updated_at=date("Y-m-d H:i:s");
           $model->created_by=Yii::$app->user->id;
           $model->updated_by= Yii::$app->user->id;
           $model->auth_permission_id=$model->setAuthPermission($authpermissionObj,$param['RolePermission']);
            if($userPermission->isSystemAdmin)
            {
              $model->is_superadmin=1; 
              
            }
            else{
                
                $model->is_superadmin=0;

            }
        
        //   if(isset($param['RolePermission']['authorize_live_dashboard']))
     //     {
             $model->authorize_live_dashboard=1; 
      //    }else{
      //        $model->authorize_live_dashboard=0; 
      //    }
           
           if(isset($param['selectItemRolePermission']))
           {    
           $RolePermission=$param['selectItemRolePermission'];    
          
           //$orgnzation_id=$param['RolePermission']['organization_id'];
           
           $orgnzation_id=implode(",",$RolePermission['organization_id']); 
                 
           //$permission_id=implode(",",$RolePermission['permission_id']); 
                    
          // $resource_id=implode(",",$RolePermission['resource_id']);
           
           //get category id
           if(isset($param['product-category']))
           {
                $categoryArray=$model->getCategoryMKTWareHouseId($param['product-category']);
                $category_id=implode(",",$categoryArray);
           }else{
                 $category_id="";
           }
           
           //get market place id
           if(isset($param['market-place-product-master']))
           {
                $marketplace_id=implode(",",$model->getCategoryMKTWareHouseId($param['market-place-product-master']));
           }else{
               $marketplace_id="";
           }
           
           //get warehouse_id
           if(isset($param['warehouse-master']))
           {
                $warehouse_id=implode(",",$model->getCategoryMKTWareHouseId($param['warehouse-master']));
                
           }else{
               $warehouse_id="";
           }
           
         
            //get category id
           $pocatArray=array();
           if(isset($param['purchase-order-category']))
           {
               $pocatArray=$model->getCategoryMKTWareHouseId($param['purchase-order-category']);
                $purchase_category_id=implode(",",$pocatArray);
                
           }else{
               
                 $purchase_category_id="";
           }
           
           //get screen
           $permissions=$model->getScreenUrl($param);
           $screens=implode(",",$permissions);
           
         
        
           $model->role_id=1;
           $model->category_id=$category_id;
           if(!$userPermission->isSystemAdmin)
           {
           $model->company_id=$company['id'];
           $company_id=$company['id'];
           
           }
           else{
             
               $company_id=$param['RolePermission']['company_id'];
              $model->company_id=$company_id;   
           }
           $model->marketplace_id=$marketplace_id;
           $model->organization_id=$orgnzation_id;
           $model->po_category_id=$purchase_category_id;
           $model->warehouse_id=$warehouse_id;
          
           }  
           
           //var_dump($model);die;
           if($model->save())
           {
                $roleObj=new  \backend\models\Roles();
                $roleName=$param['RolePermission']['role_name'];
               
                $role_id=$roleObj->getRoleId($roleName,$company_id);
                $model->role_id=$role_id; 
               
                $model->save(false);
               //set user screen
              if(isset($param['purchase-order-category']))
              {
               $productcat= \backend\models\ProductMainCategory::find()->where(['id'=>$pocatArray])->asArray()->all();
              
               $pw=$model->setPurchaseWorkflow($param['purchase-order-category'],$role_id,$roleName,$productcat);
               
              }
             
             if(in_array("sku-adjustments-A1",$permissions) || in_array("sku-adjustments-A2",$permissions))
              {
                           
               $pw=$model->setApproveLevelWorkflow($permissions,$role_id,$roleName,"SKU Adjustment");
               
              }
              
              if(in_array("customer-order-A1",$permissions) || in_array("customer-order-A2",$permissions))
              {
                           
               $pw=$model->setCustomerWorkflow($permissions,$role_id,$roleName);
               
              }
               if(isset($param['goods-receive-notification'])){
                   $model->setGrnWorkflow($param['goods-receive-notification'], $role_id, $roleName, $company_id);
               }

               $model->setUserScreen($roleName,$screens);
               
               // $model->save(false);
                return $this->redirect(['index']);
               
           } else {               
             // var_dump($model->errors);die;
            return $this->render('create', [
                'model' => $model,
                'orgnzationArray'=>$orgnzationArray,
                'permissionArray'=>$permissionArray,
                'resourceArray'=>$resourceArray,
                'marketplaceArray'=>$marketplaceArray,
                'productCategoryArray'=>$productCategoryArray,
                'warehouseArray'=>$warehouseArray,
                'permissions'=>$permissions,
                "companyArray"=>$companyArray,
                'reportpermissionMasterArray'=>$reportpermissionMasterArray,
                'authpermissionObj'=>$authpermissionObj
                
            ]);
        }
           
            
           
        } else {
            return $this->render('create', [
                'model' => $model,
                'orgnzationArray'=>$orgnzationArray,
                'permissionArray'=>$permissionArray,
                'resourceArray'=>$resourceArray,
                'marketplaceArray'=>$marketplaceArray,
                'productCategoryArray'=>$productCategoryArray,
                'warehouseArray'=>$warehouseArray,
                'permissions'=>$permissions,
                "companyArray"=>$companyArray,
                'reportpermissionMasterArray'=>$reportpermissionMasterArray,
                'authpermissionObj'=>$authpermissionObj
                
            ]);
        }
    }

    /**
     * Updates an existing RolePermission model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $roleId=$model->role_id;
        $orgnzationArray=array();
        
        $session = Yii::$app->session;
        $company=$session->get('user.company');
       
         $session = Yii::$app->session;
         $userPermission=  json_decode($session->get('userPermission'));
        
        $orgnzationObj =  \backend\models\Organization::find()->asArray()->all();
        $orgnzationArray=array();
        //array_push($orgnzationArray,"0"=>"All");
        $orgnzationArray = ArrayHelper::map($orgnzationObj, 'id', 'organization_name');
        
        $permissionObj=  \backend\models\Permission::find()->asArray()->all();
        $permissionArray = ArrayHelper::map($permissionObj, 'permission_code', 'permission_name');
        
        $resourceObj=  \backend\models\Resource::find()->orderBy("rank_id asc")->asArray()->all();
        $resourceArray = ArrayHelper::map($resourceObj, 'resource_code', 'resource_name');
        
        $marketplaceObj=  \backend\models\MarketMaster::find()->where(['company_id'=>$company['id']])->asArray()->all();
        $marketplaceArray = ArrayHelper::map($marketplaceObj, 'id', 'market_code');
        
        $productCategoryObj=  \backend\models\ProductMainCategory::find()->where(['company_id'=>$company['id']])->asArray()->all();
        $productCategoryArray = ArrayHelper::map($productCategoryObj, 'id', 'category_code');
        
        $warehouseObj= \backend\models\InternalWarehouseMaster::find()->where(['company_id'=>$company['id']])->asArray()->all();
        $warehouseArray = ArrayHelper::map($warehouseObj, 'id', 'warehouse_code');
        
         //report permission master 
        $reportpermissionMaster=  \backend\models\ReportPermissionMaster::find()->orderBy("priority asc")->asArray()->all();
        $reportpermissionMasterArray = ArrayHelper::map($reportpermissionMaster, 'report_code', 'report_name');
       
         //authorize permission
        $authpermissionObj=  \backend\models\AuthorizePermission::find()->asArray()->orderBy("module_name ")->all();
      
         
        $auth = Yii::$app->authManager;
        $roleName=$model->role_name."-".$company['company_code'];
        $permission=$auth->getPermissionsByRole($roleName);
        $permissions=array_keys($permission);
      
        if ($model->load(Yii::$app->request->post())) {
            
            $param=Yii::$app->request->post();
          $model->auth_permission_id=$model->setAuthPermission($authpermissionObj,$param['RolePermission']);
           if($userPermission->isSystemAdmin)
            {
              $model->is_superadmin=1; 
              
            }
            else{
                
                $model->is_superadmin=0;

            }
      //     if(isset($param['RolePermission']['authorize_live_dashboard']))
     //     {
             $model->authorize_live_dashboard=1; 
     //     }else{
            //  $model->authorize_live_dashboard=0; 
      //    }
          
           //$model->transactions=$param['RolePermission']['transactions'];
          // $model->reports=$param['RolePermission']['reports'];
           
           if(isset($param['selectItemRolePermission']))
           {
           
           $model->updated_at=date("Y-m-d H:i:s");        
           $model->updated_by= Yii::$app->user->id;
           
           $RolePermission=$param['selectItemRolePermission'];    
          
           //$orgnzation_id=$param['RolePermission']['organization_id'];
           
           $orgnzation_id=implode(",",$RolePermission['organization_id']); 
                 
           //$permission_id=implode(",",$RolePermission['permission_id']); 
           
          // $resource_id=implode(",",$RolePermission['resource_id']);
           
           //get warehouse_id
           if(isset($param['warehouse-master']))
           {
                $warehouse_id=implode(",",$model->getCategoryMKTWareHouseId($param['warehouse-master']));
                
           }else{
               $warehouse_id="";
           }
           //get category id
           if(isset($param['product-category']))
           {
                $category_id=implode(",",$model->getCategoryMKTWareHouseId($param['product-category']));
           }else{
                 $category_id="";
           }
           
           //get market place id
           if(isset($param['market-place-product-master']))
           {
                $marketplace_id=implode(",",$model->getCategoryMKTWareHouseId($param['market-place-product-master']));
           }else{
               $marketplace_id="";
           }
           
           //get category id
           $pocatArray=array();
           if(isset($param['purchase-order-category']))
           {
               $pocatArray=$model->getCategoryMKTWareHouseId($param['purchase-order-category']);
               $purchase_category_id=implode(",",$pocatArray);
                
           }else{
               
                 $purchase_category_id="";
           }
           
           //get screen
         // var_dump($param['purchase-order-category']);die;
           $permissions=$model->getScreenUrl($param);
           $screens=implode(",",$permissions);
         
           
           
          
           $model->category_id=$category_id;
           $model->company_id=$company['id'];
           $model->marketplace_id=$marketplace_id;
           $model->organization_id=$orgnzation_id;
           $model->po_category_id=$purchase_category_id;
           $model->warehouse_id=$warehouse_id;
          
           }
           
          
            //var_dump($model);die;
           if($model->save())
           {
              
               $roleObj=new  \backend\models\Roles();
               $roleName=$param['RolePermission']['role_name'];
               $role_id=$roleObj->getUpdateRole($roleId,$roleName);
           
              $productcat= \backend\models\ProductMainCategory::find()->where(['id'=>$pocatArray])->all();   
               if(isset($param['purchase-order-category']))
               {
                           
                $pw=$model->updatePurchaseWorkflow($param['purchase-order-category'],$roleId,$roleName,$productcat);
              }else{
                  $parampurchase_order_category=array();
                  $pw=$model->updatePurchaseWorkflow($parampurchase_order_category,$roleId,$roleName,$productcat);
              }
              
               
               $pw=$model->updateCustomerWorkflow($permissions,$roleId,$roleName);
              
              if(in_array("sku-adjustments-A1",$permissions) || in_array("sku-adjustments-A2",$permissions))
              {
                $module_name="SKU Adjustment";
              }
              
               $pw=$model->updateApproveLevelWorkflow($permissions,$roleId,$roleName,"SKU Adjustment");
             
               if(isset($param['goods-receive-notification']))
               {
                   $model->updateGrnWorkflow($param['goods-receive-notification'],$roleId, $roleName, $company['id']);
               }else{
                   $model->updateGrnWorkflow([],$roleId, $roleName, $company['id']);
               }
               $model->updateUserScreen($roleName,$screens);
                
                return $this->redirect(['index']);
               
           }else {
            return $this->render('update', [
                'model' => $model,
                'orgnzationArray'=>$orgnzationArray,
                'permissionArray'=>$permissionArray,
                'resourceArray'=>$resourceArray,
                'marketplaceArray'=>$marketplaceArray,
                'productCategoryArray'=>$productCategoryArray,
                'warehouseArray'=>$warehouseArray,
                "permissions"=>$permissions,
                'reportpermissionMasterArray'=>$reportpermissionMasterArray,
                'authpermissionObj'=>$authpermissionObj
            ]);
        }
           
        } else {
            return $this->render('update', [
                'model' => $model,
                'orgnzationArray'=>$orgnzationArray,
                'permissionArray'=>$permissionArray,
                'resourceArray'=>$resourceArray,
                'marketplaceArray'=>$marketplaceArray,
                'productCategoryArray'=>$productCategoryArray,
                'warehouseArray'=>$warehouseArray,
                "permissions"=>$permissions,
                'reportpermissionMasterArray'=>$reportpermissionMasterArray,
                'authpermissionObj'=>$authpermissionObj
            ]);
        }
    }

    /**
     * Deletes an existing RolePermission model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the RolePermission model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return RolePermission the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = RolePermission::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
