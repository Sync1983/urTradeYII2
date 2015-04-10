<?php

/**
 * Description of Basket
 * @author Sync<atc58.ru>
 */

namespace app\controllers;

use yii;
use yii\widgets\ActiveForm;
use yii\web\Response;
use yii\web\Controller;
use app\models\forms\BasketAddForm;
use app\models\BasketDataProvider;
use yii\web\NotFoundHttpException;
use yii\data\Pagination;
use app\components\helpers\GridHelper;
use app\models\events\BasketEvent;
use app\models\events\OrderEvent;
use app\components\behaviors\OrderBehavior;
use yii\helpers\Url;

class BasketController extends Controller{
  //public vars  
  public $search;
  //protected vars
  protected $items = [];
  //private vars  
  //============================= Public =======================================
  
  public function actionIndex(){    
    /* @var $user \app\models\SiteUser */
    $user = yii::$app->user;
    $all_models = $user->getBasketParts();
    $guest_models = $user->getGuestBasketParts();
    
    $user_basket = new BasketDataProvider([
        'allModels'   => $all_models,
        'pagination'  => new Pagination([
          'totalCount'  => count($all_models),
          'pageSize'        => 20,
        ]),
    ]);
    
    $guest_basket_provider = new BasketDataProvider([
        'allModels'   => $guest_models,
        'pagination'  => new Pagination([
          'totalCount'  => count($guest_models),
          'pageSize'        => 20,
        ]),
    ]);    
    
    $pjax = yii::$app->request->get("_pjax",false);
    $params = [
        'user_basket'   =>$user_basket,
        'guest_basket'  =>$guest_basket_provider,
        'grid_columns' =>  $this->getBasketColumnsDescription(),
        'guest_columns' => $this->getGuestBasketColumnsDescription()];
    
    if($pjax){      
      return $this->view->renderAjax("@app/views/basket/grid", $params);
    } 
    return $this->render("index",$params);
  }
  
  public function actionItemChange(){
    $index  = yii::$app->request->post('editableIndex',-1);
    $key    = yii::$app->request->post('editableKey', "");
    $type   = yii::$app->request->get('type',-1);
    if( ($type==-1) || ($index==-1) || ($key=="") ){
      throw new NotFoundHttpException("Данные неверны");
    }
    $data = [];
    if( $type==0 ){
      $data = yii::$app->request->post('GuestBasketPart',[]);
    } elseif( $type==1 ){
      $data = yii::$app->request->post('BasketPart',[]);      
    }
    
    $event = new BasketEvent();
    $event->key = $key;
    $event->type = intval($type);
    $event->params = $data[$index];
    yii::$app->trigger(\app\models\basket\BasketModel::EVENT_CHANGE_FIELDS,$event);
  }
  
  public function actionItemDelete(){
    $key = yii::$app->request->get("id",false);
    $type = yii::$app->request->get("type",-1);
    if( !$key || ($type == -1) ){
      throw new NotFoundHttpException("Строка не найдена");
    }
    
    $event = new BasketEvent();
    $event->key = $key;
    $event->type = $type;    
    yii::$app->trigger(\app\models\basket\BasketModel::EVENT_REMOVE_FROM_BASKET,$event);
    
    return $this->redirect(Url::to(['basket/index']));
  }
  
  public function actionDeleteList(){
    $keys = yii::$app->request->post("ids",[]);
    $type = yii::$app->request->post("type",-1);
    
    $event = new BasketEvent();
    $event->params['keys'] = $keys;
    $event->type = intval($type);
    yii::$app->trigger(\app\models\basket\BasketModel::EVENT_REMOVE_FROM_BASKET_LIST,$event);
    return $this->redirect(Url::to(['basket/index']));
  }

  public function actionItemTobasket(){
    if(yii::$app->user->isGuest){
      return $this->redirect(Url::to(['basket/index']));      
    }
    $key = yii::$app->request->get("id",-1);    
    if( $key==-1) {
      throw new NotFoundHttpException("Ключ записи не найден");
    }
    $item = yii::$app->user->getGuestBasketPart($key);
    if(!$item){
      throw new NotFoundHttpException("Запись не найдена");
    }
    $event = new BasketEvent();
    $event->params = $item;
    $event->type = BasketEvent::USER_BASKET;    
    yii::$app->trigger(\app\models\basket\BasketModel::EVENT_ADD_TO_BASKET,$event);
    $event = new BasketEvent();
    $event->key = $key;
    $event->type = BasketEvent::GUEST_BASKET;
    yii::$app->trigger(\app\models\basket\BasketModel::EVENT_REMOVE_FROM_BASKET,$event);
    
    return $this->redirect(Url::to(['basket/index']));      
  }
  
