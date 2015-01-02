<?php
use app\models\User;
use yii\helpers\Url;
/*  @var $model \app\models\User */
?>
<?php if(YII::$app->user->isGuest)
        echo 123;?>
<a href="#" onclick="login_click(this);return false;">Войти</a>
<div class="login-window">
  <form action="<?= Url::to(['site/login']);?>" method="POST">    
    <div class="login-row">
      <label for="user-name">Имя:</label>
      <input id="user-name" type="text" name="username"/>
    </div>
    <div class="login-row">
      <label for="user-pass">Пароль:</label>
      <input id="user-pass" type="password"/>
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
  </form>
</div>