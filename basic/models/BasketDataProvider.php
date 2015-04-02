<?php

/**
 * Description of BasketDataProvider
 * @author Sync<atc58.ru>
 */

namespace app\models;

use yii\data\BaseDataProvider;
use app\models\PartRecord;

class BasketDataProvider extends BaseDataProvider{
  //public vars
  public $allModels;
  //protected vars
  //private vars  
  //============================= Public =======================================
  //put your code here
  //============================= Protected ====================================
  protected function prepareKeys($models) {
    $keys = [];
    foreach ($models as $model) {
      /* @var $model PartRecord */
      $keys[] = strval($model->getAttribute("_id"));
    }
    return $keys;
  }

  protected function prepareModels() {
    $models = [];
    $pagination = $this->getPagination();
    if ($pagination === false) {
      $models = $this->allModels;
    } else {
      $models = array_slice($this->allModels, $pagination->getOffset(), $pagination->getLimit());
    }
    return $models;
  }

  protected function prepareTotalCount() {
    return count($this->allModels);
  }
  //============================= Private ======================================

  //============================= Constructor - Destructor =====================
}
