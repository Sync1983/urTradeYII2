<?php

/**
 * Description of BalanceRecord
 * @author Sync<atc58.ru>
 */

namespace app\models\balance;
use yii\mongodb\ActiveRecord;

class BalanceRecord extends ActiveRecord{
  const OP_ADD = 1;
  const OP_DEC = 2;
  
  const IT_UNKNOW = 0;
  const IT_PART = 1;
  const IT_USER = 2;
  const IT_PAY_SYSTEM = 3;
  
  //public vars
  //protected vars
  protected $_operations = [ self::OP_ADD, self::OP_DEC, ];
  protected $_item_types = [ self::IT_UNKNOW,self::IT_PART, self::IT_USER, self::IT_PAY_SYSTEM, ];
  //private vars  
  //============================= Public =======================================
  /**
   * Запрещаем удаление записей из базы, 
   * отписываем в файлвый лог известные данные о попытке такого удаления
   * @return boolean
   */
  public function beforeDelete(){
    $message = [];
    $message['time'] = time();
    $message['alert'] = "Try delete balance record form base!";
    if(\yii::$app->user){
      $message['user'] = \yii::$app->user->getIdentity()->getId();
    } else {
      $message['user'] = "guest";
      $message['session'] = \yii::$app->session;
    }
    $message['request'] = \yii::$app->request;
    \yii::info("Try delete!!!".json_encode($message), 'balance');
    return false;
  }
  /**
   * Обработчик события beforeSave 
   * добавляет лог действий в файл,
   * а так же выставляет текущее время действия
   * @param type $insert
   * @return type
   */
  public function beforeSave($insert){
    $this->time = time();
    $attr = [];
    foreach($this->attributes() as $attribute){
      $attr[$attribute] = \yii\helpers\Html::decode(json_encode($this->$attribute));
    }
    $this->value *= 1.0;
    \yii::info("Balance event: ".json_encode($attr), 'balance');
    return parent::beforeSave($insert);
  }
  //============================= Protected ====================================
  /**
   * Валидатор для полей типов
   * @param type $attribute
   * @param type $item
   * @return boolean
   */
    public function inTypeList($attribute,$item){
    if(!in_array($this->$attribute, $this->_item_types,true)){
      $this->addError($attribute, "Неверное значение поля");
      return false;
    }
    return true;
  }
  /**
   * Валидатор для полей операций
   * @param type $attribute
   * @param type $item
   * @return boolean
   */
  public function inOperationList($attribute,$item){
    if(!in_array($this->$attribute, $this->_operations,true)){
      $this->addError($attribute, "Неверное значение поля");
      return false;
    }
    return true;
  }
  
  //============================= Private ======================================
  //============================= Constructor - Destructor =====================
  public static function collectionName(){
    return "balance";
  }
  
  public function attributes(){
    return [
      "_id",
      "time",
      "operation",
      "init_type","init_id",
      "reciver_type","reciver_id",
      "value",
      "item_type","item_id",
      "comment"
    ];
  }
  
  public function rules(){
    return [
      [["value"],"number"],
      [["operation"],"inOperationList"],
      [["init_type","reciver_type","item_type"],"inTypeList"],
      [["init_id","reciver_id","item_id"],"string"],
      [["comment"],"string"],
    ];    
  }

}
