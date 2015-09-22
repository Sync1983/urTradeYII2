<?php

/**
 * Description of ProviderOnline
 *
 * @author Sync<atc58.ru>
 */
namespace app\models\search;

use app\models\search\SearchProviderBase;

class ProviderIxora extends SearchProviderBase{
  const CLSID = 4;
  const Name  = "Ixora";
  //protected $url = "http://ws.auto-iksora.ru:83/searchdetails/searchdetails.asmx"; 
  protected $url = "http://ws.ixora-auto.ru/soap/ApiService.asmx"; 
  protected $_maker_list_id = 'MakerInfo';
  protected $_maker_name    = 'name';
  protected $_maker_id      = 'name';
  protected $_part_list_id  = 'DetailInfo';

  public function __construct($default_params=[], $config=[]) {
    parent::__construct(self::Name, self::CLSID, $default_params, $config);
  }
  /**
   * @see SearchProviderBase
   * @param type $part_id
   * @param type $maker_id
   * @param type $cross
   */
  protected function sendPartRequest($part_id = "", $maker_id = "", $cross = false){
    $xml = $this->onlineRequest($this->url."/FindXML", ['Maker'=>$maker_id,'Number'=>$part_id,'StockOnly'=>'false','SubstFilter'=>($cross?'All':'Originals')]);
    $answer = $this->xmlToArray($xml);
    return $answer;
  }
  /**
   * @see SearchProviderBase
   * @param type $part_id
   * @param type $cross
   * @return type
   */
  protected function sendMakerRequest($part_id = "", $cross = false){
    $param = ['Number'=>$part_id];
    $xml  = $this->onlineRequest($this->url."/GetMakersXML", $param);
    $answer = $this->xmlToArray($xml);
    return $answer;
  }

  protected function _dataToStruct($data=[],$add_fields=[]){
    $add_fields['is_original'] = ($data['group']==='Original');
    return parent::_dataToStruct($data,$add_fields);
  }
  
  protected function _stdDataStruct(){
    return [
			  "search_articul" => "search_articul",
			  "provider"    => 0,
			  "articul"     => "number",
			  "producer"    => "maker",
			  "maker_id"    => "maker_id",
			  "name"        => "name",
			  "price"       => "price",
			  "shiping"     => "dayswarranty",
			  "stock"       => "region",  
			  "info"        => 0,
			  "update_time" => "date",
			  "is_original" => "original",
			  "count"       => "quantity",
			  "lot_quantity"=> "lotquantity"
    ];
  }
  
}
