<?php

namespace app\models\forms;

use yii\base\Model;

class SignUpTypeForm extends Model
{
	const types = [
			'private' => 'Частное лицо',
			'company' => 'Юридическое лицо'
	];
    public $type;	

    public function rules()
    {
        return [            
            ['type', 'required'],            
            ['type', 'validateType'],
        ];
    }
	/**
	 * Метки атрибутов
	 * @return mixed
	 */
	public function attributeLabels() {
	  return [
			  'type'		=> "Вы",
	  ];
	}
	
    public function validateType($attribute, $params) {
	  $types = self::types;
      if( !isset($types[$this->type]) ){
		$this->addError($attribute, 'Ошибка в значении поля');
        return false;		
	  }
	  return true;
    }
	
}
