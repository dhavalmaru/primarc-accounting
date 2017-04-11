<?php

namespace app\controllers;

use Yii;
use app\models\DebitCreditNote;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;

class DebitcreditnoteController extends Controller
{
    public function actionIndex()
    {
        $debit_credit_note = new DebitCreditNote();
        $pending = $debit_credit_note->getDetails("", "pending");
        $approved = $debit_credit_note->getDetails("", "approved");
        return $this->render('debitcreditnote_list', [
            'pending' => $pending, 'approved' => $approved,
        ]);
    }

    public function actionGetaccountdetails()
    {   
        $request = Yii::$app->request;
        $transaction = $request->post('transaction');
        
        $debit_credit_note = new DebitCreditNote();
        $result = $debit_credit_note->getAccountDetails($transaction);

        if(count($result)>0){
            echo $result[0]['code'];
        } else {
            echo "";
        }
    }

    public function actionCreate()
    {
        $transaction = "Create";
        return $this->render('debitcreditnote_details', ['transaction'=>$transaction]);
    }

    public function actionSave()
    {   
        $debit_credit_note = new DebitCreditNote();
        $transaction_id = $debit_credit_note->save();
        $this->redirect(array('debitcreditnote/ledger', 'transaction_id'=>$transaction_id));
    }

    public function actionLedger($transaction_id)
    {
        $model = new DebitCreditNote();
        $ledger = $model->getLedger($transaction_id);
        return $this->render('debitcreditnote_ledger', ['ledger' => $ledger]);
    }

    public function actionEdit($trans_id)
    {
        $debit_credit_note = new DebitCreditNote();
        $data = $debit_credit_note->getDetails($trans_id, "");
        $debit = null;
        $credit = null;
        $transaction = "Update";
        if(isset($data)){
            if(count($data)>0){
                if($data[0]['type']=='Debit'){
                    $debit = $data[0];
                    $credit = $data[1];
                } else {
                    $debit = $data[1];
                    $credit = $data[0];
                }
            }
        }
        
        return $this->render('debitcreditnote_details', ['transaction' => $transaction, 'debit' => $debit, 'credit' => $credit]);
    }
}