<?php
use yii\bootstrap\Collapse;
use app\models\search\SearchModel;
$this->title = 'Поиск';
$this->params['breadcrumbs'][] = $this->title;
/** @var $model SearchModel */

$maker = $model->generateMakerList();
//ksort($maker,SORT_STRING);
$items = [];
foreach ($maker as $key => $value) {
  $tmp = [];
  $tmp['label']=$key;
  $tmp['content']="Список деталей производителя $key";
  $items[] = $tmp;
}
if(count($items)==0){
  echo "По Вашему запросу \"$model->search\" ничего не найдено!";
} else {
  echo Collapse::widget(['items' => $items]);
}