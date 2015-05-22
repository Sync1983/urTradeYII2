<?php
/**
 * @author Sync
 */
namespace app\controllers\actions\order;
use yii\base\Action;

class PayByAction extends Action {
  public $payment = "balance";
  protected $order;
  protected $balance;

  public function run() {
    $id = \yii::$app->request->get('id',false);
    if( !$id ){
      throw new \yii\web\BadRequestHttpException("Деталь не определена");
    }

    $order = \app\models\orders\OrderRecord::findOne(['_id' => new \MongoId($id)]);
    if( !$order ){
      throw new \yii\web\BadRequestHttpException("Деталь не найдена");
    }

    /* @var $balance \app\models\balance\BalanceModel */
    $balance = \yii::$app->user->getBalance();
    if( !$balance->isNotDublicate($order) ){
      throw new \yii\web\BadRequestHttpException("Деталь уже оплачена");
    }

    $this->order = $order;
    $this->balance = $balance;
    
    if( $this->payment == 'balance') {
      return $this->payByBalance();
    } elseif ($this->payment == 'yandex') {
      return $this->payByYandex();
    }
    throw new \yii\web\BadRequestHttpException('Действие не обнаружено');
    
  }

  protected function payByBalance(){
    if( !$this->balance->isCanBay($this->order) ){
      throw new \yii\web\BadRequestHttpException("На Вашем счету недостаточно денег");
    }

    $event = new \app\models\events\BalanceEvent();
    $event->initiator = \yii::$app->user->getIdentity();
    $event->reciver   = \yii::$app->user->getIdentity();
    $event->value     = $this->order->getAttribute("sell_count") * \yii::$app->user->getUserPrice($this->order->getAttribute("price")) * 1.0;
    $event->item      = $this->order;
    \yii::$app->trigger(\app\models\events\BalanceEvent::EVENT_DEC_BALANCE,$event);
    
    return $this->controller->redirect(\yii\helpers\Url::to(['order/index']));
  }
  
  protected function payByYandex(){    
    $form = new \app\models\forms\YandexPayForm();
    $form->initOrder($this->order);
    
    return $this->controller->render('yandex_pay',['model'=>$form]);
  }
  
}
