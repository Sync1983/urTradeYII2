<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\forms\LoginForm;
use app\models\forms\SignUpForm;
use app\models\forms\SetupModel;
use app\models\forms\SearchForm;
use app\models\PartRecord;
use app\models\SearchHistoryRecord;
use app\models\search\SearchProviderBase;
use app\models\search\SearchModel;

class SiteController extends Controller
{
  public $search = null;
  public $layout = "main";

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
        if ($login_model->load(Yii::$app->request->post(),"") && $login_model->login()) {          
          return $this->render("error",['name'=>'Ошибка авторизации','message'=>'Неверные имя или пароль']);
        }
        Yii::$app->user->identity->addNotify("Добро пожаловать");
        return $this->goHome();
    }
    
    public function actionSignup(){
      if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $login_model = new SignUpForm();        
        if(!$login_model->load(Yii::$app->request->post(),"") || !$login_model->validate()){
          return $this->render("error",['name'=>'Ошибка регистрации','message'=>'Даное имя занято']);
        }
        if(!$login_model->createUser() || !$login_model->login()){
          return $this->render("error",['name'=>'Ошибка регистрации','message'=>'Неизвестная ошибка']);
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
    
    public function actionNotify(){
      $user = Yii::$app->user->identity;
      $list = $user->informer;
      if(!$list){
        echo json_encode(["messages"=>[]]);
        Yii::$app->end();
        return;
      }
      $notify = array_splice($list, 0, 10);
      $user->informer = $list;
      $user->save();
      echo json_encode(["messages"=>$notify]);
      Yii::$app->end();
    }

    public function actionSetup(){
      if (Yii::$app->user->isGuest) {
        return $this->goHome();
      }
      $post = Yii::$app->request->post();      
      $user = Yii::$app->user->getIdentity();
      $prices = $user->over_price_list;
      
      $s_model = new SetupModel();
      $s_model->loadParams($user->getAttributes());            
      if ($s_model->load($post) && $s_model->validate()) {        
          $s_model->save();
      }
      $this->view->registerJsFile("/js/setup.js",['depends' => [\yii\web\JqueryAsset::className()]]);
      return $this->render('setup',['model'=>$s_model,'prices'=>$prices]);      
    }
    
    public function actionSetupPrices(){
      if(Yii::$app->user->isGuest){
        throw new \yii\web\NotAcceptableHttpException("Ошбика доступа");
      }
      $name = Yii::$app->request->post('name',[]);
      $value = Yii::$app->request->post('value',[]);
      $len = count($name);
      $items = [];
      for($i = 0; $i<$len; $i++){
        $items[$name[$i]] = $value[$i];
      }      
      Yii::$app->user->saveOverPriceList($items);
      $this->redirect(['site/setup']);
    }

    public function actionSearch(){
      if($this->search->validate()){        
        SearchHistoryRecord::addQuery($this->search->search_text);
        return $this->render('search');
      }
      throw new \yii\web\NotAcceptableHttpException("Ошибка параметров запроса. Попробуйте повторить запрос");
    }
    
    public function actionAjaxSearchData(){      
      $post = SearchProviderBase::_clearStr(Yii::$app->request->post());
      
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
