<?php
use app\models\news\NewsProvider;
use app\models\news\NewsModel;
/* var $data NewsProvider */
?>
<div class="panel panel-info">
  <div class="panel-heading">
    <h3 class="panel-title">Новости</h3>
  </div>
  <div class="panel-body">    
      <?php foreach ($data->getModels() as $row):
            /* @var $row NewsModel */?>      
        <div class="list-group-item news-line">
          <?= $row->icon() ?>
          <?= $row->head() ?> 
          <div class="news-subtext">
            <?= $row->date() ?>
            <a href="#">Читать...</a>
          </div>
        </div>
      <?php endforeach;?>
    </ul>
  </div>
</div>

