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

class SiteUser extends User{
  /* @var $_guest_basket GuestBasket */
  protected $_guest_basket;
  /* @var $_user_basket BasketModel */
  protected $_user_basket;
  
  public function getId(){
    if($this->isGuest){
      return 0;
    }
    $identity = $this->getIdentity();
    /* @var $identity MongoUser */
    return $identity->getId();    
  }
   /**
   * Возвращает логин пользователя
   * @return string
   */
  public function getLogin(){
    if($this->isGuest){
      return "";
    }
    $identity = $this->getIdentity();
    /* @var $identity MongoUser */
    return $identity->getAttribute("user_name");
  }
  /**
   * Возвращает Имя пользователя
   * @return string
   */
  public function getCaption(){
    if($this->isGuest){
      return "";
    }
    $identity = $this->getIdentity();
    /* @var $identity MongoUser */
    return $identity->getUserName();
  }
  /**
   * Возвращает Название компании
   * @return string
   */
  public function getCompanyName(){
    if($this->isGuest){
      return "";
    }
    $identity = $this->getIdentity();
    /* @var $identity MongoUser */
    return $identity->getAttribute("name");
  }
  /**
   * Поверяет имеет ли пользователь права администратора
   */
  public function isAdmin(){
    if($this->isGuest){
      return false;
    }
    $identity = $this->getIdentity();
    /* @var $identity MongoUser */
    return $identity->isAdmin();    
  }
  /**
   * Проверяет является ли пользователь компанией
   * @return boolean
   */
  public function isCompany(){
    if($this->isGuest){
      return false;
    }
    $identity = $this->getIdentity();
    /* @var $identity MongoUser */
    return $identity->type=="company";    
  }
  /**
   * Возвращает список наценок пользователя
   * @return type
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
    $start_price = floatval($start_price);
    $over_price = 0;    
    if (!$this->isGuest) {
      $over_price = $this->getIdentity()->getAttribute("over_price");
      if(!$over_price){
        $over_price = 18;
      }
    } else {
      $over_price = isset(yii::$app->params['guestOverPrice'])?yii::$app->params['guestOverPrice']:18;
    }    
    return $start_price + round($over_price*$start_price/100,2);
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
  
  public function init() {
    parent::init();
    $this->_guest_basket = new GuestBasket();
    $this->_user_basket = new BasketModel();
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
