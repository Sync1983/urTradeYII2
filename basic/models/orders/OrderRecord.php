<?php

/**
 * Description of OrderRecord
 * @author Sync<atc58.ru>
 */
namespace app\models\orders;
use yii\mongodb\ActiveRecord;

class OrderRecord extends ActiveRecord{
  const STATE_WAIT_PAY = 0;
  const STATE_WAIT_PLACEMENT = 1;
  const STATE_PLACEMENT = 2;
  const STATE_IN_WAY = 3;
  const STATE_IN_STORAGE = 4;
  const STATE_IN_PLACE = 5;
  const STATE_REJECTED = 6;
  //public vars
  //protected vars  
  protected $states = [
    self::STATE_WAIT_PAY        => "Ожидает оплаты",
    self::STATE_WAIT_PLACEMENT  => "Ожидает размещения",
    self::STATE_PLACEMENT       => "Заказано",
    self::STATE_IN_WAY          => "В пути",
    self::STATE_IN_STORAGE      => "На складе",
    self::STATE_IN_PLACE        => "Выдано",
    self::STATE_REJECTED        => "Отказ",    
  ];  
  //private vars  
  //============================= Public =======================================
  public function textStatus(){
    return isset($this->states[$this->status])?($this->states[$this->status]):$this->status;
  }
  public function getStatuses(){
    return $this->states;
  }
  //============================= Protected ====================================
  protected function onUpdate($event){
    $this->update_time = time();    
  }  
  //============================= Private ======================================
  //============================= Constructor - Destructor =====================
  public function init(){
    parent::init();
    $this->on(self::EVENT_BEFORE_UPDATE, [$this, "onUpdate"]);
  }
  public function attributes(){    
    return ['_id','search_articul',  
      "provider", "articul","producer","maker_id",
      "name","price","shiping","stock","info",
      "update_time","is_original","count","lot_quantity", 
      "for_user","price_change","sell_count","comment",
      'status','pay','pay_request','pay_time','pay_value','wait_time'];
  }
  public function rules(){
    return [
      [['status'],'safe'],
      [['update_time','pay_time','status'],'integer'],
      [['pay','pay_request'],'boolean'],
      [['pay_value'],'number'],      
      [['_id','search_articul',  
        "provider", "articul","producer","maker_id",
        "name","price","shiping","stock","info",
        "update_time","is_original","count","lot_quantity", 
        "for_user","price_change","sell_count","comment",'wait_time'],'safe'],
    ];
  }
  public static function collectionName(){
    return 'orders';
  }
  public function beforeSave($insert){
    if( !$this->wait_time ){
      $this->wait_time = $this->update_time + intval($this->shiping)*24*60*60;
    }
    if( is_int($this->shiping) ){
      $this->shiping *= 1;
    }
    return parent::beforeSave($insert);
  }

}
