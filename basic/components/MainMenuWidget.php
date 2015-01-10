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
  public $brand = [
                    'url'   => [],
                    'class' => '',
                    'img'   => ''
                  ];
  
  public function init() {
    parent::init();
    if(!isset($this->brand['url'])){
      $this->brand['url'] = [];
    }
    if(!isset($this->brand['class'])){
      $this->brand['class'] = '';
    }
    if(!isset($this->brand['img'])){
      $this->brand['img'] = '/img/brand.png';
    }
  }

  public function run(){        
    foreach ($this->items as $name=> $params) {      
      $class = isset($params['class'])?$params['class']:"";      
      $url = Url::to(isset($params['url'])?$params['url']:"");
      $this->items[$name]=[
        'class' => $class,
        'url'   => $url,
        'badge' => 0,
      ];  
    }
    $this->getView()->registerCssFile("/css/main_menu.css");
    return $this->render("menu_widget",[
      'model' =>  SiteModel::_instance(),
      'items' =>  $this->items,
      'brand' =>  $this->brand,
    ]);
  }
}
