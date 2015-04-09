<?php

/**
 * Description of OrderBehavior
 * @author Sync<atc58.ru>
 */
namespace app\components\behaviors;

use yii\base\Behavior;
use app\models\events\OrderEvent;
use app\models\orders\OrderRecord;

class AdminBehavior extends Behavior{  
  //public vars
  //protected vars
  //private vars  
  //============================= Public =======================================
  //============================= Protected ====================================
  public function onChange(OrderEvent $event){
   $order = OrderRecord::findOne(["_id"=>new \MongoId($event->key)]); 
   if( !$order ){
     throw new \yii\web\NotFoundHttpException("Запись не найдена");
   }   
   foreach( $event->items as $key=>$value ){
     $order->setAttribute($key, $value);
   }
   if( !$order->save() ){
     throw new \yii\web\NotFoundHttpException("Ошибка сохранения");
   }
   echo json_encode(['output'=>$value]);
  }

  public function onBefore($event){
    if( \yii::$app->user->isGuest){
      return $this->redirect(['site/index']);
    }
    if( !\yii::$app->user->isAdmin()){
      throw new \yii\base\InvalidConfigException("Вы должны быть администратором!");      
    } 
  }
  //============================= Private ======================================
  //============================= Constructor - Destructor =====================
  
  public function events(){
    return [
      OrderEvent::EVENT_ORDER_CHANGE => [ $this, 'onChange' ],
      \yii\web\Controller::EVENT_BEFORE_ACTION => [  $this, 'onBefore']
    ];
  }

}
