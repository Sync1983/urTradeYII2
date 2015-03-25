<?php

/**
 * Description of BasketBehavior
 * @author Sync<atc58.ru>
 */
namespace app\models\behaviors;
use yii\base\Behavior;

class BasketBehavior {
  //public vars
  //protected vars
  //private vars  
  //============================= Public =======================================
  public function events(){
    return [
      ActiveRecord::EVENT_BEFORE_VALIDATE => 'beforeValidate',
    ];
  }
  //============================= Protected ====================================
  //============================= Private ======================================
  //============================= Constructor - Destructor =====================
}
