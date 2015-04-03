<?php
use yii\helpers\Url;
use kartik\grid\GridView;
/* @var $this yii\web\View */
$this->title = "Пользователи";
$this->params['breadcrumbs'][] = $this->title;

$columns = [
  [
    'class'=> kartik\grid\SerialColumn::className(),
    'header'=>'#',
    'width'=>'15px',    
    'vAlign'=>'middle',
  ],
  [
    'class'=>  \kartik\grid\ExpandRowColumn::className(),
    'width'=>'50px',
    'value'=>function ($model, $key, $index, $column) {
        return GridView::ROW_COLLAPSED;
    }, 
    'detailUrl'=> Url::to(['admin/user-expand'])
  ],
  [
    'attribute' => 'first_name',
    'class'=> kartik\grid\DataColumn::className(),
    'header'=>'Имя',
    'width'=>'15px',    
    'vAlign'=>'middle',
  ],
  [
    'attribute' => 'second_name',
    'class'=> kartik\grid\DataColumn::className(),
    'header'=>'Фамилия',
    'width'=>'15px',    
    'vAlign'=>'middle',
  ],
  [
    'attribute' => 'name',
    'class'=> kartik\grid\DataColumn::className(),
    'header'=>'Фирма',
    'width'=>'15px',    
    'vAlign'=>'middle',
  ],
  [
    'attribute' => 'type',
    'class'=> kartik\grid\DataColumn::className(),
    'header'=>'Тип записи',
    'width'=>'15px',    
    'vAlign'=>'middle',
    'format' => 'raw',
    'value' => function($model,$a,$b){
      if( $model->type == 'private' ){
        return 'Частное лицо';
      } elseif( $model->type == 'company' ){
        return 'Юридическое лицо';
      }
      return 'Не указано';
    }
  ],
  [
    'attribute' => 'role',
    'class'=> kartik\grid\DataColumn::className(),
    'header'=>'Роль',
    'width'=>'15px',    
    'vAlign'=>'middle',
    'format' => 'raw',
    'value' => function($model,$a,$b){
      if( $model->role == 'user' ){
        return 'Пользователь';
      } elseif( $model->role == 'admin' ){
        return 'Администратор';
      } elseif( $model->role == 'manager' ){
        return 'Менеджер';
      }
      return 'Не указано';
    }
  ],
];
?>

<h4>Пользователи</h4>  

<?= 
  GridView::widget([
    'id'            => 'user-list',
    'dataProvider'  => $list,    
    'hover'         => true,
    'columns'       => $columns,    
    'export'        => [
     ],    
    'pjax'          => false,
    'pjaxSettings'=>[
      'neverTimeout'=>true,      
    ],
    'panel' => [
      'type' => GridView::TYPE_INFO,
      'heading' => "<i class=\"glyphicon glyphicon-piggy-bank\"></i> Пользователи",
    ],
    'toolbar' => [
      [
        'content'=>
          \yii\helpers\Html::a('<i class="glyphicon glyphicon-plus"></i>', ['admin/user-add'], [
            'class' => 'btn btn-default',
            'title' => "Добавить пользователя"
          ]),
      ],
      '{export}',
      '{toggleData}'
    ]
  ])
?>
