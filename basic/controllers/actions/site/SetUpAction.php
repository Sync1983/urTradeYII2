<?php
/**
 * @author Sync
 */
namespace app\controllers\actions\site;
use yii\base\Action;

class SetUpAction extends Action {
  public $type = "setup";
  /* @var $_user app\models\MongoUser */
  protected $_user;
  protected $_prices;
  protected $_model;


  public function run() {
    if( \yii::$app->user->isGuest ){
      return $this->controller->goHome();
    }

    if( $this->type == "prices" ){
      $this->setPrices();
      return $this->controller->redirect(['setup']);
    }

    if ( $this->_model->load( \yii::$app->request->post() ) && $this->_model->validate() ) {
        $this->_user->setAttributes( $this->_model->getAttributes() );
        $this->_user->save();
    }

    return $this->controller->render('setup',['model' => $this->_model, 'prices' => $this->_prices]);
  }

  public function init() {
    $this->_user = \yii::$app->user->getIdentity();
    $this->_model= new \app\models\forms\SetupModel();

    if( \yii::$app->user->isGuest ){
      return parent::init();
    }
    
    $this->_model->setAttributes( $this->_user->getAttributes() );
    $this->_model->id = $this->_user->getId();
    $this->_prices = $this->_user->getOverPiceList();
    return parent::init();
  }

  public function setPrices(){
    $name   = \yii::$app->request->post('name',[]);
    $value  = \yii::$app->request->post('value',[]);

    //Выбираем минимальную длину в массивах названий и значений
    $len = min([count($name),count($value)]);

    $items = [];
    for($i = 0; $i<$len; $i++){
      $items[$name[$i]] = $value[$i];
    }

    \yii::$app->user->saveOverPriceList($items);
    
    return true;
  }
  
}
