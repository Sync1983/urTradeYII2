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
  protected $_maker_list_id;
  protected $_maker_name;
  protected $_maker_id;
  protected $_part_list_id;
  

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
  public function getPartList($part_id="",$maker_id="",$cross=false,$full=false){
    $answer = $this->sendPartRequest($part_id,$maker_id,$cross);
        
    if( !isset($answer[ $this->_part_list_id ]) ){return [];}
    
    if( !isset($answer[ $this->_part_list_id ][0]) ) {
     $answer[ $this->_part_list_id ] = [ $answer[ $this->_part_list_id ] ];
    } 
    $uid = \yii::$app->user->getId();
    PartRecord::deleteAll(['provider'=>$this->_CLSID,'search_articul'=>$part_id,'for_user'=>$uid]);
    foreach ($answer[ $this->_part_list_id ] as $part){
      $item = $this->_dataToStruct($part,['search_articul'=>$part_id,'maker_id'=>$maker_id]);      
      
      $part_model = new PartRecord();
      $part_model->setAttribute("for_user", $uid);
      $part_model->setAttributes($item,false);
      $part_model->save();
    }
    $and_params = [ "AND",
                    ["provider"         => $this->_CLSID] ,
                    ["search_articul"   => strval($part_id)]
      ];
    if( !$cross ){
      $and_params[] = ['articul' => strval($part_id)];
    }
    $cond = PartRecord::getCollection()->buildCondition($and_params);
    if( !$full ){
      return PartRecord::getPartsForOnlineProvider($cond);
    }
    return PartRecord::getAllPartsForOnlineProvider($cond);
  }
  /**
   * Возвращает список фирм-производителей для указанного артикула
   * @param String $part_id артикул детали
   * @param boolean $cross указывает включать ли кросс-номера артикула
   * @return array
   * @throws \BadMethodCallException
   */
  public function getMakerList($part_id="",$cross=false){
    return $this->convertMakersAnswerToStandart($this->sendMakerRequest($part_id,$cross));
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
   * Вызывает уникальный для каждого поставщика запрос к серверу и возвращает
   * ответ в виде массива   
   * @param array $param
   * @param boolean $is_post
   * @throws \BadMethodCallException
   */
  protected function sendPartRequest($part_id="",$maker_id="",$cross=false){
    throw new \BadMethodCallException("Метод должен быть описан в каждом потомке");
  }
  /**
   * Вызывает уникальный для каждого поставщика запрос к серверу и возвращает
   * ответ в виде массива   
   * @param array $param
   * @param boolean $is_post
   * @throws \BadMethodCallException
   */
  protected function sendMakerRequest($part_id="",$cross=false){
    throw new \BadMethodCallException("Метод должен быть описан в каждом потомке");
  }
  /**
   * Преобразовываем полученный массив данных в стандартный массив обмена
   * @param mixed $data
   * @return mixed
   */
  protected function convertMakersAnswerToStandart($data){    
    if(!$data || !isset($data[ $this->_maker_list_id ]) ){
      return [];
    }
    $result = [];    
    $clsid = $this->getCLSID();
    if( !isset($data[ $this->_maker_list_id ][0])){        //Такое бывает когда запись одна - массив приходит не вложенный
      $data[ $this->_maker_list_id ] = [ $data[ $this->_maker_list_id ] ];
    }
    foreach ($data[ $this->_maker_list_id ] as $value) {      
      $name     = (isset($value[ $this->_maker_name ]))?$value[ $this->_maker_name ]  : false;      
      $id       = (isset($value[ $this->_maker_id ]))?  $value[ $this->_maker_id ]	  : false;      
	  if( $name && $id ) {
		$result[$name] = [$clsid => $id];
	  }
    }
    return $result;    
  }
  /**
   * Формирует заголовки запроса
   * @param resource $ch
   */
  protected function onlineRequestHeaders($ch){
    curl_setopt($ch, CURLOPT_HEADER, 0);
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
    $ch = curl_init();
    $this->onlineRequestHeaders($ch);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, $is_post==1);
    
    $params = http_build_query(array_merge($param,$this->_default_params));
        
    if( $is_post ) {
      curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    } else  {
      $url.="?".$params;
    }     
    
    curl_setopt($ch, CURLOPT_URL, $url);
    
    if( curl_errno($ch)!==0 ){
      $answer = "[]";
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
    return preg_replace("/[^a-zA-Z0-9]/", "", strtoupper($text) );
  }
  /**
   * ПОЛНОСТЬЮ очищает из базы запчастей все данные от указанного производителя
   * @return boolean
   */
  protected function _clearAll(){
    if( !$this->_CLSID ){
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
      \yii::error($xml);      
      $xml_string = false;
      return [];
    }    
    $json = json_encode($xml_string,JSON_FORCE_OBJECT);    
    return json_decode($json,true);
  }
  /**
   * Преобразуем входящую JSON строку в массив
   * @param string $json
   * @return array
   */
  protected function jsonToArray($json){
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
