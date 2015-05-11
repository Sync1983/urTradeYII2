<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;

use app\models\SocAuth;
use yii\helpers\ArrayHelper;
use app\models\events\NotifyEvent;
use app\components\helpers\SocNetHelper;

class SocloginController extends Controller
{
  const state     = "atc_auth";  
  protected $_net = null;
  public $layout  = "initial";

  public function actions(){
    return [
      'error' => [
        'class' => 'yii\web\ErrorAction',
      ]            
    ];
  }

  public function actionLogin() {    
    return $this->redirect($this->auth_request(['soclogin/login-answer']));
  }
  
  public function actionRegister(){    
    return $this->redirect($this->auth_request(['soclogin/register-answer']));
  }
  
  public function actionLoginAnswer(){    
    $user	  =  $this->auth_answer(['soclogin/login-answer']);
    if( !$user ){
      return $this->render('error',['name'=>'Ошибка авторизации','message'=>$this->error()]);      
    }
	
	$login = Yii::$app->user->login($user, 3600*24*30);
    if( !$login ){
      return $this->render('error',['name'=>'Ошибка авторизации','message'=>"Пользователь не найден"]);      
    }
	
    Yii::$app->user->trigger(NotifyEvent::USER_NOTIFY_EVENT,new NotifyEvent(['text'=>"Добро пожаловать"]));
    return $this->goHome();    
  }
  
  public function actionRegisterAnswer(){    
    $code     = Yii::$app->request->get("code",false);
    $data	  = $this->get_data($code,['soclogin/register-answer']);
    $id		  = ArrayHelper::getValue($data, 'id', false);
    $user	  = \yii::$app->user;
	
	if( !$id ){
      return $this->render('error',['name'=>'Ошибка в ответе','message'=>"ID пользователя не найден ".$this->error()]);	  
	}
    //Если пользователь авторизирован, то просто добавляем запись для новой соц.сети
    if( !$user->isGuest ){
      SocAuth::createRecord($net->getSocNetName(), $user->getId(), $id);
	  //И уходим на страничку профиля
      return $this->redirect(['site/setup']);
    }
	//Если пользователь не авторизирован, то ищем запись регистрации в базе, для указанной соц.сети
	// и id пользователя в ней
	$sn = SocAuth::findBySocNetID($this->_net,$data['id']."");      
	//Если такая запись найдена
	if($sn){
	  $uid = $sn->getUserId(); //Получим id пользователя в нашей системе
      $load_user = \app\models\MongoUser::findOne(['_id'=> new \MongoId($uid)]);  //Находим пользователя
      $user->login($load_user,30*24*3600);	//авторизируем
	  // И уходим на главную страницу
      return $this->redirect(['site/index']);
    }
	//Сюда попадаем, если пользователь не авторизован, и не имеет записей в базе
	// для указанной соц.сети. Регистрируем нового пользователя
	$mongo_user = \app\models\MongoUser::createNew("", "", $data['first_name']);  //Добавляем запись с именем пользователя
    if(!$mongo_user){
	  //По каким-то причинам мы не смогли создать пользователя
      return $this->render('error',['name'=>'Ошибка создания пользователя','message'=>"Пользователь не может быть создан"]);	  
    }
    //Добавим основные данные из соц.сети
	$mongo_user->second_name = $data['second_name'];
    $mongo_user->photo       = $data['photo'];
    $mongo_user->email       = $data['email'];
    $mongo_user->save();  //Сохраним изменения
	//Добавим запись в таблицу соц.сетей, чтобы связать id  в нашей системе и в соц.сети
    SocAuth::createRecord($this->_net, $mongo_user->getId(), $id);        
	//Авторизуемся
    $user->login($mongo_user,30*24*3600);
	\yii::$app->user->trigger(NotifyEvent::USER_NOTIFY_EVENT,new NotifyEvent(['text'=>"Добро пожаловать"]));
	//И переходим на главную страницу	
    return $this->redirect(['site/index']);
  }
  
  //===================================== Protected ===========================
  public function beforeAction($action) {
	$net = \yii::$app->request->get("net",false);
	$state = \yii::$app->request->get("state",false);
	
	if( $net ){
	  $class = SocNetHelper::getClassByNet($net);
	  if( !$class ){
		throw new \yii\web\BadRequestHttpException('Ошибка в передаче параметров социальной сети');
	  }	  
	  $this->_net = $net;
	  $this->attachBehavior("socBehavior", $class);
	} elseif ( $state ){
	  $class = SocNetHelper::getClassByNet($state);
	  if( !$class ){
		throw new \yii\web\BadRequestHttpException('Ошибка в передаче параметров социальной сети');
	  }	  
	  $this->_net = $state;
	  $this->attachBehavior("socBehavior", $class);	
	} else {
		throw new \yii\web\BadRequestHttpException('Ошибка в передаче параметров социальной сети');	  
	}
	return parent::beforeAction($action);
  }
  //===================================== Private =============================
    
}
