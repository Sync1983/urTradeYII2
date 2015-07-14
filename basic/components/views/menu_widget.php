<?php
use yii\web\View;
use yii\helpers\Url;
/* @var $this View */
/* @var $model \app\models\forms\SearchForm */
$model  = $this->params['search_model'];
$this->registerJs('$(".over-price").selectpicker()');
$this->registerJs('$(".over-price").main().changeOverPrice()');
?>

<div class="menu-wrap">
  <nav class="navbar-1 row">
    <div class="col-md-4 col-sm-2 col-xs-4">
      <a class="navbar-header visible-md visible-lg" href="<?= Url::home(); ?>"><img src="/img/logo_left.png" /></a>
      <a class="navbar-header visible-xs visible-sm" href="<?= Url::home(); ?>"><img src="/img/logo_left_min.png" /></a>
    </div>
    <div class="col-md-offset-2 col-sm-offset-4 col-md-5 col-sm-4 col-xs-4">
      <p class="visible-sm-inline visible-xs-inline-block visible-lg visible-md"><span class="visible-md-inline-block visible-lg-inline-block">Вы вошли как: </span> <b><?= $caption ?></b></p>
      <?php if( $isCompany ): ?>
      <p class="visible-sm-inline visible-xs-inline-block visible-lg visible-md"><span class="visible-md-inline-block visible-lg-inline-block">Организация: </span> <b><?= $company ?></b></p>
      <?php endif;?>      
    </div>
    <div class="col-md-1 col-sm-2 col-xs-1 pull-right">
      <a type="button" class="btn btn-info pull-right" href="<?= Url::to(['site/logout']) ?>">Выйти</a>
      <?php if( $isAdmin ):?>
        <a type="button" class="btn btn-info pull-right" href="<?= Url::to(['admin/index']) ?>">Админка</a>
      <?php endif;?>      
    </div>
  </nav>
  <nav class="navbar-2 hidden-xs">
    <ul class="nav-line"><?= $menu ?></ul>
  </nav>
  <nav class="navbar-atc navbar-3 navbar-small resizeble">
  <?php $form = yii\bootstrap\ActiveForm::begin([
          'id'	 => 'search-form',
          'method' => 'get',
          'action' => ['site/search'],
        ]);?>
    <ul class="row">
      <li class="dropdown col-xs-2 col-sm-1 col-md-1 col-lg-1">
        <button class="btn btn-info dropdown-toggle" type="button" id="catalogLabel" data-toggle="dropdown" aria-expanded="true">
          <span class="icon glyphicon glyphicon-list visible-xs-inline-block visible-sm-inline-block visible-md-inline-block"></span>
          <span class="text hidden-xs hidden-sm hidden-md">Каталоги</span>
          <span class="caret"></span>
        </button>
        <?= kartik\dropdown\DropdownX::widget(['items' => $model->catalog]);?>
      </li>
      <li class="search col-xs-6 col-sm-<?= $isCompany?"7":"8"?> col-md-<?= $isCompany?"7":"8"?>">
        <input type="text" name="search_text" id="search-string" placeholder="Введите номер запчасти" autocomplete="off" value="<?= $model->search_text?>" />
      </li>
      <li class="col-xs-1 col-sm-1 col-md-1">
        <button id="search-btn" class="btn btn-info" type="submit">
          <span class="icon glyphicon glyphicon-search" aria-hidden="true"></span>
          <span class="text hidden-xs hidden-sm hidden-md">Искать</span>
        </button>
      </li>
      <li class="col-xs-1 col-sm-<?= $isCompany?"1":"2"?> col-md-<?= $isCompany?"1":"2"?> hidden-xs">
       <div class="checkbox-wrap">
         <?= kartik\checkbox\CheckboxX::widget([
           'options' => ['id'  			 => 'cross'],
           'name'			 => 'cross',
           'value'			 => $model->cross,
           'pluginOptions'	 => ['threeState' => false,]
         ]);?>
       </div>
       <label for= "cross" class="btn btn-info cbx-label <?= $isCompany?"cbx-company":"" ?> " >
         <span class="glyphicon glyphicon-refresh hidden-lg"></span>
         <span class="hidden-xs hidden-sm hidden-md">Аналоги</span>
       </label>
      </li>
      <?php if( $isCompany ):?>
      <li class="col-xs-1 col-sm-1 col-md-1 hidden-xs">
          <select class="over-price show-tick show-menu-arrow hidden-xs" data-style="btn-info" data-width="100%" data-size="5">
            <?= $list ?>
          </select>
          <input type="hidden" name="op" value="<?= $model->over_price ?>"/>
      </li>
      <?php endif;?>
      <li class="menu-button visible-xs-inline-block col-xs-2">
        <button class="btn btn-info dropdown-toggle" type="button" id="menuLabel" data-toggle="dropdown" aria-expanded="true">
          <span class="glyphicon glyphicon-menu-hamburger"></span>
          <span class="text"> Меню</span>
        </button>
        <?= kartik\dropdown\DropdownX::widget([
                'options'=>['class'=>'pull-right','id'=>'menuLabel'],
                'items' => $raw_menu
            ]);?>
      </li>
    </ul>
    <div id="search-helper" class="search-helper"><?= yii\helpers\Html::listBox("", 0, ['a' => 'b']); ?></div>
	<?php yii\bootstrap\ActiveForm::end() ?>
  </nav>
</div>