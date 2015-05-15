<?php
/**
 * @author Sync
 */
namespace app\controllers\actions\admin;
use yii\base\Action;

class DatabaseInfoAction extends Action {
  protected $_db;


  public function run() {	
    $connect = \yii::$app->get('mongodb');
    $this->_db = $connect->getDatabase();
    
  	$info = $this->getInfo();
  	$db   = $this->getDbInfo();
    $metric = $this->getMetric();
  	return $this->controller->render('summary/db-info',['info'=>$info,'db'=>$db, 'metric' => $metric]);
  }
  
  protected function getInfo(){
    
    $res = $this->_db->executeCommand(['hostInfo' =>  1]);
    
    $answer['system']['time']       = $res['system']['currentTime']->sec;
    $answer['system']['host']       = $res['system']['hostname'];
    $answer['system']['addr_size']  = $res['system']['cpuAddrSize'];
    $answer['system']['mem_size']   = $res['system']['memSizeMB'];
    $answer['system']['cores']      = $res['system']['numCores'];
    $answer['system']['arch']       = $res['system']['cpuArch'];
    $answer['system']['freq']       = $res['extra']['cpuFrequencyMHz'];
    $answer['system']['page']       = $res['extra']['pageSize'];
    $answer['os'] = $res['os']['type']." ".$res['os']['name']." ".$res['os']['version'];
 
    return $answer;
  }
  
  protected function getDbInfo() {
    
    $res = $this->_db->executeCommand(['dbStats' =>  1, 'scale' => 1024]);
    
    $answer['name'   ] = $res['db'];
    $answer['c_count'] = $res['collections'];
    $answer['o_count'] = $res['objects'];
    $answer['o_size' ] = round($res['avgObjSize'],2);
    $answer['db_size'] = $res['dataSize'];
    $answer['db_stor'] = $res['storageSize'];
    $answer['f_size' ] = $res['fileSize'];           
    
    return $answer;
  }
 
  protected function getMetric() {

    $res = $this->_db->executeCommand([
      'serverStatus' =>  1,
      'metrics' => 0, 
      'locks' => 0, 
      'extra_info' => 0,
      'cursors' => 0,
      'repl' => 0,
      'opcountersRepl' => 0,            
    ]);
    $answer['ver'   ]   = $res['version'];
    $answer['pid'   ]   = $res['pid'];
    $answer['uptime']   = $res['uptime'];
    $answer['a_reg' ]   = $res['asserts']['regular'];
    $answer['a_warn']   = $res['asserts']['warning'];
    $answer['a_msg' ]   = $res['asserts']['msg'];
    $answer['a_user']   = $res['asserts']['user'];
    $answer['a_roll']   = $res['asserts']['rollovers'];
    
    $answer['b_flush']  = $res['backgroundFlushing']['flushes'];
    $answer['b_time' ]  = $res['backgroundFlushing']['total_ms'];
    $answer['b_avg'  ]  = round($res['backgroundFlushing']['average_ms'],4);
    $answer['b_last' ]  = $res['backgroundFlushing']['last_ms'];
    
    $answer['c_count']  = $res['connections']['current'];
    $answer['c_ava'  ]  = $res['connections']['available'];
    $answer['c_total']  = $res['connections']['totalCreated'];
    
    $answer['n_in'   ]  = round($res['network']['bytesIn']/1024);
    $answer['n_out'  ]  = round($res['network']['bytesOut']/1024);
    $answer['n_req'  ]  = $res['network']['numRequests'];
    
    $answer['o_ins'  ]  = $res['opcounters']['insert'];
    $answer['o_query']  = $res['opcounters']['query'];
    $answer['o_upd'  ]  = $res['opcounters']['update'];
    $answer['o_del'  ]  = $res['opcounters']['delete'];
    $answer['o_cmd'  ]  = $res['opcounters']['command'];
    
    $answer['j_comm' ]  = $res['dur']['commits'];
    $answer['j_size' ]  = $res['dur']['journaledMB'];
    $answer['j_write']  = $res['dur']['writeToDataFilesMB'];
    $answer['j_lock' ]  = $res['dur']['commitsInWriteLock'];
    $answer['j_early']  = $res['dur']['earlyCommits'];
    
    return $answer;
  }
  
}