<?php

/**
 * @author Sync
 */

namespace app\models\admin\orders;
use yii\base\Model;
use app\models\orders\OrderRecord;

class TypeFilter extends Model{
  public $cnt_wait_pay        = 0;
  public $cnt_wait_placement  = 0;
  public $cnt_placement       = 0;
  public $cnt_in_way          = 0;
  public $cnt_in_storage      = 0;
  public $cnt_in_place        = 0;
  public $cnt_rejected        = 0;

  public function setCount($type = 0, $count = 0, $add = false){
    if( $type === OrderRecord::STATE_WAIT_PAY){
      $this->cnt_wait_pay = $add?($this->cnt_wait_pay + $count): $count;
    }elseif( $type === OrderRecord::STATE_WAIT_PLACEMENT ) {
      $this->cnt_wait_placement = $add?($this->cnt_wait_placement + $count): $count;
    }elseif( $type === OrderRecord::STATE_PLACEMENT ) {
      $this->cnt_placement = $add?($this->cnt_placement + $count): $count;
    }elseif( $type === OrderRecord::STATE_IN_WAY) {
      $this->cnt_in_way = $add?($this->cnt_in_way + $count): $count;
    }elseif( $type === OrderRecord::STATE_IN_STORAGE ) {
      $this->cnt_in_storage = $add?($this->cnt_in_storage + $count): $count;
    }elseif( $type === OrderRecord::STATE_IN_PLACE) {
      $this->cnt_in_place= $add?($this->cnt_in_place + $count): $count;
    }elseif( $type === OrderRecord::STATE_REJECTED) {
      $this->cnt_rejected = $add?($this->cnt_rejected + $count): $count;
    }
  }
}
