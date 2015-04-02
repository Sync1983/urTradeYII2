<?php
/**
 * @author Sync<atc58.ru>
 */

namespace app\models\socnet;

interface SocNetInterface {  
  public function auth_request($return);
  public function auth_answer($return);
  public function getData($code,$return);
  public function getSocNetName();
  public function error();
}
