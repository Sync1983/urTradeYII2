<?php

/**
 * @author Sync
 */
namespace app\models\pays;
use yii\base\Model;
use app\models\orders\OrderRecord;
use app\models\MongoUser;

class YaPayOrderModel extends Model{

  protected $_hash_error = false;
  public $requestDatetime;        //	xs:dateTime
  //Момент формирования запроса в ИС Оператора.
  public $action;                 //	xs:normalizedString, до 16 символов
  //Тип запроса. Значение: «checkOrder» (без кавычек).
  public $md5;                    //	xs:normalizedString, ровно 32 шестнадцатеричных символа, в верхнем регистре
  //MD5-хэш параметров платежной формы, правила формирования описаны в разделе 4.4 «Правила обработки HTTP-уведомлений Контрагентом».
  public $shopId;                 //	xs:long
  //Идентификатор Контрагента, присваиваемый Оператором.
  public $shopArticleId;          //	xs:long
  //Идентификатор товара, присваиваемый Оператором.
  public $invoiceId;              //	xs:long
  //Уникальный номер транзакции в ИС Оператора.
  public $orderNumber;            //	xs:normalizedString, до 64 символов	Номер заказа в ИС Контрагента.
  //Передается, только если был указан в платежной форме.
  public $customerNumber;         //	xs:normalizedString, до 64 символов
  //Идентификатор плательщика (присланный в платежной форме) на стороне Контрагента: номер договора, мобильного телефона и т.п.
  public $orderCreatedDatetime;   //	xs:dateTime
  //Момент регистрации заказа в ИС Оператора.
  public $orderSumAmount;         //	CurrencyAmount
  //Стоимость заказа. Может отличаться от суммы платежа, если пользователь платил в валюте, которая отличается от указанной в платежной форме. В этом случае Оператор берет на себя все конвертации.
  public $orderSumCurrencyPaycash;//	CurrencyCode
  //Код валюты для суммы заказа.
  public $orderSumBankPaycash;    //	CurrencyBank
  //Код процессингового центра Оператора для суммы заказа.
  public $shopSumAmount;          //	CurrencyAmount
  //Сумма к выплате Контрагенту на р/с (стоимость заказа минус комиссия Оператора).
  public $shopSumCurrencyPaycash; //	CurrencyCode
  //Код валюты для shopSumAmount.
  public $shopSumBankPaycash;     //	CurrencyBank
  //Код процессингового центра Оператора для shopSumAmount.
  public $paymentPayerCode;       //	YMAccount
  //Номер счета в ИС Оператора, с которого производится оплата.
  public $paymentType;            //	xs:normalizedString
  //Способ оплаты заказа. Список значений приведен в таблице 6.6.1.  
  
  public function isHashError(){    
    return $this->_hash_error;
  }

  public function rules() {
    return [
      [['requestDatetime','orderCreatedDatetime',],'date','format'=>"php:Y-m-d\TH:i:s.uP" ],
      ['action','validateAction'],
      ['md5','validateMD5'],
      ['shopId','validateShopId'],
      [['orderNumber','customerNumber'],'string', 'max'=>64],
      ['orderNumber','validateOrderNumber'],
      ['customerNumber','validateCustomerNumber'],
      ['shopSumAmount', 'validateSumAmount'],
      ['shopSumCurrencyPaycash','in','strict'=>TRUE, 'range' =>['643','10643']],      
      ['paymentType','in','strict'=>TRUE, 'range' =>['AC','PC','WM','AB']],
      [['shopArticleId','invoiceId','orderSumAmount','orderSumCurrencyPaycash','orderSumBankPaycash','shopSumBankPaycash','paymentPayerCode'],'safe'],
      [['requestDatetime','orderCreatedDatetime',
        'action','md5','shopId','customerNumber','orderNumber',
        'shopSumAmount','shopSumCurrencyPaycash','paymentType',
        'invoiceId','orderSumAmount','orderSumCurrencyPaycash',
        'orderSumBankPaycash','shopSumBankPaycash','paymentPayerCode'],'required'],
      [['shopArticleId'],'safe'],
    ];
  }
  
  public function validateAction($attribute,$params) {
    if( $this->$attribute!== 'checkOrder') {
      $this->addError($attribute, "Ошибочное значение $attribute");
      return false;
    }
    return true;
  }
  
  public function validateMD5($attribute,$params) {    
    $hash = md5(
      $this->action                   . ";" .
      $this->orderSumAmount           . ";" .
      $this->orderSumCurrencyPaycash  . ";" .
      $this->orderSumBankPaycash      . ";" .
      $this->shopId                   . ";" .
      $this->invoiceId                . ";" .
      $this->customerNumber           . ";" .
      \yii\helpers\ArrayHelper::getValue(\yii::$app->params, 'ya_shop_password','')
    );
    if( strcasecmp($hash, $this->$attribute) !== 0 ){
      $this->_hash_error = true;
      $this->addError($attribute, "Ошибочное значение $attribute [$hash]");
      return false;
    }
    return true;
  }
  
  public function validateShopId($attribute,$params) {
    $shopId = \yii\helpers\ArrayHelper::getValue(\yii::$app->params, 'ya_shopid', 0);
    if( $this->$attribute != $shopId ){
      $this->addError($attribute, "Ошибочное значение $attribute");
      return false;
    }
    return true;
  }
  
  public function validateOrderNumber($attribute, $params) {
    $id = $this->$attribute;
    $record = OrderRecord::findOne(['_id' => new \MongoId($id)]);

    if( !$record ) {
      $this->addError($attribute,"Заказ отсутствует");
      return false;
    }

    if( $record->pay === true ) {
      $this->addError($attribute,"Повторная оплата заказа");
      return false;
    }
    return true;
  }
  
  public function validateCustomerNumber($attribute, $params) {
    $uid = $this->$attribute;
    $user = MongoUser::findOne(['_id' => new \MongoId($uid)]);
    if( !$user ) {
      $this->addError($attribute, "Ошибочное значение $attribute");
      return false;
    }
    return true;
  }
  
  public function validateSumAmount($attribute, $params) {
    $user   = MongoUser::findOne(['_id' => new \MongoId($this->customerNumber)]);
    $record = OrderRecord::findOne(['_id' => new \MongoId($this->orderNumber)]);

    if ( !$record || !$user ){
      $this->addError($attribute, "Ошибочное значение $attribute");
      return false;
    }
    $price = $user->getUserPrice($record->price) * $record->sell_count * 1.0;
    $new_price = ($record->pay_value + $this->$attribute) * 1.0;
    \yii::info("YA CheckOrder Price [$price] incomming [$new_price]", 'balance');
    if ( ($new_price - $price) > 1.0 ){
      $this->addError($attribute, "Переплата");
      return false;
    }

    return true;
  }
  
}
