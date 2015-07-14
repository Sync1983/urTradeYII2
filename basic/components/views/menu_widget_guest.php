<?php
use yii\web\View;
use yii\helpers\Url;
/* @var $this View */
/* @var $model \app\models\forms\SearchForm */
$model  = $this->params['search_model'];
?>

<div class="menu-wrap resizeble">
  <nav class="navbar-1 row">
    <div class="col-md-4 col-sm-2 col-xs-4">
      <a class="navbar-header visible-md visible-lg" href="<?= Url::home(); ?>"><img src="/img/logo_left.png" /></a>
      <a class="navbar-header visible-xs visible-sm" href="<?= Url::home(); ?>"><img src="/img/logo_left_min.png" /></a>
    </div>
    <div class="col-md-offset-6 col-sm-offset-6 col-xs-offset-3 col-md-1 col-sm-2 col-xs-1">
      <button type="button" class="btn btn-info" data-toggle="modal" data-target="#loginModal">Войти</button>
      <a type="button" class="btn btn-info" href="<?= Url::to(['site/signup']) ?>">Регистрация</a>
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
      <li class="search col-xs-6 col-sm-8 col-md-8">
        <input type="text" name="search_text" id="search-string" placeholder="Введите номер запчасти" autocomplete="off" value="<?= $model->search_text?>" />
      </li>
      <li class="col-xs-1 col-sm-1 col-md-1">
        <button id="search-btn" class="btn btn-info" type="submit">
          <span class="icon glyphicon glyphicon-search" aria-hidden="true"></span>
          <span class="text hidden-xs hidden-sm hidden-md">Искать</span>
        </button>
      </li>
      <li class="col-xs-1 col-sm-2 col-md-2 hidden-xs">
       <div class="checkbox-wrap">
         <?= kartik\checkbox\CheckboxX::widget([
           'options' => ['id'  			 => 'cross'],
           'name'			 => 'cross',
           'value'			 => $model->cross,
           'pluginOptions'	 => ['threeState' => false,]
         ]);?>
       </div>
       <label for= "cross" class="btn btn-info cbx-label" >
         <span class="glyphicon glyphicon-refresh hidden-lg"></span>
         <span class="hidden-xs hidden-sm hidden-md">Аналоги</span>
       </label>
      </li>
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