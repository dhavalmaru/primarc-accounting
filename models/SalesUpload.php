<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use phpoffice\phpexcel\Classes\PHPExcel as PHPExcel;
use phpoffice\phpexcel\Classes\PHPExcel\PHPExcel_IOFactory as PHPExcel_IOFactory;
use phpoffice\phpexcel\Classes\PHPExcel\Cell\PHPExcel_Cell_DataValidation as PHPExcel_Cell_DataValidation;
use phpoffice\phpexcel\Classes\PHPExcel\Style\PHPExcel_Style_Protection as PHPExcel_Worksheet_Protection;

class SalesUpload extends Model
{
    public function getAccess(){
        $session = Yii::$app->session;
        $session_id = $session['session_id'];
        $role_id = $session['role_id'];

        $sql = "select A.*, '".$session_id."' as session_id from acc_user_role_options A 
                where A.role_id = '$role_id' and A.r_section = 'S_Account_Master'";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getDetails($id="", $status=""){
        $cond = "";
        $cond2 = "";
        if($id!=""){
            $cond = " and A.id = '$id'";
            $cond2 = " and acc_id = '$id'";
        }

        if($status!=""){
            $cond = $cond . " and A.status = '$status'";
        }
        
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select A.*, B.ref_id, C.is_paid from 
                (select A.*, B.username as creator, C.username as approver 
                from acc_sales_files A left join user B on (A.created_by = B.id) 
                left join user C on (A.approved_by = C.id) 
                where A.is_active = '1'" . $cond . " and A.company_id = '$company_id') A 
                left join 
                (select distinct ref_id from acc_ledger_entries where ref_type = 'sales_upload' and is_active = '1' and status = 'Approved') B 
                on (A.id = B.ref_id) 
                left join 
                (select distinct ref_id, is_paid from acc_ledger_entries 
                    where ref_type = 'sales_upload' and is_active = '1' and status = 'Approved' and is_paid = '1') C 
                on (A.id = C.ref_id) 
                order by UNIX_TIMESTAMP(A.updated_date) desc, A.id desc";

        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function insert_pincode($state_name='', $pincode='') {
        $now = date('Y-m-d H:i:s');

        $array = array('state_name' => $state_name, 
                        'pincode' => $pincode, 
                        'created_date' => $now, 
                        'updated_date' => $now
                    );

        if(count($array)>0){
            Yii::$app->db->createCommand()->insert("pincode_master", $array)->execute();
        }
    }

    public function check_gst_no_format($gst_no, $state) {
        $remarks = "";
        $bl_flag = false;
        $pattern = '/^([0-9]){2}([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}([a-zA-Z0-9]){3}?$/';

        if(preg_match($pattern, $gst_no)!=true) {
            $bl_flag = true;
        }

        if($bl_flag==false) {
            $sql = "select * from state_master where is_active = '1' and state_name = '$state'";
            $command = Yii::$app->db->createCommand($sql);
            $reader = $command->query();
            $data2 = $reader->readAll();
            if(count($data2)>0) {
                $state_code = $data2[0]['tin_number'];

                if(strlen($gst_no)>1){
                    if(substr($gst_no, 0, 2)!=$state_code) {
                        $bl_flag = true;
                    }
                } else {
                    $bl_flag = true;
                }
            }
        }

        if($bl_flag==true) {
            $remarks = $remarks . "Ship to GSTin is not in proper format. ";
        }

        return $remarks;
    }

    public function check_no($num) {
        $pattern = '/^-?(0*[0-9][0-9.,]*)$/';
        return preg_match($pattern, $num);
    }

    public function test() {
        $id = '1';
        $filename='sales_rejected_file_'.$id.'.xls';
        $upload_path = './uploads';
        if(!is_dir($upload_path)) {
            mkdir($upload_path, 0777, TRUE);
        }
        $upload_path = './uploads/sales';
        if(!is_dir($upload_path)) {
            mkdir($upload_path, 0777, TRUE);
        }
        $upload_path = './uploads/sales/'.$id;
        if(!is_dir($upload_path)) {
            mkdir($upload_path, 0777, TRUE);
        }

        $file_name = $upload_path . '/' . $filename;
        $file_path = 'uploads/sales/' . $id . '/' . $filename;

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Hello World !');

        $writer = new Xlsx($spreadsheet);
        $writer->save($file_name);
    }

    public function upload(){
        $request = Yii::$app->request;
        $session = Yii::$app->session;

        $curusr = $session['session_id'];
        $now = date('Y-m-d H:i:s');
        $company_id = $request->post('company_id');
        $date_of_upload = date('Y-m-d');

        $array = array('date_of_upload' => $date_of_upload,
                        'freeze_file' => '0',
                        'company_id' => $company_id,
                        'status' => 'approved',
                        'is_active' => '1',
                        'upload_status' => 'pending'
                        );

        if(count($array)>0){
            $tableName = "acc_sales_files";

            $array['created_by'] = $curusr;
            $array['created_date'] = $now;
            $count = Yii::$app->db->createCommand()
                        ->insert($tableName, $array)
                        ->execute();
            $id = Yii::$app->db->getLastInsertID();

            $this->setLog('SalesUpload', '', 'Upload', '', 'Upload Sales Details', 'acc_sales_files', $id);

            $sales_file = $request->post('sales_file');
            $upload_path = './uploads';
            if(!is_dir($upload_path)) {
                mkdir($upload_path, 0777, TRUE);
            }
            $upload_path = './uploads/sales';
            if(!is_dir($upload_path)) {
                mkdir($upload_path, 0777, TRUE);
            }
            $upload_path = './uploads/sales/'.$id;
            if(!is_dir($upload_path)) {
                mkdir($upload_path, 0777, TRUE);
            }

            $uploadedFile = UploadedFile::getInstanceByName('sales_file');
            if(!empty($uploadedFile)){
                $src_filename = $_FILES['sales_file'];
                $filename = $src_filename['name'];
                $filePath = $upload_path.'/'.$filename;
                $uploadedFile->saveAs($filePath);
                $original_file = 'uploads/sales/'.$id.'/'.$filename;
            }
            
            $array = array('file_name' => $filename, 
                            'original_file' => $original_file);

            $tableName = "acc_sales_files";
            $count = Yii::$app->db->createCommand()
                                ->update($tableName, $array, "id = '".$id."'")
                                ->execute();

            $this->upload_sales();
        }

        return true;
    }

    public function upload_sales() {
        $mycomponent = Yii::$app->mycomponent;
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $curusr = $session['session_id'];
        $now = date('Y-m-d H:i:s');

        $sql = "select * from acc_sales_files where is_active = '1' and (upload_status = 'pending' or upload_status is null)";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        $data = $reader->readAll();

        // echo json_encode($data);
        // echo '<br/>';

        for($i=0; $i<count($data); $i++) {
            $ref_file_id = $data[$i]['id'];
            $fileName = $data[$i]['original_file'];
            $objPHPExcel = new \PHPExcel();
            $objPHPExcel = \PHPExcel_IOFactory::load($fileName);
            $highestrow = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
            $reject_file = false;
            $highlight_file = false;
            $bl_reject = false;
            $bl_highlight = false;

            // $r_row = 1;
            // /*$reject_spreadsheet = $objPHPExcel->createSheet(1);*/
            // $objPHPExcel1 = new \PHPExcel();
            // /*$objPHPExcel1->createSheet();*/
            // $reject_sheet = $objPHPExcel1->setActiveSheetIndex(0);
            // $reject_sheet->setCellValue('A'.$r_row, 'Market place');
            // $reject_sheet->setCellValue('B'.$r_row, 'Ship from GSTin');
            // $reject_sheet->setCellValue('C'.$r_row, 'Ship from State');
            // $reject_sheet->setCellValue('D'.$r_row, 'Ship to GSTin');
            // $reject_sheet->setCellValue('E'.$r_row, 'Amazon state');
            // $reject_sheet->setCellValue('F'.$r_row, 'Pin code');
            // $reject_sheet->setCellValue('G'.$r_row, 'Invoice no');
            // $reject_sheet->setCellValue('H'.$r_row, 'Invoice date');
            // $reject_sheet->setCellValue('I'.$r_row, 'Customer name');
            // $reject_sheet->setCellValue('J'.$r_row, 'SKU');
            // $reject_sheet->setCellValue('K'.$r_row, 'Item description');
            // $reject_sheet->setCellValue('L'.$r_row, 'HSN Code');
            // $reject_sheet->setCellValue('M'.$r_row, 'Quantity');
            // $reject_sheet->setCellValue('N'.$r_row, 'Rate');
            // $reject_sheet->setCellValue('O'.$r_row, 'Sales incl GST');
            // $reject_sheet->setCellValue('P'.$r_row, 'Sales excl GST');
            // $reject_sheet->setCellValue('Q'.$r_row, 'Total GST');
            // $reject_sheet->setCellValue('R'.$r_row, 'IGST Rate');
            // $reject_sheet->setCellValue('S'.$r_row, 'IGST Amount');
            // $reject_sheet->setCellValue('T'.$r_row, 'CGST Rate');
            // $reject_sheet->setCellValue('U'.$r_row, 'CGST Amount');
            // $reject_sheet->setCellValue('V'.$r_row, 'SGST Rate');
            // $reject_sheet->setCellValue('W'.$r_row, 'SGST Amount');
            // $reject_sheet->setCellValue('X'.$r_row, 'Flag');
            // $reject_sheet->setCellValue('Y'.$r_row, 'Remarks');

            // $h_row = 1;
            // /*$highlight_spreadsheet = $objPHPExcel->createSheet(2);*/
            // $objPHPExcel2 = new \PHPExcel();
            // /*$objPHPExcel2->createSheet();*/
            // $highlight_sheet = $objPHPExcel2->setActiveSheetIndex(0);
            // $highlight_sheet->setCellValue('A'.$h_row, 'Market place');
            // $highlight_sheet->setCellValue('B'.$h_row, 'Ship from GSTin');
            // $highlight_sheet->setCellValue('C'.$h_row, 'Ship from State');
            // $highlight_sheet->setCellValue('D'.$h_row, 'Ship to GSTin');
            // $highlight_sheet->setCellValue('E'.$h_row, 'Amazon state');
            // $highlight_sheet->setCellValue('F'.$h_row, 'Pin code');
            // $highlight_sheet->setCellValue('G'.$h_row, 'Invoice no');
            // $highlight_sheet->setCellValue('H'.$h_row, 'Invoice date');
            // $highlight_sheet->setCellValue('I'.$h_row, 'Customer name');
            // $highlight_sheet->setCellValue('J'.$h_row, 'SKU');
            // $highlight_sheet->setCellValue('K'.$h_row, 'Item description');
            // $highlight_sheet->setCellValue('L'.$h_row, 'HSN Code');
            // $highlight_sheet->setCellValue('M'.$h_row, 'Quantity');
            // $highlight_sheet->setCellValue('N'.$h_row, 'Rate');
            // $highlight_sheet->setCellValue('O'.$h_row, 'Sales incl GST');
            // $highlight_sheet->setCellValue('P'.$h_row, 'Sales excl GST');
            // $highlight_sheet->setCellValue('Q'.$h_row, 'Total GST');
            // $highlight_sheet->setCellValue('R'.$h_row, 'IGST Rate');
            // $highlight_sheet->setCellValue('S'.$h_row, 'IGST Amount');
            // $highlight_sheet->setCellValue('T'.$h_row, 'CGST Rate');
            // $highlight_sheet->setCellValue('U'.$h_row, 'CGST Amount');
            // $highlight_sheet->setCellValue('V'.$h_row, 'SGST Rate');
            // $highlight_sheet->setCellValue('W'.$h_row, 'SGST Amount');
            // $highlight_sheet->setCellValue('X'.$h_row, 'Flag');
            // $highlight_sheet->setCellValue('Y'.$h_row, 'Remarks');

            $k = 0;

            // echo $highestrow;
            // echo '<br/>';

            $sql = "delete from acc_temp_sales_file_items where is_active = '1' and company_id = '$company_id' 
                    and ref_file_id = '".$ref_file_id."'";
            Yii::$app->db->createCommand($sql)->query();

            for($j=2; $j<=$highestrow; $j++) {
                $market_place_name = $objPHPExcel->getActiveSheet()->getCell('A'.$j)->getValue();

                if($market_place_name!=""){
                    // $array['created_by'] = $curusr;
                    // $array['created_date'] = $now;
                    // $count = Yii::$app->db->createCommand()
                    //             ->insert("acc_temp_sales_file_items", $array)
                    //             ->execute();

                    // $bl_reject = false;
                    // $bl_highlight = false;
                    // $bl_pincode = false;
                    // $ship_to_state[$k] = '';
                    // $remarks[$k] = '';
                    // $highlight_remarks[$k] = '';

                    // $marketplace_id[$k] = '';
                    // $market_place[$k] = $objPHPExcel->getActiveSheet()->getCell('A'.$j)->getValue();
                    // $ship_from_gstin[$k] = $objPHPExcel->getActiveSheet()->getCell('B'.$j)->getValue();
                    // $ship_from_state[$k] = $objPHPExcel->getActiveSheet()->getCell('C'.$j)->getValue();
                    // $ship_to_gstin[$k] = $objPHPExcel->getActiveSheet()->getCell('D'.$j)->getValue();
                    // $amazon_state[$k] = $objPHPExcel->getActiveSheet()->getCell('E'.$j)->getValue();
                    // $pin_code[$k] = $objPHPExcel->getActiveSheet()->getCell('F'.$j)->getValue();
                    // $invoice_no[$k] = $objPHPExcel->getActiveSheet()->getCell('G'.$j)->getValue();
                    // $invoice_date[$k] = $objPHPExcel->getActiveSheet()->getCell('H'.$j)->getValue();
                    // $customer_name[$k] =$objPHPExcel->getActiveSheet()->getCell('I'.$j)->getValue();
                    // $sku[$k] = $objPHPExcel->getActiveSheet()->getCell('J'.$j)->getValue();
                    // $item_desc[$k] = $objPHPExcel->getActiveSheet()->getCell('K'.$j)->getValue();
                    // $hsn_code[$k] = $objPHPExcel->getActiveSheet()->getCell('L'.$j)->getValue();
                    // $quantity[$k] = $objPHPExcel->getActiveSheet()->getCell('M'.$j)->getValue();
                    // $rate[$k] = $objPHPExcel->getActiveSheet()->getCell('N'.$j)->getValue();
                    // $sales_incl_gst[$k] = $objPHPExcel->getActiveSheet()->getCell('O'.$j)->getValue();
                    // $sales_excl_gst[$k] = $objPHPExcel->getActiveSheet()->getCell('P'.$j)->getValue();
                    // $total_gst[$k] = $objPHPExcel->getActiveSheet()->getCell('Q'.$j)->getValue();
                    // $igst_rate[$k] = $objPHPExcel->getActiveSheet()->getCell('R'.$j)->getValue();
                    // $igst_amount[$k] = $objPHPExcel->getActiveSheet()->getCell('S'.$j)->getValue();
                    // $cgst_rate[$k] = $objPHPExcel->getActiveSheet()->getCell('T'.$j)->getValue();
                    // $cgst_amount[$k] = $objPHPExcel->getActiveSheet()->getCell('U'.$j)->getValue();
                    // $sgst_rate[$k] = $objPHPExcel->getActiveSheet()->getCell('V'.$j)->getValue();
                    // $sgst_amount[$k] = $objPHPExcel->getActiveSheet()->getCell('W'.$j)->getValue();
                    // $flag[$k] = $objPHPExcel->getActiveSheet()->getCell('X'.$j)->getValue();

                    // $r_row += 1;
                    // $reject_sheet->setCellValue('A'.$r_row, $objPHPExcel->getActiveSheet()->getCell('A'.$j)->getValue());
                    // $reject_sheet->setCellValue('B'.$r_row, $objPHPExcel->getActiveSheet()->getCell('B'.$j)->getValue());
                    // $reject_sheet->setCellValue('C'.$r_row, $objPHPExcel->getActiveSheet()->getCell('C'.$j)->getValue());
                    // $reject_sheet->setCellValue('D'.$r_row, $objPHPExcel->getActiveSheet()->getCell('D'.$j)->getValue());
                    // $reject_sheet->setCellValue('E'.$r_row, $objPHPExcel->getActiveSheet()->getCell('E'.$j)->getValue());
                    // $reject_sheet->setCellValue('F'.$r_row, $objPHPExcel->getActiveSheet()->getCell('F'.$j)->getValue());
                    // $reject_sheet->setCellValue('G'.$r_row, $objPHPExcel->getActiveSheet()->getCell('G'.$j)->getValue());
                    // $reject_sheet->setCellValue('H'.$r_row, $objPHPExcel->getActiveSheet()->getCell('H'.$j)->getValue());
                    // $reject_sheet->setCellValue('I'.$r_row, $objPHPExcel->getActiveSheet()->getCell('I'.$j)->getValue());
                    // $reject_sheet->setCellValue('J'.$r_row, $objPHPExcel->getActiveSheet()->getCell('J'.$j)->getValue());
                    // $reject_sheet->setCellValue('K'.$r_row, $objPHPExcel->getActiveSheet()->getCell('K'.$j)->getValue());
                    // $reject_sheet->setCellValue('L'.$r_row, $objPHPExcel->getActiveSheet()->getCell('L'.$j)->getValue());
                    // $reject_sheet->setCellValue('M'.$r_row, $objPHPExcel->getActiveSheet()->getCell('M'.$j)->getValue());
                    // $reject_sheet->setCellValue('N'.$r_row, $objPHPExcel->getActiveSheet()->getCell('N'.$j)->getValue());
                    // $reject_sheet->setCellValue('O'.$r_row, $objPHPExcel->getActiveSheet()->getCell('O'.$j)->getValue());
                    // $reject_sheet->setCellValue('P'.$r_row, $objPHPExcel->getActiveSheet()->getCell('P'.$j)->getValue());
                    // $reject_sheet->setCellValue('Q'.$r_row, $objPHPExcel->getActiveSheet()->getCell('Q'.$j)->getValue());
                    // $reject_sheet->setCellValue('R'.$r_row, $objPHPExcel->getActiveSheet()->getCell('R'.$j)->getValue());
                    // $reject_sheet->setCellValue('S'.$r_row, $objPHPExcel->getActiveSheet()->getCell('S'.$j)->getValue());
                    // $reject_sheet->setCellValue('T'.$r_row, $objPHPExcel->getActiveSheet()->getCell('T'.$j)->getValue());
                    // $reject_sheet->setCellValue('U'.$r_row, $objPHPExcel->getActiveSheet()->getCell('U'.$j)->getValue());
                    // $reject_sheet->setCellValue('V'.$r_row, $objPHPExcel->getActiveSheet()->getCell('V'.$j)->getValue());
                    // $reject_sheet->setCellValue('W'.$r_row, $objPHPExcel->getActiveSheet()->getCell('W'.$j)->getValue());
                    // $reject_sheet->setCellValue('X'.$r_row, $objPHPExcel->getActiveSheet()->getCell('X'.$j)->getValue());
                    
                    // $h_row += 1;
                    // $highlight_sheet->setCellValue('A'.$h_row, $objPHPExcel->getActiveSheet()->getCell('A'.$j)->getValue());
                    // $highlight_sheet->setCellValue('B'.$h_row, $objPHPExcel->getActiveSheet()->getCell('B'.$j)->getValue());
                    // $highlight_sheet->setCellValue('C'.$h_row, $objPHPExcel->getActiveSheet()->getCell('C'.$j)->getValue());
                    // $highlight_sheet->setCellValue('D'.$h_row, $objPHPExcel->getActiveSheet()->getCell('D'.$j)->getValue());
                    // $highlight_sheet->setCellValue('E'.$h_row, $objPHPExcel->getActiveSheet()->getCell('E'.$j)->getValue());
                    // $highlight_sheet->setCellValue('F'.$h_row, $objPHPExcel->getActiveSheet()->getCell('F'.$j)->getValue());
                    // $highlight_sheet->setCellValue('G'.$h_row, $objPHPExcel->getActiveSheet()->getCell('G'.$j)->getValue());
                    // $highlight_sheet->setCellValue('H'.$h_row, $objPHPExcel->getActiveSheet()->getCell('H'.$j)->getValue());
                    // $highlight_sheet->setCellValue('I'.$h_row, $objPHPExcel->getActiveSheet()->getCell('I'.$j)->getValue());
                    // $highlight_sheet->setCellValue('J'.$h_row, $objPHPExcel->getActiveSheet()->getCell('J'.$j)->getValue());
                    // $highlight_sheet->setCellValue('K'.$h_row, $objPHPExcel->getActiveSheet()->getCell('K'.$j)->getValue());
                    // $highlight_sheet->setCellValue('L'.$h_row, $objPHPExcel->getActiveSheet()->getCell('L'.$j)->getValue());
                    // $highlight_sheet->setCellValue('M'.$h_row, $objPHPExcel->getActiveSheet()->getCell('M'.$j)->getValue());
                    // $highlight_sheet->setCellValue('N'.$h_row, $objPHPExcel->getActiveSheet()->getCell('N'.$j)->getValue());
                    // $highlight_sheet->setCellValue('O'.$h_row, $objPHPExcel->getActiveSheet()->getCell('O'.$j)->getValue());
                    // $highlight_sheet->setCellValue('P'.$h_row, $objPHPExcel->getActiveSheet()->getCell('P'.$j)->getValue());
                    // $highlight_sheet->setCellValue('Q'.$h_row, $objPHPExcel->getActiveSheet()->getCell('Q'.$j)->getValue());
                    // $highlight_sheet->setCellValue('R'.$h_row, $objPHPExcel->getActiveSheet()->getCell('R'.$j)->getValue());
                    // $highlight_sheet->setCellValue('S'.$h_row, $objPHPExcel->getActiveSheet()->getCell('S'.$j)->getValue());
                    // $highlight_sheet->setCellValue('T'.$h_row, $objPHPExcel->getActiveSheet()->getCell('T'.$j)->getValue());
                    // $highlight_sheet->setCellValue('U'.$h_row, $objPHPExcel->getActiveSheet()->getCell('U'.$j)->getValue());
                    // $highlight_sheet->setCellValue('V'.$h_row, $objPHPExcel->getActiveSheet()->getCell('V'.$j)->getValue());
                    // $highlight_sheet->setCellValue('W'.$h_row, $objPHPExcel->getActiveSheet()->getCell('W'.$j)->getValue());
                    // $highlight_sheet->setCellValue('X'.$h_row, $objPHPExcel->getActiveSheet()->getCell('X'.$j)->getValue());
                    
                    // $sql = "select * from internal_warehouse_master where is_active = '1' and company_id = '$company_id' 
                    //         and gst_id = '".$ship_from_gstin[$k]."'";
                    // $command = Yii::$app->db->createCommand($sql);
                    // $reader = $command->query();
                    // $data2 = $reader->readAll();
                    // if(count($data2)==0){
                    //     $bl_reject = true;
                    //     $remarks[$k] = $remarks[$k] . "Ship from GSTin not found in warehouse master. ";
                    // }

                    // $sql = "select * from acc_master where legal_name = '".$market_place[$k]."' and type = 'Marketplace'";
                    // $command = Yii::$app->db->createCommand($sql);
                    // $reader = $command->query();
                    // $data2 = $reader->readAll();
                    // if(count($data2)==0){
                    //     $bl_reject = true;
                    //     $remarks[$k] = $remarks[$k] . "Marketplace not found. ";
                    // } else {
                    //     $marketplace_id[$k] = $data2[0]['id'];
                    // }

                    // $sql = "select * from pincode_master where pincode = '".$pin_code[$k]."'";
                    // $command = Yii::$app->db->createCommand($sql);
                    // $reader = $command->query();
                    // $data2 = $reader->readAll();
                    // if(count($data2)>0){
                    //     $ship_to_state[$k] = $data2[0]['state_name'];
                    // }

                    // if($ship_to_state[$k]==''){
                    //     $sql = "select * from acc_amazon_state_master where is_active = '1' and company_id = '$company_id' 
                    //             and amazon_state = '".$amazon_state[$k]."'";
                    //     $command = Yii::$app->db->createCommand($sql);
                    //     $reader = $command->query();
                    //     $data2 = $reader->readAll();
                    //     if(count($data2)>0){
                    //         $ship_to_state[$k] = $data2[0]['erp_state'];
                    //         if($ship_to_state[$k]!='' && $pin_code[$k]!=''){
                    //             $this->insert_pincode($ship_to_state[$k], $pin_code[$k]);
                    //         }
                    //     }
                    // }

                    // if($ship_to_state[$k]==''){
                    //     $bl_reject = true;
                    //     $remarks[$k] = $remarks[$k] . "Pincode not found. ";
                    // }

                    // if($ship_to_gstin[$k]==''){
                    //     $sales_type[$k] = 'B2C';
                    // } else {
                    //     $sales_type[$k] = 'B2B';

                    //     $highlight_remarks[$k] = $this->check_gst_no_format($ship_to_gstin[$k], $ship_to_state[$k]);
                    //     if($highlight_remarks[$k]!=''){
                    //         $bl_highlight = true;
                    //     }
                    // }

                    // $new_hsn_code[$k] = '';
                    // if($sku[$k]!=''){
                    //     $sql = "select * from product_master where is_active = '1' and company_id = '$company_id' 
                    //             and sku_internal_code = '".$sku[$k]."'";
                    //     $command = Yii::$app->db->createCommand($sql);
                    //     $reader = $command->query();
                    //     $data2 = $reader->readAll();
                    //     if(count($data2)>0){
                    //         $new_hsn_code[$k] = $data2[0]['hsn_code'];
                    //     }
                    // }

                    // if($hsn_code[$k] == '' || $hsn_code[$k]==null){
                    //     $bl_highlight = true;
                    //     $highlight_remarks[$k] = $highlight_remarks[$k] . "HSN Code is empty. ";
                    // } else if($new_hsn_code[$k] == '' || $new_hsn_code[$k]==null){
                    //     $bl_highlight = true;
                    //     $highlight_remarks[$k] = $highlight_remarks[$k] . "HSN Code not found in Product Master. ";
                    // } else if($new_hsn_code[$k] != $hsn_code[$k]){
                    //     $bl_highlight = true;
                    //     $highlight_remarks[$k] = $highlight_remarks[$k] . "HSN Code is different. ";
                    // }

                    // if($rate[$k]!=''){
                    //     if($this->check_no($rate[$k])==false){
                    //         $bl_reject = true;
                    //         $remarks[$k] = $remarks[$k] . "Rate is not number. ";
                    //     }
                    // }
                    // if($quantity[$k]!=''){
                    //     if($this->check_no($quantity[$k])==false){
                    //         $bl_reject = true;
                    //         $remarks[$k] = $remarks[$k] . "Quantity is not number. ";
                    //     }
                    // }
                    // if($sales_incl_gst[$k]!=''){
                    //     if($this->check_no($sales_incl_gst[$k])==false){
                    //         $bl_reject = true;
                    //         $remarks[$k] = $remarks[$k] . "Sales Incl GST is not number. ";
                    //     }
                    // }
                    // if($sales_excl_gst[$k]!=''){
                    //     if($this->check_no($sales_excl_gst[$k])==false){
                    //         $bl_reject = true;
                    //         $remarks[$k] = $remarks[$k] . "Sales Excl GST is not number. ";
                    //     }
                    // }
                    // if($total_gst[$k]!=''){
                    //     if($this->check_no($total_gst[$k])==false){
                    //         $bl_reject = true;
                    //         $remarks[$k] = $remarks[$k] . "Total GST is not number. ";
                    //     }
                    // }
                    // if($igst_rate[$k]!=''){
                    //     if($this->check_no($igst_rate[$k])==false){
                    //         $bl_reject = true;
                    //         $remarks[$k] = $remarks[$k] . "IGST Rate is not number. ";
                    //     }
                    // }
                    // if($igst_amount[$k]!=''){
                    //     if($this->check_no($igst_amount[$k])==false){
                    //         $bl_reject = true;
                    //         $remarks[$k] = $remarks[$k] . "IGST Amount is not number. ";
                    //     }
                    // }
                    // if($cgst_rate[$k]!=''){
                    //     if($this->check_no($cgst_rate[$k])==false){
                    //         $bl_reject = true;
                    //         $remarks[$k] = $remarks[$k] . "CGST Rate is not number. ";
                    //     }
                    // }
                    // if($cgst_amount[$k]!=''){
                    //     if($this->check_no($cgst_amount[$k])==false){
                    //         $bl_reject = true;
                    //         $remarks[$k] = $remarks[$k] . "CGST Amount is not number. ";
                    //     }
                    // }
                    // if($sgst_rate[$k]!=''){
                    //     if($this->check_no($sgst_rate[$k])==false){
                    //         $bl_reject = true;
                    //         $remarks[$k] = $remarks[$k] . "SGST Rate is not number. ";
                    //     }
                    // }
                    // if($sgst_amount[$k]!=''){
                    //     if($this->check_no($sgst_amount[$k])==false){
                    //         $bl_reject = true;
                    //         $remarks[$k] = $remarks[$k] . "SGST Amount is not number. ";
                    //     }
                    // }

                    // if($flag[$k]!='0' && $flag[$k]!='1'){
                    //     $bl_reject = true;
                    //     $remarks[$k] = $remarks[$k] . "Flag should be 0 or 1. ";
                    // }

                    // if($bl_reject==true) {
                    //     // echo 'rejected';
                    //     // echo '<br/>';
                    //     // echo $remarks[$k];
                    //     // echo '<br/>';
                    //     $reject_sheet->setCellValue('Y'.$r_row, $remarks[$k]);
                    //     $reject_file = true;
                    // }
                    // if($bl_highlight==true) {
                    //     // echo $highlight_remarks[$k].'<br/>';
                    //     $highlight_sheet->setCellValue('Y'.$h_row, $highlight_remarks[$k]);
                    //     $highlight_file = true;
                    // }

                    $bl_reject = false;
                    $bl_highlight = false;

                    $ship_to_state = '';
                    $remarks = '';
                    $highlight_remarks = '';
                    $marketplace_id = '0';

                    $market_place = $objPHPExcel->getActiveSheet()->getCell('A'.$j)->getValue();
                    $ship_from_gstin = $objPHPExcel->getActiveSheet()->getCell('B'.$j)->getValue();
                    $ship_from_state = $objPHPExcel->getActiveSheet()->getCell('C'.$j)->getValue();
                    $ship_to_gstin = $objPHPExcel->getActiveSheet()->getCell('D'.$j)->getValue();
                    $amazon_state = $objPHPExcel->getActiveSheet()->getCell('E'.$j)->getValue();
                    $pin_code = $objPHPExcel->getActiveSheet()->getCell('F'.$j)->getValue();
                    $invoice_no = $objPHPExcel->getActiveSheet()->getCell('G'.$j)->getValue();
                    $invoice_date = $objPHPExcel->getActiveSheet()->getCell('H'.$j)->getValue();
                    $customer_name =$objPHPExcel->getActiveSheet()->getCell('I'.$j)->getValue();
                    $sku = $objPHPExcel->getActiveSheet()->getCell('J'.$j)->getValue();
                    $item_desc = $objPHPExcel->getActiveSheet()->getCell('K'.$j)->getValue();
                    $hsn_code = $objPHPExcel->getActiveSheet()->getCell('L'.$j)->getValue();
                    $quantity = $objPHPExcel->getActiveSheet()->getCell('M'.$j)->getValue();
                    $rate = $objPHPExcel->getActiveSheet()->getCell('N'.$j)->getValue();
                    $sales_incl_gst = $objPHPExcel->getActiveSheet()->getCell('O'.$j)->getValue();
                    $sales_excl_gst = $objPHPExcel->getActiveSheet()->getCell('P'.$j)->getValue();
                    $total_gst = $objPHPExcel->getActiveSheet()->getCell('Q'.$j)->getValue();
                    $igst_rate = $objPHPExcel->getActiveSheet()->getCell('R'.$j)->getValue();
                    $igst_amount = $objPHPExcel->getActiveSheet()->getCell('S'.$j)->getValue();
                    $cgst_rate = $objPHPExcel->getActiveSheet()->getCell('T'.$j)->getValue();
                    $cgst_amount = $objPHPExcel->getActiveSheet()->getCell('U'.$j)->getValue();
                    $sgst_rate = $objPHPExcel->getActiveSheet()->getCell('V'.$j)->getValue();
                    $sgst_amount = $objPHPExcel->getActiveSheet()->getCell('W'.$j)->getValue();
                    $flag = $objPHPExcel->getActiveSheet()->getCell('X'.$j)->getValue();

                    // $sql = "select * from internal_warehouse_master where is_active = '1' and company_id = '$company_id' 
                    //         and gst_id = '".$ship_from_gstin."'";
                    // $command = Yii::$app->db->createCommand($sql);
                    // $reader = $command->query();
                    // $data2 = $reader->readAll();
                    // if(count($data2)==0){
                    //     $bl_reject = true;
                    //     $remarks = $remarks . "Ship from GSTin not found in warehouse master. ";
                    // }

                    // $sql = "select * from acc_master where legal_name = '".$market_place."' and type = 'Marketplace'";
                    // $command = Yii::$app->db->createCommand($sql);
                    // $reader = $command->query();
                    // $data2 = $reader->readAll();
                    // if(count($data2)==0){
                    //     $bl_reject = true;
                    //     $remarks = $remarks . "Marketplace not found. ";
                    // } else {
                    //     $marketplace_id = $data2[0]['id'];
                    // }

                    $sql = "select * from pincode_master where pincode = '".$pin_code."'";
                    $command = Yii::$app->db->createCommand($sql);
                    $reader = $command->query();
                    $data2 = $reader->readAll();
                    if(count($data2)>0){
                        $ship_to_state = $data2[0]['state_name'];
                    }

                    if($ship_to_state==''){
                        $sql = "select * from acc_amazon_state_master where is_active = '1' and company_id = '$company_id' 
                                and amazon_state = '".$amazon_state."'";
                        $command = Yii::$app->db->createCommand($sql);
                        $reader = $command->query();
                        $data2 = $reader->readAll();
                        if(count($data2)>0){
                            $ship_to_state = $data2[0]['erp_state'];
                            if($ship_to_state!='' && $pin_code!=''){
                                $this->insert_pincode($ship_to_state, $pin_code);
                            }
                        }
                    }

                    if($ship_to_state==''){
                        $bl_reject = true;
                        $remarks = $remarks . "Pincode not found. ";
                    }

                    if($ship_to_gstin==''){
                        $sales_type = 'B2C';
                    } else {
                        $sales_type = 'B2B';

                        $highlight_remarks = $this->check_gst_no_format($ship_to_gstin, $ship_to_state);
                        if($highlight_remarks!=''){
                            $bl_highlight = true;
                        }
                    }

                    // $new_hsn_code = '';
                    // if($sku!=''){
                    //     $sql = "select * from product_master where is_active = '1' and company_id = '$company_id' 
                    //             and sku_internal_code = '".$sku."'";
                    //     $command = Yii::$app->db->createCommand($sql);
                    //     $reader = $command->query();
                    //     $data2 = $reader->readAll();
                    //     if(count($data2)>0){
                    //         $new_hsn_code = $data2[0]['hsn_code'];
                    //     }
                    // }

                    // if($hsn_code == '' || $hsn_code==null){
                    //     $bl_highlight = true;
                    //     $highlight_remarks = $highlight_remarks . "HSN Code is empty. ";
                    // } else if($new_hsn_code == '' || $new_hsn_code==null){
                    //     $bl_highlight = true;
                    //     $highlight_remarks = $highlight_remarks . "HSN Code not found in Product Master. ";
                    // } else if($new_hsn_code != $hsn_code){
                    //     $bl_highlight = true;
                    //     $highlight_remarks = $highlight_remarks . "HSN Code is different. ";
                    // }

                    // if($quantity!=''){
                    //     if($this->check_no($quantity)==false){
                    //         $quantity=NULL;
                    //     }
                    // }
                    // if($rate!=''){
                    //     if($this->check_no($rate)==false){
                    //         $rate=NULL;
                    //     }
                    // }
                    // if($sales_incl_gst!=''){
                    //     if($this->check_no($sales_incl_gst)==false){
                    //         $sales_incl_gst=NULL;
                    //     }
                    // }
                    // if($sales_excl_gst!=''){
                    //     if($this->check_no($sales_excl_gst)==false){
                    //         $sales_excl_gst=NULL;
                    //     }
                    // }
                    // if($total_gst!=''){
                    //     if($this->check_no($total_gst)==false){
                    //         $total_gst=NULL;
                    //     }
                    // }
                    // if($igst_rate!=''){
                    //     if($this->check_no($igst_rate)==false){
                    //         $igst_rate=NULL;
                    //     }
                    // }
                    // if($igst_amount!=''){
                    //     if($this->check_no($igst_amount)==false){
                    //         $igst_amount=NULL;
                    //     }
                    // }
                    // if($cgst_rate!=''){
                    //     if($this->check_no($cgst_rate)==false){
                    //         $cgst_rate=NULL;
                    //     }
                    // }
                    // if($cgst_amount!=''){
                    //     if($this->check_no($cgst_amount)==false){
                    //         $cgst_amount=NULL;
                    //     }
                    // }
                    // if($sgst_rate!=''){
                    //     if($this->check_no($sgst_rate)==false){
                    //         $sgst_rate=NULL;
                    //     }
                    // }
                    // if($sgst_amount!=''){
                    //     if($this->check_no($sgst_amount)==false){
                    //         $sgst_amount=NULL;
                    //     }
                    // }
                    // if($flag!=''){
                    //     if($this->check_no($flag)==false){
                    //         $flag=NULL;
                    //     }
                    // }


                    if($invoice_date==''){
                        $invoice_date=NULL;
                    } else {
                        $invoice_date = \PHPExcel_Style_NumberFormat::toFormattedString($invoice_date, 'YYYY-MM-DD');
                    }
                    
                    if($quantity!=''){
                        if($this->check_no($quantity)==false){
                            $quantity=NULL;
                            $bl_reject = true;
                            $remarks = $remarks . "Quantity is not number. ";
                        }
                    }
                    if($rate!=''){
                        if($this->check_no($rate)==false){
                            $rate=NULL;
                            $bl_reject = true;
                            $remarks = $remarks . "Rate is not number. ";
                        }
                    }
                    if($sales_incl_gst!=''){
                        if($this->check_no($sales_incl_gst)==false){
                            $sales_incl_gst=NULL;
                            $bl_reject = true;
                            $remarks = $remarks . "Sales Incl GST is not number. ";
                        }
                    }
                    if($sales_excl_gst!=''){
                        if($this->check_no($sales_excl_gst)==false){
                            $sales_excl_gst=NULL;
                            $bl_reject = true;
                            $remarks = $remarks . "Sales Excl GST is not number. ";
                        }
                    }
                    if($total_gst!=''){
                        if($this->check_no($total_gst)==false){
                            $total_gst=NULL;
                            $bl_reject = true;
                            $remarks = $remarks . "Total GST is not number. ";
                        }
                    }
                    if($igst_rate!=''){
                        if($this->check_no($igst_rate)==false){
                            $igst_rate=NULL;
                            $bl_reject = true;
                            $remarks = $remarks . "IGST Rate is not number. ";
                        }
                    }
                    if($igst_amount!=''){
                        if($this->check_no($igst_amount)==false){
                            $igst_amount=NULL;
                            $bl_reject = true;
                            $remarks = $remarks . "IGST Amount is not number. ";
                        }
                    }
                    if($cgst_rate!=''){
                        if($this->check_no($cgst_rate)==false){
                            $cgst_rate=NULL;
                            $bl_reject = true;
                            $remarks = $remarks . "CGST Rate is not number. ";
                        }
                    }
                    if($cgst_amount!=''){
                        if($this->check_no($cgst_amount)==false){
                            $cgst_amount=NULL;
                            $bl_reject = true;
                            $remarks = $remarks . "CGST Amount is not number. ";
                        }
                    }
                    if($sgst_rate!=''){
                        if($this->check_no($sgst_rate)==false){
                            $sgst_rate=NULL;
                            $bl_reject = true;
                            $remarks = $remarks . "SGST Rate is not number. ";
                        }
                    }
                    if($sgst_amount!=''){
                        if($this->check_no($sgst_amount)==false){
                            $sgst_amount=NULL;
                            $bl_reject = true;
                            $remarks = $remarks . "SGST Amount is not number. ";
                        }
                    }

                    if($flag!='0' && $flag!='1'){
                        $flag=NULL;
                        $bl_reject = true;
                        $remarks = $remarks . "Flag should be 0 or 1. ";
                    }

                    if($bl_reject==true) {
                        // echo 'rejected';
                        // echo '<br/>';
                        // echo $remarks;
                        // echo '<br/>';
                        // $reject_sheet->setCellValue('Y'.$r_row, $remarks);
                        $reject_file = true;
                    }
                    if($bl_highlight==true) {
                        // echo $highlight_remarks.'<br/>';
                        // $highlight_sheet->setCellValue('Y'.$h_row, $highlight_remarks);
                        $highlight_file = true;
                    }

                    $bulkInsertArray[$k] = array('ref_file_id' => $ref_file_id, 
                        'market_place' => $market_place, 
                        'marketplace_id' => $marketplace_id, 
                        'ship_from_gstin' => $ship_from_gstin, 
                        'ship_from_state' => $ship_from_state, 
                        'ship_to_gstin' => $ship_to_gstin, 
                        'ship_to_state' => $ship_to_state, 
                        'amazon_state' => $amazon_state, 
                        'pin_code' => $pin_code, 
                        'invoice_no' => $invoice_no, 
                        'invoice_date' => $invoice_date, 
                        'customer_name' => $customer_name, 
                        'sku' => $sku, 
                        'item_desc' => $item_desc, 
                        'hsn_code' => $hsn_code, 
                        'quantity' => $mycomponent->format_number($quantity,2), 
                        'rate' => $mycomponent->format_number($rate,2), 
                        'sales_incl_gst' => $mycomponent->format_number($sales_incl_gst,2), 
                        'sales_excl_gst' => $mycomponent->format_number($sales_excl_gst,2), 
                        'total_gst' => $mycomponent->format_number($total_gst,2), 
                        'igst_rate' => $mycomponent->format_number($igst_rate,2), 
                        'igst_amount' => $mycomponent->format_number($igst_amount,2), 
                        'cgst_rate' => $mycomponent->format_number($cgst_rate,2), 
                        'cgst_amount' => $mycomponent->format_number($cgst_amount,2), 
                        'sgst_rate' => $mycomponent->format_number($sgst_rate,2), 
                        'sgst_amount' => $mycomponent->format_number($sgst_amount,2), 
                        'flag' => $flag,
                        'status' => 'pending',
                        'is_active' => '1',
                        'created_by'=>$curusr,
                        'created_date'=>$now,
                        'updated_by'=>$curusr,
                        'updated_date'=>$now,
                        'approver_comments'=>'',
                        'company_id'=>$company_id,
                        'reject_remarks'=>$remarks,
                        'highlight_remarks'=>$highlight_remarks
                    );

                    $k = $k + 1;
                }

                // echo $j;
                // echo '<br/>';

                // if($j==10000){
                //     break;
                // }
            }

            if(count($bulkInsertArray)>0){
                $sql = "delete from acc_temp_sales_file_items where ref_file_id = '$ref_file_id'";
                Yii::$app->db->createCommand($sql)->execute();

                $columnNameArray=['ref_file_id','market_place','marketplace_id','ship_from_gstin','ship_from_state',
                                    'ship_to_gstin','ship_to_state','amazon_state','pin_code','invoice_no','invoice_date',
                                    'customer_name','sku','item_desc','hsn_code','quantity','rate',
                                    'sales_incl_gst','sales_excl_gst','total_gst','igst_rate','igst_amount','cgst_rate',
                                    'cgst_amount','sgst_rate','sgst_amount','flag','status','is_active',
                                    'created_by','created_date','updated_by','updated_date','approver_comments',
                                    'company_id','reject_remarks','highlight_remarks'];
                $tableName = "acc_temp_sales_file_items";
                $insertCount = Yii::$app->db->createCommand()->batchInsert($tableName, $columnNameArray, $bulkInsertArray)->execute();

                // echo $insertCount;
                // echo '<br/>';
                // echo 'hii';

                $sql = "update acc_temp_sales_file_items A left join internal_warehouse_master B 
                        on (A.ship_from_gstin=B.gst_id and A.company_id=B.company_id and A.is_active=B.is_active) 
                        set A.reject_remarks = concat(A.reject_remarks, 'Ship from GSTIN not found in warehouse master. ') 
                        where A.ref_file_id = '$ref_file_id' and A.is_active = '1' and A.company_id = '$company_id' and B.gst_id is null";
                $updateCount = Yii::$app->db->createCommand($sql)->execute();
                if($updateCount>0){
                    $bl_reject = true;
                }

                $sql = "update acc_temp_sales_file_items A left join acc_master B 
                        on (A.market_place=B.legal_name and A.company_id=B.company_id and A.is_active=B.is_active and B.type='Marketplace') 
                        set A.reject_remarks = concat(A.reject_remarks, 'Marketplace not found. ') 
                        where A.ref_file_id = '$ref_file_id' and A.is_active = '1' and A.company_id = '$company_id' and B.legal_name is null";
                $updateCount = Yii::$app->db->createCommand($sql)->execute();
                if($updateCount>0){
                    $bl_reject = true;
                } else {
                    $sql = "update acc_temp_sales_file_items A left join acc_master B 
                            on (A.market_place=B.legal_name and A.company_id=B.company_id and A.is_active=B.is_active and B.type='Marketplace') 
                            set A.marketplace_id = B.id 
                            where A.ref_file_id = '$ref_file_id' and A.is_active = '1' and A.company_id = '$company_id'";
                    Yii::$app->db->createCommand($sql)->execute();
                }

                // $sql = "update acc_temp_sales_file_items A left join pincode_master B on (A.pin_code=B.pincode) 
                //         set A.ship_to_state = B.state_name 
                //         where A.ref_file_id = '$ref_file_id' and A.is_active = '1' and A.company_id = '$company_id'";
                // $updateCount = Yii::$app->db->createCommand($sql)->execute();

                // $sql = "select * from acc_temp_sales_file_items where ref_file_id = '$ref_file_id' and is_active = '1' and company_id = '$company_id' 
                //         and (ship_to_state is null or ship_to_state = '')";
                // $command = Yii::$app->db->createCommand($sql);
                // $reader = $command->query();
                // $data2 = $reader->readAll();
                // if(count($data2)>0){
                //     for($j=0; $j<count($data2); $j++){
                //         $amazon_state = $data2[0]->amazon_state;
                //         $pin_code = $data2[0]->pin_code;

                //         $sql = "select * from acc_amazon_state_master where is_active = '1' and company_id = '$company_id' 
                //                 and amazon_state = '$amazon_state'";
                //         $command = Yii::$app->db->createCommand($sql);
                //         $reader = $command->query();
                //         $data3 = $reader->readAll();
                //         if(count($data3)>0){
                //             $ship_to_state = $data3[0]['erp_state'];
                //             if($ship_to_state!='' && $pin_code!=''){
                //                 $this->insert_pincode($ship_to_state, $pin_code);
                //             }
                //         }
                //     }
                // }

                // $sql = "update acc_temp_sales_file_items A left join pincode_master B on (A.pin_code=B.pincode) 
                //         set A.ship_to_state = B.state_name 
                //         where A.ref_file_id = '$ref_file_id' and A.is_active = '1' and A.company_id = '$company_id'";
                // $updateCount = Yii::$app->db->createCommand($sql)->execute();

                // $sql = "update acc_temp_sales_file_items A left join pincode_master B on (A.pin_code=B.pincode) 
                //         set A.reject_remarks = concat(A.reject_remarks, 'Pincode not found. ') 
                //         where A.ref_file_id = '$ref_file_id' and A.is_active = '1' and A.company_id = '$company_id' and B.pincode is null";
                // $updateCount = Yii::$app->db->createCommand($sql)->execute();
                // if($updateCount>0){
                //     $bl_reject = true;
                // }

                // $sql = "select * from acc_temp_sales_file_items where ref_file_id = '$ref_file_id' and is_active = '1' and company_id = '$company_id' 
                //         and (ship_to_gstin is null or ship_to_gstin = '')";
                // $command = Yii::$app->db->createCommand($sql);
                // $reader = $command->query();
                // $data2 = $reader->readAll();
                // if(count($data2)>0){
                //     for($j=0; $j<count($data2); $j++){
                //         $id = $data2[$j]['id'];
                //         $ship_to_gstin = $data2[$j]['ship_to_gstin'];
                //         $ship_to_state = $data2[$j]['ship_to_state'];

                //         if($ship_to_gstin==''){
                //             $sales_type = 'B2C';
                //         } else {
                //             $sales_type = 'B2B';

                //             $highlight_remarks = $this->check_gst_no_format($ship_to_gstin, $ship_to_state);
                //             if($highlight_remarks!=''){
                //                 $bl_highlight = true;

                //                 $sql = "update acc_temp_sales_file_items A 
                //                         set A.highlight_remarks = concat(A.highlight_remarks, '".$highlight_remarks." ') 
                //                         where A.id = '$id'";
                //                 $updateCount = Yii::$app->db->createCommand($sql)->execute();
                //             }
                //         }
                //     }
                // }

                $sql = "update acc_temp_sales_file_items A left join product_master B 
                        on (A.sku=B.sku_internal_code and A.company_id=B.company_id and A.is_active=B.is_active and 
                            B.is_latest = '1' and B.is_preferred = '1') 
                        set A.highlight_remarks = case when A.hsn_code is null then concat(A.highlight_remarks, 'HSN Code is empty. ') 
                            when B.hsn_code is null then concat(A.highlight_remarks, 'HSN Code not found in Product Master. ') 
                            when A.hsn_code<>B.hsn_code then concat(A.highlight_remarks, 'HSN Code is different. ') 
                            else A.highlight_remarks end 
                        where A.ref_file_id = '$ref_file_id' and A.is_active = '1' and A.company_id = '$company_id'";
                $updateCount = Yii::$app->db->createCommand($sql)->execute();
                if($updateCount>0){
                    $bl_highlight = true;
                }
            }

            if($bl_reject==true) {
                $reject_file = true;
            }
            if($bl_highlight==true) {
                $highlight_file = true;
            }

            if($reject_file==true) {
                $r_row = 1;
                /*$reject_spreadsheet = $objPHPExcel->createSheet(1);*/
                $objPHPExcel1 = new \PHPExcel();
                /*$objPHPExcel1->createSheet();*/
                $reject_sheet = $objPHPExcel1->setActiveSheetIndex(0);
                $reject_sheet->setCellValue('A'.$r_row, 'Market place');
                $reject_sheet->setCellValue('B'.$r_row, 'Ship from GSTin');
                $reject_sheet->setCellValue('C'.$r_row, 'Ship from State');
                $reject_sheet->setCellValue('D'.$r_row, 'Ship to GSTin');
                $reject_sheet->setCellValue('E'.$r_row, 'Amazon state');
                $reject_sheet->setCellValue('F'.$r_row, 'Pin code');
                $reject_sheet->setCellValue('G'.$r_row, 'Invoice no');
                $reject_sheet->setCellValue('H'.$r_row, 'Invoice date');
                $reject_sheet->setCellValue('I'.$r_row, 'Customer name');
                $reject_sheet->setCellValue('J'.$r_row, 'SKU');
                $reject_sheet->setCellValue('K'.$r_row, 'Item description');
                $reject_sheet->setCellValue('L'.$r_row, 'HSN Code');
                $reject_sheet->setCellValue('M'.$r_row, 'Quantity');
                $reject_sheet->setCellValue('N'.$r_row, 'Rate');
                $reject_sheet->setCellValue('O'.$r_row, 'Sales incl GST');
                $reject_sheet->setCellValue('P'.$r_row, 'Sales excl GST');
                $reject_sheet->setCellValue('Q'.$r_row, 'Total GST');
                $reject_sheet->setCellValue('R'.$r_row, 'IGST Rate');
                $reject_sheet->setCellValue('S'.$r_row, 'IGST Amount');
                $reject_sheet->setCellValue('T'.$r_row, 'CGST Rate');
                $reject_sheet->setCellValue('U'.$r_row, 'CGST Amount');
                $reject_sheet->setCellValue('V'.$r_row, 'SGST Rate');
                $reject_sheet->setCellValue('W'.$r_row, 'SGST Amount');
                $reject_sheet->setCellValue('X'.$r_row, 'Flag');
                $reject_sheet->setCellValue('Y'.$r_row, 'Remarks');
            }
            if($highlight_file==true) {
                $h_row = 1;
                /*$highlight_spreadsheet = $objPHPExcel->createSheet(2);*/
                $objPHPExcel2 = new \PHPExcel();
                /*$objPHPExcel2->createSheet();*/
                $highlight_sheet = $objPHPExcel2->setActiveSheetIndex(0);
                $highlight_sheet->setCellValue('A'.$h_row, 'Market place');
                $highlight_sheet->setCellValue('B'.$h_row, 'Ship from GSTin');
                $highlight_sheet->setCellValue('C'.$h_row, 'Ship from State');
                $highlight_sheet->setCellValue('D'.$h_row, 'Ship to GSTin');
                $highlight_sheet->setCellValue('E'.$h_row, 'Amazon state');
                $highlight_sheet->setCellValue('F'.$h_row, 'Pin code');
                $highlight_sheet->setCellValue('G'.$h_row, 'Invoice no');
                $highlight_sheet->setCellValue('H'.$h_row, 'Invoice date');
                $highlight_sheet->setCellValue('I'.$h_row, 'Customer name');
                $highlight_sheet->setCellValue('J'.$h_row, 'SKU');
                $highlight_sheet->setCellValue('K'.$h_row, 'Item description');
                $highlight_sheet->setCellValue('L'.$h_row, 'HSN Code');
                $highlight_sheet->setCellValue('M'.$h_row, 'Quantity');
                $highlight_sheet->setCellValue('N'.$h_row, 'Rate');
                $highlight_sheet->setCellValue('O'.$h_row, 'Sales incl GST');
                $highlight_sheet->setCellValue('P'.$h_row, 'Sales excl GST');
                $highlight_sheet->setCellValue('Q'.$h_row, 'Total GST');
                $highlight_sheet->setCellValue('R'.$h_row, 'IGST Rate');
                $highlight_sheet->setCellValue('S'.$h_row, 'IGST Amount');
                $highlight_sheet->setCellValue('T'.$h_row, 'CGST Rate');
                $highlight_sheet->setCellValue('U'.$h_row, 'CGST Amount');
                $highlight_sheet->setCellValue('V'.$h_row, 'SGST Rate');
                $highlight_sheet->setCellValue('W'.$h_row, 'SGST Amount');
                $highlight_sheet->setCellValue('X'.$h_row, 'Flag');
                $highlight_sheet->setCellValue('Y'.$h_row, 'Remarks');
            }

            if($reject_file==true || $highlight_file==true) {
                $sql = "select * from acc_temp_sales_file_items where ref_file_id = '$ref_file_id' and is_active = '1' and company_id = '$company_id'";
                $command = Yii::$app->db->createCommand($sql);
                $reader = $command->query();
                $data2 = $reader->readAll();
                if(count($data2)>0){
                    for($j=0; $j<count($data2); $j++){
                        if($reject_file==true) {
                            $r_row += 1;
                            $reject_sheet->setCellValue('A'.$r_row, $data2[$j]['market_place']);
                            $reject_sheet->setCellValue('B'.$r_row, $data2[$j]['ship_from_gstin']);
                            $reject_sheet->setCellValue('C'.$r_row, $data2[$j]['ship_from_state']);
                            $reject_sheet->setCellValue('D'.$r_row, $data2[$j]['ship_to_gstin']);
                            $reject_sheet->setCellValue('E'.$r_row, $data2[$j]['amazon_state']);
                            $reject_sheet->setCellValue('F'.$r_row, $data2[$j]['pin_code']);
                            $reject_sheet->setCellValue('G'.$r_row, $data2[$j]['invoice_no']);
                            $reject_sheet->setCellValue('H'.$r_row, $data2[$j]['invoice_date']);
                            $reject_sheet->setCellValue('I'.$r_row, $data2[$j]['customer_name']);
                            $reject_sheet->setCellValue('J'.$r_row, $data2[$j]['sku']);
                            $reject_sheet->setCellValue('K'.$r_row, $data2[$j]['item_desc']);
                            $reject_sheet->setCellValue('L'.$r_row, $data2[$j]['hsn_code']);
                            $reject_sheet->setCellValue('M'.$r_row, $data2[$j]['quantity']);
                            $reject_sheet->setCellValue('N'.$r_row, $data2[$j]['rate']);
                            $reject_sheet->setCellValue('O'.$r_row, $data2[$j]['sales_incl_gst']);
                            $reject_sheet->setCellValue('P'.$r_row, $data2[$j]['sales_excl_gst']);
                            $reject_sheet->setCellValue('Q'.$r_row, $data2[$j]['total_gst']);
                            $reject_sheet->setCellValue('R'.$r_row, $data2[$j]['igst_rate']);
                            $reject_sheet->setCellValue('S'.$r_row, $data2[$j]['igst_amount']);
                            $reject_sheet->setCellValue('T'.$r_row, $data2[$j]['cgst_rate']);
                            $reject_sheet->setCellValue('U'.$r_row, $data2[$j]['cgst_amount']);
                            $reject_sheet->setCellValue('V'.$r_row, $data2[$j]['sgst_rate']);
                            $reject_sheet->setCellValue('W'.$r_row, $data2[$j]['sgst_amount']);
                            $reject_sheet->setCellValue('X'.$r_row, $data2[$j]['flag']);
                            $reject_sheet->setCellValue('Y'.$r_row, $data2[$j]['reject_remarks']);
                        }
                        
                        if($highlight_file==true) {
                            $h_row += 1;
                            $highlight_sheet->setCellValue('A'.$h_row, $data2[$j]['market_place']);
                            $highlight_sheet->setCellValue('B'.$h_row, $data2[$j]['ship_from_gstin']);
                            $highlight_sheet->setCellValue('C'.$h_row, $data2[$j]['ship_from_state']);
                            $highlight_sheet->setCellValue('D'.$h_row, $data2[$j]['ship_to_gstin']);
                            $highlight_sheet->setCellValue('E'.$h_row, $data2[$j]['amazon_state']);
                            $highlight_sheet->setCellValue('F'.$h_row, $data2[$j]['pin_code']);
                            $highlight_sheet->setCellValue('G'.$h_row, $data2[$j]['invoice_no']);
                            $highlight_sheet->setCellValue('H'.$h_row, $data2[$j]['invoice_date']);
                            $highlight_sheet->setCellValue('I'.$h_row, $data2[$j]['customer_name']);
                            $highlight_sheet->setCellValue('J'.$h_row, $data2[$j]['sku']);
                            $highlight_sheet->setCellValue('K'.$h_row, $data2[$j]['item_desc']);
                            $highlight_sheet->setCellValue('L'.$h_row, $data2[$j]['hsn_code']);
                            $highlight_sheet->setCellValue('M'.$h_row, $data2[$j]['quantity']);
                            $highlight_sheet->setCellValue('N'.$h_row, $data2[$j]['rate']);
                            $highlight_sheet->setCellValue('O'.$h_row, $data2[$j]['sales_incl_gst']);
                            $highlight_sheet->setCellValue('P'.$h_row, $data2[$j]['sales_excl_gst']);
                            $highlight_sheet->setCellValue('Q'.$h_row, $data2[$j]['total_gst']);
                            $highlight_sheet->setCellValue('R'.$h_row, $data2[$j]['igst_rate']);
                            $highlight_sheet->setCellValue('S'.$h_row, $data2[$j]['igst_amount']);
                            $highlight_sheet->setCellValue('T'.$h_row, $data2[$j]['cgst_rate']);
                            $highlight_sheet->setCellValue('U'.$h_row, $data2[$j]['cgst_amount']);
                            $highlight_sheet->setCellValue('V'.$h_row, $data2[$j]['sgst_rate']);
                            $highlight_sheet->setCellValue('W'.$h_row, $data2[$j]['sgst_amount']);
                            $highlight_sheet->setCellValue('X'.$h_row, $data2[$j]['flag']);
                            $highlight_sheet->setCellValue('Y'.$h_row, $data2[$j]['highlight_remarks']);
                        }
                    }
                }
            }

            if($reject_file==false) {
                $sql = "insert into acc_sales_file_items (ref_file_id, market_place, marketplace_id, ship_from_gstin, 
                        ship_from_state, ship_to_gstin, ship_to_state, amazon_state, pin_code, invoice_no, invoice_date, 
                        customer_name, sku, item_desc, hsn_code, quantity, rate, sales_incl_gst, sales_excl_gst, total_gst, 
                        igst_rate, igst_amount, cgst_rate, cgst_amount, sgst_rate, sgst_amount, flag, status, is_active, 
                        created_by, created_date, updated_by, updated_date, approver_comments, company_id) 
                        select ref_file_id, market_place, marketplace_id, ship_from_gstin, 
                        ship_from_state, ship_to_gstin, ship_to_state, amazon_state, pin_code, invoice_no, invoice_date, 
                        customer_name, sku, item_desc, hsn_code, quantity, rate, sales_incl_gst, sales_excl_gst, total_gst, 
                        igst_rate, igst_amount, cgst_rate, cgst_amount, sgst_rate, sgst_amount, flag, status, is_active, 
                        created_by, created_date, updated_by, updated_date, approver_comments, company_id 
                        from acc_temp_sales_file_items 
                        where ref_file_id = '$ref_file_id' and is_active = '1' and company_id = '$company_id'";
                $updateCount = Yii::$app->db->createCommand($sql)->execute();

                $sql = "update acc_sales_files set upload_status = 'uploaded' where id = '$ref_file_id'";
                $command = Yii::$app->db->createCommand($sql);
                $count = $command->execute();

                $sql = "delete from acc_temp_sales_file_items where ref_file_id = '$ref_file_id'";
                Yii::$app->db->createCommand($sql)->execute();
            } else {
                $filename='sales_rejected_file_'.$ref_file_id.'.xlsx';
                $upload_path = './uploads';
                if(!is_dir($upload_path)) {
                    mkdir($upload_path, 0777, TRUE);
                }
                $upload_path = './uploads/sales';
                if(!is_dir($upload_path)) {
                    mkdir($upload_path, 0777, TRUE);
                }
                $upload_path = './uploads/sales/'.$ref_file_id;
                if(!is_dir($upload_path)) {
                    mkdir($upload_path, 0777, TRUE);
                }

                $file_name = $upload_path . '/' . $filename;
                $file_path = 'uploads/sales/' . $ref_file_id . '/' . $filename;

                $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel1, 'Excel2007');
                $objWriter->save($file_name);

                $sql = "update acc_sales_files set error_rejected_file = '$file_path', upload_status = 'rejected', 
                        updated_by = '$curusr', updated_date = '$now' where id = '$ref_file_id'";
                $command = Yii::$app->db->createCommand($sql);
                $count = $command->execute();
            }

            if($highlight_file==true){
                $filename='sales_highlighted_file_'.$ref_file_id.'.xlsx';
                $upload_path = './uploads';
                if(!is_dir($upload_path)) {
                    mkdir($upload_path, 0777, TRUE);
                }
                $upload_path = './uploads/sales';
                if(!is_dir($upload_path)) {
                    mkdir($upload_path, 0777, TRUE);
                }
                $upload_path = './uploads/sales/'.$ref_file_id;
                if(!is_dir($upload_path)) {
                    mkdir($upload_path, 0777, TRUE);
                }

                $file_name = $upload_path . '/' . $filename;
                $file_path = 'uploads/sales/' . $ref_file_id . '/' . $filename;

                $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel2, 'Excel2007');
                $objWriter->save($file_name);

                $sql = "update acc_sales_files set error_highlighted_file = '$file_path' where id = '$ref_file_id'";
                $command = Yii::$app->db->createCommand($sql);
                $count = $command->execute();
            }

