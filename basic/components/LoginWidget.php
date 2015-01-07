<?php

/**
 * Description of LoginWidget
 *
 * @author Sync<atc58.ru>
 */
namespace app\components;
use yii;

class LoginWidget extends \yii\base\Widget {
  public $form;
  
  public function init(){
    parent::init();    
  }
  
  public function run(){
    $this->getView()->registerJsFile("/js/login_script.js");    
    return $this->render('login_widget',array('form'=> $this->form));
  }
}
