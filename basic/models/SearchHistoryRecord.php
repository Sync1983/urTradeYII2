<?php
/**
 * Description of SearchHistoryRecord
 *
 * @author Sync<atc58.ru>
 */
namespace app\models;
use Yii;
use yii\mongodb\ActiveRecord;

class SearchHistoryRecord extends ActiveRecord{
  /**
   * Преобразовывает текст 10 последних запросов в структуру для DropDown
   * @return mixed
   */
  public static function getHtmlList(){
    $items = self::getLastRecords();
    $answer = [];
    foreach ($items as $item) {
      $answer[] = [
        'label'   => "\"".$item['_id'] ."\" в ". date("H:i:s",$item['time']),
        'url'     => "#",
        'options' =>[
          'onclick' => "main.historyItemClick('".$item['_id']."');"          
        ]
      ];
    }
    return $answer;
  }
  /**
   * Возвращает текст 10 последних поисковых запросов
   * @return array
   */
  public static function getLastRecords(){
    if(Yii::$app->user->isGuest){
      return [];
    }    
    $user = Yii::$app->user->getIdentity();
    $collection = self::getCollection();
    $result = $collection->aggregate([
      ['$match' =>['user'=>$user->getObjectId()]],
      ['$group' =>["_id"  => '$text','time'=>['$max'=>'$time']]],
      ['$sort'  =>["time" => -1]],      
      ['$limit' => 2],
      ['$project' => ["time" => 1]]
    ]);    
    return $result;
  }
  /**
   * Записывает новый поисковой запрос в базу
   * @param type $text
   * @return boolean
   */
  public static function addQuery($text){
    if(Yii::$app->user->isGuest){
      return false;
    }    
    $user = Yii::$app->user->getIdentity();    
    $data = [
      'user'  => $user->getObjectId(),
      'time'  => time(),
      'text'  => $text
    ];    
    $record = new SearchHistoryRecord();
    $record->setAttributes($data,false);
    return $record->save();
  }

  public function attributes(){
    return ['_id','user','time','text'];
  }  
  
  public static function collectionName(){
    return "search_history";
  }  
}
