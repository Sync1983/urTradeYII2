<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\SetupModel;
use app\models\prices\OverpriceModel;

/* @var $model SetupModel */
/* @var $price_model OverpriceModel */
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
          ]);?>
        <?= Html::hiddenInput("type","data"); ?>
        
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
        <div class="panel panel-info">
          <div class="panel-heading">
            <h5 class="center-block">Управление наценками для вывода в поиске</h5>
          </div>
        </div>
        <div class="panel-body">
        <?php $form_price = ActiveForm::begin([
            'id' => 'overprice-form',
            'action'=>['site/setup'],
            'enableClientValidation' => false            
          ]); ?>
        <?= $form_price->errorSummary($price_model); ?>
        <?= Html::hiddenInput("type","overprice"); ?>
        <div class="row" style="margin-left: 10px;margin-top:-30px;padding-bottom: 5px;">
          <?= Html::button("Добавить строку",['hint'=>"asd", 'onclick'=>'add_price_row(this);']); ?>
        </div>
        <?php foreach ($price_model->prices as $name=>$value):?>
          <div class="row" style="margin-left: 10px;padding-bottom: 5px;">
            <?= Html::button(Html::img("/img/cross.png"),['hint'=>"asd", 'onclick'=>'delete_price_row(this);']); ?>
            <?= Html::activeLabel($price_model,"price_name[]",['label'=>"Имя"]); ?>
            <?= Html::activeInput("text", $price_model, "prices_name[]",['value'=>$name,'size'=>'20']); ?>
            <?= Html::activeLabel($price_model,"price_value[]",['label'=>"Значение, %"]); ?>
            <?= Html::activeInput("text", $price_model, "prices_value[]",['value'=>$value,'size'=>'20']); ?>            
          </div>
        <?php endforeach;?>
        <?= Html::submitButton("Сохранить");?>
        <?php ActiveForm::end(); ?>
        </div>
      </div>
    </div>    
</div>

<script type="text/javascript">
  function delete_price_row(item){
    var parent = $(item).parent();
    parent.remove();
  }
  
  function add_price_row(item){
    var parent = $(item).parent().last();
    var text = '<div class="row panel-info" style="margin-left: 10px;padding-bottom: 5px;">'+
            '<?= Html::button(Html::img("/img/cross.png"),["hint"=>"asd", "onclick"=>"delete_price_row(this);","style"=>"margin-right: 5px;"]); ?>'+
            '<?= Html::activeLabel($price_model,"price_name[]",["label"=>"Имя","style"=>"padding-right:5px;"]); ?>'+
            '<?= Html::activeInput("text", $price_model, "prices_name[]",['value'=>"New",'size'=>'20']); ?>'+
            '<?= Html::activeLabel($price_model,"price_value[]",['label'=>"Значение, %","style"=>"padding-right:4px; padding-left:4px"]); ?>'+
            '<?= Html::activeInput("text", $price_model, "prices_value[]",['value'=>"10",'size'=>'20']); ?>'+
            '</div>';
    parent.after(text);
  }
</script>





