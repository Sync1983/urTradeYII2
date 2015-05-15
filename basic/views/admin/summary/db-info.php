<?php
use yii\helpers\Html;
use yii\helpers\Url;
/* @var $this yii\web\View */

$this->title = "База Данных";
$this->params['breadcrumbs'][] = [
  'label' => 'Общая информация',
  'url' => ['admin/index'],
  ];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">

  <div class="col-lg-3 col-md-6">
    <div class="panel panel-primary">
      <div class="panel-heading">
        <div class="row">
          <div class="col-xs-12 text-right">            
            <div><strong>Система</strong></div>
          </div>
        </div>
      </div>
      <div class="panel-body">
        <div class="row col-xs-offset-0">
          <label class="col-xs-6">Текущее время:</label>
          <?= date("d-m-Y H:i:s",$info['system']['time']); ?>
        </div>
        <div class="row col-xs-offset-0">
          <label class="col-xs-6">Архитектура:</label>
          <?= $info['system']['arch']; ?>
        </div>
        <div class="row col-xs-offset-0">
          <label class="col-xs-6">Размерность:</label>
          <?= $info['system']['addr_size']; ?>
        </div>
        <div class="row col-xs-offset-0">
          <label class="col-xs-6">Количество ядер:</label>
          <?= $info['system']['cores']; ?>
        </div>
        <div class="row col-xs-offset-0">
          <label class="col-xs-6">Частота ядра:</label>
          <?= $info['system']['freq']; ?>
        </div>
        <div class="row col-xs-offset-0">
          <label class="col-xs-6">Память:</label>
          <?= $info['system']['mem_size']; ?> Mb
        </div>
        <div class="row col-xs-offset-0">
          <label class="col-xs-6">Размер страницы:</label>
          <?= $info['system']['page']; ?> Byte
        </div>
        <div class="row col-xs-offset-0">
          <label class="col-xs-6">Хост:</label>
          <?= $info['system']['host']; ?>
        </div>
        <div class="row col-xs-offset-0">
          <label class="col-xs-6">ОС:</label>
          <?= $info['os']; ?>
        </div>
        <div class="clearfix"></div>
      </div>      
    </div>
  </div>
  
  <div class="col-lg-3 col-md-6">
    <div class="panel panel-primary">
      <div class="panel-heading">
        <div class="row">          
          <div class="col-xs-4 text-left">            
            <div><strong>База данных</strong></div>
          </div>
        </div>
      </div>
      <div class="panel-body">
        <div class="row col-md-offset-0">
          <label class="col-xs-7">Имя базы:</label>
          <?= $db['name']; ?>
        </div>
        <div class="row col-xs-offset-0">
          <label class="col-xs-7">Количество таблиц:</label>
          <?= $db['c_count']; ?>
        </div>
        <div class="row col-xs-offset-0">
          <label class="col-xs-7">Количество объектов:</label>
          <?= $db['o_count']; ?>
        </div>
        <div class="row col-xs-offset-0">
          <label class="col-xs-7">Средний размер документа:</label>
          <?= $db['o_size']; ?> KB
        </div>
        <div class="row col-xs-offset-0">
          <label class="col-xs-7">Размер базы:</label>
          <?= $db['db_size']; ?> KB
        </div>
        <div class="row col-xs-offset-0">
          <label class="col-xs-7">Выделено памяти:</label>
          <?= $db['db_stor']; ?> KB
        </div>
        <div class="row col-md-offset-0">
          <label class="col-xs-7">Выделено на диске:</label>
          <?= $db['f_size']; ?> KB
        </div>        
        <div class="clearfix"></div>
      </div>      
    </div>
  </div>
  
  <div class="col-lg-3 col-md-6">
    <div class="panel panel-primary">
      <div class="panel-heading">
        <div class="row">          
          <div class="col-xs-12 text-right">            
            <div><strong>Сервер базы данных</strong></div>
          </div>
        </div>
      </div>
      <div class="panel-body">
        <div class="row col-md-offset-0">
          <label class="col-xs-7">Версия:</label>
          <?= $metric['ver']; ?>
        </div>
        <div class="row col-xs-offset-0">
          <label class="col-xs-7">PID процесса:</label>
          <?= $metric['pid']; ?>
        </div>
        <div class="row col-xs-offset-0">
          <label class="col-xs-7">Аптайм:</label>
          <?= $metric['uptime']; ?> cек.
        </div>        
      </div>
      <div class="clearfix"></div>
    </div>
  </div>
</div> 

