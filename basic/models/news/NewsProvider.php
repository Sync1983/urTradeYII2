<?php
/**
 * Description of NewsProvider
 *
 * @author Sync<atc58.ru>
 */
namespace app\models\news;
use yii\data\BaseDataProvider;
use yii\data\DataProviderInterface;
use app\models\news\NewsModel;

class NewsProvider extends BaseDataProvider implements DataProviderInterface {
  public $id = "News";

  protected function prepareKeys($models) {
    $keys = array();    
    /* @var $model \app\models\news\NewsModel */
    foreach ($models as $model) {
      $keys[] = $model->getId();
    }
    return $keys;
  }

  protected function prepareModels() {        
    $data = NewsModel::find()->orderBy('date')->offset($this->pagination->offset)->limit($this->pagination->limit)->all();        
    return $data;
  }

  protected function prepareTotalCount() {
    $this->totalCount = intval(NewsModel::find()->count());    
    //return $this->totalCount;
  }
  
}
