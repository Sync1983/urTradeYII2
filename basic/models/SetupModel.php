<?php
/**
 * Description of SetupModel
 *
 * @author Sync<atc58.ru>
 */
namespace app\models;
use yii\base\Model;
use app\models\MongoUser;
use yii;

class SetupModel extends Model {
  public $type = 0;
  public $name = "Название";
  public $first_name = "Имя";
  public $second_name = "Фамилия";
  public $inn = "----------";
  public $kpp;
  public $addres = "Адрес доставки";
  public $phone  = "+7---------";
  public $email  = "test@test.ru";

  public function rules(){
    return [            
            [['type', 'first_name','second_name','addres','phone'], 'required'],            
            [['type','phone','inn'], 'integer'],
            ['email','email'],            
            ['inn', 'validateInn'],
            ['name','string']
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
  
  public function loadParams($data=[]){       
    foreach ($this->getAttributes() as $name=>$value){        
      if(isset($data[$name])){
        $this->$name = $data[$name];
      }
    }
    return $this->validate();
  }

  public function save(){    
    /* @var $user MongoUser */
    $user = YII::$app->user->getIdentity();
    foreach ($this->getAttributes() as $name=>$value){
      $user->setAttribute($name, $value);
    }
    
    if($user->update(true)){
      return true;
    }
    return false;
  }
}
