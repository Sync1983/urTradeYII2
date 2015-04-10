<?php

/**
 * Description of ProviderOnline
 *
 * @author Sync<atc58.ru>
 */
namespace app\models\search;
use yii;
use app\models\search\SearchProviderBase;
use app\models\PartRecord;

class ProviderIxora extends SearchProviderBase{
  const CLSID = 004;
  const Name  = "Ixora";
  protected $url = "http://ws.auto-iksora.ru:83/searchdetails/searchdetails.asmx"; 


  public function __construct($default_params=[], $config=[]) {
    parent::__construct(self::Name, self::CLSID, $default_params, $config);
  }
  
  public function getPartList($part_id="",$maker_id="",$cross=false){
    $xml = $this->onlineRequest($this->url."/FindDetailsXML", ['MakerID'=>$maker_id,'DetailNumber'=>$part_id]);
    $answer = $this->xmlToArray($xml);
    if(!isset($answer['row'])){return [];}    
    if(isset($answer['row']['detailnumber'])) {
     $answer['row'] = [$answer['row']];
    } 
    $uid = yii::$app->user->getId();
    PartRecord::deleteAll(['provider'=>$this->_CLSID,'search_articul'=>$part_id,'for_user'=>$uid]);
    foreach ($answer['row'] as $part){
      $item = $this->_dataToStruct($part,['search_articul'=>$part_id,'maker_id'=>$maker_id]);      
      $part_model = new PartRecord();
      $part_model->setAttribute("for_user", $uid);
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
    $param = ['DetailNumber'=>$part_id];
    $xml  = $this->onlineRequest($this->url."/GetMakersByDetailNubmerXML", $param);    
    $answer = $this->xmlToArray($xml);
    return $this->convertMakersAnswerToStandart($answer);    
  }
  
  /**
   * Преобразовываем полученный массив данных в стандартный массив обмена
   * @param mixed $data
   * @return mixed
   */
  protected function convertMakersAnswerToStandart($data){    
    if(!$data || !isset($data['row'])){
      return [];
    }
    $result = [];    
    $clsid = $this->getCLSID();    
    if(isset($data['row']['id'])){        //Такое бывает когда запись одна - массив приходит не вложенный
      $data['row'] = [$data['row']];
    }
    foreach ($data['row'] as $value) {      
      $name     = (isset($value['name']))?$value['name']:"";      
      $id       = (isset($value['id']))?$value['id']:"";      
      $result[$name] = [$clsid => $id];
    }
    return $result;    
  }
  
  protected function _stdDataStruct(){
    return [
			  "search_articul" => "search_articul",
			  "provider"    => 0,
			  "articul"     => "detailnumber",
			  "producer"    => "maker_name",
			  "maker_id"    => "maker_id",
			  "name"        => "detailname",
			  "price"       => "price",
			  "shiping"     => "dayswarranty",
			  "stock"       => "regionname",  
			  "info"        => 0,
			  "update_time" => "pricedate",
			  "is_original" => "groupid",
			  "count"       => "quantity",
			  "lot_quantity"=> "lotquantity"
    ];
  }
  
}
