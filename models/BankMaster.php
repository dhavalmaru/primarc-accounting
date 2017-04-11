<?php

namespace app\models;

use Yii;
use yii\base\Model;

class BankMaster extends Model
{
    public function getBankDetails($id="", $status=""){
        $cond = "";
        if($id!=""){
            $cond = " Where id = '$id'";
        }
        if($status!=""){
            if($cond==""){
                $cond = " Where status = '$status'";
            } else {
                $cond = $cond . " and status = '$status'";
            }
        }

        $sql = "select * from bank_master" . $cond;
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function save(){
        $request = Yii::$app->request;
        $mycomponent = Yii::$app->mycomponent;

        $id = $request->post('id');
        $bank_name = $request->post('bank_name');
        $branch = $request->post('branch');
        $address = $request->post('address');
        $landmark = $request->post('landmark');
        $city = $request->post('city');
        $pincode = $request->post('pincode');
        $state = $request->post('state');
        $country = $request->post('country');
        $acc_type = $request->post('acc_type');
        $acc_no = $request->post('acc_no');
        $ifsc_code = $request->post('ifsc_code');
        $customer_id = $request->post('customer_id');
        $phone_no = $request->post('phone_no');
        $relationship_manager = $request->post('relationship_manager');
        $opening_balance = $mycomponent->format_number($request->post('opening_balance'),2);
        $balance_ref_date = $request->post('balance_ref_date');

        $array = array('bank_name' => $bank_name, 
                        'branch' => $branch, 
                        'address' => $address, 
                        'landmark' => $landmark, 
                        'city' => $city, 
                        'pincode' => $pincode, 
                        'state' => $state, 
                        'country' => $country, 
                        'acc_type' => $acc_type, 
                        'acc_no' => $acc_no, 
                        'ifsc_code' => $ifsc_code, 
                        'customer_id' => $customer_id, 
                        'phone_no' => $phone_no, 
                        'relationship_manager' => $relationship_manager, 
                        'opening_balance' => $opening_balance, 
                        'balance_ref_date' => $balance_ref_date, 
                        'status' => 'pending');

        if(count($array)>0){
            $columnNameArray=['bank_name','branch','address', 'landmark', 'city', 'pincode', 'state', 'country', 'acc_type', 
                                'acc_no', 'ifsc_code', 'customer_id', 'phone_no', 'relationship_manager', 
                                'opening_balance', 'balance_ref_date'];
            $tableName = "bank_master";

            if (isset($id) && $id!=""){
                $count = Yii::$app->db->createCommand()
                            ->update($tableName, $array, "id = '".$id."'")
                            ->execute();
            } else {
                $count = Yii::$app->db->createCommand()
                            ->insert($tableName, $array)
                            ->execute();
            }
            
            // echo $count;
        }

        return true;
    }
}