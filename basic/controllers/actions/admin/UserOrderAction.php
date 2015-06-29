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
  const TYPE_CHANGE = "change";
  public $type = self::TYPE_INFO;


  public function run() {	
    if( $this->type == self::TYPE_INFO ){
      return $this->typeInfo();
    }elseif( $this->type == self::TYPE_USER ){
      return $this->typeUser();
    }elseif( $this->type == self::TYPE_EXTEND ){
      return $this->typeExtend();
    }
  }

  protected function typeInfo(){
    $orders_collection = \app\models\orders\OrderRecord::getCollection();
    $orders_info = $orders_collection->aggregate([['$group' => ['_id'=>'$status','total'=>['$sum'=>1]]]]);
    $filter = new \app\models\admin\orders\TypeFilter();
    foreach ($orders_info as $item){
      $filter->setCount($item['_id'], $item['total']);
    }
    $orders = [];
    $orders_data = $orders_collection->aggregate([[
        '$group'=>[
          '_id' => ['user'=>'$for_user','status'=>'$status'],
          'total' => ['$sum'=>1]
        ]]]);

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

  protected function typeUser(){
    $user_id = \yii::$app->request->get("id",false);
    $user = MongoUser::findOne(["_id"=> new \MongoId(strval($user_id))]);

    if( !$user ){
      throw new \yii\web\HttpException(404,"Пользователь не найден");
    }

    $orders = \app\models\orders\OrderRecord::find()->orderBy(['status'=>SORT_ASC,'for_user'=> strval($user_id)])->all();
    $list = new \app\models\BasketDataProvider(['allModels'   => $orders,
        'pagination'  => new \yii\data\Pagination([
          'totalCount'  => count($orders),
          'pageSize'        => 40,
        ]),
    ]);
    $search_model = new \app\models\search\SearchModel();
    return $this->controller->render('orders/list',['user'=>$user,'list'=>$list,'search_model'=>$search_model]);
  }

  protected function typeExtend(){
    $key = \yii::$app->request->post('expandRowKey',false);
    if( !$key ){
      throw new \yii\web\NotFoundHttpException("Ключ записи не найден");
    }
    
    $order = \app\models\orders\OrderRecord::findOne(["_id"=> new \MongoId($key)]);
    if( !$order ){
      throw new \yii\web\NotFoundHttpException("Запись не найдена");
    }

    $providers = new \app\models\search\SearchModel();
    $user = \app\models\MongoUser::findOne(['_id'=> new \MongoId($order->for_user)]);
    return $this->controller->renderPartial('orders/order_info',['order'=>$order,'providers'=>$providers,'user'=>$user]);
  }
}