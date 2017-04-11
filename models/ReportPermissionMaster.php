<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "report_permission_master".
 *
 * @property integer $id
 * @property string $report_name
 * @property string $report_code
 */
class ReportPermissionMaster extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'report_permission_master';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['report_name', 'report_code'], 'required'],
            [['report_name'], 'string', 'max' => 150],
            [['report_code'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'report_name' => 'Report Name',
            'report_code' => 'Report Code',
        ];
    }
}
