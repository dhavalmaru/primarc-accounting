<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class AccountMaster extends Model
{
    public function getAccountDetails($id="", $status=""){
        $cond = "";
        $cond2 = "";
        if($id!=""){
            $cond = " and id = '$id'";
            $cond2 = " and acc_id = '$id'";
        }
        // if($status!=""){
        //     if($cond==""){
        //         $cond = " where status = '$status'";
        //     } else {
        //         $cond = $cond . " and status = '$status'";
        //     }
        // }

        if($status!=""){
            $cond = $cond . " and status = '$status'";
        }
        
        // $sql = "select * from acc_master where is_active = '1'" . $cond . " order by id desc";
        $sql = "select A.*, concat_ws(',', A.category_1, A.category_2, A.category_3) as acc_category, B.bus_category from 
                (select * from acc_master where is_active = '1'" . $cond . ") A 
                left join 
                (select acc_id, GROUP_CONCAT(category_name) as bus_category from acc_categories where is_active = '1'" . $cond2 . " 
                    group by acc_id) B 
                on (A.id = B.acc_id) order by updated_date desc";

        // echo $sql;
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getVendors($vendor_id=""){
        $sql = "select * from vendor_master where is_active = '1' and 
                id not in (select distinct vendor_id from acc_master where vendor_id != '$vendor_id') order by vendor_name";
        // $sql = "select vendor_code as value, vendor_name as label, id from vendor_master 
        //         where is_active = '1' order by vendor_name desc";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getVendorDetails(){
        $request = Yii::$app->request;
        $vendor_id = $request->post('vendor_id');

        // $vendor_id = "5";

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

        // $sql = "select GROUP_CONCAT(distinct main_category) as main_category, GROUP_CONCAT(distinct subcategory1) as subcategory1, 
        //         GROUP_CONCAT(distinct subcategory2) as subcategory2 from 
        //         (select B.*, C.category_name as main_category, D.subcategory_name as subcategory1, 
        //             E.subcategory_name as subcategory2 from product_master A 
        //             left join product_category_relation B on (A.id = B.product_id) 
        //             left join product_main_category C on (B.main_category_id = C.id) 
        //             left join product_subcategory1 D on (B.sub_category1_id = D.id) 
        //             left join product_subcategory2 E on (B.sub_category2_id = E.id) 
        //             where A.preferred_vendor_id = '$vendor_id' and C.is_active = '1' and D.is_active = '1' and E.is_active = '1') F";
        // $command = Yii::$app->db->createCommand($sql);
        // $reader = $command->query();
        // $data['category_details'] = $reader->readAll();

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

        // $type = "Vendor Expenses";
        
        if($type=="Vendor Expenses"){
            $code = "VE";
        } else if($type=="Bank Account"){
            $code = "BK";
        } else if($type=="Goods Purchase"){
            $code = "PU";
        } else if($type=="Tax"){
            $code = "TX";
        } else if($type=="Goods Sales"){
            $code = "GS";
        } else if($type=="Employee"){
            $code = "EM";
        } else {
            $code = "OT";
        }

        $sql = "select * from series_master where type = '$code'";
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
        } else if($type=="Goods Sales"){
            $code = "GS";
        } else if($type=="Employee"){
            $code = "EM";
        } else {
            $code = "OT";
        }

        $sql = "select * from series_master where type = '$code'";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        $data = $reader->readAll();

        if (count($data)>0){
            $series = intval($data[0]['series']) + 1;

            $sql = "update series_master set series = '$series' where type = '$code'";
            $command = Yii::$app->db->createCommand($sql);
            $count = $command->execute();
        } else {
            $series = 1;

            $sql = "insert into series_master (type, series) values ('".$code."', '".$series."')";
            $command = Yii::$app->db->createCommand($sql);
            $count = $command->execute();
        }

        $code = $code . str_pad($series, 4, "0", STR_PAD_LEFT);

        return $code;
    }

    public function save(){
        $request = Yii::$app->request;

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

        if($type=="Vendor Goods"){
            $vendor_id = $request->post('vendor_id');
            $code = $request->post('vendor_code');
            $pan_no = $request->post('pan_no');
            $address = $request->post('address');
            $legal_entity_name = $request->post('legal_entity_name');
            $vat_no = $request->post('vat_no');
            $bus_category = $request->post('bus_category');
            $bus_category_name = $request->post('bus_category_name');
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


        // $uploadedFile = CUploadedFile::getInstanceByName('file');

        // $src_filename= $_FILES['address_doc_file']['tmp_name']['img_thumb'];

        // $src_filename= $_FILES['address_doc_file'];
        // echo '<pre>$src_filename::'.print_r($src_filename,true).'<pre>'.$src_filename['name']; // I saw valid file path
        // if (file_exists('address_doc_file')) {
        // echo '<pre>$src_filenameEXISTS::'.print_r($src_filename,true).'<pre>';  // it shoes me that file exists!
        // }

        // $uploadedFile = UploadedFile::getInstanceByName('address_doc_file');
        // echo '<pre>111$uploadedFile::'.print_r($uploadedFile,true).'<pre>'; // But CUploadedFile object is empty !

        // $uploadedFile = UploadedFile::getInstanceByName('Good');


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
                        'is_active' => '1');

        if(count($array)>0){
            $tableName = "acc_master";

            if (isset($id) && $id!=""){
                $count = Yii::$app->db->createCommand()
                            ->update($tableName, $array, "id = '".$id."'")
                            ->execute();
            } else {
                $count = Yii::$app->db->createCommand()
                            ->insert($tableName, $array)
                            ->execute();
                $id = Yii::$app->db->getLastInsertID();
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
                                                    'is_active' => '1');
                    }
                }
            }

            if(count($acc_categories)>0){
                $columnNameArray=['acc_id','category_id','category_name', 'status', 'is_active'];
                $tableName = "acc_categories";
                $insertCount = Yii::$app->db->createCommand()
                                ->batchInsert(
                                    $tableName, $columnNameArray, $acc_categories
                                )
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
        
        return true;
    }

    public function getAccountCategories(){
        $sql = "select * from account_category_master where status = 'approved'";
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

                    $tableName = "account_category_master";

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

        // $id='';
        // $legal_name='Tax_KA_VAT_15.50';

        // echo $id;
        // echo '<br/>';
        // echo $legal_name;
        // echo '<br/>';

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
}