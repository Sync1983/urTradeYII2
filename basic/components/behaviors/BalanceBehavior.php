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
  protected function parseInitiator(BalanceRecord &$record, BalanceEvent &$event){
    $record->setAttribute("init_type", BalanceRecord::IT_UNKNOW);
    $record->setAttribute("init_id", " ");
    
    if($event->initiator instanceof \app\models\MongoUser){
      $record->setAttribute("init_type", BalanceRecord::IT_USER);
      $record->setAttribute("init_id", strval($event->initiator->getId()) );
    } else if( $event->initiator instanceof \app\models\pays\YaPayType){
      $record->setAttribute("init_type", BalanceRecord::IT_PAY_SYSTEM);
      $record->setAttribute("init_id", strval($event->initiator->invoiceId));
    }    
    
    return ;
  }
  
  protected function parseReciver(BalanceRecord &$record, BalanceEvent &$event){
    $record->setAttribute("reciver_type", BalanceRecord::IT_UNKNOW);
    $record->setAttribute("reciver_id", " ");    
    
    if($event->reciver instanceof \app\models\MongoUser){
      $record->setAttribute("reciver_type", BalanceRecord::IT_USER);
      $record->setAttribute("reciver_id", strval($event->reciver->getId()) );
    }    
    
    return ;
  }
  
  protected function parseItem(BalanceRecord &$record, BalanceEvent &$event){
    $record->setAttribute("item_type", BalanceRecord::IT_UNKNOW);
    $record->setAttribute("item_id", " ");    
    
    if($event->item instanceof \app\models\orders\OrderRecord){
      $record->setAttribute("item_type", BalanceRecord::IT_PART);
      $record->setAttribute("item_id", strval($event->item->getAttribute("_id")) );
    }    
    
    return ;
  }

  /**
   * Слушатель события пополнения баланса
   * @param BalanceEvent $event
   */
  protected function onAddBalance(BalanceEvent $event){    
    \yii::info("Try ADD to balance for event: [".$event->serialize()."]", 'balance');    
    
    $record = new BalanceRecord();
    $record->setAttribute("operation", BalanceRecord::OP_ADD);
    $record->setAttribute("value", $event->value);
    $record->setAttribute("comment", $event->comment);
    
    $this->parseInitiator($record, $event);
    $this->parseReciver($record, $event);
    $this->parseItem($record, $event);
    
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
  
  public function bayPart(\app\models\orders\OrderRecord $order, $value, $user = null){
    $pay_value = $order->getAttribute("pay_value") * 1.0;
    $pay_value += $value * 1.0;
    
    
    $order->setAttribute("pay_value", $pay_value);
    $order->setAttribute("pay_time", time());
    $order->setAttribute("pay_request", true);
    
    if($order->getAttribute("status") == \app\models\orders\OrderRecord::STATE_WAIT_PAY){
      $order->setAttribute("status", \app\models\orders\OrderRecord::STATE_WAIT_PLACEMENT);
    }
    if( !$user ){
      $user = \yii::$app->user;
    }

    $need_pay = $user->getUserPrice($order->getAttribute("price")) * $order->getAttribute("sell_count") * 1.0;

    if( $pay_value >= $need_pay ){
      $order->setAttribute("pay", true);
    } else {
      $order->setAttribute("pay", false);      
    }
    
    if(!$order->save(true)){
      \yii::info("Order validate Error: ".json_encode($order->getErrors()), 'balance');
      throw new yii\web\BadRequestHttpException("Ошибка изменения записи: [".strval($order->getAttribute("_id"))."]<br> Свяжитесь с администрацией для предотвращения потери средств");
    }
    return true;
  }

  public function onDecBalance(BalanceEvent $event){    
    \yii::info("Try DEC balance for event: [".$event->serialize()."]", 'balance');    
    $record = new BalanceRecord();
    $record->setAttribute("operation", BalanceRecord::OP_DEC);
    $record->setAttribute("value", $event->value);
    $record->setAttribute("comment", $event->comment);
    
    $this->parseInitiator($record, $event);
    $this->parseReciver($record, $event);
    $this->parseItem($record, $event);    
    
    if( !$record->validate() || !$record->save()){
      \yii::info("Validate Error: ".json_encode($record->getErrors()), 'balance');
      \yii::$app->user->trigger(NotifyEvent::USER_NOTIFY_EVENT,new NotifyEvent(['text'=>"Ошибка уменьшения баланса"]));
      \yii::$app->user->trigger(BalanceEvent::EVENT_BALANCE_REJECT,$event);
      return false;
    }
    
    if($event->item instanceof \app\models\orders\OrderRecord){
      if( $event->reciver instanceof \app\models\MongoUser){
        $this->bayPart($event->item,$event->value, $event->reciver);
      } else {
        $this->bayPart($event->item,$event->value);
      }
    }
    
    \yii::$app->user->trigger(NotifyEvent::USER_NOTIFY_EVENT,new NotifyEvent([
      'text'    => "Баланс уменьшен на величину: ".$event->value,
      'reciver' => ($event->reciver instanceof \app\models\MongoUser)?($event->reciver->getId()):(\yii::$app->user->getId()),
      ]));    
    $event->status = BalanceEvent::STATUS_MONEY_DEC_OK;
    \yii::$app->user->trigger(BalanceEvent::EVENT_BALANCE_CHANGE,$event);
    return true;
  }

  //============================= Private ======================================
  //============================= Constructor - Destructor =====================
  
  public function events(){     
    return [
      BalanceEvent::EVENT_ADD_BALANCE => [ $this, "onAddBalance" ],
      BalanceEvent::EVENT_DEC_BALANCE => [ $this, "onDecBalance" ],
    ];
  }

}
