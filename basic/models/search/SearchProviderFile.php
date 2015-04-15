<?php
/**
 * Description of SearchProviderFile
 *
 * @author Sync<atc58.ru>
 */
namespace app\models\search;
use app\models\search\SearchProviderBase;
use app\models\PartRecord;

class SearchProviderFile extends SearchProviderBase{
  protected $skip_lines = 0;
  protected $convert_string = false;
  protected $divider = ';';
  
  private $_field_array;
  private $_data_array;
  
  public function getPartList($part_id = "", $maker_id = "", $cross = false) {
    $collection = PartRecord::getCollection();    
    $cond = $collection->buildCondition(["AND",
              ["provider" => $this->_CLSID] ,
              ["articul"  => strval($part_id)],
              ["maker_id" => strval($maker_id)]
    ]);    
    $answer = PartRecord::find()->where($cond)->orderBy(["price"=>SORT_ASC,"shiping"=>SORT_ASC])->limit(20)->asArray()->all();    
    return $answer;
  }

  public function getMakerList($part_id = "", $cross = false) {
    $search = PartRecord::find()->where(['articul'=>$part_id,'provider'=>  $this->_CLSID])->all();
    $maker_list = [];
    foreach ($search as $part) {
      $maker_list[$part->getAttribute("producer")]=[$this->_CLSID => $part->getAttribute("maker_id")];
    }
    return $maker_list;
  }  
  //================ Общие методы для файлов ==============================
  /**
   * Возвращает [имя,дату] последнего добавленного файла
   * @return mixed
   * @throws \BadMethodCallException
   */
  public function getLastFileNameDate(){
    $dir = $this->getDir();
    if(!is_dir($dir)){
      throw new \BadMethodCallException("Путь к директории прайс-листов не является фактической директорией");     
    }
    $do = scandir($dir);
    if(!$do){
      throw new \BadMethodCallException("Путь к директории прайс-листов не является фактической директорией");     
    }
    $max_file_time = 0;
    $file_path = "";
    foreach($do as $file){
      if(($file ==".")||($file=="..")){
        continue;
      }
      $file_time = filemtime($dir ."/". $file);
      if($max_file_time<=$file_time){
        $max_file_time = $file_time;
        $file_path = $dir ."/". $file;
      }
    }
    if(($file_path=="")||($max_file_time==0)){
      //throw new \BadMethodCallException("В указанной директории не обнаружено прайс-листов");
	  return ['path'=>$dir,'time'=>0];
    }
    return ['path'=>$file_path,'time'=>$max_file_time];
  }
  /**
   * Возвращает путь к каталогу файлов прайс-листов
   * @return mixed
   */
  public function getDir(){
    return __DIR__."/../../../".$this->_default_params['dir']."";
  }  
  /**
   * ПОЛНОСТЬЮ очищает из базы запчастей все данные от указанного производителя
   * @return boolean
   */
  public function clearAll(){
    return $this->_clearAll();
  }
  /**
   * Читает файл по строкам и отправляет строки на подготовку
   * и преобразование в структуру записи БД
   * @param string $file
   * @throws \BadMethodCallException
   */
  public function loadFile($file){
    $new_file = $this->_unzipFile($file);
    
    $fh = fopen($new_file, "r");
    if(!$fh){
      throw new \BadMethodCallException("Невозможно открыть файл");
    }
    $this->_field_array = $this->_csvLine();
    $this->_data_array  = $this->_stdDataStruct();
    for($i=0;$i<$this->skip_lines;$i++){
      if(!feof($fh)){
        echo "skip ".$this->_loadLine($fh)."\r\n";
      }
    }  
    $count = 0;
    $time = time();
    while(!feof($fh))
    {
      $str = $this->_loadLine($fh);      
      $data = $this->_convertLine($str);      
      $res = $this->_dataToStruct($data);
      $this->_saveItem($res);      
      $count++;
    }    
    echo "Add $count items  by  ".(time()-$time)." sec.\r\n";
    fclose($fh);
  }  
  /**
   * Преобразует строку файла в массив, где ключу из _csvLine сопоставлено
   * соответствующее значение из строки
   * В случае несовпадения длин массивов возвращает false
   * @param string $line
   * @return mixed
   */
  protected function _convertLine($line){
    $parts = explode($this->divider, $line);    
    if(count($parts)!=count($this->_field_array)){
      echo "Не совпадает количество полей в файле и в массиве полей (".count($parts)."-".count($this->_field_array).")\r\n";
      return [];
    }
    $result = [];
    foreach ($this->_field_array as $key => $name) {
      $result[$name] = $parts[$key];
    }
    return $result;
  }
  /**
   * Читает строку из файла и возвращает её,
   * либо при conver_string==true преобразовывает строку из
   * Win-1251 в utf-8
   * @param resource $file_descriptor
   * @return string
   */
  protected function _loadLine($file_descriptor){
    $str = fgets($file_descriptor);
    if(!$this->convert_string){
      return $str;
    }
    $str_res = mb_convert_encoding($str,"utf-8","Windows-1251");
    return $str_res;
  }
  /**
   * Проверяет не является ли файл архивом
   * и возвращает либо исходное имя файла
   * либо разархивирует файл и сохраняет его с расширением
   * .csv и возвращает путь к новому файлу
   * @param string $file
   * @return string
   * @throws \BadMethodCallException
   */
  protected function _unzipFile($file){
    if(strpos($file,".zip")===false){
      return $file;
    }    
    $new_filename = str_replace(".zip", ".csv", $file);
    $command = "unzip -p '$file' > '$new_filename'";
    echo "Unzip command: $command \r\n";
    $result = 0;
    $output = "";
    exec($command,$output,$result);
    if($result!==0){
      throw new \BadMethodCallException("Ошибка разархивации файла: $output");
    }
    return $new_filename;
  }
  /**
   * Проверяет не является ли файл архивом
   * и возвращает либо исходное имя файла
   * либо разархивирует файл и сохраняет его с расширением
   * .csv и возвращает путь к новому файлу
   * @param string $file
   * @return string
   * @throws \BadMethodCallException
   */
  protected function _unrarFile($file){
    if(strpos($file,".rar")===false){
      return $file;
    }    
	
    $new_filename = str_replace(".rar", ".csv", $file);
	
    $command = "unrar e -y $file ".dirname($file);
    echo "Unrar command: $command \r\n";
	$output = "";
	$result = 0;
    exec($command,$output,$result);
    
    if($result!==0){
      throw new \BadMethodCallException("Ошибка разархивации файла: ".json_encode($output));
    }
    return $new_filename;
  }
  /**
   * Возвращает последовательность столбцов csv-файла прайс-листа
   * @return array
   */
  protected function _csvLine(){
    return [];
  }  

  public function __construct($Name, $CLSID, $default_params, $config=[]) {
    parent::__construct($Name, $CLSID, $default_params, $config);
    if(!isset($default_params['dir'])){
      throw \BadMethodCallException("Класс должен содержать описание пути к директории прайсов \"dir\"");
    }
  }
}
