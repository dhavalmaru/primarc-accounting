<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class AssignRole extends Model
{
    public function getAccess(){
        $session = Yii::$app->session;
        $role_id = $session['role_id'];

        $sql = "select * from acc_user_role_options where role_id = '$role_id' and r_section = 'S_User_Roles'";
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
            $cond = $cond . " and A.status = '$status'";
        }

        $sql = "select A.*, B.username as user_name, C.role, D.username from acc_user_roles A 
                left join user B on(A.user_id = B.id) 
                left join acc_user_role_master C on(A.role_id = C.id) 
                left join user D on(A.updated_by = D.id) 
                where A.is_active='1'" . $cond . " order by A.updated_date desc";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getUsers($id=""){
        $cond = "";
        if($id!=""){
            $cond = " and id != '$id'";
        }
        $sql = "select A.* from user A where A.id not in (select distinct user_id from acc_user_roles 
                    where is_active = '1' ".$cond.") order by A.username";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getRoles(){
        $sql = "select A.* from acc_user_role_master A where A.is_active='1' and A.status = 'approved' order by A.role";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function save(){
        $request = Yii::$app->request;
        $session = Yii::$app->session;

        $now = date('Y-m-d H:i:s');
        $curusr = $session['session_id'];

        $array = array(
                    'user_id' => $request->post('user_id'),
                    'role_id' => $request->post('role_id'),
                    'status' => 'approved',
                    'is_active' => '1',
                    'updated_by' => $curusr,
                    'updated_date' => $now
                );
        $id = $request->post('id');
        if (isset($id) && $id!=""){
            $count = Yii::$app->db->createCommand()
                        ->update('acc_user_roles', $array, "id = '".$id."'")
                        ->execute();
        } else {
            $array['created_by'] = $curusr;
            $array['created_date'] = $now;

            $count = Yii::$app->db->createCommand()
                        ->insert('acc_user_roles', $array)
                        ->execute();

            $id = Yii::$app->db->getLastInsertID();
        }

        return true;
    }

    public function checkUserRoleAvailablity(){
        $request = Yii::$app->request;

        $id = $request->post('id');
        $user_id = $request->post('user_id');
        // $role_id = $request->post('role_id');

        // $id = '127';
        // $role = 'Admin';

        $sql = "SELECT * FROM acc_user_role_master WHERE id!='".$id."' and user_id='".$user_id."'";
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