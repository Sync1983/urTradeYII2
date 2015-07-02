<?php
use yii\helpers\Url;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $users app\models\MongoUser */
/* @var $model app\models\orders\OrderRecord */
/* @var $search_model \app\models\search\SearchModel */

$this->title = "Заказы клиента";

$this->params['breadcrumbs'][] = [
  'label' => 'Заказы',
  'url' => ['admin/user-order'],
  ];
$this->params['breadcrumbs'][] = $this->title;

$columns = [
  [
    'class'=>  \kartik\grid\ExpandRowColumn::className(),
    'width'=>'50px',
    'value'=>function ($model, $key, $index, $column) {
        return GridView::ROW_COLLAPSED;
    }, 
    'detailUrl'=> Url::to(['admin/order-info'])
  ],
  [
    'header'          =>'Даты',
    'class'           => kartik\grid\DataColumn::className(),    
    'vAlign'          =>'middle',
    'format'          => 'raw',
    'value'           => function ($model,$a,$b){
      return "<b>C:</b> " . date("d-m-Y", $model->_id->getTimestamp()) . "<br><b>И:</b> " . date("d-m-Y", $model->update_time);
    },
  ],
  [
    'header'=>'Дата ожидания',
    'attribute' => 'wait_time',
    'vAlign'=>'middle',
    'format'=>['date', 'php:d.m.Y'],
    'class'=> kartik\grid\EditableColumn::className(),    
    'editableOptions'=> function ($model, $key, $index) {      
      return[    
            'header'=>'Дата ожидания',
            'format'=>['date', 'php:d.m.Y'],
            'data'  => function ($model){
              return 1;
            },
            'displayValue' => date("d.m.Y",$model->wait_time),
            'inputType'=> \kartik\editable\Editable::INPUT_DATE,
            "formOptions"=>[
              "action"=> Url::to(["admin/order-change"])
            ],            
          ];
    },
  ],
  [
    'attribute'       =>'status',
    'header'          =>'Статус',
    'class'=> kartik\grid\EditableColumn::className(),
    'value'    => function ($model, $key, $index) {      
      return $model->textStatus();
    },
    'editableOptions'=> function ($model, $key, $index) {      
      return[    
            'header'=>'Статус',            
            'inputType'=> \kartik\editable\Editable::INPUT_DROPDOWN_LIST,
            'data'=> app\models\orders\OrderRecord::$states,//getStatuses(),
            "formOptions"=>[
              "action"=> Url::to(["admin/order-change"])
            ],            
          ];
    },
    'vAlign'          =>'middle',
  ],
  [
    'attribute'       =>'articul',
    'header'          =>'Артикул',
    'class'           => kartik\grid\DataColumn::className(),    
    'vAlign'          =>'middle',
    'format'          =>'raw',
    'value'           => function ($model,$a,$b){
      return "<span>".$model->articul." ".Html::button("<i class=\"glyphicon glyphicon-scissors\"></i>",['onClick'=>'cpyToClipboard("'.$model->articul.'");'])."</span>";
    },
  ],
  [
    'attribute'       =>'name',
    'header'          =>'Деталь',
    'class'           => kartik\grid\DataColumn::className(),    
    'vAlign'          =>'middle',
    'format'          =>'raw',
    'value'           => function ($model,$a,$b) use ($search_model) {
      return  "<b>Ориг.:</b> " . ($model->is_original?"Да":"Нет") . "<br><b>C:</b> " . $search_model->getProviderByCLSID($model->provider)->getName() . " [ " . $model->stock . " ]<br><b>П:</b> " . $model->producer . "<br><b>И:</b> " . $model->name;
    },
  ],
  [
    'header'          =>'Кол-во',
    'class'           => kartik\grid\DataColumn::className(),
    'format'          =>'raw',
    'value'           => function($model,$a,$b){
      /* @var $model app\models\orders\OrderRecord */
      return $model->getAttribute('sell_count')." шт.<br>Упак.<br> ".$model->getAttribute('lot_quantity')." шт.";
    },
    'vAlign'          =>'middle',
  ],
  [
    'header'          =>'Цена',
    'class'           => kartik\grid\DataColumn::className(),
    'format'          =>'raw',
    'value'           => function ($model, $a,$b) use ($user){            
        $user_price = $user->getUserPrice($model->price);      
      return "Наша: ".$model->price."<br> Польз.: ".$user_price;      
    },
    'vAlign'          =>'middle',
  ],
  [
    'attribute'       =>'price_change',
    'header'          =>'Цена меняется',
    'class'           => kartik\grid\BooleanColumn::className(),    
    'vAlign'          =>'middle',
  ],
  [
    'attribute'       =>'pay',
    'header'          =>'Оплачено',
    'class'           => kartik\grid\BooleanColumn::className(),    
    'vAlign'          =>'middle',
  ],
  [
    'attribute'       =>'comment',
    'header'          =>'Комментарий',
    'class'           => kartik\grid\DataColumn::className(),    
    'vAlign'          =>'middle',
  ],
];

echo GridView::widget([
    'id'            => 'orders',
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
      'heading' => "<i class=\"glyphicon glyphicon-book\"></i> Заказы " . $user->getUserName() . " : " . $user->name. ($user->isCompany()?" [ Юр.лицо ]":""),
    ],    
    'persistResize' => false,
  ]);
?>

<script type="text/javascript">
  function cpyToClipboard(text){
    if( window.clipboardData ){
      window.clipboardData.setData("Text",text);
      return;
    };
    if( window.copyToClipboard ){
      window.copyToClipboard(text);
      return;
    }
    window.prompt("Copy to clipboard: Ctrl+C, Enter", text);
  }
</script>
