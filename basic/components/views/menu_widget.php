<?php
use yii\helpers\Html;
use yii\bootstrap\ButtonDropdown;
/** @var \app\models\SiteModel $model*/
?>
        <nav class="navbar navbar-default navbar-menu" style="top:70px;">          
          <ul class="nav-atc-list">
                <?php foreach ($items as $name=>$params):?>
                  <li>
                    <a href="<?= $params['url'];?>" class="menu-item">
                      <div class="menu-icon <?= $params['class'];?>"></div>
                      <span class="menu-title"><?= $name;?><span class="badge"><?= $params['badge']?$params['badge']:"";?></span></span>
                      <p class="menu-describe"><?= $params['describe'];?></p></a>
                    <span class="divider">&nbsp;</span>
                  </li>
                <?php endforeach;?>                
          </ul>              
          
          
          <!--<div class="container-fluid navbar-search">          
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
          <?php if((!YII::$app->user->isGuest)&&(YII::$app->user->getIdentity()->isAdmin())):?>            
          <div class="container-fluid navbar-search">
            <div class="btn-toolbar" role="toolbar">
              <div class="btn-group" role="group">
                <a href="#" class="btn btn-info">Заказы</a>
                <a href="#" class="btn btn-info">Клиенты</a>
                <a href="#" class="btn btn-info">Прайс-листы</a>
              </div>              
            </div>
          </div>  
          <?php endif;?>-->
        </nav>    
        <div id="search-helper" class="search-helper"></div>
