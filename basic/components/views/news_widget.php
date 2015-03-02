<?php
use app\models\news\NewsProvider;
use app\models\news\NewsModel;
use yii\web\View;
/* @var $data NewsProvider */
/* @var $this View */
$this->registerJsFile('/js/news_roller.js',['depends' => ['yii\web\YiiAsset']]);
?>
<div class="panel panel-info" style="margin-top: 20px;" id="news-roller">
  <div class="panel-heading">
    Новости
  </div>
  <div class="panel-body" style="padding: 0;position: relative;">
    <button type="button" class="btn btn-default new-scroll-left" disabled>
      <span class="glyphicon glyphicon-chevron-left"></span>
    </button>
    <button type="button" class="btn btn-default new-scroll-right">
      <span class="glyphicon glyphicon-chevron-right"></span>
    </button>
    <div class="news-clip">
      <div class="news-roller">      
        <?php foreach ($data as $row):
              /* @var $row NewsModel */?>      
          <div class="news-line">
            <?= $row->date() ?>          
            <?= $row->icon() ?>
            <?= $row->head() ?> 
          </div>
        <?php endforeach;?>
      </div>
    </div>
  </div>
</div>

<?php $this->registerJs('news.init();',View::POS_READY);?>
