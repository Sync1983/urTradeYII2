<?php
namespace app\models\cars;

use yii\mongodb\ActiveRecord;

class ManufactureRecord extends ActiveRecord{

  public function rules() {
    return [
            ['old_id',    'integer',],
            ['NAME',      'string',],
            ['PC',        'boolean',],
            ['CV',        'boolean',],
            ['ENG',       'boolean',],
            ['AXL',       'boolean',],
            ['ENG_TYPE',  'integer',],
            ['MODELS',    'safe'],
    ];
  }

  public function attributes(){
    return ['_id',
            'old_id',
            'NAME',         // Полное имя
            'PC',           // Пр-ль легковых
            'CV',           // Пр-ль грузовых
            'ENG',          // Пр-ль двигателей
            'AXL',          // Пр-ль осей
            'ENG_TYPE',     // Тип двигателя
            'MODELS'];      // Модели
  }

  public static function collectionName(){
    return "cars";
  }

}
