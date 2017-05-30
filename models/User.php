<?php

namespace app\models;

use dektrium\user\models\User as BaseUser;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\log\Logger;
use yii\web\IdentityInterface;
use dektrium\user\helpers\ModuleTrait;
use dektrium\user\helpers\Password;
use app\models\Mailer;


class User extends BaseUser {
    /**
     * This method is used to register new user account. If Module::enableConfirmation is set true, this method
     * will generate new confirmation token and use mailer to send it to the user. Otherwise it will log the user in.
     * If Module::enableGeneratingPassword is set true, this method will generate new 8-char password. After saving user
     * to database, this method uses mailer component to send credentials (username and password) to user via email.
     *
     * @return bool
     */

    /**
     * @return bool Whether the user is confirmed or not.
     */
    /** @inheritdoc */
     public $role_id;
     public $company_id;
     public $name;
     
    /** @var string Default password regexp */
    public static $passwordRegexp = "/(?=^.{8,}$)(?=.*\d)(?=.*[!@#$%^&*]+)(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/";

     public function scenarios()
    {
        $scenarios = parent::scenarios();
        // add field to scenarios
        $scenarios['create'][]   = 'name';
        $scenarios['create'][]   = 'password';
        //$scenarios['update'][]   = 'name';
       // $scenarios['register'][] = 'field';
        return $scenarios;
    }
    
 /**
     * @return Mailer
     * @throws \yii\base\InvalidConfigException
     */
    protected function getMailer() {
        return Yii::$container->get(Mailer::className());
    }

    /** Validation rules for user
 
     */
    public function rules()
    {
        $rules = parent::rules();
        // add some rules
        $rules['nameRequired'] = ['name', 'required', 'on' => 'create'];
        $rules['passwordRequired'] = ['password', 'required', 'on' => ['register', 'create']];
        $rules['passwordLength'] = ['password', 'string', 'min' => 8, 'on' => ['register', 'create']];
        $rules['passwordMatch'] = ['password', 'match', 'pattern' => self::$passwordRegexp, 'message' => Yii::t('user', 'Password is invalid. Password should be of minimum 8 characters and should contain atleast 1 uppercase letter, 1 lowercase letter, 1 numeric value, 1 special character.')];
      //  $rules['fieldLength']   = ['field', 'string', 'max' => 10];

        return $rules;
    }
    /** databse table name for user
 
     */
    public static function tableName()
    {
        return '{{%user}}';
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne('\backend\models\Profile', ['user_id' => 'id']);
    }
    
    /** User list 
     * @return \yii\db\ActiveQuery
     */

    public function getUserList() 
    {
        
        $connection = \Yii::$app->db;

        $command = $connection->createCommand("select distinct(u.id),u.username,u.email,u.created_at,u.blocked_at,r.role_name from user as u left join acc_user_roles as ur on ur.user_id=u.id left join roles as r on r.id=ur.role_id");

        $dataReader = $command->queryAll();
        return $dataReader;
    }
    
    /** get the alertnate and primary email for the user
     * 
     * @return array $email email of given user.
     */
    
    public function getUserPrimaryAlternateEmail($userName){
        $emails=array();
        $connection = \Yii::$app->db;

        $command = $connection->createCommand("select u.email,pr.public_email from users u "
                . "inner join profile pr on pr.user_id=u.id where u.username='".$userName."'");

        $dataReader = $command->queryAll();
        foreach($dataReader as $row){
            $emails[]=$row['email'];
            $emails[]=$row['public_email'];
        }

        return $emails;
    }
    
    /** check if username exists
     * @param string $userName
     * 
     * @return mixed if exist return user_id otherwise null.
     */
 
    public function getUserByName($userName){
        //$emails=array();
        $connection = \Yii::$app->db;

        $command = $connection->createCommand("select u.id from users u where u.username='".$userName."'");

        $dataReader = $command->queryOne();
        
        if(isset($dataReader['id'])){
            return $dataReader['id'];
        }
        else {
            return NULL; 
        }
       
    }
    
    /** return current user role name if set otherwise return "-"  
     * 
     * @return mixed if exist return role_name otherwise "-".
     */
    
    public function getRoleName()
    {
       
        if($this->id)
        {
           // echo $this->id;die;
        $role= UserRoles::find()->select('roles.*')->leftJoin('roles', 'acc_user_roles.role_id=roles.id')->where(['acc_user_roles.user_id'=>$this->id])->asArray()->one();
        //var_dump($role);die;
      return  $role['role_name'];
        }else{
            
            return "-";
        }
        
    }
    
    /** return  user roles for given id
     *  @param integer $id
     * 
     * @return mixed if exist return user roles. 
     */
    
    public function getUserRole($id)
    {
        
           $connection = \Yii::$app->db;

           $command = $connection->createCommand("select group_concat(role_id) as roles from acc_user_roles  where user_id=".$id." group by user_id"
            );

            $dataReader = $command->queryAll();

           return $dataReader[0]['roles'];
        
    }
    
