<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class AccountMaster extends Model
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

    public function getApprover($action){
        $session = Yii::$app->session;
        $session_id = $session['session_id'];

        $cond = "";
        if($action!="authorise" && $action!="view"){
            $cond = " and A.id!='".$session_id."'";
        } 

        $sql = "select distinct A.id, A.username, C.r_approval from user A 
                left join acc_user_roles B on (A.id = B.user_id) 
                left join acc_user_role_options C on (B.role_id = C.role_id) 
                where C.r_section = 'S_Account_Master' and 
                        C.r_approval = '1' and C.r_approval is not null" . $cond;
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getAccountDetails($id="", $status=""){
        $cond = "";
        $cond2 = "";
        if($id!=""){
            $cond = " and A.id = '$id'";
            $cond2 = " and acc_id = '$id'";
        }

        if($status!=""){
            $cond = $cond . " and A.status = '$status'";
        }
        
        $sql = "select A.*, concat_ws(',', A.category_1, A.category_2, A.category_3) as acc_category, B.bus_category from 
                (select A.*, B.username as updater, C.username as approver from 
                    acc_master A left join user B on (A.updated_by = B.id) left join user C on (A.approved_by = C.id) 
                    where A.is_active = '1'" . $cond . ") A 
                left join 
                (select acc_id, GROUP_CONCAT(category_name) as bus_category from acc_categories where is_active = '1'" . $cond2 . " 
                    group by acc_id) B 
                on (A.id = B.acc_id) order by UNIX_TIMESTAMP(A.updated_date) desc, A.id desc";

        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getVendors($vendor_id=""){
        $sql = "select * from vendor_master where is_active = '1' and company_id = '1' and 
                id not in (select distinct vendor_id from acc_master where vendor_id != '$vendor_id') order by vendor_name";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getVendorDetails(){
        $request = Yii::$app->request;
        $vendor_id = $request->post('vendor_id');

        $sql = "select A.*, B.* from 
                (select AA.*, BB.legal_entity_name from vendor_master AA left join legal_entity_type_master BB 
                    on (AA.legal_entity_type_id = BB.id) where AA.id = '$vendor_id' and BB.is_active = '1') A 
                left join 
                (select AA.vendor_id, AA.office_address_line_1, AA.office_address_line_2, AA.office_address_line_3, 
                        AA.pincode, BB.city_name, CC.state_name, DD.country_name from 
                vendor_office_address AA left join city_master BB on (AA.city_id = BB.id) left join 
                state_master CC on (AA.state_id = CC.id) left join country_master DD on (AA.country_id = DD.id) 
                where AA.vendor_id = '$vendor_id' and AA.is_active = '1' and BB.is_active = '1' 
                        and CC.is_active = '1' and DD.is_active = '1') B 
                on (A.id = B.vendor_id)";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        $data['vendor_details'] = $reader->readAll();

        $sql = "select distinct id, category_name from product_main_category 
                    where company_id = '1' and is_active = '1'";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        $data['category_details'] = $reader->readAll();

        return $data;
    }

    public function getTaxDetails(){
        $sql = "select C.*, D.tax_type_name from 
                (select A.*, B.tax_zone_name from 
                (select * from tax_rate_master where is_active = '1') A 
                left join 
                (select * from tax_zone_master where is_active = '1') B 
                on (A.tax_zone_id = B.id)) C 
                left join 
                (select * from tax_type_master where is_active = '1' order by id desc) D 
                on (C.tax_type_id = D.id)
                order by C.id desc";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getAccCategories($id){
        $sql = "select * from acc_categories where acc_id='$id' and is_active='1' order by id";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getBusinessCategories(){
        $sql = "select distinct id, category_name from product_main_category 
                    where company_id = '1' and is_active = '1'";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getCode(){
        $request = Yii::$app->request;
        $type = $request->post('type');

        if($type=="Vendor Expenses"){
            $code = "VE";
        } else if($type=="Bank Account"){
            $code = "BK";
        } else if($type=="Goods Purchase"){
            $code = "PU";
        } else if($type=="Tax"){
            $code = "TX";
        } else if($type=="CGST"){
            $code = "CGST";
        } else if($type=="SGST"){
            $code = "SGST";
        } else if($type=="IGST"){
            $code = "IGST";
        } else if($type=="Goods Sales"){
            $code = "GS";
        } else if($type=="Employee"){
            $code = "EM";
        } else {
            $code = "OT";
        }

        $sql = "select * from acc_series_master where type = '$code'";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        $data = $reader->readAll();

        if (count($data)>0){
            $series = intval($data[0]['series']) + 1;
        } else {
            $series = 1;
        }

        $code = $code . str_pad($series, 4, "0", STR_PAD_LEFT);

        return $code;
    }

    public function setCode($type){
        if($type=="Vendor Expenses"){
            $code = "VE";
        } else if($type=="Bank Account"){
            $code = "BK";
        } else if($type=="Goods Purchase"){
            $code = "PU";
        } else if($type=="Tax"){
            $code = "TX";
        } else if($type=="CGST"){
            $code = "CGST";
        } else if($type=="SGST"){
            $code = "SGST";
        } else if($type=="IGST"){
            $code = "IGST";
        } else if($type=="Goods Sales"){
            $code = "GS";
        } else if($type=="Employee"){
            $code = "EM";
        } else {
            $code = "OT";
        }

        $sql = "select * from acc_series_master where type = '$code'";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        $data = $reader->readAll();

        if (count($data)>0){
            $series = intval($data[0]['series']) + 1;

            $sql = "update acc_series_master set series = '$series' where type = '$code'";
            $command = Yii::$app->db->createCommand($sql);
            $count = $command->execute();
        } else {
            $series = 1;

            $sql = "insert into acc_series_master (type, series) values ('".$code."', '".$series."')";
            $command = Yii::$app->db->createCommand($sql);
            $count = $command->execute();
        }

        $code = $code . str_pad($series, 4, "0", STR_PAD_LEFT);

        return $code;
    }

    public function save(){
        $request = Yii::$app->request;

        $action = $request->post('action');
        if($action=="authorise"){
            if($request->post('btn_reject')!==null){
                $action = "reject";
            } else {
                $action = "approve";
            }
        }

        if($action=="edit" || $action=="insert"){
            $this->saveEdit();
        } else if($action=="approve"){
            $this->authorise("approved");
        } else if($action=="reject"){
            $this->authorise("rejected");
        }
        
        return true;
    }

    public function saveEdit(){
        $request = Yii::$app->request;
        $session = Yii::$app->session;

        $curusr = $session['session_id'];
        $now = date('Y-m-d H:i:s');

        $id = $request->post('id');
        $type = $request->post('type_val');
        $description = $request->post('description');
        $legal_name = $request->post('legal_name');
        $code = $request->post('code');
        $account_type = $request->post('account_type');
        $details = $request->post('details');
        $category_1 = $request->post('ac_category_1');
        $category_2 = $request->post('ac_category_2');
        $category_3 = $request->post('ac_category_3');
        $department = $request->post('department');
        $remarks = $request->post('remarks');
        $approver_id = $request->post('approver_id');

        $vendor_id = "";
        $pan_no = "";
        $address = "";
        $legal_entity_name = "";
        $vat_no = "";
        $bus_category = array();
        $bus_category_name = array();
        $expense_type = "";
        $location = "";
        $aadhar_card_no = "";
        $service_tax_no = "";
        $agreement_details = "";
        $pf_esic_no = "";
        $other = "";
        $gst_id = "";

        if($type=="Vendor Goods"){
            $vendor_id = $request->post('vendor_id');
            $code = $request->post('vendor_code');
            $pan_no = $request->post('pan_no');
            $address = $request->post('address');
            $legal_entity_name = $request->post('legal_entity_name');
            $vat_no = $request->post('vat_no');
            $bus_category = $request->post('bus_category');
            $bus_category_name = $request->post('bus_category_name');
            $gst_id = $request->post('gst_id');
        } else if($type=="Vendor Expenses"){
            $expense_type = $request->post('expense_type');
            $location = $request->post('location');
            $address = $request->post('address');
            $pan_no = $request->post('pan_no');
            $aadhar_card_no = $request->post('aadhar_card_no');
            $service_tax_no = $request->post('service_tax_no');
            $agreement_details = $request->post('agreement_details');
            $vat_no = $request->post('vat_no');
            $pf_esic_no = $request->post('pf_esic_no');
            $other = $request->post('other');
        } else if($type=="Employee"){
            $expense_type = $request->post('expense_type');
            $location = $request->post('location');
            $address = $request->post('address');
            $pan_no = $request->post('pan_no');
            $aadhar_card_no = $request->post('aadhar_card_no');
            $other = $request->post('other');
        }

        if($type=="Vendor Goods" || $type=="Vendor Expenses" || $type=="Bank Account" || $type=="Employee"){
            $account_holder_name = $request->post('account_holder_name');
            $bank_name = $request->post('bank_name');
            $branch = $request->post('branch');
            $acc_no = $request->post('acc_no');
            $ifsc_code = $request->post('ifsc_code');
        } else {
            $account_holder_name = "";
            $bank_name = "";
            $branch = "";
            $acc_no = "";
            $ifsc_code = "";
        }

        if (!isset($id) || $id==""){
            if ($type!="Vendor Goods"){
                $code = $this->setCode($type);
            }
        }

        $array = array('type' => $type, 
                        'vendor_id' => $vendor_id, 
                        'legal_name' => ucwords(strtolower($legal_name)), 
                        'department' => $department,
                        'code' => $code, 
                        'description' => $description, 
                        'account_type' => $account_type, 
                        'details' => $details,
                        'category_1' => $category_1,
                        'category_2' => $category_2,
                        'category_3' => $category_3,
                        'pan_no' => $pan_no,
                        'address' => $address,
                        'legal_entity_name' => $legal_entity_name,
                        'vat_no' => $vat_no,
                        'expense_type' => $expense_type,
                        'location' => $location,
                        'aadhar_card_no' => $aadhar_card_no,
                        'service_tax_no' => $service_tax_no,
                        'agreement_details' => $agreement_details,
                        'pf_esic_no' => $pf_esic_no,
                        'other' => $other,
                        'account_holder_name' => $account_holder_name,
                        'bank_name' => $bank_name,
                        'branch' => $branch,
                        'acc_no' => $acc_no,
                        'ifsc_code' => $ifsc_code,
                        'status' => 'pending',
                        'is_active' => '1',
                        'updated_by'=>$curusr,
                        'updated_date'=>$now,
                        'approver_comments'=>$remarks,
                        'approver_id'=>$approver_id,
                        'gst_id'=>$gst_id
                        );

        if(count($array)>0){
            $tableName = "acc_master";

            if (isset($id) && $id!=""){
                $count = Yii::$app->db->createCommand()
                            ->update($tableName, $array, "id = '".$id."'")
                            ->execute();

                $this->setLog('AccountMaster', '', 'Save', '', 'Update Account Master Details', 'acc_master', $id);
            } else {
                $array['created_by'] = $curusr;
                $array['created_date'] = $now;
                $count = Yii::$app->db->createCommand()
                            ->insert($tableName, $array)
                            ->execute();
                $id = Yii::$app->db->getLastInsertID();

                $this->setLog('AccountMaster', '', 'Save', '', 'Insert Account Master Details', 'acc_master', $id);
            }
        }

        $sql = "delete from acc_categories where acc_id = '$id'";
        Yii::$app->db->createCommand($sql)->execute();

        if ($type=="Vendor Goods"){
            $acc_categories = array();
            if(count($bus_category)>0){
                for($i=0; $i<count($bus_category); $i++){
                    if($bus_category[$i]!=""){
                        $acc_categories[$i] = array('acc_id' => $id, 
                                                    'category_id' => $bus_category[$i], 
                                                    'category_name' => $bus_category_name[$i], 
                                                    'status' => 'pending',
                                                    'is_active' => '1',
                                                    'created_by'=>$curusr,
                                                    'created_date'=>$now,
                                                    'updated_by'=>$curusr,
                                                    'updated_date'=>$now,
                                                    'approver_comments'=>$remarks
                                                );
                    }
                }
            }

            if(count($acc_categories)>0){
                // $acc_categories[$i]['created_by'] = $curusr;
                // $acc_categories[$i]['created_date'] = $now;

                $columnNameArray=['acc_id', 'category_id', 'category_name', 'status', 'is_active', 
                                    'created_by', 'created_date', 'updated_by', 'updated_date', 
                                    'approver_comments'];
                $tableName = "acc_categories";
                $insertCount = Yii::$app->db->createCommand()
                                ->batchInsert($tableName, $columnNameArray, $acc_categories)
                                ->execute();
            }
        }


        $address_doc_path = $request->post('address_doc_path');
        $pan_no_doc_path = $request->post('pan_no_doc_path');
        $aadhar_card_no_doc_path = $request->post('aadhar_card_no_doc_path');
        $service_tax_no_doc_path = $request->post('service_tax_no_doc_path');
        $vat_no_doc_path = $request->post('vat_no_doc_path');
        $pf_esic_no_doc_path = $request->post('pf_esic_no_doc_path');
        $agreement_details_doc_path = $request->post('agreement_details_doc_path');
        $acc_no_doc_path = $request->post('acc_no_doc_path');
        $other_doc_path = $request->post('other_doc_path');

        $upload_path = './uploads';
        if(!is_dir($upload_path)) {
            mkdir($upload_path, 0777, TRUE);
        }
        $upload_path = './uploads/account';
        if(!is_dir($upload_path)) {
            mkdir($upload_path, 0777, TRUE);
        }
        $upload_path = './uploads/account/'.$id;
        if(!is_dir($upload_path)) {
            mkdir($upload_path, 0777, TRUE);
        }

        $uploadedFile = UploadedFile::getInstanceByName('address_doc_file');
        if(!empty($uploadedFile)){
            $src_filename= $_FILES['address_doc_file'];
            $filePath = $upload_path.'/'.$src_filename['name'];
            $uploadedFile->saveAs($filePath);
            $address_doc_path = 'uploads/account/'.$id.'/'.$src_filename['name'];
        }
        $uploadedFile = UploadedFile::getInstanceByName('pan_no_doc_file');
        if(!empty($uploadedFile)){
            $src_filename= $_FILES['pan_no_doc_file'];
            $filePath = $upload_path.'/'.$src_filename['name'];
            $uploadedFile->saveAs($filePath);
            $pan_no_doc_path = 'uploads/account/'.$id.'/'.$src_filename['name'];
        }
        $uploadedFile = UploadedFile::getInstanceByName('aadhar_card_no_doc_file');
        if(!empty($uploadedFile)){
            $src_filename= $_FILES['aadhar_card_no_doc_file'];
            $filePath = $upload_path.'/'.$src_filename['name'];
            $uploadedFile->saveAs($filePath);
            $aadhar_card_no_doc_path = 'uploads/account/'.$id.'/'.$src_filename['name'];
        }
        $uploadedFile = UploadedFile::getInstanceByName('service_tax_no_doc_file');
        if(!empty($uploadedFile)){
            $src_filename= $_FILES['service_tax_no_doc_file'];
            $filePath = $upload_path.'/'.$src_filename['name'];
            $uploadedFile->saveAs($filePath);
            $service_tax_no_doc_path = 'uploads/account/'.$id.'/'.$src_filename['name'];
        }
        $uploadedFile = UploadedFile::getInstanceByName('agreement_details_doc_file');
        if(!empty($uploadedFile)){
            $src_filename= $_FILES['agreement_details_doc_file'];
            $filePath = $upload_path.'/'.$src_filename['name'];
            $uploadedFile->saveAs($filePath);
            $agreement_details_doc_path = 'uploads/account/'.$id.'/'.$src_filename['name'];
        }
        $uploadedFile = UploadedFile::getInstanceByName('vat_no_doc_file');
        if(!empty($uploadedFile)){
            $src_filename= $_FILES['vat_no_doc_file'];
            $filePath = $upload_path.'/'.$src_filename['name'];
            $uploadedFile->saveAs($filePath);
            $vat_no_doc_path = 'uploads/account/'.$id.'/'.$src_filename['name'];
        }
        $uploadedFile = UploadedFile::getInstanceByName('pf_esic_no_doc_file');
        if(!empty($uploadedFile)){
            $src_filename= $_FILES['pf_esic_no_doc_file'];
            $filePath = $upload_path.'/'.$src_filename['name'];
            $uploadedFile->saveAs($filePath);
            $pf_esic_no_doc_path = 'uploads/account/'.$id.'/'.$src_filename['name'];
        }
        $uploadedFile = UploadedFile::getInstanceByName('acc_no_doc_file');
        if(!empty($uploadedFile)){
            $src_filename= $_FILES['acc_no_doc_file'];
            $filePath = $upload_path.'/'.$src_filename['name'];
            $uploadedFile->saveAs($filePath);
            $acc_no_doc_path = 'uploads/account/'.$id.'/'.$src_filename['name'];
        }
        $uploadedFile = UploadedFile::getInstanceByName('other_doc_file');
        if(!empty($uploadedFile)){
            $src_filename= $_FILES['other_doc_file'];
            $filePath = $upload_path.'/'.$src_filename['name'];
            $uploadedFile->saveAs($filePath);
            $other_doc_path = 'uploads/account/'.$id.'/'.$src_filename['name'];
        }

        $array = array('address_doc_path' => $address_doc_path, 
                        'pan_no_doc_path' => $pan_no_doc_path, 
                        'aadhar_card_no_doc_path' => $aadhar_card_no_doc_path, 
                        'service_tax_no_doc_path' => $service_tax_no_doc_path,
                        'vat_no_doc_path' => $vat_no_doc_path,
                        'pf_esic_no_doc_path' => $pf_esic_no_doc_path,
                        'agreement_details_doc_path' => $agreement_details_doc_path,
                        'acc_no_doc_path' => $acc_no_doc_path,
                        'other_doc_path' => $other_doc_path);

        $tableName = "acc_master";
        $count = Yii::$app->db->createCommand()
                            ->update($tableName, $array, "id = '".$id."'")
                            ->execute();
    }

    public function authorise($status){
        $request = Yii::$app->request;
        $session = Yii::$app->session;

        $curusr = $session['session_id'];
        $now = date('Y-m-d H:i:s');
        $id = $request->post('id');
        $remarks = $request->post('remarks');

        $array = array('status' => $status, 
                        'approved_by' => $curusr, 
                        'approved_date' => $now,
                        'approver_comments'=>$remarks);

        $count = Yii::$app->db->createCommand()
                            ->update("acc_master", $array, "id = '".$id."'")
                            ->execute();

        $count = Yii::$app->db->createCommand()
                            ->update("acc_categories", $array, "acc_id = '".$id."'")
                            ->execute();

        if($status=='approved'){
            $this->setLog('AccountMaster', '', 'Approve', '', 'Approve Account Master Details', 'acc_master', $id);
        } else {
            $this->setLog('AccountMaster', '', 'Reject', '', 'Reject Account Master Details', 'acc_master', $id);
        }
    }

    public function getAccountCategories(){
        $sql = "select * from acc_category_master where status = 'approved'";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function saveCategories() {
        $request = Yii::$app->request;

        $category_id = $request->post('category_id');
        $category_1 = $request->post('category_1');
        $category_2 = $request->post('category_2');
        $category_3 = $request->post('category_3');

        for($i=0; $i<count($category_id); $i++){
            if(isset($category_1[$i]) || isset($category_2[$i]) || isset($category_3[$i])){
                if($category_1[$i]!='' || $category_2[$i]!='' || $category_3[$i]!=''){
                    $array = array('category_1' => $category_1[$i], 
                                'category_2' => $category_2[$i], 
                                'category_3' => $category_3[$i],
                                'status' => 'approved');

                    $tableName = "acc_category_master";

                    if (isset($category_id[$i]) && $category_id[$i]!=""){
                        $count = Yii::$app->db->createCommand()
                                    ->update($tableName, $array, "id = '".$category_id[$i]."'")
                                    ->execute();
                    } else {
                        $count = Yii::$app->db->createCommand()
                                    ->insert($tableName, $array)
                                    ->execute();
                    }
                }
            }
        }
        
        return true;
    }

    public function checkLegalNameAvailablity(){
        $request = Yii::$app->request;

        $id = $request->post('id');
        $legal_name = $request->post('legal_name');

        $sql = "SELECT * FROM acc_master WHERE id!='".$id."' and legal_name='".$legal_name."'";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        $data = $reader->readAll();
        if (count($data)>0){
            return 1;
        } else {
            return 0;
        }
    }

    public function setLog($module_name, $sub_module, $action, $vendor_id, $description, $table_name, $table_id) {
        $session = Yii::$app->session;
        $curusr = $session['session_id'];
        $now = date('Y-m-d H:i:s');

        $array = array('module_name' => $module_name, 
                        'sub_module' => $sub_module, 
                        'action' => $action, 
                        'vendor_id' => $vendor_id, 
                        'user_id' => $curusr, 
                        'description' => $description, 
                        'log_activity_date' => $now, 
                        'table_name' => $table_name, 
                        'table_id' => $table_id);
        $count = Yii::$app->db->createCommand()
                            ->insert("acc_user_log", $array)
                            ->execute();

        return true;
    }
}