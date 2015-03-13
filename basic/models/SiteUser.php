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
  
  public function getId(){
    if($this->isGuest){
      return 0;
    }
    $identity = $this->getIdentity();
    /* @var $identity MongoUser */
    return $identity->getId();    
  }
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
   * Возвращает Имя пользователя
   * @return string
   */
  public function getCaption(){
    if($this->isGuest){
      return "";
    }
    $identity = $this->getIdentity();
    /* @var $identity MongoUser */
    return $identity->getUserName();
  }
  /**
   * Возвращает Название компании
   * @return string
   */
  public function getCompanyName(){
    if($this->isGuest){
      return "";
    }
    $identity = $this->getIdentity();
    /* @var $identity MongoUser */
    return $identity->getAttribute("name");
  }
  /*
   * Поверяет имеет ли пользователь права администратора
   */
  public function isAdmin(){
    if($this->isGuest){
      return false;
    }
    $identity = $this->getIdentity();
    /* @var $identity MongoUser */
    return $identity->isAdmin();    
  }
  
  public function isCompany(){
    if($this->isGuest){
      return false;
    }
    $identity = $this->getIdentity();
    /* @var $identity MongoUser */
    return $identity->type=="company";    
  }

  public function getOverPiceList(){
    if($this->isGuest){
      return ["Без наценки"=>0];
    }
    $identity = $this->getIdentity();
    /* @var $identity MongoUser */
    return array_merge($identity->getOverPiceList(),["Без наценки"=>0]);
  }
  
  public function saveOverPriceList($list = []){
    if($this->isGuest){
      return false;
    }
    $identity = $this->getIdentity();
    $identity->setAttribute('over_price_list', $list);
    return $identity->save();
  }
}
