<?php

/**
 * Description of AdminUserForm
 * @author Sync<atc58.ru>
 */
namespace app\models\admin\forms;
use yii\base\Model;

class AdminUserForm extends Model{
  //public vars
  public $_id; 
  public $user_name;        //Логин
  public $user_pass;        //Пароль
  public $role;             //Роль пользователя      
  public $over_price;       //Наценка для пользователя      
  public $first_name;       //Имя
  public $second_name;      //Фамилия
  public $type;             //Тип
  public $photo;            //Адрес аватара
  public $name;             //Название фирмы
  public $inn;              //ИНН фирмы
  public $kpp;              //КПП фирмы
  public $addres;           //Адрес доставки
  public $phone;            //Телефон для связи
  public $email;            //Почта для связи  
  public $credit;           //Кредит пользователя
  
  //public $informer;          //Записи сообщений
  //protected vars
  //private vars  
  //============================= Public =======================================
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
  //============================= Protected ====================================
  //============================= Private ======================================
  //============================= Constructor - Destructor =====================
  public function attributeLabels(){
    return [
      '_id'              => "ID",
      'user_name'        => "Логин",
      'user_pass'        => "Пароль",
      'role'             => "Роль пользователя      ",
      'over_price'       => "Наценка для пользователя      ",
      'first_name'       => "Имя",
      'second_name'      => "Фамилия",
      'type'             => "Тип",
      'photo'            => "Адрес аватара",
      'name'             => "Название фирмы",
      'inn'              => "ИНН фирмы",
      'kpp'              => "КПП фирмы",
      'addres'           => "Адрес доставки",
      'phone'            => "Телефон для связи",
      'email'            => "Почта для связи",      
      'credit'           => "Кредит пользователя",
      'basket'           => "Записи корзины",
      'informer'         => "Записи сообщений",
      ];
  }
  
  public function rules(){
    return [
      [ ['_id'],'safe' ], 
      [ ['user_name','user_pass','role','first_name','second_name','type','name','addres',],'string',],
      [ ['photo'],'url' ],      
      [ ['email'],'email'],
      [ ['credit','over_price'], 'number'],
      [ ['phone',],'string'],      
      [ ['inn','kpp',],'safe'],
      [ ['inn',],'validateInn'],      
      ];
  }
}
