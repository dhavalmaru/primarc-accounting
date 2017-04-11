<?php

namespace app\controllers;

use Yii;
use app\models\Grn;
use app\models\GrnSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * GrnController implements the CRUD actions for Grn model.
 */
class PendinggrnController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Grn models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new GrnSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Grn model.
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
     * Creates a new Grn model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Grn();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->grn_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Grn model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        // $model = $this->findModel($id);
        $model = new Grn();
        $grn_details = $model->getGrnDetails($id);
        $total_val = $model->getTotalValue($id);
        $total_tax = $model->getTotalTax($id);
        $invoice_details = $model->getInvoiceDetails($id);
        $invoice_tax = $model->getInvoiceTax($id);

        if (count($grn_details)>0) {
            $grn_details['isNewRecord']=0;

            return $this->render('update', ['grn_details' => $grn_details, 'total_val' => $total_val, 'total_tax' => $total_tax, 
                                'invoice_details' => $invoice_details, 'invoice_tax' => $invoice_tax]);
        }

        // if ($model->load(Yii::$app->request->post()) && $model->save()) {
        //     return $this->redirect(['view', 'id' => $model->grn_id]);
        // } else {
        //     return $this->render('update', [
        //         'model' => $model,
        //     ]);
        // }
    }

    public function actionFormatter(){
       return $this->render('formatter');
    }

    /**
     * Deletes an existing Grn model.
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
     * Finds the Grn model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Grn the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $model = new Grn();
        $rows = $model->getGrnDetails($id);
        
        if (count($rows)>0) {
            return $rows;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    public function actionPendinggrn()
    {
        $model = new PendingGrn();
        $rows = $model->getPendingGrn();
        
        if (count($rows)>0) {
            // echo $rows[0]->grn_id;

            return $this->render('entry-confirm', ['model' => $rows]);
        } else {
            // either the page is initially displayed or there is some validation error
            return $this->render('entry', ['model' => $model]);
        }
    }
}
