<?php
use yii\bootstrap\Collapse;
use app\models\search\SearchModel;
use yii\web\View;

$this->title = 'Поиск';
$this->params['breadcrumbs'][] = $this->title;
/* @var  $model SearchModel */
/* @var $this View */
$this->registerCssFile("/css/dataTables.css");
$this->registerJsFile("/js/jquery.dataTables.min.js", ['depends'=>'yii\web\JqueryAsset']);

$maker = $model->generateMakerList();
ksort($maker,SORT_STRING);

$items = [];

foreach ($maker as $key => $providers) {
  $ids = [];
  foreach ($providers as $id=>$maker_id){
    if(($id=="")||($maker_id=="")){
      continue;
    }
    $ids[] = "$id:'$maker_id'";
  }
  $ids_line = "{".implode(",", $ids)."}";
  if(($key=="")||($ids_line=="{}")){
    continue;
  }
  $html_table_key = md5($key);
  $items[] = [
    'label' => $key."",
    'content' => "Лучшие варианты деталей производителя $key<table id=\"out-data-$html_table_key\" class=\"out-data\"></table> <a href=\"#\">Показать полный список...</a>",
    'options' => [
      'onclick'   => "main.loadPartList(this,$ids_line,'out-data-$html_table_key')",
      'class'     => 'provider-header'
    ]
    ];
}
if(count($items)==1){
  $items[0]['contentOptions'] = ['class' => 'in'];
}
if(count($items)==0){
  echo "По Вашему запросу \"$model->search\" ничего не найдено!";
} else {
  echo Collapse::widget(['items' => $items]);
}

$this->registerJsFile("/js/search_tables_init.js", ['depends'=>'/js/jquery.dataTables.min.js']);
