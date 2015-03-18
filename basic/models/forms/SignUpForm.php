<?php

namespace app\models\forms;

use Yii;
use yii\base\Model;
use app\models\MongoUser;

/**
 * LoginForm is the model behind the login form.
 */
class SignUpForm extends Model
{
    public $username;
    public $userpass;    

    private $_user = false;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'userpass'], 'required'],            
            // password is validated by validatePassword()
            ['userpass', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
      
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            
            if ($user) {
                $this->addError($attribute, 'Данное имя пользователя уже используется.');
                return false;
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
      $user = $this->getUser();
      if(!$user){
        return false;
      }
      if($user->user_pass!=md5($this->userpass)) {
        return false;
      }
      return Yii::$app->user->login($user, 3600*24*30);       
    }
    
    /**
     * Create new User
     */
    public function createUser(){
      $user = MongoUser::createNew($this->username, $this->userpass, false);
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
