<?php
/**
 * Description of SearchForm
 *
 * @author Sync<atc58.ru>
 */
namespace app\models\forms;

use yii;
use yii\base\Model;
use app\models\search\SearchProviderBase;

class SearchForm extends Model{
  protected $_providers;
  
  public $cross = true;
  public $search_text;
  public $over_price = 0;
  public $history = [];
  
  /**
   * Возвращает список производителей запрашиваемого артикула
   * @return array
   */
  public function generateMakerList(){
    $makers = [];
    $search = SearchProviderBase::_clearStr($this->search_text);    
    foreach ($this->_providers as $provider){      
      if(count($makers)==0){
        $makers = $provider->getMakerList($search, $this->cross);        
      } else {
        $makers = array_merge_recursive($makers, $provider->getMakerList($search, $this->cross));        
      }
    }
    return $makers;
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
    $this->over_price = yii::$app->user->getOverPiceList();
    $op_items = [];    
    foreach ($this->over_price as $key=>$value){
      $op_items[$value] = $key."($value%)";
    }
    ksort($op_items);
    $this->over_price = $op_items;
  }
  
  public function validateSearch($attribute, $params){
    //var_dump($attribute);
    return true;
  }

  public function rules(){
    return [            
            ['search_text','validateSearch'],            
            ['cross', 'boolean'],
            ['over_price','integer']
            ];
  }
  
}
