<?php

/**
 * Description of BootboxAsset
 * @author Sync<atc58.ru>
 */

namespace app\assets;

use Yii;
use yii\web\AssetBundle;

class AdminBootstrapAsset extends AssetBundle{
  //public vars
  public $sourcePath = '@bower/bootstrap';
  public $js = [
          '/dist/js/bootstrap.min.js',        
          '/js/dropdown.js',        
  ];
  //protected vars
  //private vars  
  //============================= Public =======================================  
  //============================= Protected ====================================
  //============================= Private ======================================
  //============================= Constructor - Destructor =====================
}
