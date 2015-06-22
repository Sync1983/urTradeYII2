<?php

/**
 * Description of ProviderOnline
 *
 * @author Sync<atc58.ru>
 */
namespace app\models\search;

use app\models\search\SearchProviderBase;

class ProviderMoskovia extends SearchProviderBase{
  const CLSID = 8;
  const Name  = "Moskovia";
  protected $url = "http://portal.moskvorechie.ru/portal.api";
  protected $_maker_list_id = 'result';
  protected $_maker_name    = 'brand';
  protected $_maker_id      = 'nr';
  protected $_part_list_id  = 'result';

  public function __construct($default_params=[], $config=[]) {    
    parent::__construct(self::Name, self::CLSID, $default_params, $config);
  }
  /**
   * @see SearchProviderBase 
   */
  protected function sendPartRequest($part_id="",$maker_id="",$cross=false){    
    $params = explode("@@", $maker_id);
    $id = $params[0];
    $name = $params[1];

    $param = [
      'act' => 'price_by_nr_firm',
      'nr'  => $id,
      'f'   => $name,
      'avail' => 1,
    ];

    if( $cross ){
      $param['alt'] = 1;
      $param['oe'] = 1;
    }
    
    $json = $this->onlineRequest($this->url, $param);
    $answer = $this->jsonToArray($json);
    
    return $answer;
  }
  
  protected function sendMakerRequest($part_id = "", $cross = false){
    $param = [
      'act' => 'brand_by_nr',
      'nr'=>$part_id];
    if( $cross ){
      $param['alt'] = 1;
      $param['oe'] = 1;
    }
    $json = $this->onlineRequest($this->url, $param);    
    $answer = $this->jsonToArray($json);    
    return $answer;
  }

  protected function convertMakersAnswerToStandart($data){
    if(!$data || !isset($data[ $this->_maker_list_id ]) ){
      return [];
    }
    $result = [];
    $clsid = $this->getCLSID();
    foreach ($data[ $this->_maker_list_id ] as $value) {
      $name     = (isset($value[ $this->_maker_name ]))?$value[ $this->_maker_name ]  : false;
      $id       = (isset($value[ $this->_maker_id ]))?  $value[ $this->_maker_id ]	  : false;
      if( $name && $id ) {
        $std_name = $this->convertNameToStandart($name);
        $result[$std_name] = [$clsid => $id . "@@" . $name];
      }
    }
    return $result;
  }
  
  protected function _stdDataStruct(){
    return [
        "search_articul" => "search_articul",
			  "provider"    => 0,
			  "articul"     => "nr",
			  "producer"    => "brand",
			  "maker_id"    => 0,
			  "name"        => "name",
			  "price"       => "price",
			  "shiping"     => "delivery",
			  "stock"       => "",  
			  "info"        => 0,
			  "update_time" => 0,
			  "is_original" => 0,
			  "count"       => "stock",
			  "lot_quantity"=> "minq"
    ];
  }

  /*
   *{"result":
   * [
   *  {
   *    "nr":"86614-1R000",
   *    "brand":"Hyundai",
   *    "name":"Кронштейн бампера HYUNDAI SOLARIS 10- зад.прав.седан",
   *    "stock":"-",
   *    "delivery":"не известно",
   *    "minq":"1",
   *    "upd":"18.06.15 12:03",
   *    "price":"325.35",
   *    "currency":"руб."
   *  }
   * ]
   *}
   */
  
}
