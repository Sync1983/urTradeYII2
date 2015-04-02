<?php

/**
 * Description of Basket
 * @author Sync<atc58.ru>
 */

namespace app\controllers;

use yii;
use yii\web\Response;
use yii\base\Controller;
use app\models\MongoUser;


class C1Controller extends Controller{
  //public vars  
  
  //protected vars
  protected $_xml_head = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\r\n"
      . "<data></data>";
  //private vars  
  //============================= Public =======================================
  public function actionUsers(){
    $users = MongoUser::find()->all();
    
    $xml = new \SimpleXMLElement($this->_xml_head);
    foreach ($users as $user){
      $child = $xml->addChild("user");
      $attributes = $user->getAttributes();
      $attributes['create_time'] = $attributes["_id"]->getTimeStamp();
      unset($attributes["_id"]);
      unset($attributes["over_price"]);
      unset($attributes["basket"]);
      unset($attributes["informer"]);
      unset($attributes["user_pass"]);
      $attributes['create_time'] = date("H:i:s d-m-y",$attributes['create_time']);
      $child->addAttribute("id", $user->getAttribute("_id"));
      
      foreach ($attributes as $name => $value){
        if( !is_array($value) ){
         $child->addAttribute($name,$value);
        }/* else {
          var_dump($value);
          echo "<br>";
        }*/
      }
    }
      yii::$app->response->format = Response::FORMAT_XML;
      echo $xml->asXML();
    //$xml->
  }
  
  //============================= Protected ====================================
  //============================= Private ======================================
  //============================= Constructor - Destructor =====================
  public function beforeAction($action) {
    return parent::beforeAction($action);
  }
}
