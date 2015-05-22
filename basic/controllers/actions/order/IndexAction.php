<?php
/**
 * @author Sync
 */
namespace app\controllers\actions\order;
use yii\base\Action;

class IndexAction extends Action {
  
  public function run() {

    if( \yii::$app->user->isGuest ){
      //Для гостей формируем пустой список заказов
      $items = [];
    } else {
      //Для авторизированных - запрашиваем из базы
      $items = $this->loadDataWithSorting();
    }

    $order_list = new \app\models\BasketDataProvider([
        'allModels'   => $items,
        'pagination'  => new \yii\data\Pagination([
          'totalCount'  => count($items),
          'pageSize'    => 20,
        ]),
    ]);
    $order_list->setSort([
      'attributes' =>['status','pay','pay_value','sell_count','wait_time','part_attr','shiping','sum_attr']
    ]);
    
    return $this->controller->render(
              'index',
              [
                'list'    => $order_list,
                'columns' =>  $this->controller->columns()
              ]);
  }

  protected function loadDataWithSorting(){
    $sort = \yii::$app->request->get('sort',false);

    $record = new \app\models\orders\OrderRecord();    
    $sort_order = SORT_ASC;
    
    if( substr($sort, 0,1) == "-"){
      $sort_order = SORT_DESC;
      $sort = substr($sort, 1);
    }    

    if( !$sort ){
      //Если изначально нет сортировки, то применяем сортировку по статусу, используя базу
      return $record->find( ['for_user' => strval(\yii::$app->user->getId())] )
                ->orderBy(['status'=>$sort_order])
                ->all();
    } elseif( \yii\helpers\ArrayHelper::keyExists($sort, $record->getAttributes()) ){
      //Если ключь сортировки - это атрибут записи, то отдаем сортировку базе
      return $record->find( ['for_user' => strval(\yii::$app->user->getId())] )
                ->orderBy([$sort => $sort_order])
                ->all();
    }
    //Для вычисляемух полей придется применить собственную сортировку
    $items = $record->find( ['for_user' => strval(\yii::$app->user->getId())] )
                ->orderBy([$sort => $sort_order])
                ->all();
    usort($items, function ($A, $B) use ($sort,$sort_order) {
      if( $sort == 'sum_attr' ){
        $valA = $A->price * $A->sell_count * 1.0;
        $valB = $B->price * $B->sell_count * 1.0;
      }elseif( $sort == 'part_attr'){
        $valA = $A->articul;
        $valB = $B->articul;
      } else {
        $valA = $valB = 0;
      }
      $compare = ($valA == $valB)? 0 : ( ($valA>$valB)?1:-1 );      
      return $sort_order==SORT_ASC?$compare:-$compare;
    });                  
                  
    return $items;
  }
  
}
