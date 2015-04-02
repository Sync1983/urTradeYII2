<?php
use app\models\news\NewsProvider;
use app\models\news\NewsModel;
use yii\web\View;
/* @var $data NewsProvider */
/* @var $this View */
$this->registerJs("$('.carousel').carousel();");
?>
<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">  
  <ol class="carousel-indicators">
   <?php $pos = 0;
    foreach ($data as $key=>$row):     
              /* @var $row NewsModel */?>      
     <li data-target="#carousel-example-generic" data-slide-to="<?=$key?>" class="<?=!($pos++)?"active":""?>"></li>    
   <?php endforeach;?>    
  </ol>

  <!-- Wrapper for slides -->
  <div class="carousel-inner" role="listbox">
   <?php  $pos = 0;
          foreach ($data as $key=>$row):     
              /* @var $row NewsModel */?>     
    <div class="item <?=!($pos++)?"active":""?>">
      <img src="<?=$row->icon?>" alt="">
      <div class="carousel-caption">
        <h4><?= $row->header?></h4>
        <p><?= $row->text ?></p>        
      </div>
    </div>
   <?php endforeach;?>
  </div>

  <!-- Controls -->
  <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
    <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
    <span class="sr-only">Назад</span>
  </a>
  <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
    <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
    <span class="sr-only">Вперед</span>
  </a>
</div>