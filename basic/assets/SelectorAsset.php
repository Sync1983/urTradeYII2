<?php

/**
 * Description of BootboxAsset
 * @author Sync<atc58.ru>
 */

namespace app\assets;

use Yii;
use yii\web\AssetBundle;

class SelectorAsset extends AssetBundle{
  //public vars
  public $sourcePath = '@vendor/bower/bootstrap-select/dist';
  public $css = [
        '/css/bootstrap-select.css',
  ];
  public $js = [
        '/js/bootstrap-select.js',
  ];
  public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
  //protected vars
  //private vars  
  //============================= Public =======================================
  public static function registerJS() {
        Yii::$app->view->registerJs("$('.selectpicker').selectpicker({
              style: 'btn btn-info',
              size: 4              
              });");
  }
  //============================= Protected ====================================
  //============================= Private ======================================
  //============================= Constructor - Destructor =====================
}
