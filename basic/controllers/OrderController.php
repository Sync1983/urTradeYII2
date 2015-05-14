<?php

/**
 * Description of OrdersController
 * @author Sync<atc58.ru>
 */
namespace app\controllers;

use yii;
use yii\web\Controller;
use app\models\orders\OrderRecord;
use app\components\helpers\GridHelper;
use app\models\BasketDataProvider;
use yii\data\Pagination;

class OrderController extends Controller{
  
  //public vars
  //protected vars
  //private vars  
  //============================= Public =======================================
  public function actionIndex(){
    if(yii::$app->user->isGuest){
      $items = [];
    } else {
      $items = OrderRecord::find(['for_user'=>  strval(yii::$app->user->getId()) ])->orderBy(['status'=>SORT_ASC]) ->all();
    }
    $order_list = new BasketDataProvider([
        'allModels'   => $items,
        'pagination'  => new Pagination([
          'totalCount'  => count($items),
          'pageSize'        => 20,
        ]),
    ]);    
    return $this->render('index', ['list' => $order_list, 'columns'=>  $this->columns()]);
  }
  
  public function actionPayByBalance(){
    $id = yii::$app->request->get('id',false);
    if( !$id ){
      throw new yii\web\BadRequestHttpException("Деталь не определена");
    }
    $order = OrderRecord::findOne(['_id' => new \MongoId($id)]);
    if( !$order ){
      throw new yii\web\BadRequestHttpException("Деталь не найдена");
    }
    
    /* @var $balance \app\models\balance\BalanceModel */
    $balance = yii::$app->user->getBalance();
    if( !$balance->isCanBay($order) ){
      throw new yii\web\BadRequestHttpException("На Вашем счету недостаточно денег");
    }
    if( !$balance->isNotDublicate($order) ){
      throw new yii\web\BadRequestHttpException("Деталь уже оплачена");
    }
    $event = new \app\models\events\BalanceEvent();
    $event->initiator = yii::$app->user->getIdentity();
    $event->reciver = yii::$app->user->getIdentity();
    $event->value = $order->getAttribute("sell_count") * yii::$app->user->getUserPrice($order->getAttribute("price")) * 1.0;
    $event->item = $order;    
    yii::$app->trigger(\app\models\events\BalanceEvent::EVENT_DEC_BALANCE,$event);
    return $this->redirect(yii\helpers\Url::to(['order/index']));
  }
  
  public function actionPayByYandex(){
    $id = yii::$app->request->get('id',false);
    if( !$id ){
      throw new yii\web\BadRequestHttpException("Деталь не определена");
    }
    $order = OrderRecord::findOne(['_id' => new \MongoId($id)]);
    if( !$order ){
      throw new yii\web\BadRequestHttpException("Деталь не найдена");
    }
    /* @var $balance \app\models\balance\BalanceModel */
    $balance = yii::$app->user->getBalance();    
    if( !$balance->isNotDublicate($order) ){
      throw new yii\web\BadRequestHttpException("Деталь уже оплачена");
    }
    $form = new \app\models\forms\YandexPayForm();
	$form->initOrder($order);
    return $this->render('yandex_pay',['model'=>$form]);    
  }

  //============================= Protected ====================================  
  //============================= Private ======================================
  private function columns(){
    return [
      GridHelper::ColumnStatus(),
      GridHelper::ColumnWaitTime(),
      GridHelper::Column2(),      
      GridHelper::Column4(),
      GridHelper::Column5O(),
      GridHelper::Column6O(),
      GridHelper::ColumnPay(),
      GridHelper::ColumnPayValue(),
      GridHelper::ColumnComment(),
      GridHelper::ColumnPayAction(),
    ];
  }
  //============================= Constructor - Destructor =====================  
  
  public function behaviors(){
    return [
    \app\components\behaviors\SearchFormBehavior::className(),
    ];
  }

}
