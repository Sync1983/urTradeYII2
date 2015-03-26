<?php

use app\models\PartRecord;

/* @var $this yii\web\View */
/* @var $model PartRecord */
$this->title = 'Корзина';
$this->params['breadcrumbs'][] = $this->title;

echo $this->render("grid", [
  'user_basket'   =>$user_basket,
  'guest_basket'  =>$guest_basket,
  'grid_columns'  =>$grid_columns,
  'guest_columns' =>$guest_columns]);

?>

