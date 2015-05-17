<?php

/**
 * @author Sync
 */

namespace app\components\helpers;
use yii\helpers\ArrayHelper;

class YaMHelper {
  
  protected static $_percent_by_type = [
    'PC'  => 0.030,
    'AC'  => 0.035,
    'WM'  => 0.035,
    'AB'  => 0.035,
    'GP'  => 0.035,
    'MA'  => 0.035
  ];

  /**
   * Возвращает возможные типы платежей
   * @return array
   */
  public static function getPayTypes(){
    return array_keys(self::$_percent_by_type);
  }
  /**
   * Возвращает процент наценки по типу операции
   * @param type $type
   * @return type
   */
  public static function getPercent($type){
    return round(ArrayHelper::getValue(self::$_percent_by_type, $type, 0) * 100, 2);
  }
  /**
   * Прибавляет к указанному значению процент, в зависиомсти от типа операции
   * @param type $type
   * @param float $value
   * @return type
   */
  public static function addPercent($type, $value){
    $percent = 1.0 - ArrayHelper::getValue(self::$_percent_by_type, $type, 0);    
    return round($value/$percent,2);
  }
  /**
   * Уменьшает указанное значение на процент типа операции
   * @param type $type
   * @param float $value
   * @return type
   */
  public static function decPercent($type, $value){
    $percent = 1.0 - ArrayHelper::getValue(self::$_percent_by_type, $type, 0);
    return round($value*(1-$percent),2);
  }

  public static function getJSObject(){
    return json_encode(self::$_percent_by_type);
  }
  
}
