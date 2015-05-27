<?php
use app\models\news\NewsProvider;
use app\models\news\NewsModel;
use yii\web\View;
/* @var $data NewsProvider */
/* @var $this View */
/* @var $row NewsModel */

app\assets\NewsWidgetAsset::register($this);
$this->registerJs("$(\".carousel-atc\").carousel_atc()");
?>
<div class="carousel-atc wrapper">
  <ol class="indicators"></ol>
  
  <ul class="img-part">
    <?php foreach ($data as $key=>$row): ?>
      <li class="item" data-area="<?=$key?>" img-src="<?=$row->icon?>">
      </li>
    <?php endforeach;?>
  </ul>

  <a class="left arrow" href="#" role="button" data-arrow="prev">
    <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
    <span class="sr-only">Назад</span>
  </a>
  <a class="right arrow" href="#" role="button" data-arrow="next">
    <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
    <span class="sr-only">Вперед</span>
  </a>

  <div class="text-part">
    <?php foreach ($data as $key=>$row): ?>
      <div class="item" data-area="<?=$key?>">
        <div class="header"><?= $row->header?></div>
        <div class="text"><?= $row->text ?></div>
      </div>
    <?php endforeach;?>
  </div>
</div>