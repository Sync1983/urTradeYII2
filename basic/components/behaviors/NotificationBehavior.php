<?php

/**
 * Description of NotificationBehavior
 * @author Sync<atc58.ru>
 */

namespace app\components\behaviors;

use yii;
use yii\base\Behavior;
use app\models\events\NotifyEvent;
use app\models\MongoUser;

class NotificationBehavior extends Behavior{

  //public vars
  //protected vars  
  //private vars  
  //============================= Public =======================================
  public function onNotification(NotifyEvent $event){  
    $user = false;
    if( ($event->reciver === NULL) && (yii::$app->user)){
      $user = yii::$app->user->getIdentity();
    } else {
      $user = MongoUser::find()->where(['_id'=>$event->reciver])->one();
    }
    if( !$user ){
      return;
    }
    $arr = $user->informer;
    $arr[] = $event->text;
    $user->informer = $arr;
    $user->save();    
    return;
  }
  //============================= Protected ====================================
  //============================= Private ======================================
  //============================= Constructor - Destructor =====================
  
  public function events(){    
    return [
     NotifyEvent::USER_NOTIFY_EVENT => 'onNotification',
    ];
  }

}
