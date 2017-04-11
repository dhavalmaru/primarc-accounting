<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "permission".
 *
 * @property integer $id
 * @property string $permission_name
 * @property string $permission_code
 * @property string $created_at
 * @property string $updated_at
 */
class Permission extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'permission';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['permission_name', 'permission_code', 'created_at'], 'required'],
            [['permission_name', 'permission_code'], 'unique'],
            [['created_at', 'updated_at'], 'safe'],
            [['permission_name'], 'string', 'max' => 150],
            [['permission_code'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'permission_name' => 'Permission Name',
            'permission_code' => 'Permission Code',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
