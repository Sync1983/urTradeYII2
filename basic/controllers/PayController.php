<?php

/**
 * @author Sync
 */

namespace app\controllers;
use yii\web\Controller;

class PayController extends Controller{
  
  public $enableCsrfValidation = false;

  public function actions() {
    return [
      'check'  => [
        'class' => actions\pay\CheckAction::className()      
      ],
      'aviso'  => [
        'class' => actions\pay\AvisoAction::className()
      ]
    ];
  }

  //https://test.atc58.ru/index.php?r=pay/fail&
  //caller=fail&w
  //bp_ShopKeyID=2350484147&
  //cps_theme=default&
  //wbp_shoperrorinfo=Shop+error&
  //merchant_order_id=54b48c8b03e3356a1d8ba3e0_210515181803_00&
  //customerNumber=5513f65d03e33574058b4594&
  //sumCurrency=10643&
  //wbp_Version=2&
  //wbp_ShopEncryptionKey=hAAAEicBAI%2FgWZ7nPmvPCEf6CyNZrDT%2FM5dqhxF0IQeB%2Bpv7vetU1a35irDRuShgwsyUjxsUxHiwFgoOO51QqednVreeWZO16APsHZhWFQGw4cZhSzOlC5470PgGSGt%2FMZTqxetuSYe9ZbnaOMXqy3grEkzB%2FZ1iim40KHtTyewiIiXJhAKJ&
  //ErrorTemplate=ym2xmlerror&
  //shn=%D0%90%D0%92%D0%A2%D0%9E%D0%A2%D0%95%D0%A5%D0%A1%D0%9D%D0%90%D0%91&
  //_csrf=S2o4UW0uc3d5BW4AD2Y7NBw4Ux0pHzlEfhpaEiVqKhkKAG8WB2RBMA%3D%3D&
  //shopId=33436&
  //ContractTemplateID=523497&
  //orderN=23d2585e9fe082b66c3e450578fa20e25e36541c&
  //cps_changeSum=false&
  //payment-name=%D0%90%D0%92%D0%A2%D0%9E%D0%A2%D0%95%D0%A5%D0%A1%D0%9D%D0%90%D0%91&
  //cps_eplDisable=true&
  //wbp_ShopAddress=77.75.157.167%3A9128&
  //wbp_CorrespondentID=F55EFDE2D16BA0456B2DDBE468A6C0F8B1D0D105&
  //custAddr=%D0%9C%D0%BD%D0%B5&
  //skr_hold=false&
  //wbp_ShopAdditionalAddress=77.75.157.167%3A9138&
  //wbp_InactivityPeriod=2&
  //orderNumber=54b48c8b03e3356a1d8ba3e0&
  //cps_region_id=49&
  //SuccessTemplate=ym2xmlsuccess&
  //WAShopID=1936803660&
  //cps-source=default&
  //custEMail=1983sync%40gmail.com&
  //custName=%D0%9C%D0%B0%D1%80%D0%B0%D1%82&
  //nst_unilabel=1cf00aeb-0001-5000-8000-00000005464b&
  //scid=61146&
  //wbp_messagetype=MoneyInvitationRequest

  //https://test.atc58.ru/index.php?r=pay/ok&
  //cdd_moi=555fc4f603e3351d7c8b49fc_230515030843_00&
  //paymentDatetime=2015-05-23T03%3A09%3A12.524%2B03%3A00&
  //shopSumBankPaycash=1003&
  //requestDatetime=2015-05-23T03%3A09%3A26.425%2B03%3A00&
  //merchant_order_id=555fc4f603e3351d7c8b49fc_230515030843_00&
  //customerNumber=5513f65d03e33574058b4594&
  //sumCurrency=10643&
  //cdd_pan_mask=426803%7C5624&
  //shopSumAmount=1366.80&
  //cps_user_country_code=RU&
  //shopSumCurrencyPaycash=10643&
  //ErrorTemplate=ym2xmlerror&
  //orderSumAmount=1416.37&
  //cdd_eci=&
  //shn=%C0%C2%D2%CE%D2%C5%D5%D1%CD%C0%C1&
  //cps_user_ip=91.144.179.85&
  //_csrf=ZWYyM0lPcnBXCWRiKwc6MzI0WX8NfjhDUBZQcAELKx4kDGV0IwVANw%3D%3D&
  //shopId=33436&
  //shopArticleId=146068&
  //ContractTemplateID=523497&
  //orderSumCurrencyPaycash=10643&
  //cps_changeSum=false&
  //cps_eplDisable=true&
  //skr_sum=1416.37&
  //orderSumBankPaycash=1003&
  //external_id=deposit&
  //invoiceId=2000000492926&
  //paymentType=AC&
  //custAddr=%CC%ED%E5&
  //cdd_rrn=783344812333&
  //orderCreatedDatetime=2015-05-23T03%3A09%3A11.985%2B03%3A00&
  //paymentPayerCode=4100322062290&
  //rebillingOn=false&
  //depositNumber=50917e28fc32e847c86d8dce39d18f3093630d42&
  //yandexPaymentId=257009543455&
  //skr_env=desktop&
  //orderNumber=555fc4f603e3351d7c8b49fc&
  //cps_region_id=49&
  //SuccessTemplate=ym2xmlsuccess&
  //cdd_exp_date=0216&
  //cps-source=default&
  //custEMail=1983sync%40gmail.com&
  //nst_unilabel=1cf1d8cb-0001-5000-8000-000000055465&
  //custName=%CC%E0%F0%E0%F2&
  //requestid=313533313530325f62346232306437343130396566653434656565306162393436353337356465373638303133343035&
  //scid=61146&
  //cdd_auth_code=794687
  
}
