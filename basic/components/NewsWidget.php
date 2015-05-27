<?php
/**
 * Description of NewsWidget
 *
 * @author Sync<atc58.ru>
 */

namespace app\components;
use yii\base\Widget;
use app\models\news\NewsModel;

class NewsWidget extends Widget {
  protected $data;

  public function init(){
    parent::init();    
    $this->data = NewsModel::find()
            ->where(['show'=>true])
            ->orderBy(['date'=>SORT_DESC])
            ->limit(50)
            ->all();
    \app\assets\NewsWidgetAsset::register($this->view);
  }
  
  public function run(){    
    return $this->render('news_widget',array('data'=> $this->data));
  }
  
}
