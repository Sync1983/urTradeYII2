<?php

/**
 * Description of OrderBehavior
 * @author Sync<atc58.ru>
 */
namespace app\components\behaviors;

use yii\base\Behavior;
use app\models\events\OrderEvent;
use app\models\orders\OrderRecord;
use app\models\events\BalanceEvent;
use app\models\events\NotifyEvent;

class OrderBehavior extends Behavior{  
  //public vars
  //protected vars
  //private vars  
  //============================= Public =======================================
  //============================= Protected ====================================
  /**
   * Слушатель события добавления элемента в заказ
   * @param OrderEvent $event
   * @return type
   */
  public function onAdd(OrderEvent $event){
    if( empty($event->items) ){
      return;
    }
    $mail_text = [];
    $search_model = new \app\models\search\SearchModel();
    /* @var $item \app\models\basket\BasketPart */
    foreach( $event->items as $item ){
      $order_item = new OrderRecord();	  
      $order_item->setAttributes($item);
      $order_item->setAttribute("status", OrderRecord::STATE_WAIT_PAY);
      $order_item->setAttribute("pay", false);
      $order_item->setAttribute("pay_request", false);
      $order_item->setAttribute("pay_time", 0);
      $order_item->setAttribute("pay_value", 0.0);
      if( $order_item->validate() ){		
        $order_item->insert();
        $provider = $search_model->getProviderByCLSID($item['provider']);
        $mail_text[] = [
          'id'	  => $item['_id'],		  
          'stock' => $item['stock'],
          'prov'  => $provider->getName(),
          'art'	  => $item['articul'],
          'prod'  => $item['producer'],
          'name'  => $item['name'],
          'price' => $item['price'],
          'time'  => $item['shiping'],
          'orig'  => $item['is_original'],
          'cnt'	  => $item['sell_count'],
          'lot'	  => $item['lot_quantity'],
          'comm'  => $item['comment'],
        ];
      }
    }
    \yii::$app->mailer->compose('site/new_order',[ 'items' => $mail_text ])
		  ->setFrom(['robot@atc58.ru' => 'АвтоТехСнаб'])
		  ->setTo('sales@atc58.ru')
		  ->setSubject('Тест АвтоТехСнаб Новый Заказ')
		  ->send();
  }
  
  public function onChangeBalance(BalanceEvent $event){
    $notify = new NotifyEvent();
    $notify->reciver = \yii::$app->user->getId();
    if( $event->status === BalanceEvent::STATUS_OK){
      $notify->text = "Баланс изменен на значение ".$event->value;
    } elseif ($event->status === BalanceEvent::STATUS_MONEY_ADD_OK){
      $notify->text = "Баланс увеличен на ".$event->value;
    } elseif ($event->status === BalanceEvent::STATUS_MONEY_DEC_OK){
      $notify->text = "Баланс уменьшен на ".$event->value;      
    }    
    \yii::$app->user->trigger(NotifyEvent::USER_NOTIFY_EVENT,$notify);    
  }
  //============================= Private ======================================
  //============================= Constructor - Destructor =====================
  
  public function events(){
    return [
      OrderEvent::EVENT_ORDER_ADD => [ $this, 'onAdd' ],
      BalanceEvent::EVENT_BALANCE_CHANGE => [  $this, 'onChangeBalance'],
    ];
  }

}
