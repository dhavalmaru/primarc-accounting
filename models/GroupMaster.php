<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class GroupMaster extends Model
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
        $company_id = $session['company_id'];

        $cond = "";
        if($action!="authorise" && $action!="view"){
            $cond = " and A.id!='".$session_id."'";
        } 

        $sql = "select distinct A.id, A.username, C.r_approval from user A 
                left join acc_user_roles B on (A.id = B.user_id) 
                left join acc_user_role_options C on (B.role_id = C.role_id) 
                where B.company_id = '$company_id' and C.r_section = 'S_Account_Master' and 
                        C.r_approval = '1' and C.r_approval is not null" . $cond;
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getGroupDetails($parent_id=""){
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $final_tree = '';
        $sql = "select A.* from acc_group_master A where A.is_active = '1' and A.status = 'approved' and 
                A.company_id = '$company_id' and A.parent_id = '$parent_id' order by A.id";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        $data = $reader->readAll();
        if(count($data)>0){
            $final_tree = $final_tree . '<ul>';
            for($i=0; $i<count($data); $i++){
                $final_tree = $final_tree . '<li id="type_'.$data[$i]['id'].'">'.$data[$i]['account_type'];
                $final_tree = $final_tree . $this->getGroupDetails($data[$i]['id']);
            }
            $final_tree = $final_tree . '</li></ul>';
        }
        
        return $final_tree;
    }

    public function getAccountType(){
        $request = Yii::$app->request;
        $session = Yii::$app->session;
        $company_id = $session['company_id'];
        $curusr = $session['session_id'];
        $now = date('Y-m-d H:i:s');

        $parent_id = $request->post('parent_id');
        $account_type = $request->post('account_type');

        $result = 0;
        $sql = "select A.* from acc_group_master A where A.is_active = '1' and A.status = 'approved' and 
                A.company_id = '$company_id' and A.id != '$parent_id' and A.account_type = '$account_type' 
                order by A.id";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        $data = $reader->readAll();
        if(count($data)>0){
            $result = 1;
        }
        
        return $result;
    }

    public function getAccTypeFromAccMaster(){
        $request = Yii::$app->request;
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $action = $request->post('action');
        $parent_id = $request->post('parent_id');
        $account_type = $request->post('account_type');

        if($action=='delete'){
            $cond = " and (A.legal_name = '$account_type' or A.sub_account_type = '$parent_id')";
        } else {
            $cond = " and A.legal_name = '$account_type'";
        }

        $result = 0;
        $sql = "select A.* from acc_master A where A.is_active = '1' and A.status = 'approved' and 
                A.company_id = '$company_id' ".$cond." order by A.id";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        $data = $reader->readAll();
        if(count($data)>0){
            $result = 1;
        }
        
        return $result;
    }

    public function getChildAccountType(){
        $request = Yii::$app->request;
        $session = Yii::$app->session;
        $company_id = $session['company_id'];
        $curusr = $session['session_id'];
        $now = date('Y-m-d H:i:s');

        $parent_id = $request->post('parent_id');

        $result = 0;
        $sql = "select A.* from acc_group_master A where A.is_active = '1' and A.status = 'approved' and 
                A.company_id = '$company_id' and A.parent_id = '$parent_id' order by A.id";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        $data = $reader->readAll();
        if(count($data)>0){
            $result = 1;
        }
        
        return $result;
    }

    public function setAccountType(){
        $request = Yii::$app->request;
        $session = Yii::$app->session;
        $company_id = $session['company_id'];
        $curusr = $session['session_id'];
        $now = date('Y-m-d H:i:s');

        $action = $request->post('action');
        $parent_id = $request->post('parent_id');
        $account_type = $request->post('account_type');

        $id = '';
        $tableName = "acc_group_master";

        if($action=='insert'){
            $array = array('account_type' => $account_type, 
                            'parent_id' => $parent_id,
                            'status' => 'approved',
                            'is_active' => '1',
                            'updated_by'=>$curusr,
                            'updated_date'=>$now,
                            'company_id'=>$company_id
                            );
            if(count($array)>0){
                $array['created_by'] = $curusr;
                $array['created_date'] = $now;
                $count = Yii::$app->db->createCommand()
                            ->insert($tableName, $array)
                            ->execute();
                $id = Yii::$app->db->getLastInsertID();

                $this->setLog('GroupMaster', '', 'Save', '', 'Insert Group Master Details', 'acc_group_master', $id);
            }
        } else if($action=='update'){
            $id = $parent_id;

            $array = array('account_type' => $account_type,
                            'status' => 'approved',
                            'is_active' => '1',
                            'updated_by'=>$curusr,
                            'updated_date'=>$now,
                            'company_id'=>$company_id
                            );
            $count = Yii::$app->db->createCommand()
                        ->update($tableName, $array, "id = '".$id."'")
                        ->execute();

            $this->setLog('GroupMaster', '', 'Update', '', 'Update Group Master Details', 'acc_group_master', $id);
        } else if($action=='delete'){
            $id = $parent_id;

            $array = array('account_type' => $account_type,
                            'status' => 'inactive',
                            'is_active' => '0',
                            'updated_by'=>$curusr,
                            'updated_date'=>$now,
                            'company_id'=>$company_id
                            );
            $count = Yii::$app->db->createCommand()
                        ->update($tableName, $array, "id = '".$id."'")
                        ->execute();

            $this->setLog('GroupMaster', '', 'Delete', '', 'Delete Group Master Details', 'acc_group_master', $id);
        }
        
        return $id;
    }

    public function setLog($module_name, $sub_module, $action, $vendor_id, $description, $table_name, $table_id){
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

    public function getGroupDetails_invoice($parent_id="",$submit="",$group_ids=''){
        $session = Yii::$app->session;
        $company_id = $session['company_id'];


        $final_tree = '';
        $sql = "select A.* from acc_group_master A where A.is_active = '1' and A.status = 'approved' and 
                A.company_id = '$company_id' and A.parent_id = '$parent_id' order by A.id";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        $data = $reader->readAll();
        $group = explode(",",$group_ids);
        if(count($data)>0){
            for($i=0; $i<count($data); $i++){
                $select = '';
                $data[$i]['id'];

                if(in_array($data[$i]['id'],$group))
                    {
                       $select = 'selected';
                    }

                $final_tree = $final_tree . '<option  id="type_'.$data[$i]['id'].'" value="'.$data[$i]['id'].'" '.$select.'>'.$data[$i]['account_type'].'</option>';
                $final_tree = $final_tree . $this->getGroupDetails_invoice($data[$i]['id'],'',$group_ids);
            }
        }
        
        return $final_tree;
    }

    public function getGroupDetails_ids($parent_id=""){
        $session = Yii::$app->session;
        $company_id = $session['company_id'];
        $final_tree = '';
        $sql = "select A.* from acc_group_master A where A.is_active = '1' and A.status = 'approved' and 
                A.company_id = '$company_id' and A.parent_id = '$parent_id' order by A.id";
         
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        $data = $reader->readAll();
        if(count($data)>0){
            for($i=0; $i<count($data); $i++){
                $final_tree = $final_tree . "'" . $data[$i]['id'] . "', ";
                $final_tree = $final_tree . $this->getGroupDetails_ids($data[$i]['id']);
            }
        }

        return $final_tree;
    }

    public function getLedgerDetail($id='', $acc_type=''){
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        if($id!='') {
            $id = rtrim($id,",");
            $sql = "SELECT id, legal_name from acc_master Where (account_type in ($acc_type) or sub_account_type IN ($id)) and company_id = '$company_id' and is_active = '1' and status = 'approved'";
            $command = Yii::$app->db->createCommand($sql);
            $reader = $command->query();
            $data = $reader->readAll(); 
        } else {
            $sql = "SELECT id, legal_name from acc_master Where company_id = '$company_id' and is_active = '1' and status = 'approved'";
            $command = Yii::$app->db->createCommand($sql);
            $reader = $command->query();
            $data = $reader->readAll(); 
        }

        return $data;
    }
}