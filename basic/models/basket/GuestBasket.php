<?php

/**
 * Description of GuestBasket
 * @author Sync<atc58.ru>
 */

namespace app\models\basket;

use yii;
use app\models\basket\BasketModel;
use app\models\basket\GuestBasketRecord;
use app\models\basket\GuestBasketPart;
use yii\web\Cookie;
use MongoId;

class GuestBasket extends BasketModel{
  //public vars
  //protected vars  
  //private vars  
  /* @var $_basket_record GuestBasketRecord */
  private $_basket_record;
  protected $_type = 0;
  //============================= Public =======================================
  public function init() {
    parent::init();
    $id = self::getIdFromCookie();
    if(!$id){
      $this->_basket_record = new GuestBasketRecord();
      $this->_basket_record->save();
      $id = strval($this->_basket_record->getAttribute("_id"));
      self::setIdToCookie($id);
    }
    $this->_basket_record = self::getById($id);    
    if( !$this->_basket_record ){
      $this->_basket_record = new GuestBasketRecord();
      $this->_basket_record->save();
      $id = strval($this->_basket_record->getAttribute("_id"));
      self::setIdToCookie($id);
    }
    $this->_list = $this->buildList($this->_basket_record->basket);
    yii::$app->on(self::EVENT_CHANGE, [$this,"onSave"]);
  }
  
  public function onSave($event){
    $this->_basket_record->setAttribute("basket",$this->getList());    
    $this->_basket_record->save();
  }
  /**
   * Возвращает элемент Гостевая корзина по id
   * @param string $id
   * @return GuestBasket
   */
  public static function getById($id){
    return GuestBasketRecord::findOne(["_id"=> new MongoId($id)]);    
  }
  /**
   * Возвращает ID гостевой корзины, сохраненной в куках
   * @return string basket_id 
   */
  public static function getIdFromCookie(){
    $cookie_basket = yii::$app->getRequest()->getCookies()->getValue("basket",false);
    if(!$cookie_basket){
      return false;      
    }
    $basket_id = json_decode($cookie_basket);
    if(!$basket_id){
      return false;
    }
    $cookie = new Cookie(['name'=>"basket"]);      
    $cookie->value = json_encode($basket_id);
    $cookie->expire= time()+30*24*3600;
    Yii::$app->getResponse()->getCookies()->add($cookie); 
    return $basket_id;
  }
  /**
   * Устанавливает ID гостевой корзины
   * @param type $basket_id
   */
  public static function setIdToCookie($basket_id){
    $cookie = new Cookie(['name'=>"basket"]);      
    $cookie->value = json_encode(strval($basket_id));
    $cookie->expire= time()+30*24*3600;
    Yii::$app->getResponse()->getCookies()->add($cookie); 
  }
  /**
   * @inherit   
   */
  public function addTo($item, $count) {
    parent::addTo($item, $count);
    $this->_basket_record->setAttribute("basket",$this->getList());    
    $this->_basket_record->save();    
  }
  
  public function remove($key) {
    parent::remove($key);
    $this->_basket_record->setAttribute("basket",$this->getList());
    $this->_basket_record->save();
  }
  //============================= Protected ====================================  
  /**
   * Строит список деталей по входному массиву параметров
   * @param array $list
   * @return array BasketPart
   */
  protected function buildList($list = []){
    $result = [];
    if( !$list ){
      return [];
    }
    
    foreach ($list as $item){
      if(!isset($item["_id"])){
        continue;
      }
      foreach ($item as $key=>$value){
        if( is_array($value) && (count($value)==0) ){
          $item[$key] = null;
        }
      }
      $id = strval($item["_id"]);
      $new_item = new GuestBasketPart();
      $new_item->setAttributes($item);
      $result[$id] = $new_item;
    }
    return $result;
  }
  //============================= Private ======================================
  //============================= Constructor - Destructor =====================
}
