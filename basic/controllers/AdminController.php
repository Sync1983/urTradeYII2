<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\news\NewsModel;


class AdminController extends Controller
{
  public $layout = 'admin';
  public $menu_items = [
            'Новости'      => 'news'   ,
            'Прайс-листы'  => 'prices' ,
            'Заказы'       => 'orders' ,
            'Корзины'      => 'baskets',
            'Пользователи' => 'users'  
    ];
  
  public function behaviors(){
      return [];
  }

  public function actions(){
    return [
      'error' => [
        'class' => 'yii\web\ErrorAction',
      ]            
    ];
  }

  public function actionIndex() { 
    $this->view->registerCssFile('/css/admin.css');    
    return $this->render('index',['menu_items'=> array_flip($this->menu_items)]);
  }
  
  public function actionMenuCall(){
    if(!Yii::$app->request->isAjax){
      echo json_encode(['error'=>'Запрос должен быть внутренним']);
      Yii::$app->end();
      return;
    }
    $request = Yii::$app->request->post();
    $item = $request['item'];
    if(!isset($this->menu_items[$item])){
      echo json_encode(['error'=>"Действие не найдено"]);
      Yii::$app->end();
      return;      
    }
    
    $name = $this->menu_items[$item];
    if(!$this->hasMethod("menu".$name)){
      echo json_encode(['error'=>"Метод $name не найден"]);
      Yii::$app->end();
      return;      
    }
    $html = call_user_method("menu".$name, $this);
    echo json_encode(["html"=>$html]);
    Yii::$app->end();
    return;    
  }
  
  public function actionNewsSave(){
    if(!Yii::$app->request->isAjax){
      echo json_encode(['error'=>'Запрос должен быть внутренним']);
      Yii::$app->end();
      return;
    }
    $request = Yii::$app->request->post();
    $uid  = $this->loadFromArray("uid", $request);
    $head = $this->loadFromArray("head", $request);
    $icon = $this->loadFromArray("icon", $request);
    $text = $this->loadFromArray("text", $request);
    $show = $this->loadFromArray("show", $request);
    if($show!="true"){
      $show = false;
    } else {
      $show = true;
    }
    $item = NewsModel::findOne(['_id'=>$uid]);
    if(!$item){
      echo json_encode(['error'=>'Запись не найдена']);
      Yii::$app->end();
      return;      
    }
    /* @var $item NewsModel */
    $item->setAttributes([
      'header'  => $head,
      'text'    => $text,
      'icon'    => $icon,
      'show'    => $show
      ],false);    
    if($item->save(false)){
      echo json_encode(['ok'=>1]);
    } else {
      echo json_encode(['error'=>"Ошибка записи"]);      
    }
    Yii::$app->end();
    return;
  }
  
  //============= Menu Actions ===============
  protected function menuNews(){
    $items = NewsModel::find()->orderBy(['date'=>SORT_DESC])->all();
    return $this->renderAjax("news",['items'=>$items]);
  }
  
  protected function loadFromArray($needle,$array){
    return isset($array[$needle])?$array[$needle]:"";
  }
}
