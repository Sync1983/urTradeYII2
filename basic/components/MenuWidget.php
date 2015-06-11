<?php
/**
 * Description of NewsWidget
 *
 * @author Sync<atc58.ru>
 */

namespace app\components;
use yii\base\Widget;
use yii\helpers\Url;

class MenuWidget extends Widget {
  protected $data;

  public $menu = [];
  public $default_over_price = 0;
  
  public function run(){
    var_dump($this->default_over_price);
    $caption    = \yii::$app->user->getCaption();
    $company    = \yii::$app->user->getCompanyName();
    $isAdmin    = \yii::$app->user->isAdmin();
    $isComapny  = \yii::$app->user->isCompany();
    $over_price = \yii::$app->user->getOverPiceList();

    $menu       = $this->createMenu();
    $list       = $this->createList($over_price);

    if( \yii::$app->user->isGuest ){
      return $this->render('menu_widget_guest',['menu'=> $menu]);
    }

    return $this->render('menu_widget',[
            'caption' => $caption,
            'company' => $company,
            'isAdmin' => $isAdmin,
            'isCompany' => $isComapny,
            'menu'=> $menu,
            'list'=> $list,
        ]);
  }

  protected function createMenu(){
    $answer = "";
    foreach ($this->menu as $item){
      $answer .= "<li>"
          . "<a href=\"" . Url::to($item['url']) . "\">"
          . "<p class=\"menu-title\">"
          . "<img src=\"". $item['img'] ."\" />"
          . "<span>" . $item['title'] . "</span>"
          . "</p>"
          . "<p class=\"menu-describe\">" . $item['descr'] . "</p>"
          . "</a>"
          . "</li>";
    }
    return $answer;
  }

  protected function createList($data){
    asort($data);
    $answer = "";
    foreach ($data as $name=>$value){
      $answer .= "<option data-subtext=\"$name\"". (($value==$this->default_over_price)?"selected":"")
          . ">$value %</option>";
    }
    return $answer;
  }
  
}
