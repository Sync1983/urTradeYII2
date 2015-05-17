<?php

/**
 * @author Sync
 */
namespace app\models\pays;
use yii\mongodb\ActiveRecord;

class YaPayOrderRecord extends ActiveRecord{

  public static function collectionName() {
    return 'ya_check_order';
  }

  public function rules() {
    return [
      [['requestDatetime','orderCreatedDatetime',
        'action','md5','shopId','customerNumber','orderNumber',
        'shopSumAmount','shopSumCurrencyPaycash','paymentType',
        'shopArticleId','invoiceId','orderSumAmount','orderSumCurrencyPaycash',
        'orderSumBankPaycash','shopSumBankPaycash','paymentPayerCode'],'safe'],
    ];
  }

  public function attributes() {
    return [
      '_id',
      'requestDatetime',        //	xs:dateTime           
      //Момент формирования запроса в ИС Оператора.
      'action',                 //	xs:normalizedString, до 16 символов	
      //Тип запроса. Значение: «checkOrder» (без кавычек).
      'md5',                    //	xs:normalizedString, ровно 32 шестнадцатеричных символа, в верхнем регистре	
      //MD5-хэш параметров платежной формы, правила формирования описаны в разделе 4.4 «Правила обработки HTTP-уведомлений Контрагентом».
      'shopId',                 //	xs:long               
      //Идентификатор Контрагента, присваиваемый Оператором.
      'shopArticleId',          //	xs:long	
      //Идентификатор товара, присваиваемый Оператором.
      'invoiceId',              //	xs:long	
      //Уникальный номер транзакции в ИС Оператора.
      'orderNumber',            //	xs:normalizedString, до 64 символов	Номер заказа в ИС Контрагента. 
      //Передается, только если был указан в платежной форме.
      'customerNumber',         //	xs:normalizedString, до 64 символов	
      //Идентификатор плательщика (присланный в платежной форме) на стороне Контрагента: номер договора, мобильного телефона и т.п.
      'orderCreatedDatetime',   //	xs:dateTime	
      //Момент регистрации заказа в ИС Оператора.
      'orderSumAmount',         //	CurrencyAmount	
      //Стоимость заказа. Может отличаться от суммы платежа, если пользователь платил в валюте, которая отличается от указанной в платежной форме. В этом случае Оператор берет на себя все конвертации.
      'orderSumCurrencyPaycash',//	CurrencyCode	
      //Код валюты для суммы заказа.
      'orderSumBankPaycash',    //	CurrencyBank	
      //Код процессингового центра Оператора для суммы заказа.
      'shopSumAmount',          //	CurrencyAmount	
      //Сумма к выплате Контрагенту на р/с (стоимость заказа минус комиссия Оператора).
      'shopSumCurrencyPaycash', //	CurrencyCode	
      //Код валюты для shopSumAmount.
      'shopSumBankPaycash',     //	CurrencyBank	
      //Код процессингового центра Оператора для shopSumAmount.
      'paymentPayerCode',       //	YMAccount	
      //Номер счета в ИС Оператора, с которого производится оплата.
      'paymentType',            //	xs:normalizedString	
      //Способ оплаты заказа. Список значений приведен в таблице 6.6.1.
      'code',           
      //Код ответа
      'error'
      //Описание ошибок
    ];
  }
  
}
