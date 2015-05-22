<?php

/**
 * Description of GridHelper
 * @author Sync<atc58.ru>
 */
namespace app\components\helpers;

use yii;
use kartik\grid\CheckboxColumn;
use kartik\grid\DataColumn;
use kartik\grid\EditableColumn;
use kartik\editable\Editable;
use kartik\grid\ActionColumn;
use yii\helpers\Url;
use yii\helpers\Html;
use app\models\events\BasketEvent;

class GridHelper{
  //============================= Public =======================================
  public static function Column1(){
    return ['attribute'=>'update_time','class'=> DataColumn::className(),'header'=>'От','width'=>'50px','format'=>'raw','headerOptions'=>['class'=>'kartik-sheet-style'],'value'=>function ($model, $key, $index, $widget) {return "<span>".date("H:i",$model->update_time)."<br>".date("d-m-y",$model->update_time)."</span>";},'vAlign'=>'middle',];
  }
  
  public static function Column2(){
    return ['headerOptions'=>['class'=>'kartik-sheet-style'],'label'=>'Деталь','attribute'=>'part_attr','format' => 'raw','hAlign'=>'center','value'=>function ($model, $key, $index, $widget) {return "<span>[ ".$model->articul." ] ".$model->producer."<br><b>".$model->name."</b></span>";},'vAlign'=>'middle',];
  }
  
  public static function Column3(){
    return [
        'attribute'=>'price',    
        'header'=>'Цена',
        'headerOptions'=>['class'=>'kartik-sheet-style'],
        'vAlign'=>'middle',
        'format' => 'raw',
        'value'=>function ($model, $key, $index, $widget) { 
            $price = yii::$app->user->getUserPrice($model->price);
            $delta = round($price-$price*0.1,2)." - ".round($price+$price*0.1,2);
            return "<span>".$price.($model->price_change==1?"<br>$delta":"")."</span>";
        },
      ];
  }
  
  public static function Column4(){
    return ['attribute'=>'shiping','label'=>'Срок','headerOptions'=>['class'=>'kartik-sheet-style'],'width'=>'50px','vAlign'=>'middle',];
  }
  
  public static function Column5(){
    return [
        'attribute'=>'sell_count',    
        'label'=>'Кол-во',
        'headerOptions'=>['class'=>'kartik-sheet-style'],
        'pageSummary'=>true,    
        'width'=>'50px',
        'vAlign'=>'middle',
        'format'=>['decimal', 0],    
        'class'=>  EditableColumn::className(),
        'editableOptions'=> function ($model, $key, $index) {      
          return[    
            'header'=>'Количество', 
            'inputType'=>  Editable::INPUT_SPIN,
            "formOptions"=>[
              "action"=> Url::to(["basket/item-change",'type'=> BasketEvent::USER_BASKET])
            ],       
            'pluginEvents'      => [
              "editableSuccess"=>"function(event, val, form, data) { "
              . "$.pjax.reload({container:'#user-basket'});"
              . "}",
            ],
            'options'=>[
                'pluginOptions'=>[    
                  'multiple'=>false,
                  'min'=>1, 
                  'max'=>100,              
                  'postfix' => 'шт.',
                ]
            ]
          ];
        },
      ];
  }
  
  public static function Column5O(){
    return [
        'attribute'=>'sell_count',    
        'label'=>'Кол-во',
        'headerOptions'=>['class'=>'kartik-sheet-style'],
        'width'=>'50px',
        'vAlign'=>'middle',
        'format'=>['decimal', 0],    
    ];
  }
  
  public static function Column6(){
    return [
        'class'         =>'kartik\grid\FormulaColumn',
        'header'        =>'Cумма',
        'headerOptions' =>['class'=>'kartik-sheet-style'],
        'format'        =>['decimal', 2],
        'width'         =>'100px',
        'vAlign'=>'middle',
        'value'         => function ($model, $key, $index, $widget) { 
            $p = compact('model', 'key', 'index');
            $price = yii::$app->user->getUserPrice($model->price);
            return $price * $widget->col(4, $p);
        },
        'mergeHeader'   => true,
        'pageSummary'   => true,
        'footer'        => true
      ];
  }
  
  public static function Column6O(){
    return [
        'class'         => 'kartik\grid\FormulaColumn',
        'label'         => 'Cумма',
        'attribute'     => 'sum_attr',
        'headerOptions' => ['class'=>'kartik-sheet-style'],
        'format'        => ['decimal', 2],
        'width'         => '100px',
        'vAlign'        => 'middle',
        'value'         => function ($model, $key, $index, $widget) {             
            $price = yii::$app->user->getUserPrice($model->price)*$model->sell_count;
            return $price;
        },
        'mergeHeader'   => true,
        'pageSummary'   => true,
        'footer'        => true
    ];
  }
  
