<?php
/**
 * Description of SiteModel
 *
 * @author Sync<atc58.ru>
 */
namespace app\models;

use yii\base\Model;
use app\models\LoginForm;

class SiteModel extends Model {
  /* @var $instance SiteModel */
  static protected $instance;
  
  public $search  = "";
  public $cross   = true;
  public $op      = 0;
  public $login_form;
  public $over_price = [1=>'10%',2=>'15%',3=>'20%',4=>'25%'];
  
  public function generateOverPrice(){
    $text = "";
    $this->over_price[0]="Без наценки";
    ksort($this->over_price);
    foreach ($this->over_price as $key => $value) {
      $text .= "<option value=\"$key\" ".(($key==$this->op)?"selected":"").">".$value."</option>";
    }
    return $text;
  }

  /* @return SiteModel */
  static public function _instance(){
    if(!SiteModel::$instance){
      SiteModel::$instance = new SiteModel();
      SiteModel::$instance->login_form = new LoginForm();      
    }
    return SiteModel::$instance;
  }
}
