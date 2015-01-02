<?php
/**
 * Description of DbController
 *
 * @author Sync<atc58.ru>
 */
namespace app\commands;

use yii\console\Controller;
use yii\mongodb\Connection;

class DbController extends Controller{
  
  public function actionInit() {
    $connection = new Connection(['dsn'=>'mongodb://localhost:27017']);
    $connection->open();
    $database = $connection->getDatabase('ur_trade');
    $collection = $database->getCollection('users');
    if ($collection->find(['user_name' => "admin"])->count() == 0) {
      $collection->insert(['user_name' => "admin", 'user_pass' => md5("test"), 'role' => "admin"]);      
    }
  }
  
  public function actionIndex() {
        echo "cron service runnning";
  }
}
