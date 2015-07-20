<?php

/**
 * @author Sync
 */

namespace app\models\forms;
use yii\base\Model;

class CatalogCreateForm extends Model{

  public $name    = "Новый каталог";
  public $visible = false;
  public $fields  = [
    ['attr'     => 'producer',
     'name'     => 'Производитель',
     'position' => 1,
     'type'     => 'string', //string, int, car, list
     'visible'  => true,
     'filter'   => true,
    ],
    ['attr'     => 'name',
     'name'     => 'Марка',
     'position' => 2,
     'type'     => 'string',
     'visible'  => true,
     'filter'   => true,
    ],
    ['attr'     => 'articul',
     'name'     => 'Артикул',
     'position' => 3,
     'type'     => 'string',
     'visible'  => false,
     'filter'   => false,
    ],
  ];

  public function attributes() {
    return [
      'name','visible','fields'
    ];
  }

  public function attributeLabels() {
    return [
      'name'    => 'Название каталога',
      'visible' => 'Видимость каталога',
      'fields'  => 'Поля каталога',
    ];
  }

  public function rules() {
    return [
      ['name','string','max'=>30],
      ['visible','boolean'],
      ['fields','safe']
    ];
  }
  
}
