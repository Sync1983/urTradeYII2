<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\forms\LoginForm;
use app\models\forms\SetupModel;
use app\models\PartRecord;
use app\models\SearchHistoryRecord;
use app\models\search\SearchProviderBase;
use app\models\search\SearchModel;
use app\controllers\actions\site\DefaultAction;
use app\controllers\actions\site\SignUpAction;
use app\controllers\actions\site\ProfileAction;

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
			'index'	=> [
				'class' => DefaultAction::className(),
			],
			'contact' => [
			  'class' => DefaultAction::className(),
			],
			'consumers'	=> [
			  'class' => DefaultAction::className(),
			],
			'signup'	=> [
			  'class' => SignUpAction::className(),
			  'stage' => SignUpAction::STAGE_INITIAL
			],
			'signup-wait-mail'	=> [
			  'class' => SignUpAction::className(),
			  'stage' => SignUpAction::STAGE_WAIT_MAIL
			],
			'signup-validate'	=> [
			  'class' => SignUpAction::className(),
			  'stage' => SignUpAction::STAGE_VALIDATE_FORM
			],
			'signup-mail-answer'=> [
			  'class' => SignUpAction::className(),
			  'stage' => SignUpAction::STAGE_MAIL_ANSWER
			],
			'new-user'	=> [
			  'class' => ProfileAction::className(),
			],
			'new-user-last-step'=> [
			  'class' => ProfileAction::className(),
			],
			'profile-discard'=> [
			  'class' => ProfileAction::className(),
			],
			'profile-save'=> [
			  'class' => ProfileAction::className(),
			],
			'profile-validate'=> [
			  'class' => ProfileAction::className(),
			],
        ];
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

	public function actionLogout() {
        Yii::$app->user->logout();
        return $this->goHome();
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
      $s_model->setAttributes($user->getAttributes());
	  $s_model->id = $user->getId();
      if ($s_model->load($post) && $s_model->validate()) {
		$user->setAttributes($s_model->getAttributes());
        $user->save();
      }
      
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
      if( $this->getSearchForm()->validate() ){        
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
	
	public function beforeAction($action) {
	  if( \yii::$app->user->isGuest || \yii::$app->session->get('ProfileRequestDiscard',false) ){
		return parent::beforeAction($action);
	  }	  
	  
	  if( !\yii::$app->user->identity->getAttribute('is_init') && ($action->id === 'index') ) {
		return $this->redirect(\yii\helpers\Url::to(['site/new-user']));
	  }
	  
	  return parent::beforeAction($action);
	}

}
