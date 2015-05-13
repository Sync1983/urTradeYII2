<?php

/**
 * Description of BootboxAsset
 * @author Sync<atc58.ru>
 */

namespace app\assets;

use Yii;
use yii\web\AssetBundle;

class SearchTableAsset extends AssetBundle{
  //public vars
  public $basePath = '@webroot';
  public $baseUrl = '@web';
  
  public $css = [
        'css/dataTables.css',
  ];
  public $js = [
        'js/jquery.dataTables.js',
		'js/search_tables_init.js'
  ];
  public $depends = [
		'yii\web\JqueryAsset',
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
  //protected vars
  //private vars  
  //============================= Public =======================================
  public static function initCollapse() {
	Yii::$app->view->registerJs("window.initCollapse();");
  }
  //============================= Protected ====================================
  //============================= Private ======================================
  //============================= Constructor - Destructor =====================
}
