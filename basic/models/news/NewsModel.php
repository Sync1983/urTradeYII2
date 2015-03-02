<?php
/**
 * Description of NewsModel
 *
 * @author Sync<atc58.ru>
 */
namespace app\models\news;
use yii\mongodb\ActiveRecord;
use yii\helpers\Html;

class NewsModel extends ActiveRecord {  
  public $show = false;
  
  public function attributes(){
    return ['_id', 'icon', 'header', 'text', 'date', 'show'];
  }  
  
  public static function collectionName(){
    return "news";
  }
  
  public function getId(){
    /* @var $id MongoId */
    $id = $this->getAttribute("_id");
    return $id->__toString();
  }

  public function head(){
    return Html::label($this->getAttribute("header"),null,['class'=>'news-header']);
  }
  
  public function icon(){
    return Html::img($this->getAttribute("icon"));
  }
  
  public function text(){
    return Html::label($this->getAttribute("text"),null,['class'=>'news-text']);
  }
  
  public function date(){
    $date = $this->getAttribute("date");
    setlocale(LC_TIME, "ru_RU");
    $date_fmt = date("H:i:s d-M-Y", $date);
    return Html::label($date_fmt,null,['class'=>'news-date']);
  }
  
  public function isVisible(){
    return $this->getAttribute('show');
  }
  
}
