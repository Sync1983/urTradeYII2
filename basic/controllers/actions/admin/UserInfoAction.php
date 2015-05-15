<?php
/**
 * @author Sync
 */
namespace app\controllers\actions\admin;
use yii\base\Action;
use app\models\MongoUser;

class UserInfoAction extends Action {
  
  public function run() {	
    $info = $this->getInfo();
  	return $this->controller->render('summary/user-info',['info'=>$info]);
  }
  
  protected function getInfo() {
    $answer['count']     =   MongoUser::find()->count();
    $answer['ur_count']  =   MongoUser::find()->where(['type' => 'company'])->count();
    $answer['pr_count']  =   MongoUser::find()->where(['type' => 'private'])->count();
    $answer['ad_count']  =   MongoUser::find()->where(['role' => 'admin'])->count();
    $answer['ur_av_price']  =   MongoUser::find()->where(['type' => 'company'])->average('over_price');
    $answer['pr_av_price']  =   MongoUser::find()->where(['type' => 'private'])->average('over_price');
    $answer['full_credit']  =   MongoUser::find()->sum('credit');
    
    return $answer;    
  }
  
}