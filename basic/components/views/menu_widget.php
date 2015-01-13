<?php
use yii\helpers\Url;
use yii\helpers\Html;
use app\components\LoginWidget;
use yii\bootstrap\ButtonDropdown;
/** @var \app\models\SiteModel $model*/
?>
        <nav class="navbar navbar-default navbar-fixed-top nav-atc">
          <div class="container-fluid">
            <?php if($brand):?>
            <div class="navbar-header">
              <a class="navbar-brand <?= $brand['class'];?>" href="<?= Url::to($brand['url'])?>">
                <img alt="Brand" src="<?= $brand['img'];?>">
              </a>
            </div>              
            <?php endif;?>
            <div class="collapse navbar-collapse">
              <ul class="nav navbar-nav nav-atc-list">
                <?php foreach ($items as $name=>$params):?>
                  <li>
                    <div class="menu-item <?= $params['class'];?>">
                      <?php if($params['badge']):?>
                        <span class="badge"><?= $params['badge'];?></span>
                      <?php endif;?>
                    </div>
                    <a href="<?= $params['url'];?>"><?= $name;?></a>
                  </li>
                  <li class="divider"></li>
                <?php endforeach;?>                
              </ul>
              <ul class="nav navbar-nav navbar-right">
                <?php if(YII::$app->user->isGuest): ?>
                <li><button type="button" class="btn btn-info navbar-btn" onclick="login_click(this);return false;">Войти <span class="caret"></span></button></li>
                <?php else: ?>
                <li><a class="navbar-link" href="<?= Url::to(['site/logout']);?>" data-method="post"><?= YII::$app->user->getIdentity()->getUserName();?> (Выйти)</a></li>
                <?php endif; ?>
              </ul>
            </div>  
          </div>          
          <?= LoginWidget::widget(['form'=> $model->login_form]) ?>                      
          <div class="container-fluid navbar-search">          
            <div class="input-group">              
              <div class="input-group-btn">
                <?= ButtonDropdown::widget([
                    'label' => 'Каталоги',                    
                    'options' => [
                      'class' => 'btn btn-info',
                    ],
                    'dropdown' => [
                      'items' => $model->history,
                    ]
                  ]);?>                
              </div>
              <?= Html::input("text", "search-string", $model->search, [
                  'class'=>'form-control input-medium',
                  'id'=>'search-string',
                  'min-size'=>'50',
                  'size'=>'20',
                  'placeholder'=>"Введите номер запчасти",
                  'aria-describedby'=>'sizing-addon1',
                  'onkeyup'=>'main.searchKeyPress(this)',
                  'onfocus'=>'main.searchKeyPress(this)',                  
                ]);?>              
              <div class="input-group-btn">                  
                <?= ButtonDropdown::widget([
                    'label' => '',
                    'options' => [
                      'class' => 'btn-info',
                    ],
                    'dropdown' => [
                      'options' => [
                        'class' => 'dropdown-menu-right'                        
                      ],
                      'items' => $model->history
                    ]
                  ]);?>                
                <?= Html::button("Искать", ['class'=>'btn btn-info search-button','id'=>'search-button']);?>            
                <?= Html::checkbox("cross", $model->cross, [                  
                      'id'    =>'cross',
                      'class' => 'big-check']); ?>            
                <?= Html::label("Аналоги","cross",['class'=>'btn btn-info']); ?>            
                <?= Html::dropDownList('over-price', $model->op, $model->generateOverPrice(),[
                      'id'        =>'over-price',
                      'class'     =>'over-price btn btn-info',
                      'onchange'  =>'main.changeOverPrice();']);?>            
              </div>          
          </div>          
        </nav>    
        <div id="search-helper" class="search-helper"></div>
