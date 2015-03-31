<?php

/**
 * Description of NotificationBehavior
 * @author Sync<atc58.ru>
 */

namespace app\components\behaviors;

use yii;
use yii\base\Behavior;
use app\models\events\NotifyEvent;
use app\models\events\BalanceEvent;
use app\models\balance\BalanceRecord;

class BalanceBehavior extends Behavior{
  const YANDEX_ID = "ya";
  //public vars
  //protected vars  
  //private vars  
  //============================= Public =======================================
  
  //============================= Protected ====================================
  /**
   * Слушатель события пополнения баланса
   * @param BalanceEvent $event
   */
  protected function onAddBalance(BalanceEvent $event){
    \yii::info("Trying add to balance for event: ".json_encode($event), 'balance');
    $record = new BalanceRecord();
    $record->setAttribute("operation", BalanceRecord::OP_ADD);
    $record->setAttribute("value", $event->value);
    $record->setAttribute("comment", $event->value);
    
    $record->setAttribute("item_type", BalanceRecord::IT_UNKNOW);
    $record->setAttribute("item_id", "none");
    
    $record->setAttribute("init_type", BalanceRecord::IT_UNKNOW);
    $record->setAttribute("reciver_type", BalanceRecord::IT_UNKNOW);
    
    if($event->initiator instanceof \app\models\MongoUser){
      $record->setAttribute("init_type", BalanceRecord::IT_USER);
      $record->setAttribute("init_id", strval($event->initiator->getId()) );
    } 
    /** @todo Добавить определение платежа из Yandex, для этого добавить класс платежа */
    
    if($event->reciver instanceof \app\models\MongoUser){
      $record->setAttribute("reciver_type", BalanceRecord::IT_USER);
      $record->setAttribute("reciver", strval($event->reciver->getId()) );
    }
    if( !$record->validate() || !$record->save()){
      \yii::info("Validate Error: ".json_encode($record->getErrors()), 'balance');
      \yii::$app->user->trigger(NotifyEvent::USER_NOTIFY_EVENT,new NotifyEvent(['text'=>"Ошибка пополнения баланса"]));
      \yii::$app->user->trigger(BalanceEvent::EVENT_BALANCE_REJECT,$event);
      return false;
    }
    \yii::$app->user->trigger(NotifyEvent::USER_NOTIFY_EVENT,new NotifyEvent([
      'text'    => "Баланс пополнен на величину: ".$event->value,
      'reciver' => ($event->reciver instanceof \app\models\MongoUser)?($event->reciver->getId()):(\yii::$app->user->getId()),
      ]));
    $event->status = BalanceEvent::STATUS_MONEY_ADD_OK;
    \yii::$app->user->trigger(BalanceEvent::EVENT_BALANCE_CHANGE,$event);
    return true;
  }
  
  protected function onDecBalance(BalanceEvent $event){
    var_dump("dec123");
  }

  //============================= Private ======================================
  //============================= Constructor - Destructor =====================
  
  public function events(){     
    return [
      BalanceEvent::EVENT_ADD_BALANCE => "onAddBalance",
      BalanceEvent::EVENT_DEC_BALANCE => "onDecBalance",
    ];
  }

}
