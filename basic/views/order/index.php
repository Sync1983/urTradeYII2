<?php

use yii\widgets\Pjax;
use kartik\grid\GridView;
/* @var $this yii\web\View */
$this->title = 'Корзина';
$this->params['breadcrumbs'][] = $this->title;

Pjax::begin(['id'=>'order-list']);        
  echo GridView::widget([
    'id'            => 'order-list',
    'dataProvider'  => $list,    
    'hover'         => true,
    'columns'       => $columns,    
    'export' => false,
    'pjax'=>true,
    'pjaxSettings'=>[
      'neverTimeout'=>true,      
    ],
    'panel' => [
      'type' => GridView::TYPE_INFO,
      'heading' => "<i class=\"glyphicon glyphicon-book\"></i> Ваши заказы",
    ],
  ]);
  Pjax::end();
