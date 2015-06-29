<?php
/**
 * Description of MongoUser
 *
 * @author Sync<atc58.ru>
 */
namespace app\models;

use yii\web\IdentityInterface;
use yii\mongodb\ActiveRecord;
use MongoId;
use yii\helpers\ArrayHelper;

class MongoUser extends ActiveRecord implements IdentityInterface {    
  

  public static function createNew($login,$pass,$name="new name"){
    $old_user = MongoUser::findByUsername($login);
    if($old_user && (bool) $login && (bool) $pass){
      return false;
    }
    
    $user = new MongoUser();
    $user->user_name      = $login?$login:$name;     //Логин
    $user->user_pass      = md5($pass); //Пароль
    $user->role           = "user";     //Роль пользователя      
    $user->over_price     = ArrayHelper::getValue(\yii::$app->params, 'guestOverPrice', 18.0);  //Наценка для пользователя
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
    $user->credit         = 0.0;       //Кредит пользователя
    $user->over_price_list= [];        //Список наценок пользователя      
    $user->basket         = [];        //Записи корзины
    $user->informer       = ["Спасибо за регистрацию!"];        //Записи информера
    $user->is_init		  = false;     //Проведена ли начальная настройка
    if( $user->insert() ){      
      return $user;
    }
    return FALSE;
  }  
  
  /**
   * Возвращает отображаемое имя пользователя
   * @return string
   */
  public function getUserName(){      
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
   * Проверяет является ли пользователь компанией
   * @return boolean
   */
  public function isCompany(){
    return $this->type=="company";
  }
  /**
   * Получает список установленных пользователем наценок
   * @return array
   * **/
  public function getOverPiceList(){
    return $this->getAttribute("over_price_list")?$this->getAttribute("over_price_list"):[];
  }
  /**
   * Пересчитывает сумму start_price с наценкой пользователя или
   * общей наценкой для гостей
   * @param float $start_price
   * @return float
   * @throws \yii\base\InvalidValueException
   */
  public function getUserPrice($start_price){
    if( !is_numeric($start_price) ){
      throw new \yii\base\InvalidValueException("Ошибка в формате цены");
    }
    
    $over_price = \yii\helpers\ArrayHelper::getValue(\yii::$app->params, 'guestOverPrice', 18.0);
    
    if ( $this->hasAttribute("over_price") ) {
      $over_price = $this->getAttribute("over_price") * 1.0;      
    }
    
    return $start_price + round($over_price*$start_price*1.0/100.0,2);
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
      'credit',           //Кредит пользователя
      'basket',           //Записи корзины
      'informer',          //Записи сообщений
      'is_init'           //Проведены ли начальные настройки
      ];
  }
  
  public function rules(){
    return[
      [
        ['user_name','user_pass','role','over_price','first_name','second_name',
         'type','photo','name','inn','kpp','addres','phone','email','credit','is_init'],'safe'
      ],
    ];
  }

  public static function collectionName(){
    return "users";
  }
  
  public static function findByUsername($name){    
    return (bool) $name?MongoUser::findOne(['user_name'=>$name]):false;
  }
  
  public function validatePassword($password) {
    return ( $this->getAttribute('user_pass') === md5($password) ) && ( (bool) $password);
  }  
  
  // =================== Interface ====================
  public function getAuthKey() {
    return md5($this->getId().$this->getUserName().$this->getAttribute("role"));
  }

  public function getId() {
    $id = $this->getAttribute("_id");    
    return $id->__toString();
  }
  
  public function getObjectId(){    
    return $this->getAttribute("_id");    
  }

  public function validateAuthKey($authKey) {
	return md5($this->getId().$this->getUserName().$this->getAttribute("role")) === $authKey;
  }

  public static function findIdentity($id) {
    $result = MongoUser::findOne(["_id"=>new MongoId($id)]);
    return $result;
  }

  public static function findIdentityByAccessToken($token, $type = null) {
	\yii::trace("Id by token");
    echo "find id by AT";
  }
}
