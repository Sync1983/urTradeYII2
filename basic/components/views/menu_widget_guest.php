<?php
use yii\web\View;
use yii\helpers\Url;
/* @var $this View */
/* @var $model \app\models\forms\SearchForm */
$model  = $this->params['search_model'];
?>

<div class="menu-wrap resizeble">
  <nav class="navbar-1 resizeble">
    <a class="navbar-header" href="<?= Url::home(); ?>">&nbsp;</a>
    <div class="head-buttons pull-right">
      <div id="btn-part">
        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#loginModal">Войти</button>
        <a type="button" class="btn btn-info" href="<?= Url::to(['site/signup']) ?>">Регистрация</a>
      </div>
    </div>
  </nav>
  <nav class="navbar-2 resizeble">
    <ul class="nav-line"><?= $menu ?></ul>
  </nav>
  <nav class="navbar-atc navbar-3 navbar-small resizeble">
  <?php $form = yii\bootstrap\ActiveForm::begin([
          'id'	 => 'search-form',
          'method' => 'get',
          'action' => ['site/search'],          
        ]);?>
    <ul>
      <li class="dropdown">
        <button class="btn btn-info dropdown-toggle" type="button" id="catalogLabel" data-toggle="dropdown" aria-expanded="true">
          <span class="icon glyphicon glyphicon-list"></span>
          <span class="text">Каталоги</span> <span class="caret"></span>
        </button>
        <?= kartik\dropdown\DropdownX::widget(['items' => $model->catalog]);?>
      </li>
      <li class="search">
        <input type="text" name="search_text" id="search-string" placeholder="Введите номер запчасти" autocomplete="off" value="<?= $model->search_text?>" />
      </li>
      <li class="buttons">
        <button id="search-btn" class="btn btn-info" type="submit">
          <span class="icon glyphicon glyphicon-search" aria-hidden="true"></span>
          <span class="text">Искать</span></button>
          <div class="checkbox-wrap">
            <?= kartik\checkbox\CheckboxX::widget([
              'options' => ['id'  			 => 'cross'],
              'name'			 => 'cross',
              'value'			 => $model->cross,
              'pluginOptions'	 => ['threeState' => false,]
            ]);?>
          </div>
          <label for= "cross" class="btn btn-info cbx-label">Аналоги</label>
      </li>
      <li class="menu-button">
        <button class="btn btn-info"><span class="glyphicon glyphicon-menu-hamburger" aria-hidden="true" ></span> Меню</button>
      </li>
    </ul>
    <div id="search-helper" class="search-helper"><?= yii\helpers\Html::listBox("", 0, ['a' => 'b']); ?></div>
	<?php yii\bootstrap\ActiveForm::end() ?>
  </nav>
</div>