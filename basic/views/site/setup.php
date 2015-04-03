<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\SetupModel;
use app\models\prices\OverpriceModel;
use app\controllers\SocloginController;
use yii\helpers\Url;

/* @var $price_model OverpriceModel */
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model SetupModel */

$nets = SocLoginController::getAvaibleNets();
$active_nets = SocloginController::getActiveNets();
$not_active_nets = array_diff($nets, $active_nets);      

$this->title = 'Настройки пользователя';
$this->params['breadcrumbs'][] = $this->title;
?>

<h3>Карточка клиента</h3>  
<h4>Укажите Ваши реальные данные, чтобы мы могли правильно оформить документы и доставить Вам товар.</h4>
<div class="alert alert-info" role="alert">Для редактирования просто кликните на текст</div>
<div class="site-contact">
  <?php $form = ActiveForm::begin([
            'id' => 'setup-form',
            'action'=>['site/setup'],
            'enableClientValidation'=>false,            
          ]);?>
  <?= $form->errorSummary($model); ?>
  <div class="user-badge">
    <div class="id"><p>ID: <?= $model->_id?></p></div>
    <div class="photo"><?= Html::img($model->photo)?></div>
    <div class="text name" style="top:80px;">
      <?= Html::activeLabel($model, "first_name",['label'=>'Имя:']); ?>
      <p class="viewer">
      <span><?= $model->first_name?></span>
        <?= Html::activeInput("text", $model, "first_name"); ?>      
      </p>
    </div>
    <div class="text name" style="top:100px;">
      <?= Html::activeLabel($model, "second_name",['label'=>'Фамилия:']); ?>
      <p class="viewer">
      <span><?= $model->second_name?></span>
        <?= Html::activeInput("text", $model, "second_name"); ?>
      </p>
    </div>
    <div class="text name">
      <?= Html::activeLabel($model, "phone",['label'=>'Телефон:']); ?>
      <p class="viewer">
      <span><?= $model->phone?></span>
        <?= Html::activeInput("tel", $model, "phone",['maxlength'=>11]); ?>
      </p>
    </div>
    <div class="text name">
      <?= Html::activeLabel($model, "email",['label'=>'Почта:']); ?>
      <p class="viewer">
      <span><?= $model->email?></span>
        <?= Html::activeInput("text", $model, "email"); ?>
      </p>
    </div>
    <?php if($model->company):?>      
    <div class="text name">      
      <?= Html::activeLabel($model, "name",['label'=>'Название:']); ?>
      <p class="viewer">
      <span><?= $model->name?></span>
          <?= Html::activeInput("text", $model, "name");?>
      </p>
    </div>
    <div class="text name">      
      <?= Html::activeLabel($model, "inn",['label'=>'ИНН:']); ?>
      <p class="viewer">
      <span><?= $model->inn?></span>
        <?= Html::activeInput("text", $model, "inn",['maxlength'=>12]); ?>        
      </p>
    </div>
    <div class="text name">      
      <?= Html::activeLabel($model, "kpp",['label'=>'КПП:']); ?>
      <p class="viewer">
      <span><?= $model->kpp?></span>
        <?= Html::activeInput("text", $model, "kpp"); ?>
      </p>
    </div>
    <?php endif;?>    
    <div class="text name">
      <?= Html::activeLabel($model, "addres",['label'=>'Адрес доставки:']); ?>
      <p class="viewer">
      <span><?= $model->addres?></span>
        <?= Html::activeInput("text", $model, "addres"); ?>
      </p>
    </div>
    <?php if(!$model->company):?>
      <?= Html::a("Я юридическое лицо",Url::to(["site/i-company"]),['class'=>'btn btn-info']);?>
    <?php endif;?>
    <?= Html::submitButton("Сохранить",['class'=>'btn btn-info']);?>
    <?php ActiveForm::end();?>
  </div>
  
  <div class="socnet-info panel panel-info">
    <div class="panel-heading">
      <h5 class="center-block">Вы можете обновить данные из доступных соц. сетей</h5>
    </div>   
    <div class="panel-body">  
      <ul class="socnet-list">
      <?php foreach ($active_nets as $net_name):?>
        <li class="<?=$net_name?>-icon"><?= Html::a($net_name, Url::to(["soclogin/update-info","net"=>$net_name]),['data-confirm'=>"При обновлении старые данные будет утеряны. Выполнить обновление?"]);?></li>
      <?php endforeach;?>
      </ul>
    </div>
  </div>
  <div class="socnet-info panel panel-info">
    <div class="panel-heading">
      <h5 class="center-block">Вы можете добавить авторизацию через следующие соц.сети</h5>
    </div>   
    <div class="panel-body">  
      <ul class="socnet-list">
      <?php foreach ($not_active_nets as $net_name):?>
        <li class="<?=$net_name?>-icon"><?= Html::a($net_name, Url::to(["soclogin/register","net"=>$net_name]));?></li>
      <?php endforeach;?>
      </ul>
    </div>
  </div>
  
  <div class="socnet-info panel panel-info">
    <div class="panel-heading">
      <h5 class="center-block">Здесь вы можете управлять списком наценок строки поиска</h5>
    </div>   
    <div class="panel-body">  
      <?php $form = ActiveForm::begin([
              'id' => 'prices-form',
              'action'=>['site/setup-prices'],
              'enableClientValidation'=>true,            
              'validateOnType'=>true
              ]);?>
        <table class="over-price-panel">        
          <thead>
            <tr>
              <th style="width: 50%;">Имя</th>
              <th style="width: 50%;">Значение (%)</th>
              <th>&nbsp;</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
              <td colspan="3">
              <button type="button" class="btn btn-info btn-add" onclick="add_row('over-price-panel');">+</button>
              <?= Html::submitButton("Сохранить",['class'=>'btn btn-info'])?></td>
            </tr>
          </tfoot>
          <tbody>            
            <?php foreach ($prices as $name=>$value):?>                
              <tr>
                <td><?= Html::input("text", "name[]", $name)?></td>                
                <td><?= Html::input("number", "value[]", $value,['min'=>0,"max"=>1000])?></td>
                <td><a href="" class="btn btn-danger" onclick="del_row(this); return false">&#x232B;</a></td>
              </tr>
            <?php endforeach;?>
          </tbody>
        </table>
      <?php ActiveForm::end(); ?>
    </div>
  </div>
  
</div>
  






