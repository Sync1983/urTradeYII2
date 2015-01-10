<?php
/**
 * Description of ProviderForum
 *
 * @author Sync<atc58.ru>
 */
namespace app\models\search;
use app\models\search\SearchProviderFile;

class ProviderForum extends SearchProviderFile{
  const CLSID = 003;  
  const name = "Forum";
  protected $skip_lines = 2;
  protected $convert_string = true;

  public function __construct($default_params, $config) {
    parent::__construct(self::name, self::CLSID, $default_params, $config);
  }
  
  protected function _csvLine(){
    //Брэнд; Код производителя; Наименование; Модель; Цена; Валюта; Наличие; Кратность; Код;
    return ["Maker","Articul","Name","model","Price","cur","Count","Quantity","code","null"];
  }
  
  protected function _dataToStruct($data=[]){
    $result = parent::_dataToStruct($data);
    $result['maker_id'] = md5($result['producer']);
    $result['shiping'] = 0;
    $result['is_original'] = 1;
    $result['stock'] = $this->getName();    
    $result['price'] *= 1; 
    if(isset($data['model'])){
      $result['name'] .= " ".$data['model'];
    }
    $result['articul'] = $this->_clearStr($result['articul']);
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
			  "lot_quantity"=> "Quantity"
    ];
  }
}
