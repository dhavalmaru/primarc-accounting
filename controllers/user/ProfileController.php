<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace backend\controllers\user;

use dektrium\user\controllers\ProfileController as BaseProfileController;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;

/**
 * ProfileController shows users profiles.
 *
 * @property \dektrium\user\Module $module
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class ProfileController extends BaseProfileController {

   
    /**
     * Shows user's profile.
     *
     * @param $id
     *
     * @return \yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    
    public function actionShow($id) {
        $profile = $this->module->manager->findProfileById($id);

        if ($profile === null) {
            throw new NotFoundHttpException;
        }

        return $this->render('show', [
                    'profile' => $profile
        ]);
    }

}
