<?php

namespace app\models;

use Yii;
use yii\base\Model;

class Login extends Model
{	
	public function getUser($id){
        $sql = "select A.*, B.role_id, C.r_section, C.r_view, C.r_insert, C.r_edit, C.r_delete, C.r_approval, C.r_export 
                from user A left join acc_user_roles B on (A.id = B.user_id) left join acc_user_role_options C on (B.role_id=C.role_id) 
                where A.id = '$id'";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getDetails($uname, $upass){
        $sql = "select A.*, B.role_id, C.r_section, C.r_view, C.r_insert, C.r_edit, C.r_delete, C.r_approval, C.r_export 
                from user A left join acc_user_roles B on (A.id = B.user_id) left join acc_user_role_options C on (B.role_id=C.role_id) 
                where A.username = '$uname' and A.password_hash = '$upass'";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function log($action){
        $session = Yii::$app->session;
        $curusr = $session['session_id'];
        $now = date('Y-m-d H:i:s');

        $array = array('company_id' => '1', 
                        'user_id' => $curusr, 
                        'user_action' => $action, 
                        'action_date' => $now);
        $count = Yii::$app->db->createCommand()
                            ->insert("acc_user_log_history", $array)
                            ->execute();

        return true;
    }
}