  public function actionToBasketList(){    
    $keys = yii::$app->request->post("ids",[]);
    $type = yii::$app->request->post("type",-1);
    
    $event = new BasketEvent();
    $event->type = BasketEvent::USER_BASKET;
    $event->params['items'] = [];
    
    foreach ($keys as $key){
      $item = yii::$app->user->getGuestBasketPart($key);
      if(!$item){
        continue;
      }
      $event->params['items'][] = $item;      
    }
    yii::$app->trigger(\app\models\basket\BasketModel::EVENT_ADD_TO_BASKET_LIST,$event);
    $event = new BasketEvent();
    $event->params['keys'] = $keys;
    $event->type = BasketEvent::GUEST_BASKET;
    yii::$app->trigger(\app\models\basket\BasketModel::EVENT_REMOVE_FROM_BASKET_LIST,$event);
    return $this->redirect(Url::to(['basket/index']));      
  }
  
  public function actionToOrderList(){
    $ids = yii::$app->request->post("ids",[]);    
    $user = yii::$app->user;
    $order_event = new OrderEvent();    
    foreach($ids as $id){
      $item = $user->getBasketPart($id);
      $order_event->items[$id] = $item->getAttributes();      
    }
    $this->trigger(OrderEvent::EVENT_ORDER_ADD,$order_event);
    
    foreach($ids as $id){
      $basket_remove = new BasketEvent();
      $basket_remove->type  = BasketEvent::USER_BASKET;
      $basket_remove->key   = $id;
      yii::$app->trigger(\app\models\basket\BasketModel::EVENT_REMOVE_FROM_BASKET,$basket_remove);
    }
    return $this->redirect(Url::to(['basket/index']));      
  }

  public function actionItemOrder(){
    $key = yii::$app->request->get("id",false);
    if(!$key){
      return $this->redirect(Url::to(['basket/index']));      
    }
    $user = yii::$app->user;
    $item = $user->getBasketPart($key);
    
    if(!$item){
      return $this->redirect(Url::to(['basket/index']));      
    }
    
    $order_event = new OrderEvent();    
    $order_event->items[$key] = $item->getAttributes();    
    $this->trigger(OrderEvent::EVENT_ORDER_ADD,$order_event);
    $basket_remove = new BasketEvent();
    $basket_remove->type  = BasketEvent::USER_BASKET;
    $basket_remove->key   = $key;
    yii::$app->trigger(\app\models\basket\BasketModel::EVENT_REMOVE_FROM_BASKET,$basket_remove);
    return $this->redirect(Url::to(['basket/index']));      
  }

  public function actionAddTo(){ 
    /* @var $model BasketAddForm */
    $model = new BasketAddForm();
    if($model->load(Yii::$app->request->post())&&($model->validate())){
        $model->process();      
    } else{
      echo json_encode(["error"=>1]);
    }
    yii::$app->end();
  }
  
  public function actionAddValidate(){ 
    /* @var $model BasketAddForm */
    $model = new BasketAddForm();
    $model->load(Yii::$app->request->post());
    Yii::$app->response->format = Response::FORMAT_JSON;    
    return ActiveForm::validate($model);
  }
  //============================= Protected ====================================
  //============================= Private ======================================
  
  
  private function getBasketColumnsDescription(){
    return [
      GridHelper::Column1(),
      GridHelper::Column2(),
      GridHelper::Column3(),
      GridHelper::Column4(),
      GridHelper::Column5(),
      GridHelper::Column6(),
      GridHelper::Column7(),
      GridHelper::Column8(),
      GridHelper::Column9()
    ];
  }
  
  private function getGuestBasketColumnsDescription(){
    return [
      GridHelper::Column1(),
      GridHelper::Column2(),
      GridHelper::Column3(),
      GridHelper::Column4(),
      GridHelper::Column5G(),
      GridHelper::Column6(),
      GridHelper::Column7G(),
      GridHelper::Column8G(),
      GridHelper::Column9()
    ];
  }
  //============================= Constructor - Destructor =====================
  public function actions() {
    return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ]
    ];
  }
  
  public function behaviors(){
    return [
      [
        'class' => OrderBehavior::class,        
      ],
      //'on '.OrderEvent::EVENT_ORDER_ADD => [  OrderBehavior::class,'onAdd'],
      \app\components\behaviors\SearchFormBehavior::className(),
    ];
  }
  
}
