<?php
/**
 * Description of MongoUser
 *
 * @author Sync<atc58.ru>
 */
namespace app\models;
use yii\web\IdentityInterface;
use yii\mongodb\ActiveRecord;

class MongoUser extends ActiveRecord implements IdentityInterface {
  
  public function attributes(){
    return ['_id', 'user_name', 'user_pass', 'role', 'sailt',
      'type','name','first_name','second_name','inn','kpp','addres','phone','email'];
  }  
  
  public static function collectionName(){
    return "users";
  }
  
  public function findByUsername($name){
    return MongoUser::findOne(['user_name'=>$name]);
  }
  
  public function validatePassword($password) {
    return $this->getAttribute('user_pass') === md5($password);
  }
  
  public function isAdmin(){
    return $this->getAttribute("role")==="admin";
  }

  // =================== Interface ====================
  public function getAuthKey() {
    return $this->getAttribute("sailt");
  }

  public function getId() {
    $id = $this->getAttribute("_id");    
    return $id->__toString();
  }

  public function validateAuthKey($authKey) {    
    echo "valid AK ($authKey)";
  }

  public static function findIdentity($id) {
    $result = MongoUser::findOne(["_id"=>new \MongoId($id)]);
    return $result;
  }

  public static function findIdentityByAccessToken($token, $type = null) {
    echo "find id by AT";
  }
}
