<?php

/**
 * Description of OverpriceModel
 *
 * @author Sync<atc58.ru>
 */

namespace app\models\prices;
use yii;
use yii\base\Model;
use app\models\MongoUser;
use app\models\prices\Overprice;

class OverpriceModel extends Model {  
  /** @var $_user MongoUser **/
  protected $_user;
  public $prices = ['a'=>10,'b'=>20];  
  public $prices_name = [];
  public $prices_value = [];
  
  public function rules() {    
    return [
            ['prices_name','validateName'],
            ['prices_value','validateValue']];
  }
  
  public function validateValue($attribute,$attr){
    if(!isset($this->attributes[$attribute])){
      $this->addError($attribute,"Неверное имя аттрибута");
      return false;
    }
    $values = $this->attributes[$attribute];
    if(!is_array($values)){      
      $this->addError($attribute,"Значение должно быть массивом");
      return false;
    }
    foreach ($values as $value) {
      if($value*1!=$value){
        $this->addError($attribute,"Значение { $value } должно быть целым числом");
        return false;        
      }
    }
    if(count($this->prices_name)!=count($this->prices_value)){
      $this->addError($attribute,"Количество значений и имен должно совпадать");
      return false;
    }
    return TRUE;
  }
  
  public function validateName($attribute,$attr){
    if(!isset($this->attributes[$attribute])){
      $this->addError($attribute,"Неверное имя аттрибута");
      return false;
    }
    $values = $this->attributes[$attribute];
    if(!is_array($values)){      
      $this->addError($attribute,"Значение должно быть массивом");
      return false;
    }
    foreach ($values as $value) {
      if(!is_string($value)){
        $this->addError($attribute,"Имя { $value } должно быть целым числом");
        return false;        
      }
    }    
    if(count($this->prices_name)!=count($this->prices_value)){
      $this->addError($attribute,"Количество значений и имен должно совпадать");
      return false;
    }    
    return TRUE;
  }

  public function __construct($config = array()) {
    parent::__construct($config);
    
    $this->_user = yii::$app->user->getIdentity();
    if(!($this->_user && ($this->prices = $this->_user->getOverPiceList()))){
      $this->prices = [];
    }    
  }
  
  /**
   * Возвращает значение наценки по её имени
   * @param String$name
   * @return int
   */
  public function getValue($name){
    if(isset($this->prices[$name])){
      return intval($this->prices[$name]);
    }
    return 0;
  }

  public function load($data,$formName=NULL){    
    parent::load($data, $formName);
    
    if(count($this->prices_name)!=count($this->prices_value)){
      return false;
    }
    
    $this->prices = [];
    foreach ($this->prices_name as $key=>$name){
      $this->prices[$name] = $this->prices_value[$key]*1;
    }
    return true;
  }
   
  public function setList($data = []){
    $this->prices = $data;
    $this->_user->setOverPiceList($data);
  }
  
  public function save(){
    if(!$this->_user){
      return;
    }
    $this->_user->setOverPiceList($this->prices);     
    return $this->_user->update();
  }
  
}
