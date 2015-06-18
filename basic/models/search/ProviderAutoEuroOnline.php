<?php

/**
 * Description of ProviderOnline
 *
 * @author Sync<atc58.ru>
 */
namespace app\models\search;

use app\models\search\SearchProviderBase;

class ProviderAutoEuroOnline extends SearchProviderBase{
  const CLSID = 7;
  const Name  = "AutoEuroOnline";
  protected $url = "http://online.autoeuro.ru/ae_server/srv_main.php";
  protected $_maker_list_id = 'makers';
  protected $_maker_name    = 'maker';
  protected $_maker_id      = 'maker';
  protected $_part_list_id  = 'detail';

  public function __construct($default_params=[], $config=[]) {
    parent::__construct(self::Name, self::CLSID, $default_params, $config);
  }

  protected function sendPartRequest($part_id="",$maker_id="",$cross=false){
    $param = [
      'postdata' => serialize([
        'command' => [
          'proc_id' => 'Get_Element_Details',
          'parm'    => [0=>$maker_id,1=>$part_id,2=>$cross?"1":"0"]
         ],
        'auth'    => [
          'client_name' => $this->_default_params['Login'],
          'client_pwd'  => $this->_default_params['Password']
        ]
      ]),
    ];
    $param = array_map("base64_encode", $param);
    $json   = $this->onlineRequest($this->url, $param);
    
    return [$this->_part_list_id => $json];
  }
  
  protected function sendMakerRequest($part_id = "", $cross = false){

    $param = [
      'postdata' => serialize([
        'command' => [
          'proc_id' => 'Search_By_Code',
          'parm'    => [0=>$part_id,1=>1]
         ],
        'auth'    => [
          'client_name' => $this->_default_params['Login'],
          'client_pwd'  => $this->_default_params['Password']
        ]
      ]),
    ];    
    $param = array_map("base64_encode", $param);    
    $json   = $this->onlineRequest($this->url, $param);
    
    return [$this->_maker_list_id => $json];
  }

  protected function onlineRequestHeaders($ch) {
    $headers = [
      "Content-type: application/x-www-form-urlencoded",
      "Accept-Charset: windows-1251",
      "Accept:"
    ];
    curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
  }

  protected function onlineRequest($url="",$param=[],$is_post=true) {
    $ch = curl_init();
    $this->onlineRequestHeaders($ch);
    
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, $is_post==1);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);        

    $params = http_build_query($param);
    
    if( $is_post ) {
      curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    } else  {
      $url.="?".$params;
    }

    curl_setopt($ch, CURLOPT_URL, $url);
 
    $answer = curl_exec($ch);    

    if( curl_errno($ch)!==0 ){
      $answer = "[]";
    }

    curl_close($ch);

    $decode = base64_decode($answer);
    $open = unserialize($decode);
    foreach ($open as $num=>$item){
      foreach ($item as $key => $value) {
        $open[$num][$key] = mb_convert_encoding($value, "UTF-8", "Windows-1251");
      }
      //$open[$num]["id"] = $open[$num]['maker'] . "@@" . $open[$num]['code'];
    }
    return $open;
  }

  protected function _dataToStruct($data=[],$add_fields=[]){
    $result = parent::_dataToStruct($data,$add_fields);

    $result['price'] = round($result['price']*1,2);
    $result['articul'] = self::_clearStr($result['articul']);
    
    $result['is_original'] = 1 - $result['is_original'];
    $result['lot_quantity'] = $result['lot_quantity']==0?1:$result['lot_quantity'];

    $result['shiping'] = \yii\helpers\ArrayHelper::getValue($data, "order_time", "0");

    return $result;
  }
  
  protected function _stdDataStruct(){
    return [
        "search_articul" => "search_articul",
			  "provider"    => 0,
			  "articul"     => "code",
			  "producer"    => "maker",
			  "maker_id"    => 0,
			  "name"        => "name",
			  "price"       => "price",
			  "shiping"     => 0,//"order_time",
			  "stock"       => 0,
			  "info"        => 0,
			  "update_time" => 0,
			  "is_original" => "is_kross",
			  "count"       => "amount",
			  "lot_quantity"=> "packing"
    ];
  }

  /*
   ["is_kross"]=>string(1) "0"
    ["maker"]=>string(10) "MECAFILTER"
    ["code"]=>string(3) "123"
    ["name"]=>string(50) "Фильтр воздушный Citroen C3 HDI 02-"
    ["order_time"]=>string(3) "2-4"
    ["packing"]=>string(1) "0"
    ["price"]=>string(7) "413.946"
    ["amount"]=>string(1) "3"
    ["unit"]=>string(5) "шт."
   */  
}