<?php

namespace backend\controllers\user;

use dektrium\user\controllers\AdminController as BaseAdminController;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use backend\models\User;
use yii;
use yii\helpers\Url;
use dektrium\user\models\UserSearch;
use yii\helpers\ArrayHelper;
use yii\web\Response;

class AdminController extends BaseAdminController {

    /** @inheritdoc */
    public function behaviors() {


        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'confirm' => ['post'],
                    'block' => ['post']
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [],
                        'allow' => true,
                        'roles' => ['@'],
                         'matchCallback' => function ($rule, $action) 
                        {
                           $operation=$action->id;
                          $idparam=Yii::$app->request->get('id');
                           $session = Yii::$app->session;
                           $userPermission=  json_decode($session->get('userPermission'));
                          $permission=new \common\models\CommonCode();
                          if($operation=="update" || $operation=="update-profile")
                          {
                              if($idparam==$userPermission->user_id)
                               return true;
                              
                               if ($operation=="update-profile" && $permission->canAccess("user-update")){
                                   
                                   return true;
                               }
                              
                          }
                      
                          if($operation=="block")
                          {
                              if($permission->canAccess("user-deactive"))
                              {
                                  return true;
                              }
                          }
                         
                          if ($permission->canAccess("user-".$operation) || $userPermission->isSystemAdmin) {
                                return TRUE;
                            } else {
                                return FALSE;
                            }
                          
                          
                        }
                    ],
                ]
            ]
        ];
    }
      /**
     * Lists all User models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        Url::remember('', 'actions-redirect');
        $searchModel  = Yii::createObject(\backend\models\UserSearch::className());
        $dataProvider = $searchModel->search(Yii::$app->request->get());
        
         $session = Yii::$app->session;
        $company=$session->get('user.company');
        
        $userPermission=  json_decode($session->get('userPermission'));
        if($userPermission->isSystemAdmin)
        {
            $roleObj = \backend\models\Roles::find()->where(["cre_user"=>$userPermission->user_id])->asArray()->all();
        }else 
        {
            $roleObj = \backend\models\Roles::find()->where(["company_id"=>$userPermission->company_id])->asArray()->all();
            
        }
        $roleArray = ArrayHelper::map($roleObj, 'id', 'role_name'); 

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel'  => $searchModel,
            'roleArray'=>$roleArray
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        /** @var User $user */
        $user = Yii::createObject([
            'class'    => User::className(),
            'scenario' => 'create',
        ]);
        
        $session = Yii::$app->session;
        $company=$session->get('user.company');
		
	 $permission=new \common\models\CommonCode();	
        $userPermission=  json_decode($session->get('userPermission'));
		$company_id = $userPermission->company_id;
        $roles=$userPermission->roles;
        $roleId=$this->getUserPermissionRoleId($roles);
        
        if($userPermission->isSystemAdmin)
        {
            $roleObj = \backend\models\Roles::find()->where(["is_superadmin"=>1])->asArray()->all();
            $user->is_superadmin=1;
            
        }else
         {
             if ($permission->canAccess("user-update") || $permission->canAccess("update-profile") || $permission->canAccess("user-create"))
             {
                  $roleObj = \backend\models\Roles::find()->where(["company_id"=>$company_id,"is_superadmin"=>0])->asArray()->all(); 
             }
             else
                 {
                 
                $roleObj = \backend\models\Roles::find()->where(["company_id"=>$company_id,"is_superadmin"=>0])->andWhere(['not in',"id",  $roleId])->asArray()->all(); 
         

                 
             }
             
        }
        $roleArray = ArrayHelper::map($roleObj, 'id', 'role_name'); 
        if($userPermission->isSystemAdmin)
        {
            $companyObj = \backend\models\CompanyMaster::find()->where(["!=","id",$company['id']])->asArray()->all();
            
        }else{
            
            $companyObj = \backend\models\CompanyMaster::find()->where(["id"=>$company['id']])->asArray()->all();
        }
        $companyArray = ArrayHelper::map($companyObj, 'id', 'company_name'); 
     
        $this->performAjaxValidation($user);
        //var_dump($user);die;
        $param=Yii::$app->request->post();
       // var_dump($user);die;
         if(isset($param['selectItemUser']['role_id']))
         {
             $selectedRoleArray=implode(",",$param['selectItemUser']['role_id']);
             
         }else{
             $selectedRoleArray=0;
         }
         
         //check superadmin
         
        if ($user->load(Yii::$app->request->post()) ) 
        {
            
            $param=Yii::$app->request->post();
            $user->company_id=$param['User']['company_id'];
            if(isset($param['selectItemUser']['role_id']) && count($param['selectItemUser']['role_id']))
            {
                if($user->create())
                {
                $roles=$param['selectItemUser']['role_id'];
                foreach($roles as $role)
                {
                   $userrole=new  \backend\models\UserRoles();   
                   $userrole->role_id=$role;
                   $userrole->user_id=$user->id;            
                   $userrole->save();
                   
                   
                   
                }
                
                //update the user name
                $paramprofile=$param["User"];
               
                if(isset($paramprofile['name']))
                {
                
                    $userprofile=\backend\models\Profile::find()->where(['user_id'=>$user->id])->one();
                    $userprofile->name=$paramprofile['name'];
                    $userprofile->save(false);
                }
                
                
                //assign permission to user
                $rolepermission=$user->AssignRolePermission($user->id,$param['selectItemUser']['role_id']);
                }
                  $company=new \backend\models\UserCompanyRel();
            $company->company_id=$user->company_id;
            $company->user_id=$user->id;
            $company->created_at=date("Y-m-d H:i:s");
            $company->updated_at=date("Y-m-d H:i:s");
            $company->save();
           
           
           Yii::$app->getSession()->setFlash('success', Yii::t('user', 'User has been created'));
           
            return $this->redirect(['update', 'id' => $user->id]);
               
            
            }else{
                $user->addError("role_id","please select role");
                
            }
            
          
        }

        return $this->render('create', [
            'user' => $user,
            'roleArray'=>$roleArray,
            'companyArray'=>$companyArray,
            'selectedRoleArray'=>$selectedRoleArray
        ]);
    }

    /**
     * Updates an existing User model.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        Url::remember('', 'actions-redirect');
        $user = $this->findModel($id);
     //   var_dump($user);die;
        $user->scenario = 'update';
        
         $session = Yii::$app->session;
        $company=$session->get('user.company');
		
        
        $commonCode=new \common\models\CommonCode();
        
        $userPermission=  json_decode($session->get('userPermission'));
		$company_id = $userPermission->company_id;
        $roles=$userPermission->roles;
        $roleId=$this->getUserPermissionRoleId($roles);
        $ownupdate=0;
        if($userPermission->isSystemAdmin)
        {
           
            $roleObj = \backend\models\Roles::find()->where(["is_superadmin"=>1])->asArray()->all();          
            $user->is_superadmin=1;
            
            
        }else if($userPermission->isSuperAdmin) 
        {
                if($id==$userPermission->user_id)
                {
                     $roleObj = \backend\models\Roles::find()->where(["company_id"=>$company_id,"id"=>$roleId])->asArray()->all(); 
            
                    $ownupdate=1;
                }
                else
                {
                    $roleObj = \backend\models\Roles::find()->where(["company_id"=>$company_id,"is_superadmin"=>0])->asArray()->all(); 
                }
            
           
        }else{
            
             if($commonCode->canAccess("user-create")  || $commonCode->canAccess("user-update"))
            {
                if($id==$userPermission->user_id)
                {
                    $ownupdate=1;
                }
                $roleObj = \backend\models\Roles::find()->where(["company_id"=>$company['id'],"is_superadmin"=>0])->asArray()->all();
                
            }else
            {
                           
               $roleObj = \backend\models\Roles::find()->where(["company_id"=>$company['id'],"id"=>$roleId])->asArray()->all();  
                    
              
            }
        }
        $roleArray = ArrayHelper::map($roleObj, 'id', 'role_name'); 
        if($userPermission->isSystemAdmin)
        {
            if($id==$userPermission->user_id)
            {
            
            $companyObj = \backend\models\CompanyMaster::find()->where(["=","id",$company['id']])->asArray()->all();
                        
            }
            else{
                
                $companyObj = \backend\models\CompanyMaster::find()->where(["!=","id",$company['id']])->asArray()->all();
            }
            
        }else{
            
            $companyObj = \backend\models\CompanyMaster::find()->where(["id"=>$company['id']])->asArray()->all();
        }
        
        $companyArray = ArrayHelper::map($companyObj, 'id', 'company_code'); 
        
        if(isset($param['User']['company_id']))
        {
         $user->company_id=$param['User']['company_id'];
        }else{
            
            $user->company_id=$user->getUserCompany($id);
        }

        $this->performAjaxValidation($user);
        
        $param=Yii::$app->request->post();
        $rarray=$selectedRoleArray= $user->getUserRole($id);
         if(isset($param['selectItemUser']['role_id']))
         {
             $selectedRoleArray=implode(",",$param['selectItemUser']['role_id']);            
             
         }
         
        $userdbrole=$user->getDBRole($id);
        
       
        if($userdbrole)
        $dbRole=explode(",",$userdbrole);

        if ($user->load(Yii::$app->request->post()) && $user->save()) 
        {
             $user->company_id=$param['User']['company_id'];
               if(isset($param['selectItemUser']['role_id']))
                {
                    if($param['selectItemUser']['role_id']!=explode(",",$rarray))
                   {
                         $auth = Yii::$app->authManager;
                        $userroledelete=$user->deleteuserrole($id);
                        $userrole=$auth->getRolesByUser($id);

                        if(isset($param['selectItemUser']['role_id']))
                       {
                           $roles=$param['selectItemUser']['role_id'];
                           foreach($roles as $role)
                           {
                              $userrole=new  \backend\models\UserRoles();   
                              $userrole->role_id=$role;
                              $userrole->user_id=$user->id;            
                              $userrole->save();
                           }

                           //assign permission to user
                       $rolepermission=$user->UpdateRolePermission($user->id,$param['selectItemUser']['role_id'],$userdbrole);



                      }else{

                             $user->addError("role_id","please select role");
                            return $this->render('_account', [
                            'user' => $user,
                            'roleArray'=>$roleArray,
                            'companyArray'=>$companyArray,
                            "selectedRoleArray"=>$selectedRoleArray,
                            'ownupdate'=>$ownupdate
                        ]); 
                      }
                   }
                 
              }else{
                
                  $user->addError("role_id","please select role");
                     return $this->render('_account', [
                     'user' => $user,
                     'roleArray'=>$roleArray,
                     'companyArray'=>$companyArray,
                     "selectedRoleArray"=>$selectedRoleArray,
                     'ownupdate'=>$ownupdate]);
            }
             $user->updateUserCompany($id,$user->company_id);
             
             Yii::$app->getSession()->setFlash('success', Yii::t('user', 'Account details have been updated'));

            return $this->refresh();
        }

        return $this->render('_account', [
            'user' => $user,
            'roleArray'=>$roleArray,
            'companyArray'=>$companyArray,
            "selectedRoleArray"=>$selectedRoleArray,
            'ownupdate'=>$ownupdate
        ]);
    }

    /**
     * Updates an existing profile.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionUpdateProfile($id)
    {
        Url::remember('', 'actions-redirect');
        $user    = $this->findModel($id);
        $profile = $user->profile;
        $path = Yii::$app->params['userimagePath']; 
        //$oldimage=Yii::$app->basePath . $path . $profile->user_image;
        
        $username=\backend\models\User::find()->select("username")->where(['id'=>$profile['user_id']])->asArray()->one();
        $user_name=$username['username'];
        
       

        if ($profile == null) {
            $profile = Yii::createObject(\backend\models\Profile::className());
            $profile->link('user', $user);
        }

      $this->performAjaxValidation($profile);
     
      $request=Yii::$app->request->post();
      
      $session = Yii::$app->session;
    //  $userPermission=  $session->set('userPermission');
         
          if($request)
          {
           $profile->mobile=$request["Profile"]["mobile"];
           $profile->phone=$request["Profile"]["phone"];
           $profile->fax=$request["Profile"]["fax"];
           $image = \yii\web\UploadedFile::getInstance($profile, 'user_image');
              if (isset($image))
                   {
                        
                        if(in_array($image->extension,[ 'png','jpg','jpeg','bmp','gif']))
                        {
                            $randstring = uniqid();
                            $fullpath = Yii::$app->basePath . $path . $randstring. '.' . $image->extension;
                          /*  if(isset($profile->user_image) && $profile->user_image!="noimage.png" && file_exists($oldimage))
                            {
                                unlink($oldimage);
                            }*/
                            $image->saveAs($fullpath);
                            $profile->user_image=$randstring. '.' . $image->extension;
                            
                            //$session->set("user_image", $profile->user_image);
                        }
                        else
                        {
                         $error = "Invalid File Format";
                         return $this->render('_profile', ['profile' => $profile,'user'=> $user, 'error' => $error]);
                        }
                   }
                   
          }
          
        if ($profile->load(Yii::$app->request->post()) && $profile->save()) {
           
             //var_dump($profile->user_image);die;
             $session = Yii::$app->session;        
             $userPermission=json_decode($session->get('userPermission'));
             if($userPermission->user_id==$id)
             {
                $userPermission->user_image=$profile->user_image;

                $jsonstring=  json_encode($userPermission);                    
                $session->set('userPermission', $jsonstring);
                
             }
             
            Yii::$app->getSession()->setFlash('success', Yii::t('user', 'Profile details have been updated'));

            return $this->refresh();
            
        }
        return $this->render('_profile', [
            'user'    => $user,
            'profile' => $profile,
        ]);
    }

    /**
     * Shows information about user.
     *
     * @param int $id
     *
     * @return string
     */
    public function actionInfo($id)
    {
        Url::remember('', 'actions-redirect');
        $user = $this->findModel($id);

        return $this->render('_info', [
            'user' => $user,
        ]);
    }

    /**
     * If "dektrium/yii2-rbac" extension is installed, this page displays form
     * where user can assign multiple auth items to user.
     *
     * @param int $id
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionAssignments($id)
    {
        if (!isset(Yii::$app->extensions['dektrium/yii2-rbac'])) {
            throw new NotFoundHttpException();
        }
        Url::remember('', 'actions-redirect');
        $user = $this->findModel($id);

        return $this->render('_assignments', [
            'user' => $user,
        ]);
    }

    /**
     * Confirms the User.
     *
     * @param int $id
     *
     * @return Response
     */
    public function actionConfirm($id)
    {
        $this->findModel($id)->confirm();
        Yii::$app->getSession()->setFlash('success', Yii::t('user', 'User has been confirmed'));

        return $this->redirect(Url::previous('actions-redirect'));
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        if ($id == Yii::$app->user->getId()) {
            Yii::$app->getSession()->setFlash('danger', Yii::t('user', 'You can not remove your own account'));
        } else {
            $this->findModel($id)->delete();
            Yii::$app->getSession()->setFlash('success', Yii::t('user', 'User has been deleted'));
        }

        return $this->redirect(['index']);
    }

    /**
     * Blocks the user.
     *
     * @param int $id
     *
     * @return Response
     */
    public function actionBlock($id)
    {
        if ($id == Yii::$app->user->getId()) {
            Yii::$app->getSession()->setFlash('danger', Yii::t('user', 'You can not block your own account'));
        } else {
            $user = $this->findModel($id);
            if ($user->getIsBlocked()) {
                $user->unblock();
                Yii::$app->getSession()->setFlash('success', Yii::t('user', 'User has been Active'));
            } else {
                $user->block();
                Yii::$app->getSession()->setFlash('success', Yii::t('user', 'User has been InActive'));
            }
        }

        return $this->redirect(Url::previous('actions-redirect'));
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param int $id
     *
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
       // $user = $this->finder->findUserById($id);
        $user=  User::find()->where(['id'=>$id])->one();
        if ($user === null) {
            throw new NotFoundHttpException('The requested page does not exist');
        }

        return $user;
    }

    /**
     * Performs AJAX validation.
     *
     * @param array|Model $model
     *
     * @throws ExitException
     */
    protected function performAjaxValidation($model)
    {
        if (Yii::$app->request->isAjax && !Yii::$app->request->isPjax) {
            if ($model->load(Yii::$app->request->post())) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                echo json_encode(yii\widgets\ActiveForm::validate($model));
                Yii::$app->end();
            }
        }
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    /*
    public function actionIndex() {
        //$searchModel  = $this->module->manager->createUserSearch();
        //$dataProvider = $searchModel->search($_GET);
        $user = new User();

        $dataProvider = $user->getUserList();

        //var_dump($dataProvider);die;
        return $this->render('index', [
                    'dataProvider' => $dataProvider,
                        //'searchModel'  => $searchModel,
        ]);
    }*/

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     */
    /*
    public function actionCreate() 
    {
        
        $model = $this->module->manager->createUser(['scenario' => 'create']);
        $companyObj = new \backend\models\Company();
        
        $user=$this->user->identity;
       // var_dump($this->user->identity);die;
        $companyList = $companyObj->getCompanyList();

        //array_unshift($companyList, "select");
        $companyArray = \backend\models\Company::find()->select('category_id')->asArray()->where(['id' => $user['company_id']])->one();
         $RoleObj = new \backend\models\Roles();
 
        $roleIdArray = $RoleObj->getCategoryRole($companyArray['category_id']);
       $roleArray = $RoleObj->find()->asArray()->where(['id' => $this->getRoleId($roleIdArray)])->all();
       
        
        
        $rolelist = $RoleObj->getRoleList($roleArray);
        
        //array_unshift($rolelist, "select");
        //var_dump($rolelist);die;
        if ($model->load(\Yii::$app->request->post()) && $model->create()) {
            \Yii::$app->getSession()->setFlash('user.success', \Yii::t('user', 'User has been created'));
            return $this->redirect(['index']);
        }

        return $this->render('create', [
                    'model' => $model,
                    'companyList' => $companyList,
                    'RoleObj' => $RoleObj,
                    'rolelist' => $rolelist,
        ]);
    }*/
    
     public function getRoleId($roleIdArray) {

        $roleid = array();
        foreach ($roleIdArray as $rid) {

            array_push($roleid, $rid['role_id']);
        }

        return $roleid;
    }


    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'index' page.
     * @param  integer $id
     * @return mixed
     */
    /*
    public function actionUpdate($id) {
        $model = $this->findModel($id);
        $model->scenario = 'update';

        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            \Yii::$app->getSession()->setFlash('user.success', \Yii::t('user', 'User has been updated'));
            return $this->refresh();
        }

        return $this->render('update', [
                    'model' => $model
        ]);
    }*/

   



    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param  integer                    $id
     * @return \dektrium\user\models\User the loaded model
     * @throws NotFoundHttpException      if the model cannot be found
     */
    /*
    protected function findModel($id) {
        $user = $this->module->manager->findUserById($id);

        if ($user === null) {
            throw new NotFoundHttpException('The requested page does not exist');
        }

        return $user;
    }*/
    
    public function getUserPermissionRoleId($roles)
    {
        $roleArray=[];
        foreach($roles as $r)
        {
            array_push($roleArray,$r->role_id);
        }
        
        return $roleArray;
        
    }

}

?>