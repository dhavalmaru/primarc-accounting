<?php

namespace app\controllers;

use Yii;
use app\models\BankMaster;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;

/**
 * GrnController implements the CRUD actions for Grn model.
 */
class BankmasterController extends Controller
{
    public function actionIndex()
    {
        $bank_master = new BankMaster();
        $pending = $bank_master->getBankDetails("", "pending");
        $approved = $bank_master->getBankDetails("", "approved");
        return $this->render('bank_list', [
            'pending' => $pending, 'approved' => $approved,
        ]);
    }

    public function actionCreate()
    {
        return $this->render('bank_details');
    }

    public function actionSave()
    {   
        $bank_master = new BankMaster();
        $result = $bank_master->save();
        $this->redirect(array('bankmaster/index'));
    }

    public function actionEdit($id)
    {
        $bank_master = new BankMaster();
        $data = $bank_master->getBankDetails($id, "");
        
        return $this->render('bank_details', ['data' => $data]);
    }
}
