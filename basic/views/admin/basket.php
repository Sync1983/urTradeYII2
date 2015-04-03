<?php

use yii\helpers\Html;
use app\models\PartRecord;
use kartik\grid\GridView;
use app\components\helpers\GridHelper;

$this->title = "Корзина пользователя";
$this->params['breadcrumbs'][] = [
  'label' => 'Корзины',
  'url' => ['admin/user-basket'],
  ];
$this->params['breadcrumbs'][] = $this->title;

/* @var $this yii\web\View */
/* @var $model PartRecord */
/* @var $user \app\models\MongoUser */
$columns =[
  GridHelper::Column1(),
  GridHelper::Column2(),
  [
        'attribute'=>'price',    
        'header'=>'Цена',
        'headerOptions'=>['class'=>'kartik-sheet-style'],
        'vAlign'=>'middle',
        'format' => 'raw',
        'value'=>function ($model, $key, $index, $widget) use ($user) { 
            $price = $user->getUserPrice($model->price);
            $delta = round($price-$price*0.1,2)." - ".round($price+$price*0.1,2);
            return "<span>".
                "Наша цена: <b>".$model->price."</b>".
                "<br> Цена пользователя: <b>".
                $price.($model->price_change==1?"<br>$delta":"")."</b></span>";
        },
  ],
  GridHelper::Column4(),
  GridHelper::Column5O(),
  [
        'class'         =>'kartik\grid\FormulaColumn',
        'header'        =>'Cумма',
        'headerOptions' =>['class'=>'kartik-sheet-style'],
        'format'        =>['decimal', 2],
        'width'         =>'100px',
        'vAlign'=>'middle',
        'value'         => function ($model, $key, $index, $widget) { 
            $p = compact('model', 'key', 'index');
            $price = $model->price;
            return $price * $widget->col(4, $p);
        },
        'mergeHeader'   => true,
        'pageSummary'   => true,
        'footer'        => true
  ],
  [
        'attribute'       =>'comment',
        'header'          =>'Комментарий',
        'class'           => kartik\grid\DataColumn::className(),
        'headerOptions'   =>['class'=>'kartik-sheet-style'],    
        'width'           =>'150px',
        'vAlign'=>'middle',        
  ]
  
];
    ?>
<h4>Корзина пользователя <?= $user->getUserName()." : ".$user->getAttribute("name")?></h4>
<?php
echo GridView::widget([
    'id'            => 'user-basket',
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
    'persistResize' => false,
  ]);
?>

