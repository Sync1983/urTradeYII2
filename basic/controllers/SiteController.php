<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\data\Pagination;
use app\models\SiteModel;
use app\models\LoginForm;
use app\models\news\NewsProvider;
use app\models\SetupModel;

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
    
    public function beforeAction($action){      
      $model = SiteModel::_instance();
      $get = Yii::$app->request->get();
      if(isset($get['search'])){
        $model->search  = $get['search'];
      }
      if(isset($get['cross'])){
        $model->cross   = $get['cross']=="true";
      }
      if(isset($get['op'])){
        $model->op      = intval($get['op']);      
      }
      return parent::beforeAction($action);
    }

    public function actionIndex() {
      //var_dump(YII::$app->user);
      $paginator = new Pagination();
      $paginator->defaultPageSize = 3;
      $news = new NewsProvider();
      $paginator->totalCount = $news->totalCount;
      $news->setPagination($paginator);      
      return $this->render('index',['news_provider'=>$news]);
    }

    public function actionLogin() {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $login_model = new LoginForm();        
        if ($login_model->load(Yii::$app->request->post()) && $login_model->login()) {
          $this->goBack();
        };
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
      
      $user = Yii::$app->user->getIdentity();
      $s_model = new SetupModel();
      $model_attr = $s_model->getAttributes();
      $user_attr  = $user->getAttributes();
      $attr = [];
      foreach ($user_attr as $name=> $value) {
        if(isset($model_attr[$name])){
          $attr[$name] = $value;
        }
      }      
      $s_model->setAttributes($attr);
      if ($s_model->load(Yii::$app->request->post()) && $s_model->validate()) {        
        $s_model->save();
      }
      return $this->render('setup',['model'=>$s_model]);      
    }

}
