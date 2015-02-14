<?php

/**
 * Description of LoginWidget
 *
 * @author Sync<atc58.ru>
 */
namespace app\components;
use Yii;
use app\models\forms\LoginForm;

class LoginWidget extends \yii\base\Widget {
  public $form;
  public $isGuest;


  public function init(){
    parent::init();    
    $this->isGuest = Yii::$app->user->isGuest;
    $this->form = new LoginForm();
  }
  
  public function run(){
    $this->getView()->registerJsFile("/js/login_script.js");  
    $params = ['form'=>  $this->form];    
    $params['guest'] = $this->isGuest;
    return $this->render('login_widget',$params);
  }
}
