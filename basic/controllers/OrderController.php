<?php

/**
 * Description of OrdersController
 * @author Sync<atc58.ru>
 */
namespace app\controllers;

use yii\web\Controller;
use app\components\helpers\GridHelper;

class OrderController extends Controller{
  
  //public vars
  //protected vars
  //private vars  
  //============================= Public =======================================
  public function actions() {
    return [
      'index' =>  [
        'class' => actions\order\IndexAction::className(),
      ],
      'pay-by-balance' => [
        'class'   => actions\order\PayByAction::className(),
        'payment' => 'balance'
      ],
      'pay-by-yandex' => [
        'class'   => actions\order\PayByAction::className(),
        'payment' => 'yandex'
      ],
      'pay-delete'  => [
        'class'   => actions\order\OrderActions::className(),
        'type'    => 'delete'
      ]
    ];
  }

  //============================= Protected ====================================  
  //============================= Private ======================================
  //============================= Constructor - Destructor =====================  
  
  public function behaviors(){
    return [
      \app\components\behaviors\SearchFormBehavior::className(),
    ];
  }

  public function columns(){
    return [
      GridHelper::ColumnStatus(),
      GridHelper::ColumnWaitTime(),
      GridHelper::Column2(),
      GridHelper::Column4(),
      GridHelper::Column5O(),
      GridHelper::Column6O(),
      GridHelper::ColumnPay(),
      GridHelper::ColumnPayValue(),
      GridHelper::ColumnComment(),
      GridHelper::ColumnPayAction(),
    ];
  }

}
