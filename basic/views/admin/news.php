<?php
use yii\helpers\Html;
use app\models\news\NewsModel;
/* @var $this yii\web\View */
?>
<ul class="a-news-list">  
  <?php foreach ($items as $key=>$value):?>
  <li class="a-news-line clearfix"> 
    <div class = "row" id="uid">      
      <?= Html::label("ID: ".$value->getId());?>
    </div>
    <div class = "row">      
      <?= Html::input("text", "post-icon", $value->getAttribute("icon"));?>
    </div>
    <div class = "row">      
      <?= Html::input("text", "post-header", $value->getAttribute("header"));?>
    </div>
    <div class = "row">      
      <?= Html::textarea("post-text", $value->getAttribute("text"));?>
    </div>
    <div class = "row">      
      <?= Html::checkbox("post-show", $value->isVisible());?>
      <?= Html::label("Отображать","post-show");?>
      <?= Html::buttonInput("Сохранить",['uid'=>$value->getId(),'class'=>"btn btn-info"]); ?>
    </div>
    <div class="news-view">      
      <?php 
        /* @var $value NewsModel */
        echo $value->icon();      
        echo $value->head();
        echo $value->text();      
        echo $value->date();      
      ?>  
    </div>
  </li>
  <?php endforeach;?>
</ul>

<script type="text/javascript">
  admin.news_init();
</script>