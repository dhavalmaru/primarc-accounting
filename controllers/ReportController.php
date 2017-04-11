<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\db\Query;

class ReportController extends Controller{

    public function actionIndex()
    {

       return $this->render('index');
    }
    public function actionView(){
        return $this->render('view');
    }
	
	public function actionDownload(){
        return $this->render('download');
    }
	
	public function actionDownloads(){
        return $this->render('downloads');
    }
    
    public function actionUserPermissions() {
        $connection = \Yii::$app->db;
        $mainQuery = "select u.id as id, username, email, is_superadmin, is_systemadmin from user as u 
                    order by u.id"; 
        $result = $connection->createCommand($mainQuery)->queryAll();
        $i=0;$userpermission = array();
        foreach($result as $userinfo){
            $roleobj = \backend\models\Roles::find()->innerJoin('user_roles', 'roles.id=user_roles.role_id')->where(['user_roles.user_id'=>$userinfo['id']])->asArray()->all();

            
            //get user assign roles
            $getuserrole=ArrayHelper::map($roleobj, 'id', 'role_code');
            $getuserroleNameArray = array();
            $getuserroleName=ArrayHelper::map($roleobj, 'id', 'role_name');
            foreach($roleobj as $r)
            {
                $getuserroleName['role_id']=$r['id'];
                $getuserroleName['role_name']=$r['role_name'];
                $getuserroleNameArray[]=$getuserroleName;
            }
            
            $roles= array_keys($getuserrole);
            
            
            
            $userimage=\backend\models\Profile::find()->where(['user_id'=>$userinfo['id']])->asArray()->one();
            
            $user_image=$userimage['user_image'];
            $user_name=$userimage['name'];
            $user_phone=$userimage['phone'];
            $user_mobile=$userimage['mobile'];
            $user_fax=$userimage['fax'];



            //get user assign company
            $companyObj= \backend\models\CompanyMaster::find()->innerJoin("user_company_rel","company_master.id=user_company_rel.company_id")->where(['user_id'=>$userinfo['id']])->asArray()->all();

            if($companyObj)
            {
                $company_id=$companyObj[0]['id'];
                $company_name=$companyObj[0]['company_name'];
                $company_code=$companyObj[0]['company_code'];
            }else{

                $company_id=0;
                $company_name="SuperAdmin"; 
                $company_code='SuperAdmin';
            }

            
            $getAssignCategoriesObj=  \backend\models\RolePermission::find()->where(['role_id'=>$roles])->asArray()->all();
            $categorymarketArray=$this->getCategoriesMarketPlace($getAssignCategoriesObj);
            
            $transactions_authorize=$this->getTransactionAuthorize($getAssignCategoriesObj);
            $reports_authorize=$this->getReportAuthorize($getAssignCategoriesObj);

            $authorize_live_dashboard=$this->getLiveDashboardAuthorize($getAssignCategoriesObj);
            $authorize_b2c_sales=$this->getB2CAuthorize($getAssignCategoriesObj);

           // var_dump($categorymarketArray);die;
            $categoryObj=  \backend\models\ProductMainCategory::find()->where(['id'=>$categorymarketArray['category_id']])->asArray()->all();
            $categoryArray=ArrayHelper::map($categoryObj, 'id', 'category_code');


            $marketObj= \backend\models\MarketMaster::find()->where(['id'=>$categorymarketArray['marketplace_id']])->asArray()->all();
            $marketArray=ArrayHelper::map($marketObj, 'id', 'market_name');

            $marketplacePermissionArray=$this->getMarketPlacePermissionArray($marketObj);

            $warehouseObj= \backend\models\InternalWarehouseMaster::find()->where(['id'=>$categorymarketArray['warehouse_id']])->asArray()->all();
            $warehouseArray=ArrayHelper::map($warehouseObj, 'id', 'warehouse_name');

           //  var_dump($categorymarketArray['po_category_id']);die;
            $poObj=  \backend\models\ProductMainCategory::find()->where(['id'=>$categorymarketArray['po_category_id']])->asArray()->all();
            $poArray=ArrayHelper::map($categoryObj, 'id', 'category_code');

            $permission=$this->getScreens($getuserrole);    
            
			$get_resources_obj = \backend\models\Resource::find()->asArray()->all();
			$get_permissions_obj = \backend\models\Permission::find()->asArray()->all();
			$resource_permission_array = array();
			$resource_array = array();
			$permission_array = array();
			if($userinfo['is_systemadmin']!=1 && $userinfo['is_superadmin']!=1)
            {
				foreach($get_resources_obj as $resource){
					$permission_array = array();
					foreach($get_permissions_obj as $perm){
						$resource_permission = $resource['resource_code']."-".$perm['permission_code'];
						if(in_array($resource_permission,$permission)){
							$permission_array += array($perm['permission_code'] => 'Yes');
						}else{
							$permission_array += array($perm['permission_code'] => 'No');
						}


					}
					$resource_array += array($resource['resource_name']=>$permission_array);
				}
			}
			
            //get goods inwards details
            $warehousearray=$this->getWarehouseDetails($permission,$warehouseObj);

            $product_categories=$this->getProductCategoriesDetails($categoryObj);
            $purchaseOrder=$this->getPurchaseOrder($permission);

            $customerOrder=$this->getCustomerOrder($permission);

            $pocategories=$this->getPOCategoriesDetails($permission,$poObj);

            //report section
            $reports=$this->getReportPermission($permission);
            if($userinfo['is_systemadmin']==1 || $userinfo['is_superadmin']==1)
            {

                $isSystemAdmin=1;
                 $userpermission[$i]=[            
                "user_id"=>$userinfo['id'],
                "user_image"=>$user_image,
                "user_name"=>$user_name,
                "user_phone"=>$user_phone,
                "user_mobile"=>$user_mobile,
                "user_fax"=>$user_fax,
                "roles"=>$getuserroleNameArray,
                'username'=>$userinfo['username'],
                "email"=>$userinfo['email'],
                "company_id"=>$company_id,
                "company_name"=>$company_name,
                "company_code"=>$company_code,
                "product_categories"=>[],
                "warehouses"=>$isSystemAdmin,
                "market_places"=>$isSystemAdmin,
                "screens"=>$isSystemAdmin,
                'purchase_order'=>[],
                "po_categories"=>$isSystemAdmin,
                'transactions_authorize'=>$isSystemAdmin,
                'reports_authorize'=>$isSystemAdmin,
                'authorize_b2c_sales'=>$isSystemAdmin,
                'authorize_live_dashboard'=>$isSystemAdmin,
                'reports'=>$isSystemAdmin

                ];        
            
            }else{
                $userpermission[$i]=[  
                    "user_id"=>$userinfo['id'],
                    "roles"=>$getuserroleNameArray,
                    'username'=>$userinfo['username'],
                    "email"=>$userinfo['email'],
                    "company_name"=>$company_name,
                    "company_code"=>$company_code,
                    "product_categories"=>$product_categories,
                    "warehouses"=>$warehousearray,
                    "market_places"=>0,
                    "screens"=>$resource_array,
                    'purchase_order'=>$purchaseOrder,
                    'customer_order'=>$customerOrder,
                    "po_categories"=>$pocategories,
                    'transactions_authorize'=>$transactions_authorize,
                    'reports_authorize'=>$reports_authorize,
                    'authorize_b2c_sales'=>$authorize_b2c_sales,
                    'authorize_live_dashboard'=>$authorize_live_dashboard,
                    'reports'=>$reports
                    ];
                
            }$i++;
        }
        $fullpath="uploads/download/user_permission.xlsx";
        $objPHPExcel = \PHPExcel_IOFactory::load($fullpath);
        $commonModel= new \common\models\CommonCode();
        $company_id=$commonModel->getCompanyID();
        $rowCount = 3; 
        $oneScreen = '';
        $objPHPExcel->setActiveSheetIndex(0);
        foreach($userpermission as $row){
            $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $row['username']);
            $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $row['email']);
            $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $row['company_name']);
            $roleCount = $rowCount;
            $marketCount = $rowCount;
            if(!empty($row['roles'])){
                foreach($row['roles'] as $role){
                    if(isset($role['role_name']) && isset($role['role_id'])){
                        $objPHPExcel->getActiveSheet()->SetCellValue('D'.$roleCount, $role['role_name']);
                        if($row['market_places'] == 1){
                            $objPHPExcel->getActiveSheet()->SetCellValue('AF'.$marketCount, 'ALL');
                            $objPHPExcel->getActiveSheet()->SetCellValue('AG'.$marketCount, 'Yes');
                            $objPHPExcel->getActiveSheet()->SetCellValue('AH'.$marketCount, 'Yes');
                            $objPHPExcel->getActiveSheet()->SetCellValue('AI'.$marketCount, 'Yes');
                            $objPHPExcel->getActiveSheet()->SetCellValue('AJ'.$marketCount, 'Yes');
                            $objPHPExcel->getActiveSheet()->SetCellValue('AK'.$marketCount, 'Yes');
                            $marketCount++;
                        }else{
                            $market_ids_query='select marketplace_id from role_permission where role_id = '.$role['role_id'];
                            $market_ids = $connection->createCommand($market_ids_query)->queryAll();
                            if(!empty($market_ids)){
                                $market_entries = $this->getMarketPermission($market_ids[0]['marketplace_id']);
                                foreach($market_entries as $market_key => $market_permissions){
                                    $objPHPExcel->getActiveSheet()->SetCellValue('AF'.$marketCount, $market_key);
                                    foreach($market_permissions as $perm){
                                        if($perm == 'create'){
                                            $objPHPExcel->getActiveSheet()->SetCellValue('AG'.$marketCount, 'Yes');
                                        }elseif($perm == 'update'){
                                            $objPHPExcel->getActiveSheet()->SetCellValue('AH'.$marketCount, 'Yes');
                                        }elseif($perm == 'A1'){
                                            $objPHPExcel->getActiveSheet()->SetCellValue('AI'.$marketCount, 'Yes');
                                        }elseif($perm == 'A2'){
                                            $objPHPExcel->getActiveSheet()->SetCellValue('AJ'.$marketCount, 'Yes');
                                        }elseif($perm == 'deactive'){
                                            $objPHPExcel->getActiveSheet()->SetCellValue('AK'.$marketCount, 'Yes');
                                        }
                                    }
                                    $marketCount++;
                                }
                            }
                        }
                        $roleCount++;
                    }
                }
            }
            $WHCount = $rowCount;
            $WH_module_count = $rowCount;
            if(!empty($row['warehouses']) && $row['warehouses'] != 1){
                if(!$row['warehouses']['permissions']){
                    
                    foreach($row['warehouses'] as $key => $value){
                        $objPHPExcel->getActiveSheet()->SetCellValue('J'.$WH_module_count, $key);
                        if(isset($value['Authorised']) && $value['Authorised'] == 1){
                            $objPHPExcel->getActiveSheet()->SetCellValue('K'.$WH_module_count, 'Yes');
                        }else{
                            $objPHPExcel->getActiveSheet()->SetCellValue('K'.$WH_module_count, 'No');
                        }
                        if(isset($value['permissions']) && !empty($value['permissions'])){
                            foreach($value['permissions'] as $perm){
                                if($perm['create'] == 1){
                                    $objPHPExcel->getActiveSheet()->SetCellValue('L'.$WH_module_count, 'Yes');
                                }else{
                                    $objPHPExcel->getActiveSheet()->SetCellValue('L'.$WH_module_count, 'No');
                                }
                                if($perm['read'] == 1){
                                    $objPHPExcel->getActiveSheet()->SetCellValue('M'.$WH_module_count, 'Yes');
                                }else{
                                    $objPHPExcel->getActiveSheet()->SetCellValue('M'.$WH_module_count, 'No');
                                }
                                if($perm['update'] == 1){
                                    $objPHPExcel->getActiveSheet()->SetCellValue('N'.$WH_module_count, 'Yes');
                                }else{
                                    $objPHPExcel->getActiveSheet()->SetCellValue('N'.$WH_module_count, 'No');
                                }
                                if($perm['approve1'] == 1){
                                    $objPHPExcel->getActiveSheet()->SetCellValue('O'.$WH_module_count, 'Yes');
                                }else{
                                    $objPHPExcel->getActiveSheet()->SetCellValue('O'.$WH_module_count, 'No');
                                }
                                if($perm['approve2'] == 1){
                                    $objPHPExcel->getActiveSheet()->SetCellValue('P'.$WH_module_count, 'Yes');
                                }else{
                                    $objPHPExcel->getActiveSheet()->SetCellValue('P'.$WH_module_count, 'No');
                                }
                            }
                        }$WH_module_count++;
                    }
                }
                foreach($row['warehouses']['permissions'] as $WH){
                    if(isset($WH['warehouse_code'])){
                        $objPHPExcel->getActiveSheet()->SetCellValue('E'.$WHCount, $WH['warehouse_code']);
                    }
                    if(isset($WH['warehouse_name'])){
                        $objPHPExcel->getActiveSheet()->SetCellValue('F'.$WHCount, $WH['warehouse_name']);
                    }
                    if(isset($WH['permissions'])){
                        foreach($WH['permissions'] as $perm){
                            if($perm['create'] == 1){
                                $objPHPExcel->getActiveSheet()->SetCellValue('G'.$WHCount, 'Yes');
                            }else{
                                $objPHPExcel->getActiveSheet()->SetCellValue('G'.$WHCount, 'No');
                            }
                            if($perm['read'] == 1){
                                $objPHPExcel->getActiveSheet()->SetCellValue('H'.$WHCount, 'Yes');
                            }else{
                                $objPHPExcel->getActiveSheet()->SetCellValue('H'.$WHCount, 'No');
                            }
                            if($perm['update'] == 1){
                                $objPHPExcel->getActiveSheet()->SetCellValue('I'.$WHCount, 'Yes');
                            }else{
                                $objPHPExcel->getActiveSheet()->SetCellValue('I'.$WHCount, 'No');
                            }
                        }
                    }
                    $WHCount++;
                }
            }else{
                $objPHPExcel->getActiveSheet()->SetCellValue('E'.$WHCount, 'ALL');
                $objPHPExcel->getActiveSheet()->SetCellValue('F'.$WHCount, 'ALL');
                $objPHPExcel->getActiveSheet()->SetCellValue('G'.$WHCount, 'Yes');
                $objPHPExcel->getActiveSheet()->SetCellValue('H'.$WHCount, 'Yes');
                $objPHPExcel->getActiveSheet()->SetCellValue('I'.$WHCount, 'Yes');
                $objPHPExcel->getActiveSheet()->SetCellValue('J'.$WHCount, 'ALL');
                $objPHPExcel->getActiveSheet()->SetCellValue('K'.$WHCount, 'Yes');
                $objPHPExcel->getActiveSheet()->SetCellValue('L'.$WHCount, 'Yes');
                $objPHPExcel->getActiveSheet()->SetCellValue('M'.$WHCount, 'Yes');
                $objPHPExcel->getActiveSheet()->SetCellValue('N'.$WHCount, 'Yes');
                $objPHPExcel->getActiveSheet()->SetCellValue('O'.$WHCount, 'Yes');
                $objPHPExcel->getActiveSheet()->SetCellValue('P'.$WHCount, 'Yes');
            }
            
            $CatCount = $rowCount;
            if(!empty($row['po_categories']) && $row['po_categories'] != 1){
                foreach($row['po_categories'] as $cat){
                    $objPHPExcel->getActiveSheet()->SetCellValue('Q'.$CatCount, $cat['category_code']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('R'.$CatCount, $cat['category_name']);
                    foreach($cat['permissions'] as $perm){
                        if($perm['create'] == 1){
                            $objPHPExcel->getActiveSheet()->SetCellValue('S'.$CatCount, 'Yes');
                        }else{
                            $objPHPExcel->getActiveSheet()->SetCellValue('S'.$CatCount, 'No');
                        }
                        if($perm['read'] == 1){
                            $objPHPExcel->getActiveSheet()->SetCellValue('T'.$CatCount, 'Yes');
                        }else{
                            $objPHPExcel->getActiveSheet()->SetCellValue('T'.$CatCount, 'No');
                        }
                        if($perm['update'] == 1){
                            $objPHPExcel->getActiveSheet()->SetCellValue('U'.$CatCount, 'Yes');
                        }else{
                            $objPHPExcel->getActiveSheet()->SetCellValue('U'.$CatCount, 'No');
                        }
                        if($perm['approve1'] == 1){
                            $objPHPExcel->getActiveSheet()->SetCellValue('V'.$CatCount, 'Yes');
                        }else{
                            $objPHPExcel->getActiveSheet()->SetCellValue('V'.$CatCount, 'No');
                        }
                        if($perm['approve2'] == 1){
                            $objPHPExcel->getActiveSheet()->SetCellValue('W'.$CatCount, 'Yes');
                        }else{
                            $objPHPExcel->getActiveSheet()->SetCellValue('W'.$CatCount, 'No');
                        }
                        if($perm['deactive'] == 1){
                            $objPHPExcel->getActiveSheet()->SetCellValue('X'.$CatCount, 'Yes');
                        }else{
                            $objPHPExcel->getActiveSheet()->SetCellValue('X'.$CatCount, 'No');
                        }
                    }

                    $CatCount++;
                }
            }else{
                $objPHPExcel->getActiveSheet()->SetCellValue('Q'.$CatCount, 'ALL');
                $objPHPExcel->getActiveSheet()->SetCellValue('R'.$CatCount, 'ALL');
                $objPHPExcel->getActiveSheet()->SetCellValue('S'.$CatCount, 'Yes');
                $objPHPExcel->getActiveSheet()->SetCellValue('T'.$CatCount, 'Yes');
                $objPHPExcel->getActiveSheet()->SetCellValue('U'.$CatCount, 'Yes');
                $objPHPExcel->getActiveSheet()->SetCellValue('V'.$CatCount, 'Yes');
                $objPHPExcel->getActiveSheet()->SetCellValue('W'.$CatCount, 'Yes');
                $objPHPExcel->getActiveSheet()->SetCellValue('X'.$CatCount, 'Yes');
            }
            if($row['transactions_authorize'] == 1){
                $objPHPExcel->getActiveSheet()->SetCellValue('Y'.$rowCount, 'Yes');
            }else{
                $objPHPExcel->getActiveSheet()->SetCellValue('Y'.$rowCount, 'No');
            }
            if($row['reports_authorize'] == 1){
                $objPHPExcel->getActiveSheet()->SetCellValue('Z'.$rowCount, 'Yes');
            }else{
                $objPHPExcel->getActiveSheet()->SetCellValue('Z'.$rowCount, 'No');
            }
            if($row['authorize_b2c_sales'] == 1){
                $objPHPExcel->getActiveSheet()->SetCellValue('AA'.$rowCount, 'Yes');
            }else{
                $objPHPExcel->getActiveSheet()->SetCellValue('AA'.$rowCount, 'No');
            }
            if($row['authorize_live_dashboard'] == 1){
                $objPHPExcel->getActiveSheet()->SetCellValue('AB'.$rowCount, 'Yes');
            }else{
                $objPHPExcel->getActiveSheet()->SetCellValue('AB'.$rowCount, 'No');
            }
            $reportCount = $rowCount;
            if($row['reports'] != 1){
                foreach($row['reports'] as $key => $value){
                    $objPHPExcel->getActiveSheet()->SetCellValue('AC'.$reportCount, $key);
                    if($value['create'] == 1){
                        $objPHPExcel->getActiveSheet()->SetCellValue('AD'.$reportCount, 'Yes');
                    }else{
                        $objPHPExcel->getActiveSheet()->SetCellValue('AD'.$reportCount, 'No');
                    }
                    if($value['read'] == 1){
                        $objPHPExcel->getActiveSheet()->SetCellValue('AE'.$reportCount, 'Yes');
                    }else{
                        $objPHPExcel->getActiveSheet()->SetCellValue('AE'.$reportCount, 'No');
                    }
                    $reportCount++;
                }
            }else{
                $objPHPExcel->getActiveSheet()->SetCellValue('AC'.$reportCount, 'ALL');
                $objPHPExcel->getActiveSheet()->SetCellValue('AD'.$reportCount, 'Yes');
                $objPHPExcel->getActiveSheet()->SetCellValue('AE'.$reportCount, 'Yes');
            }
            
			
			$module_count = $rowCount;
            if($row['screens'] != 1){
				foreach($row['screens'] as $key => $value){
					$objPHPExcel->getActiveSheet()->SetCellValue('AL'.$module_count, $key);
					foreach($value as $valkey => $val){
						switch ($valkey){
							case 'create':$objPHPExcel->getActiveSheet()->SetCellValue('AM'.$module_count, $val);break;
							case 'index':$objPHPExcel->getActiveSheet()->SetCellValue('AN'.$module_count, $val);break;
							case 'update':$objPHPExcel->getActiveSheet()->SetCellValue('AO'.$module_count, $val);break;
							case 'A1':$objPHPExcel->getActiveSheet()->SetCellValue('AP'.$module_count, $val);break;
							case 'A2':$objPHPExcel->getActiveSheet()->SetCellValue('AQ'.$module_count, $val);break;
							case 'deactive':$objPHPExcel->getActiveSheet()->SetCellValue('AR'.$module_count, $val);break;
						}
					}
					$module_count++;
				}
            }else{
                $objPHPExcel->getActiveSheet()->SetCellValue('AL'.$module_count, 'ALL');
                $objPHPExcel->getActiveSheet()->SetCellValue('AM'.$module_count, 'Yes');
                $objPHPExcel->getActiveSheet()->SetCellValue('AN'.$module_count, 'Yes');
				$objPHPExcel->getActiveSheet()->SetCellValue('AO'.$module_count, 'Yes');
                $objPHPExcel->getActiveSheet()->SetCellValue('AP'.$module_count, 'Yes');
			    $objPHPExcel->getActiveSheet()->SetCellValue('AQ'.$module_count, 'Yes');
                $objPHPExcel->getActiveSheet()->SetCellValue('AR'.$module_count, 'Yes');
            }
            $maxcount = max($reportCount,$CatCount,$WHCount,$WH_module_count,$roleCount,$marketCount,$module_count);
            $rowCount = $maxcount + 1;
            
            
        }
        header('Content-Type: application/vnd.ms-excel'); 
        header('Content-Disposition: attachment;filename="Audit_report.xlsx"'); 
        header('Cache-Control: max-age=0'); 
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');
        $objWriter->save('php://output');
        
    }
    
    public function getMarketPermission($role_market_ids){
        $connection = \Yii::$app->db;
        $market_place_ids = explode(',', $role_market_ids);
        $market_perm1 = array();
        $market_ids = ['1'=>'Amazon','2'=>'Snapdeal','3'=>'ShopClues','4'=>'Flipkart','9'=>'Snapdeal','10'=>'Amazon','11'=>'ShopClues','12'=>'Snapdeal','13'=>'Paytm','14'=>'Flipkart','15'=>'Zotezo','16'=>'Medisyskart','17'=>'Shilajit Gold','18'=>'Shopclues','19'=>'Rediff Shopping','20'=>'Ask me Bazaar','21'=>'Ebay','22'=>'Royzez','23'=>'Unicommerce','24'=>'Test MC','25'=>'Test Account Market','26'=>'TEST','27'=>'Amazon','28'=>'TMPPM','29'=>'Test MPP Sheet','30'=>'Test MC','31'=>'LimeRoad','32'=>'PAYTM'];
        $market_query = "SELECT
                        SUBSTRING_INDEX(name,'-',4) as module,
                        SUBSTRING_INDEX(SUBSTRING_INDEX(name,'-',-2),'-',1) permission,
                        SUBSTRING_INDEX(name,'-',-1) as ids
                        from auth_item
                        where name like '%market-place-product-master%'"; 
        $market1 = $connection->createCommand($market_query)->queryAll();
        $arr11 = array();
        foreach($market1 as $market_perm){
            if($market_perm['ids']!= 'master')
            {
                $market_perm['market_name'] = $market_ids[$market_perm['ids']];  
                $arr11[] = $market_perm;
            }
        }
        foreach($market_place_ids as $market_id){
            foreach($arr11 as $arr){
                if($arr['ids'] == $market_id){
                    $market_perm1[$arr['market_name']][] = $arr['permission'];
                }
            }
        }
        
        return $market_perm1;
        
    }
    
    public function getReportPermission($permission)
    {
        $report=[];
        $reportmasterobj=  \backend\models\ReportPermissionMaster::find()->asArray()->all();
        foreach($reportmasterobj as $reportmaster)
        {
            $create=$reportmaster['report_code']."-"."create";
            $read=$reportmaster['report_code']."-"."index";
            $code=$reportmaster['report_code'];
            if(in_array($create,$permission))
            {
                $report[$code]['create']=1;
            }else{
                
                $report[$code]['create']=0;
            }
            
            if(in_array($read,$permission))
            {
                $report[$code]['read']=1;
            }else{
                
                $report[$code]['read']=0;
            }
            
        }
        
        return $report;
        
        
    }
    public function getTransactionAuthorize($getAssignCategoriesObj)
    {
        
        foreach($getAssignCategoriesObj as $transauth)
        {
            if($transauth['transactions']==1)
            {
                return 1;
            }
            
        }
        
        return 0;
        
    }
    public function getReportAuthorize($getAssignCategoriesObj)
    {
        
        foreach($getAssignCategoriesObj as $reportauth)
        {
            if($reportauth['reports']==1)
            {
                return 1;
            }
            
        }
        
        return 0;
        
        
    }
  
    public function getLiveDashboardAuthorize($getAssignCategoriesObj)
    {
        
        foreach($getAssignCategoriesObj as $transauth)
        {
            if($transauth['authorize_live_dashboard']==1)
            {
                return 1;
            }
            
        }
        
        return 0;
        
    }
    public function getB2CAuthorize($getAssignCategoriesObj)
    {
        
        foreach($getAssignCategoriesObj as $reportauth)
        {
            if($reportauth['authorize_b2c_sales']==1)
            {
                return 1;
            }
            
        }
        
        return 0;
        
        
    }
    
    public function getMarketPlacePermissionArray($marketObj)
    {
        $marketArray=array();
        $marketplacePermission=array();
        foreach($marketObj as $mkt)
        {
            
            $marketArray['market_id']=$mkt['id'];
            $marketArray['market_name']=$mkt['market_name'];
             $marketArray['market_code']=$mkt['market_code'];
            $marketplacePermission[]=$marketArray;
        }
        //var_dump($marketplacePermission);die;
        return $marketplacePermission;
        
    }
    
    public function getPurchaseOrder($permission)
    {
         $poArray=array();
         $poArray['create']=0;
        if(in_array("purchase-order-create",$permission))
        {
            $poArray['create']=1;
        }
        $poArray['read']=0;
         if(in_array("purchase-order-index",$permission))
        {
            $poArray['read']=1;
        }
        $poArray['update']=0;
         if(in_array("purchase-order-update",$permission))
        {
            $poArray['update']=1;
        }
        
        $poArray['approve1']=0;
         if(in_array("purchase-order-A1",$permission))
        {
            $poArray['approve1']=1;
        }
        
        $poArray['approve2']=0;
         if(in_array("purchase-order-A2",$permission))
        {
            $poArray['approve2']=1;
        }
        
        return $poArray;
        
        
        
    }
      
    public function getCustomerOrder($permission)
    {
         $coArray=array();
         $coArray['create']=0;
        if(in_array("customer-order-create",$permission))
        {
            $coArray['create']=1;
        }
        $coArray['read']=0;
         if(in_array("customer-order-index",$permission))
        {
            $coArray['read']=1;
        }
        $coArray['update']=0;
         if(in_array("customer-order-update",$permission))
        {
            $coArray['update']=1;
        }
        
        $coArray['approve1']=0;
         if(in_array("customer-order-A1",$permission))
        {
            $coArray['approve1']=1;
        }
        
        $coArray['approve2']=0;
         if(in_array("customer-order-A2",$permission))
        {
            $coArray['approve2']=1;
        }
        
        return $coArray;
        
        
        
    }
    public function getWarehouseDetails($permission,$warehouseObj){
        
        $goods_inwards=$this->getGoodsInward($permission);
        $goods_outward=$this->getGoodsOutward($permission);
        $grn=$this->getGoodsReceiveNotification($permission);
        $prepare_go=$this->getPrepareGO($permission);
        $sku_movements=$this->getSKUMovements($permission);
        $warehouse_inventory=$this->getWarehouseInventory($permission);
        $warehouse=$this->getWarehouse($permission,$warehouseObj);      
       
        $finalwarehouseArray['goods_inward']=$goods_inwards;
        $finalwarehouseArray['goods_outward']=$goods_outward;        
        $finalwarehouseArray['grn']=$grn;
        
        $finalwarehouseArray['prepare_go']=$prepare_go;
        $finalwarehouseArray['sku_movements']=$sku_movements;
        $finalwarehouseArray['warehouse_inventory']=$warehouse_inventory;
        
        $finalwarehouseArray['permissions']=$warehouse;
        
        //var_dump($finalwarehouseArray[3]);die;
        
        return $finalwarehouseArray;
        
        
    }
    public function getPrepareGO($permission){
        
        $prepare_go=array();
        if(in_array("prepare-go-enable",$permission))
        {
            $prepare_go['Authorised']=1;
        }else{
            $prepare_go['Authorised']=0;
        }
        
        if(in_array("prepare-go-create",$permission))
        {
            $prepare_go['permissions']['create']=1;
        }else{
            $prepare_go['permissions']['create']=0;
        }
        
        if(in_array("prepare-go-index",$permission))
        {
            $prepare_go['permissions']['read']=1;
        }else{
             $prepare_go['permissions']['read']=0;
        }
        
        if(in_array("prepare-go-update",$permission))
        {
            $prepare_go['permissions']['update']=1;
        }else{
            $prepare_go['permissions']['update']=0;
        }
        
        if(in_array("prepare-go-A1",$permission))
        {
            $prepare_go['permissions']['approve1']=1;
        }else{
            $prepare_go['permissions']['approve1']=0;
        }
        
        if(in_array("prepare-go-A2",$permission))
        {
            $prepare_go['permissions']['approve2']=1;
        }
        else
        {
            $prepare_go['permissions']['approve2']=0;
        }
        
        return $prepare_go;
    }
    
      
     public function getSKUMovements($permission){
        
        $sku_movements=array();
        if(in_array("sku-movements-enable",$permission))
        {
            $sku_movements['Authorised']=1;
        }else{
            $sku_movements['Authorised']=0;
        }
        
        if(in_array("sku-movements-create",$permission))
        {
            $sku_movements['permissions']['create']=1;
        }else{
            $sku_movements['permissions']['create']=0;
        }
        
        if(in_array("sku-movements-index",$permission))
        {
            $sku_movements['permissions']['read']=1;
        }else{
             $sku_movements['permissions']['read']=0;
        }
        
        if(in_array("sku-movements-update",$permission))
        {
            $sku_movements['permissions']['update']=1;
        }else{
            $sku_movements['permissions']['update']=0;
        }
        
        if(in_array("sku-movements-A1",$permission))
        {
            $sku_movements['permissions']['approve1']=1;
        }
        else{
            
            $sku_movements['permissions']['approve1']=0;
        }
        
        if(in_array("sku-movements-A2",$permission))
        {
            $sku_movements['permissions']['approve2']=1;
        }
        else
        {
            $sku_movements['permissions']['approve2']=0;
        }
        
        return $sku_movements;
    }
    
     public function getWarehouseInventory($permission){
        
        $warehouse_inventory=array();
        if(in_array("warehouse-inventory-enable",$permission))
        {
            $warehouse_inventory['Authorised']=1;
        }else{
            $warehouse_inventory['Authorised']=0;
        }
        
        if(in_array("warehouse-inventory-create",$permission))
        {
            $warehouse_inventory['permissions']['create']=1;
        }else{
            $warehouse_inventory['permissions']['create']=0;
        }
        
        if(in_array("warehouse-inventory-index",$permission))
        {
            $warehouse_inventory['permissions']['read']=1;
        }else{
             $warehouse_inventory['permissions']['read']=0;
        }
        
        if(in_array("warehouse-inventory-update",$permission))
        {
            $warehouse_inventory['permissions']['update']=1;
        }else{
            $warehouse_inventory['permissions']['update']=0;
        }
        
        if(in_array("warehouse-inventory-A1",$permission))
        {
            $warehouse_inventory['permissions']['approve1']=1;
        }else{
            $warehouse_inventory['permissions']['approve1']=0;
        }
        
        if(in_array("warehouse-inventory-A2",$permission))
        {
            $warehouse_inventory['permissions']['approve2']=1;
        }
        else
        {
            $warehouse_inventory['permissions']['approve2']=0;
        }
        
        return $warehouse_inventory;
    }
    
    public function getProductCategoriesDetails($categoryObj)
    {
        $categoryArray=array();
        $auth = Yii::$app->authManager;
        $systemrolesObjs=$auth->getRoles();
        
        $systemroles= array_keys($systemrolesObjs);
        
        $userroleObj= \backend\models\UserRoles::find()->select("user.username,user_roles.user_id,roles.role_name")->leftJoin("roles","roles.id=user_roles.role_id")->leftJoin("user","user.id=user_roles.user_id")->asArray()->all();
                
        $userRolePermission=$this->getUserRole($userroleObj);
        
       $productCategoryArray=array();
      // var_dump($categoryObj);die;
        foreach($categoryObj as $category)
        {
                $categoryArray=array();
                $categoryArray['category_id']=$category['id'];
                $categoryArray['category_code']=$category['category_code'];
                $categoryArray['category_name']=$category['category_name'];
            //    $level1=array();
           //     $level2=array();
                
            /*    foreach($systemroles as $roles)
                {
                    $permissionsObj=$auth->getPermissionsByRole($roles);
                    $permissions= array_keys($permissionsObj);
                    $categoryScreenA1="product-category-A1-".$category['id'];
                    if(in_array($categoryScreenA1,$permissions))
                    {
                        $level1[]=$userRolePermission[$roles];
                        
                    }
                    
                    $categoryScreenA2="product-category-A2-".$category['id'];
                    if(in_array($categoryScreenA2,$permissions))
                    {
                         $level2[]=$userRolePermission[$roles];
                        
                    }
                    
                }
                 $categoryArray['approval_levels']['level_1']=$level1;
                 $categoryArray['approval_levels']['level_2']=$level2;
                */
              $productCategoryArray[]=$categoryArray;  
        }
        
       // var_dump($productCategoryArray);die;
        return $productCategoryArray;
        
    }
    
    public function getPOCategoriesDetails($permission,$poObj)
    {
        $poArray=array();
        $poPermissions=array();
        foreach($poObj as $po)
        {
                $poArray['category_id']=$po['id'];
                $poArray['category_code']=$po['category_code'];
                $poArray['category_name']=$po['category_name'];
                
                if(in_array("purchase-order-category-create-".$po['id'],$permission))
                {
                    
                    $poArray['permissions']['create']=1;
                    
                }else{
                    
                    $poArray['permissions']['create']=0;
                }
                
                
                 if(in_array("purchase-order-category-index-".$po['id'],$permission))
                {
                    
                    $poArray['permissions']['read']=1;
                    
                }else{
                    
                    $poArray['permissions']['read']=0;
                }
                
                if(in_array("purchase-order-category-update-".$po['id'],$permission))
                {
                    
                    $poArray['permissions']['update']=1;
                    
                }else{
                    
                    $poArray['permissions']['update']=0; 
                }  
                
                if(in_array("purchase-order-category-A1-".$po['id'],$permission))
                {
                    
                    $poArray['permissions']['approve1']=1;
                    
                }else{
                    
                    $poArray['permissions']['approve1']=0; 
                }  
                
                if(in_array("purchase-order-category-A2-".$po['id'],$permission))
                {
                    
                    $poArray['permissions']['approve2']=1;
                    
                }else{
                    
                    $poArray['permissions']['approve2']=0; 
                }  
                
                if(in_array("purchase-order-category-deactive-".$po['id'],$permission))
                {
                    
                    $poArray['permissions']['deactive']=1;
                    
                }else{
                    
                    $poArray['permissions']['deactive']=0; 
                }  
                
                $poPermissions[]=$poArray;   
              
        }
        
        return $poPermissions;
        
    }
    
    public function getUserRole($userroleObj)
    {
        $userroleArray=array();
        //var_dump($userroleObj);die;
        foreach($userroleObj as $userrole)
        {
            $userArray=array();
            $roleName=$userrole['role_name'];
            $userroleArray[$roleName][$userrole['user_id']]=$userrole['username'];
            
        }
        //var_dump($userroleArray);die;
        return $userroleArray;
        
    }
    public function getGoodsInward($permission){
        
        
        if(in_array("goods-inward-enable",$permission))
        {
            $goods_inwards['Authorised']=1;
        }else{
            $goods_inwards['Authorised']=0;
        }
        
        if(in_array("goods-inward-create",$permission))
        {
            $goods_inwards['permissions']['create']=1;
        }else{
            $goods_inwards['permissions']['create']=0;
        }
        
        if(in_array("goods-inward-index",$permission))
        {
            $goods_inwards['permissions']['read']=1;
        }else{
             $goods_inwards['permissions']['read']=0;
        }
        
        if(in_array("goods-inward-update",$permission))
        {
            $goods_inwards['permissions']['update']=1;
        }else{
            $goods_inwards['permissions']['update']=0;
        }
        
        if(in_array("goods-inward-A1",$permission))
        {
            $goods_inwards['permissions']['approve1']=1;
        }else{
            $goods_inwards['permissions']['approve1']=0;
        }
        
        if(in_array("goods-inward-A2",$permission))
        {
            $goods_inwards['permissions']['approve2']=1;
        }else{
            $goods_inwards['permissions']['approve2']=0;
        }
        
        return $goods_inwards;
    }
    public function getGoodsOutward($permission){
        
        $goods_outward=array();
        if(in_array("goods-outward-enable",$permission))
        {
            $goods_outward['Authorised']=1;
        }else{
            $goods_outward['Authorised']=0;
        }
        
        if(in_array("goods-outward-create",$permission))
        {
            $goods_outward['permissions']['create']=1;
        }else{
            $goods_outward['permissions']['create']=0;
        }
        
        if(in_array("goods-outward-index",$permission))
        {
            $goods_outward['permissions']['read']=1;
        }else{
             $goods_outward['permissions']['read']=0;
        }
        
         if(in_array("goods-outward-update",$permission))
        {
            $goods_outward['permissions']['update']=1;
        }else{
            $goods_outward['permissions']['update']=0;
        }
        
        if(in_array("goods-outward-A1",$permission))
        {
            $goods_outward['permissions']['approve1']=1;
        }else{
            $goods_outward['permissions']['approve1']=0;
        }
        
        if(in_array("goods-outward-A2",$permission))
        {
            $goods_outward['permissions']['approve2']=1;
        }else{
            $goods_outward['permissions']['approve2']=0;
        }
        
        return $goods_outward;
    }
    
    
     public function getGoodsReceiveNotification($permission){
        
        $grn=array();
        if(in_array("goods-receive-notification-enable",$permission))
        {
            $grn['Authorised']=1;
        }else{
            $grn['Authorised']=0;
        }
        
        if(in_array("goods-receive-notification-create",$permission))
        {
            $grn['permissions']['create']=1;
        }else{
            $grn['permissions']['create']=0;
        }
        
        if(in_array("goods-receive-notification-index",$permission))
        {
            $grn['permissions']['read']=1;
        }else{
             $grn['permissions']['read']=0;
        }
        
         if(in_array("goods-receive-notification-update",$permission))
        {
            $grn['permissions']['update']=1;
        }else{
            $grn['permissions']['update']=0;
        }
        
        if(in_array("goods-receive-notification-A1",$permission))
        {
            $grn['permissions']['approve1']=1;
        }else{
            $grn['permissions']['approve1']=0;
        }
        
        if(in_array("goods-receive-notification-A2",$permission))
        {
            $grn['permissions']['approve2']=1;
        }else{
            $grn['permissions']['approve2']=0;
        }
        
        return $grn;
    }
    public function getWarehouse($permission,$warehouseObj)
    {
        $warehouseArray=array();
        $warehousePermissions=array();
        foreach($warehouseObj as $warehouse)
        {
                $warehouseArray['warehouse_id']=$warehouse['id'];
                $warehouseArray['warehouse_code']=$warehouse['warehouse_code'];
                $warehouseArray['warehouse_name']=$warehouse['warehouse_name'];
                
                if(in_array("warehouse-create-".$warehouse['id'],$permission))
                {
                    
                    $warehouseArray['permissions']['create']=1;
                    
                }else{
                    
                    $warehouseArray['permissions']['create']=0;
                }
                
                
                 if(in_array("warehouse-index-".$warehouse['id'],$permission))
                {
                    
                    $warehouseArray['permissions']['read']=1;
                    
                }else{
                    
                    $warehouseArray['permissions']['read']=0;
                }
                
                if(in_array("warehouse-update-".$warehouse['id'],$permission))
                {
                    
                    $warehouseArray['permissions']['update']=1;
                    
                }else{
                    
                    $warehouseArray['permissions']['update']=0; 
                }  
                $warehousePermissions[]=$warehouseArray;   
              
        }
        
        return $warehousePermissions;
        
    }
    
    
    public function getCategoriesMarketPlace($getAssignCategoriesObj)
    {
        
        $categorystring="";
        $marketplacestring="";
        $warehousestring="";
        $categorymarketArray=array();
        $i=0;
        $postring = '';
        foreach($getAssignCategoriesObj as $category)
        {
            if($i==0)
            {
                $categorystring=$category['category_id'];
                $marketplacestring=$category['marketplace_id'];
                $warehousestring=$category['warehouse_id'];
                $postring=$category['po_category_id'];
                
            }else{
                
                $categorystring.=",".$category['category_id'];
                $marketplacestring.=$category['marketplace_id'];
                $warehousestring.=$category['warehouse_id'];
                $postring=$category['po_category_id'];
            }
            
        }
        $categorymarketArray['category_id']=array(0);
        $categorymarketArray['marketplace_id']=array(0);
        $categorymarketArray['warehouse_id']=array(0);
        $categorymarketArray['po_category_id']=array(0);
        
        if($categorystring!="")
        {
        $categorymarketArray['category_id']=  array_unique(explode(",",$categorystring));
        }
        if($marketplacestring!="")
        {
        $categorymarketArray['marketplace_id']=  array_unique(explode(",",$marketplacestring));
        }
        if($warehousestring!="")
        {
        $categorymarketArray['warehouse_id']=  array_unique(explode(",",$warehousestring));
        }
        if($postring!="")
        {
        $categorymarketArray['po_category_id']=  array_unique(explode(",",$postring));
        }
        
        return $categorymarketArray;
        
    }
    public function getScreens($roles){
        
        $auth = Yii::$app->authManager;
        $permissionArray=array();
        foreach($roles as $role)
        {
            
            $permission=$auth->getPermissionsByRole($role);        
            $permissionArray=array_merge($permissionArray,$permission);
        
        }
        
       $permissionArray=array_keys($permissionArray);
        
       return array_unique($permissionArray);
        
    }
}