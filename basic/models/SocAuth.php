<?php
/**
 * Description of SocAuthModel
 * @author Sync<atc58.ru>
 */
namespace app\models;
use yii\mongodb\ActiveRecord;

class SocAuth extends ActiveRecord {
  //public vars
  //protected vars
  //private vars  
  //============================= Public =======================================
  public function getUserId(){
    return $this->getAttribute("user_id");
  }
  /*
   * @return SocAuth
   */
  public static function findBySocNetID($snet,$id){
   return SocAuth::findOne(["net"=>$snet."","soc_id"=>$id.""]);
  }
  
  public static function createRecord($snet,$user_id,$soc_id){
    $item = new SocAuth();
    $item->net = $snet."";
    $item->user_id = new \MongoId($user_id);
    $item->soc_id = $soc_id."";
    return $item->save();
  }
  
  public function attributes(){
    return [
      '_id', 
      'net',
      'user_id', 
      'soc_id'
      ];
  }  
  
  public static function collectionName(){
    return "soc_auth";
  }

  //============================= Protected ====================================
  //============================= Private ======================================
  //============================= Constructor - Destructor =====================
}
