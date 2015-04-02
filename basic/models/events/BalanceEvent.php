<?php

/**
 * Description of BalanceEvent
 * @author Sync<atc58.ru>
 */
namespace app\models\events;
use yii\base\Event;

class BalanceEvent extends Event{
  const EVENT_ADD_BALANCE = "USER_EVENT_ADD_BALANCE";
  const EVENT_DEC_BALANCE = "USER_EVENT_DEC_BALANCE";
  const EVENT_BALANCE_CHANGE = "USER_EVENT_BALANCE_CHANGE";
  const EVENT_BALANCE_REJECT = "USER_EVENT_BALANCE_REJECT";
  
  const STATUS_REJECTED = 10;
  const STATUS_OK = 200;
  const STATUS_MONEY_DEC_OK = 300;
  const STATUS_MONEY_ADD_OK = 400;
  
  //public vars
  public $initiator;
  public $reciver;
  public $item;
  public $status;
  public $value;
  //protected vars
  //private vars  
  //============================= Public =======================================
  public function serialize(){    
    return json_encode([
      'initiator' => $this->initiator->getAttribute("_id"),
      'reciver'   => $this->reciver->getAttribute("_id"),
      'item'      => $this->item->getAttribute("_id"),
      'status'    => $this->status * 1,
      'value'     => $this->value * 1.0
    ]);
  }
  //============================= Protected ====================================
  //============================= Private ======================================
  //============================= Constructor - Destructor =====================

}