            // echo json_encode($invoice_date);

            // echo count($market_place);
            // echo '<br/>';

            // if($reject_file==false) {
            //     for($j=0; $j<count($market_place); $j++) {
            //         if($invoice_date[$j]==''){
            //             $invoice_date[$j]=NULL;
            //         } else {
            //             // $invoice_date[$j]=$mycomponent->formatdate($invoice_date[$j]);
            //             $invoice_date[$j] = \PHPExcel_Style_NumberFormat::toFormattedString($invoice_date[$j], 'YYYY-MM-DD');
            //         }

            //         $array = array('ref_file_id' => $ref_file_id, 
            //                         'market_place' => $market_place[$j], 
            //                         'marketplace_id' => $marketplace_id[$j], 
            //                         'ship_from_gstin' => $ship_from_gstin[$j], 
            //                         'ship_from_state' => $ship_from_state[$j], 
            //                         'ship_to_gstin' => $ship_to_gstin[$j], 
            //                         'ship_to_state' => $ship_to_state[$j], 
            //                         'amazon_state' => $amazon_state[$j], 
            //                         'pin_code' => $pin_code[$j], 
            //                         'invoice_no' => $invoice_no[$j], 
            //                         'invoice_date' => $invoice_date[$j], 
            //                         'customer_name' => $customer_name[$j], 
            //                         'sku' => $sku[$j], 
            //                         'item_desc' => $item_desc[$j], 
            //                         'hsn_code' => $hsn_code[$j], 
            //                         'quantity' => $mycomponent->format_number($quantity[$j],2), 
            //                         'rate' => $mycomponent->format_number($rate[$j],2),
            //                         'sales_incl_gst' => $mycomponent->format_number($sales_incl_gst[$j],2),
            //                         'sales_excl_gst' => $mycomponent->format_number($sales_excl_gst[$j],2),
            //                         'total_gst' => $mycomponent->format_number($total_gst[$j],2),
            //                         'igst_rate' => $mycomponent->format_number($igst_rate[$j],2),
            //                         'igst_amount' => $mycomponent->format_number($igst_amount[$j],2),
            //                         'cgst_rate' => $mycomponent->format_number($cgst_rate[$j],2),
            //                         'cgst_amount' => $mycomponent->format_number($cgst_amount[$j],2),
            //                         'sgst_rate' => $mycomponent->format_number($sgst_rate[$j],2),
            //                         'sgst_amount' => $mycomponent->format_number($sgst_amount[$j],2),
            //                         'flag' => $flag[$j], 
            //                         'status' => 'pending',
            //                         'is_active' => '1',
            //                         'updated_by'=>$curusr,
            //                         'updated_date'=>$now,
            //                         'approver_comments'=>$remarks[$j],
            //                         'company_id'=>$company_id
            //                     );

