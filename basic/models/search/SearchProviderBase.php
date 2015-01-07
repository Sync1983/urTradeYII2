<?php
/**
 * Description of SearchProviderBase
 *
 * @author Sync<atc58.ru>
 */
namespace app\models\search;
use yii\base\Object;

class SearchProviderBase extends Object {
  
  protected $_CLSID;
  protected $_default_params;
  protected $_name;

  public function __construct($Name,$CLSID = null,$default_params=[],$config=[]) {    
    $this->_CLSID = $CLSID;
    $this->_default_params = $default_params;
    $this->_name = $Name;
    parent::__construct($config);
  }
  /**
   * Возвращает список фирм-производителей для указанного артикула
   * @param String $part_id артикул детали
   * @param boolean $cross указывает включать ли кросс-номера артикула
   * @return array
   * @throws \BadMethodCallException
   */
  public function getMakerList($part_id="",$cross=false){
    throw new \BadMethodCallException("Метод должен быть описан в каждом потомке");
  }

    /**
   * Возвращает имя поставщика
   * @return string
   */
  public function getName(){
    return $this->_name;
  }
  
  /**
   * Возвращает идентификатор поставщика
   * @return int
   */
  public function getCLSID(){
    return $this->_CLSID;
  }
  /**
   * Отправляет запрос по указаному URL с параметрами $param
   * POST или GET запрос определяется флагом is_post
   * @param string $url
   * @param array $param
   * @param boolean $is_post
   * @return mixed
   */
  protected function onlineRequest($url="",$param=[],$is_post=true) {
    if(!$is_post){
      $url.="?".http_build_query(array_merge($param,$this->_default_params));
    }    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_POST, $is_post==1);
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if($is_post){
      curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array_merge($param,$this->_default_params)));
    }    
	  $answer = curl_exec($ch);
    curl_close($ch);
    return $answer;
  }
  
  /**
   * Преобразуем входящую XML строку в массив
   * @param string $xml
   * @return array
   */
  protected function xmlToArray($xml){
    $xml_string = simplexml_load_string($xml, "SimpleXMLElement", LIBXML_NOCDATA);
    $json = json_encode($xml_string);
    return json_decode($json,true);
  }
}
