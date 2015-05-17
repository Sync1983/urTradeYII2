<?php

/**
 * @author Sync
 */

namespace app\controllers;
use yii\web\Controller;

class PayController extends Controller{
  
  public function actions() {
    return [
      'check'  => [
        'class' => actions\pay\CheckAction::className()      
      ]
    ];
  }
}
