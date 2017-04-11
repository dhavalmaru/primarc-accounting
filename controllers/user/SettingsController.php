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

use dektrium\user\controllers\SettingsController as BaseSettingsController;
use Yii;
use dektrium\user\Module;
use yii\authclient\ClientInterface;
use yii\helpers\Url;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\web\UploadedFile;
use backend\models\SettingsForm;
use yii\helpers\ArrayHelper;

/**
 * SettingsController manages updating user settings (e.g. profile, email and password).
 *
 * @property \dektrium\user\Module $module
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class SettingsController extends BaseSettingsController {

    /**
     * @inheritdoc
     */
    public $defaultAction = 'profile';

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'disconnect' => ['post']
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['profile', 'account', 'userinformation', 'confirm', 'networks', 'connect', 'disconnect', 'changepassword'],
                        'roles' => ['@']
                    ],
                ]
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions() {
        return [
            'connect' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'connect'],
            ]
        ];
    }

    /**
     * Shows profile settings form.
     *
     * @return string|\yii\web\Response
     */
    public function actionProfile() {
        $id = Yii::$app->request->get('id');
        $model = $this->module->manager->findProfileById($id);

        // var_dump($model);

        if (\Yii::$app->request->isAjax && $model->load(\Yii::$app->request->post())) {

            \Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(\Yii::$app->getRequest()->post()) && $model->save()) {

            $image = UploadedFile::getInstance($model, 'image');
            // var_dump($image);die;
            // store the source file name
            if (isset($image)) {
                $model->image = $image->name;
                $ext = end((explode(".", $image->name)));

                // generate a unique file name
                $model->avatar = Yii::$app->security->generateRandomString() . ".{$ext}";

                // the path to save file, you can set an uploadPath
                // in Yii::$app->params (as used in example below)
                $path = Yii::$app->basePath . "/web/" . Yii::$app->params['uploadPath'] . "/" . $model->avatar;

                if ($model->save()) {
                    $image->saveAs($path);
                }
            }
            \Yii::$app->getSession()->setFlash('success', \Yii::t('user', 'Profile settings have been successfully saved'));
            return $this->refresh();
        }

        return $this->render('profile', [
                    'model' => $model,
                    'id' => $id,
        ]);
    }

    public function actionUserinformation() {

        // $model = $this->module->manager->createUser(['scenario' => 'create']);
        $id = Yii::$app->request->get('id');

        $userLogin = Yii::$app->user->identity;
        $user_id = $userLogin->id;
        $userObj = new \backend\models\Users();

        $user = $userObj->find()->asArray()->where(['id' => $id])->one();

        $companyArray = \backend\models\Company::find()->select('category_id')->asArray()->where(['id' => $user['company_id']])->one();
        $roleObj = new \backend\models\Roles();



        $companyObj = new \backend\models\Company();
        $userroleObj = new \backend\models\UserRoles();
        $role_code = $userroleObj->getCustomerRole($user_id);
        if (strcmp($role_code, "superadm") == 0) {

            $companyListModel = $companyObj->getAllCompanyDetails();
            $roleIdArray = $roleObj->getCategoryRole($companyArray['category_id']);
            $roleArray = $roleObj->find()->asArray()->where(['id' => $this->getRoleId($roleIdArray)])->all();
        } else {
            $companyListModel = $companyObj->getCompanyDetailsById($user['company_id']);
            $roleId = $userroleObj->getUserRole($id);
            $roleArray = $roleObj->find()->asArray()->where(['id' => $roleId])->all();
        }

        $companyList = ArrayHelper::map($companyListModel, 'id', 'company_name');
        $rolelist = $roleObj->getRoleList($roleArray);

        $userRoleObj = new \backend\models\UserRoles();
        $role_id = $userRoleObj->getUserRole($id);

        $model = $this->findModel($id);
        $model->scenario = 'update';
        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            \Yii::$app->getSession()->setFlash('user.success', \Yii::t('user', 'User has been updated'));
            return $this->refresh();
        }
        return $this->render('update', [
                    'model' => $model,
                    'companyList' => $companyList,
                    'RoleObj' => $roleObj,
                    'rolelist' => $rolelist,
                    'id' => $id,
                    'role_id' => $role_id
        ]);
    }

    public function getRoleId($roleIdArray) {

        $roleid = array();
        foreach ($roleIdArray as $rid) {

            array_push($roleid, $rid['role_id']);
        }

        return $roleid;
    }

    /**
     * Displays page where user can update account settings (username, email or password).
     *
     * @return string|\yii\web\Response
     */
    public function actionAccount() {
        $id = Yii::$app->request->get('id');

        $model = $this->module->manager->createSettingsForm();

        // var_dump($model);die;
        if (\Yii::$app->request->isAjax && $model->load(\Yii::$app->request->post())) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(\Yii::$app->getRequest()->post()) && $model->save()) {
            \Yii::$app->session->setFlash('success', \Yii::t('user', 'password change successfully'));
            return $this->refresh();
        }

        return $this->render('account', [
                    'model' => $model,
                    'id' => $id,
        ]);
    }

    public function actionChangepassword() {
        $id = Yii::$app->request->get('id');

        $settingObj = new SettingsForm();

        $model = $settingObj->getUserInfo($id);

        if (\Yii::$app->request->isAjax && $model->load(\Yii::$app->request->post())) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(\Yii::$app->getRequest()->post()) && $model->save()) {
            \Yii::$app->session->setFlash('success', \Yii::t('user', 'password change successfully'));
            return $this->refresh();
        }

        return $this->render('changepassword', [
                    'model' => $model,
                    'id' => $id,
        ]);
    }

    /**
     * Attempts changing user's password.
     *
     * @param  integer $id
     * @param  string  $code
     * @return string
     * @throws \yii\web\HttpException
     */
    public function actionConfirm($id, $code) {
        $user = $this->module->manager->findUserById($id);

        if ($user === null || $this->module->emailChangeStrategy == Module::STRATEGY_INSECURE) {
            throw new NotFoundHttpException;
        }

        if ($user->attemptEmailChange($code)) {
            \Yii::$app->session->setFlash('success', \Yii::t('user', 'Your email has been successfully changed'));
        } else {
            \Yii::$app->session->setFlash('danger', \Yii::t('user', 'Your confirmation token is invalid'));
        }

        return $this->redirect('account');
    }

    /**
     * Displays list of connected network accounts.
     * 
     * @return string
     */
    public function actionNetworks() {
        return $this->render('networks', [
                    'user' => \Yii::$app->user->identity
        ]);
    }

    /**
     * Disconnects a network account from user.
     *
     * @param  integer $id
     * @return \yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     * @throws \yii\web\ForbiddenHttpException
     */
    public function actionDisconnect($id) {
        $account = $this->module->manager->findAccountById($id);
        if ($account === null) {
            throw new NotFoundHttpException;
        }
        if ($account->user_id != \Yii::$app->user->id) {
            throw new ForbiddenHttpException;
        }
        $account->delete();

        return $this->redirect(['networks']);
    }

    /**
     * Connects social account to user.
     *
     * @param  ClientInterface $client
     * @return \yii\web\Response
     */
    public function connect(ClientInterface $client) {
        $attributes = $client->getUserAttributes();
        $provider = $client->getId();
        $clientId = $attributes['id'];

        if (null === ($account = $this->module->manager->findAccount($provider, $clientId))) {
            $account = $this->module->manager->createAccount([
                'provider' => $provider,
                'client_id' => $clientId,
                'data' => json_encode($attributes),
                'user_id' => \Yii::$app->user->id
            ]);
            $account->save(false);
            \Yii::$app->session->setFlash('success', \Yii::t('user', 'Account has been successfully connected'));
        } else {
            \Yii::$app->session->setFlash('error', \Yii::t('user', 'This account has already been connected to another user'));
        }

        $this->action->successUrl = Url::to(['/user/settings/networks']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param  integer                    $id
     * @return \dektrium\user\models\User the loaded model
     * @throws NotFoundHttpException      if the model cannot be found
     */
    protected function findModel($id) {
        $user = $this->module->manager->findUserById($id);

        if ($user === null) {
            throw new NotFoundHttpException('The requested page does not exist');
        }

        return $user;
    }

}
