<?php

/**
 * Description of SiteUser
 *
 * @author Sync<atc58.ru>
 */
namespace app\models;

use yii;
use yii\web\User;
use app\models\MongoUser;
use app\models\basket\GuestBasket;
use app\models\basket\BasketModel;
use app\models\balance\BalanceModel;
use app\components\behaviors\NotificationBehavior;
use yii\helpers\ArrayHelper;

class SiteUser extends User{
  /* @var $_guest_basket GuestBasket */
  protected $_guest_basket;
  /* @var $_user_basket BasketModel */
  protected $_user_basket;
  /* @var $_balance BalanceModel */
  protected $_balance;
  /* @var $_record MongoUser */
  protected $_record;


  public function getId(){
    if($this->isGuest){
      return 0;
    }    
    return $this->getIdentity()->getId();    
  }
   /**
   * Возвращает логин пользователя
   * @return string
   */
  public function getLogin(){
    if($this->isGuest){
      return "";
    }    
    return $this->getIdentity()->getAttribute("user_name");
  }
  /**
   * Возвращает Имя пользователя
   * @return string
   */
  public function getCaption(){
    if($this->isGuest){
      return "";
    }    
    return $this->getIdentity()->getUserName();
  }
  /**
   * Возвращает Название компании
   * @return string
   */
  public function getCompanyName(){
    if($this->isGuest){
      return "";
    }    
    return $this->getIdentity()->getAttribute("name");
  }
  /**
   * Поверяет имеет ли пользователь права администратора
   * @return boolean
   */
  public function isAdmin(){
    if($this->isGuest){
      return false;
    }    
    return $this->getIdentity()->isAdmin();    
  }
  /**
   * Проверяет является ли пользователь компанией
   * @return boolean
   */
  public function isCompany(){
    if($this->isGuest){
      return false;
    }    
    return $this->getIdentity()->type=="company";    
  }
  /**
   * Возвращает список наценок пользователя
   * @return array
   */
  public function getOverPiceList(){
    if($this->isGuest){
      return ["Без наценки"=>0];
    }
    $identity = $this->getIdentity();
    /* @var $identity MongoUser */
    return array_merge($identity->getOverPiceList(),["Без наценки"=>0]);
  }
  /**
   * Сохраняет список наценок
   * @param type $list
   * @return boolean
   */
  public function saveOverPriceList($list = []){
    if($this->isGuest){
      return false;
    }
    $identity = $this->getIdentity();
    $identity->setAttribute('over_price_list', $list);
    return $identity->save();
  }
  /**
   * Пересчитывает сумму start_price с наценкой пользователя или
   * общей наценкой для гостей
   * @param float $start_price
   * @return float
   * @throws \yii\base\InvalidValueException
   */
  public function getUserPrice($start_price){
    if(!is_numeric($start_price)){
      throw new \yii\base\InvalidValueException("Ошибка в формате цены");
    }
    
    $over_price = ArrayHelper::getValue(yii::$app->params, 'guestOverPrice', 18.0);
    
    if ( !$this->isGuest && $this->getIdentity()->hasAttribute("over_price")) {
      $over_price = $this->getIdentity()->getAttribute("over_price") * 1.0;      
    }
    
    return $start_price + round($over_price*$start_price*1.0/100.0,2);
  }
  /**
   * Возвращает список деталей в корзине
   * @return array
   */
  public function getBasketParts(){    
    return $this->_user_basket->getRawList();
  }
  /**
   * Возвращает список деталей в гостевой корзине
   * @return array
   */
  public function getGuestBasketParts(){    
    return $this->_guest_basket->getRawList();
  }
  /**
   * Возвращает деталь из гостевой корзины по ID
   * @param string $key
   * @return basket\BasketPart
   */
  public function getGuestBasketPart($key){    
    return $this->_guest_basket->getPartById($key);
  }
  /**
   * Возвращает деталь из корзины по ID
   * @param string $key
   * @return basket\BasketPart
   */
  public function getBasketPart($key){    
    return $this->_user_basket->getPartById($key);
  }
  /**
   * Возвращает модель баланс пользователя
   * @return BalanceModel
   */
  public function getBalance(){
    return $this->_balance;
  }
  
  //============================================================================
  public function behaviors(){
    return [
      NotificationBehavior::className(),
    ];
  }

  public function init() {
    parent::init();
    $this->_guest_basket  = new GuestBasket();
    $this->_user_basket   = new BasketModel();
    $this->_balance       = new BalanceModel();
    if(!$this->isGuest){
      $this->_user_basket->setList($this->identity->basket);
    }
    Yii::$app->on(basket\BasketModel::EVENT_CHANGE, [$this,"onSave"]);
  }
  
  public function onSave($event){
    $user = $this->identity;
    if( !$user ){
      return;
    }
    $user->basket = $this->_user_basket->getList();
    $user->save();
  }
  
  public function afterLogin($identity, $cookieBased, $duration) {    
    parent::afterLogin($identity, $cookieBased, $duration);    
    $this->_user_basket->setList($identity->basket);    
  }
}
