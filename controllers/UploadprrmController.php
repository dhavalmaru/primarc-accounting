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
use phpoffice\phpexcel\Classes\PHPExcel\Cell\PHPExcel_Cell as PHPExcel_Cell;

use phpoffice\phpexcel\Classes\PHPExcel\Style\PHPExcel_Style_Protection as PHPExcel_Worksheet_Protection;

/**
 * GrnEntriesController implements the CRUD actions for GrnEntries model.
 */
class UploadprrmController extends Controller
{
	public function actionIndex() {
		return $this->render('prrm_upload');
	}

	public function actionSaveupload() {
		
        $request = Yii::$app->request;
        $session = Yii::$app->session;
        $company_id = $session['company_id'];
        $curusr = $session['session_id'];
        $now = date('Y-m-d H:i:s');
        $mycomponent = Yii::$app->mycomponent;
        $status = '';

        $payment_file = $request->post('scraping_file');
        $file_type = $request->post('file_type');
        $upload_path = './uploads';
        if(!is_dir($upload_path)) {
            mkdir($upload_path, 0777, TRUE);
        }

        $upload_path = './uploads/prrm_file/';
        if(!is_dir($upload_path)) {
            mkdir($upload_path, 0777, TRUE);
        }

        $fetched_file='';
        $uploadedFile = UploadedFile::getInstanceByName('prrm_file');
        if(!empty($uploadedFile)){
            $src_filename = $_FILES['prrm_file'];
            $fetched_file=$filename = $src_filename['name'];
            $filePath = $upload_path.'/'.$filename;
            $uploadedFile->saveAs($filePath);
            $original_file = 'uploads/prrm_file/'.$filename;
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
        $highestColumm = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
        $colNumber = \PHPExcel_Cell::columnIndexFromString($highestColumm);
        $row= 2;
        $col = 0;
        $request = Yii::$app->request;
        $session = Yii::$app->session;
        $company_id = $session['company_id'];
        $curusr = $session['session_id'];
        $now = date('Y-m-d H:i:s');

        
        if($file_type=='DN'){
            $batch_array = [];
            $col_name[]=array();
            for($i=0; $i<=41; $i++) {
                $col_name[$i]=\PHPExcel_Cell::stringFromColumnIndex($i);
            }
            
            if($highestrow>0 && $colNumber==42){  
                for($k=2;$k<=$highestrow;$k++){
                      $error = '';
                      $temp_flag = 0;

                 $venor_code=$objPHPExcel->getActiveSheet()->getCell($col_name[$col].$row)->getValue();
                 $vendor_name=$objPHPExcel->getActiveSheet()->getCell($col_name[$col+1].$row)->getValue();
                 $type = $objPHPExcel->getActiveSheet()->getCell($col_name[$col+2].$row)->getValue();
                 $item_code = $objPHPExcel->getActiveSheet()->getCell($col_name[$col+3].$row)->getValue();
                 $order_date = $objPHPExcel->getActiveSheet()->getCell($col_name[$col+4].$row)->getValue();
                 $order_date = \PHPExcel_Style_NumberFormat::toFormattedString($order_date, 'YYYY-MM-DD');

                 $grn_number = $objPHPExcel->getActiveSheet()->getCell($col_name[$col+5].$row)->getValue();
                 $grn_date = $objPHPExcel->getActiveSheet()->getCell($col_name[$col+6].$row)->getValue();
                 $grn_date = \PHPExcel_Style_NumberFormat::toFormattedString($grn_date, 'YYYY-MM-DD H:i:s');

                 $grn_invoice_no = $objPHPExcel->getActiveSheet()->getCell($col_name[$col+7].$row)->getValue();
                 $grn_invoice_date = $objPHPExcel->getActiveSheet()->getCell($col_name[$col+8].$row)->getValue();
                 $grn_invoice_date = \PHPExcel_Style_NumberFormat::toFormattedString($grn_invoice_date, 'YYYY-MM-DD');

                 $order_date = \PHPExcel_Style_NumberFormat::toFormattedString($order_date, 'YYYY-MM-DD');
                 $po_code = $objPHPExcel->getActiveSheet()->getCell($col_name[$col+9].$row)->getValue();
                 $po_date = $objPHPExcel->getActiveSheet()->getCell($col_name[$col+10].$row)->getValue();
                 $po_date = \PHPExcel_Style_NumberFormat::toFormattedString($po_date, 'YYYY-MM-DD H:i:s');

                 $category = $objPHPExcel->getActiveSheet()->getCell($col_name[$col+11].$row)->getValue();
                 $item_type_name = $objPHPExcel->getActiveSheet()->getCell($col_name[$col+12].$row)->getValue();
                 $brand = $objPHPExcel->getActiveSheet()->getCell($col_name[$col+13].$row)->getValue();
                 $item_type_skucode = $objPHPExcel->getActiveSheet()->getCell($col_name[$col+14].$row)->getValue();
                 $vendor_skucode = $objPHPExcel->getActiveSheet()->getCell($col_name[$col+15].$row)->getValue();
                 $mrp= $objPHPExcel->getActiveSheet()->getCell($col_name[$col+16].$row)->getValue();
                 $taxable_amount= $objPHPExcel->getActiveSheet()->getCell($col_name[$col+17].$row)->getValue();
                 $tax_amount= $objPHPExcel->getActiveSheet()->getCell($col_name[$col+18].$row)->getValue();
                 $total_amount= $objPHPExcel->getActiveSheet()->getCell($col_name[$col+19].$row)->getValue();
                 $tax= $objPHPExcel->getActiveSheet()->getCell($col_name[$col+20].$row)->getValue();
                 $tax = str_replace("%", "", $tax);
                 $hsn_code= $objPHPExcel->getActiveSheet()->getCell($col_name[$col+21].$row)->getValue();
                 $facility= $objPHPExcel->getActiveSheet()->getCell($col_name[$col+22].$row)->getValue();
                 $sale_tax= $objPHPExcel->getActiveSheet()->getCell($col_name[$col+23].$row)->getValue();
                 $sale_tax = str_replace("%", "", $sale_tax);
                 $sales_mrp= $objPHPExcel->getActiveSheet()->getCell($col_name[$col+24].$row)->getValue();
                 $total_discount= $objPHPExcel->getActiveSheet()->getCell($col_name[$col+25].$row)->getValue();
                 $vendor_discount= $objPHPExcel->getActiveSheet()->getCell($col_name[$col+26].$row)->getValue();
                 $floor_price= $objPHPExcel->getActiveSheet()->getCell($col_name[$col+27].$row)->getValue();
                 $floor_price_diff= $objPHPExcel->getActiveSheet()->getCell($col_name[$col+28].$row)->getValue();
                 $margin= $objPHPExcel->getActiveSheet()->getCell($col_name[$col+29].$row)->getValue();
                 $margin = str_replace("%", "", $margin);
                 $vendor_margin_amount= $objPHPExcel->getActiveSheet()->getCell($col_name[$col+30].$row)->getValue();
                 $revised_mrp= $objPHPExcel->getActiveSheet()->getCell($col_name[$col+31].$row)->getValue();
                 $taxable= $objPHPExcel->getActiveSheet()->getCell($col_name[$col+32].$row)->getValue();
                 $tax_amount2= $objPHPExcel->getActiveSheet()->getCell($col_name[$col+33].$row)->getValue();
                 $total_amount2= $objPHPExcel->getActiveSheet()->getCell($col_name[$col+34].$row)->getValue();
                 $commercial_dn_amount= $objPHPExcel->getActiveSheet()->getCell($col_name[$col+35].$row)->getValue();
                 $gst_dn_no= $objPHPExcel->getActiveSheet()->getCell($col_name[$col+36].$row)->getValue();
                 $gst_dn_date= $objPHPExcel->getActiveSheet()->getCell($col_name[$col+37].$row)->getValue();
                 $gst_dn_date = \PHPExcel_Style_NumberFormat::toFormattedString($gst_dn_date, 'YYYY-MM-DD');

                 $gst_dn_amount= $objPHPExcel->getActiveSheet()->getCell($col_name[$col+38].$row)->getValue();
                 $gst_tax_amount= $objPHPExcel->getActiveSheet()->getCell($col_name[$col+39].$row)->getValue();
                 $gst_taxt_total_amount= $objPHPExcel->getActiveSheet()->getCell($col_name[$col+40].$row)->getValue();
                 $dn_type= $objPHPExcel->getActiveSheet()->getCell($col_name[$col+41].$row)->getValue();


                      $sql = "select * from acc_prrm Where company_id='$company_id' and item_code='$item_code' and date(added_on)=date(now())";

                      $command = Yii::$app->db->createCommand($sql);
                      $reader = $command->query();
                      $result = $reader->readAll();

                      if($item_code!="")
                      {
                          if(count($result)>0)
                          {
                                $insert_data = [
                                 'venor_code'=>$venor_code,
                                 'vendor_name'=>$vendor_name,
                                 'type' =>$type,
                                 'item_code'=>$item_code,
                                 'order_date'=>$order_date,
                                 'grn_number'=>$grn_number,
                                 'grn_date'=>$grn_date,
                                 'grn_invoice_no' =>$grn_invoice_no,
                                 'grn_invoice_date'=>$grn_invoice_date,
                                 'po_code'=>$po_code,
                                 'po_date'=>$po_date,
                                 'category'=>$category,
                                 'item_type_name'=>$item_type_name,
                                 'brand'=>$brand,
                                 'item_type_skucode'=>$item_type_skucode,
                                 'vendor_skucode'=>$vendor_skucode,
                                 'mrp'=>$mrp,
                                 'taxable_amount'=>$taxable_amount,
                                 'tax_amount'=>$tax_amount,
                                 'total_amount'=>$total_amount,
                                 'tax'=>$tax,
                                 'hsn_code'=>$hsn_code,
                                 'facility'=>$facility,
                                 'sale_tax'=>$sale_tax,
                                 'sales_mrp'=>$sales_mrp,
                                 'total_discount'=>$total_discount,
                                 'vendor_discount'=>$vendor_discount,
                                 'floor_price'=>$floor_price,
                                 'floor_price_diff'=>$floor_price_diff,
                                 'margin'=>$margin,
                                 'vendor_margin_amount'=>$vendor_margin_amount,
                                 'revised_mrp'=>$revised_mrp,
                                 'taxable'=>$taxable,
                                 'tax_amount2'=>$tax_amount2,
                                 'total_amount2'=>$total_amount2,
                                 'commercial_dn_amount'=>$commercial_dn_amount,
                                 'gst_dn_no'=>$gst_dn_no,
                                 'gst_dn_date'=>$gst_dn_date,
                                 'gst_dn_amount'=>$gst_dn_amount,
                                 'gst_tax_amount'=>$gst_tax_amount,
                                 'gst_taxt_total_amount'=>$gst_taxt_total_amount,
                                 'dn_type'=>$dn_type,
                                 'modified_by'=>$curusr,
                                 'modified_on'=>$now,
                                 'company_id'=>$company_id];

                                Yii::$app->db->createCommand()->update("acc_prrm", $insert_data, "item_code = '".$item_code."'")->execute();
                          }
                          else
                          {
                                $insert_data = [
                                 'venor_code'=>$venor_code,
                                 'vendor_name'=>$vendor_name,
                                 'type' =>$type,
                                 'item_code'=>$item_code,
                                 'order_date'=>$order_date,
                                 'grn_number'=>$grn_number,
                                 'grn_date'=>$grn_date,
                                 'grn_invoice_no' =>$grn_invoice_no,
                                 'grn_invoice_date'=>$grn_invoice_date,
                                 'po_code'=>$po_code,
                                 'po_date'=>$po_date,
                                 'category'=>$category,
                                 'item_type_name'=>$item_type_name,
                                 'brand'=>$brand,
                                 'item_type_skucode'=>$item_type_skucode,
                                 'vendor_skucode'=>$vendor_skucode,
                                 'mrp'=>$mrp,
                                 'taxable_amount'=>$taxable_amount,
                                 'tax_amount'=>$tax_amount,
                                 'total_amount'=>$total_amount,
                                 'tax'=>$tax,
                                 'hsn_code'=>$hsn_code,
                                 'facility'=>$facility,
                                 'sale_tax'=>$sale_tax,
                                 'sales_mrp'=>$sales_mrp,
                                 'total_discount'=>$total_discount,
                                 'vendor_discount'=>$vendor_discount,
                                 'floor_price'=>$floor_price,
                                 'floor_price_diff'=>$floor_price_diff,
                                 'margin'=>$margin,
                                 'vendor_margin_amount'=>$vendor_margin_amount,
                                 'revised_mrp'=>$revised_mrp,
                                 'taxable'=>$taxable,
                                 'tax_amount2'=>$tax_amount2,
                                 'total_amount2'=>$total_amount2,
                                 'commercial_dn_amount'=>$commercial_dn_amount,
                                 'gst_dn_no'=>$gst_dn_no,
                                 'gst_dn_date'=>$gst_dn_date,
                                 'gst_dn_amount'=>$gst_dn_amount,
                                 'gst_tax_amount'=>$gst_tax_amount,
                                 'gst_taxt_total_amount'=>$gst_taxt_total_amount,
                                 'dn_type'=>$dn_type,
                                 'added_on'=>$now,
                                 'added_by'=>$curusr,
                                 'modified_by'=>$curusr,
                                 'modified_on'=>$now,
                                 'company_id'=>$company_id];

                                Yii::$app->db->createCommand()->insert("acc_prrm", $insert_data)->execute();
                          }
                      }


                      $row = $row+1;
                }

                   /*Yii::$app->db->createCommand()->Insert("acc_scraping_upload", $batch_array)->execute();*/

             echo 'success';
            }
            else{
                echo "Wrong file type selected , Please select another";
            }
        }else{
            $col_name[]=array();
            for($i=0; $i<=15; $i++) {
                $col_name[$i]=\PHPExcel_Cell::stringFromColumnIndex($i);
            }

            if($highestrow>0 && $colNumber==15){  
                for($k=2;$k<=$highestrow;$k++){
                     $error = '';
                     $temp_flag = 0;

                     $vendor_code=$objPHPExcel->getActiveSheet()->getCell($col_name[$col].$row)->getValue();
                     $vendor_name=$objPHPExcel->getActiveSheet()->getCell($col_name[$col+1].$row)->getValue();
                     $type = $objPHPExcel->getActiveSheet()->getCell($col_name[$col+2].$row)->getValue();
                     $facility = $objPHPExcel->getActiveSheet()->getCell($col_name[$col+3].$row)->getValue();
                     $grn_code = $objPHPExcel->getActiveSheet()->getCell($col_name[$col+4].$row)->getValue();
                     $grn_date = $objPHPExcel->getActiveSheet()->getCell($col_name[$col+5].$row)->getValue();
                     $grn_date = \PHPExcel_Style_NumberFormat::toFormattedString($grn_date, 'YYYY-MM-DD H:i:s');
                     $po_code = $objPHPExcel->getActiveSheet()->getCell($col_name[$col+6].$row)->getValue();
                     $po_date = $objPHPExcel->getActiveSheet()->getCell($col_name[$col+7].$row)->getValue();
                     $po_date = \PHPExcel_Style_NumberFormat::toFormattedString($po_date, 'YYYY-MM-DD H:i:s');
                     $grn_invoice_no = $objPHPExcel->getActiveSheet()->getCell($col_name[$col+8].$row)->getValue();
                     $grn_invoice_date = $objPHPExcel->getActiveSheet()->getCell($col_name[$col+9].$row)->getValue();
                     $grn_invoice_date = \PHPExcel_Style_NumberFormat::toFormattedString($grn_invoice_date, 'YYYY-MM-DD');
                     $quantity = $objPHPExcel->getActiveSheet()->getCell($col_name[$col+10].$row)->getValue();
                     $taxable_amount = $objPHPExcel->getActiveSheet()->getCell($col_name[$col+11].$row)->getValue();
                     $tax = $objPHPExcel->getActiveSheet()->getCell($col_name[$col+12].$row)->getValue();
                     $total_amount = $objPHPExcel->getActiveSheet()->getCell($col_name[$col+13].$row)->getValue();
                     $tax_rate = $objPHPExcel->getActiveSheet()->getCell($col_name[$col+14].$row)->getValue();



                      if($vendor_code!="")
                      {
                          $insert_data = [
                                 'vendor_code'=>$vendor_code,
                                 'vendor_name'=>$vendor_name,
                                 'type' =>$type,
                                 'facility'=>$facility,
                                 'grn_code'=>$grn_code,
                                 'grn_date'=>$grn_date,
                                 'po_code'=>$po_code,
                                 'po_date'=>$po_date,
                                 'grn_invoice_no'=>$grn_invoice_no,
                                 'grn_invoice_date'=>$grn_invoice_date,
                                 'quantity'=>$quantity,
                                 'taxable_amount'=>$taxable_amount,
                                 'tax'=>$tax,
                                 'total'=>$total_amount,
                                 'tax_rate'=>$tax_rate,
                                 'added_on'=>$now,
                                 'added_by'=>$curusr,
                                 'company_id'=>$company_id];

                          Yii::$app->db->createCommand()->insert("acc_grn_prrm", $insert_data)->execute();
                      }

                      $row = $row+1;
                }

                /*Yii::$app->db->createCommand()->Insert("acc_scraping_upload", $batch_array)->execute();*/

                echo 'success';
            }else {
                echo "Wrong file type selected , Please select another";
            }

        }

        
	}

}

?>
