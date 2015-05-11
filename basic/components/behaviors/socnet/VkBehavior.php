<?php

/**
 * @author Sync
 */

namespace app\components\behaviors\socnet;
use app\components\behaviors\socnet\ApiBehavior;
use yii\helpers\Url;
use app\models\SocAuth;
use app\models\MongoUser;

class VkBehavior extends ApiBehavior{
  const auth_code = 'vk';
  
  public function auth_answer($return_path) {
	$error              = \yii::$app->request->get('error',false);
    $error_description  = \yii::$app->request->get('error_description',false);
    $state              = \yii::$app->request->get('state',false);
    $code               = \yii::$app->request->get('code',false);
	
    //Проверяем на наличие ошибок
    if( $error ){
      $this->_error = "Причина: $error_description";
      return false;
    }elseif( ($state!=self::auth_code) || (!$code) ){
      $this->_error = "Ошибка ответа соц.сети";
      return false;
    }
    //Запрпшивает access_token и проверяем нет ли в ответе ошибки
    $at = $this->getAccesTokenVK($code,$return_path);    
    if( !$at || isset($at['error']) || !isset($at['access_token']) ){
      $this->_error = "Ошибка ответа соц.сети: ".$at['error_description'];
      return false;
    }
    /* Формат ответа access_token
	 * ["access_token"]=> "e..." 
	 * ["expires_in"]=> 0 
	 * ["user_id"]=> 3889405 
	 * ["email"]=> "1983sync@gmail.com"*/    
	
	//Находим ссылку на учетную запись в базе
    /* @var $soc_auth SocAuth */
    $soc_auth = SocAuth::findBySocNetID('vk', $at['user_id']);    
    if( !$soc_auth ){
      $this->_error = "Пользователь не найден";
      return false;      
    }
	//По учетной записи находим ссылка на id пользователя в системе и загружаем самого пользователя
    $user = MongoUser::findOne(['_id'=>$soc_auth->getUserId()]);
    if( !$user ){
      $this->_error = "Пользователь не найден";
      return false;      
    }
	//Возврщаем данные о пользователе
    return $user;
  }

  public function auth_request($return_path) {
	$params = [
        'client_id'     => \yii::$app->params['vk_id'],
        'scope'         => 'notify,email',
        'redirect_uri'  => Url::to($return_path,true),
        'response_type' => 'code',
        'v'             => '5.28',
        'state'         =>  self::auth_code
    ];
    $url = "https://oauth.vk.com/authorize?".http_build_query($params);
    return $url;
  }

  public function get_data($code, $return) {
	//Получаем токен от соц.сети
	$access_token = $this->getAccesTokenVK($code,$return);    
    //Проверяем нет ли ошибки в ответе
    if( !$access_token || isset($access_token['error']) || !isset($access_token['access_token']) ){
      $this->_error = "Ошибка ответа соц.сети: ".$access_token['error_description'];
      return false;
    }
	//Сохраним адрес почты
    $email = $access_token['email'];
	//Запросим данные профиля
    $data = $this->getIdData($access_token['user_id'], $access_token['access_token']);
	//Проверим нет ли ошибки в ответе
    if( !$data || !isset($data['response']) ){
      $this->_error = "Ошибка ответа соц.сети: ".json_encode($data);
      return false;
    }
	//Сформируем нормированный ответ
    $user_data = $data['response']['0'];
    $answer = [      
      'id'            => $user_data['id'],
      'first_name'    => $user_data['first_name'],
      'second_name'   => $user_data['last_name'],
      'photo'         => $user_data['photo_100'],
      'email'         => $email
    ];
	
    return $answer;
  }
  //===============================================================================
  private function getAccesTokenVK($code,$return){
    $params = [
      'client_id'     => \yii::$app->params['vk_id'],
      'client_secret' => \yii::$app->params['vk_secret'],
      'code'          => $code,
      'redirect_uri'  => Url::to($return,true)
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
  
}
