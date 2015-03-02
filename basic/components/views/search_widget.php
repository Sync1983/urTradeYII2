<?php
use yii\helpers\Html;
use yii\bootstrap\ButtonDropdown;
use app\models\forms\SearchForm;
use yii\widgets\ActiveForm;
/** @var \app\models\SiteModel $model*/
/* @var $form SearchForm */
?>
<nav class="navbar-search center-block"> 
  <?php  ActiveForm::begin([
          'id' => 'search-form',
          'method' => 'get',
          'action'=>['site/search']
   ]);?>
  <div class="nav navbar-nav center-block" style="width: 100%;">
      <div class="input-group">
        <span class="input-group-btn">            
          <?= ButtonDropdown::widget([
              'label' => 'Каталоги',                    
              'options' => [
                'class' => 'btn btn-info',
              ],
              'dropdown' => [
                'items' => $form->history,
              ]
            ]);?>
        </span>        
        <?= Html::input("text", "search_text", $form->search_text, [
            'class'=>'form-control input-medium',
            'id'=>'search-string',
            'min-size'=>'50',
            'size'=>'20',
            'placeholder'=>"Введите номер запчасти"
          ]);?>
        <div id="search-helper" class="search-helper"><?=  Html::listBox("search-helper-list",0,['a'=>'b']);?></div>      
        <span class="input-group-btn">            
          <?= ButtonDropdown::widget([
              'label' => '',
              'options' => [
                'class' => 'btn-info',
              ],
              'dropdown' => [
                'options' => [
                  'class' => 'dropdown-menu-right'                        
                ],
                'items' => $form->history
              ]
            ]);?>
          <?= Html::submitButton("Искать", ['class'=>'btn btn-info search-button','id'=>'search-button']);?>                 
          <?= Html::checkbox("cross", $form->cross, [                  
                'id'    =>'cross',
                'class' => 'big-check']); ?>
          <?= Html::label("Аналоги","cross",['class'=>'btn btn-info']); ?>
          <?= Html::dropDownList('over-price', 0, $form->over_price,[
                'id'        =>'over-price',
                'class'     =>'over-price btn btn-info',
                'onchange'  =>'main.changeOverPrice();']);?>
        </span>        
      </div>
  </div>
  <?php ActiveForm::end()?>
</nav>