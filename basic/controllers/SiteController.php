<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\data\Pagination;
use app\models\forms\LoginForm;
use app\models\news\NewsProvider;
use app\models\SetupModel;
use app\models\prices\OverpriceModel;
use app\models\forms\SearchForm;
use app\models\PartRecord;
use app\models\SearchHistoryRecord;
use app\models\search\SearchProviderBase;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ]            
        ];
    }

    public function actionIndex() {      
      return $this->render('index');
    }

    public function actionLogin() {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $login_model = new LoginForm();        
        if ($login_model->load(Yii::$app->request->post()) && $login_model->login()) {
          $this->goBack();
        }
        return $this->goHome();        
    }

    public function actionLogout() {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    public function actionContact() {
       return $this->render('contact');        
    }
    
    public function actionConsumers() {
       return $this->render('consumers');        
    }
    
    public function actionSetup(){
      if (Yii::$app->user->isGuest) {
        return $this->goHome();
      }
      $post = Yii::$app->request->post();
      $type = isset($post['type'])?$post['type']:"";
      $user = Yii::$app->user->getIdentity();
      $s_model = new SetupModel();
      $s_model->loadParams($user->getAttributes());            
      $price_model = new OverpriceModel();
        
      if($type == "data"){
        if ($s_model->load($post) && $s_model->validate()) {        
          $s_model->save();
        }
      } 
      elseif($type=="overprice"){
        if($price_model->load($post) && $price_model->validate()){
          $price_model->save();
        }
      }
      
      return $this->render('setup',['model'=>$s_model,'price_model'=>$price_model]);      
    }
    
    public function actionSearch(){
      $search = new SearchForm();      
      if($search->load(Yii::$app->request->get(),'') && $search->validate()){
        SearchHistoryRecord::addQuery($search->search_text);
        return $this->render('search',['model'=>$search]);
      }
      throw new \yii\web\NotAcceptableHttpException("Ошибка параметров запроса. Попробуйте повторить запрос");
    }
    
    public function actionAjaxSearchData(){      
      $post = Yii::$app->request->post();
      $post = SearchProviderBase::_clearStr($post);
      if(!isset($post['text'])){
        return json_encode([]);
      }
      
      if((!$search_helper = PartRecord::getHelperByPartId($post['text']))||(count($search_helper)<2)){
        return json_encode([]);
      }
      
      $items = [];
      foreach ($search_helper as $item) {
        $items[$item->getAttribute("articul")] = $item->getAttribute("articul")." - <b>".$item->getAttribute("producer")."</b>";
      } 
      ksort($items);
      echo json_encode($items);
      Yii::$app->end();
    }
    
    public function actionAjaxLoadParts(){
      $post = Yii::$app->request->post();
      $model = new SearchModel();
      $model->load($post,'');
      $answer = $model->loadParts();
      return json_encode(['id'=>$model->getCurrentCLSID(),'parts'=>$answer]);
    }

}
