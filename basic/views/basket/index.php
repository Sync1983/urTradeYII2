<?php

use app\models\PartRecord;

/* @var $this yii\web\View */
/* @var $model PartRecord */
$this->title = 'Корзина';
$this->params['breadcrumbs'][] = $this->title;

?>
<h5>Детали, которые Вы желаете получить одновременно рекомендуется добавлять в заказ списком</h5>
<h5>Оплата за такие заказы будет проводиться за весь список, а доставка осуществляться единовременно</h5>
<?php
echo $this->render("grid", [
  'user_basket'   =>$user_basket,
  'guest_basket'  =>$guest_basket,
  'grid_columns'  =>$grid_columns,
  'guest_columns' =>$guest_columns]);

?>

