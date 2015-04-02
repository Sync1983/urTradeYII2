<?php
/**
 * Description of SearchProviderBase
 *
 * @author Sync<atc58.ru>
 */
namespace app\models\search;
use yii\base\Object;
use app\models\PartRecord;

class SearchProviderBase extends Object {
  
  protected $_CLSID;
  protected $_default_params;
  protected $_name;  

  public function __construct($Name,$CLSID = null,$default_params=[],$config=[]) {
    if($CLSID){
      $this->_CLSID = $CLSID;
    }    
    $this->_name = $Name;
    $this->_default_params = $default_params;
    parent::__construct($config);
  }
  /**
   * Возвращает список деталей по указанному номеру, id производителя и указанию 
   * на использование кросс-номеров
   * Возвращаются первые 20 позиций с минимальной ценой и сроком доставки
   * @param string $part_id
   * @param string $maker_id
   * @param boolean $cross
   * @return ['price'=>$min_price,'time'=>$min_time];
   * @throws \BadMethodCallException
   */
  public function getPartList($part_id="",$maker_id="",$cross=false){    
    throw new \BadMethodCallException("Метод должен быть описан в каждом потомке");
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
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if($is_post){
      curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array_merge($param,$this->_default_params)));
    }    
    if( curl_errno($ch)!==0 ){
      $answer = "[]";
      var_dump("12313423525243542");
      //var_dump($answer);
    } else {
      $answer = curl_exec($ch);	  
    }
    curl_close($ch);
    return $answer;
  }
  /**
   * Формирует итоговую структуру, готовую для передачи в БД
   * @param mixed $data
   * @param mixed $add_fields Добавочные поля. Позволяет добавить\заменить стандартные поля в итоговой структуре
   * @return mixed Итоговая структура полей для записи в БД
   */
  protected function _dataToStruct($data=[],$add_fields=[]){
    $result = [];
    foreach ($this->_stdDataStruct() as $key => $name) {
      if(isset($data[$name])){
        $result[$key] = $data[$name];
      } else {
        $result[$key] = null;
      }        
    }
    
    $result['provider'] = $this->_CLSID;    
    
    foreach ($add_fields as $key => $value) {
      $result[$key] = $value;
    }
    return $result;
  }    
  /**
   * Очищает строку от всех символов кроме буквенно-цифровых
   * @param string $text
   * @return string
   */
  public static function _clearStr($text) {    
    return preg_replace("/[^a-zA-Z0-9]/", "", $text);    
  }
  /**
   * ПОЛНОСТЬЮ очищает из базы запчастей все данные от указанного производителя
   * @return boolean
   */
  protected function _clearAll(){
    if(!$this->_CLSID){
      return false;
    }
    return PartRecord::deleteAll(['provider'=>  $this->_CLSID]); 
  }
  /**
   * Сохраняет данные в БД
   * @param dataStruct $data
   */
  protected function _saveItem($data=[]){
    if(count($data)==0){
      return false;
    }
    $item = new PartRecord();
    $item->setAttributes($data,false);
    return $item->save();
  }
  /**
   * Преобразуем входящую XML строку в массив
   * @param string $xml
   * @return array
   */
  protected function xmlToArray($xml){    
    try{
      $xml_string = simplexml_load_string($xml, "SimpleXMLElement", LIBXML_NOCDATA);
    }catch(\yii\base\ErrorException $err){
      \yii::error($err->getMessage());      
      $xml_string = false;
      return [];
    }    
    $json = json_encode($xml_string,JSON_FORCE_OBJECT);    
    return json_decode($json,true);
  }
  /**
   * Возвращает соответствие полей в стандартной структуре данных БД
   * @return array
   */
  protected function _stdDataStruct(){
    return [
        "search_articul" => 0,
			  "provider"    => 0,
			  "articul"     => 0,
			  "producer"    => 0,
			  "maker_id"    => 0,
			  "name"        => 0,
			  "price"       => 0,
			  "shiping"     => 0,
			  "stock"       => 0,  
			  "info"        => 0,
			  "update_time" => 0,
			  "is_original" => 0,
			  "count"       => 0,
			  "lot_quantity"=> 0
    ];
  }
}
