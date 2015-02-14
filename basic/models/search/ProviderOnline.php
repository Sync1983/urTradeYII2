<?php

/**
 * Description of ProviderOnline
 *
 * @author Sync<atc58.ru>
 */
namespace app\models\search;
use app\models\search\SearchProviderBase;
use app\models\PartRecord;

class ProviderOnline extends SearchProviderBase{
  const CLSID = 001;
  const Name  = "Online";
  protected $url = "http://onlinezakaz.ru/xmlprice.php"; 


  public function __construct($default_params=[], $config=[]) {
    parent::__construct(self::Name, self::CLSID, $default_params, $config);
  }
  
  public function getPartList($part_id="",$maker_id="",$cross=false){    
    $xml = $this->onlineRequest($this->url, ['ident'=>$maker_id],false);
    $answer = $this->xmlToArray($xml);
    
    if(!isset($answer['detail'])){return [];}      
    
    if(isset($answer['detail']['uid'])){      //Такое бывает когда запись одна - массив приходит не вложенный
      $answer['detail'] = [$answer['detail']];
    }
    
    PartRecord::deleteAll(['provider'=>$this->_CLSID,'search_articul'=>$part_id]);
    foreach ($answer['detail'] as $part){
      $item = $this->_dataToStruct($part,['search_articul'=>$part_id,'maker_id'=>$maker_id,'lot_quantity'=>1]);      
      $part_model = new PartRecord();
      $part_model->setAttributes($item,false);
      $part_model->save();
    }    
    $cond = PartRecord::getCollection()->buildCondition(["AND",
              ["provider" => $this->_CLSID] ,
              ["search_articul"  => strval($part_id)]              
    ]);
    return PartRecord::getPartsForOnlineProvider($cond);
  }
  
  public function getMakerList($part_id="",$cross=false){
    $param = ['sm'=>'1','code'=>$part_id];
    $xml  = $this->onlineRequest($this->url, $param, false);
    $answer = $this->xmlToArray($xml);
    return $this->convertMakersAnswerToStandart($answer);
  }
  
  /**
   * Преобразовываем полученный массив данных в стандартный массив обмена
   * @param mixed $data
   * @return mixed
   */
  protected function convertMakersAnswerToStandart($data){    
    if(!$data || !isset($data['detail'])){
      return [];
    }
    $result = [];    
    $clsid = $this->getCLSID();
    if(isset($data['detail']['ident'])){        //Такое бывает когда запись одна - массив приходит не вложенный
      $data['detail'] = [$data['detail']];
    }
    foreach ($data['detail'] as $value) {      
      $name     = (isset($value['producer']))?$value['producer']:"";      
      $id       = (isset($value['ident']))?$value['ident']:"";      
      $result[$name] = [$clsid => $id];
    }
    return $result;    
  }
  
  protected function _stdDataStruct(){
    return [
        "search_articul" => "search_articul",
			  "provider"    => 0,
			  "articul"     => "code",
			  "producer"    => "producer",
			  "maker_id"    => 0,
			  "name"        => "caption",
			  "price"       => "price",
			  "shiping"     => "deliverydays",
			  "stock"       => "stock",  
			  "info"        => "stockinfo",
			  "update_time" => 0,
			  "is_original" => "analog",
			  "count"       => "rest",
			  "lot_quantity"=> 0
    ];
  }
  
}
