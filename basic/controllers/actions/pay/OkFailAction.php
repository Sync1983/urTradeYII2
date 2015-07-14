<?php

/**
 * @author Sync
 */
namespace app\controllers\actions\pay;
use yii\base\Action;

class OkFailAction extends Action{
  const OK    = "OK";
  const FAIL  = "FAIL";

  public $type = self::FAIL;

  public function run() {
    if( $this->type==self::OK ){
      return $this->okAnswer();
    } elseif ($this->type==self::FAIL ) {
      return $this->failAnswer();
    }
    return $this->controller->goHome();
  }

  protected function okAnswer(){
    \yii::info("YA OK ANSWER", 'balance');
    return $this->controller->render("ok-fail",['fail' => false]);
  }

  protected function failAnswer(){
    \yii::info("YA [FAIL] ANSWER", 'balance');
    return $this->controller->render("ok-fail",['fail' => true]);
  }
  
}
