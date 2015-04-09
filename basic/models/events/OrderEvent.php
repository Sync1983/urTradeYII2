<?php

/**
 * Description of AddToBasketEvent
 * @author Sync<atc58.ru>
 */
namespace app\models\events;
use yii\base\Event;

class OrderEvent extends Event{  
  const EVENT_ORDER_ADD     = "USER_ORDER_ADD_EVENT";
  const EVENT_ORDER_REMOVE  = "USER_ORDER_REMOVE_EVENT";
  const EVENT_ORDER_PAY     = "USER_ORDER_PAY_EVENT";
  const EVENT_ORDER_CHANGE  = "USER_ORDER_CHANGE_EVENT";
  //public vars  
  public $items = [];
  public $key;  
  //protected vars
  //private vars  
  //============================= Public =======================================
  //put your code here
  //============================= Protected ====================================
  //============================= Private ======================================
  //============================= Constructor - Destructor =====================
}
