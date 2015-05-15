<?php
use yii\helpers\Html;
use yii\helpers\Url;
/* @var $this yii\web\View */
$this->title = "Общая информация";
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">

  <div class="col-lg-4 col-md-8">
    <div class="panel panel-primary">
      <div class="panel-heading">
        <div class="row">
          <div class="col-xs-12 text-right">            
            <div><strong>Пользователи</strong></div>
          </div>
        </div>
      </div>
      <div class="panel-body">
        <div class="row col-xs-offset-0">
          <label class="col-xs-8">Общее количество:</label>
          <?= $info['count']; ?>
        </div>
        <div class="row col-xs-offset-0">
          <label class="col-xs-8">Юридических лиц:</label>
          <?= $info['ur_count']; ?>
        </div>
        <div class="row col-xs-offset-0">
          <label class="col-xs-8">Частных лиц:</label>
          <?= $info['pr_count']; ?>
        </div>
        <div class="row col-xs-offset-0">
          <label class="col-xs-8">Администраторов:</label>
          <?= $info['ad_count']; ?>
        </div>
        <div class="row col-xs-offset-0">
          <label class="col-xs-8">Средняя наценка юр.лица:</label>
          <?= $info['ur_av_price']; ?> %
        </div>
        <div class="row col-xs-offset-0">
          <label class="col-xs-8">Средняя наценка частного лица:</label>
          <?= $info['pr_av_price']; ?> %
        </div>        
        <div class="row col-xs-offset-0">
          <label class="col-xs-8">Общий кредит:</label>
          <?= $info['full_credit']; ?> руб.
        </div>        
        <div class="clearfix"></div>
      </div>      
    </div>
  </div>
  
</div>