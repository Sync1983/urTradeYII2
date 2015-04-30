<?php
/**
 * Description of ProviderController
 *
 * @author Sync<atc58.ru>
 */
namespace app\commands;
use yii\console\Controller;

class RegController extends Controller{
  public $_providers = [];
  
  public function actionList() {
	$items = \app\models\RegRecord::find()
			  ->where(['time' => [ '$lte' => time() ] ])
			  ->andWhere([ 'was_send' => false ])
			  ->orderBy([ 'time' => SORT_ASC ])
			  ->all();	
	
    foreach ($items as $item) {
	  echo "Запрос от ".date("d-m-Y H:i:s",$item->time)." для логина [ $item->login ] и пароля [ $item->password ] почта [ $item->mail ] ключь активации [ $item->key ] \r\n";
    }
  }
  
  public function actionSendMail(){
	$items = \app\models\RegRecord::find()
			  ->where(['time' => [ '$lte' => time(), '$gte' => time()-24*60*60 ] ])
			  ->andWhere([ 'was_send' => false ])
			  ->orderBy([ 'time' => SORT_ASC ])
			  ->all();	
	
    foreach ($items as $item) {
	  echo "Send mail to login [ $item->login ] from date ".date("d-m-Y H:i:s",$item->time)." mail [ $item->mail ] key [ $item->key ] ";
	  if( \yii::$app->mailer->compose('site/contact',[ 'key' => $item->key] )
		  ->setFrom(['robot@atc58.ru' => 'АвтоТехСнаб Робот'])
		  ->setTo($item->mail)
		  ->setSubject('АвтоТехСнаб активация учетной записи')
		  ->send() ) {
		echo "[OK] \r\n";
	  } else {
		echo "[FAIL] \r\n";
	  }		  
    }
  }
  
  
}
