<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\SetupModel;
use app\models\prices\OverpriceModel;
use app\components\helpers\SocNetHelper;
use yii\helpers\Url;

/* @var $price_model OverpriceModel */
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model SetupModel */

$nets = SocNetHelper::getAvaibleNets();
$active_nets = SocNetHelper::getActiveNets();
$not_active_nets = array_diff($nets, $active_nets);      

$this->title = 'Настройки пользователя';
$this->params['breadcrumbs'][] = $this->title;
?>

<h3>Карточка клиента</h3>  
<h4>Укажите Ваши реальные данные, чтобы мы могли правильно оформить документы и доставить Вам товар.</h4>

<div class="site-contact">
  <?php $form = ActiveForm::begin([
		'id'					  => 'setup-form',
		'method'				  => 'POST',
		'action'				  => Url::to(['site/setup']),		
		'layout'				  => 'horizontal',
		'enableClientValidation'  => false,
		'enableAjaxValidation'	  => false,
		'validateOnSubmit'		  => false,
		'validateOnBlur'		  => false,
		'fieldConfig'			  => [		  
		  'horizontalCssClasses'	=> [
            'label'		=> 'col-sm-3',
            'offset'	=> 'col-sm-offset-3',
            'wrapper'	=> 'col-sm-6',
            'error'		=> '',
            'hint'		=> 'col-sm-5',
        ],
    ],
  ]);?>
  
  <?= $form->errorSummary($model); ?>
  <?= $form->field($model, 'id')->input('text', ['readonly'=>true])->error()->label();?>
  <?= $form->field($model, 'type')->dropDownList(\app\models\forms\SignUpTypeForm::types)
		->error()
		->label();
  ?>
  <?php if($model->company):?>
	<?= $form->field($model, 'name')->error()->label();?>	
  <?php endif;?>
  <?= $form->field($model, 'first_name')->error()->label();?>
  <?= $form->field($model, 'second_name')->error()->label();?>
  <?= $form->field($model, 'addres')->error()->label();?>
  <?= $form->field($model, 'phone')->input('tel')->error()->label();?>
  <?= $form->field($model, 'email')->error()->label();?>
  <?php if($model->company):?>
	<?= $form->field($model, 'inn')->error()->label();?>
	<?= $form->field($model, 'kpp')->error()->label();?>
  <?php endif;?>
  <div class="row col-sm-offset-7">
	<?= Html::submitButton("Сохранить",['class'=>'btn btn-info']);?>	
  </div>
<?php ActiveForm::end();?>

  <div class="socnet-info panel panel-info">
    <div class="panel-heading">
      <h5 class="center-block">Вы можете добавить авторизацию через следующие соц.сети</h5>
    </div>   
    <div class="panel-body">  
      <ul class="socnet-list" style="margin-top: 20px;">
      <?php foreach ($not_active_nets as $net_name):?>
        <li><span class="icon <?=$net_name?>-icon"></span><?= Html::a(SocNetHelper::getNetName($net_name), Url::to(["soclogin/register","net"=>$net_name]));?></li>
      <?php endforeach;?>
      </ul>
    </div>
  </div>
<?php if($model->company):?>  
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
 <?php endif;?> 
</div>
  
<script type="text/javascript">
function add_row(name){
  var body = $("."+name).children("tbody");
  var elem = '<tr><td><input type="text" name="name[]" placeholder="Введите имя"</td><td><input type="number" name="value[]" min=0 max=1000 placeholder="Введите наценку в %"></td><td><a href="" class="btn btn-danger" onclick="del_row(this); return false">&#x232B;</a></td></tr>';
  body.append(elem);  
}

function del_row(item){
  var row = $(item).parent().parent();
  row.remove();
  return false;
}
</script>




