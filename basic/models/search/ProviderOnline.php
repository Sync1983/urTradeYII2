<?php

/**
 * Description of ProviderOnline
 *
 * @author Sync<atc58.ru>
 */
namespace app\models\search;
use app\models\search\SearchProviderBase;

class ProviderOnline extends SearchProviderBase{
  const CLSID = 001;
  const Name  = "Online";
  protected $url = "http://onlinezakaz.ru/xmlprice.php"; 


  public function __construct($default_params=[], $config=[]) {
    parent::__construct(self::Name, self::CLSID, $default_params, $config);
  }
  
  public function getMakerList($part_id="",$cross=false){
    $param = ['sm'=>'1','code'=>$part_id];
    $xml  = $this->onlineRequest($this->url, $param, false);
    try {
      $answer = $this->xmlToArray($xml);      
    } catch (Exception $exc) {
      var_dump($exc);
      Yii::error($exc);
    }     
    return $this->convertMakersAnswerToStandart($answer);
  }
  
  /**
   * Преобразовываем полученный массив данных в стандартный массив обмена
   * @param mixed $data
   * @return mixed
   */
  protected function convertMakersAnswerToStandart($data){
    if(!$data || !isset($data['detail'])){
      return [];
    }
    $result = [];    
    $clsid = $this->getCLSID();
    foreach ($data['detail'] as $key => $value) {
      $name     = (isset($value['producer']))?$value['producer']:"";
      //$pard_id  = (isset($value['article']))?$value['article']:"";
      $id       = (isset($value['ident']))?$value['ident']:"";      
      $result[$name] = [$clsid => $id];
    }
    return $result;    
  }
  
}
