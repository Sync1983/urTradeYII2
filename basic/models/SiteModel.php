<?php
/**
 * Description of SiteModel
 *
 * @author Sync<atc58.ru>
 */
namespace app\models;

use yii\base\Model;
use app\models\LoginForm;
use app\models\prices\OverpriceModel;

class SiteModel extends Model {
  /** @var $instance SiteModel **/
  static protected $instance;
  
  public $search  = "";
  public $cross   = 1;
  public $op      = 0;
  public $login_form;
  public $over_price = [];
  
  public function attributes() {
    return ['search','cross','op'];
  }
  
  public function rules() {
    return [
      ['search','string'],
      ['cross','boolean'],
      ['op','integer'],
    ];
  }

    /**
   * Возвращает подготовленный массив наценок
   * @return array
   */
  public function generateOverPrice(){  
    $res = [];
    $res[0] = "Без наценки";
    foreach ($this->over_price as $key => $value){
      $res[$value] = "$key ( $value % )";
    }
    return $res;
  }

  /** @return SiteModel **/
  static public function _instance(){
    if(!SiteModel::$instance){
      SiteModel::$instance = new SiteModel();
      SiteModel::$instance->login_form = new LoginForm();      
      SiteModel::$instance->over_price = (new OverpriceModel())->prices;
    }
    return SiteModel::$instance;
  }
}
