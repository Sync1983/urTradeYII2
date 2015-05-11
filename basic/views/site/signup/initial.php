<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\components\helpers\SocNetHelper;

/* @var $this yii\web\View */
/* @var $model \app\models\forms\SignUpForm */
/* @var $form ActiveForm */

$this->title = 'Регистрация';
$this->params['breadcrumbs'][] = $this->title;

$form = ActiveForm::begin([
		'id'					  => 'signup-form',
		'method'				  => 'POST',
		'action'				  => Url::to(['site/signup-wait-mail']),
		'validationUrl'			  => Url::to(['site/signup-validate']),
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
<p>Зарегистрировавшись Вы получаете возможность самостоятельно заказывать запасные части, проводить оплату и отслеживать
  состояние Вашего заказа. Так же становится доступна статистика по Вашим платежам и включается система бонусов и скидок</p>
<p>Если вы уже зарегистрировались, используйте кнопку "Войти" в правом верхнем углу страницы</p>
<p class="col-sm-offset-3"><b>Вы можете использовать авторизацию через социальные сети</b></p>
<ul class="socnet-list col-sm-offset-3" style="margin-top: 10px;margin-bottom: 20px;">
<?php foreach ( SocNetHelper::getAvaibleNets() as $net_name ): ?>
  <li><span class="icon <?=$net_name?>-icon"></span><?= Html::a("С помощью ".SocNetHelper::getNetName($net_name), Url::to(["soclogin/register","net"=>$net_name]));?></li>
<?php endforeach;?>
</ul>
<?= $form->errorSummary($model); ?>

<?= Html::activeHiddenInput($model, 'key'); ?>
	
<?= $form->field($model, 'username')
	  -> error()
	  -> label()
	  -> hint('Данное имя нужно будет ввести при входе на сайт'); 
?>

<?= $form->field($model, 'userpass')
	  -> passwordInput()
	  -> error()
	  -> label()
	  -> hint('Не сообщайте никому текст логина-пароля, во избежании потери доступа к своему аккаунту'); 
?>

<?= $form->field($model, 'passrepeat', ['validateOnChange' => true,'validateOnBlur' => true])
	  -> passwordInput()
	  -> error()
	  -> label()
	  -> hint('Повторите введёный пароль, чтобы избежать возможных ошибок'); 
?>

<?= $form->field($model, 'email',  ['validateOnChange' => true,'validateOnBlur' => true])
	  -> error()
	  -> label()
	  -> hint('Введите реальный адрес почты. На него придёт письмо со ссылкой для подтверждения регистрации.'); 
?>

<?= $form->field($model, 'captcha') ->widget(yii\captcha\Captcha::className())
	  -> label()
	  -> hint('Введите текст на картинке в поле ввода. Если текст сложно разобрать, кликните на изображении для выбора другого текста'); 
?>
<div class="row col-sm-offset-4" style="padding-bottom: 20px;">  
  <?= Html::button('Сбросить',['class' => 'btn btn-danger', 'onClick'=>'clearSignUpForm();']) ?>
  <?= Html::submitButton('Зарегистрироваться',['class' => 'btn btn-info']) ?>
</div>

<?php ActiveForm::end(); ?>

<p>* Почтовый адрес, введённый на этой странице, использутеся только для разовой отправки писем.<br> 
  Мы не сохраняем, не используем в иных целях, кроме указанной, и не передаем эти данные третьим лицам</p>

<script type="text/javascript">
  function clearSignUpForm(){
	$("form#signup-form")[0].reset();
  }
</script>