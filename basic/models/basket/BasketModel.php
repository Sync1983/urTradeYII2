<?php

/**
 * Description of BasketModel
 * @author Sync<atc58.ru>
 */

namespace app\models\basket;
use yii;
use yii\base\Model;
use app\models\basket\BasketPart;
use app\models\events\NotifyEvent;

class BasketModel extends Model{
  //public vars
  const EVENT_ADD_TO_BASKET = "USER_ADD_TO_BASKET_EVENT";
  const EVENT_REMOVE_FROM_BASKET = "USER_REMOVE_FROM_BASKET_EVENT";
  const EVENT_ADD_TO_BASKET_LIST = "USER_ADD_TO_BASKET_LIST_EVENT";
  const EVENT_REMOVE_FROM_BASKET_LIST = "USER_REMOVE_FROM_BASKET_LIST_EVENT";
  const EVENT_CHANGE = "USER_BASKET_CHANGE_EVENT";
  const EVENT_CHANGE_FIELDS = "USER_BASKET_CHANGE_FIELDS_EVENT";
  
  //protected vars
  protected $_list = [];
  protected $_errors = [];
  protected $_type = 1;
  //private vars  
  //============================= Public =======================================
  public function __construct($config = array()) {
    if(isset($config["basket_list"])){      
      $this->_list = $this->buildList($config["basket_list"]);
      unset($config['basket_list']);
    }
    parent::__construct($config);
  }
  
  public function init() {
    parent::init();
    $this->initListner();
  }
  /**
   * Устанавливает список записей корзины
   * @param array $list
   */
  public function setList($list = []){
    $this->_list = $this->buildList($list);
  }
  /**
   * Добавить компонент в корзину
   * @param PartRecord $item
   * @return boolean
   */
  public function addTo($item, $count){
    if( !is_a($item, BasketPart::class) ){
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
      $this->_list[$key]->setAttribute("sell_count", $count);
      yii::$app->trigger(self::EVENT_CHANGE);
      return true;
    } else {
      $old_count = $this->_list[$key]->getAttribute("sell_count");
      $new_count = ($old_count?$old_count:0) + $count;      
      $this->_list[$key]->setAttribute("sell_count", $new_count);
      if( !$this->_list[$key]->validate() ) {
        $this->_errors = array_merge($this->_errors,  $this->_list[$key]->getErrors());
        return false;
      }      
      yii::$app->trigger(self::EVENT_CHANGE);
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
    yii::$app->trigger(self::EVENT_CHANGE);
    return true;
  }
  /**
   * Возвращает массив аттрибутов деталей
   * @return array
   */
  public function getList(){
    $result = [];
    foreach ($this->_list as $item){
      $result[] = $item->getAttributes();
    }
    return $result;
  }
  /**
   * Возвращает массив деталей
   * @return array
   */
  public function getRawList(){
    return $this->_list;
  }
  
  public function getPartById($key){
    return isset($this->_list[$key])?$this->_list[$key]:false;
  }

  //============================= Protected ====================================
  /**
   * Обработчик события "Добавить в базу"
   * @param \app\models\events\BasketEvent $event
   */
  protected function onAdd($event){    
    yii::info("Event Trigger ".$event->name);
    if($event->type!= $this->_type){
      return false;
    }
    if( !is_a($event->params, BasketPart::class)) {
      $part = \app\models\PartRecord::getById($event->key);
      $basket_part = new BasketPart();
      $basket_part->setAttributes($part->getAttributes());
      $basket_part->setAttribute("price_change", $event->params["price_change"]);
      $basket_part->setAttribute("sell_count", $event->params["sell_count"]);
      $basket_part->setAttribute("for_user", \yii::$app->user->getId());
    } else {
      $basket_part = $event->params;
    }    
    if( $this->addTo($basket_part, $basket_part->getAttribute("sell_count")) ){
      $notify_event = new NotifyEvent();
      $notify_event->text = "Деталь добавлена";
      yii::$app->user->trigger(NotifyEvent::USER_NOTIFY_EVENT, $notify_event);
    }
  }
  /**
   * Обработчик события "Добавить в базу список"
   * @param \app\models\events\BasketEvent $event
   */
  protected function onAddList($event){    
    yii::info("Event Trigger ".$event->name);
    if($event->type!= $this->_type){
      return false;
    }
    $items = $event->params['items'];
    $cnt = 0;
    foreach ($items as $item){      
      if( $this->addTo($item, $item->getAttribute("sell_count")) ){
        $cnt ++;
      }
    }    
    $notify_event = new NotifyEvent();
    $notify_event->text = $cnt. " деталей добавлено";
    yii::$app->user->trigger(NotifyEvent::USER_NOTIFY_EVENT, $notify_event);
  }
  /**
   * Слушатель события "Удалить"
   * @param \app\models\events\BasketEvent $event
   * @return boolean
   */
  protected function onRemove($event){    
    yii::info("Event Trigger ".$event->name);
    if($event->type!= $this->_type){
      return false;
    }
    $this->remove($event->key);
    $notify_event = new NotifyEvent();
    $notify_event->text = "Деталь удалена";
    yii::$app->user->trigger(NotifyEvent::USER_NOTIFY_EVENT,$notify_event);
  }
  /**
   * Слушатель события "Удалить список"
   * @param \app\models\events\BasketEvent $event
   * @return boolean
   */
  protected function onRemoveList($event){    
    if($event->type!= $this->_type){
      return false;
    }
    $keys = $event->params["keys"];
    $cnt = 0;
    foreach ($keys as $key){      
     $this->remove($key);
     $cnt ++;
    }    
    $notify_event = new NotifyEvent();
    $notify_event->text = $cnt . " деталей удалено";
    yii::$app->user->trigger(NotifyEvent::USER_NOTIFY_EVENT,$notify_event);
  }
  /**
   * Изменяет поля записи в корзине
   * @param \app\models\events\BasketEvent $event
   */
  protected function onFieldsChange($event){  
    if( $this->_type !== $event->type ){
      return;
    }
    
    if(!isset($this->_list[$event->key])){
      echo json_encode(['output'=>0,'message'=>'Запись не найдена']);
      return false;
    }
    
    $item = &$this->_list[$event->key];
    foreach ($event->params as $param_name => $param_value){
      $item->setAttribute($param_name, $param_value);
      echo json_encode(['output'=>$param_value]);
    }
    yii::$app->trigger(self::EVENT_CHANGE);    
    yii::$app->user->trigger(NotifyEvent::USER_NOTIFY_EVENT, new NotifyEvent(['text'=>"Изменения записаны"]));
  }

  protected function initListner(){
    yii::$app->on(self::EVENT_ADD_TO_BASKET, [$this,'onAdd']);
    yii::$app->on(self::EVENT_ADD_TO_BASKET_LIST, [$this,'onAddList']);
    yii::$app->on(self::EVENT_REMOVE_FROM_BASKET, [$this,'onRemove']);
    yii::$app->on(self::EVENT_REMOVE_FROM_BASKET_LIST, [$this,'onRemoveList']);
    yii::$app->on(self::EVENT_CHANGE_FIELDS, [$this,'onFieldsChange']);    
  }
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
      $new_item = new BasketPart();
      $new_item->setAttributes($item);
      $result[$id] = $new_item;
    }
    return $result;
  }
  //============================= Private ======================================
  //============================= Constructor - Destructor =====================
}
