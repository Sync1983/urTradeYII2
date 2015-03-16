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

class BasketController extends Controller{
  //public vars  
  //protected vars
  protected $items = [];
  //private vars  
  //============================= Public =======================================
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
}
