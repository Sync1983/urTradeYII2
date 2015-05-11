<?php

/**
 * @author Sync
 */

namespace app\components\helpers;
use yii\helpers\ArrayHelper;
use app\models\SocAuth;
use app\components\behaviors\socnet\VkBehavior;
use app\components\behaviors\socnet\FbBehavior;

class SocNetHelper {
  
  protected static $_net_by_name = [
    'vk'      => [
	  'class'	=> '\app\components\behaviors\socnet\VkBehavior',
	  'name'	=> 'Vkontakte'
	],		  
    'fb'      => [
	  'class'	=> '\app\components\behaviors\socnet\FbBehavior',
	  'name'	=> 'Facebook'
	],
  ];
  /**
   * Возвращает имя соц.сети
   * @param string $soc_net Ключ соц.сети
   * @return strign
   */
  public static function getNetName($soc_net){
	$net = ArrayHelper::getValue(self::$_net_by_name, $soc_net, false);
    return ArrayHelper::getValue($net, 'name', false);
  }
  /**
   * Возвращает название класса поведения для указанной соц.сети
   * @param string $soc_net Ключ соц.сети
   * @return string
   */
  public static function getClassByNet($soc_net){    
	$net = ArrayHelper::getValue(self::$_net_by_name, $soc_net, false);
    return ArrayHelper::getValue($net, 'class', false);
  }
  /**
   * Возвращает список принципиально доступных для авторизации сетей
   * @return type
   */
  public static function getAvaibleNets(){
    return array_keys(self::$_net_by_name);
  }
  /**
   * Возвращает список сетей, в которых пользователь уже зарегистрирован
   * @return array of string
   */
  public static function getActiveNets(){
    if( \yii::$app->user->isGuest ){
      return [];
    }
    $items = SocAuth::find()->where(['user_id'=>\yii::$app->user->identity->getObjectId()])->all();
	return ArrayHelper::getColumn($items, 'net', false);
  }
  
  
}