            //         $array['created_by'] = $curusr;
            //         $array['created_date'] = $now;
            //         $count = Yii::$app->db->createCommand()
            //                     ->insert("acc_sales_file_items", $array)
            //                     ->execute();

            //         // echo json_encode($array);
            //         // echo '<br/><br/>';
            //     }

            //     $sql = "update acc_sales_files set upload_status = 'uploaded' where id = '$ref_file_id'";
            //     $command = Yii::$app->db->createCommand($sql);
            //     $count = $command->execute();
            // } else {
            //     $filename='sales_rejected_file_'.$ref_file_id.'.xlsx';
            //     $upload_path = './uploads';
            //     if(!is_dir($upload_path)) {
            //         mkdir($upload_path, 0777, TRUE);
            //     }
            //     $upload_path = './uploads/sales';
            //     if(!is_dir($upload_path)) {
            //         mkdir($upload_path, 0777, TRUE);
            //     }
            //     $upload_path = './uploads/sales/'.$ref_file_id;
            //     if(!is_dir($upload_path)) {
            //         mkdir($upload_path, 0777, TRUE);
            //     }

            //     $file_name = $upload_path . '/' . $filename;
            //     $file_path = 'uploads/sales/' . $ref_file_id . '/' . $filename;

            //     $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel1, 'Excel2007');
            //     $objWriter->save($file_name);


