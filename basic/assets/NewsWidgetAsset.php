<?php

/**
 * Description of BootboxAsset
 * @author Sync<atc58.ru>
 */

namespace app\assets;

use yii\web\AssetBundle;

class NewsWidgetAsset extends AssetBundle{
  //public vars
  public $basePath = '@webroot';
  public $baseUrl = '@web';
  public $css = [
        'css/carousel.css',
  ];
  public $js = [
        'js/carousel.js',
  ];
  public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
  //protected vars
  //private vars  
  //============================= Public =======================================  
  //============================= Protected ====================================
  //============================= Private ======================================
  //============================= Constructor - Destructor =====================
}
