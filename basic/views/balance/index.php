<?php

use kartik\grid\GridView;
/* @var $this yii\web\View */
$this->title = 'Баланс';
$this->params['breadcrumbs'][] = $this->title;

echo GridView::widget([
    'id'            => 'order-list',
    'dataProvider'  => $list,    
    'hover'         => true,
    'columns'       => $columns,    
    'export' => false,
    'panel' => [
      'type' => GridView::TYPE_INFO,
      'heading' => "<i class=\"glyphicon glyphicon-book\"></i> Ваши операции с балансом",
      'footer'  => "<h4>Общий баланс: ".\yii::$app->user->getBalance()->getFullBalance()."</h4>",
    ],
  ]);

