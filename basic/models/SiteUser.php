<?php

/**
 * Description of SiteUser
 *
 * @author Sync<atc58.ru>
 */
namespace app\models;

use yii\web\User;
use app\models\MongoUser;

class SiteUser extends User{
  
  /**
   * Возвращает логин пользователя
   * @return string
   */
  public function getLogin(){
    if($this->isGuest){
      return "";
    }
    $identity = $this->getIdentity();
    /* @var $identity MongoUser */
    return $identity->getAttribute("user_name");
  }
  /**
   * Возвращает название организации
   * @return string
   */
  public function getCaption(){
    if($this->isGuest){
      return "";
    }
    $identity = $this->getIdentity();
    /* @var $identity MongoUser */
    return $identity->getAttribute("name");
  }
  
  public function getOverPiceList(){
    if($this->isGuest){
      return ["Без наценки"=>0];
    }
    $identity = $this->getIdentity();
    /* @var $identity MongoUser */
    return array_merge($identity->getOverPiceList(),["Без наценки"=>0]);
  }
}
