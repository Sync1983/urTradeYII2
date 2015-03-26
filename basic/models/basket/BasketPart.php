<?php

/**
 * Description of PartRecord
 *
 * @author Sync<atc58.ru>
 */
namespace app\models\basket;
use yii\db\BaseActiveRecord;

class BasketPart extends BaseActiveRecord{  
  
  public function getStrID(){
    return strval($this->getAttribute("_id"));
  }

  //====================================================================
  public function rules() {
    return [
      [['sell_count'],'checkSellCount'],      
      [['_id','search_articul',  
        "provider", "articul","producer","maker_id",
        "name","price","shiping","stock","info",
        "update_time","is_original","count","lot_quantity", 
        "for_user","price_change","sell_count","comment"],'safe'],      
    ];
  }
  
  public function checkSellCount($attribute,$params){
    if(!$this->hasAttribute($attribute)){
      return true;
    }
    $mod = $this->sell_count % $this->lot_quantity;
    if($mod!==0){
      $this->addError($attribute, "Количество детаелй должно быть кратно ".$this->lot_quantity." шт.");
      return false;
    }
    return true;
  }
  
  public function attributes(){
    return ['_id','search_articul',  
      "provider", "articul","producer","maker_id",
      "name","price","shiping","stock","info",
      "update_time","is_original","count","lot_quantity", 
      "for_user","price_change","sell_count","comment"];
  }

  public function insert($runValidation = true, $attributes = null) {
    
  }

  public static function find() {
    
  }

  public static function getDb() {
    
  }

  public static function primaryKey() {
    return $this->getAttribute("_id");
  }

}
