<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "authorize_permission".
 *
 * @property integer $id
 * @property integer $is_authorize
 * @property string $name
 * @property string $authorize_code
 * @property string $module_name
 * @property integer $priority
 */
class AuthorizePermission extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'authorize_permission';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['is_authorize', 'priority'], 'integer'],
            [['name', 'authorize_code', 'module_name', 'priority'], 'required'],
            [['module_name'], 'string'],
            [['name', 'authorize_code'], 'string', 'max' => 250],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'is_authorize' => 'Is Authorize',
            'name' => 'Name',
            'authorize_code' => 'Authorize Code',
            'module_name' => 'Module Name',
            'priority' => 'Priority',
        ];
    }
}
