<?php

/**
 * Description of VkApi
 * @author Sync<atc58.ru>
 */

namespace app\models\socnet;
use Yii;
use yii\helpers\Url;
use app\models\socnet\SocNetInterface;
use app\models\SocAuth;
use app\models\MongoUser;

class VkApi implements SocNetInterface {
  //public vars
  const auth_code = "vk";
  //protected vars
  protected $_error;
  //private vars  
  //============================= Public =======================================
  public function auth_answer($return) {
    $error              = Yii::$app->request->get('error',false);
    $error_description  = Yii::$app->request->get('error_description',false);
    $state              = Yii::$app->request->get('state',false);
    $code               = Yii::$app->request->get('code',false);
    
    if($error){
      $this->_error = "Причина: $error_description";
      return false;
    }elseif( ($state!=self::auth_code) || (!$code) ){
      $this->_error = "Ошибка ответа соц.сети";
      return false;
    }
    
    $at = $this->getAccesTokenVK($code,$return);    
    if( !$at || isset($at['error']) || !isset($at['access_token']) ){
      $this->_error = "Ошибка ответа соц.сети: ".$at['error_description'];
      return false;
    }
    /*["access_token"]=> "e..." 
    ["expires_in"]=> 0 
    ["user_id"]=> 3889405 
    ["email"]=> "1983sync@gmail.com"*/    
    /* @var $soc_auth SocAuth */
    $soc_auth = SocAuth::findBySocNetID('vk', $at['user_id']);    
    if(!$soc_auth){
      $this->_error = "Пользователь не найден";
      return false;      
    }
    $user = MongoUser::findOne(['_id'=>$soc_auth->getUserId()]);
    if(!$user){
      $this->_error = "Пользователь не найден";
      return false;      
    }
    return $user;
  }

  public function auth_request($return) {
    $params = [
        'client_id'     =>Yii::$app->params['vk_id'],
        'scope'         =>'notify,email',
        'redirect_uri'  =>Url::to($return,true),
        'response_type' =>'code',
        'v'             =>'5.28',
        'state'         =>  self::auth_code
    ];
    $url = "https://oauth.vk.com/authorize?".http_build_query($params);
    return $url;
  }

  public function getData($code,$return) {
    $at = $this->getAccesTokenVK($code,$return);    
    
    if( !$at || isset($at['error']) || !isset($at['access_token']) ){
      $this->_error = "Ошибка ответа соц.сети: ".$at['error_description'];
      return false;
    }
    $email = $at['email'];
    $data = $this->getIdData($at['user_id'], $at['access_token']);
    if(!$data || !isset($data['response'])){
      $this->_error = "Ошибка ответа соц.сети: ".json_encode($data);
      return false;
    }
    $data = $data['response']['0'];
    $answer = [      
      'id'            => $data["id"],
      'first_name'    => $data['first_name'],
      'second_name'   => $data['last_name'],
      'photo'         => $data['photo_100'],
      'email'         => $email
    ];
    return $answer;
  }

  public function getSocNetName() {
    return self::auth_code;
  }
  
  public function error() {
    return $this->_error;
  }

  //============================= Protected ====================================
  //============================= Private ======================================
  private function getAccesTokenVK($code,$return){
    $params = [
      'client_id'     =>Yii::$app->params['vk_id'],
      'client_secret' =>Yii::$app->params['vk_secret'],
      'code'          =>$code,
      'redirect_uri'  =>Url::to($return,true)
    ];
    $url = "https://oauth.vk.com/access_token?".http_build_query($params);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_POST, 0);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	  $answer = curl_exec($ch);
    curl_close($ch);
    return json_decode($answer,true);
  }
  
  private function getIdData($id,$access_token){
    $params = [
      'acces_token'   => $access_token,
      'user_id'       => $id,
      'v'             => "5.28",
      'fields'        => "photo_100",
    ];
    $url = "https://api.vk.com/method/users.get?".http_build_query($params);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_POST, 0);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	  $answer = curl_exec($ch);
    curl_close($ch);
    return json_decode($answer,true);
  }
  //============================= Constructor - Destructor =====================
}
