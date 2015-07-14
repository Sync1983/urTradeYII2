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
    $caption    = \yii::$app->user->getCaption();
    $company    = \yii::$app->user->getCompanyName();
    $isAdmin    = \yii::$app->user->isAdmin();
    $isComapny  = \yii::$app->user->isCompany();
    $over_price = \yii::$app->user->getOverPiceList();

    $menu       = $this->createMenu();
    $list       = $this->createList($over_price);
    $raw        = $this->rawMenu();

    if( \yii::$app->user->isGuest ){
      return $this->render('menu_widget_guest',['menu'=> $menu,'raw_menu' => $raw]);
    }

    return $this->render('menu_widget',[
            'caption' => $caption,
            'company' => $company,
            'isAdmin' => $isAdmin,
            'isCompany' => $isComapny,
            'menu'=> $menu,
            'list'=> $list,
            'raw_menu' => $raw
        ]);
  }

  protected function rawMenu() {
    $user = \yii::$app->user;
    $isCompany = $user->isCompany();
    $overPrices = $user->getOverPiceList();
    $op = \yii::$app->request->get("op",0);
    $searchForm = new \app\models\forms\SearchForm();
    $searchForm->load(\yii::$app->request->get(),"");
    $cross = $searchForm->cross;

    $menu = [];
    foreach ($this->menu as $item){
      $menu[] = [
        'label' => $item['title'],
        'url'   => $item['url']
      ];
    }
    
    if( $isCompany ){
      $sub_menu1 = [];
      foreach ($overPrices as $name=>$value){
        $hidden = "hidden";
        if( $op === $value ){
          $hidden = "";
        }
        $sub_menu1[] = [
          'encode'   => false,
          'label' => "<span class=\"glyphicon glyphicon-ok $hidden\"></span>  " . $name . "( $value% )",
          'url'   => "#",
          'options'=> ['onClick' => "$().main().overPriceClick(this," . intval($value) .")", 'class' => 'over-price-menu-main'],
        ];
      }
      $menu[] =  '<li class="divider"></li>';
      $menu[] =  ['label'=>'Наценка','items'=>$sub_menu1,'options'=>['class'=>'sub-menu-main']];
    }
    
    $menu[] =  '<li class="divider"></li>';
    $cross_label = "<span class=\"glyphicon glyphicon-ok hidden\"></span> Аналоги";
    if( $cross ){
      $cross_label = "<span class=\"glyphicon glyphicon-ok\"></span> Аналоги";
    }
    $menu[] = [
      'label'   => $cross_label,
      'encode'   => false,
      'url'     => "#",
      'options' => ['onClick' => '$().main().crossClick()','class'=>'cross-menu']
    ];
    return $menu;
  }

  protected function createMenu(){
    $answer = "";
    foreach ($this->menu as $item){
      $answer .= "<li class=\"col-md-2 col-xs-2\">"
          . "<a href=\"" . Url::to($item['url']) . "\">"
          . "<p class=\"menu-title\">"
          . "<img class=\"visible-lg-inline-block\" src=\"". $item['img'] ."\" />"
          . "<img class=\"visible-sm\" src=\"". $item['img'] ."\" />"
          . "<span class=\"hidden-sm\">" . $item['title'] . "</span>"
          . "<span class=\"visible-sm\">" . $item['title'] . "</span>"
          . "</p>"
          . "<p class=\"menu-describe visible-md visible-lg\">" . $item['descr'] . "</p>"
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
