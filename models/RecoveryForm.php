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

use dektrium\user\models\RecoveryForm as BaseRecoveryForm;
use Yii;
use dektrium\user\models\Token;


/**
 * Model for collecting data on password recovery.
 *
 * @property \dektrium\user\Module $module
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class RecoveryForm extends BaseRecoveryForm {

    public function sendRecoveryMessage() {
        
        if (!$this->validate()) {
            return false;
        }
        $user = $this->finder->findUserByEmail($this->email);

        if ($user) {
            /** @var Token $token */
            $token = \Yii::createObject([
                'class' => Token::className(),
                'user_id' => $user->id,
                'type' => Token::TYPE_RECOVERY,
            ]);

            if (!$token->save(false)) {
                return false;
            }
            $mailer = new \backend\models\Mailer();
            $mailer->sendRecoveryMessage($user, $token);
            Yii::$app->session->setFlash('info', Yii::t('user', 'An email has been sent with instructions for resetting your password'));

            return true;
             }
       

        return false;
    }

}
