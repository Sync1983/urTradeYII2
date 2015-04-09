<?php
use yii\helpers\Url;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $users app\models\MongoUser */
/* @var $model app\models\orders\OrderRecord */

$this->title = "Заказы";
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
    'header'          =>'Дата создания',
    'class'           => kartik\grid\DataColumn::className(),    
    'vAlign'          =>'middle',
    'value'           => function ($model,$a,$b){
      return date("d-m-Y", $model->_id->getTimestamp());
    },
  ],
  [
    'header'          =>'Дата изменения',
    'class'           => kartik\grid\DataColumn::className(),    
    'vAlign'          =>'middle',
    'value'           => function ($model,$a,$b){
      return date("d-m-Y", $model->update_time);
    },
  ],
  [
    'header'=>'Дата ожидания',
    'attribute' => 'wait_time',
    'vAlign'=>'middle',
    'format'=>['date', 'php:d-m-Y'],
    'class'=> kartik\grid\EditableColumn::className(),    
    'editableOptions'=> function ($model, $key, $index) {      
      return[    
            'header'=>'Дата ожидания',
            'format'=>['date', 'php:d-m-Y'],            
            'inputType'=> \kartik\editable\Editable::INPUT_DATE,
            "formOptions"=>[
              "action"=> Url::to(["admin/order-change",'type'=> 'wait_time'])
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
            'data'=>$model->getStatuses(),
            "formOptions"=>[
              "action"=> Url::to(["admin/order-change",'type'=> 'status'])
            ],            
          ];
    },
    'vAlign'          =>'middle',
  ],
  [
    'attribute'       =>'stock',
    'header'          =>'Сток',
    'class'           => kartik\grid\DataColumn::className(),    
    'vAlign'          =>'middle',
  ],
  [
    'attribute'       =>'articul',
    'header'          =>'Артикул',
    'class'           => kartik\grid\DataColumn::className(),    
    'vAlign'          =>'middle',
    'format'          =>'raw',
    'value'           => function ($model,$a,$b){
      return "<span>".$model->articul." ".Html::button("<i class=\"glyphicon glyphicon-scissors\"></i>",['onClick'=>'copyToClipboard("'.$model->articul.'");'])."</span>";
    },
  ],
  [
    'attribute'       =>'producer',
    'header'          =>'Производитель',
    'class'           => kartik\grid\DataColumn::className(),    
    'vAlign'          =>'middle',
  ],
  [
    'attribute'       =>'name',
    'header'          =>'Наименование',
    'class'           => kartik\grid\DataColumn::className(),    
    'vAlign'          =>'middle',
  ],  
  [
    'attribute'       =>'is_original',
    'header'          =>'Ориг.',
    'class'           => kartik\grid\BooleanColumn::className(),    
    'vAlign'          =>'middle',
  ],
  [
    'header'          =>'Количество',
    'class'           => kartik\grid\DataColumn::className(),
    'format'          =>'raw',
    'value'           => function($model,$a,$b){
      /* @var $model app\models\orders\OrderRecord */
      return $model->getAttribute('sell_count')." шт.<br>Упак. ".$model->getAttribute('lot_quantity')." шт.";
    },
    'vAlign'          =>'middle',
  ],
  [
    'header'          =>'Цена',
    'class'           => kartik\grid\DataColumn::className(),
    'format'          =>'raw',
    'value'           => function ($model, $a,$b) use ($users){      
      if(isset($users[$model->for_user])){
        $user = $users[$model->for_user];
        $user_price = $user->getUserPrice($model->price);        
      } else {
        $user_price = "Не найден";
      }
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


/*Id 
 * Provider 
 * Maker Id	
 
 
 * Info	
 * Update Time	
 
 * Count	
 
  
 * Comment	
 * Status	
 
 */





?>

<?php
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
      'heading' => "<i class=\"glyphicon glyphicon-book\"></i> Заказы",
    ],    
    'persistResize' => false,
  ]);
?>

<script type="text/javascript">
  function copyToClipboard(text){
    /*if( window.clipboardData ){
      window.clipboardData.setDate("Text",text);
    };
    if( window.copyToClipboard ){
      window.copyToClipboard(text);
    }*/
    window.prompt("Copy to clipboard: Ctrl+C, Enter", text);
  }
</script>
