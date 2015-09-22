<?php
/**
 * @author Sync
 */
namespace app\controllers\actions\admin;
use yii\base\Action;

class CarsAction extends Action {
  
  const TYPE_CREATE = "create";
  const TYPE_CONTROL= "control";
  const TYPE_FILL   = "fill";
  
  public $type = self::TYPE_CREATE;


  public function run() {	
    return $this->controller->render('cars/index');
  }
  
}