<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class UserRole extends Model
{
    public function getAccess(){
        $session = Yii::$app->session;
        $role_id = $session['role_id'];

        $sql = "select * from acc_user_role_options where role_id = '$role_id' and r_section = 'S_User_Roles'";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getRoleDetails($id="", $status=""){
        $cond = "";
        if($id!=""){
            $cond = " and A.id = '$id'";
        }
        if($status!=""){
            $cond = $cond . " and A.status = '$status'";
        }

        $sql = "select A.*, B.username from acc_user_role_master A left join user B on(A.updated_by = B.id) 
                where A.is_active='1'" . $cond . " order by A.updated_date desc";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getRoleOptions($id=""){
        $cond = "";
        if($id!=""){
            $cond = " and A.role_id = '$id'";
        }

        $sql = "select * from acc_user_role_options A where A.r_section in('S_Account_Master', 'S_Purchase', 
                    'S_Journal_Voucher', 'S_Payment_Receipt', 'S_User_Roles', 'S_Reports')" . $cond . " 
                order by A.id";
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
                    'role' => $request->post('role'),
                    'description' => $request->post('description'),
                    'status' => 'approved',
                    'is_active' => '1',
                    'updated_by' => $curusr,
                    'updated_date' => $now
                );
        $id = $request->post('id');
        if (isset($id) && $id!=""){
            $count = Yii::$app->db->createCommand()
                        ->update('acc_user_role_master', $array, "id = '".$id."'")
                        ->execute();
        } else {
            $array['created_by'] = $curusr;
            $array['created_date'] = $now;

            $count = Yii::$app->db->createCommand()
                        ->insert('acc_user_role_master', $array)
                        ->execute();

            $id = Yii::$app->db->getLastInsertID();
        }

        $vw = $request->post('view');
        $ins = $request->post('insert');
        $upd = $request->post('update');
        $del = $request->post('delete');
        $apps = $request->post('approval');
        $exp = $request->post('export');

        $sects = array('S_Account_Master', 'S_Purchase', 'S_Journal_Voucher', 'S_Payment_Receipt', 'S_User_Roles', 'S_Reports');
        $a=$b=$c=$d=$e=$f=0;
        $viw=$insrt=$updt=$delt=$apprs=$expt=NULL;
        for ($i=0; $i < count($sects) ; $i++) { 
            if(count($vw)>$a){
                if($vw[$a]==$i){
                    $viw[$i]=1;
                    if(count($viw)>$a){
                        $a++;
                    }
                } else {
                    $viw[$i]=0;
                }    
            } else {
                $viw[$i]=0;
            }
            
            if(count($ins)>$b) {
                if($ins[$b]==$i){
                    $insrt[$i]=1;
                    if(count($insrt)>$b){
                        $b++;
                    }
                } else {
                    $insrt[$i]=0;
                }
            } else {
                $insrt[$i]=0;
            }

            if(count($upd)>$c) {
                if($upd[$c]==$i){
                    echo $i;
                    echo $upd[$c];
                    $updt[$i]=1;
                        echo count($updt);  echo $c;
                     if(count($updt)>$c){
                        $c++;
                     
                    }
                } else {
                    $updt[$i]=0;
                }
            } else {
                $updt[$i]=0;
            }
           
            if(count($del)>$d) {
                if($del[$d]==$i){
                    $delt[$i]=1;
                    if(count($delt)>$d){
                        $d++;
                    }
                } else {
                    $delt[$i]=0;
                }
            } else {
                $delt[$i]=0;
            }

            if(count($apps)>$e) {
                if($apps[$e]==$i){
                    $apprs[$i]=1;
                    if(count($apprs)>$e){
                        $e++;
                    }
                } else {
                    $apprs[$i]=0;
                }
            } else {
                $apprs[$i]=0;
            }

            if(count($exp)>$f) {
                if($exp[$f]==$i){
                    $expt[$i]=1;
                    if(count($expt)>$f){
                        $f++;
                    }
                } else {
                    $expt[$i]=0;
                }
            } else {
                $expt[$i]=0;
            }
        }

        for ($i=0; $i < count($sects) ; $i++) { 
            $array = array(
                'role_id' => $id,
                'r_section' => $sects[$i],
                'r_view' => $viw[$i],
                'r_insert' => $insrt[$i],
                'r_edit' => $updt[$i],
                'r_delete' => $delt[$i],
                'r_approval' => $apprs[$i],
                'r_export' => $expt[$i]
            );

            $sql = "select * from acc_user_role_options where role_id = '".$id."' and r_section = '".$sects[$i]."'";
            $command = Yii::$app->db->createCommand($sql);
            $reader = $command->query();
            $data = $reader->readAll();

            if (count($data)>0){
                Yii::$app->db->createCommand()
                    ->update('acc_user_role_options', $array, "role_id = '".$id."' and r_section = '".$sects[$i]."'")
                    ->execute();
            } else {
                Yii::$app->db->createCommand()
                    ->insert('acc_user_role_options', $array)
                    ->execute();
            }
        }

        return true;
    }

    public function checkRoleAvailablity(){
        $request = Yii::$app->request;

        $id = $request->post('id');
        $role = $request->post('role');

        // $id = '127';
        // $role = 'Admin';

        $sql = "SELECT * FROM acc_user_role_master WHERE id!='".$id."' and role='".$role."'";
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