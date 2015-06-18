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
  protected $url = "http://ws.auto-iksora.ru:83/searchdetails/searchdetails.asmx"; 
  protected $_maker_list_id = 'row';
  protected $_maker_name    = 'name';
  protected $_maker_id      = 'id';
  protected $_part_list_id  = 'row';

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
    $xml = $this->onlineRequest($this->url."/FindDetailsXML", ['MakerID'=>$maker_id,'DetailNumber'=>$part_id]);
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
    $param = ['DetailNumber'=>$part_id];
    $xml  = $this->onlineRequest($this->url."/GetMakersByDetailNubmerXML", $param);    
    $answer = $this->xmlToArray($xml);
    return $answer;
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
