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
  
  public function run(){
    $caption    = \yii::$app->user->getCaption();
    $company    = \yii::$app->user->getCompanyName();
    $isAdmin    = \yii::$app->user->isAdmin();
    $menu       = $this->createMenu();

    if( \yii::$app->user->isGuest ){
      return $this->render('menu_widget_guest',['menu'=> $menu]);
    }

    return $this->render('menu_widget',[
            'caption' => $caption,
            'company' => $company,
            'isAdmin' => $isAdmin,
            'menu'=> $menu
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
  
}
