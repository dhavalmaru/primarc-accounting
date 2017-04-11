<?php

namespace app\models;

use Yii;
use yii\base\Model;

class PendingGrn extends Model
{
    public function getPendingGrn(){
    	$sql = "SELECT * FROM grn where status = 'Approved' and is_active = '1'";
    	$command = Yii::$app->db->createCommand($sql);
		$reader = $command->query();
		return $reader->readAll();
    }
}