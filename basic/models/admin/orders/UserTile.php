<?php

/**
 * @author Sync
 */

namespace app\models\admin\orders;
use yii\base\Model;
use app\models\admin\orders\TypeFilter;

class UserTile extends Model{
  /** @var $data app\models\admin\orders\TypeFilter */
  public $data;
  /** @var $user \app\models\MongoUser */
  public $user;

  public function init() {
    $this->data = new TypeFilter();
    return parent::init();
  }

  public function initUser($id){
    $this->user = \app\models\MongoUser::findOne(['_id'=> new \MongoId(strval($id))]);
  }
}
