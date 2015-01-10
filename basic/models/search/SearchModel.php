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
  
  protected $_providers = [];
  
  /**
   * Возвращает список производителей запрашиваемого артикула
   * @return array
   */
  public function generateMakerList(){
    /** @var SearchProviderBase $provider */
    $makers = [];
    foreach ($this->_providers as $provider){
      if(count($makers)==0){
        $makers = $provider->getMakerList($this->search, $this->cross);        
      } else {
        $makers = array_merge_recursive($makers, $provider->getMakerList($this->search, $this->cross));        
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
      $this->_providers[] = yii::createObject($provider,[$default]);      
    }
  }

  public function rules() {
    return [
      ['cross','boolean'],
      ['search','string'],
      ['op','string']      
    ];
  }
  
  public function attributes() {
    return ['cross','search','op'];
  }
  
  
}
