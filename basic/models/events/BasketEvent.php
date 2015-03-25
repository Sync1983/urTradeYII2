<?php

/**
 * Description of AddToBasketEvent
 * @author Sync<atc58.ru>
 */
namespace app\models\events;
use yii\base\Event;
use app\models\PartRecord;

class BasketEvent extends Event{  
  //public vars
  const GUEST_BASKET = 0;
  const USER_BASKET  = 1;
  
  /* @var $part PartRecord */
  public $part;
  public $params;
  public $key;
  public $type;
  //protected vars
  //private vars  
  //============================= Public =======================================
  //put your code here
  //============================= Protected ====================================
  //============================= Private ======================================
  //============================= Constructor - Destructor =====================
}
