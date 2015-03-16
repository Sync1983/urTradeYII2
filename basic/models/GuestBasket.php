<?php

/**
 * Description of GuestBasket
 * @author Sync<atc58.ru>
 */

namespace app\models;

use yii\mongodb\ActiveRecord;
use app\models\PartRecord;

class GuestBasket extends ActiveRecord{
  //public vars
  //protected vars
  public $_list =[];
  //private vars  
  //============================= Public =======================================
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
