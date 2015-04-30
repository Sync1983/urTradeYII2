<?php

/**
 * Description of PartRecord
 *
 * @author Sync<atc58.ru>
 */
namespace app\models;
use yii\mongodb\ActiveRecord;

class RegRecord extends ActiveRecord {
  
  /**
   * Возвращает запись по её ID 
   * @param string $id
   * @return PartRecord
   */
  public static function getById($id){    
    return RegRecord::findOne(['_id'=> new \MongoId($id)]);    
  }
  /**
   * Возвращает текстовый id записи
   * @return RegRecord
   */  
  public function getStrID(){
    return strval($this->getAttribute("_id"));
  }
  /**
   * Проверяет наличие записи в базе с указанным ключем
   * @param string $key
   * @return boolean
   */
  public static function checkKey($key){
	$data = RegRecord::findOne([ 'key' => strval($key) ]);
	return $data?true:false;
  }
  /**
   * Возвращает запись по указанному ключу
   * @param strign $key
   * @return RegRecord
   */
  public function getByKey($key){
	$data = RegRecord::findOne([ 'key' => strval($key) ]);
	return $data;
  }
  /**
   * Генерирует уникальный ключ из 32х символов с проверкой на наличие в таблице
   * @return string
   */
  public static function generateKey(){
	$key = false;
	while ( !$key || self::checkKey($key) ) {
	  $key = \yii::$app->getSecurity()->generateRandomString();
	}
	return $key;
  }

  //====================================================================
  public function rules() {
    return [      
      [['_id', 'key', 'login', 'password', 'time', 'was_send', 'mail' ],'safe'],      
    ];
  }

  public static function collectionName(){
    return "prereg";
  }  
  
  public function attributes(){
    return ['_id', 'key', 'login', 'password', 'time', 'was_send', 'mail'];
  }
  
}
