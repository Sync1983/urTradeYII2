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
use app\models\forms\SearchForm;
use app\models\BasketDataProvider;
use yii\web\NotFoundHttpException;
use yii\data\Pagination;
use kartik\grid\CheckboxColumn;
use kartik\grid\DataColumn;
use kartik\grid\EditableColumn;
use kartik\editable\Editable;
use kartik\grid\ActionColumn;
use yii\helpers\Url;
use yii\helpers\Html;
use app\models\events\BasketEvent;

class BasketController extends Controller{
  //public vars  
  public $search;
  //protected vars
  protected $items = [];
  //private vars  
  //============================= Public =======================================
  public function actions() {
    return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ]
    ];
  }
  
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
    
    if($pjax){      
      return $this->view->renderAjax("grid", [
        'user_basket'   =>$user_basket,
        'guest_basket'  =>$guest_basket_provider,
        'grid_columns' =>  $this->getBasketColumnsDescription(),
        'guest_columns' => $this->getGuestBasketColumnsDescription()]);
    } 

    return $this->render("index",[
      'user_basket'  =>$user_basket,
      'guest_basket' =>$guest_basket_provider,
      'grid_columns' =>  $this->getBasketColumnsDescription(),
      'guest_columns' => $this->getGuestBasketColumnsDescription()]);
  }
  
  public function actionItemChange(){
    $index = yii::$app->request->post('editableIndex',-1);
    $key = yii::$app->request->post('editableKey', "");
    $type = yii::$app->request->get('type',-1);
    if( ($type==-1) || ($index==-1) || ($key=="") ){
      throw new NotFoundHttpException("Данные не верны");
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


  /*public function actionChangeBasketCount(){
    $parts = yii::$app->user->identity->getBasketParts();
    
    $item = $this->findAndChangeValueInList($parts, "PartRecord", "sell_count");
    if($item){
      yii::$app->user->identity->addNotify("Изменено количество на ".$item->getAttribute("sell_count")." шт.");
    }
    return;        
  }
  
  public function actionChangeBasketComment(){
    $parts = yii::$app->user->identity->getBasketParts();
 
    $item = $this->findAndChangeValueInList($parts, "PartRecord", "comment");
    if($item){
      yii::$app->user->identity->addNotify("Изменено количество на ".$item->getAttribute("comment")." шт.");
    }
    return;        
  }
  
  public function actionChangeGuestBasketCount(){
    $guest_id = GuestBasket::getIdFromCookie();
    if($guest_id){
      $guest_basket = GuestBasket::getById($guest_id);      
    }
    
    if(!$guest_basket){
      echo json_encode(['output'=>1,'message'=>'Гостевая корзина пуста']);
      yii::$app->end();
      return;        
    }    
    
    $list = &$guest_basket->getItems();
    
    $item = $this->findAndChangeValueInList($list, "GuestPartRecord", "sell_count");
    if($item){
      yii::$app->user->identity->addNotify("Изменено количество на ".$item->getAttribute("sell_count")." шт.");
      $guest_basket->save();
    }    
    return;        
  }
  
  public function actionChangeGuestBasketComment(){
    $guest_id = GuestBasket::getIdFromCookie();
    if($guest_id){
      $guest_basket = GuestBasket::getById($guest_id);      
    }
    
    if(!$guest_basket){
      echo json_encode(['output'=>1,'message'=>'Гостевая корзина пуста']);
      yii::$app->end();
      return;        
    }    
    
    $list = &$guest_basket->getItems();
    
    $item = $this->findAndChangeValueInList($list, "GuestPartRecord", "comment");
    if($item){
      yii::$app->user->identity->addNotify("Комментарий изменен на ".$item->getAttribute("comment"));
      $guest_basket->save();
    }    
    return;        
  }*/
  
  public function actionItemDelete(){
    $key = yii::$app->request->get("id",false);
    $type = yii::$app->request->get("type",-1);
    if( !$key || ($type == -1) ){
      throw new NotFoundHttpException("Строка не найден");
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
  private function Column1(){
    return [
        'attribute'=>'update_time',
        'class'=> DataColumn::className(),    
        'header'=>'От',
        'width'=>'50px',
        'format'=>'raw',
        'headerOptions'=>['class'=>'kartik-sheet-style'],    
        'value'=>function ($model, $key, $index, $widget) { 
            return "<span>".date("H:i",$model->update_time)."<br>".date("d-m-y",$model->update_time)."</span>";
        },
      ];        
  }
  private function Column2(){
    return [
        'headerOptions'=>['class'=>'kartik-sheet-style'],
        'header'=>'Деталь',
        'format' => 'raw',    
        'hAlign'=>'center',
        'value'=>function ($model, $key, $index, $widget) {    
            return "<span>[ ".$model->articul." ] ".$model->producer."<br><b>".$model->name."</b></span>";
        },
      ];
  }
  
  private function Column3(){
    return [
        'attribute'=>'price',    
        'header'=>'Цена',
        'headerOptions'=>['class'=>'kartik-sheet-style'],
        'format' => 'raw',
        'value'=>function ($model, $key, $index, $widget) { 
            $price = yii::$app->user->getUserPrice($model->price);
            $delta = round($price-$price*0.1,2)." - ".round($price+$price*0.1,2);
            return "<span>".$price.($model->price_change==1?"<br>$delta":"")."</span>";
        },
      ];
  }
  
  private function Column4(){
    return [
        'attribute'=>'shiping',    
        'header'=>'Срок',
        'headerOptions'=>['class'=>'kartik-sheet-style'],
        'width'=>'50px',
      ];
  }
  
  private function Column5(){
    return [
        'attribute'=>'sell_count',    
        'header'=>'Кол-во',
        'headerOptions'=>['class'=>'kartik-sheet-style'],
        'pageSummary'=>true,    
        'width'=>'50px',
        'format'=>['decimal', 0],    
        'class'=>  EditableColumn::className(),
        'editableOptions'=> function ($model, $key, $index) {      
          return[    
            'header'=>'Количество', 
            'inputType'=>  Editable::INPUT_SPIN,
            "formOptions"=>[
              "action"=> Url::to(["basket/item-change",'type'=> BasketEvent::USER_BASKET])
            ],       
            'pluginEvents'      => [
              "editableSuccess"=>"function(event, val, form, data) { "
              . "$.pjax.reload({container:'#user-basket'});"
              . "}",
            ],
            'options'=>[
                'pluginOptions'=>[    
                  'multiple'=>false,
                  'min'=>1, 
                  'max'=>100,              
                  'postfix' => 'шт.',
                ]
            ]
          ];
        },
      ];
  }
  
  private function Column6(){
    return [
        'class'         =>'kartik\grid\FormulaColumn',
        'header'        =>'Cумма',
        'headerOptions' =>['class'=>'kartik-sheet-style'],
        'format'        =>['decimal', 2],
        'width'         =>'100px',
        'value'         => function ($model, $key, $index, $widget) { 
            $p = compact('model', 'key', 'index');
            $price = yii::$app->user->getUserPrice($model->price);
            return $price * $widget->col(4, $p);
        },
        'mergeHeader'   => true,
        'pageSummary'   => true,
        'footer'        => true
      ];
  }
  
  private function Column7(){
    return [
        'attribute'       =>'comment',
        'header'          =>'Комментарий',
        'class'           =>  EditableColumn::className(),
        'headerOptions'   =>['class'=>'kartik-sheet-style'],    
        'width'           =>'150px',
        'refreshGrid'     => true,
        'editableOptions' => function ($model, $key, $index) {      
          return[
            'header'      =>'Количество', 
            'inputType'   =>  Editable::INPUT_TEXT,
            'formOptions' =>[
              "action"=>  Url::to(["basket/item-change",'type'=> BasketEvent::USER_BASKET])
            ],        
          ];
        },
      ];
  }
  
  private function Column8(){
    return [
        'class'=> ActionColumn::className(),
        'header' => 'Действия',
        "template" => '{delete} {order}',
        'buttons' => [      
          'order'=> function($url,$model){
            $label = '<i class="glyphicon glyphicon-shopping-cart"></i>';
            $options = ['title'=>'Разместить заказ', 
                        'data-toggle'=>'tooltip',
                        'data-confirm' => 
                          "Вы хотите заказать эту деталь? <br>[ ".
                          $model->articul." ] ".
                          $model->producer."<br><b>".
                          $model->name."</b><br>".
                          "В количестве: <b>".$model->sell_count."</b> шт. ".
                          "По цене: ".yii::$app->user->getUserPrice($model->price)." руб. за шт.<br>".
                          "Общая цена составит: ".(yii::$app->user->getUserPrice($model->price)*$model->sell_count)." руб.",
              ];
            return Html::a($label, $url,$options);
          }],
        'deleteOptions' => ['label' => '<i class="glyphicon glyphicon-remove"></i>','title'=>'Удалить запись из корзины', 'data-toggle'=>'tooltip'],    
        'urlCreator'=>function($action, $model, $key, $index) { return Url::to(['basket/item-'.$action,"id"=>$key,"type"=>  BasketEvent::USER_BASKET]); },
        'headerOptions'=>['class'=>'kartik-sheet-style'],
      ];
  }
  private function Column9(){
    return [
        'class'         =>  CheckboxColumn::className(),    
        'headerOptions' => ['class'=>'kartik-sheet-style'],
        'width'         => "35px",
      ];
  }
  
  private function Column5G(){
    return [
    'attribute'=>'sell_count',    
    'header'=>'Кол-во',
    'headerOptions'=>['class'=>'kartik-sheet-style'],
    'pageSummary'=>true,    
    'width'=>'50px',
    'format'=>['decimal', 0],    
    'class'=>  EditableColumn::className(),
    'editableOptions'=> function ($model, $key, $index) {      
      return[    
        'header'=>'Количество', 
        'inputType'=>  Editable::INPUT_SPIN,
        "formOptions"=>[
          "action"=> Url::to(["basket/item-change",'type'=> BasketEvent::GUEST_BASKET])
        ],       
        'pluginEvents'      => [
          "editableSuccess"=>"function(event, val, form, data) { "
          . "$.pjax.reload({container:'#guest-basket'});"
          . "}",
        ],
        'options'=>[
            'pluginOptions'=>[    
              'multiple'=>false,
              'min'=>1, 
              'max'=>100,              
              'postfix' => 'шт.',
            ]
          ]
        ];
      },
    ];
  }
  
  private function Column7G(){
    return [
    'attribute'       =>'comment',
    'header'          =>'Комментарий',
    'class'           =>  EditableColumn::className(),
    'headerOptions'   =>['class'=>'kartik-sheet-style'],    
    'width'           =>'150px',
    'refreshGrid'     => true,
    'editableOptions' => function ($model, $key, $index) {      
      return[
        'header'      =>'Количество', 
        'inputType'   =>  Editable::INPUT_TEXT,
        'formOptions' =>[
          "action"=> Url::to(["basket/item-change",'type'=> BasketEvent::GUEST_BASKET])
          ],        
        ];
      },
    ];
  }
  
  private function Column8G(){
    return [
    'class'=> ActionColumn::className(),
    'header' => 'Действия',
    "template" => yii::$app->user->isGuest?'{delete}':'{delete} {tobasket}',
    'buttons' => [      
      'tobasket'=> function($url,$model){
        $label = '<i class="glyphicon glyphicon-open-file"></i>';
        $options = ['title'=>'Переместить в корзину', 
                    'data-toggle'=>'tooltip',
                    'data-confirm' => 
                      "Вы хотите поместить эту деталь в корзину? <br>[ ".
                      $model->articul." ] ".
                      $model->producer."<br><b>".
                      $model->name."</b><br>".
                      "В количестве: <b>".$model->sell_count."</b> шт. ".
                      "По цене: ".yii::$app->user->getUserPrice($model->price)." руб. за шт.<br>".
                      "Общая цена составит: ".(yii::$app->user->getUserPrice($model->price)*$model->sell_count)." руб.",
          ];
        return Html::a($label, $url,$options);
      }],
    'deleteOptions' => ['label' => '<i class="glyphicon glyphicon-remove"></i>','title'=>'Удалить запись', 'data-toggle'=>'tooltip'],    
    'urlCreator'=>function($action, $model, $key, $index) { return Url::to(['basket/item-'.$action,"id"=>$key,"type"=> BasketEvent::GUEST_BASKET]); },
    'headerOptions'=>['class'=>'kartik-sheet-style'],
    ];
  }
  
  private function getBasketColumnsDescription(){
    return [
      $this->Column1(),
      $this->Column2(),
      $this->Column3(),
      $this->Column4(),
      $this->Column5(),
      $this->Column6(),
      $this->Column7(),
      $this->Column8(),
      $this->Column9()
    ];
  }
  
  private function getGuestBasketColumnsDescription(){
    return [
      $this->Column1(),
      $this->Column2(),
      $this->Column3(),
      $this->Column4(),
      $this->Column5G(),
      $this->Column6(),
      $this->Column7G(),
      $this->Column8G(),
      $this->Column9()
    ];
  }
  //============================= Constructor - Destructor =====================
  public function beforeAction($action) {
      if(!$this->search){
        $this->search =  new SearchForm();
      }
      $request = Yii::$app->request->get();
      if(isset($request['over-price'])){
        $request['over_price'] = intval($request['over-price']);      
      }
      $this->search->load($request,'');       
      return parent::beforeAction($action);
    }

  public function render($view, $params = array()) {      
    $params['search_model'] = $this->search;      
    $this->view->params['search_model'] = $this->search;
    return parent::render($view, $params);
  }
}