  public static function Column7(){
    return [
        'attribute'       =>'comment',
        'header'          =>'Комментарий',
        'class'           =>  EditableColumn::className(),
        'headerOptions'   =>['class'=>'kartik-sheet-style'],    
        'width'           =>'150px',
        'vAlign'=>'middle',
        'refreshGrid'     => true,
        'editableOptions' => function ($model, $key, $index) {      
          return[
            'header'      =>'Количество', 
            'inputType'   =>  Editable::INPUT_TEXT,
            'formOptions' =>[
              "action"=>  Url::to(["basket/item-change",'type'=> BasketEvent::USER_BASKET])
            ],        
          ];
        },
      ];
  }
  
  public static function Column8(){
    return [
        'class'=> ActionColumn::className(),
        'header' => 'Действия',
        "template" => '{delete} {order}',
        'vAlign'=>'middle',
        'buttons' => [      
          'order'=> function($url,$model){
            $label = '<i class="glyphicon glyphicon-shopping-cart"></i>';
            $options = ['title'=>'Разместить заказ', 
                        'data-toggle'=>'tooltip',
                        'data-confirm' => 
                          "Вы хотите заказать эту деталь? <br>[ ".
                          $model->articul." ] ".
                          $model->producer."<br><b>".
                          $model->name."</b><br>".
                          "В количестве: <b>".$model->sell_count."</b> шт. ".
                          "По цене: ".yii::$app->user->getUserPrice($model->price)." руб. за шт.<br>".
                          "Общая цена составит: ".(yii::$app->user->getUserPrice($model->price)*$model->sell_count)." руб.",
              ];
            return Html::a($label, $url,$options);
          }],
        'deleteOptions' => ['label' => '<i class="glyphicon glyphicon-remove"></i>','title'=>'Удалить запись из корзины', 'data-toggle'=>'tooltip'],    
        'urlCreator'=>function($action, $model, $key, $index) { return Url::to(['basket/item-'.$action,"id"=>$key,"type"=>  BasketEvent::USER_BASKET]); },
        'headerOptions'=>['class'=>'kartik-sheet-style'],
      ];
  }
  
  public static function Column9(){
    return ['class'         =>  CheckboxColumn::className(),'headerOptions' => ['class'=>'kartik-sheet-style'],'width' => "35px",'vAlign'=>'middle',];
  }
  
  public static function Column5G(){
    return [
    'attribute'=>'sell_count',    
    'header'=>'Кол-во',
    'headerOptions'=>['class'=>'kartik-sheet-style'],
    'pageSummary'=>true,    
    'width'=>'50px',
    'vAlign'=>'middle',
    'format'=>['decimal', 0],    
    'class'=>  EditableColumn::className(),
    'editableOptions'=> function ($model, $key, $index) {      
      return[    
        'header'=>'Количество', 
        'inputType'=>  Editable::INPUT_SPIN,
        "formOptions"=>[
          "action"=> Url::to(["basket/item-change",'type'=> BasketEvent::GUEST_BASKET])
        ],       
        'pluginEvents'      => [
          "editableSuccess"=>"function(event, val, form, data) { "
          . "$.pjax.reload({container:'#guest-basket'});"
          . "}",
        ],
        'options'=>[
            'pluginOptions'=>[    
              'multiple'=>false,
              'min'=>1, 
              'max'=>100,              
              'postfix' => 'шт.',
            ]
          ]
        ];
      },
    ];
  }
  
  public static function Column7G(){
    return [
    'attribute'       =>'comment',
    'header'          =>'Комментарий',
    'class'           =>  EditableColumn::className(),
    'headerOptions'   =>['class'=>'kartik-sheet-style'],    
    'width'           =>'150px',
    'refreshGrid'     => true,
    'vAlign'=>'middle',
    'editableOptions' => function ($model, $key, $index) {      
      return[
        'header'      =>'Количество', 
        'inputType'   =>  Editable::INPUT_TEXT,
        'formOptions' =>[
          "action"=> Url::to(["basket/item-change",'type'=> BasketEvent::GUEST_BASKET])
          ],        
        ];
      },
    ];
  }
  
  public static function Column8G(){
    return [
    'class'=> ActionColumn::className(),
    'header' => 'Действия',
    "template" => yii::$app->user->isGuest?'{delete}':'{delete} {tobasket}',
    'vAlign'=>'middle',
    'buttons' => [      
      'tobasket'=> function($url,$model){
        $label = '<i class="glyphicon glyphicon-open-file"></i>';
        $options = ['title'=>'Переместить в корзину', 
                    'data-toggle'=>'tooltip',
                    'data-confirm' => 
                      "Вы хотите поместить эту деталь в корзину? <br>[ ".
                      $model->articul." ] ".
                      $model->producer."<br><b>".
                      $model->name."</b><br>".
                      "В количестве: <b>".$model->sell_count."</b> шт. ".
                      "По цене: ".yii::$app->user->getUserPrice($model->price)." руб. за шт.<br>".
                      "Общая цена составит: ".(yii::$app->user->getUserPrice($model->price)*$model->sell_count)." руб.",
          ];
        return Html::a($label, $url,$options);
      }],
    'deleteOptions' => ['label' => '<i class="glyphicon glyphicon-remove"></i>','title'=>'Удалить запись', 'data-toggle'=>'tooltip'],    
    'urlCreator'=>function($action, $model, $key, $index) { return Url::to(['basket/item-'.$action,"id"=>$key,"type"=> BasketEvent::GUEST_BASKET]); },
    'headerOptions'=>['class'=>'kartik-sheet-style'],
    ];
  }
  
