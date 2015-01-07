<?php
use yii\bootstrap\Collapse;
use app\models\search\SearchModel;

/** @var $model SearchModel */

$maker = $model->generateMakerList();
//ksort($maker,SORT_STRING);
$itmes = [];
foreach ($maker as $key => $value) {
  $tmp = [];
  $tmp['label']=$key;
  $tmp['content']="Список деталей производителя $key";
  $items[] = $tmp;
}

echo Collapse::widget(['items' => $items]);