<?php

namespace console\controllers;
use yii\console\Controller;


/**
 * HotelController implements the CRUD actions for Hotel model.
 */
class VendorPromotionController extends Controller 
{
    //put your code here
    
    public function actionIndex()
    {
        echo "hello";
        
    }
    public function actionChangeStatus() {

           
            $connection=  \Yii::$app->db;
            $expiredate=date('Y-m-d');
             
            $activestatus=$connection->createCommand()->update('vendor_promotions', ['promo_status' => "On Going"],"approve_status='Approved'and promo_status='Not Started' and promo_start_date <='$expiredate'")->execute();
          
            if($activestatus)
            {
                echo "update active value";
                
            }
            
           
            $closestatus=$connection->createCommand()->update('vendor_promotions', ['promo_status' => "Closed"], "approve_status='Approved'and promo_status='On Going' and promo_end_date < '$expiredate'" )->execute();
          
            if($closestatus)
            {
                echo "update close value";
            }
            
            $closepreviousstatus=$connection->createCommand()->update('vendor_promotions', ['promo_status' => "Closed"], "approve_status='Approved' and promo_end_date < '$expiredate'" )->execute();
          
            if($closepreviousstatus)
            {
                echo "update close value";
            }
            
            
            
        
    }

}