  public static function ColumnPay(){
    return [
      'class'=>'kartik\grid\BooleanColumn',
      'label' => 'Оплачено',
      'attribute'=>'pay', 
      'vAlign'=>'middle'
    ];
  }
  
  public static function ColumnPayValue(){
    return [      
      'label' => 'Сумма оплаты',
      'format'   =>['decimal', 2],
      'attribute'=>'pay_value', 
      'vAlign'=>'middle'
    ];
  }
  
  public static function ColumnStatus(){
    return [      
      'label' => 'Статус',
      'attribute'=>'status', 
      'vAlign'=>'middle',
      'value'=>function ($model, $key, $index, $widget) { 
        return $model->textStatus();
      }
    ];
  }
  
  public static function ColumnComment(){
    return [      
      'header' => 'Комментарий',      
      'attribute'=>'comment', 
      'vAlign'=>'middle',      
    ];    
  }
  
  public static function ColumnPayAction(){
    return [
    'class'=> ActionColumn::className(),
    'header' => 'Действия',
    "template" => '{by-balance} {by-yandex} {delete}',
    'vAlign'=>'middle',
    'buttons' => [      
      'delete' => function ($url,$model){
        if( ((bool)$model->pay) || ($model->status > 0) ){
          return "";
        }
        $label = '<i class="glyphicon glyphicon glyphicon glyphicon-remove"></i>';
        $options = [ 
          'label' => '<i class="glyphicon glyphicon-remove"></i>',
          'title'=>'Удалить запись', 
          'data-toggle'=>'tooltip',
          'data-confirm' => "Вы уверены, что хотите удалить запись?"];
        return Html::a($label, $url,$options);
      },
      'by-balance' => function($url,$model){
        if( (bool)$model->pay || !yii::$app->user->isCompany() || ($model->status == \app\models\orders\OrderRecord::STATE_REJECTED) ){
          return "";
        }
        $label = '<i class="glyphicon glyphicon glyphicon-rub"></i>';
        $options = [
          'title' => 'Оплатить из денег на балансе',
          'data-toggle'=>'tooltip',
          'class' => 'btn btn-link',
          'style' => 'padding: 0; margin: -4px 0 0 0',
          'data-confirm' => 
            "Вы хотите оплатить заказ деталей из Ваших денег <b>на балансе?</b><br>".
            "В количестве <b>".$model->sell_count."</b> шт. <br>".
            "По цене: <b>".yii::$app->user->getUserPrice($model->price)."</b> руб. за шт.<br>".
            "Общая сумма составит: ".(yii::$app->user->getUserPrice($model->price)*$model->sell_count)." руб.",
        ];

        if( yii::$app->user->getUserPrice($model->price) > yii::$app->user->getBalance()->getFullBalance() ){
          $options['disabled'] = 'true';
        }
        return Html::a($label, $url, $options);
      },
      'by-yandex' => function($url,$model){
        if( (bool)$model->pay || ($model->status == \app\models\orders\OrderRecord::STATE_REJECTED) ){
          return "";
        }
        $label = '<i> <img src="img/yandex-money.png" alt="Оплатить через Yandex-деньги" style="width:30px;heoght:20px;margin-top:-5px;"/></i>';
        $options = [
          'title' => 'Оплатить через Yandex-деньги',
          'data-toggle'=>'tooltip',
          'data-confirm' => 
            "Вы хотите оплатить заказ деталей через <b>Yandex-деньги?</b><br>".            
            "В количестве <b>".$model->sell_count."</b> шт. <br>".
            "По цене: <b>".yii::$app->user->getUserPrice($model->price)."</b> руб. за шт.<br>".
            "Общая сумма составит: ".(yii::$app->user->getUserPrice($model->price)*$model->sell_count)." руб.",          
          ];
        return Html::a($label, $url,$options);
      }],    
    'urlCreator'=>function($action, $model, $key, $index) { return Url::to(['order/pay-'.$action,"id"=>$key]); },    
    ];
  }
  
  public static function ColumnWaitTime(){
    return [
      'label'  => 'Ожидается',
      'vAlign'  => 'middle',
      'attribute' => 'wait_time',
      'value'=>function ($model, $key, $index, $widget) { 
        return date("d-m-Y",$model->wait_time);
      }
    ];
  }
  //============================= Protected ====================================
  //============================= Private ======================================
  //============================= Constructor - Destructor =====================

}
