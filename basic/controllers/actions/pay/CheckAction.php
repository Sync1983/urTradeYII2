<?php

/**
 * @author Sync
 */
namespace app\controllers\actions\pay;
use yii\base\Action;
use app\models\pays\YaPayOrderModel;
use app\models\pays\YaPayOrderRecord;

class CheckAction extends Action{
  
  public function run() {
    $model = new YaPayOrderModel();
    $record = new YaPayOrderRecord();
    
    $model->setAttributes(\yii::$app->request->post());
    $record->setAttributes(\yii::$app->request->post());
    \yii::info("YA CheckOrder ".json_encode($model), 'balance');
    if( $model->validate() ){
      $record->setAttribute('code', 0);
      $record->save();
      \yii::info("YA CheckOrder OK!", 'balance');
      $answer = $this->sendAnswer(0, $model);
      \yii::info("YA CheckOrder Answer: [$answer]", 'balance');
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

    \yii::info("YA CheckOrder ERROR code: ".$record->getAttribute('code')." text: [$error_text]", 'balance');

    $error_text = substr($error_text, 0,254);    
    return $this->sendAnswer($record->getAttribute('code'), $model, $error_text);
  }

  protected function sendAnswer($code, YaPayOrderModel $model, $error_text ="" ){
    \yii::$app->response->format = 'xml_std';
    $answer = [
      'checkOrderResponse' => [
        'performedDatetime'   => date("Y-m-d\TH:i:s.uP"),
        'code'                => $code,
        'shopId'              => $model->shopId,
        'invoiceId'           => $model->invoiceId,
        'orderSumAmount'      => $model->orderSumAmount,
      ]
    ];
    if( $error_text ){
      $answer['checkOrderResponse']['message'] = $error_text;
    }
    return $answer;    
  }
  
}
