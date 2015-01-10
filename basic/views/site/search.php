<?php
use yii\bootstrap\Collapse;
use app\models\search\SearchModel;
$this->title = 'Поиск';
$this->params['breadcrumbs'][] = $this->title;
/** @var $model SearchModel */

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
  $items[] = [
    'label' => $key."",
    'content' => "Список деталей производителя $key",
    'options' => [
      'onclick'   => 'this.load_part_list('.$ids_line.')',
      'class'     => 'provider-header'
    ]
    ];
}
if(count($items==1)){
  $items[0]['contentOptions'] = ['class' => 'in'];
}
if(count($items)==0){
  echo "По Вашему запросу \"$model->search\" ничего не найдено!";
} else {
  echo Collapse::widget(['items' => $items]);
}