<?php

/**
 * Description of OrdersController
 * @author Sync<atc58.ru>
 */
namespace app\controllers;

use yii;
use yii\web\Controller;
use app\models\balance\BalanceRecord;
use app\components\helpers\GridHelper;
use app\models\BasketDataProvider;
use yii\data\Pagination;

class BalanceController extends Controller{
  
  //public vars
  //protected vars
  //private vars  
  //============================= Public =======================================
  public function actionIndex(){
    if(yii::$app->user->isGuest){
      $items = [];
    } else {
      $items = BalanceRecord::find()->where([
          'reciver_id'  => strval(yii::$app->user->getId()),
          'reciver_type'=> BalanceRecord::IT_USER,
        ])->orWhere([
          'init_id'  => strval(yii::$app->user->getId()),
          'init_type'=> BalanceRecord::IT_USER,          
        ])
        ->orderBy(['time'])
        ->all();
    }
    $order_list = new BasketDataProvider([
        'allModels'   => $items,
        'pagination'  => new Pagination([
          'totalCount'  => count($items),
          'pageSize'        => 20,
        ]),
    ]);    
    return $this->render('index', ['list' => $order_list, 'columns'=>  $this->columns()]);
  }

  //============================= Protected ====================================  
  //============================= Private ======================================
  private function columns(){
    return [
      
      //GridHelper::Column2(),      
      //GridHelper::Column4(),
      //GridHelper::Column5O(),
      GridHelper::Column6O(),      
    ];
  }
  //============================= Constructor - Destructor =====================  
  
  public function behaviors(){
    return [
    \app\components\behaviors\SearchFormBehavior::className(),
    ];
  }

}
