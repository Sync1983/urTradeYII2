<?php
/**
 * Description of MongoUser
 *
 * @author Sync<atc58.ru>
 */
namespace app\models;
use yii;
use yii\web\IdentityInterface;
use yii\mongodb\ActiveRecord;

class MongoUser extends ActiveRecord implements IdentityInterface {
  
  public function attributes(){
    return ['_id', 'user_name', 'user_pass', 'role', 'sailt',
      'type','name','first_name','second_name','inn','kpp','addres','phone','email','over_price_list'];
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
  
  /**
   * Возвращает отображаемое имя пользователя
   * @return string
   */
  public function getUserName(){
    if(Yii::$app->user->isGuest){
      return "guest";
    }
    if($this->getAttribute("type")==0){
      return $this->getAttribute("name");
    }
    return $this->getAttribute("first_name"). " " .$this->getAttribute("second_name");
  }

    /**
   * Проверяет имеет ли пользователь права администратора
   * @return boolean 
   * **/
  public function isAdmin(){
    return $this->getAttribute("role")==="admin";
  }
  
  /**
   * Получает список установленных пользователем наценок
   * @return array
   * **/
  public function getOverPiceList(){
    return $this->getAttribute("over_price_list");
  }
  
  /**
   * Сохраняет список установленных пользователем наценок
   * @return boolena
   * **/
  public function setOverPiceList($list){
    if(is_array($list)){
      $this->setAttribute("over_price_list",$list);
    }
  }

  // =================== Interface ====================
  public function getAuthKey() {
    return $this->getAttribute("sailt");
  }

  public function getId() {
    $id = $this->getAttribute("_id");    
    return $id->__toString();
  }
  
  public function getObjectId(){    
    return $this->getAttribute("_id");    
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
