<?php
/**
 * Description of NewsWidget
 *
 * @author Sync<atc58.ru>
 */

namespace app\components;
use yii\base\Widget;
use app\models\NewsProvider;

class NewsWidget extends Widget {
  /* @var $data_provider NewsProvider */
  public $data_provider;
  protected $data_out;


  public function init(){
    parent::init();    
  }
  
  public function run(){
    if($this->data_provider==null){
      return;
    }    
    return $this->render('news_widget',array('data'=> $this->data_provider));
  }
  
}
