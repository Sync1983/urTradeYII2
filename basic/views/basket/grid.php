<?php

use yii\helpers\Html;
use app\models\PartRecord;
use yii\widgets\Pjax;
use kartik\grid\GridView;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model PartRecord */
    
if(!yii::$app->user->isGuest){
  Pjax::begin(['id'=>'user-basket']);        
  echo GridView::widget([
    'id'            => 'user-basket',
    'dataProvider'  => $user_basket,    
    'hover'         => true,
    'columns'       => $grid_columns,
    'showPageSummary' => true,
    'export' => false,
    'pjax'=>true,
    'pjaxSettings'=>[
      'neverTimeout'=>true,      
    ],
    'panel' => [
      'type' => GridView::TYPE_INFO,
      'heading' => "<i class=\"glyphicon glyphicon-book\"></i> Корзина",
    ],
    'panelAfterTemplate' => 
      "<div class=\"pull-right\">".
      Html::button(
        "<i class=\"glyphicon glyphicon-shopping-cart\"></i>".
        " Заказать",[
          'class'=>'btn btn-primary', 
          'data-confirm'=>"Заказать набор деталей?"
        ]). 
      Html::button(
        "<i class=\"glyphicon glyphicon-remove\"></i> Удалить",
        [
          'class'=>'btn btn-danger',           
          'onClick' => 'deleteItems()',
        ]).
      "</div>".
      "<div class=\"clearfix\"></div>",
    'persistResize' => false,
  ]);
  Pjax::end();
}
//var_dump($guest_columns);
Pjax::begin(['id'=>'guest-basket']);        
echo GridView::widget([
    'id'            => 'guest-basket',
    'dataProvider'  => $guest_basket,    
    'hover'         => true,
    'columns'       => $guest_columns,
    'showPageSummary' => true,
    'export' => false,
    'pjax'=>true,
    'pjaxSettings'=>[
      'options' => [
        'id' => 'guest-basket',
      ],
      'neverTimeout'=>true,      
    ],
    'panel' => [
      'type' => GridView::TYPE_INFO,
      'heading' => "<i class=\"glyphicon glyphicon-book\"></i> Детали добавленные без авторизации",
    ],
    'panelAfterTemplate' => 
      "<div class=\"pull-right\">".(!yii::$app->user->isGuest?
      Html::button(
        "<i class=\"glyphicon glyphicon-open-file\"></i>".
        " В корзину",[
          'class'=>'btn btn-primary', 
          'onClick' => 'toBasketGuestItems()',          
        ]):"").
      Html::button(
        "<i class=\"glyphicon glyphicon-remove\"></i> Удалить",
        [
          'class'=>'btn btn-danger',           
          'onClick' => 'deleteGuestItems()',
        ]).  
      "</div>".
      "<div class=\"clearfix\"></div>",
    'persistResize' => false,
  ]);
Pjax::end();

ActiveForm::begin([
  "id"=>"delete",
  "method"=>"POST",
  "action" => Url::to(['basket/delete-list']),
  "options" => [
    "style" => "display: none"
    ]
]);
ActiveForm::end();
ActiveForm::begin([
  "id"=>"guest-all",
  "method"=>"POST",
  "action" => Url::to(['basket/to-basket-list']),
  "options" => [
    "style" => "display: none"
    ]
]);
ActiveForm::end();

?>

<script type="text/javascript">
  function deleteGuestItems(){
    var keys = $('#guest-basket').yiiGridView('getSelectedRows');
    if(keys.length === 0){
      return;
    }
    if(confirm("Удалить набор из "+keys.length+" деталей?")){      
      var form = $("#delete");
      form.children("[name=\"ids[]\"]").remove();
      form.children("[name=\"type\"]").remove();
      for(var index in keys){
        form.append('<input type="hidden" name="ids[]" value="'+keys[index]+'" />');
      }
      form.append('<input type="hidden" name="type" value="0" />');
      $(form).submit();      
    }
  }
  
  function deleteItems(){
    var keys = $('#user-basket').yiiGridView('getSelectedRows');
    if(keys.length === 0){
      return;
    }
    if(confirm("Удалить набор из "+keys.length+" деталей?")){      
      var form = $("#delete");
      form.children("[name=\"ids[]\"]").remove();
      form.children("[name=\"type\"]").remove();
      for(var index in keys){
        form.append('<input type="hidden" name="ids[]" value="'+keys[index]+'" />');
      }
      form.append('<input type="hidden" name="type" value="1" />');
      $(form).submit();      
    }
  }
  
  function toBasketGuestItems(){
    var keys = $('#guest-basket').yiiGridView('getSelectedRows');
    if(keys.length === 0){
      return;
    }
    if(confirm("Поместить в корзину набор из "+keys.length+" деталей?")){      
      var form = $("#guest-all");
      form.children("[name=\"ids[]\"]").remove();
      for(var index in keys){
        form.append('<input type="hidden" name="ids[]" value="'+keys[index]+'" />');
      }
      $(form).submit();      
    }
  }
</script>

