<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\forms\LoginForm;
use app\models\forms\SignUpForm;
use app\models\forms\SetupModel;
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
            'form' => [
                'class' => \app\components\behaviors\SearchFormBehavior::className(),
            ]
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],		
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
            ],        
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
          $event = new \app\models\events\NotifyEvent();
          $event->text = "Добро пожаловать";
          Yii::$app->user->trigger(\app\models\events\NotifyEvent::USER_NOTIFY_EVENT,$event);
        } else {
          return $this->render("error",['name'=>'Ошибка авторизации','message'=>'Неверные имя или пароль']);          
        }
        return $this->goHome();
    }
    
    public function actionSignup(){
      if (!\yii::$app->user->isGuest) {
            return $this->goHome();
      }
	  
	  $stage = \yii::$app->request->get('stage',false);
	  $model = new SignUpForm();
	  
	  if( !$stage ){
		$model->key = \app\models\RegRecord::generateKey();
		return $this->render('signup/stage0',['model' => $model]);
	  } else {
		if( !$model->load(\yii::$app->request->post()) || !$model->key || !$model->email ){
		  throw new \yii\web\BadRequestHttpException("Ошибка в формате запроса");
		}
		if( \app\models\RegRecord::checkKey($model->key) ){
		  throw new \yii\web\BadRequestHttpException("Повторный запрос регистрации");
		}
		$reg_record = new \app\models\RegRecord();
		$reg_record->key = $model->key;
		$reg_record->login	= $model->username;
		$reg_record->password = $model->userpass;
		$reg_record->time = time();
		$reg_record->was_send = false;
		$reg_record->mail = $model->email;
		if ( !$reg_record->save() ){
		  throw new \yii\web\BadRequestHttpException("Ошибка добавления пользователя");
		}
		
		return $this->render('signup/wait_mail',['email' => $model->email]);		
	  }
	  
	  return $this->render('signup/stage0',['model'=>$model]);
    }
	
	public function actionSignupValidate() {
	  $model = new SignUpForm();
	  
	  if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return \yii\bootstrap\ActiveForm::validate($model);
	  }
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
      /* @var $user \app\models\MongoUser */
      $user = Yii::$app->user->getIdentity();
      $prices = $user->getOverPiceList();
      
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
      if($this->getSearchForm()->validate()){        
        SearchHistoryRecord::addQuery($this->getSearchForm()->search_text);
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

}
