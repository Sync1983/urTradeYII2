<?php

/**
 * @author Sync
 */
namespace app\models\forms;

use yii\base\Model;

class YandexPayForm extends Model{
  /* @var $order app\models\orders\OrderRecord */
  public $order;
  /* @var $shopId Integer */
  public $shopId;	  	  // xs:long, обязательный	Идентификатор Контрагента, выдается Оператором.
  /* @var $scid Integer */
  public $scid;			  // xs:long, обязательный	Номер витрины Контрагента, выдается Оператором.
  /* @var $sum float */
  public $sum;			  // CurrencyAmount, обязательный	Стоимость заказа.
  /* @var $customerNumber String */
  public $customerNumber; // xs:normalizedString, до 64 символов, обязательный	Идентификатор плательщика в ИС Контрагента. В качестве идентификатора может использоваться номер договора плательщика, логин плательщика и т. п. Возможна повторная оплата по одному и тому же идентификатору плательщика.
  
  //public $shopArticleId;  // xs:long, необязательный	Идентификатор товара, выдается Оператором. Применяется, если Контрагент использует несколько платежных форм для разных товаров.
  /* @var $orderNumber String */
  public $orderNumber;	  // xs:normalizedString, до 64 символов, необязательный	Уникальный номер заказа в ИС Контрагента. Уникальность контролируется Оператором в сочетании с параметром shopId. Если платеж с таким номером заказа уже был успешно проведен, то повторные попытки оплаты будут отвергнуты Оператором.
  /* @var $cps_email String */  
  public $cps_email;	  // xs:string, до 100 символов, необязательный	Адрес электронной почты плательщика. Если он передан, то соответствующее поле на странице подтверждения платежа будет предзаполнено (шаг 3 на схеме выше).
  /* @var $cps_phone String */  
  public $cps_phone;	  // xs:string, до 15 символов, только цифры, необязательный	Номер мобильного телефона плательщика. Если он передан, то соответствующее поле на странице подтверждения платежа будет предзаполнено (шаг 3 на схеме выше). Номер телефона используется при оплате наличными через терминалы.
  /* @var $paymentType String */  
  public $paymentType;	  // xs:normalizedString, до 5 символов, необязательный	Способ оплаты. Например:
						  //  PC — оплата из кошелька в Яндекс.Деньгах;
						  //  AC — оплата с произвольной банковской карты.
  /* @var $custName String */  
  public $custName;		  // xs:string, необязательный	ФИО плательщика.
  /* @var $custAddr String */  
  public $custAddr;		  // xs:string, необязательный	Адрес доставки товара или адрес проживания плательщика.
  /* @var $custEMail String */  
  public $custEMail;	  // xs:string, необязательный	Адрес электронной почты плательщика, только для отправки в email-уведомлениях.
  //public $orderDetails;	  // xs:string, необязательный	Детали заказа: список приобретенных товаров, их количество, назначение платежа и т. п.
  //public $shopSuccessURL; // xs:string, до 250 символов, необязательный	URL, на который нужно отправить плательщика в случае успеха перевода. Используется при выборе соответствующей опции подключения Контрагента (см. раздел 6.1 «Параметры подключения Контрагента»).
  //public $shopFailURL;	  // xs:string, до 250 символов, необязательный	URL, на который нужно отправить плательщика в случае ошибки оплаты. Используется при выборе соответствующей опции подключения Контрагента.
  
  public $test_payment = true;
  public $test_result = "success";
  public $real_sum    = 0.0;
  
  public function init() {
	if( \yii::$app->user->isGuest ){
	  return parent::init();	  
	}
	/* @var $user app\models\MongoUser */
	$user = \yii::$app->user->identity;
	$this->custName	  = $user->getAttribute('first_name','');
	$this->custEMail  = $user->getAttribute('email',false);
	$this->cps_email  = $user->getAttribute('email',false);
	$this->cps_phone  =	$user->getAttribute('phone',false);
	$this->custAddr	  =	$user->getAttribute('addres',false);
	$this->customerNumber =	$user->getId();
	$this->paymentType= 'PC';
	$this->scid		  = \yii\helpers\ArrayHelper::getValue(\yii::$app->params, 'ya_scid', 0);
	$this->shopId	  = \yii\helpers\ArrayHelper::getValue(\yii::$app->params, 'ya_shopid', 0);
	$this->sum		  = 0.0;
	return parent::init();
  }
  
  public function initOrder(\app\models\orders\OrderRecord $order){
	if( !$order || \yii::$app->user->isGuest ){
	  return false;
	}
	$this->order = $order;
	$this->orderNumber = (string) $order->getAttribute("_id");
	$this->real_sum = \yii::$app->user->getUserPrice($order->price)*$order->sell_count - $order->pay_value;
  $this->sum = \app\components\helpers\YaMHelper::addPercent($this->paymentType, $this->real_sum);
	return true;
  }

  public function rules() {
	return [            
            [['order','shopId','scid','sum','customerNumber','orderNumber'], 'required'],            
            ['customerNumber', 'string', 'max'=>64],
            ['orderNumber', 'string', 'max'=>64],
            ['cps_email', 'string', 'max'=>100],
            ['cps_phone', 'string', 'max'=>15],
            ['custName', 'string', 'max'=>50],
            ['custAddr', 'string', 'max'=>150],
            ['custEMail', 'string', 'max'=>100],
            ['cps_email', 'email'],
            ['custEMail', 'email'],
            ['paymentType', 'in','strict'=>TRUE, 'range' =>['AC','PC','WM','AB','GP','MA']],
            ['sum', 'match', 'pattern' => '/\d{1,12}\.\d{0,2}$/i','message'=>"Значение должно соответствовать формату xxx.xx"],
            ['sum', 'double', 'max' => $this->sum]
    ];
  }
  
  public function attributeLabels() {
	return [
	  'sum'			=> 'Сумма',
	  'orderNumber'	=> 'Номер заказ',      
      'cps_phone'	=> 'Телефон',
      'custName'	=> 'Имя',
      'custAddr'	=> 'Адрес доставки',      
      'cps_email'	=> 'Электронная почта',
      'custEMail'	=> 'Электронная почта',
      'paymentType'	=> 'Тип платежа',
	  
	];	
  }
  
}