<div class="row">
    
  <div class="col-lg-3 col-md-6">
    <div class="panel panel-primary">
      <div class="panel-heading">
        <div class="row">          
          <div class="col-xs-12 text-right">            
            <div><strong>Пердупреждения БД</strong></div>
          </div>
        </div>
      </div>
      <div class="panel-body">
        <div class="row col-md-offset-0">
          <label class="col-xs-7">Постоянные:</label>
          <?= $metric['a_reg']; ?>
        </div>
        <div class="row col-xs-offset-0">
          <label class="col-xs-7">Предупреждения:</label>
          <?= $metric['a_warn']; ?>
        </div>
        <div class="row col-xs-offset-0">
          <label class="col-xs-7">Сообщения:</label>
          <?= $metric['a_msg']; ?>
        </div>        
        <div class="row col-xs-offset-0">
          <label class="col-xs-7">Пользовательские:</label>
          <?= $metric['a_user']; ?>
        </div>        
        <div class="row col-xs-offset-0">
          <label class="col-xs-7">Откаты:</label>
          <?= $metric['a_roll']; ?>
        </div>        
      </div>
      <div class="clearfix"></div>
    </div>
  </div>
    
  <div class="col-lg-3 col-md-6">
    <div class="panel panel-primary">
      <div class="panel-heading">
        <div class="row">          
          <div class="col-xs-6 text-left">            
            <div><strong>Запись на диск</strong></div>
          </div>
        </div>
      </div>
      <div class="panel-body">
        <div class="row col-md-offset-0">
          <label class="col-xs-7">Количество:</label>
          <?= $metric['b_flush']; ?>
        </div>
        <div class="row col-xs-offset-0">
          <label class="col-xs-7">Общее время записи:</label>
          <?= $metric['b_time']; ?> мсек.
        </div>
        <div class="row col-xs-offset-0">
          <label class="col-xs-7">Среднее время записи:</label>
          <?= $metric['b_avg']; ?> мсек.
        </div>        
        <div class="row col-xs-offset-0">
          <label class="col-xs-7">Последнее время записи:</label>
          <?= $metric['b_last']; ?> мсек.
        </div>        
      </div>
      <div class="clearfix"></div>
    </div>    
  </div>
  
  <div class="col-lg-3 col-md-6">
    <div class="panel panel-primary">
      <div class="panel-heading">
        <div class="row">          
          <div class="col-xs-12 text-right">            
            <div><strong>Соединения</strong></div>
          </div>
        </div>
      </div>
      <div class="panel-body">
        <div class="row col-md-offset-0">
          <label class="col-xs-7">Количество:</label>
          <?= $metric['c_count']; ?>
        </div>
        <div class="row col-xs-offset-0">
          <label class="col-xs-7">Доступные:</label>
          <?= $metric['c_ava']; ?>
        </div>
        <div class="row col-xs-offset-0">
          <label class="col-xs-7">Всего:</label>
          <?= $metric['c_total']; ?>
        </div>        
      </div>
      <div class="clearfix"></div>
    </div>    
  </div>
  
</div>

<div class="row">
  
  <div class="col-lg-3 col-md-6">
    <div class="panel panel-primary">
      <div class="panel-heading">
        <div class="row">          
          <div class="col-xs-12 text-right">            
            <div><strong>Сеть</strong></div>
          </div>
        </div>
      </div>
      <div class="panel-body">
        <div class="row col-md-offset-0">
          <label class="col-xs-7">Входящих данных:</label>
          <?= $metric['n_in']; ?> KB
        </div>
        <div class="row col-xs-offset-0">
          <label class="col-xs-7">Исходящих данных:</label>
          <?= $metric['n_out']; ?> KB
        </div>
        <div class="row col-xs-offset-0">
          <label class="col-xs-7">Запросов:</label>
          <?= $metric['n_req']; ?>
        </div>        
      </div>
      <div class="clearfix"></div>
    </div>    
  </div>
  
  <div class="col-lg-3 col-md-6">
    <div class="panel panel-primary">
      <div class="panel-heading">
        <div class="row">          
          <div class="col-xs-12 text-left">            
            <div><strong>Счетчики</strong></div>
          </div>
        </div>
      </div>
      <div class="panel-body">
        <div class="row col-md-offset-0">
          <label class="col-xs-7">Insert:</label>
          <?= $metric['o_ins']; ?>
        </div>
        <div class="row col-xs-offset-0">
          <label class="col-xs-7">Query:</label>
          <?= $metric['o_query']; ?>
        </div>
        <div class="row col-xs-offset-0">
          <label class="col-xs-7">Update:</label>
          <?= $metric['o_upd']; ?>
        </div>        
        <div class="row col-xs-offset-0">
          <label class="col-xs-7">Delete:</label>
          <?= $metric['o_del']; ?>
        </div>        
        <div class="row col-xs-offset-0">
          <label class="col-xs-7">Command:</label>
          <?= $metric['o_cmd']; ?>
        </div>        
      </div>
      <div class="clearfix"></div>
    </div>    
  </div>
  
  <div class="col-lg-3 col-md-6">
    <div class="panel panel-primary">
      <div class="panel-heading">
        <div class="row">          
          <div class="col-xs-12 text-right">            
            <div><strong>Журнал</strong></div>
          </div>
        </div>
      </div>
      <div class="panel-body">
        <div class="row col-md-offset-0">
          <label class="col-xs-7">Количество транзакций:</label>
          <?= $metric['j_comm']; ?>
        </div>
        <div class="row col-xs-offset-0">
          <label class="col-xs-7">Записано:</label>
          <?= $metric['j_size']; ?> MB
        </div>
        <div class="row col-xs-offset-0">
          <label class="col-xs-7">Сброшено на диск:</label>
          <?= $metric['j_write']; ?> MB
        </div>        
        <div class="row col-xs-offset-0">
          <label class="col-xs-7">Транзакции во время блокировки:</label>
          <?= $metric['j_lock']; ?>
        </div>        
        <div class="row col-xs-offset-0">
          <label class="col-xs-7">Ранних транзакций:</label>
          <?= $metric['j_early']; ?>
        </div>        
      </div>
      <div class="clearfix"></div>
    </div>    
  </div>
  
</div>