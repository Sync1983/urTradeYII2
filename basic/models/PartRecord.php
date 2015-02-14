<?php

/**
 * Description of PartRecord
 *
 * @author Sync<atc58.ru>
 */
namespace app\models;
use yii\mongodb\ActiveRecord;

class PartRecord extends ActiveRecord {
  
  /**
   * Возвращает 20 лучших запчастей для указанных условий
   * @param array $cond Условия поиска
   * @return array
   */
  public static function getPartsForOnlineProvider($cond=[]){
    return self::find()->
        where($cond)->
        orderBy(["price"=>SORT_ASC,"shiping"=>SORT_ASC])->
        limit(20)->
        asArray()->
        all();
  }
  /**
   * Возвращает первые 50 позиций начинающихся с текста part_id
   * для формирования помошника поиска
   * Производитель не учитывается
   * @param string $part_id
   * @return mixed
   */
  public static function getHelperByPartId($part_id){
    if(strlen($part_id."")<3){
      return false;
    }
    $cond = PartRecord::getCollection()->buildRegexCondition('articul',['articul',"/^$part_id.*/"]);    
    $result = PartRecord::find()->where($cond)->limit(50)->all();
    return $result;
  }
  
  //====================================================================
  public function attributes(){
    return ['_id','search_articul',  
      "provider", "articul","producer","maker_id",
      "name","price","shiping","stock","info",
      "update_time","is_original","count","lot_quantity"];
  }  
  
  public static function collectionName(){
    return "parts";
  }  
  
}
