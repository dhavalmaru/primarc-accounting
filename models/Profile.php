<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace backend\models;

use dektrium\user\models\Profile as BaseProfile;
use dektrium\user\helpers\ModuleTrait;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "profile".
 *
 * @property integer $user_id
 * @property string  $name
 * @property string  $public_email
 * @property string  $gravatar_email
 * @property string  $gravatar_id
 * @property string  $location
 * @property string  $website
 * @property string  $bio
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com
 */
class Profile extends BaseProfile {

    

    

     public $mobile;
     public $phone;
     public $fax;
     public $user_image;
     
       public function scenarios()
    {
        $scenarios = parent::scenarios();
        // add field to scenarios
      //  $scenarios['create'][]   = 'name';
        $scenarios['update'][]   = 'name';
        return $scenarios;
    }

    public function rules()
    {
        $rules = parent::rules();
        // add some rules
       $rules['nameSafe'] = ['name', 'safe'];
       $rules['nameRequired'] = ['name', 'required'];
       $rules['nameLength']   = ['name', 'string', 'max' => 50];

        return $rules;
    }
    
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%profile}}';
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert) {

       
        $this->setAttribute('phone', $this->phone);
        $this->setAttribute('fax', $this->fax);
        $this->setAttribute('mobile', $this->mobile);
        $this->setAttribute('user_image', $this->user_image);

        if (parent::beforeSave($insert)) {
            if ($this->isAttributeChanged('gravatar_email')) {
                //$this->setAttribute('gravatar_id', md5($this->getAttribute('gravatar_email')));
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * @return \yii\db\ActiveQueryInterface
     */
    public function getUser() {
        return $this->hasOne('\backend\models\User', ['id' => 'user_id']);
    }

}
