<?php
use yii\helpers\Html;
use yii\widgets\Pjax;
use kartik\grid\GridView;
/* @var $this yii\web\View */
$this->title = 'About';
$this->params['breadcrumbs'][] = $this->title;

Pjax::begin(['id'=>'order-list']);        
  echo GridView::widget([
    'id'            => 'order-list',
    'dataProvider'  => $list,    
    'hover'         => true,
    'columns'       => $columns,
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
