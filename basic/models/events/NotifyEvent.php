<?php

/**
 * Description of AddToBasketEvent
 * @author Sync<atc58.ru>
 */
namespace app\models\events;

use yii;
use yii\base\Event;

class NotifyEvent extends Event{  
  const USER_NOTIFY_EVENT = "USER_NOTIFY_EVENT";
  //public vars  
  public $text;
  public $reciver = null;
  //protected vars
  //private vars  
  //============================= Public =======================================
  //put your code here
  //============================= Protected ====================================
  //============================= Private ======================================
  //============================= Constructor - Destructor =====================
  public function init(){
    parent::init();
    if( yii::$app->user && yii::$app->user->getIdentity() ){
      $this->reciver = yii::$app->user->getIdentity()->getId();
    }
  }

}
