<?php
/**
 * @author Sync
 */
namespace app\controllers\actions\site;
use yii\base\Action;

class SearchAction extends Action {
  const TYPE_INDEX        = "index";
  const TYPE_HELPER       = "helper";
  const TYPE_PARTS_SHORT  = "parts";
  const TYPE_PARTS_FULL   = "full";

  public $type  = self::TYPE_INDEX;
  /* @var $_form app\models\forms\SearchForm */
  protected $_form;

  public function run() {
    $this->_form = $this->controller->getSearchForm();

    if( $this->type == self::TYPE_HELPER ){
      return $this->actionHelper();
    }elseif( $this->type == self::TYPE_PARTS_SHORT ){
      return $this->actionShortList();
    }elseif($this->type == self::TYPE_PARTS_FULL ){
      return $this->actionFullList();
    }

    if( $this->_form->validate() ){
      \app\models\SearchHistoryRecord::addQuery( $this->_form->search_text );
    }

    return $this->controller->render('search');
  }

  public function actionHelper() {
    \yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    
    $search_text = \yii::$app->request->post('text',false);
    if( !$search_text ){
      return [];
    }

    $clear_text = \app\models\search\SearchProviderBase::_clearStr( $search_text );
    $helper = \app\models\PartRecord::getHelperByPartId( $clear_text );
    if( !$helper ){
      return [];
    }

    $items = [];
    foreach ($helper as $item) {
      $items[$item->getAttribute("articul")] = $item->getAttribute("articul")." - <b>".$item->getAttribute("producer")."</b>";
    }
    ksort($items);
    
    return $items;
  }

  public function actionShortList() {
    \yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    
    $model = new \app\models\search\SearchModel();
    if( $model->load(\yii::$app->request->post(),'') ){
      return [  'id'    => $model->getCurrentCLSID(),
                'parts' => $model->loadParts() ];
    }
    
    return [  'id'    => $model->getCurrentCLSID(),
              'parts' => [] ];
  }

  public function actionFullList() {
    \yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

    $model = new \app\models\search\SearchModel();
    if( $model->load(\yii::$app->request->post(),'') ){
      return [  'id'    => $model->getCurrentCLSID(),
                'parts' => $model->loadAllParts() ];
    }

    return [  'id'    => $model->getCurrentCLSID(),
              'parts' => [] ];

  }
  
}
