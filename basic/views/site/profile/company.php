<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \app\models\forms\SignUpForm */
/* @var $form ActiveForm */

$this->title = 'Регистрация';
$this->params['breadcrumbs'][] = $this->title;

$form = ActiveForm::begin([
		'id'					  => 'profile-type',
		'method'				  => 'POST',
		'action'				  => Url::to(['site/profile-save']),		
		'validationUrl'			  => Url::to(['site/profile-validate']),		
		'layout'				  => 'horizontal',
		'enableClientValidation'  => false,
		'enableAjaxValidation'	  => true,
		'validateOnSubmit'		  => true,
		'validateOnBlur'		  => true,
		'fieldConfig'			  => [		  
		  'horizontalCssClasses'	=> [
            'label'		=> 'col-sm-3',
            'offset'	=> 'col-sm-offset-3',
            'wrapper'	=> 'col-sm-4',
            'error'		=> '',
            'hint'		=> 'col-sm-5',
        ],
    ],
]);

?>

<h4>Регистрация</h4>
<p>Помните, что данные этой формы будут использованы для оформления заказа.<br>
Пожалуйста, проверьте их правильность при заполнении, чтобы избежать ошибок и проблем при получении заказа</p>

<?= $form->errorSummary($model); ?>
	
<?= $form->field($model, 'name')
	  -> error()
	  -> label()
	  -> hint('Название организации'); 
?>
<?= $form->field($model, 'first_name')
	  -> error()
	  -> label()
	  -> hint('Укажите имя контактного лица'); 
?>
<?= $form->field($model, 'second_name')
	  -> error()
	  -> label()
	  -> hint('Укажите фамилию контактного лица'); 
?>
<?= $form->field($model, 'addres')
	  -> error()
	  -> label()
	  -> hint('По данному адресу будет осуществляться доставка'); 
?>
<?= $form->field($model, 'phone')
	  -> error()
	  -> label()
	  -> hint('Укажите телефон, по которому можно будет связаться с Вашей организацией'); 
?>
<?= $form->field($model, 'email')
	  -> error()
	  -> label()
	  -> hint('Укажите Ваш почтовый адрес (e-mail), если хотите получать уведомления о состоянии заказа'); 
?>
<?= $form->field($model, 'inn')
	  -> error()
	  -> label()
	  -> hint('ИНН организации (для выставления счетов)'); 
?>
<?= $form->field($model, 'kpp')
	  -> error()
	  -> label()
	  -> hint('КПП организации (для выставления счетов)'); 
?>
<div class="row col-sm-offset-4" style="padding-bottom: 20px;">   
  <?= Html::submitButton('Сохранить',['class' => 'btn btn-info']) ?>
</div>

<?php ActiveForm::end(); ?>