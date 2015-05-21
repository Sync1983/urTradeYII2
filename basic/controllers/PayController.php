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
  
}
