<?php
/**
 * @author Sync
 */
namespace app\controllers\actions\site;
use yii\base\Action;

class DefaultAction extends Action {
  
  public function run() {
	return $this->controller->render($this->id);
  }
}
