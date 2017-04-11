<?php

namespace app\controllers;

use Yii;
use app\models\JournalVoucher;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;

/**
 * GrnController implements the CRUD actions for Grn model.
 */
class JournalvoucherController extends Controller
{
    public function actionIndex()
    {
        $journal_voucher = new JournalVoucher();
        $pending = $journal_voucher->getJournalVoucherDetails("", "pending");
        $approved = $journal_voucher->getJournalVoucherDetails("", "approved");
        return $this->render('journalvoucher_list', [
            'pending' => $pending, 'approved' => $approved,
        ]);
    }

    public function actionGetaccdetails(){
        $journal_voucher = new JournalVoucher();
        $request = Yii::$app->request;
        $acc_id = $request->post('acc_id');
        // $acc_id = '43';
        // $acc_code = '';
        $data = $journal_voucher->getAccountDetails($acc_id);
        // if(count($data)>0){
        //     $acc_code = $data[0]['code'];
        // }
        echo json_encode($data);
    }

    public function actionCreate()
    {
        $journal_voucher = new JournalVoucher();
        $acc_details = $journal_voucher->getAccountDetails();

        // $vendor = json_encode($vendor);

        return $this->render('journalvoucher_details', ['acc_details' => $acc_details]);
        // return $this->render('journalvoucher_details', ['category' => $category]);
    }

    public function actionSave()
    {   
        $journal_voucher = new JournalVoucher();
        $result = $journal_voucher->save();
        $this->redirect(array('journalvoucher/index'));
    }

    public function actionGetcode(){
        $journal_voucher = new JournalVoucher();
        echo $journal_voucher->getCode();
    }

    public function actionEdit($id)
    {
        $journal_voucher = new JournalVoucher();
        $data = $journal_voucher->getJournalVoucherDetails($id, "");
        $acc_details = $journal_voucher->getAccountDetails();
        $jv_entries = $journal_voucher->gerJournalVoucherEntries($id);
        
        return $this->render('journalvoucher_details', ['data' => $data, 'acc_details' => $acc_details, 'jv_entries' => $jv_entries]);
    }

    public function actionSavecategories(){
        $journal_voucher = new JournalVoucher();
        $result = $journal_voucher->saveCategories();
        $category = $journal_voucher->getAccountCategories();
        echo json_encode($category);
        // echo $result;
    }

    public function actionGetcategories(){
        $journal_voucher = new JournalVoucher();
        $category = $journal_voucher->getAccountCategories();
        echo json_encode($category);
    }

    public function actionGetvendors(){
        $journal_voucher = new JournalVoucher();
        $vendor = $journal_voucher->getVendors();

        // foreach($vendor as $row) {
        //     $abc[] = array('value' => $row['vendor_code'], 'label' => $row['vendor_name']);
        // }
        
        // echo json_encode($abc);

        echo json_encode($vendor);
    }

    public function actionGetvendordetails(){
        $journal_voucher = new JournalVoucher();
        $data = $journal_voucher->getVendorDetails();
        echo json_encode($data);
    }
}
