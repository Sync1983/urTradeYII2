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
  public $search;
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
  public function loadParts(){    
    if(!$class = $this->getProviderByCLSID($this->provider)){
      return [];
    }
    $search = SearchProviderBase::_clearStr($this->search);    
    $parts = $class->getPartList($search,  $this->maker_id,  $this->cross);
    $isGuest = yii::$app->user->isGuest;
    $over_price = 0;
    if (!$isGuest) {
      $over_price = yii::$app->user->getIdentity()->getUserOverPriver();
    }
    foreach ($parts as $key=>$part){
      if(!$isGuest){
        $parts[$key]["price"] += round($over_price*$part["price"]/100,2);
      } else {
        //Цена для гостей
        //$parts[$key]["price"] = 0;
      }
    }
    return $parts;
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
      $class = yii::createObject($provider,[$default]);
      $this->_providers[$class->getCLSID()] = $class;
    }
  }

  public function rules() {
    return [
      ['cross','boolean'],
      ['search','string'],
      ['op','string'],
      ['provider','integer'],
      ['maker_id','string'],      
    ];
  }
  
  public function attributes() {
    return ['cross','search','op','maker_id','provider'];
  }
  
  
}
