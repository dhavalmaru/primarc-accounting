<?php

namespace app\controllers;

use Yii;
use app\models\GrnEntries;
use app\models\GrnEntriesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use mPDF;
use yii\web\UploadedFile;
use phpoffice\phpexcel\Classes\PHPExcel as PHPExcel;
use phpoffice\phpexcel\Classes\PHPExcel\PHPExcel_IOFactory as PHPExcel_IOFactory;
use phpoffice\phpexcel\Classes\PHPExcel\Cell\PHPExcel_Cell_DataValidation as PHPExcel_Cell_DataValidation;
use phpoffice\phpexcel\Classes\PHPExcel\Style\PHPExcel_Style_Protection as PHPExcel_Worksheet_Protection;

/**
 * GrnEntriesController implements the CRUD actions for GrnEntries model.
 */
class UploadscrapingController extends Controller
{
	public function actionIndex() {
		return $this->render('scraping_upload');
	}

	public function actionSaveupload()
	{
		
        $request = Yii::$app->request;
        $session = Yii::$app->session;
        $company_id = $session['company_id'];
        $curusr = $session['session_id'];
        $now = date('Y-m-d H:i:s');
        $mycomponent = Yii::$app->mycomponent;
        $status = '';

        echo 'eneterd';  

        echo $payment_file = $request->post('scraping_file');
        $upload_path = './uploads';
        if(!is_dir($upload_path)) {
            mkdir($upload_path, 0777, TRUE);
        }

        $upload_path = './uploads/scraping_file/';
        if(!is_dir($upload_path)) {
            mkdir($upload_path, 0777, TRUE);
        }

        $fetched_file='';
        $uploadedFile = UploadedFile::getInstanceByName('scraping_file');
        if(!empty($uploadedFile)){
            $src_filename = $_FILES['scraping_file'];
            $fetched_file=$filename = $src_filename['name'];
            $filePath = $upload_path.'/'.$filename;
            $uploadedFile->saveAs($filePath);
            $original_file = 'uploads/scraping_file/'.$filename;
        }

        $objPHPExcel = new \PHPExcel();
        $objPHPExcel = \PHPExcel_IOFactory::load($original_file);
        $objPHPExcel->setActiveSheetIndex(0);
       
        $array = array();
        $prev_type = '';
        
        $r_row = 2;
        $boolerror=0;
        $bank_name_tem = '';    
        $highestrow = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();


        $batch_array = [];
        
        if($highestrow>0)
        {  
        	for($k=2;$k<=10;$k++){
                  $error = '';
                  $temp_flag = 0;

                  $product_code = $objPHPExcel->getActiveSheet()->getCell('A'.$k)->getValue();
                  $unit_price = $objPHPExcel->getActiveSheet()->getCell('B'.$k)->getValue();
                  $vendor_code = $objPHPExcel->getActiveSheet()->getCell('C'.$k)->getValue();
                  $vendor_skucode = $objPHPExcel->getActiveSheet()->getCell('D'.$k)->getValue();
                  $vendor_inventory = $objPHPExcel->getActiveSheet()->getCell('E'.$k)->getValue();
                  $mrp = $objPHPExcel->getActiveSheet()->getCell('F'.$k)->getValue();
                  $lead_time = $objPHPExcel->getActiveSheet()->getCell('G'.$k)->getValue();
                  $priority = $objPHPExcel->getActiveSheet()->getCell('H'.$k)->getValue();
                  $enabled = $objPHPExcel->getActiveSheet()->getCell('I'.$k)->getValue();
                  $color = $objPHPExcel->getActiveSheet()->getCell('J'.$k)->getValue();
                  $brand = $objPHPExcel->getActiveSheet()->getCell('K'.$k)->getValue();
                  $size = $objPHPExcel->getActiveSheet()->getCell('L'.$k)->getValue();
                  $updated = $objPHPExcel->getActiveSheet()->getCell('M'.$k)->getValue();
                  $hsn_code = $objPHPExcel->getActiveSheet()->getCell('N'.$k)->getValue();
                  $vendor_style_code = $objPHPExcel->getActiveSheet()->getCell('O'.$k)->getValue();
                  $myntra_team_lead = $objPHPExcel->getActiveSheet()->getCell('P'.$k)->getValue();
                  $myntra_unit_price = $objPHPExcel->getActiveSheet()->getCell('Q'.$k)->getValue();

                  $sql = "select * from acc_scraping_upload Where company_id='$company_id' and product_code='$product_code' ";

        		  $command = Yii::$app->db->createCommand($sql);
        		  $reader = $command->query();
        		  $result = $reader->readAll();

        		  if(count($result)>0)
        		  {
	        		  	$array=[
	                  			'product_code'=>$product_code,
	                            'unit_price' => $unit_price, 
	                            'vendor_code'=>$vendor_code,
	                            'vendor_skucode'=>$vendor_skucode,
	                            'vendor_inventory'=>$vendor_inventory,
	                            'mrp'=>$mrp,
	                            'lead_time'=>$lead_time,
	                            'priority'=>$priority,
	                            'enabled'=>$enabled,
	                            'color'=>$color,
	                            'brand'=>$brand,
	                            'size'=>$size,
	                            'updated_on'=>$updated,
	                            'hsn_code'=>$hsn_code,
	                            'vendor_style_code'=>$vendor_style_code,
	                            'myntra_team_lead'=>$myntra_team_lead,
	                            'myntra_unit_price'=>$myntra_unit_price,
	                            'updated_by'=>$curusr,
	                            'updated_date'=>$now,
	                            'company_id'=>$company_id
	                            ];

	        		  	Yii::$app->db->createCommand()->update("acc_scraping_upload", $array, "product_code = '".$product_code."'");
        		  }
        		  else
        		  {
        		  		$array=[
	                  			'product_code'=>$product_code,
	                            'unit_price' => $unit_price, 
	                            'vendor_code'=>$vendor_code,
	                            'vendor_skucode'=>$vendor_skucode,
	                            'vendor_inventory'=>$vendor_inventory,
	                            'mrp'=>$mrp,
	                            'lead_time'=>$lead_time,
	                            'priority'=>$priority,
	                            'enabled'=>$enabled,
	                            'color'=>$color,
	                            'brand'=>$brand,
	                            'size'=>$size,
	                            'updated_on'=>$updated,
	                            'hsn_code'=>$hsn_code,
	                            'vendor_style_code'=>$vendor_style_code,
	                            'myntra_team_lead'=>$myntra_team_lead,
	                            'myntra_unit_price'=>$myntra_unit_price,
	                            'added_on'=>$now,
	                            'added_by'=>$curusr,
	                            'updated_by'=>$curusr,
	                            'updated_date'=>$now,
	                            'company_id'=>$company_id
	                            ];

	                  	Yii::$app->db->createCommand()->insert("acc_scraping_upload", $array)->execute();
        		  }
            }

			/*Yii::$app->db->createCommand()->Insert("acc_scraping_upload", $batch_array)->execute();*/
        }
	}
}

?>
