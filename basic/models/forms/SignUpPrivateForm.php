<?php

namespace app\models\forms;

use yii\base\Model;

class SignUpPrivateForm extends Model
{
	public	$first_name,
			$second_name,
			$addres,
			$phone,	
			$email;	
	public	$type='private';

		public function rules()
    {
        return [            
            [['first_name','second_name','addres','phone','email'], 'required'],
			['email','email']
        ];
    }
	/**
	 * Метки атрибутов
	 * @return mixed
	 */
	public function attributeLabels() {
	  return [
		'first_name'	=> "Имя",
		'second_name'	=> "Фамилия",
		'addres'		=> "Адрес доставки",
		'phone'			=> "Номер телефона",
		'email'			=> "Почтовый адрес",
	  ];
	}
	
}
