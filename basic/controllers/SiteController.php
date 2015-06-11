<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\forms\LoginForm;
use app\controllers\actions\site\DefaultAction;
use app\controllers\actions\site\SignUpAction;
use app\controllers\actions\site\ProfileAction;

class SiteController extends Controller
{
  public $search = null;
  public $layout = "main";

  public function behaviors() {
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
                
            ],
        ],
        'form' => [
            'class' => \app\components\behaviors\SearchFormBehavior::className(),
        ]
    ];
  }

  public function actions() {
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
            'setup' => [
              'class' => actions\site\SetUpAction::className(),
            ],
            'setup-prices' => [
              'class' => actions\site\SetUpAction::className(),
              'type'  => 'prices',
            ],
            'search'  => [
              'class'   => actions\site\SearchAction::className(),
              'type'    => actions\site\SearchAction::TYPE_INDEX
            ],
            'ajax-search-data'  => [
              'class'   => actions\site\SearchAction::className(),
              'type'    => actions\site\SearchAction::TYPE_HELPER
            ],
            'ajax-load-parts'  => [
              'class'   => actions\site\SearchAction::className(),
              'type'    => actions\site\SearchAction::TYPE_PARTS_SHORT
            ],
            'ajax-full-load'  => [
              'class'   => actions\site\SearchAction::className(),
              'type'    => actions\site\SearchAction::TYPE_PARTS_FULL
            ]
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
