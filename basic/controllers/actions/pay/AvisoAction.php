<?php

/**
 * @author Sync
 */
namespace app\controllers\actions\pay;
use yii\base\Action;
use app\models\pays\YaPayAvisoModel;
use app\models\pays\YaPayAvisoRecord;

class AvisoAction extends Action{
  
  public function run() {
    $model = new YaPayAvisoModel();
    $record = new YaPayAvisoRecord();
    
    $model->setAttributes(\yii::$app->request->post());
    $record->setAttributes(\yii::$app->request->post());
    
    \yii::info("YA AvisoOrder".json_encode($model), 'balance');

    if( $model->validate() ){

      $record->setAttribute('code', 0);
      $record->save();

      \yii::info("YA AvisoOrder OK!", 'balance');

      $answer = $this->sendAnswer(0, $model);
      \yii::info("YA AvisoOrder Answer: [". json_encode($answer) ."]", 'balance');
      
      //Проверяем запрос
      //Если он повторный - нужно просто ответить кодом 0
      if( $this->checkInvoice($model->invoiceId) ){
        \yii::info("YA AvisoOrder invoiceId Double: [". $model->invoiceId ."]", 'balance');
        return $answer;
      }
      
      //Иначе - это первый запрос. Необходимо провести оплату      
      $this->buyPart($model);
      
      return $answer;
    }
    
    if( $model->isHashError() ){
      $record->setAttribute('code', 1);
    } else {
      $record->setAttribute('code', 200);
    }
    
    $error_text = implode(",", $model->getFirstErrors());
    
    $record->setAttribute('error', $error_text);
    $record->save();

    \yii::info("YA AvisoOrder ERROR code: ".$record->getAttribute('code')." text: [$error_text]", 'balance');

    $error_text = substr($error_text, 0,254);    
    return $this->sendAnswer($record->getAttribute('code'), $model, $error_text);
  }

  protected function sendAnswer($code, YaPayAvisoModel $model, $error_text ="" ){
    \yii::$app->response->format = 'xml_std';
    $answer = [
      'paymentAvisoResponse' => [
        'performedDatetime'   => date("Y-m-d\TH:i:s.uP"),
        'code'                => $code,
        'shopId'              => $model->shopId,
        'invoiceId'           => $model->invoiceId
      ]
    ];
    return $answer;    
  }

  protected function checkInvoice($invoiceId) {
    $record = \app\models\pays\YaPayOrderRecord::find()->where(['invoiceId' => $invoiceId])->count();
    return $record>1;
  }

  protected function buyPart(YaPayAvisoModel $model){
    //Добавляем сумму на баланс
    $add_money = new \app\models\events\BalanceEvent();
    $add_money->value     = $model->shopSumAmount;
    $add_money->comment   = "Платеж Yandex-Деньги. Invoice: " . $model->invoiceId. " Тип платежа: ".\app\components\helpers\YaMHelper::getNameByType($model->paymentType);
    $add_money->initiator = new \app\models\pays\YaPayType(['invoiceId' => $model->invoiceId]);
    $add_money->reciver   = $model->getUser();
    $add_money->item      = $model->getOrder();
    \yii::$app->trigger(\app\models\events\BalanceEvent::EVENT_ADD_BALANCE, $add_money);
    //Покупаем деталь
    $dec_money = new \app\models\events\BalanceEvent();
    $dec_money->value     = $model->shopSumAmount;
    $dec_money->comment   = "Покупка через Yandex-Деньги. Invoice: " . $model->invoiceId;
    $dec_money->initiator = $model->getUser();
    $dec_money->reciver   = $model->getUser();
    $dec_money->item      = $model->getOrder();
    \yii::$app->trigger(\app\models\events\BalanceEvent::EVENT_DEC_BALANCE, $dec_money);
  }
  
}
