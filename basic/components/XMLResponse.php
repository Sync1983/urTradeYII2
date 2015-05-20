<?php

/**
 * @author Sync
 */

namespace app\components;
use yii\web\ResponseFormatterInterface;

class XMLResponse implements ResponseFormatterInterface{
  /**
   * Форматирует ответ в XML
   * входные данные представляются в виде объекта с полями
   * name: {  //Имя записи   
   *  items: [ массив item ],
   *  attributes... : Все остальные аттрибуты будут размещены в виде пар ключ:значение в аттрибутах записи
   * }
   * @param type $response
   */
  public function format($response) {
    $content_type = "application/xml; charset=".$response->charset;
    $response->getHeaders()->set('Content-Type', $content_type);

    $head  = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
    $response->content = $head.$this->build($response->data);    
  }

  protected function build($root){
    $result = "";
    foreach ($root as $name=>$values){
      
      $start = "<$name";
      $items = isset($values['items'])?$values['items']:[];

      if( !empty($values['items']) ){
        unset($values['items']);
      }

      foreach ($values as $key=>$value){
        $start .= " $key=\"$value\"";
      }

      if( empty($items) ){
        $result .= $start . "/>";
      } else {
        $items_build = [];
        foreach ($items as $key => $item){
          $items_build[$key] = $this->build($item);
        }
        $result .= $start . ">" . implode(" ",$items_build) . "</$name>";
      }
    }
    return $result;
  }

}
