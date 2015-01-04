<?php
/**
 * Description of DbController
 *
 * @author Sync<atc58.ru>
 */
namespace app\commands;

use yii\console\Controller;
use yii\mongodb\Connection;
use app\models\news\NewsModel;

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
  
  public function actionNews(){
    $connection = new Connection(['dsn'=>'mongodb://localhost:27017']);
    $connection->open();
    $database = $connection->getDatabase('ur_trade');
    $collection = $database->getCollection('news');
    $collection->insert([
    'header'=>"С 10 декабря 2014г. компания Luzar начала поставки нового электровентилятора охлаждения (мотора радиатора) для автомобилей Chevrolet Cruze/Orlando",
    'icon'=>"http://cs622825.vk.me/v622825759/12ac7/DURwn-2vCVs.jpg",
    'text'=>"OEM артикул: 13335181, 13267640, 13267641 фирменное наименование: LFc 0550 для автомобилей:
                    Chevrolet Cruze (09-)/Orlando (10-) 1.6i/1.8i M/A
                    Для справки: данный вентилятор агрегатируется с кожухами 13267630, 13267633
                    Рекомендованная розничная цена: 4 470 руб.
                    ",
     'date'=>  time()]);
  }

  public function actionIndex() {
        echo "cron service runnning";
  }
}
