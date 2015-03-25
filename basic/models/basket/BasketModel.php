<?php

/**
 * Description of BasketModel
 * @author Sync<atc58.ru>
 */

namespace app\models\basket;

use yii\base\Model;
use app\models\basket\BasketPart;

class BasketModel extends Model{
  //public vars
  const EVENT_ADD_TO_BASKET = "USER_ADD_TO_BASKET_EVENT";
  const EVENT_REMOVE_FROM_BASKET = "USER_REMOVE_FROM_BASKET_EVENT";
  //protected vars
  protected $_list = [];
  protected $_errors = [];
  //private vars  
  //============================= Public =======================================
  public function __construct($config = array()) {
    if(isset($config["basket_list"])){
      var_dump($config["basket_list"]);
      $this->_list = $this->buildList($config["basket_list"]);
      unset($config['basket_list']);
    }
    parent::__construct($config);
  }
  /**
   * Добавить компонент в корзину
   * @param PartRecord $item
   * @return boolean
   */
  public function addTo($item, $count){
    if( !is_subclass_of($item, "PartRecord") ){
      return false;
    }
    $key = $item->getStrID();
    $new_item = new BasketPart();
    $new_item->setAttributes($item->getAttributes());
    if(!$new_item->validate()){
      $this->_errors = array_merge($this->_errors,$new_item->getErrors());
      return false;
    }
    if( !isset($this->_list[$key]) ){
      $this->_list[$key] = $new_item;
      return true;
    } else {
      $new_count = $this->_list[$key]->getAttribute("sell_count") + $count;
      $this->_list[$key]->setAttribute("sell_count", $new_count);
      if( !$this->_list[$key]->validate() ) {
        $this->_errors = array_merge($this->_errors,  $this->_list[$key]->getErrors());
        return false;
      }
      return true;
    }
    return false;
  }
  /**
   * Удаляет деталь по ключу
   * @param string $key
   * @return boolean
   */
  public function remove($key){
    unset($this->_list[$key]);    
    $this->_list = array_values($this->_list);
    return true;
  }
  
  public function getList(){
    $result = [];
    foreach ($this->_list as $item){
      $result[] = $item->getAttributes();
    }
    return $result;
  }
  
  public function getRawList(){
    return $this->_list;
  }

  //============================= Protected ====================================
  protected function buildList($list = []){
    $result = [];
    foreach ($list as $item){
      if(!isset($item["_id"])){
        continue;
      }
      $id = strval($item["_id"]);
      $new_item = new BasketPart();
      $new_item->setAttributes($item);
      $result[$id] = $new_item;
    }
    return $result;
  }
  //============================= Private ======================================
  //============================= Constructor - Destructor =====================
}
