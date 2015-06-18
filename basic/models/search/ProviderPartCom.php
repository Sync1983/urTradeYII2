<?php
/**
 * Description of ProviderPartCom
 *
 * @author Sync
 */
namespace app\models\search;

use app\models\search\SearchProviderBase;

class ProviderPartCom extends SearchProviderBase{
  const CLSID = 6;
  const Name  = "PartCom";
  protected $url = "http://www.part-kom.ru/engine/api/v1/"; 
  protected $_maker_list_id = 'list';
  protected $_maker_name    = 'name';
  protected $_maker_id      = 'id';
  protected $_part_list_id  = 'detail';
  
  public function __construct($default_params=[], $config=[]) {
    parent::__construct(self::Name, self::CLSID, $default_params, $config);
  }
  
  protected function sendMakerRequest($part_id = "", $cross = false){
    $param  = ['number'=>$part_id];
    $json   = $this->onlineRequest($this->url."search/brands", $param, false);
    $answer = $this->jsonToArray($json);    
    return [$this->_maker_list_id => $answer];
  }

  protected function sendPartRequest($part_id = "", $maker_id = "", $cross = false) {
    $param  = ['number'=>$part_id,'maker_id'=>$maker_id,'find_substitutes'=>$cross?"on":""];
    $json   = $this->onlineRequest($this->url."search/parts", $param, false);
    $answer = $this->jsonToArray($json);
    return [$this->_part_list_id => $answer];
  }

  protected function onlineRequestHeaders($ch) {
    $headers = [
      "Authorization: Basic ".  base64_encode($this->_default_params['Login']. ":" .$this->_default_params['Password']),
      "Accept: application/json",
      "Content-type: application/json'"
    ];
    curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
  }

  protected function _dataToStruct($data=[],$add_fields=[]){
    $result = parent::_dataToStruct($data,$add_fields);

    $result['price'] *= 1.0;
    $result['articul'] = self::_clearStr($result['articul']);    
    $result['is_original'] = ($result['is_original']==="Original")?1:0;

    return $result;
  }

  protected function _stdDataStruct(){
    return [
        "search_articul" => "search_articul",
			  "provider"    => 0,
			  "articul"     => "number",
			  "producer"    => "maker",
			  "maker_id"    => "makerId",
			  "name"        => "description",
			  "price"       => "price",
			  "shiping"     => "warrantedDeliveryDays",
			  "stock"       => "providerDescription",
			  "info"        => "",//stockinfo",
			  "update_time" => "lastUpdateDate",
			  "is_original" => "analog",
			  "count"       => "quantity",
			  "lot_quantity"=> "minQuantity"
    ];
  }

  /*["number"]=>string(5) "RUL08"
    ["maker"]=>string(10) "JAPANPARTS"
    ["makerId"]=>string(3) "263"
    ["description"]=>string(39) "Втулка стабилизатора"
    ["providerId"]=>int(360)
      ["providerDescription"]=>string(17) "МСК склад"
      ["minQuantity"]=>int(1)
      ["storehouse"]=>bool(false)
    ["minDeliveryDays"]=>int(1)
    ["averageDeliveryDays"]=>int(1)
    ["maxDeliveryDays"]=>int(4)
    ["warrantedDeliveryDays"]=>int(4)
    ["lastUpdateDate"]=>string(19) "2015-06-11 06:45:45"
    ["statProvider"]=>int(90)
    ["price"]=>int(547)
    ["quantity"]=>string(1) "4"
    ["detailGroup"]=>string(22) "ReplacementNonOriginal"
    ["group"]=>string(3) "263"
    ["lastOrderDate"]=>string(0) ""
    ["statSuccessCount"]=>int(0)
    ["statRefusalCount"]=>int(0)
    ["statTotalOrderCount"]=>int(0)
*/
}
