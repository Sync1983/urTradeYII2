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
  
  public $cross = false;
  public $search_text;
  public $over_price = 0;
  public $over_price_list = [];
  public $history = [];
  public $catalog = [
          ["label" => "Общие"       ,'url'=> "#"  ],
          ["label" => "Комплекты ТО",'url'=> "#"  ],
          ["label" => "Масла"       ,'url'=> "#"  ],
          ["label" => "Аксесуары"   ,'url'=> "#"  ],
          ];
  
  /**
   * Возвращает список производителей запрашиваемого артикула
   * @return array
   */
  public function generateMakerList(){    
    $makers = [];
    $search = SearchProviderBase::_clearStr($this->search_text);    
    foreach ($this->_providers as $provider){      
      $provider_list = $provider->getMakerList($search, $this->cross);
      $makers = \yii\helpers\ArrayHelper::merge($makers,$provider_list);
    }
    ksort($makers,SORT_STRING);
    $makers_json = [];
    foreach ($makers as $key => $value){
      $makers_json[ $key ] = json_encode($value,JSON_FORCE_OBJECT);
    }
    return $makers_json;
  }
  
  //============= System =================
  
  public function init() {
    parent::init();
    $param = isset(yii::$app->params['providerUse'])?yii::$app->params['providerUse']:false;
    $default_data = isset(yii::$app->params['providers'])?yii::$app->params['providers']:false;
    if( !$param || !$default_data|| !is_array($param)){
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
    $this->over_price_list = yii::$app->user->getOverPiceList();
    $op_items = [];    
    foreach ($this->over_price_list as $key=>$value){
      $op_items[$value] = $key."($value%)";
    }
    ksort($op_items);
    $this->over_price_list = $op_items;
  }
  
  public function validateSearch($attribute, $params){
    $this->$attribute = SearchProviderBase::_clearStr($this->$attribute);
    return true;
  }

  public function rules(){
    return [            
            ['search_text','validateSearch'],            
            ['cross', 'boolean'],
            ['over_price','integer']
            ];
  }

  public function attributeLabels() {
    return [
      'catalog' => "Каталоги",
    ];
  }
  
}
