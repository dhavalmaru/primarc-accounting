<?php

namespace backend\controllers;

use Yii;
use backend\models\DebitNotes;
use backend\models\DebitNotesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class DebitNotesController extends Controller
{
	public function actionDebitNotes(){
		$connection = Yii::$app->db;
		$current_date = date("Y-m-d H:i:s");
		$promo_query = "SELECT vp.id as promotion_id,
									cm.promotion_code,
									sum(cm.vendor_promo as amount),
									vp.promo_start_date,
									vp.promo_end_date
								FROM vendor_promotions AS vp
								INNER JOIN contribution_margin_order_level cm ON
									vp.child_sku = cm.child_sku and vp.company_id = cm.company_id and vp.promotion_code = cm.promotion_code
								WHERE vp.promo_status in ('Discontinue','Closed') 
									AND vp.approve_status = 'Approved'
									AND vp.promo_end_date = DATE_ADD(CURDATE(), INTERVAL -1 DAY)";
		$promo_data = $connection->createCommand($promo_query)->queryAll();
		
		foreach($promo_data as $each_promo){
			$vendor_promo[] = ['id' => '','promotion_id'=> $each_promo['promotion_id'], 
							   'contribution_margin_id' => $each_promo['contribution_margin_id'], 'promotion_code' => $each_promo['promotion_code'], 
							   'amount' => $each_promo['amount'], 'promo_start_date' => $each_promo['promo_start_date'], 
							   'promo_end_date' => $each_promo['promo_end_date'], 'order_date' => $each_promo['order_date'], 
							   'created_date' => $created_date, 'updated_date' => $current_date];							
		}
		Yii::$app->db->createCommand()->batchInsert('debit_notes_calculations', ['id','promotion_id', 'contribution_margin_id', 'promotion_code', 
														 'amount', 'promo_start_date', 'promo_end_date', 'order_date', 'created_date', 
														 'updated_date'], $vendor_promo
							)->execute();
	}
}