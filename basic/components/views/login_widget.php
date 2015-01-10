<?php
use app\models\LoginForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $form LoginForm */
?>
<div class="container-fluid" id="navbar-login">
  <?php ActiveForm::begin(['id' => 'login-form','action'=>['site/login']]) ?>  
  <ul  class="nav navbar-nav navbar-right">
    <li>
      <div class="login-icon fb-icon">&nbsp;</div>
      <div class="login-icon tw-icon">&nbsp;</div>
      <div class="login-icon od-icon">&nbsp;</div>
      <div class="login-icon mm-icon">&nbsp;</div>
      <div class="login-icon vk-icon">&nbsp;</div>
    </li>
    <li>
      <?= Html::activeLabel($form, 'username',['label'=>'Имя:']); ?>     
      <?= Html::activeInput("text", $form, "username",['id'=>"user-name"]); ?>    
    
      <?= Html::activeLabel($form, 'userpass',['label'=>'Пароль:']); ?>     
      <?= Html::activeInput("password", $form, "userpass",['id'=>"user-pass"]); ?>
    
      <?= Html::activeCheckbox($form, "rememberMe",['id'=>"remember-me",'label'=>'Запомнить меня']); ?>    
      
      <?= Html::submitButton("Войти",['class'=>'btn btn-default']); ?>
      <?= Html::button("Регистрация",['class'=>'btn btn-info']); ?>
    </li>
  </ul>
  <?php    ActiveForm::end()?>
</div>