<?php
use app\models\LoginForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
/* @var $form LoginForm */
?>
<?php if(YII::$app->user->isGuest): ?>
<a href="#" onclick="login_click(this);return false;">Войти</a>
<?php else: ?>
<a href="<?= Url::to(['site/logout']);?>" data-method="post">(<?= YII::$app->user->getIdentity()->getId();?>) Выйти</a>
<?php endif;?>
<div class="login-window">
  <?php ActiveForm::begin(['id' => 'login-form','action'=>['site/login']]) ?>  
    <div class="login-row">
      <?= Html::activeLabel($form, 'username',['label'=>'Имя:']); ?>     
      <?= Html::activeInput("text", $form, "username",['id'=>"user-name"]); ?>
    </div>
    <div class="login-row">
      <?= Html::activeLabel($form, 'userpass',['label'=>'Пароль:']); ?>     
      <?= Html::activeInput("password", $form, "userpass",['id'=>"user-pass"]); ?>
    </div>
    <div class="login-row">      
      <?= Html::activeCheckbox($form, "rememberMe",['id'=>"remember-me",'label'=>'Запомнить меня']); ?>
    </div>
    <div class="login-row">              
      <input type="submit" value="Войти"/>
      <input type="button" value="Регистрация"/>
    </div>
    <div class="login-row">              
      <div class="login-icon fb-icon">&nbsp;</div>
      <div class="login-icon tw-icon">&nbsp;</div>
      <div class="login-icon od-icon">&nbsp;</div>
      <div class="login-icon mm-icon">&nbsp;</div>
      <div class="login-icon vk-icon">&nbsp;</div>
    </div>
  <?php    ActiveForm::end()?>
</div>