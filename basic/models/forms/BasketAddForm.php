<?php

namespace app\models\forms;

use yii;
use app\models\events\BasketEvent;
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
     * Инициирует процесс добавления детали
     */
    public function process(){
      $event = new BasketEvent();      
      $event->key = $this->id;
      $event->params["sell_count"] = intval($this->count);
      $event->params["price_change"] = intval($this->price_change);
      if(yii::$app->user->isGuest){
        $event->type = BasketEvent::GUEST_BASKET;
      } else{
        $event->type = BasketEvent::USER_BASKET;
      }       
      
      $this->trigger(\app\models\basket\BasketModel::EVENT_ADD_TO_BASKET, $event);
    }
    
}
