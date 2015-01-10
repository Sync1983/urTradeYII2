<?php

/**
 * Description of PartRecord
 *
 * @author Sync<atc58.ru>
 */
namespace app\models;
use yii\mongodb\Database;
use yii\mongodb\ActiveRecord;

class PartRecord extends ActiveRecord {
  
  public function attributes(){
    return ['_id',  
      "provider", "articul","producer","maker_id",
      "name","price","shiping","stock","info",
      "update_time","is_original","count","lot_quantity"];
  }  
  
  public static function collectionName(){
    return "parts";
  }  
  
}
