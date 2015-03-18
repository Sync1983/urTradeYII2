<?php

namespace app\models\forms;

use Yii;
use yii\web\Cookie;
use yii\base\Model;
use app\models\MongoUser;
use app\models\PartRecord;
use app\models\GuestBasket;
use MongoId;

/**
 * LoginForm is the model behind the login form.
 */
class BasketAddForm extends Model
{
    public $id;
    public $count;
    public $price_change; 
    
    public function attributes() {
      return ["id","count","price_change"];
    }

    public function attributeLabels()
    {
        return [
          'id'=>'',
          'count'=>'Общее количество:',
          'price_change'=>'Позволить заказ при изменении цены в пределах 10%:'
                ];
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [            
            [['id','count'],'required'],
            [['id','price_change'],'string'],            
            ['count','integer','min'=>1],
            ['count','validateLot'],            
        ];
    }

    /**     
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateLot($attribute, $params) {
      $part = PartRecord::getById($this->id);
      if(!$part){
        $this->addError($attribute, 'Запись устарела! Пожалуйста, обновите страницу.');
        return false;
      }
      $mod = $this->count % $part->lot_quantity;
      if($mod!==0){
        $this->addError($attribute, 'Количество деталей должно быть кратно указанной величине.');
        return false;
      }      
      if(is_numeric($part->count)){
        $max_count = intval($part->count);
      } else {
        $max_count = 100;
      }
      if($this->count > $max_count){
        $this->addError($attribute, 'На данном складе нет достаточного количества деталей');
        return false;
      }
      return true;      
    }
    /**
     * Добавляем деталь в корзину пользователя
     */
    public function addToUserBasket(){
      $part = PartRecord::getById($this->id);      
      $part->sell_count  = intval($this->count);
      $part->price_change = intval($this->price_change);
      $part->update_time = time();
      
      /* @var $user MongoUser*/
      $user = Yii::$app->user->identity;
      $user->addPartToBasket($part);      
      $user->save();
    }    
    /**
     * Добавляем деталь в гостевую корзину
     */    
    public function addToGuestBasket(){
      $part = PartRecord::getById($this->id);      
      $part->sell_count  = intval($this->count);
      $part->price_change = intval($this->price_change);
      $part->update_time = time();
      
      $basket_id = GuestBasket::getIdFromCookie();
      if($basket_id){
        $basket = GuestBasket::findOne(["_id"=>new MongoId($basket_id)]);        
      } else {
        $basket = new GuestBasket();
        $basket->save();        
        $basket_id = strval($basket->getAttribute("_id"));        
        GuestBasket::setIdToCookie($basket_id);
      }
      
      $basket->addPart($part);
      $basket->save();
    }
    
}
