<?php

namespace app\models;

use Yii;
use yii\base\Model;
use mPDF;

class Amazonstate extends Model
{
    public function getAccess(){
        $session = Yii::$app->session;
        $session_id = $session['session_id'];
        $role_id = $session['role_id'];

        $sql = "select A.*, '".$session_id."' as session_id from acc_user_role_options A 
                where A.role_id = '$role_id' and A.r_section = 'S_Purchase'";
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
                where B.company_id = '$company_id' and C.r_section = 'S_Purchase' and 
                        C.r_approval = '1' and C.r_approval is not null" . $cond;
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getDetails($id="", $status=""){
        $cond = "";

        if($id!=""){
            $cond = " and A.id = '$id'";
        }
        if($status!=""){
            if($cond==""){
                $cond = " and A.status = '$status'";
            } else {
                $cond = $cond . " and A.status = '$status'";
            }
        }

        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select A.*, B.username as updater, C.username as approver from acc_amazon_state_master A 
                left join user B on (A.updated_by = B.id) 
                left join user C on (A.approved_by = C.id) where A.company_id = '$company_id' " . $cond . " 
                order by UNIX_TIMESTAMP(A.updated_date) desc, A.id desc";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
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
        $mycomponent = Yii::$app->mycomponent;
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $curusr = $session['session_id'];
        $now = date('Y-m-d H:i:s');

        $id = $request->post('id');
        $amazon_state = $request->post('amazon_state');
        $erp_state = $request->post('erp_state');
        $remarks = $request->post('remarks');
        // $approver_id = $request->post('approver_id');
        
        $array=[
             
                'amazon_state' => $amazon_state, 
                'erp_state' => $erp_state, 
                'status'=>'approved',
                'is_active'=>'1',
                'updated_by'=>$curusr,
                'updated_date'=>$now,
                // 'payment_date'=>$payment_date,
                'approver_comments'=>$remarks,
                // 'approver_id'=>$approver_id,
                'company_id'=>$company_id
            ];

        if (isset($id) && $id!=""){
            $count = Yii::$app->db->createCommand()
                        ->update("acc_amazon_state_master", $array, "id = '".$id."'")
                        ->execute();

            $this->setLog('Amazonstate', '', 'Save', '', 'Update Tax type Details', 'acc_amazon_state_master', $id);
        } else {
            $array['created_by']=$curusr;
            $array['created_date']=$now;

            $count = Yii::$app->db->createCommand()
                        ->insert("acc_amazon_state_master", $array)
                        ->execute();
            $id = Yii::$app->db->getLastInsertID();
          
            $this->setLog('Amazonstate', '', 'Save', '', 'Insert Tax type Details', 'acc_amazon_state_master', $id);
        }

        return true;
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
                            ->update("acc_amazon_state_master", $array, "id = '".$id."'")
                            ->execute();



        if($status=='approved'){
            $this->setLog('Amazonstate', '', 'Approve', '', 'Approve Tax type Details', 'acc_amazon_state_master', $id);
        } else {
            $this->setLog('Amazonstate', '', 'Reject', '', 'Reject Tax type Details', 'acc_amazon_state_master', $id);
        }
    }

    public function setLog($module_name, $sub_module,$vendor_id,$action,$description, $table_name, $table_id) {
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

    public function checkAmazonstateAvailablity(){
        $request = Yii::$app->request;
        $id = $request->post('id');
        $amazon_state = $request->post('amazon_state');
        $company_id = $request->post('company_id');

        $sql = "SELECT * FROM acc_amazon_state_master WHERE id!='".$id."' and amazon_state='".$amazon_state."' and company_id='".$company_id."'";
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