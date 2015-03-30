<?php

/**
 * Description of OrderBehavior
 * @author Sync<atc58.ru>
 */
namespace app\components\behaviors;

use yii\base\Behavior;
use app\models\events\OrderEvent;
use app\models\orders\OrderRecord;

class OrderBehavior extends Behavior{  
  //public vars
  //protected vars
  //private vars  
  //============================= Public =======================================
  public function on_add(OrderEvent $event){
    if(empty($event->items)){
      return;
    }    
    foreach($event->items as $item){
      $order_item = new OrderRecord();
      $order_item->setAttributes($item);
      $order_item->setAttribute("_id", null);
      $order_item->setAttribute("status", OrderRecord::STATE_WAIT_PAY);
      $order_item->setAttribute("pay", false);
      $order_item->setAttribute("pay_request", false);
      $order_item->setAttribute("pay_time", 0);
      $order_item->setAttribute("pay_value", 0.0);
      if($order_item->validate()){
        $order_item->save();
      }
    }    
  }
  //============================= Protected ====================================
  //============================= Private ======================================
  //============================= Constructor - Destructor =====================
  
  public function events(){
    return [
      OrderEvent::EVENT_ORDER_ADD => 'on_add',
    ];
  }

}
