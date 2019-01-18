<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
                (select distinct ref_id from acc_ledger_entries) B 
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
        $pattern = '/^(0*[0-9][0-9.,]*)$/';
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

    public function actionDownlaod_ledger() {
        $reject_spreadsheet = new Spreadsheet();
        $reject_sheet = $reject_spreadsheet->getActiveSheet();
        $reject_sheet->setCellValue('A1', 'Market place');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="01simple.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $writer = new Xlsx($reject_spreadsheet);
        // $writer->save($file_name);

        $writer->save('php://output');

        // $objWriter = new Xlsx($highlight_spreadsheet);
        // // $writer->save($file_name);

        // // $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        // $objWriter->save('php://output');
        // Yii::app()->end();
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

        for($i=0; $i<count($data); $i++) {
            $ref_file_id = $data[$i]['id'];
            $fileName = $data[$i]['original_file'];
            $objPHPExcel = \moonland\phpexcel\Excel::import($fileName);
            $reject_file = false;
            $highlight_file = false;

            $r_row = 1;
            $reject_spreadsheet = new Spreadsheet();
            $reject_sheet = $reject_spreadsheet->getActiveSheet();
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

            $h_row = 1;
            $highlight_spreadsheet = new Spreadsheet();
            $highlight_sheet = $highlight_spreadsheet->getActiveSheet();
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

            for($j=0; $j<count($objPHPExcel); $j++) {
                $bl_reject = false;
                $bl_highlight = false;
                $bl_pincode = false;
                $ship_to_state[$j] = '';
                $remarks[$j] = '';
                $highlight_remarks[$j] = '';

                $marketplace_id[$j] = '';
                $market_place[$j] = $objPHPExcel[$j]['Market place'];
                $ship_from_gstin[$j] = $objPHPExcel[$j]['Ship from GSTin'];
                $ship_from_state[$j] = $objPHPExcel[$j]['Ship from State'];
                $ship_to_gstin[$j] = $objPHPExcel[$j]['Ship to GSTin'];
                $amazon_state[$j] = $objPHPExcel[$j]['Amazon state'];
                $pin_code[$j] = $objPHPExcel[$j]['Pin code'];
                $invoice_no[$j] = $objPHPExcel[$j]['Invoice no'];
                $invoice_date[$j] = $objPHPExcel[$j]['Invoice date'];
                $customer_name[$j] = $objPHPExcel[$j]['Customer name'];
                $sku[$j] = $objPHPExcel[$j]['SKU'];
                $item_desc[$j] = $objPHPExcel[$j]['Item description'];
                $hsn_code[$j] = $objPHPExcel[$j]['HSN Code'];
                $quantity[$j] = $objPHPExcel[$j]['Quantity'];
                $rate[$j] = $objPHPExcel[$j]['Rate'];
                $sales_incl_gst[$j] = $objPHPExcel[$j]['Sales incl GST'];
                $sales_excl_gst[$j] = $objPHPExcel[$j]['Sales excl GST'];
                $total_gst[$j] = $objPHPExcel[$j]['Total GST'];
                $igst_rate[$j] = $objPHPExcel[$j]['IGST Rate'];
                $igst_amount[$j] = $objPHPExcel[$j]['IGST Amount'];
                $cgst_rate[$j] = $objPHPExcel[$j]['CGST Rate'];
                $cgst_amount[$j] = $objPHPExcel[$j]['CGST Amount'];
                $sgst_rate[$j] = $objPHPExcel[$j]['SGST Rate'];
                $sgst_amount[$j] = $objPHPExcel[$j]['SGST Amount'];
                $flag[$j] = $objPHPExcel[$j]['Flag'];

                $r_row += 1;
                $reject_sheet->setCellValue('A'.$r_row, $objPHPExcel[$j]['Market place']);
                $reject_sheet->setCellValue('B'.$r_row, $objPHPExcel[$j]['Ship from GSTin']);
                $reject_sheet->setCellValue('C'.$r_row, $objPHPExcel[$j]['Ship from State']);
                $reject_sheet->setCellValue('D'.$r_row, $objPHPExcel[$j]['Ship to GSTin']);
                $reject_sheet->setCellValue('E'.$r_row, $objPHPExcel[$j]['Amazon state']);
                $reject_sheet->setCellValue('F'.$r_row, $objPHPExcel[$j]['Pin code']);
                $reject_sheet->setCellValue('G'.$r_row, $objPHPExcel[$j]['Invoice no']);
                $reject_sheet->setCellValue('H'.$r_row, $objPHPExcel[$j]['Invoice date']);
                $reject_sheet->setCellValue('I'.$r_row, $objPHPExcel[$j]['Customer name']);
                $reject_sheet->setCellValue('J'.$r_row, $objPHPExcel[$j]['SKU']);
                $reject_sheet->setCellValue('K'.$r_row, $objPHPExcel[$j]['Item description']);
                $reject_sheet->setCellValue('L'.$r_row, $objPHPExcel[$j]['HSN Code']);
                $reject_sheet->setCellValue('M'.$r_row, $objPHPExcel[$j]['Quantity']);
                $reject_sheet->setCellValue('N'.$r_row, $objPHPExcel[$j]['Rate']);
                $reject_sheet->setCellValue('O'.$r_row, $objPHPExcel[$j]['Sales incl GST']);
                $reject_sheet->setCellValue('P'.$r_row, $objPHPExcel[$j]['Sales excl GST']);
                $reject_sheet->setCellValue('Q'.$r_row, $objPHPExcel[$j]['Total GST']);
                $reject_sheet->setCellValue('R'.$r_row, $objPHPExcel[$j]['IGST Rate']);
                $reject_sheet->setCellValue('S'.$r_row, $objPHPExcel[$j]['IGST Amount']);
                $reject_sheet->setCellValue('T'.$r_row, $objPHPExcel[$j]['CGST Rate']);
                $reject_sheet->setCellValue('U'.$r_row, $objPHPExcel[$j]['CGST Amount']);
                $reject_sheet->setCellValue('V'.$r_row, $objPHPExcel[$j]['SGST Rate']);
                $reject_sheet->setCellValue('W'.$r_row, $objPHPExcel[$j]['SGST Amount']);
                $reject_sheet->setCellValue('X'.$r_row, $objPHPExcel[$j]['Flag']);

                $h_row += 1;
                $highlight_sheet->setCellValue('A'.$h_row, $objPHPExcel[$j]['Market place']);
                $highlight_sheet->setCellValue('B'.$h_row, $objPHPExcel[$j]['Ship from GSTin']);
                $highlight_sheet->setCellValue('C'.$h_row, $objPHPExcel[$j]['Ship from State']);
                $highlight_sheet->setCellValue('D'.$h_row, $objPHPExcel[$j]['Ship to GSTin']);
                $highlight_sheet->setCellValue('E'.$h_row, $objPHPExcel[$j]['Amazon state']);
                $highlight_sheet->setCellValue('F'.$h_row, $objPHPExcel[$j]['Pin code']);
                $highlight_sheet->setCellValue('G'.$h_row, $objPHPExcel[$j]['Invoice no']);
                $highlight_sheet->setCellValue('H'.$h_row, $objPHPExcel[$j]['Invoice date']);
                $highlight_sheet->setCellValue('I'.$h_row, $objPHPExcel[$j]['Customer name']);
                $highlight_sheet->setCellValue('J'.$h_row, $objPHPExcel[$j]['SKU']);
                $highlight_sheet->setCellValue('K'.$h_row, $objPHPExcel[$j]['Item description']);
                $highlight_sheet->setCellValue('L'.$h_row, $objPHPExcel[$j]['HSN Code']);
                $highlight_sheet->setCellValue('M'.$h_row, $objPHPExcel[$j]['Quantity']);
                $highlight_sheet->setCellValue('N'.$h_row, $objPHPExcel[$j]['Rate']);
                $highlight_sheet->setCellValue('O'.$h_row, $objPHPExcel[$j]['Sales incl GST']);
                $highlight_sheet->setCellValue('P'.$h_row, $objPHPExcel[$j]['Sales excl GST']);
                $highlight_sheet->setCellValue('Q'.$h_row, $objPHPExcel[$j]['Total GST']);
                $highlight_sheet->setCellValue('R'.$h_row, $objPHPExcel[$j]['IGST Rate']);
                $highlight_sheet->setCellValue('S'.$h_row, $objPHPExcel[$j]['IGST Amount']);
                $highlight_sheet->setCellValue('T'.$h_row, $objPHPExcel[$j]['CGST Rate']);
                $highlight_sheet->setCellValue('U'.$h_row, $objPHPExcel[$j]['CGST Amount']);
                $highlight_sheet->setCellValue('V'.$h_row, $objPHPExcel[$j]['SGST Rate']);
                $highlight_sheet->setCellValue('W'.$h_row, $objPHPExcel[$j]['SGST Amount']);
                $highlight_sheet->setCellValue('X'.$h_row, $objPHPExcel[$j]['Flag']);

                $sql = "select * from internal_warehouse_master where is_active = '1' and company_id = '$company_id' 
                        and gst_id = '".$ship_from_gstin[$j]."'";
                $command = Yii::$app->db->createCommand($sql);
                $reader = $command->query();
                $data2 = $reader->readAll();
                if(count($data2)==0){
                    $bl_reject = true;
                    $remarks[$j] = $remarks[$j] . "Ship from GSTin not found in warehouse master. ";
                }

                $sql = "select * from acc_master where legal_name = '".$market_place[$j]."' and type = 'Marketplace'";
                $command = Yii::$app->db->createCommand($sql);
                $reader = $command->query();
                $data2 = $reader->readAll();
                if(count($data2)==0){
                    $bl_reject = true;
                    $remarks[$j] = $remarks[$j] . "Marketplace not found. ";
                } else {
                    $marketplace_id[$j] = $data2[0]['id'];
                }

                $sql = "select * from pincode_master where pincode = '".$pin_code[$j]."'";
                $command = Yii::$app->db->createCommand($sql);
                $reader = $command->query();
                $data2 = $reader->readAll();
                if(count($data2)>0){
                    $ship_to_state[$j] = $data2[0]['state_name'];
                }

                if($ship_to_state[$j]==''){
                    $sql = "select * from acc_amazon_state_master where is_active = '1' and company_id = '$company_id' 
                            and erp_state = '".$ship_to_state[$j]."'";
                    $command = Yii::$app->db->createCommand($sql);
                    $reader = $command->query();
                    $data2 = $reader->readAll();
                    if(count($data2)>0){
                        $ship_to_state[$j] = $data2[0]['amazon_state'];
                        if($ship_to_state[$j]!='' && $pin_code[$j]!=''){
                            $this->insert_pincode($ship_to_state[$j], $pin_code[$j]);
                        }
                    }
                }

                if($ship_to_state[$j]==''){
                    $bl_reject = true;
                    $remarks[$j] = $remarks[$j] . "Pincode not found. ";
                }

                if($ship_to_gstin[$j]==''){
                    $sales_type[$j] = 'B2C';
                } else {
                    $sales_type[$j] = 'B2B';

                    $highlight_remarks[$j] = $this->check_gst_no_format($ship_to_gstin[$j], $ship_to_state[$j]);
                    if($highlight_remarks[$j]!=''){
                        $bl_highlight = true;
                    }
                }

                $new_hsn_code[$j] = '';
                if($sku[$j]!=''){
                    $sql = "select * from product_master where is_active = '1' and company_id = '$company_id' 
                            and sku_internal_code = '".$sku[$j]."'";
                    $command = Yii::$app->db->createCommand($sql);
                    $reader = $command->query();
                    $data2 = $reader->readAll();
                    if(count($data2)>0){
                        $new_hsn_code[$j] = $data2[0]['hsn_code'];
                    }
                }

                if($hsn_code[$j] == '' || $hsn_code[$j]==null){
                    $bl_highlight = true;
                    $highlight_remarks[$j] = $highlight_remarks[$j] . "HSN Code is empty. ";
                } else if($new_hsn_code[$j] == '' || $new_hsn_code[$j]==null){
                    $bl_highlight = true;
                    $highlight_remarks[$j] = $highlight_remarks[$j] . "HSN Code not found in Product Master. ";
                } else if($new_hsn_code[$j] != $hsn_code[$j]){
                    $bl_highlight = true;
                    $highlight_remarks[$j] = $highlight_remarks[$j] . "HSN Code is different. ";
                }

                if($rate[$j]!=''){
                    if($this->check_no($rate[$j])==false){
                        $bl_reject = true;
                        $remarks[$j] = $remarks[$j] . "Rate is not number. ";
                    }
                }
                if($quantity[$j]!=''){
                    if($this->check_no($quantity[$j])==false){
                        $bl_reject = true;
                        $remarks[$j] = $remarks[$j] . "Quantity is not number. ";
                    }
                }
                if($sales_incl_gst[$j]!=''){
                    if($this->check_no($sales_incl_gst[$j])==false){
                        $bl_reject = true;
                        $remarks[$j] = $remarks[$j] . "Sales Incl GST is not number. ";
                    }
                }
                if($sales_excl_gst[$j]!=''){
                    if($this->check_no($sales_excl_gst[$j])==false){
                        $bl_reject = true;
                        $remarks[$j] = $remarks[$j] . "Sales Excl GST is not number. ";
                    }
                }
                if($total_gst[$j]!=''){
                    if($this->check_no($total_gst[$j])==false){
                        $bl_reject = true;
                        $remarks[$j] = $remarks[$j] . "Total GST is not number. ";
                    }
                }
                if($igst_rate[$j]!=''){
                    if($this->check_no($igst_rate[$j])==false){
                        $bl_reject = true;
                        $remarks[$j] = $remarks[$j] . "IGST Rate is not number. ";
                    }
                }
                if($igst_amount[$j]!=''){
                    if($this->check_no($igst_amount[$j])==false){
                        $bl_reject = true;
                        $remarks[$j] = $remarks[$j] . "IGST Amount is not number. ";
                    }
                }
                if($cgst_rate[$j]!=''){
                    if($this->check_no($cgst_rate[$j])==false){
                        $bl_reject = true;
                        $remarks[$j] = $remarks[$j] . "CGST Rate is not number. ";
                    }
                }
                if($cgst_amount[$j]!=''){
                    if($this->check_no($cgst_amount[$j])==false){
                        $bl_reject = true;
                        $remarks[$j] = $remarks[$j] . "CGST Amount is not number. ";
                    }
                }
                if($sgst_rate[$j]!=''){
                    if($this->check_no($sgst_rate[$j])==false){
                        $bl_reject = true;
                        $remarks[$j] = $remarks[$j] . "SGST Rate is not number. ";
                    }
                }
                if($sgst_amount[$j]!=''){
                    if($this->check_no($sgst_amount[$j])==false){
                        $bl_reject = true;
                        $remarks[$j] = $remarks[$j] . "SGST Amount is not number. ";
                    }
                }

                if($flag[$j]!='0' && $flag[$j]!='1'){
                    $bl_reject = true;
                    $remarks[$j] = $remarks[$j] . "Flag should be 0 or 1. ";
                }

                if($bl_reject==true) {
                    // echo 'rejected';
                    // echo '<br/>';
                    // echo $remarks[$j];
                    // echo '<br/>';
                    $reject_sheet->setCellValue('Y'.$r_row, $remarks[$j]);
                    $reject_file = true;
                }
                if($bl_highlight==true) {
                    // echo $highlight_remarks[$j].'<br/>';
                    $highlight_sheet->setCellValue('Y'.$h_row, $highlight_remarks[$j]);
                    $highlight_file = true;
                }
            }

            if($reject_file==false) {
                for($j=0; $j<count($market_place); $j++) {
                    if($invoice_date[$j]==''){
                        $invoice_date[$j]=NULL;
                    } else {
                        $invoice_date[$j]=$mycomponent->formatdate($invoice_date[$j]);
                    }

                    $array = array('ref_file_id' => $ref_file_id, 
                                    'market_place' => $market_place[$j], 
                                    'marketplace_id' => $marketplace_id[$j], 
                                    'ship_from_gstin' => $ship_from_gstin[$j], 
                                    'ship_from_state' => $ship_from_state[$j], 
                                    'ship_to_gstin' => $ship_to_gstin[$j], 
                                    'ship_to_state' => $ship_to_state[$j], 
                                    'amazon_state' => $amazon_state[$j], 
                                    'pin_code' => $pin_code[$j], 
                                    'invoice_no' => $invoice_no[$j], 
                                    'invoice_date' => $invoice_date[$j], 
                                    'customer_name' => $customer_name[$j], 
                                    'sku' => $sku[$j], 
                                    'item_desc' => $item_desc[$j], 
                                    'hsn_code' => $hsn_code[$j], 
                                    'quantity' => $mycomponent->format_number($quantity[$j],2), 
                                    'rate' => $mycomponent->format_number($rate[$j],2),
                                    'sales_incl_gst' => $mycomponent->format_number($sales_incl_gst[$j],2),
                                    'sales_excl_gst' => $mycomponent->format_number($sales_excl_gst[$j],2),
                                    'total_gst' => $mycomponent->format_number($total_gst[$j],2),
                                    'igst_rate' => $mycomponent->format_number($igst_rate[$j],2),
                                    'igst_amount' => $mycomponent->format_number($igst_amount[$j],2),
                                    'cgst_rate' => $mycomponent->format_number($cgst_rate[$j],2),
                                    'cgst_amount' => $mycomponent->format_number($cgst_amount[$j],2),
                                    'sgst_rate' => $mycomponent->format_number($sgst_rate[$j],2),
                                    'sgst_amount' => $mycomponent->format_number($sgst_amount[$j],2),
                                    'flag' => $flag[$j], 
                                    'status' => 'pending',
                                    'is_active' => '1',
                                    'updated_by'=>$curusr,
                                    'updated_date'=>$now,
                                    'approver_comments'=>$remarks[$j],
                                    'company_id'=>$company_id
                                );

                    $array['created_by'] = $curusr;
                    $array['created_date'] = $now;
                    $count = Yii::$app->db->createCommand()
                                ->insert("acc_sales_file_items", $array)
                                ->execute();

                    // echo json_encode($array);
                    // echo '<br/><br/>';
                }

                $sql = "update acc_sales_files set upload_status = 'uploaded' where id = '$ref_file_id'";
                $command = Yii::$app->db->createCommand($sql);
                $count = $command->execute();
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

                $writer = new Xlsx($reject_spreadsheet);
                $writer->save($file_name);

                $sql = "update acc_sales_files set error_rejected_file = '$file_path', upload_status = 'rejected', 
                        updated_by = '$curusr', updated_date = '$now' 
                        where id = '$ref_file_id'";
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

                $writer = new Xlsx($highlight_spreadsheet);
                $writer->save($file_name);

                $sql = "update acc_sales_files set error_highlighted_file = '$file_path' 
                        where id = '$ref_file_id'";
                $command = Yii::$app->db->createCommand($sql);
                $count = $command->execute();
            }
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
        $invoice = $this->getFileInvoices($id);

        $bl_posted = false;
        $sql = "select * from acc_sales_entries where file_id = '$id'";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        $result = $reader->readAll();
        if(count($result)>0){
            $bl_posted = true;
        }

        if($bl_posted==false) {
            for($k=0; $k<count($invoice); $k++) {
                $invoice_no = $invoice[$k]['invoice_no'];
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
                
                $sql = "select * from acc_sales_file_items where ref_file_id = '$id' and invoice_no = '$invoice_no' 
                        order by invoice_no, cgst_rate, sgst_rate, igst_rate, marketplace_id";
                $command = Yii::$app->db->createCommand($sql);
                $reader = $command->query();
                $result = $reader->readAll();
                for($i=0; $i<count($result); $i++) {
                    if($result[$i]['ship_to_state']==null || $result[$i]['ship_to_state']=='') {
                        $trans_type = 'B2C';
                    } else {
                        $trans_type = 'B2B';
                    }
                    if(strtoupper(trim($result[$i]['ship_from_state']))==strtoupper(trim($result[$i]['ship_to_state']))) {
                        $tax_type = 'Local';
                    } else {
                        $tax_type = 'Inter State';
                    }

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
                    $state_name = $result[$i]['ship_to_state'];

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
                    $tax_code = 'Sales-'.$state_name.'-'.$tax_type.'-'.$trans_type.'-'.$vat_percen;
                    $result2 = $this->getAccountDetails('','',$tax_code);
                    if(count($result2)>0) {
                        $acc_id = $result2[0]['id'];
                        $ledger_name = $result2[0]['legal_name'];
                        $ledger_code = $result2[0]['code'];
                    }
                    $bl_flag = false;
                    for($j=0; $j<count($item_details); $j++) {
                        if($item_details[$j]['acc_id']==$acc_id) {
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
                        $tax_code = 'Output-'.$state_name.'-CGST-'.$cgst_rate;
                        $result2 = $this->getAccountDetails('','',$tax_code);
                        if(count($result2)>0) {
                            $acc_id = $result2[0]['id'];
                            $ledger_name = $result2[0]['legal_name'];
                            $ledger_code = $result2[0]['code'];
                        }
                        $bl_flag = false;
                        for($j=0; $j<count($item_details); $j++) {
                            if($item_details[$j]['acc_id']==$acc_id) {
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
                        $tax_code = 'Output-'.$state_name.'-SGST-'.$sgst_rate;
                        $result2 = $this->getAccountDetails('','',$tax_code);
                        if(count($result2)>0) {
                            $acc_id = $result2[0]['id'];
                            $ledger_name = $result2[0]['legal_name'];
                            $ledger_code = $result2[0]['code'];
                        }
                        $bl_flag = false;
                        for($j=0; $j<count($item_details); $j++) {
                            if($item_details[$j]['acc_id']==$acc_id) {
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
                        $tax_code = 'Output-'.$state_name.'-IGST-'.$igst_rate;
                        $result2 = $this->getAccountDetails('','',$tax_code);
                        if(count($result2)>0) {
                            $acc_id = $result2[0]['id'];
                            $ledger_name = $result2[0]['legal_name'];
                            $ledger_code = $result2[0]['code'];
                        }
                        $bl_flag = false;
                        for($j=0; $j<count($item_details); $j++) {
                            if($item_details[$j]['acc_id']==$acc_id) {
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

                for($j=0; $j<count($invoice_marketplace); $j++) {
                    if($invoice_marketplace[$j]['sales_incl_gst']>0){
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

                $final_data[$k]['invoice_no'] = $invoice[$k]['invoice_no'];
                $final_data[$k]['marketplace'] = $invoice_marketplace;
                $final_data[$k]['item_details'] = $item_details;
            }
        } else {
            for($k=0; $k<count($invoice); $k++) {
                $invoice_no = $invoice[$k]['invoice_no'];
                $invoice_marketplace = $marketplace;
                $item_details = array();
                $a = 0;

                $sql = "select * from acc_sales_entries where file_id = '$id' and invoice_no = '$invoice_no' 
                        order by id";
                $command = Yii::$app->db->createCommand($sql);
                $reader = $command->query();
                $result = $reader->readAll();
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
                            if($item_details[$j]['particular']==$result[$i]['particular'] || 
                                $item_details[$j]['acc_id']==$result[$i]['acc_id'] || 
                                $item_details[$j]['ledger_name']==$result[$i]['ledger_name'] || 
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
                            for($j=0; $j<count($invoice_marketplace); $j++) {
                                if($invoice_marketplace[$j]['marketplace_id']==$marketplace_id){
                                    $item_details[$a][$marketplace_id] = $result[$i]['amount'];
                                } else {
                                    $item_details[$a][$invoice_marketplace[$j]['marketplace_id']] = 0;
                                }
                            }
                            $a += 1;
                        }
                    }
                }

                $final_data[$k]['invoice_no'] = $invoice[$k]['invoice_no'];
                $final_data[$k]['marketplace'] = $invoice_marketplace;
                $final_data[$k]['item_details'] = $item_details;
            }
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
            $invoice_no=$request->post('invoice_no_'.$k);
            $particular=$request->post('particular_'.$k);
            $acc_id=$request->post('acc_id_'.$k);
            $ledger_name=$request->post('ledger_name_'.$k);
            $ledger_code=$request->post('ledger_code_'.$k);
            $tax_percent=$request->post('tax_percent_'.$k);

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
                                            'marketplace_id'=>$marketplace[$j]['acc_id']
                                        ];

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
                                        'type'=>'Credit',
                                        'amount'=>$mycomponent->format_number($marketplace[$j]['amount'][$i],4),
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
                                        'marketplace_id'=>$marketplace[$j]['acc_id']
                                    ];

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
                                        'type'=>'Debit',
                                        'amount'=>$mycomponent->format_number($marketplace[$j]['total_amount'],4),
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