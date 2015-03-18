<?php

/**
 * Description of Basket
 * @author Sync<atc58.ru>
 */

namespace app\controllers;

use yii;
use yii\widgets\ActiveForm;
use yii\web\Response;
use yii\base\Controller;
use app\models\forms\BasketAddForm;
use app\models\forms\SearchForm;
use app\models\BasketDataProvider;
use app\models\PartRecord;

class BasketController extends Controller{
  //public vars  
  public $search;
  //protected vars
  protected $items = [];
  //private vars  
  //============================= Public =======================================
  public function actionIndex(){
    $all_models = [];
    if(!yii::$app->user->isGuest){
      $all_models = yii::$app->user->identity->getBasketParts();
    }
    $user_basket = new BasketDataProvider([
        'allModels'=> $all_models,
        'pagination' => [
          'pageSize' => 2, 
        ],
    ]);
    $guest_basket = [];//new ArrayDataProvider();
    
    $pjax = yii::$app->request->get("_pjax",false);
    
    if($pjax){      
      return $this->view->renderAjax("grid", ['user_basket'=>$user_basket,'guest_basket'=>$guest_basket]);
    } 

    return $this->render("index",['user_basket'=>$user_basket,'guest_basket'=>$guest_basket]);
  }
  
  public function actionChangeBasketCount(){
    $parts = yii::$app->user->identity->getBasketParts();
    $key = yii::$app->request->post("editableKey",-1);    
    $index = yii::$app->request->post("editableIndex",-1);
    if( $key==-1 || $index==-1 ){
      echo json_encode(['output'=>0, 'message'=>'Ключ записи не найден']);      
      yii::$app->end();
      return;
    }
    
    $sell_count = yii::$app->request->post("PartRecord",false);    
    if( !$sell_count || !isset($sell_count[$index]) || !isset($sell_count[$index]['sell_count']) ){
      echo json_encode(['output'=>0, 'message'=>'Количество неверно']);
      yii::$app->end();
      return;
    }
    
    $new_count = intval($sell_count[$index]['sell_count']);
    if($new_count<1){
      echo json_encode(['output'=>0, 'message'=>'Количество меньше 1']);
      yii::$app->end();
      return;      
    }
    
    /* @var $part PartRecord */
    foreach ($parts as $part){
      if( strval($part->getAttribute("_id")) !== $key ){
        continue;
      }
      $part->setAttribute("sell_count", $new_count);
      if(!$part->validate(["sell_count"])){
        echo json_encode(['output'=>0, 'message'=>$part->getErrors("sell_count")]);
        yii::$app->end();
        return;      
      }
      yii::$app->user->identity->addNotify("Изменено количество на $new_count шт.");      
    }    
    echo json_encode(['output'=>$new_count]);
    yii::$app->end();
    return;        
  }
  
  public function actionChangeBasketComment(){
    $parts = yii::$app->user->identity->getBasketParts();
    $key = yii::$app->request->post("editableKey",false);    
    $index = yii::$app->request->post("editableIndex",false);
    if( $key===false || $index===false ){
      echo json_encode(['output'=>0, 'message'=>'Ключ записи не найден']);      
      yii::$app->end();
      return;
    }
    
    $comment_array = yii::$app->request->post("PartRecord",false);    
    if( !$comment_array || !isset($comment_array[$index]) || !isset($comment_array[$index]['comment']) ){
      echo json_encode(['output'=>0, 'message'=>'Количество неверно']);
      yii::$app->end();
      return;
    }    
    
    $comment =$comment_array[$index]['comment'];
    /* @var $part PartRecord */
    foreach ($parts as $part){
      if( strval($part->getAttribute("_id")) !== $key ){
        continue;
      }
      $part->setAttribute("comment", $comment);
      yii::$app->user->identity->addNotify("Комменатрий изменен  \"$comment\" ");      
    }
    echo json_encode(['output'=>$comment]);
    yii::$app->end();
    return;        
  }

  public function actionAddTo(){ 
    /* @var $model BasketAddForm */
    $model = new BasketAddForm();
    if($model->load(Yii::$app->request->post())&&($model->validate())){
      if(!yii::$app->user->isGuest){
        $model->addToUserBasket();        
      }else {
        $model->addToGuestBasket();
      }
      yii::$app->end();
      return;
    }
    echo json_encode(["error"=>1]);
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
