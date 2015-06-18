<?php
/**
 * Description of ProviderArmtek
 *
 * @author Sync<atc58.ru>
 */
namespace app\models\search;
use app\models\search\SearchProviderFile;

class ProviderArmtek extends SearchProviderFile{
  const CLSID = 2;  
  const name = "Armtek";
  protected $skip_lines = 1;
  protected $convert_string = true;
  protected $divider = "\t";


  public function __construct($default_params, $config) {
    parent::__construct(self::name, self::CLSID, $default_params, $config);
  }
  
  protected function _csvLine(){
    return ["Maker","Articul","Name","brand_artcul","armtek_code","Count","Price"];
  }
  
  protected function _dataToStruct($data=[],$add_fields=[]){
    $result = parent::_dataToStruct($data,[
      'shiping'       => 0,
      'is_original'   => 1,
      'lot_quantity'  => 1,
      'stock'         => $this->getName()
    ]);
	$result['articul'] = self::_clearStr($result['articul']);
    $result['search_articul'] = $result['articul'];
    $result['maker_id'] = md5($result['producer']);
    $result['price'] *= 1.0; 
    return $result;
  }

  protected function _stdDataStruct(){
    return [        
			  "provider"    => 0,
			  "articul"     => "Articul",
			  "producer"    => "Maker",
			  "maker_id"    => 0,
			  "name"        => "Name",
			  "price"       => "Price",
			  "shiping"     => 0,
			  "stock"       => 0,  
			  "info"        => 0,
			  "update_time" => 0,
			  "is_original" => 0,
			  "count"       => "Count",
			  "lot_quantity"=> 0
    ];
  }
}
