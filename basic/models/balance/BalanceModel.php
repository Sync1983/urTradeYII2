<?php

/**
 * Description of BalanceModel
 * @author Sync<atc58.ru>
 */
namespace app\models\balance;

use yii;
use yii\base\Component;
use app\models\events\BalanceEvent;
use app\models\balance\BalanceRecord;

class BalanceModel extends Component {
  //public vars
  //protected vars
  protected $pay_add    = 0.0;
  protected $pay_dec    = 0.0;
  protected $pay_credit = 0.0;
  
  //private vars  
  //============================= Public =======================================
  /**
   * Возвращает баланс по средствам пользователя, включая кредит
   * @return float
   */
  public function getFullBalance(){
    return ($this->pay_add + $this->pay_credit - $this->pay_dec)*1.0;
  }
  /**
   * Возвращает баланс по собственным средствам пользователя
   * @return float
   */
  public function getRealBalance(){
    return ($this->pay_add - $this->pay_dec)*1.0;
  }
  //============================= Protected ====================================
  protected function calculate(){   
    if( yii::$app->user->isGuest){
      return;
    }
    $in = BalanceRecord::find()
            ->where([
                'operation'   => BalanceRecord::OP_ADD,
                'reciver_type' => BalanceRecord::IT_USER,
                'reciver_id' => strval(\yii::$app->user->getId()),])
            ->sum("value");
    $out = BalanceRecord::find()
            ->where([
              'operation'   => BalanceRecord::OP_DEC,
              'reciver_type' => BalanceRecord::IT_USER,
              'reciver_id' => strval(\yii::$app->user->getId()),])
            ->sum("value");
    $this->pay_add = $in;
    $this->pay_dec = $out;
    $this->pay_credit = floatval(\yii::$app->user->getIdentity()->credit);    
  }
  /**
   * Слушатель событий изменения баланса
   * вызывает перерасчет данных о балансе
   * при появлении сообщения
   * @param BalanceEvent $event
   */
  public function onBalanceChange(BalanceEvent $event){
    $this->calculate();
  }
  //============================= Private ======================================
  //============================= Constructor - Destructor =====================
  public function init(){
    \yii::$app->on(BalanceEvent::EVENT_BALANCE_CHANGE,[  $this,'onBalanceChange']);
  }

}
