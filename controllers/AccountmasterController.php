<?php

namespace app\controllers;

use Yii;
use app\models\AccountMaster;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;

/**
 * GrnController implements the CRUD actions for Grn model.
 */
class AccountmasterController extends Controller
{
    public function actionIndex()
    {
        $acc_master = new AccountMaster();
        $pending = $acc_master->getAccountDetails("", "pending");
        $approved = $acc_master->getAccountDetails("", "approved");
        return $this->render('account_list', [
            'pending' => $pending, 'approved' => $approved,
        ]);
    }

    public function actionGetcode(){
        $acc_master = new AccountMaster();
        echo $acc_master->getCode();
    }

    public function actionCreate()
    {
        $acc_master = new AccountMaster();
        $category = $acc_master->getAccountCategories();
        $vendor = $acc_master->getVendors();
        $category_list = $acc_master->getBusinessCategories();

        // $vendor = json_encode($vendor);

        return $this->render('account_details', ['category' => $category, 'vendor' => $vendor, 'category_list' => $category_list]);
        // return $this->render('account_details', ['category' => $category]);
    }

    public function actionEdit($id)
    {
        $acc_master = new AccountMaster();
        $data = $acc_master->getAccountDetails($id, "");
        $category = $acc_master->getAccountCategories();
        $vendor = $acc_master->getVendors();
        $acc_category = $acc_master->getAccCategories($id);
        $category_list = $acc_master->getBusinessCategories();

        if(count($data)>0){
            if($data[0]['type']=="Vendor Goods"){
                $vendor_id = $data[0]['vendor_id'];
                $vendor = $acc_master->getVendors($vendor_id);
            }
        }
        
        return $this->render('account_details', ['data' => $data, 'category' => $category, 'vendor' => $vendor, 'acc_category' => $acc_category, 'category_list' => $category_list]);
    }

    public function actionView($id)
    {
        $acc_master = new AccountMaster();
        $data = $acc_master->getAccountDetails($id, "");
        $category = $acc_master->getAccountCategories();
        $vendor = $acc_master->getVendors();
        $acc_category = $acc_master->getAccCategories($id);
        $category_list = $acc_master->getBusinessCategories();

        if(count($data)>0){
            if($data[0]['type']=="Vendor Goods"){
                $vendor_id = $data[0]['vendor_id'];
                $vendor = $acc_master->getVendors($vendor_id);
            }
        }

        $action = 'view';
        
        return $this->render('account_details', ['data' => $data, 'category' => $category, 'vendor' => $vendor, 'acc_category' => $acc_category, 'category_list' => $category_list, 'action' => $action]);
    }

    public function actionSave()
    {   
        $acc_master = new AccountMaster();
        $result = $acc_master->save();
        $this->redirect(array('accountmaster/index'));
    }

    public function actionSavecategories(){
        $acc_master = new AccountMaster();
        $result = $acc_master->saveCategories();
        $category = $acc_master->getAccountCategories();
        echo json_encode($category);
        // echo $result;
    }

    public function actionGetcategories(){
        $acc_master = new AccountMaster();
        $category = $acc_master->getAccountCategories();
        echo json_encode($category);
    }

    public function actionGetvendors(){
        $acc_master = new AccountMaster();
        $vendor = $acc_master->getVendors();

        // foreach($vendor as $row) {
        //     $abc[] = array('value' => $row['vendor_code'], 'label' => $row['vendor_name']);
        // }
        
        // echo json_encode($abc);

        echo json_encode($vendor);
    }

    public function actionGetvendordetails(){
        $acc_master = new AccountMaster();
        $data = $acc_master->getVendorDetails();
        echo json_encode($data);
    }

    public function actionChecklegalnameavailablity(){
        $acc_master = new AccountMaster();
        $result = $acc_master->checkLegalNameAvailablity();
        echo $result;
    }
}
