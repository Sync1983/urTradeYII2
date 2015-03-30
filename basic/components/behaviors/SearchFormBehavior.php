<?php

/**
 * Description of SearchFromBehavior
 * @author Sync<atc58.ru>
 */

namespace app\components\behaviors;

use yii;
use yii\base\Behavior;
use app\models\forms\SearchForm;
use yii\web\Controller;

class SearchFormBehavior extends Behavior{
  //public vars
  //protected vars
  protected $search;
  //private vars  
  //============================= Public =======================================
  public function getSearchForm(){
    return $this->search;
  }
  
  public function initSearch($event){    
    $this->search =  new SearchForm();
    $request = yii::$app->request->get();
    $request['over_price'] = (int) Yii::$app->request->get('over-price',0);         
    $this->search->load($request,'');    
    $event->action->controller->getView()->params['search_model'] = $this->search;
  }
  //============================= Protected ====================================
  //============================= Private ======================================
  //============================= Constructor - Destructor =====================
  public function events(){
    return [
      Controller::EVENT_BEFORE_ACTION => 'initSearch',
    ];
  }

}
