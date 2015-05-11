<?php

/**
 * @author Sync
 */
namespace app\components\behaviors\socnet;
use yii\base\Behavior;

abstract class ApiBehavior extends Behavior{
  const auth_code = "auth_code";
  protected $_error;
  /**
   * Вызывается для начал проверки авторизации
   * в качестве аргумента передается адрес ответа
   */
  abstract public function auth_request($return_path);
  /**
   * Вызывается при ответе авторизации по адресу ответа функции auth_request
   * В параметрах передается копия адреса вызова
   */
  abstract public function auth_answer($return_path);
  /**
   * Запрашивает данные соц.сети на пользователя
   * В качестве параметров передается access_token и путь возврата, такой же как и для функции auth_request
   */
  abstract public function get_data($code,$return);
  /**
   * Возвращает дополнительный параметр авторизации для уточнения соц.сети
   * @return string
   */
  public function get_soc_net_name(){
	return self::auth_code;
  }
  /**
   * Возвращает список ошибок
   * @return array
   */
  public function error() {
	return $this->_error;
  }
	  
}
