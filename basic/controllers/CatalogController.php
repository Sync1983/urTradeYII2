<?php

/**
 * Description of Basket
 * @author Sync<atc58.ru>
 */

namespace app\controllers;

use yii\base\Controller;



class CatalogController extends Controller{
  //public vars  
  
  //protected vars  
  //private vars  
  //============================= Public =======================================
  public function actionExternal(){
    $search = $this->getSearchForm();
    $search->search_text = \yii::$app->request->get('article',"");
    $search->cross  = true;
    return $this->render('@app/views/site/search');
  }
  
  //============================= Protected ====================================
  //============================= Private ======================================
  //============================= Constructor - Destructor =====================
  public function behaviors() {
    return [
      'form' => [
            'class' => \app\components\behaviors\SearchFormBehavior::className(),
        ]
    ];
  }
}
