<?php

namespace app\models\search;
use app\models\search\SearchProviderFile;
use PHPExcel_IOFactory;

class ProviderAutoEuro extends SearchProviderFile {
  const CLSID = 005;  
  const name = "AutoEuro";
  
  protected $skip_lines = 5;
  protected $convert_string = true;

  public function __construct($default_params, $config) {
    parent::__construct(self::name, self::CLSID, $default_params, $config);
  }
  
  public function loadFile($file) {
	$dir = dirname($file);				  //Уроды, что передают данные в rar и xls - чтобы у вас в жопе был такой вот костыль
	$file_name = basename($file);
	$file_name = str_replace("rar", "xls", $file_name);
	$cmd = "rm $dir/*.xls";
	$cmd1 = "mv $dir/*.xls $dir/$file_name";
	$result = 0;
	$output = "";
	exec($cmd,$output,$result);		
    $this->_unrarFile($file);
	exec($cmd1,$output,$result);
	
	$start_time = time();
	$excel = PHPExcel_IOFactory::load($dir."/".$file_name);	
	$sheet0 = $excel->getSheet(0);	
	$sheet1 = $excel->getSheet(1);	
	$cnt = $this->loadSheet0($sheet0);	
	Echo "Add $cnt lines by ".(time()-$start_time)." sec.\r\n";
	$cnt = $this->loadSheet1($sheet1);	
	Echo "Add $cnt lines by ".(time()-$start_time)." sec.\r\n";	
  }
  
  protected function loadSheet1($sheet0){
	$max_line = $sheet0->getHighestRow();
	for($col = $this->skip_lines-1; $col<$max_line; $col++){
	  $maker	= $sheet0->getCellByColumnAndRow(0,$col)->getValue();
	  $articul	= $sheet0->getCellByColumnAndRow(1,$col)->getValue();
	  $name		= $sheet0->getCellByColumnAndRow(3,$col)->getValue();
	  $shiping	= $sheet0->getCellByColumnAndRow(4,$col)->getValue();
	  $price	= $sheet0->getCellByColumnAndRow(5,$col)->getValue();
	  $count	= $sheet0->getCellByColumnAndRow(7,$col)->getValue();
	  $quantity	= $sheet0->getCellByColumnAndRow(8,$col)->getValue();
	  $part = [];
	  $part['search_articul'] = self::_clearStr($articul);
      $part['provider']		  = $this->_CLSID;
	  $part['articul']		  = $part['search_articul'];
	  $part['producer']		  = $maker;
	  $part['maker_id']		  = md5($maker);
	  $part['name']			  = $name;
	  $part['price']		  = $price * 1;
	  $part['shiping']		  = $shiping * 1;
	  $part['is_original']	  = true;
	  $part['count']		  = $count;
	  $part['lot_quantity']	  = $quantity?$quantity:1;
	  //$part['stock']		  = 
	  //$part['info']			  =
	  //$part['update_time']	  =
	  //$part['for_user']		  = \yii::$app->user
      $this->_saveItem($part);	  
	}
	return $col - $this->skip_lines;
  }
  
  protected function loadSheet0($sheet0){
	$max_line = $sheet0->getHighestRow();
	for($col = $this->skip_lines; $col<$max_line-1; $col++){
	  $maker	= $sheet0->getCellByColumnAndRow(0,$col)->getValue();
	  $articul	= $sheet0->getCellByColumnAndRow(3,$col)->getValue();
	  $name		= $sheet0->getCellByColumnAndRow(5,$col)->getValue();
	  $price	= $sheet0->getCellByColumnAndRow(6,$col)->getValue();
	  $count	= $sheet0->getCellByColumnAndRow(8,$col)->getValue();
	  $quantity	= $sheet0->getCellByColumnAndRow(9,$col)->getValue();
	  $part = [];
	  $part['search_articul'] = self::_clearStr($articul);
      $part['provider']		  = $this->_CLSID;
	  $part['articul']		  = $part['search_articul'];
	  $part['producer']		  = $maker;
	  $part['maker_id']		  = md5($maker);
	  $part['name']			  = $name;
	  $part['price']		  = $price * 1;
	  $part['shiping']		  = 0;
	  $part['is_original']	  = true;
	  $part['count']		  = $count;
	  $part['lot_quantity']	  = $quantity?$quantity:1;
	  //$part['stock']		  = 
	  //$part['info']			  =
	  //$part['update_time']	  =
	  //$part['for_user']		  = \yii::$app->user
      $this->_saveItem($part);	  
	}
	return $col - $this->skip_lines;
  }
  
}
