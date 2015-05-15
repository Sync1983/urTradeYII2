<?php
use yii\bootstrap\ActiveForm;
/* @var $this yii\web\View */
/* @var $form ActiveForm */
/* @var $model app\models\forms\YandexPayForm */

$this->title = 'Оплата';
$this->params['breadcrumbs'][] = [
  'label' => 'Заказы',
  'url' => ['order/index'],
  ];
$this->params['breadcrumbs'][] = $this->title;
?>
<h3>Оплата товара</h3>
<?php $form = ActiveForm::begin([
		'id'					  => 'order-ya-pay',
		'method'				  => 'POST',
		'action'				  => 'https://demomoney.yandex.ru/eshop.xml',//'https://money.yandex.ru/eshop.xml',
		'layout'				  => 'horizontal',
		'enableClientValidation'  => true,
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
		'class' => 'pay-rows',
]); 
?>

<?= $form->field($model, 'orderNumber')->input('text', ['name'=>'orderNumber','readonly'=>true])->label();?>
<?= $form->field($model, 'sum')->input('text', ['name'=>'sum'])->label()->hint("Комиссия платежа: <b>3%</b>");?>

<?= $form->field($model, 'custName')->input('text', ['name'=>'custName','readonly'=>true])->label();?>
<?= $form->field($model, 'custAddr')->input('text', ['name'=>'custAddr','readonly'=>true])->label();?>

<?= $form->field($model, 'cps_phone')->input('text', ['name'=>'cps_phone','readonly'=>true])->label();?>
<?= $form->field($model, 'custEMail')->input('text', ['name'=>'custEMail','readonly'=>true])->label();?>

<?=	yii\helpers\Html::radioList('paymentType',$model->paymentType, [
			  'PC'	=> \yii\helpers\Html::img('/img/pay_icon/yandexmoney.png',	  ['alt'=>'Яндекс.Деньги', 'title' => 'Яндекс.Деньги']),
			  'AC'	=> \yii\helpers\Html::img('/img/pay_icon/cards.png',	  ['alt'=>'Банковская карта', 'title' => 'Банковская карта']),
			  'WM'	=> \yii\helpers\Html::img('/img/pay_icon/webmoney-white.png', ['alt'=>'WebMoney', 'title' => 'WebMoney']),
			  'AB'	=> \yii\helpers\Html::img('/img/pay_icon/alfabank-white.png', ['alt'=>'Альфа-Клик', 'title' => 'Альфа-Клик']),
			  'GP'	=> \yii\helpers\Html::img('/img/pay_icon/cash_rub.png', ['alt'=>'Кассы и терминалы', 'title'=>'Кассы и терминалы']),
			  'МА'	=> \yii\helpers\Html::img('/img/pay_icon/masterpass.png', ['alt'=>'MasterPass', 'title'=>'MasterPass']),
			  //'MC'	=> \yii\helpers\Html::img($src, ['alt'=>'Платеж со счета мобильного телефона.']),
			  //'SB'	=> \yii\helpers\Html::img($src, ['alt'=>'Оплата через Сбербанк: оплата по SMS или Сбербанк Онлайн.']),
			  //'MP'	=> \yii\helpers\Html::img($src, ['alt'=>'Оплата через мобильный терминал (mPOS).']),
			  //'PB'	=> \yii\helpers\Html::img($src, ['alt'=>'Оплата через Промсвязьбанк.']),
			],['encode' => false, 'class'=>'payment-select col-sm-offset-3','name'=>'paymentType']);?>

<?= \yii\helpers\Html::hiddenInput('shopId',  $model->shopId); ?>
<?= \yii\helpers\Html::hiddenInput('scid',	  $model->scid); ?>
<?= \yii\helpers\Html::hiddenInput('customerNumber', $model->customerNumber); ?>
<?= \yii\helpers\Html::hiddenInput('cps_email',	$model->cps_email); ?>
<div class="row" style="padding-bottom: 20px;">
<?= \yii\helpers\Html::submitButton("Оплатить",['class'=>'btn btn-info col-md-offset-6']); ?>
</div>
<?php ActiveForm::end(); ?>


