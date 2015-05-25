<?php

/**
 * Description of OrdersController
 * @author Sync<atc58.ru>
 */
namespace app\controllers;

use yii;
use yii\web\Controller;
use app\models\balance\BalanceRecord;
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
      \yii::$app->trigger(\app\models\events\BalanceEvent::EVENT_BALANCE_CHANGE, new \app\models\events\BalanceEvent());
      
      $items = BalanceRecord::find()->where([
          'reciver_id'  => strval(yii::$app->user->getId()),
          'reciver_type'=> BalanceRecord::IT_USER,
        ])->orWhere([
          'init_id'  => strval(yii::$app->user->getId()),
          'init_type'=> BalanceRecord::IT_USER,          
        ])
        ->orderBy(['time'=>SORT_DESC])
        ->all();
    }
    $order_list = new BasketDataProvider([
        'allModels'   => $items,
        'pagination'  => new Pagination([
          'totalCount'  => count($items),
          'pageSize'    => 20,
        ]),
    ]);    
    return $this->render('index', ['list' => $order_list, 'columns'=>  $this->columns()]);
  }

  //============================= Protected ====================================  
  //============================= Private ======================================
  private function columns(){
    return [
      [
        'attribute' => 'time',
        'class'     => \kartik\grid\DataColumn::className(),
        'header'    => 'Время',
        'width'     => '30px',
        'format'    => 'raw',
        'headerOptions' => ['class'=>'kartik-sheet-style'],
        'value'     => function ($model, $key, $index, $widget) {
          return "<span>".date("H:i",$model->time)."<br>".date("d-m-y",$model->time)."</span>";          
        },
        'vAlign'=>'middle'
      ],
      [
        'attribute' => 'time',
        'class'     => \kartik\grid\DataColumn::className(),
        'header'    => 'Операция',        
        'width'     => '30px',
        'format'    => 'raw',
        'headerOptions' => ['class'=>'kartik-sheet-style'],
        'value'     => function ($model, $key, $index, $widget) {
          if( $model->operation == BalanceRecord::OP_ADD ){
            return "<span>Увеличение</span>";
          }elseif( $model->operation == BalanceRecord::OP_DEC){
            return "<span>Уменьшение</span>";
          }
          return "<span>Неизвестно</span>";
        },
        'vAlign'=>'middle'
      ],
      [
        'attribute' => 'init',
        'class'     => \kartik\grid\DataColumn::className(),
        'header'    => 'Инициатор',        
        'format'    => 'raw',
        'headerOptions' => ['class'=>'kartik-sheet-style'],
        'value'     => function ($model, $key, $index, $widget) {
          if( $model->init_type == BalanceRecord::IT_USER ) {
            $user = \app\models\MongoUser::findOne(['_id' => new \MongoId($model->init_id)]);
            return "<span>Пользователь " . ($user?"<br>".$user->getUserName():"") . "</span>";
          } elseif( $model->init_type == BalanceRecord::IT_PAY_SYSTEM ) {
            return "<span>Платежная система <br> ID: " . $model->init_id . " </span>";
          }
          return "<span>Неизвестно [" . $model->init_type . "]</span>";
        },
        'vAlign'=>'middle'
      ],
      [
        'attribute' => 'recive',
        'class'     => \kartik\grid\DataColumn::className(),
        'header'    => 'Получатель',        
        'format'    => 'raw',
        'headerOptions' => ['class'=>'kartik-sheet-style'],
        'value'     => function ($model, $key, $index, $widget) {
          if( $model->reciver_type == BalanceRecord::IT_USER ) {
            $user = \app\models\MongoUser::findOne(['_id' => new \MongoId($model->reciver_id)]);
            return "<span>Пользователь " . ($user?"<br>".$user->getUserName():"") . "</span>";
          } elseif( $model->reciver_type == BalanceRecord::IT_PAY_SYSTEM ) {
            return "<span>Платежная система</span>".$model->init_id;
          }
          return "<span>Неизвестно [" . $model->init_type . "]</span>";
        },
        'vAlign'=>'middle'
      ],
      [
        'attribute' => 'item',
        'class'     => \kartik\grid\DataColumn::className(),
        'header'    => 'Объект',        
        'format'    => 'raw',
        'headerOptions' => ['class'=>'kartik-sheet-style'],
        'value'     => function ($model, $key, $index, $widget) {
          if( $model->item_type == BalanceRecord::IT_PART ) {
            $order = \app\models\orders\OrderRecord::findOne(['_id' => new \MongoId($model->item_id)]);
            return "<span>Деталь " . ($order?"<br>Артикул ".$order->articul."<br>Произв. ".$order->producer:"") . "</span>";
          }          
          return "<span>Неизвестно [" . $model->init_type . "]</span>";
        },
        'vAlign'=>'middle'
      ],
      [
        'attribute' => 'value',
        'class'     => \kartik\grid\DataColumn::className(),
        'header'    => 'Сумма',        
        'format'    => ['decimal', 2],
        'width'     => '100px',
        'headerOptions' => ['class'=>'kartik-sheet-style'],        
        'vAlign'    => 'middle'
      ],
      [
        'attribute' => 'comment',
        'class'     => \kartik\grid\DataColumn::className(),
        'header'    => 'Комментарий',
        'headerOptions' => ['class'=>'kartik-sheet-style'],
        'vAlign'    => 'middle'
      ],
    ];
  }
  //============================= Constructor - Destructor =====================  
  
  public function behaviors(){
    return [
      \app\components\behaviors\SearchFormBehavior::className(),
    ];
  }

}
