<?php
/**
 * @author Sync
 */
namespace app\controllers\actions\order;
use yii\base\Action;

class OrderActions extends Action {
  public $type = "none";

  public function run() {
    
    if( $this->type == 'delete') {
      return $this->deleteItem();
    }
    
    throw new \yii\web\BadRequestHttpException('Действие не обнаружено');
    
  }

  protected function deleteItem(){
    $id = \yii::$app->request->get('id',false);
    if( !$id ){
      throw new \yii\web\BadRequestHttpException('Деталь не найдена');
    }

    $order = \app\models\orders\OrderRecord::findOne(['_id' => new \MongoId($id)]);
    if( !$order ){
      throw new \yii\web\BadRequestHttpException('Деталь не найдена');
    }
    //Нельзя удалить заказ, который уже оплачен, либо размещен
    if( $order->status !== \app\models\orders\OrderRecord::STATE_WAIT_PAY ){
      throw new \yii\web\BadRequestHttpException('Заказ нельзя удалить');
    }

    if( !$order->delete() ){
      throw new \yii\web\BadRequestHttpException('Ошибка при удалении заказа');
    }
    
    return $this->controller->redirect(\yii\helpers\Url::to(['order/index']));
  }
  
}
