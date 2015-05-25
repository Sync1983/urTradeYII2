<?php
/**
 * Description of Search
 *
 * @author Sync<atc58.ru>
 */
namespace app\models\search;
use yii;
use yii\base\Model;
use app\models\search\SearchProviderBase;

class SearchModel extends Model{
  
  public $cross;
  public $search_text;
  public $op;
  public $provider;
  public $maker_id;
  
  protected $_providers = [];  
  
  /**
   * Возвращает список запчастей для конкретного поставщика
   * Данные provider и maker_id должны быть переданы модели
   * как и указания на правила кросс-номеров, наценки и 
   * строки поиска артикула детали
   * @return array
   */
  public function loadAllParts(){
    if( !$class = $this->getProviderByCLSID($this->provider) ){
      return [];
    }

    $search = SearchProviderBase::_clearStr($this->search_text);    
    $parts = $class->getPartList($search,  $this->maker_id,  $this->cross, true);
    
    $answer_data = [];    
    foreach ($parts as $key=>$part){      
        $data = $parts[$key];
        $data["price"] = yii::$app->user->getUserPrice($data["price"]);
        if( !isset($data["info"]) || is_array($data["info"]) ){
          $data["info"] = "";
        }
	  
        $answer_data[$key] = [
          "id"          => strval($data["_id"]),
          "articul"     => $data["articul"],
          "producer"    => $data["producer"],
          "name"        => $data["name"],
          "price"       => $data["price"],
          "shiping"     => $data["shiping"],
          "info"        => strval($data["info"]),
          "update_time" => $data["update_time"],
          "is_original" => boolval($data["is_original"]),
          "count"       => $data["count"],
          "lot_quantity"=> $data["lot_quantity"],
          "data-order"	=> ( ( $data['articul'] === $data['search_articul'] )? "0": "10" ). "_" . $data["articul"],
        ];
    }
    return $answer_data;
  }  
  /**
   * Возвращает список запчастей для конкретного поставщика
   * Данные provider и maker_id должны быть переданы модели
   * как и указания на правила кросс-номеров, наценки и
   * строки поиска артикула детали
   * @return array
   */
  public function loadParts(){
    if(!$class = $this->getProviderByCLSID($this->provider)){
      return [];
    }
    $search = SearchProviderBase::_clearStr($this->search_text);
    $parts = $class->getPartList($search,  $this->maker_id,  $this->cross);

    $answer_data = [];
    foreach ($parts as $key=>$part){
        $data = $parts[$key];
        $data["price"] = yii::$app->user->getUserPrice($data["price"]);
        if( !isset($data["info"]) || is_array($data["info"]) ){
          $data["info"] = "";
        }

        $answer_data[$key] = [
          "id"          => strval($data["_id"]),
          "articul"     => $data["articul"],
          "producer"    => $data["producer"],
          "name"        => $data["name"],
          "price"       => $data["price"],
          "shiping"     => $data["shiping"],
          "info"        => strval($data["info"]),
          "update_time" => $data["update_time"],
          "is_original" => boolval($data["is_original"]),
          "count"       => $data["count"],
          "lot_quantity"=> $data["lot_quantity"],
          "data-order"	=> ( ( $data['articul'] === $data['search_articul'] )? "0": "10" ). "_" . $data["articul"],
        ];
    }
    return $answer_data;
  }
  /**
   * Возвращает класс поставщика по указанном CLSID
   * @param int $clsid
   * @return mixed
   */
  public function getProviderByCLSID($clsid=0){
    $clsid = intval($clsid);
    if(!isset($this->_providers[$clsid])){
      return false;
    }
    return $this->_providers[$clsid];        
  }
  /**
   * Возвращает CLSID поставщика с которым выполнялись текущие операции
   * @return integer
   */
  public function getCurrentCLSID(){
    return $this->provider;
  }

  //============= System =================
  
  public function init() {
    parent::init();
    if(!isset(yii::$app->params['providerUse'])){
      return;
    }
    $param = yii::$app->params['providerUse'];
    $default_data = yii::$app->params['providers'];
    if(!is_array($param)){
      return;
    }
    foreach ($param as $provider){
      $default = [];
      if(isset($default_data[$provider])){
        $default = $default_data[$provider];
      }      
      $class = yii::createObject($provider,[$default,[]]);
      $this->_providers[$class->getCLSID()] = $class;
    }
  }

  public function rules() {
    return [
      ['cross','boolean'],
      ['search_text','string'],
      ['op','string'],
      ['provider','integer'],
      ['maker_id','string'],      
    ];
  }
  
  public function attributes() {
    return ['cross','search_text','op','maker_id','provider'];
  }
  
  
}
