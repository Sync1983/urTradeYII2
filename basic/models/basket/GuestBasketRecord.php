<?php

/**
 * Description of GuestBasketRecord
 * @author Sync<atc58.ru>
 */
namespace app\models\basket;
use yii\mongodb\ActiveRecord;

class GuestBasketRecord extends ActiveRecord{
  //public vars
  //protected vars
  //private vars  
  //============================= Public =======================================
  public static function collectionName() {
    return "guest_basket";
  }
  
  public function rules() {
    return [
      [['update_time'], 'integer'],
      [['basket'],'safe'],
    ];
  }
  
  public function attributes() {
    return ["_id","update_time","basket"];
  }
  
  public function beforeSave($insert) {
    $this->update_time = time();    
    return parent::beforeSave($insert);
  }
  //============================= Protected ====================================
  //============================= Private ======================================
  //============================= Constructor - Destructor =====================
}
