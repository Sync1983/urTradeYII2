<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\widgets\CarSelector\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class CarSelectorAsset extends AssetBundle
{
    public $sourcePath = __DIR__ . '/../';
    public $publishOptions = [
      'forceCopy' => true,
    ];
    public $css = [
        'css/car-selector.css',
    ];
    public $js = [
        'js/treeview.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',        
    ];
    /* @var $view \yii\web\View */
    public static function registerJS($view,$name,$data){
      $view->registerJs("$('#$name').find('span.header').on('blur',function(event){\n"
          . "  var container = $('#$name-container');\n"          
          . "  if( $('#$name').has(event.originalEvent.explicitOriginalTarget).length ) {\n"
          . "    $('#$name').find('span.header').addClass('focus');"
          . "    setTimeout(function() {\n"
          . "     $('#$name').find('span.header').focus();\n"
          . "    }, 0);\n"
          . "  } else {\n"
          . "    $('#$name').find('span.header').removeClass('focus')\n"
          . "  }\n"
          . "});\n");
      $view->registerJs("$('#$name-container').treeview({data:" . json_encode($data) . "})");
    }
}