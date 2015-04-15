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

class ProviderOnline extends SearchProviderBase{
  const CLSID = 001;
  const Name  = "Online";
  protected $url = "http://onlinezakaz.ru/xmlprice.php"; 
  protected $_maker_list_id = 'detail';
  protected $_maker_name    = 'producer';
  protected $_maker_id      = 'ident';
  protected $_part_list_id  = 'detail';

  public function __construct($default_params=[], $config=[]) {
    parent::__construct(self::Name, self::CLSID, $default_params, $config);
  }
  /**
   * @see SearchProviderBase 
   */
  protected function sendPartRequest($part_id="",$maker_id="",$cross=false){
    $xml = $this->onlineRequest($this->url, ['ident'=>$maker_id],false);
    $answer = $this->xmlToArray($xml);    
    return $answer;
  }
  
  protected function sendMakerRequest($part_id = "", $cross = false){
    $param = ['sm'=>'1','code'=>$part_id];
    $xml  = $this->onlineRequest($this->url, $param, false);
    $answer = $this->xmlToArray($xml);
    return $answer;
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
