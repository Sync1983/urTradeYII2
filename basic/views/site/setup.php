<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\SetupModel;

/* @var $model SetupModel */
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

$this->title = 'Настройки пользователя';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-contact">
    <h3><?= Html::encode($this->title) ?></h3>  
    <h5>Укажите Ваши контактные данные, чтобы мы могли правильно оформить документы и доставить Вам товар.</h5>
    <div class="row setup-style">

      <div class="col-lg-6">
        <?php $form = ActiveForm::begin([
            'id' => 'setup-form',
            'action'=>['site/setup'],
            'enableClientValidation'=>false,
            //'clientOptions'=>['validateOnSubmit'=>true,]
          ]);?>
        
        <?= $form->errorSummary($model); ?>
        <div class="row">
          <?= Html::activeLabel($model, "type",['label'=>'Заказчик']); ?>
          <?= Html::activeDropDownList($model, "type",
              [0=>"Юридическое лицо",
               1=>"Частное лицо"]);?>          
        </div>
        <div class="row">
          <?= Html::activeLabel($model, "name",['label'=>'Название организации']); ?>
          <?= Html::activeInput("text", $model, "name",['size'=>50]);?>          
        </div>
        <div class="row">
          <?= Html::activeLabel($model, "first_name",['label'=>'Имя контактного лица']); ?>
          <?= Html::activeInput("text", $model, "first_name",['size'=>50]); ?>
        </div>
        <div class="row">
          <?= Html::activeLabel($model, "second_name",['label'=>'Фамилия контактного лица']); ?>
          <?= Html::activeInput("text", $model, "second_name",['size'=>50]); ?>
        </div>
        <div class="row">
          <?= Html::activeLabel($model, "inn",['label'=>'ИНН (Для юр.лиц)']); ?>
          <?= Html::activeInput("text", $model, "inn",['size'=>50,'maxlength'=>12]); ?>
        </div>
        <div class="row">
          <?= Html::activeLabel($model, "kpp",['label'=>'КПП (Для юр.лиц)']); ?>
          <?= Html::activeInput("text", $model, "kpp",['size'=>50]); ?>
        </div>
        <div class="row">
          <?= Html::activeLabel($model, "phone",['label'=>'Номер телефона']); ?>
          <?= Html::activeInput("tel", $model, "phone",['size'=>50,'maxlength'=>11]); ?>
        </div>
        <div class="row">
          <?= Html::activeLabel($model, "email",['label'=>'Почта для связи']); ?>
          <?= Html::activeInput("text", $model, "email",['size'=>50]); ?>
        </div>
        <div class="row">
          <?= Html::activeLabel($model, "addres",['label'=>'Адрес доставки груза']); ?>
          <?= Html::activeInput("text", $model, "addres",['size'=>50]); ?>
        </div>
        <div class="row center-block" style="left:50%; position: relative;">
          <?= Html::submitButton("Сохранить");?>
        </div>
        <?php ActiveForm::end();?>
      </div>
      <div class="col-lg-6">
        <h4 class="center-block">Управление наценками для вывода в поиске</h4>
      </div>
    </div>
    
</div>





