<?php

namespace app\models\forms;

use Yii;
use yii\base\Model;
use app\models\MongoUser;
use app\models\UserIdentity;

/**
 * LoginForm is the model behind the login form.
 */
class SignUpForm extends Model
{
    public $username;
    public $userpass;    
    public $passrepeat;    
    public $email;
	public $captcha;
	public $key;

    private $_user = false;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [            
            [['username', 'userpass', 'passrepeat', 'email', 'captcha'], 'required'],            
            ['username', 'validateUser'],
            ['userpass', 'validatePassword'],
            ['passrepeat', 'validateRepeat'],
            ['email', 'email'],
            ['key', 'string'],
            ['captcha', 'captcha'],
        ];
    }
	/**
	 * Метки атрибутов
	 * @return mixed
	 */
	public function attributeLabels() {
	  return [
			  'username'	=> "Логин",
			  'userpass'	=> "Пароль",
			  'passrepeat'	=> "Повторите пароль",
			  'email'		=> "Ваша почта (e-mail)",
			  'captcha'		=> " ",
	  ];
	}
	
    public function validatePassword($attribute, $params) {
      
	  if( !preg_match('/^[a-z0-9_-]{6,20}$/', $this->userpass) ){
		$this->addError($attribute, 'Поле может содержать только латинские буквы, цифры, знаки подчеркивания. Длина поля от 6 до 20 символов');
        return false;
	  }
	  return true;
    }
	
    public function validateRepeat($attribute, $params) {      
	  if ($this->userpass !== $this->passrepeat ) {
		  $this->addError($attribute, 'Поле не совпадает с паролем');
          return false;
      }
      return true;
    }
	
    public function validateUser($attribute, $params) {      
	  if ( $this->getUser() ) {
		  $this->addError($attribute, 'Пользователь с таким именем уже зарегестрирован');
          return false;
      }
	  if( !preg_match('/^[a-z0-9_-]{5,16}$/', $this->username) ){
		$this->addError($attribute, 'Поле может содержать только латинские буквы, цифры, знаки подчеркивания. Длина поля от 5 до 16 символов');
        return false;
	  }
      return true;
    }
    /**
     * Logs in a user using the provided username and password.
     * @return boolean whether the user is logged in successfully
     */
    public function login() {
      $user = $this->getUser();
      if(!$user){
        return false;
      }
      if($user->user_pass!=md5($this->userpass)) {
        return false;
      }
      return Yii::$app->user->login(new UserIdentity(), 3600*24*30);       
    }    
    /**
     * Create new User
     */
    public function createUser(){
      $user = MongoUser::createNew($this->username, $this->userpass,$this->username);
      if(!$user){
        return false;
      }
      $this->_user = $user;
      return true;
    }
    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = MongoUser::findByUsername($this->username);            
        }
        return $this->_user;
    }
}
