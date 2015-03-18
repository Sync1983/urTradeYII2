<?php
/**
 * Description of MongoUser
 *
 * @author Sync<atc58.ru>
 */
namespace app\models;
use Yii;
use yii\web\IdentityInterface;
use yii\mongodb\ActiveRecord;
use app\models\PartRecord;

class MongoUser extends ActiveRecord implements IdentityInterface {  
  protected $_list    = [];
  
  public static function createNew($login,$pass,$name="new name"){
    $old_user = MongoUser::findByUsername($login);
    if($old_user){
      return false;
    }
    
    $user = new MongoUser();
    $user->user_name      = $login;     //Логин
    $user->user_pass      = md5($pass); //Пароль
    $user->role           = "user";     //Роль пользователя      
    $user->over_price     = 20;         //Наценка для пользователя
    $user->type           = "private";  //Тип 
    $user->first_name     = $name?$name:$login; //Имя
    $user->second_name    = "";        //Фамилия
    $user->photo          = "";        //Адрес аватара
    $user->name           = "";        //Название фирмы
    $user->inn            = "";        //ИНН фирмы
    $user->kpp            = "";        //КПП фирмы
    $user->addres         = "";        //Адрес доставки
    $user->phone          = "";        //Телефон для связи
    $user->email          = "";        //Почта для связи
    $user->over_price_list= [];        //Список наценок пользователя      
    $user->basket         = [];        //Записи корзины
    $user->informer       = ["Списибо за регистрацию!"];        //Записи корзины
    $user->save();
    return $user;
  }
  
  public function addNotify($text){
    $arr = $this->informer;
    $arr[] = $text;
    $this->informer = $arr;
    $this->save();
  }
  /**
   * Возвращает отображаемое имя пользователя
   * @return string
   */
  public function getUserName(){
    if(Yii::$app->user->isGuest){
      return "guest";
    }
    if($this->getAttribute("type")=="company"){
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
   * Добавляет деталь в корзину пользователя
   * @param PartRecord $part
   */
  public function addPartToBasket($part,$notify=true){
    /* @var $basket_part PartRecord */
    if($notify){
      $this->addNotify("Деталь добавлена в корзину");
    }
    foreach ($this->_list as $basket_part){
      if($part->compare($basket_part)){
        $basket_part->setAttribute("sell_count", $basket_part->getAttribute("sell_count")+$part->getAttribute("sell_count"));
        return;
      }
    }
    $this->_list[] = $part;    
  }
  /**
   * Возвращает список деталей в корзине
   * @return array
   */
  public function getBasketParts(){
    return $this->_list;
  }

  public function attributes(){
    return [
      '_id', 
      'user_name',        //Логин
      'user_pass',        //Пароль
      'role',             //Роль пользователя      
      'over_price',       //Наценка для пользователя      
      'first_name',       //Имя
      'second_name',      //Фамилия
      'type',             //Тип
      'photo',            //Адрес аватара
      'name',             //Название фирмы
      'inn',              //ИНН фирмы
      'kpp',              //КПП фирмы
      'addres',           //Адрес доставки
      'phone',            //Телефон для связи
      'email',            //Почта для связи
      'over_price_list',  //Список наценок пользователя      
      'basket',            //Записи корзины
      'informer'          //Записи сообщений
      ];
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

  public function beforeSave($insert) {    
    $this->basket = [];    
    $list = [];
    foreach ($this->_list as $part) {      
      $list[] = $part->getAttributes();
    }
    
    $this->basket = $list;
    return parent::beforeSave($insert);
  }
  
  public function afterFind() {
    parent::afterFind();
    if(!$this->basket){
      return;
    }
    foreach ($this->basket as $part){
      $list_part = new PartRecord();
      $list_part->setAttributes($part,false);
      $this->_list[] = $list_part;
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