            //     $sql = "update acc_sales_files set error_rejected_file = '$file_path', upload_status = 'rejected', 
            //             updated_by = '$curusr', updated_date = '$now' 
            //             where id = '$ref_file_id'";
            //     $command = Yii::$app->db->createCommand($sql);
            //     $count = $command->execute();
            // }

            // if($highlight_file==true){
            //     $filename='sales_highlighted_file_'.$ref_file_id.'.xlsx';
            //     $upload_path = './uploads';
            //     if(!is_dir($upload_path)) {
            //         mkdir($upload_path, 0777, TRUE);
            //     }
            //     $upload_path = './uploads/sales';
            //     if(!is_dir($upload_path)) {
            //         mkdir($upload_path, 0777, TRUE);
            //     }
            //     $upload_path = './uploads/sales/'.$ref_file_id;
            //     if(!is_dir($upload_path)) {
            //         mkdir($upload_path, 0777, TRUE);
            //     }

            //     $file_name = $upload_path . '/' . $filename;
            //     $file_path = 'uploads/sales/' . $ref_file_id . '/' . $filename;

            //     $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel2, 'Excel2007');
            //     $objWriter->save($file_name);

            //     $sql = "update acc_sales_files set error_highlighted_file = '$file_path' 
            //             where id = '$ref_file_id'";
            //     $command = Yii::$app->db->createCommand($sql);
            //     $count = $command->execute();
            // }
        }
    }

    public function getAccountDetails($id="", $status="", $tax_code=""){
        $cond = "";
        $cond2 = "";
        if($id!=""){
            $cond = " and id = '$id'";
            $cond2 = " and acc_id = '$id'";
        }

        if($status!=""){
            $cond = $cond . " and status = '$status'";
        }

        if($tax_code!=""){
            $cond = $cond . " and legal_name like '%".$tax_code."%'";
        }

        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select A.*, concat_ws(',', A.category_1, A.category_2, A.category_3) as acc_category, B.bus_category from 
                (select * from acc_master where is_active = '1' and company_id = '$company_id' " . $cond . ") A 
                left join 
                (select acc_id, GROUP_CONCAT(category_name) as bus_category from acc_categories where is_active = '1'" . $cond2 . " 
                    group by acc_id) B 
                on (A.id = B.acc_id) order by legal_name";

        // echo $sql;
        // echo '<br/>';
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getFileDetails($id=""){
        $cond = "";
        if($id!=""){
            $cond = " where id = '$id'";
        }

        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select * from acc_sales_files " . $cond;
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getFileMarketplaces($id=""){
        $sql = "select distinct A.marketplace_id, A.market_place, B.id as acc_id, B.code as acc_code, 
                B.legal_name as acc_legal_name, 0 as sales_excl_gst, 0 as total_gst, 0 as sales_incl_gst, 
                0 as voucher_id 
                from acc_sales_file_items A left join acc_master B on (A.marketplace_id = B.id) 
                where A.ref_file_id = '$id' order by A.marketplace_id";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getFileInvoices($id=""){
        $sql = "select distinct invoice_no from acc_sales_file_items 
                where ref_file_id = '$id' order by invoice_no";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function get_details($id) {
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $final_data = array();

        $marketplace = $this->getFileMarketplaces($id);
        // $invoice = $this->getFileInvoices($id);

        $bl_posted = false;
        $sql = "select * from acc_sales_entries where file_id = '$id'";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        $result = $reader->readAll();
        if(count($result)>0){
            $bl_posted = true;
        }

        if($bl_posted==false) {
            $k=0;

            // for($k=0; $k<count($invoice); $k++) {
                // $invoice_no = $invoice[$k]['invoice_no'];
                $invoice_marketplace = $marketplace;
                $item_details = array();
                $a = 0;

                // $sql = "select * from 
                //         (select marketplace_id, invoice_no, invoice_date, ship_from_state, ship_to_state, 
                //             cgst_rate, sgst_rate, igst_rate, 
                //             sum(sales_excl_gst) as sales_excl_gst, sum(total_gst) as total_gst, 
                //             sum(sales_incl_gst) as sales_incl_gst, sum(cgst_amount) as cgst_amount, 
                //             sum(sgst_amount) as sgst_amount, sum(igst_amount) as igst_amount 
                //         from acc_sales_file_items where ref_file_id = '$id' and invoice_no = '$invoice_no' 
                //         group by marketplace_id, invoice_no, invoice_date, ship_from_state, ship_to_state, 
                //             cgst_rate, sgst_rate, igst_rate) A 
                //         order by invoice_no, cgst_rate, sgst_rate, igst_rate, marketplace_id";
                
                // $sql = "select * from acc_sales_file_items where ref_file_id = '$id' and invoice_no = '$invoice_no' 
                //         order by invoice_no, cgst_rate, sgst_rate, igst_rate, marketplace_id";

                $sql = "select * from acc_sales_file_items where ref_file_id = '$id' 
                        order by cgst_rate, sgst_rate, igst_rate, marketplace_id";
                $command = Yii::$app->db->createCommand($sql);
                $reader = $command->query();
                $result = $reader->readAll();
                for($i=0; $i<count($result); $i++) {
                    $marketplace_id = $result[$i]['marketplace_id'];
                    $market_place = $result[$i]['market_place'];
                    $invoice_no = $result[$i]['invoice_no'];
                    $invoice_date = $result[$i]['invoice_date'];
                    $sales_excl_gst = $result[$i]['sales_excl_gst'];
                    $total_gst = $result[$i]['total_gst'];
                    $sales_incl_gst = $result[$i]['sales_incl_gst'];
                    $cgst_rate = $result[$i]['cgst_rate'];
                    $cgst_amount = $result[$i]['cgst_amount'];
                    $sgst_rate = $result[$i]['sgst_rate'];
                    $sgst_amount = $result[$i]['sgst_amount'];
                    $igst_rate = $result[$i]['igst_rate'];
                    $igst_amount = $result[$i]['igst_amount'];
                    $state_name = $result[$i]['ship_from_state'];
                    $ship_from_state = $result[$i]['ship_from_state'];
                    $ship_to_state = $result[$i]['ship_to_state'];

                    if($ship_to_state==null || $ship_to_state=='') {
                        $trans_type = 'B2C';
                    } else {
                        $trans_type = 'B2B';
                    }
                    if(strtoupper(trim($ship_from_state))==strtoupper(trim($ship_to_state))) {
                        $tax_type = 'Local';
                    } else {
                        $tax_type = 'Inter State';
                    }

                    if($cgst_rate==null || $cgst_rate=='') $cgst_rate=0;
                    if($sgst_rate==null || $sgst_rate=='') $sgst_rate=0;
                    if($igst_rate==null || $igst_rate=='') $igst_rate=0;

                    if(!is_numeric($cgst_rate)) $cgst_rate=0; else $cgst_rate=$cgst_rate*100;
                    if(!is_numeric($sgst_rate)) $sgst_rate=0; else $sgst_rate=$sgst_rate*100;
                    if(!is_numeric($igst_rate)) $igst_rate=0; else $igst_rate=$igst_rate*100;

                    if($cgst_rate<0){
                        $cgst_rate = $cgst_rate * -1;
                    }
                    if($sgst_rate<0){
                        $sgst_rate = $sgst_rate * -1;
                    }
                    if($igst_rate<0){
                        $igst_rate = $igst_rate * -1;
                    }

                    $vat_percen = 0;
                    if($tax_type == 'Local') {
                        if(is_numeric($cgst_rate)){
                            $vat_percen = floatval($cgst_rate)*2;
                        } else if(is_numeric($sgst_rate)){
                            $vat_percen = floatval($sgst_rate)*2;
                        }
                    } else {
                        if(is_numeric($igst_rate)){
                            $vat_percen = floatval($igst_rate);
                        }
                    }

                    $acc_id = '';
                    $ledger_name = '';
                    $ledger_code = '';
                    $tax_code = 'Sales-'.$state_name.'-'.$tax_type.'-'.$trans_type.'-'.$vat_percen.'%';
                    $ledger_name = $tax_code;
                    $result2 = $this->getAccountDetails('','',$tax_code);
                    // echo $tax_code.'<br/>';
                    // echo count($result2).'<br/><br/>';
                    if(count($result2)>0) {
                        $acc_id = $result2[0]['id'];
                        $ledger_name = $result2[0]['legal_name'];
                        $ledger_code = $result2[0]['code'];
                    }
                    $bl_flag = false;
                    for($j=0; $j<count($item_details); $j++) {
                        if($item_details[$j]['ledger_name']==$ledger_name) {
                            $bl_flag = true;

                            $item_details[$j][$marketplace_id]+=$sales_excl_gst;
                        }
                    }
                    if($bl_flag == false) {
                        $item_details[$a]['particular'] = 'Taxable Amount';
                        $item_details[$a]['acc_type'] = 'Goods Sales';
                        $item_details[$a]['acc_id'] = $acc_id;
                        $item_details[$a]['ledger_name'] = $ledger_name;
                        $item_details[$a]['ledger_code'] = $ledger_code;
                        $item_details[$a]['tax_percent'] = $vat_percen;
                        $item_details[$a]['ship_from_state'] = $ship_from_state;
                        $item_details[$a]['ship_to_state'] = $ship_to_state;
                        for($j=0; $j<count($invoice_marketplace); $j++) {
                            if($invoice_marketplace[$j]['marketplace_id']==$marketplace_id){
                                $item_details[$a][$invoice_marketplace[$j]['marketplace_id']] = $sales_excl_gst;
                                $invoice_marketplace[$j]['sales_excl_gst'] += $sales_excl_gst;
                                $invoice_marketplace[$j]['sales_incl_gst'] += $sales_excl_gst;
                            } else {
                                $item_details[$a][$invoice_marketplace[$j]['marketplace_id']] = 0;
                            }
                        }
                        $a += 1;
                    } else {
                        for($j=0; $j<count($invoice_marketplace); $j++) {
                            if($invoice_marketplace[$j]['marketplace_id']==$marketplace_id){
                                $invoice_marketplace[$j]['sales_excl_gst'] += $sales_excl_gst;
                                $invoice_marketplace[$j]['sales_incl_gst'] += $sales_excl_gst;
                            }
                        }
                    }

                    if($tax_type == 'Local') {
                        $acc_id = '';
                        $ledger_name = '';
                        $ledger_code = '';
                        $tax_code = 'Output-'.$state_name.'-CGST-'.$cgst_rate.'%';
                        $ledger_name = $tax_code;
                        $result2 = $this->getAccountDetails('','',$tax_code);
                        // echo $tax_code.'<br/>';
                        // echo count($result2).'<br/><br/>';
                        if(count($result2)>0) {
                            $acc_id = $result2[0]['id'];
                            $ledger_name = $result2[0]['legal_name'];
                            $ledger_code = $result2[0]['code'];
                        }
                        $bl_flag = false;
                        for($j=0; $j<count($item_details); $j++) {
                            if($item_details[$j]['ledger_name']==$ledger_name) {
                                $bl_flag = true;

                                $item_details[$j][$marketplace_id]+=$cgst_amount;
                            }
                        }
                        if($bl_flag == false) {
                            $item_details[$a]['particular'] = 'CGST';
                            $item_details[$a]['acc_type'] = 'CGST';
                            $item_details[$a]['acc_id'] = $acc_id;
                            $item_details[$a]['ledger_name'] = $ledger_name;
                            $item_details[$a]['ledger_code'] = $ledger_code;
                            $item_details[$a]['tax_percent'] = $cgst_rate;
                            $item_details[$a]['ship_from_state'] = $ship_from_state;
                            $item_details[$a]['ship_to_state'] = $ship_to_state;
                            for($j=0; $j<count($invoice_marketplace); $j++) {
                                if($invoice_marketplace[$j]['marketplace_id']==$marketplace_id){
                                    $item_details[$a][$invoice_marketplace[$j]['marketplace_id']] = $cgst_amount;
                                    $invoice_marketplace[$j]['total_gst'] += $cgst_amount;
                                    $invoice_marketplace[$j]['sales_incl_gst'] += $cgst_amount;
                                } else {
                                    $item_details[$a][$invoice_marketplace[$j]['marketplace_id']] = 0;
                                }
                            }
                            $a += 1;
                        } else {
                            for($j=0; $j<count($invoice_marketplace); $j++) {
                                if($invoice_marketplace[$j]['marketplace_id']==$marketplace_id){
                                    $invoice_marketplace[$j]['total_gst'] += $cgst_amount;
                                    $invoice_marketplace[$j]['sales_incl_gst'] += $cgst_amount;
                                }
                            }
                        }

                        $acc_id = '';
                        $ledger_name = '';
                        $ledger_code = '';
                        $tax_code = 'Output-'.$state_name.'-SGST-'.$sgst_rate.'%';
                        $ledger_name = $tax_code;
                        $result2 = $this->getAccountDetails('','',$tax_code);
                        // echo $tax_code.'<br/>';
                        // echo count($result2).'<br/><br/>';
                        if(count($result2)>0) {
                            $acc_id = $result2[0]['id'];
                            $ledger_name = $result2[0]['legal_name'];
                            $ledger_code = $result2[0]['code'];
                        }
                        $bl_flag = false;
                        for($j=0; $j<count($item_details); $j++) {
                            if($item_details[$j]['ledger_name']==$ledger_name) {
                                $bl_flag = true;

                                $item_details[$j][$marketplace_id]+=$sgst_amount;
                            }
                        }
                        if($bl_flag == false) {
                            $item_details[$a]['particular'] = 'SGST';
                            $item_details[$a]['acc_type'] = 'SGST';
                            $item_details[$a]['acc_id'] = $acc_id;
                            $item_details[$a]['ledger_name'] = $ledger_name;
                            $item_details[$a]['ledger_code'] = $ledger_code;
                            $item_details[$a]['tax_percent'] = $sgst_rate;
                            $item_details[$a]['ship_from_state'] = $ship_from_state;
                            $item_details[$a]['ship_to_state'] = $ship_to_state;
                            for($j=0; $j<count($invoice_marketplace); $j++) {
                                if($invoice_marketplace[$j]['marketplace_id']==$marketplace_id){
                                    $item_details[$a][$invoice_marketplace[$j]['marketplace_id']] = $sgst_amount;
                                    $invoice_marketplace[$j]['total_gst'] += $sgst_amount;
                                    $invoice_marketplace[$j]['sales_incl_gst'] += $sgst_amount;
                                } else {
                                    $item_details[$a][$invoice_marketplace[$j]['marketplace_id']] = 0;
                                }
                            }
                            $a += 1;
                        } else {
                            for($j=0; $j<count($invoice_marketplace); $j++) {
                                if($invoice_marketplace[$j]['marketplace_id']==$marketplace_id){
                                    $invoice_marketplace[$j]['total_gst'] += $sgst_amount;
                                    $invoice_marketplace[$j]['sales_incl_gst'] += $sgst_amount;
                                }
                            }
                        }
                    } else {
                        $acc_id = '';
                        $ledger_name = '';
                        $ledger_code = '';
                        $tax_code = 'Output-'.$state_name.'-IGST-'.$igst_rate.'%';
                        $ledger_name = $tax_code;
                        $result2 = $this->getAccountDetails('','',$tax_code);
                        // echo $tax_code.'<br/>';
                        // echo count($result2).'<br/><br/>';
                        if(count($result2)>0) {
                            $acc_id = $result2[0]['id'];
                            $ledger_name = $result2[0]['legal_name'];
                            $ledger_code = $result2[0]['code'];
                        }
                        $bl_flag = false;
                        for($j=0; $j<count($item_details); $j++) {
                            if($item_details[$j]['ledger_name']==$ledger_name) {
                                $bl_flag = true;

                                $item_details[$j][$marketplace_id]+=$igst_amount;
                            }
                        }
                        if($bl_flag == false) {
                            $item_details[$a]['particular'] = 'IGST';
                            $item_details[$a]['acc_type'] = 'IGST';
                            $item_details[$a]['acc_id'] = $acc_id;
                            $item_details[$a]['ledger_name'] = $ledger_name;
                            $item_details[$a]['ledger_code'] = $ledger_code;
                            $item_details[$a]['tax_percent'] = $igst_rate;
                            $item_details[$a]['ship_from_state'] = $ship_from_state;
                            $item_details[$a]['ship_to_state'] = $ship_to_state;
                            for($j=0; $j<count($invoice_marketplace); $j++) {
                                if($invoice_marketplace[$j]['marketplace_id']==$marketplace_id){
                                    $item_details[$a][$invoice_marketplace[$j]['marketplace_id']] = $igst_amount;
                                    $invoice_marketplace[$j]['total_gst'] += $igst_amount;
                                    $invoice_marketplace[$j]['sales_incl_gst'] += $igst_amount;
                                } else {
                                    $item_details[$a][$invoice_marketplace[$j]['marketplace_id']] = 0;
                                }
                            }
                            $a += 1;
                        } else {
                            for($j=0; $j<count($invoice_marketplace); $j++) {
                                if($invoice_marketplace[$j]['marketplace_id']==$marketplace_id){
                                    $invoice_marketplace[$j]['total_gst'] += $igst_amount;
                                    $invoice_marketplace[$j]['sales_incl_gst'] += $igst_amount;
                                }
                            }
                        }
                    }
                }

                // echo json_encode($invoice_marketplace);
                // echo '<br/>';

                for($j=0; $j<count($invoice_marketplace); $j++) {
                    if($invoice_marketplace[$j]['sales_incl_gst']!=0){
                        $series = 1;
                        $sql = "select * from acc_series_master where type = 'Voucher' and company_id = '$company_id'";
                        $command = Yii::$app->db->createCommand($sql);
                        $reader = $command->query();
                        $data = $reader->readAll();
                        if (count($data)>0){
                            $series = intval($data[0]['series']) + 1;

                            $sql = "update acc_series_master set series = '$series' where type = 'Voucher' and company_id = '$company_id'";
                            $command = Yii::$app->db->createCommand($sql);
                            $count = $command->execute();
                        } else {
                            $series = 1;

                            $sql = "insert into acc_series_master (type, series, company_id) values ('Voucher', '".$series."', '".$company_id."')";
                            $command = Yii::$app->db->createCommand($sql);
                            $count = $command->execute();
                        }

                        $invoice_marketplace[$j]['voucher_id'] = $series;
                    }
                }

                // $final_data[$k]['invoice_no'] = $invoice[$k]['invoice_no'];
                $final_data[$k]['marketplace'] = $invoice_marketplace;
                $final_data[$k]['item_details'] = $item_details;
            // }
        } else {
            $k=0;

            // for($k=0; $k<count($invoice); $k++) {
            //     $invoice_no = $invoice[$k]['invoice_no'];
                $invoice_marketplace = $marketplace;
                $item_details = array();
                $a = 0;

                // $sql = "select * from acc_sales_entries where file_id = '$id' and invoice_no = '$invoice_no' 
                //         order by id";
                $sql = "select * from acc_sales_entries where file_id = '$id' order by id";
                $command = Yii::$app->db->createCommand($sql);
                $reader = $command->query();
                $result = $reader->readAll();
                // echo json_encode($result);
                // echo '<br/><br/>';
                
                for($i=0; $i<count($result); $i++) {
                    $marketplace_id = $result[$i]['marketplace_id'];

                    if($result[$i]['particular']=='Total Amount') {
                        for($j=0; $j<count($invoice_marketplace); $j++) {
                            if($invoice_marketplace[$j]['marketplace_id']==$marketplace_id){
                                $invoice_marketplace[$j]['sales_excl_gst'] += $result[$i]['amount'];
                                $invoice_marketplace[$j]['sales_incl_gst'] += $result[$i]['amount'];
                                $invoice_marketplace[$j]['voucher_id'] = $result[$i]['voucher_id'];
                            }
                        }
                    } else {
                        $bl_flag = false;

                        for($j=0; $j<count($item_details); $j++) {
                            if($item_details[$j]['particular']==$result[$i]['particular'] && 
                                $item_details[$j]['acc_id']==$result[$i]['acc_id'] && 
                                $item_details[$j]['ledger_name']==$result[$i]['ledger_name'] && 
                                $item_details[$j]['ledger_code']==$result[$i]['ledger_code']) {

                                $item_details[$j][$marketplace_id] += $result[$i]['amount'];
                                $bl_flag = true;
                            }
                        }

                        if($bl_flag == false){
                            $item_details[$a]['particular'] = $result[$i]['particular'];
                            if($result[$i]['particular']=='Taxable Amount'){
                                $item_details[$a]['acc_type'] = 'Goods Sales';
                            } else if($result[$i]['particular']=='CGST'){
                                $item_details[$a]['acc_type'] = 'CGST';
                            } else if($result[$i]['particular']=='SGST'){
                                $item_details[$a]['acc_type'] = 'SGST';
                            } else if($result[$i]['particular']=='IGST'){
                                $item_details[$a]['acc_type'] = 'IGST';
                            }
                            $item_details[$a]['acc_id'] = $result[$i]['acc_id'];
                            $item_details[$a]['ledger_name'] = $result[$i]['ledger_name'];
                            $item_details[$a]['ledger_code'] = $result[$i]['ledger_code'];
                            $item_details[$a]['tax_percent'] = $result[$i]['tax_percent'];
                            $item_details[$a]['ship_from_state'] = $result[$i]['ship_from_state'];
                            $item_details[$a]['ship_to_state'] = $result[$i]['ship_to_state'];
                            for($j=0; $j<count($invoice_marketplace); $j++) {
                                if($invoice_marketplace[$j]['marketplace_id']==$marketplace_id){
                                    $item_details[$a][$marketplace_id] = $result[$i]['amount'];
                                } else {
                                    $item_details[$a][$invoice_marketplace[$j]['marketplace_id']] = 0;
                                }
                            }
                            $a = $a + 1;
                        }
                    }
                }

                // $final_data[$k]['invoice_no'] = $invoice[$k]['invoice_no'];
                $final_data[$k]['marketplace'] = $invoice_marketplace;
                $final_data[$k]['item_details'] = $item_details;
            // }
        }
        
        return $final_data;
    }

    public function getSalesParticulars(){
        $request = Yii::$app->request;
        $mycomponent = Yii::$app->mycomponent;
        $session = Yii::$app->session;

        $company_id = $session['company_id'];

        $file_id = $request->post('file_id');
        $no_of_invoices = $request->post('no_of_invoices');
        $no_of_marketplaces = $request->post('no_of_marketplaces');
        $date_of_upload = $request->post('date_of_upload');
        if($date_of_upload==''){
            $date_of_upload=NULL;
        } else {
            $date_of_upload=$mycomponent->formatdate($date_of_upload);
        }

        $bulkInsertArray = array();
        $ledgerArray = array();

        $x = 0;

        for($k=0; $k<$no_of_invoices; $k++) {
            // $invoice_no=$request->post('invoice_no_'.$k);
            $invoice_no='';
            $particular=$request->post('particular_'.$k);
            $acc_id=$request->post('acc_id_'.$k);
            $ledger_name=$request->post('ledger_name_'.$k);
            $ledger_code=$request->post('ledger_code_'.$k);
            $tax_percent=$request->post('tax_percent_'.$k);
            $ship_from_state=$request->post('ship_from_state_'.$k);
            $ship_to_state=$request->post('ship_to_state_'.$k);

            $marketplace = array();

            for($j=0; $j<$no_of_marketplaces; $j++) {
                $marketplace[$j]['acc_id']=$request->post('marketplace_acc_id_'.$k.'_'.$j);
                $marketplace[$j]['acc_code']=$request->post('marketplace_acc_code_'.$k.'_'.$j);
                $marketplace[$j]['acc_legal_name']=$request->post('marketplace_acc_legal_name_'.$k.'_'.$j);
                $marketplace[$j]['voucher_id']=$request->post('marketplace_voucher_id_'.$k.'_'.$j);
                $marketplace[$j]['total_amount']=$request->post('total_amount_'.$k.'_'.$j);

                $marketplace[$j]['amount']=$request->post('amount_'.$k.'_'.$j);
            }

            for($j=0; $j<count($marketplace); $j++) {
                for($i=0; $i<count($particular); $i++) {
                    if($mycomponent->format_number($marketplace[$j]['amount'][$i],4)!=''){
                        $bulkInsertArray[$x]=[
                                            'file_id'=>$file_id,
                                            'particular'=>$particular[$i],
                                            'acc_id'=>($acc_id[$i]!='')?$acc_id[$i]:null,
                                            'ledger_name'=>$ledger_name[$i],
                                            'ledger_code'=>$ledger_code[$i],
                                            'voucher_id'=>$marketplace[$j]['voucher_id'],
                                            'ledger_type'=>'Sub Entry',
                                            'tax_percent'=>$mycomponent->format_number($tax_percent[$i],4),
                                            'invoice_no'=>$invoice_no,
                                            'amount'=>$mycomponent->format_number($marketplace[$j]['amount'][$i],4),
                                            'status'=>'approved',
                                            'is_active'=>'1',
                                            'updated_by'=>$session['session_id'],
                                            'updated_date'=>date('Y-m-d h:i:s'),
                                            'date_of_upload'=>$date_of_upload,
                                            'company_id'=>$company_id,
                                            'marketplace_id'=>$marketplace[$j]['acc_id'],
                                            'ship_from_state'=>$ship_from_state[$i],
                                            'ship_to_state'=>$ship_to_state[$i]
                                        ];

                        $amount = $mycomponent->format_number($marketplace[$j]['amount'][$i],4);
                        if($amount<0){
                            $amount = $amount * -1;
                            $type = 'Debit';
                        } else {
                            $type = 'Credit';
                        }
                        
                        $ledgerArray[$x]=[
                                        'ref_id'=>$file_id,
                                        'ref_type'=>'sales_upload',
                                        'entry_type'=>$particular[$i],
                                        'invoice_no'=>$invoice_no,
                                        'acc_id'=>($acc_id[$i]!='')?$acc_id[$i]:null,
                                        'ledger_name'=>$ledger_name[$i],
                                        'ledger_code'=>$ledger_code[$i],
                                        'voucher_id'=>$marketplace[$j]['voucher_id'],
                                        'ledger_type'=>'Sub Entry',
                                        'type'=>$type,
                                        'amount'=>$amount,
                                        'status'=>'approved',
                                        'is_active'=>'1',
                                        'updated_by'=>$session['session_id'],
                                        'updated_date'=>date('Y-m-d h:i:s'),
                                        'ref_date'=>$date_of_upload,
                                        'company_id'=>$company_id
                                    ];

                        $x = $x + 1;
                    }
                }

                if($mycomponent->format_number($marketplace[$j]['total_amount'],4)!=0){
                    $bulkInsertArray[$x]=[
                                        'file_id'=>$file_id,
                                        'particular'=>'Total Amount',
                                        'acc_id'=>($marketplace[$j]['acc_id']!='')?$marketplace[$j]['acc_id']:null,
                                        'ledger_name'=>$marketplace[$j]['acc_legal_name'],
                                        'ledger_code'=>$marketplace[$j]['acc_code'],
                                        'voucher_id'=>$marketplace[$j]['voucher_id'],
                                        'ledger_type'=>'Main Entry',
                                        'tax_percent'=>null,
                                        'invoice_no'=>$invoice_no,
                                        'amount'=>$mycomponent->format_number($marketplace[$j]['total_amount'],4),
                                        'status'=>'approved',
                                        'is_active'=>'1',
                                        'updated_by'=>$session['session_id'],
                                        'updated_date'=>date('Y-m-d h:i:s'),
                                        'date_of_upload'=>$date_of_upload,
                                        'company_id'=>$company_id,
                                        'marketplace_id'=>$marketplace[$j]['acc_id'],
                                        'ship_from_state'=>null,
                                        'ship_to_state'=>null
                                    ];

                    $total_amount = $mycomponent->format_number($marketplace[$j]['total_amount'],4);
                    if($total_amount<0){
                        $total_amount = $total_amount * -1;
                        $type = 'Credit';
                    } else {
                        $type = 'Debit';
                    }
                    
                    $ledgerArray[$x]=[
                                        'ref_id'=>$file_id,
                                        'ref_type'=>'sales_upload',
                                        'entry_type'=>'Total Amount',
                                        'invoice_no'=>$invoice_no,
                                        'acc_id'=>($marketplace[$j]['acc_id']!='')?$marketplace[$j]['acc_id']:null,
                                        'ledger_name'=>$marketplace[$j]['acc_legal_name'],
                                        'ledger_code'=>$marketplace[$j]['acc_code'],
                                        'voucher_id'=>$marketplace[$j]['voucher_id'],
                                        'ledger_type'=>'Main Entry',
                                        'type'=>$type,
                                        'amount'=>$total_amount,
                                        'status'=>'approved',
                                        'is_active'=>'1',
                                        'updated_by'=>$session['session_id'],
                                        'updated_date'=>date('Y-m-d h:i:s'),
                                        'ref_date'=>$date_of_upload,
                                        'company_id'=>$company_id
                                    ];

                    $x = $x + 1;
                }
            }
        }

        $data['bulkInsertArray'] = $bulkInsertArray;
        $data['ledgerArray'] = $ledgerArray;

        return $data;
    }

    public function getSalesAccLedgerEntries($id){
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select A.*, B.invoice_date from 
                (select A.date_of_upload, B.* from acc_sales_files A left join acc_ledger_entries B 
                on (A.id=B.ref_id and B.ref_type='sales_upload') 
                where A.id = '$id' and B.ref_id = '$id' and B.ref_type='sales_upload') A 
                left join 
                (select distinct ref_file_id, invoice_no, invoice_date from acc_sales_file_items where ref_file_id = '$id') B 
                on (A.ref_id = B.ref_file_id and A.invoice_no = B.invoice_no) 
                order by A.ref_id, A.invoice_no, A.voucher_id, A.id";

        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function freeze_file(){
        $request = Yii::$app->request;
        $session = Yii::$app->session;

        $curusr = $session['session_id'];
        $now = date('Y-m-d H:i:s');
        $id = $request->post('file_id');

        $array = array('freeze_file' => '1',
                        'updated_by' => $curusr,
                        'updated_date' => $now
                        );
        $tableName = "acc_sales_files";
        $count = Yii::$app->db->createCommand()
                            ->update($tableName, $array, "id = '".$id."'")
                            ->execute();
        $this->setLog('SalesUpload', '', 'Freeze', '', 'Sales File Freezed.', 'acc_sales_files', $id);

        return true;
    }

    public function check_hsn(){
        // $request = Yii::$app->request;
        $session = Yii::$app->session;

        $curusr = $session['session_id'];
        $now = date('Y-m-d H:i:s');
        // $id = $request->post('file_id');
        $id = '';

        $cond = "";
        if($id!=''){
            $cond = " and id = '$id'";
        }

        $sql = "update acc_sales_file_items A, product_master B set A.hsn_code = B.hsn_code, 
                A.updated_by = '$curusr', A.updated_date='$now' 
                where A.ref_file_id in (select distinct id from acc_sales_files where status = 'approved' and 
                    is_active = '1' and (freeze_file = '0' or freeze_file is null)".$cond.") and 
                    A.sku = B.sku_internal_code and A.company_id = B.company_id and 
                    A.is_active = '1' and B.is_active = '1' and B.is_latest = '1'";
        // return $sql;
        $command = Yii::$app->db->createCommand($sql);
        $count = $command->execute();
        return true;
    }

    public function setLog($module_name, $sub_module, $action, $vendor_id, $description, $table_name, $table_id) {
        $session = Yii::$app->session;
        $curusr = $session['session_id'];
        $now = date('Y-m-d H:i:s');
        $company_id = $session['company_id'];

        $array = array('module_name' => $module_name, 
                        'sub_module' => $sub_module, 
                        'action' => $action, 
                        'vendor_id' => $vendor_id, 
                        'user_id' => $curusr, 
                        'description' => $description, 
                        'log_activity_date' => $now, 
                        'table_name' => $table_name, 
                        'table_id' => $table_id, 
                        'company_id' => $company_id);
        $count = Yii::$app->db->createCommand()
                            ->insert("acc_user_log", $array)
                            ->execute();

        return true;
    }
}