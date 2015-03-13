<?php

/**
 * Description of BootboxAsset
 * @author Sync<atc58.ru>
 */

namespace app\assets;

use Yii;
use yii\web\AssetBundle;

class NotifyAsset extends AssetBundle{
  //public vars  
  public $basePath = '@webroot';
  public $baseUrl = '@web';
  public $css = [
        'css/bootstrap-notify.css',
        'css/alert-bangtidy.css',        
    ];
    public $js = [        
        'js/bootstrap-notify.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
  //protected vars
  //private vars  
  //============================= Public =======================================
  /*public static function overrideSystemConfirm() {
        Yii::$app->view->registerJs('
            yii.confirm = function(message, ok, cancel) {
                bootbox.confirm(message, function(result) {
                    if (result) { !ok || ok(); } else { !cancel || cancel(); }
                });
            }
        ');
    }*/
  //============================= Protected ====================================
  //============================= Private ======================================
  //============================= Constructor - Destructor =====================
}
