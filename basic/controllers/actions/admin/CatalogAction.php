<?php
/**
 * @author Sync
 */
namespace app\controllers\actions\admin;
use yii\base\Action;

class CatalogAction extends Action {
  const TYPE_CREATE = "create";
  const TYPE_CONTROL= "control";
  const TYPE_FILL   = "fill";
  
  public $type = self::TYPE_CREATE;


  public function run() {	
    if( $this->type == self::TYPE_CREATE ){
      return $this->typeCreate();
    }elseif( $this->type == self::TYPE_CONTROL ){
      return $this->typeControl();
    }elseif( $this->type == self::TYPE_FILL ){
      return $this->typeFill();
    }
  }

  protected function typeCreate() {
    $model = new \app\models\forms\CatalogCreateForm();
    $data = $model->fields;
    
    return $this->controller->render("catalogs/catalog-create",['model'=>$model,'data'=>$data]);
  }

  protected function typeControl() {

  }

  protected function typeFill() {

  }

  
}