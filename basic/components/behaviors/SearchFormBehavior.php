<?php

/**
 * Description of SearchFromBehavior
 * @author Sync<atc58.ru>
 */

namespace app\components\behaviors;

use yii;
use yii\base\Behavior;
use app\models\forms\SearchForm;
use app\models\orders\OrderRecord;
use yii\web\Controller;

class SearchFormBehavior extends Behavior{
  //public vars
  //protected vars
  protected $search;
  //private vars  
  //============================= Public =======================================
  public function getSearchForm(){
    return $this->search;
  }
  
  public function initSearch($event){    
    $this->search =  new SearchForm();
    $request = yii::$app->request->get();
    $request['over_price'] = (int) Yii::$app->request->get('over_price',0);
    $this->search->load($request,'');    
    \yii::$app->trigger(\app\models\events\BalanceEvent::EVENT_BALANCE_CHANGE,new \app\models\events\BalanceEvent());
    $event->action->controller->getView()->params['search_model'] = $this->search;
    $event->action->controller->getView()->params['basket_count'] = count(yii::$app->user->getBasketParts());
    $orders_count = OrderRecord::find()->where(['for_user' => strval(yii::$app->user->getId()),'status' => 0])->count();
    $event->action->controller->getView()->params['order_count'] = $orders_count;
    $event->action->controller->getView()->params['balance'] = yii::$app->user->getBalance()->getFullBalance();
  }
  //============================= Protected ====================================
  //============================= Private ======================================
  //============================= Constructor - Destructor =====================
  public function events(){
    return [
      Controller::EVENT_BEFORE_ACTION => 'initSearch',
    ];
  }

}
