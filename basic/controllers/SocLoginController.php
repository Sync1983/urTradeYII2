<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\socnet\SocNetInterface;
use app\models\SocAuth;
use yii\helpers\ArrayHelper;

class SocLoginController extends Controller
{
  const state     = "atc_auth";
  public $layout  = "initial";
  protected static $_net_by_name = [
    'vk'      => '\app\models\socnet\VkApi',
    'fb'      => '\app\models\socnet\FbApi',
  ];
  private $_user  = null;

  public function behaviors(){
      return [];
  }

  public function actions(){
    return [
      'error' => [
        'class' => 'yii\web\ErrorAction',
      ]            
    ];
  }
  
  public static function getClassByNet($soc_net){ 
    if(!$soc_net){
      return false;
    }
    return ArrayHelper::getValue(self::$_net_by_name, $soc_net, false);
  }
  
  public static function getAvaibleNets(){
    return array_keys(self::$_net_by_name);
  }
  
  public static function getActiveNets(){
    if(Yii::$app->user->isGuest){
      return [];
    }
    $items = SocAuth::find()->where(['user_id'=>Yii::$app->user->identity->getObjectId()])->all();
    $answer = [];
    foreach ($items as $value) {
      $answer[] = $value['net'];
    }
    return $answer;
  }

  public function actionLogin() {     
    $net_name = self::getClassByNet(Yii::$app->request->get("net",false));    
    if( (!$net_name) ){
      return $this->render('error',['name'=>'Ошибка авторизации','message'=>"Неизвестная соц. сеть"]);
    }
    /* @var $net SocNetInterface */
    $net = new $net_name();
    return $this->redirect($net->auth_request(['soclogin/answer']));
  }
  
  public function actionAnswer(){    
    $net_name = self::getClassByNet(Yii::$app->request->get("state",false));
    if( (!$net_name) ){
      return $this->render('error',['name'=>'Ошибка авторизации','message'=>"Неизвестная соц. сеть"]);
    }
    /* @var $net SocNetInterface */
    $net = new $net_name();
    $this->_user = $net->auth_answer(['soclogin/answer']);
    if(!$this->_user){
      return $this->render('error',['name'=>'Ошибка авторизации','message'=>$net->error()]);      
    }
    return $this->login();
  }
  
  public function actionUpdateInfo(){
    $net_name = self::getClassByNet(Yii::$app->request->get("net",false));
    if( (!$net_name) ){
      return $this->render('error',['name'=>'Ошибка в запросе','message'=>"Неизвестная соц. сеть"]);
    }
    /* @var $net SocNetInterface */
    $net = new $net_name();
    $soc_id = $this->getSocIdByUser($net->getSocNetName());
    
    if(!$soc_id){
      return $this->render('error',['name'=>'Ошибка в запросе','message'=>"Пользователь не найден"]);
    }
    return $this->redirect($net->auth_request(['soclogin/answer-data']));    
  }
  
  public function actionAnswerData(){
    $net_name = self::getClassByNet(Yii::$app->request->get("state",false));
    $code     = Yii::$app->request->get("code",false);
    
    if( (!$net_name) ){
      return $this->render('error',['name'=>'Ошибка в запросе','message'=>"Неизвестная соц. сеть"]);
    }
    /* @var $net SocNetInterface */
    $net = new $net_name();
    $soc_id = $this->getSocIdByUser($net->getSocNetName());
    
    if(!$soc_id){
      return $this->render('error',['name'=>'Ошибка в запросе','message'=>"Пользователь не найден"]);
    }
    $data = $net->getData($code,['soclogin/answer-data']);    
    if(!$data){
      return $this->render('error',['name'=>'Ошибка в ответе','message'=>$net->error()]);
    }
    $user = Yii::$app->user->getIdentity();
    $user->first_name   = $data['first_name'];
    $user->second_name  = $data['second_name'];
    $user->photo        = $data['photo'];
    $user->email        = $data['email'];
    $user->save();
    return $this->redirect(['site/setup']);        
  }
  
  public function actionRegister(){    
    $net_name = self::getClassByNet(Yii::$app->request->get("net",false));
    if( (!$net_name) ){
      return $this->render('error',['name'=>'Ошибка в запросе','message'=>"Неизвестная соц. сеть"]);
    }
    /* @var $net SocNetInterface */
    $net = new $net_name();
    return $this->redirect($net->auth_request(['soclogin/register-answer']));
  }
  
  public function actionRegisterAnswer(){
    $net_name = self::getClassByNet(Yii::$app->request->get("state",false));
    $code     = Yii::$app->request->get("code",false);
    
    if( (!$net_name) ){
      return $this->render('error',['name'=>'Ошибка в запросе','message'=>"Неизвестная соц. сеть"]);
    }
    /* @var $net SocNetInterface */
    $net = new $net_name();
    $data = $net->getData($code,['soclogin/register-answer']);
    //var_dump($data);
    if(!isset($data["id"])){
      return $this->render('error',['name'=>'Ошибка в ответе','message'=>"ID пользователя не найден ".$net->error()]);      
    }
    $user = Yii::$app->user;
    if(!$user->isGuest){
      SocAuth::createRecord($net->getSocNetName(), $user->getId(), $data["id"]);
      return $this->redirect(['site/setup']);
    } else {
      $sn = SocAuth::findBySocNetID($net->getSocNetName(),$data['id']."");
      var_dump($sn);
      var_dump($data);
      var_dump($net->getSocNetName());
      if($sn){
        $uid = $sn->getUserId();
        $load_user = \app\models\MongoUser::findOne(['_id'=> new \MongoId($uid)]);
        $user->login($load_user,30*24*3600);
        return $this->redirect(['site/index']);
      } else {      
        $mongo_user = \app\models\MongoUser::createNew("", "", $data['first_name']);
        if(!$mongo_user){
          return false;
        }
        $mongo_user->second_name = $data['second_name'];
        $mongo_user->photo       = $data['photo'];
        $mongo_user->email       = $data['email'];
        $mongo_user->save();
        SocAuth::createRecord($net->getSocNetName(), $mongo_user->getId(), $data["id"]);        
        $user->login($mongo_user,30*24*3600);
        return $this->redirect(['site/index']);
      }
    }
  }

  
  //===================================== Protected ===========================
  protected function login(){
    $login = Yii::$app->user->login($this->_user, 3600*24*30);
    if(!$login){
      return $this->render('error',['name'=>'Ошибка авторизации','message'=>"Пользователь не найден"]);      
    }
    $this->_user->addNotify("Добро пожаловать");
    return $this->goHome();
  }
  
  protected function getSocIdByUser($net_name){
    if(Yii::$app->user->isGuest){
      return false;
    }
    $id = Yii::$app->user->identity->getObjectId();
    $answer = SocAuth::findOne(["net"=>$net_name,"user_id"=>$id]);
    if(!$answer){
      return false;
    }
    return $answer->soc_id;
  }
  //===================================== Private =============================
    
}
