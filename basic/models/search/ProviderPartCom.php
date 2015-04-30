<?php
/**
 * Description of ProviderPartCom
 *
 * @author Sync
 */
namespace app\models\search;

use app\models\search\SearchProviderBase;

class ProviderPartCom extends SearchProviderBase{
  const CLSID = 006;
  const Name  = "PartCom";
  protected $url = "http://www.part-kom.ru/engine/api/v1/"; 
  protected $_maker_list_id = 'detail';
  protected $_maker_name    = 'producer';
  protected $_maker_id      = 'ident';
  protected $_part_list_id  = 'detail';
  
  public function __construct($default_params=[], $config=[]) {
    parent::__construct(self::Name, self::CLSID, $default_params, $config);
  }
  
  protected function sendMakerRequest($part_id = "", $cross = false){
    $param = ['number'=>$part_id];
    $xml  = $this->onlineRequest($this->url."search/brands", $param, false);
	var_dump($xml);
    //$answer = $this->xmlToArray($xml);
    //return $answer;
  }
  
  protected function onlineRequestHeaders($ch) {
	$headers = [
	  "Authorization: Basic ".  base64_encode($this->_default_params['Login']. ":" .$this->_default_params['Password']),			
	  "Accept: application/json",
	  "Content-type: application/json'"
	];
	curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
  }
}
