<?php

namespace app\models\forms;

use yii\base\Model;

class SignUpCompanyForm extends Model
{
	public	$first_name,
			$second_name,
			$name,
			$inn,
			$kpp,
			$addres,
			$phone,	
			$email;
	public	$type='company';

    public function rules()
    {
        return [            
            [['first_name','second_name','addres','phone','email','name','inn'], 'required'],
			['email','email'],
			['inn','validateInn']
        ];
    }
	/**
	 * Метки атрибутов
	 * @return mixed
	 */
	public function attributeLabels() {
	  return [
		'name'			=> "Название",
		'first_name'	=> "Имя контактного лица",
		'second_name'	=> "Фамилия контактного лица",
		'addres'		=> "Адрес доставки",
		'phone'			=> "Номер телефона",
		'email'			=> "Почтовый адрес",
		'inn'			=> "ИНН",
		'kpp'			=> "КПП",
	  ];
	}
	
  public function validateInn($attribute, $params){    
    $k10=[2,4,10,3,5,9,4,6,8];
    $k12_2=[7,2,4,10,3,5,9,4,6,8];
    $k12_1=[3,7,2,4,10,3,5,9,4,6,8];
    $inn = strval($this->inn);
    $inn = trim($inn);
    $len = strlen($inn);    
    if($len!=10 && $len!=12){
      $this->addError($attribute, 'Неверное количество символов в ИНН.');
    }elseif($len==10){
      $sum = 0;
      $crc = $inn[$len-1]*1;
      for($i=0;$i<$len-1;$i++){
        $sum += $inn[$i]*$k10[$i];        
      }
      if($crc!=$sum%11){
        $this->addError($attribute, 'Ошибка при проверке ИНН. Проверьте правильность введеных цифр.');
      }
    }elseif($len==12){
      $sum = 0;
      $crc1 = $inn[$len-1]*1;
      $crc2 = $inn[$len-2]*1;
      for($i=0;$i<$len-2;$i++){
        $sum += $inn[$i]*$k12_2[$i];        
      }
      $crc_2 = $sum%11;
      $sum = 0;
      for($i=0;$i<$len-1;$i++){
        $sum += $inn[$i]*$k12_1[$i];        
      }
      $crc_1 = $sum%11;
      if($crc_1!=$crc1 || $crc_2!=$crc2){
        $this->addError($attribute, 'Ошибка при проверке ИНН. Проверьте правильность введеных цифр.');
      }
    }    
  }
	
}
