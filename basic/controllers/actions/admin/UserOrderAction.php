<?php
/**
 * @author Sync
 */
namespace app\controllers\actions\admin;
use yii\base\Action;
use app\models\MongoUser;

class UserOrderAction extends Action {
  const TYPE_INFO = "info";
  const TYPE_USER = "user";
  const TYPE_EXTEND = "extend";
  public $type = self::TYPE_INFO;


  public function run() {	
    if( $this->type == self::TYPE_INFO){
      return $this->typeInfo();
    }
    
  }

  protected function typeInfo(){
    $orders_collection = \app\models\orders\OrderRecord::getCollection();
    $orders_info = $orders_collection->aggregate([
      [
        '$group' => ['_id'=>'$status','total'=>['$sum'=>1]],        
      ]
    ]);
    $filter = new \app\models\admin\orders\TypeFilter();
    foreach ($orders_info as $item){
      $filter->setCount($item['_id'], $item['total']);
    }
    $orders = [];
    $orders_data = $orders_collection->aggregate([
      [
        '$group'=>[
          '_id' => ['user'=>'$for_user','status'=>'$status'],
          'total' => ['$sum'=>1]
        ]
      ]
    ]);

    /* @var $user_id string */

    foreach ($orders_data as $item){
      $user_id  = $item['_id']['user'];
      $type     = $item['_id']['status'];
      $count    = $item['total'];
      if( !$user_id ){
        continue;
      }
      if( !isset($orders[$user_id]) ){
        $orders[$user_id] = new \app\models\admin\orders\UserTile();
        $orders[$user_id]->initUser($user_id);
      }
      if( !$orders[$user_id]->user ){
        unset($orders[$user_id]);
        continue;
      }
      $orders[$user_id]->data->setCount($type,$count);
    }

    return $this->controller->render("orders/index",['filter'=>$filter,'orders'=>$orders]);
  }
  
}