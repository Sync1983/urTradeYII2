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
		'action'				  => Url::to(['site/new-user-last-step']),		
		'layout'				  => 'horizontal',
		'enableClientValidation'  => false,
		'enableAjaxValidation'	  => false,
		'validateOnSubmit'		  => false,
		'validateOnBlur'		  => false,
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
<p>Осталось всего два шага</p>
<p>Мы просим Вас указать данные, необходимые для правильного оформления документов и доставки.</p>

<?= $form->errorSummary($model); ?>
	
<?= $form->field($model, 'type')->dropDownList(\app\models\forms\SignUpTypeForm::types)
	  -> error()
	  -> label()
	  -> hint('Уточните представляете ли какое-либо юридическое лицо, либо являетесь частным клиентом'); 
?>
<div class="row col-sm-offset-4" style="padding-bottom: 20px;"> 
  <span style="padding-right: 20px;">Впереди последний шаг!</span>
  <?= Html::submitButton('Далее',['class' => 'btn btn-info']) ?>
</div>

<?php ActiveForm::end(); ?>

<div class="row"> 
  <a class="btn btn-sm btn-block" href="<?= Url::to(['site/profile-discard'])?>">Я не хочу сейчас вводить данные</a>
</div>