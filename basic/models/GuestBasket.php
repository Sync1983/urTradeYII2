<?php

/**
 * Description of GuestBasket
 * @author Sync<atc58.ru>
 */

namespace app\models;

use yii\mongodb\ActiveRecord;
use app\models\PartRecord;
use yii\web\Cookie;

class GuestBasket extends ActiveRecord{
  //public vars
  //protected vars
  public $_list =[];
  //private vars  
  //============================= Public =======================================
  /**
   * Возвращает ID гостевой корзины, сохраненной в куках
   * @return string basket_id 
   */
  public static function getIdFromCookie(){
    $cookie_basket = Yii::$app->getRequest()->getCookies()->getValue("basket",false);
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
  
  public static function setIdToCookie($basket_id){
    $cookie = new Cookie(['name'=>"basket"]);      
    $cookie->value = json_encode(strval($basket_id));
    $cookie->expire= time()+30*24*3600;
    Yii::$app->getResponse()->getCookies()->add($cookie); 
  }
  /**
   * Добавляет деталь в гостевую корзину с проверкой на дублирование
   * @param PartRecord $part
   * @return boolean
   */
  public function addPart($part){
    $count_change = false;
    /* @var $partA PartRecord */    
    foreach ($this->_list as $partA){
      if( $part->compare($partA) ){
        $partA->setAttribute("sell_count", $partA->getAttribute("sell_count")+$part->getAttribute("sell_count"));        
        $count_change = true;
      }      
    }
    if($count_change){
      return true;
    }    
    $this->_list[] = $part;
    return true;
  }
  
  public static function collectionName(){
    return "guest_basket";
  }  
  
  public function attributes(){
    return ['_id','update_time','basket'];
  }
  
  public function beforeSave($insert) {
    $this->update_time = time();
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
  //============================= Protected ====================================  
  //============================= Private ======================================
  //============================= Constructor - Destructor =====================
}