    /** delete the user role 
     *  @param integer $id
     *    
     */
    public function deleteuserrole($id)
    {
        $connection = \Yii::$app->db;

        $command = $connection->createCommand("delete from acc_user_roles where user_id=".$id );

        $dataReader = $command->execute();
        
    }
    
    /** update the user company 
     *  @param integer $user_id
     *  @param integer $company_id
     * 
     */
    
    public function updateUserCompany($user_id,$company_id)
    {
        $connection = \Yii::$app->db;

        $command = $connection->createCommand("update user_company_rel set company_id='".$company_id."' where user_id='".$user_id."'" );

        $dataReader = $command->execute();
        
        
    }
    
    /** get user company
     *  @param integer $user_id
     *   
     *  @return intger company id if exist otherwise 0.
     */
    
    public function getUserCompany($user_id)
    {
        $connection = \Yii::$app->db;
        $usercompany=  UserCompanyRel::find()->where(["user_id"=>$user_id])->asArray()->one();
        
        if($usercompany)
        {
            return $usercompany['company_id'];
            
        }else{
            
            return 0;
        }
        
        
    }
    
     /** Assign user role permission
     *  @param integer $user_id
     *  @param array $roleArray  role id array
    
     */
    
    public function AssignRolePermission($user_id,$roleArray)
    {
        
         $session = Yii::$app->session;
        $company=$session->get('user.company');
        
        $roleObj=  Roles::find()->where(['id'=>$roleArray])->where(["company_id"=>$company['id']])->asArray()->all();   
       
        if($roleObj)
        {
            
              $auth = Yii::$app->authManager;
              
              foreach($roleObj as $role)
              {
                  try{
                $r=$auth->createRole($role['role_code']) ;
               // $auth->add($r);
                $auth->assign($r, $user_id);
                  }catch(\yii\db\Exception $e){
                         Yii::warning("role allready assign");
                    }
              }
            
             
        }
        
    }
    
     /** Update user role permission
     *  @param integer $user_id
     *  @param array $roleArray  role id array
     *  @param array $userdbrole  updated role id array   
     */
    
    public function UpdateRolePermission($user_id,$roleArray,$userdbrole)
    {
        
         $session = Yii::$app->session;
        $company=$session->get('user.company');
        
        $roleObj=  Roles::find()->where(['id'=>$roleArray])->where(["company_id"=>$company['id']])->asArray()->all();       
        
        $userdbroleArray=explode(",",$userdbrole);
        
        if($roleObj)
        {
            
              $auth = Yii::$app->authManager;
              
              //assign user role
              foreach($roleObj as $role)
              {
                if(!in_array($role['id'],$userdbroleArray)) 
                {
                     try{
                    $r=$auth->createRole($role['role_code']) ;
                   // $auth->add($r);
                    $auth->assign($r, $user_id);
                     }catch(\yii\db\Exception $e){
                         Yii::warning("role allready assign");
                    }
                }
                
              }
              
               //revoke user roles
              foreach($userdbroleArray as $dbrole)
              {
                 
                if(!in_array($dbrole,$roleArray)) 
                {
                    $r1=  Roles::find()->where(['id'=>$dbrole])->asArray()->one(); 
                     try{
                    $r=$auth->createRole($r1['role_code']) ;
                    $auth->revoke($r, $user_id);
                     }catch(\yii\db\Exception $e){
                         Yii::warning("role allready remove");
                    }
                  
                }
              }
        }
        
    }
    
    /** get db role 
     *  @param integer $user_id  
     *  @return  mixed  $user_id if exist otherwise false  
     */
    
    public function getDBRole($user_id)
    {
         $connection = \Yii::$app->db;

        $command = $connection->createCommand("select group_concat(ur.role_id) as role_id from acc_user_roles as ur left join roles as r on ur.role_id=r.id where ur.user_id=".$user_id." group by ur.user_id");
       
        $dataReader = $command->queryAll();  
        
        if($dataReader)
        {
            return $dataReader[0]['role_id'];
        }else{
            
            return false;
        }
        
    }
    
    /**
     * Creates new user account. It generates password if it is not provided by user.
     *
     * @return bool
     */
    public function create()
    {
        if ($this->getIsNewRecord() == false) {
            throw new \RuntimeException('Calling "' . __CLASS__ . '::' . __METHOD__ . '" on existing user');
        }

        $this->confirmed_at = time();
        $this->password = $this->password == null ? Password::generate(8) : $this->password;

        $this->trigger(self::BEFORE_CREATE);

        if (!$this->save()) {
            return false;
        }
       // $mailer=new Mailer()

        $this->mailer->sendWelcomeMessage($this, null, true);
        $this->trigger(self::AFTER_CREATE);

        return true;
    }
   

    
   
    
}
