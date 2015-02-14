<?php
/**
 * Description of MainMenuWidget
 *
 * @author Sync<atc58.ru>
 */
namespace app\components;

use yii\bootstrap\Widget;
use app\models\SiteModel;
use yii\helpers\Url;

class MainMenuWidget extends Widget{
  
  public $items = [];  
  
  public function init() {
    parent::init();    
  }

  public function run(){        
    foreach ($this->items as $name=> $params) {      
      $class = isset($params['class'])?$params['class']:"";      
      $url = Url::to(isset($params['url'])?$params['url']:"");
      $describe = isset($params['describe'])?$params['describe']:"";
      $this->items[$name]=[
        'class' => $class,
        'url'   => $url,
        'badge' => 0,
        'describe' =>$describe,
      ];  
    }
    $this->getView()->registerCssFile("/css/main_menu.css");
    return $this->render("menu_widget",[
      'model' =>  SiteModel::_instance(),
      'items' =>  $this->items      
    ]);
  }
}
