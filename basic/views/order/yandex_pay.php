<?php

use yii\widgets\Pjax;

/* @var $this yii\web\View */
$this->title = 'Оплата';
$this->params['breadcrumbs'][] = [
  'label' => 'Заказы',
  'url' => ['order/index'],
  ];
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
