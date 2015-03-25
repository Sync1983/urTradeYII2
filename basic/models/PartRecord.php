<?php

/**
 * Description of PartRecord
 *
 * @author Sync<atc58.ru>
 */
namespace app\models;
use yii\mongodb\ActiveRecord;

class PartRecord extends ActiveRecord {
  protected $_compare_fields =[    
      "provider", "articul","producer","maker_id","name","price","shiping","stock",
      "is_original","lot_quantity"
  ];
  
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
  /**
   * Возвращает деталь по её ID в базе
   * @param string $id Текстовый id детали
   * @return PartRecord
   */
  public static function getById($id){    
    return PartRecord::findOne(['_id'=> new \MongoId($id)]);    
  }
  /**
   * Сравнивает текущую деталь с передаваемой в параметре
   * @param PartRecord $part
   * @return boolean
   */
  public function compare($part){
    $this_attributes = $this->getAttributes();
    $part_attributes = $part->getAttributes();
    foreach ($this->_compare_fields as $field){
      if($part_attributes[$field]!==$this_attributes[$field]){
        return false;
      }
    }
    return true;
  }
  
  public function getStrID(){
    return strval($this->getAttribute("_id"));
  }

  //====================================================================
  public function rules() {
    return [      
      [['_id','search_articul',  
        "provider", "articul","producer","maker_id",
        "name","price","shiping","stock","info",
        "update_time","is_original","count","lot_quantity","for_user"],'safe'],      
    ];
  }

  public static function collectionName(){
    return "parts";
  }  
  
  public function attributes(){
    return ['_id','search_articul',  
      "provider", "articul","producer","maker_id",
      "name","price","shiping","stock","info",
      "update_time","is_original","count","lot_quantity","for_user"];
  }

  public function beforeSave($insert) {
    $this->update_time = time();
    return parent::beforeSave($insert);
  }
  
}
