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

class FbApi implements SocNetInterface {
  //public vars
  const auth_code = "fb";
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
    
    $at = $this->getAccesTokenFb($code,$return);    
    if( !$at || isset($at['error']) || !isset($at['access_token']) ){
      $error = "нет данных";
      if(isset($at['error'])){
        $error = $at['error']['message'];
      }
      $this->_error = "Ошибка ответа соц.сети: ".$error;
      return false;
    }
    /*["access_token"]=> "e..." 
    ["expires_in"]=> 0*/    
    $me = $this->getMe($at['access_token']);
    if(!$me){
      $this->_error = "Пользователь не найден";
      return false;      
    }
    /*
     * ["id"]=>  string(15) "873093216083551"
     * ["email"]=>string(14) "sync06@mail.ru"
     * ["first_name"]=>string(10) "Марат"
     * ["gender"]=>string(4) "male"
     * ["last_name"]=>string(6) "Ббб"
     * ["link"]=>string(60) "https://www.facebook.com/app_scoped_user_id/873093216083551/"
     * ["locale"]=>string(5) "ru_RU"
     * ["name"]=>string(17) "Марат Ббб"
     * ["timezone"]=>int(3)
     * ["updated_time"]=>string(24) "2014-12-25T07:52:34+0000"
     * ["verified"]=>bool(true)
     */
    /* @var $soc_auth SocAuth */
    $soc_auth = SocAuth::findBySocNetID('fb', $me['id']);    
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
        'client_id'     => Yii::$app->params['fb_id'],
        'scope'         => 'public_profile,email',
        'redirect_uri'  => Url::to($return,true),
        'response_type' => 'code',
        'state'         =>  self::auth_code
    ];
    $url = "https://www.facebook.com/dialog/oauth?".http_build_query($params);
    return $url;
  }
  
  public function getData($code, $return) {
    
    $at = $this->getAccesTokenFb($code,$return);    
    
    if( !$at || isset($at['error']) || !isset($at['access_token']) ){
      $error = "нет данных";
      if(isset($at['error'])){
        $this->_error = $at['error']['message'];
      }
      $this->_error = "Ошибка ответа соц.сети: ".$error;
      return false;
    }
    /*["access_token"]=> "e..." 
    ["expires_in"]=> 0*/    
    $me = $this->getMe($at['access_token']);
    if(!$me){
      $this->_error = "Пользователь не найден";
      return false;      
    }
    /*
     * ["id"]=>  string(15) "873093216083551"
     * ["email"]=>string(14) "sync06@mail.ru"
     * ["first_name"]=>string(10) "Марат"
     * ["gender"]=>string(4) "male"
     * ["last_name"]=>string(6) "Ббб"
     * ["link"]=>string(60) "https://www.facebook.com/app_scoped_user_id/873093216083551/"
     * ["locale"]=>string(5) "ru_RU"
     * ["name"]=>string(17) "Марат Ббб"
     * ["timezone"]=>int(3)
     * ["updated_time"]=>string(24) "2014-12-25T07:52:34+0000"
     * ["verified"]=>bool(true)
     */
    $photo = $this->getMePhoto($me['id'], $at['access_token']);
    if(!isset($photo['data'])||!isset($photo['data']['url'])){
      $photo['data']['url'] = "";
    }
    
    $answer = [
      'id'            => $me["id"],
      'first_name'    => $me['first_name'],
      'second_name'   => $me['last_name'],
      'photo'         => $photo['data']['url'],
      'email'         => $me['email']
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
  private function getMe($access_token){
    $params = [
      'access_token'   =>$access_token,
    ];
    $url = "https://graph.facebook.com/me?".http_build_query($params);
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
  
  private function getMePhoto($id,$access_token){
    $params = [
      'access_token'   => $access_token,
      'redirect'       => 0,
      'type'           => "normal",
      'height'         => 200,
      'width'          => 150
    ];
    $url = "https://graph.facebook.com/$id/picture?".http_build_query($params);
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

  private function getAccesTokenFb($code,$return){
    $params = [
      'client_id'     =>Yii::$app->params['fb_id'],
      'client_secret' =>Yii::$app->params['fb_secret'],
      'code'          =>$code,
      'redirect_uri'  =>Url::to($return,true)
    ];
    $url = "https://graph.facebook.com/oauth/access_token?".http_build_query($params);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_POST, 0);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	  $answer = curl_exec($ch);
    curl_close($ch);
    $answer_array = [];
    parse_str($answer,$answer_array);    
    return $answer_array;
  }
  //============================= Constructor - Destructor =====================
}
