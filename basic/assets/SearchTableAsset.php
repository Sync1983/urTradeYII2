<?php

/**
 * Description of BootboxAsset
 * @author Sync<atc58.ru>
 */

namespace app\assets;

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
    $js_text = <<<JS
      $("body").find("a.collapse-toggle").each(
        function( index, item ){
          $(item).click(
            function( event ){
              if ( $(this).attr("aria-expanded") === "true" ) {
                return;
              }
              var head = $(this).parent().parent().parent();
              var dataSource = head.find('script[type="text/json"]').text();
              var dataObject = JSON.parse(dataSource);
              main.loadParts(dataObject,head);
            }
          );
        }
      );
JS;
    \yii::$app->view->registerJs($js_text);
  }
  //============================= Protected ====================================
  //============================= Private ======================================
  //============================= Constructor - Destructor =====================
}
