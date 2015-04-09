<?php
/**
 * Description of ProviderController
 *
 * @author Sync<atc58.ru>
 */
namespace app\commands;
use yii;
use yii\console\Controller;
use app\models\search\SearchProviderFile;
use app\models\PartRecord;

class ProviderController extends Controller{
  public $_providers = [];
  
  public function actionTest(){
    $collection = PartRecord::getCollection();    
    $res2 = PartRecord::find()->where(['articul'=>"123300"])->all();
    $maker_list = [];
    foreach ($res2 as $part) {
      $maker_list[$part->getAttribute("maker_id")] = $part->getAttribute("producer");
    }
    var_dump($maker_list);    
  }

  public function actionLoadPrices(){
    $app = Yii::$app;    
    if(!isset($app->params['providerUse'])){
      return;
    }
    $param = $app->params['providerUse'];
    $default_data = $app->params['providers'];
    if(!is_array($param)){
      return;
    }
    foreach ($param as $provider){
      $default = [];
      if(isset($default_data[$provider])){
        $default = $default_data[$provider];
      }      
      $class = yii::createObject($provider,[$default,[]]);
      if( $class instanceof SearchProviderFile){
       $this->_providers[$class->getCLSID()] = $class;
      }
    }
    $this->_getLastFile();
  }
  
  protected function _getLastFile(){
    /** @var $provider SearchProviderFile */
    foreach ($this->_providers as $provider){      
      $file_info = $provider->getLastFileNameDate();
      \yii::info("Loading ".$file_info['path'],'console');
      \yii::info("File date: ".date("d:m:Y H-i-s", $file_info['time']),'console');
      echo "Loading ".$file_info['path']."\r\n";      
      echo "File date: ".date("d:m:Y H-i-s", $file_info['time'])."\r\n";
      $provider->clearAll();
      $provider->loadFile($file_info['path']);      
    }
  }
  
}
