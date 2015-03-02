<?php
use app\models\LoginForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
/* @var $form LoginForm */
$user = Yii::$app->user;
/** @var $user SiteUser **/
?>
<?php if($guest==true):?>
  <div class="navbar-right navbar-form collapse navbar-collapse" style="width: 50%;">
    <div class="row col-md-8 col-md-offset-7">
      <?= Html::button("Войти",['class'=>'btn btn-default btn-sm','onClick'=>'main.showLoginWindow("login-window",this)']); ?>
      <?= Html::button("Регистрация",['class'=>'btn btn-info btn-sm']); ?>
      <?= Html::a("Админка",Url::toRoute(['admin/index']),['class'=>'btn btn-info btn-sm']); ?>
    </div>
  </div>
<?php else: ?>  
  <div class="navbar-right collapse navbar-collapse">
    <div class="navbar-text">
      <p>Вы вошли как: <b><?= $user->getLogin();?></b><br>
         Учетная запись для: <b><?= $user->getCaption();?></b></p>
    </div>      
    <div class="navbar-text">
      <?= Html::a("Выйти", Url::to(['site/logout']), ['class'=>'btn btn-info btn-sm','data-method'=>"post"]) ?>
    </div>
  </div>
<?php endif;?>
  <!--<ul  class="nav navbar-nav navbar-right">
    <li>
      <div class="login-icon fb-icon">&nbsp;</div>
      <div class="login-icon tw-icon">&nbsp;</div>
      <div class="login-icon od-icon">&nbsp;</div>
      <div class="login-icon mm-icon">&nbsp;</div>
      <div class="login-icon vk-icon">&nbsp;</div>
    </li>
    <li>      
    </li>
  </ul>-->

<div id="login-window" class="hidden">
  <?php ActiveForm::begin([
          'id' => 'login-form',
          'action'=>['site/login'],
          'options'=>['class'=>'form-horizontal form-group window'],
          'fieldConfig'=>[
            'labelOptions'=>'control-label',
          ]
   ]);?>
  <div class="row">
    <?= Html::activeLabel($form, 'username',['label'=>'Ваш логин:','style'=>'width:100px']); ?>
    <?= Html::activeInput("text", $form, "username",['id'=>"user-name"]); ?>
  </div>
  <div class="row">
    <?= Html::activeLabel($form, 'userpass',['label'=>'Пароль:','style'=>'width:100px']); ?>
    <?= Html::activeInput("password", $form, "userpass",['id'=>"user-pass"]); ?>
  </div>
  <div class="row">
    <?= Html::submitButton("Войти",['class'=>'btn btn-info']); ?>
  </div>
  <?php ActiveForm::end()?>
</div>    