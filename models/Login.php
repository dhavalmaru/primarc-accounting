<?php

namespace app\models;

use Yii;
use yii\base\Model;

class Login extends Model
{	
	public function getUser($id){
        $sql = "select * from user where id = '$id'";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getDetails($uname, $upass){
        $sql = "select * from user where email = '$uname' and password_hash = '$upass'";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }
}