<?php

namespace app\controllers;

use yii\web\Controller;

class AdminController extends Controller
{
  public $layout = 'admin';
  public $notify = ['admin'];
  
  public function behaviors(){
      return [
        \app\components\behaviors\AdminBehavior::className(),
      ];
  }

  public function actions(){
    return [
      'error' => [
        'class' => 'yii\web\ErrorAction',
      ],
      'database-info' => [
        'class' => actions\admin\DatabaseInfoAction::className(),
      ],
      'user-info' => [
        'class' => actions\admin\UserInfoAction::className(),
      ],
      'user-order' => [
        'class' => actions\admin\UserOrderAction::className(),
        'type'  => actions\admin\UserOrderAction::TYPE_INFO
      ],
      'user-order-open' => [
        'class' => actions\admin\UserOrderAction::className(),
        'type'  => actions\admin\UserOrderAction::TYPE_USER
      ],
      'order-info' => [
        'class' => actions\admin\UserOrderAction::className(),
        'type'  => actions\admin\UserOrderAction::TYPE_EXTEND
      ],
      'order-change' => [
        'class' => actions\admin\UserOrderAction::className(),
        'type'  => actions\admin\UserOrderAction::TYPE_CHANGE
      ],
      'catalog-create' => [
        'class' => actions\admin\CatalogAction::className(),
        'type'  => actions\admin\CatalogAction::TYPE_CREATE
      ],
      'catalog-control' => [
        'class' => actions\admin\CatalogAction::className(),
        'type'  => actions\admin\CatalogAction::TYPE_CONTROL
      ],
      'catalog-fill' => [
        'class' => actions\admin\CatalogAction::className(),
        'type'  => actions\admin\CatalogAction::TYPE_FILL
      ],
      'cars-control' =>[
        'class' => actions\admin\CarsAction::className()
      ],
      /*'car-selector' => [
        'class' => \app\widgets\CarSelector\action\CarSelectorAction::className(),
      ]*/
    ];
  }

  public function actionIndex() {     
    $this->addNotification("blabla-bla");
    $this->addNotification("blabla-bla2");
    return $this->render('index');
  }
  
  public function actionUsers(){
    $users = \app\models\MongoUser::find()->all();
    $list = new \app\models\BasketDataProvider([
      'allModels'   => $users,
        'pagination'  => new \yii\data\Pagination([
          'totalCount'  => count($users),
          'pageSize'        => 40,
        ]),
    ]);
    return $this->render('users',['list'=>$list]);
  }
  
  public function actionUserExpand(){
    $id = \yii::$app->request->post('expandRowKey',false);    
    if( !$id ){
      return "Ошибка";
    }
    $user = \app\models\MongoUser::findOne(['_id' => new \MongoId($id)]);    
    $form = new \app\models\admin\forms\AdminUserForm();
    $form->setAttributes($user->getAttributes());    
    return $this->renderAjax('forms/admin_user',['form'=>$form]);
  }
  
  public function actionUserAjaxValidate(){
    $ajax = \yii::$app->request->post("ajax",false);
    if(!$ajax){
      throw new \yii\base\Exception("Неверный тип запроса");      
    }
    $model = new \app\models\admin\forms\AdminUserForm();
    if(!$model->load(\yii::$app->request->post())){
      throw new \yii\base\Exception("Ошибка данных");      
    }
    \yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    return \yii\bootstrap\ActiveForm::validate($model);
  }
  
  public function actionUserAjaxChange(){
    $model = new \app\models\admin\forms\AdminUserForm();
    if( !$model->load(\yii::$app->request->post()) || !$model->validate() ){
      throw new \yii\base\Exception($model->getErrors());      
    }
    $user = \app\models\MongoUser::findOne(['_id' => new \MongoId($model->_id)]);
    if( !$user ){
      throw new \yii\base\Exception("Пользователь не найден");      
    }  
    $attributes = $model->getAttributes();  
    if( isset($attributes['user_pass']) ){
      $attributes['user_pass'] = md5($attributes['user_pass']);
    }
    $user->setAttributes($attributes);    
    if( !$user->save() ){
      throw new \yii\base\Exception("Ошибка в сохранении");      
    }
    return $this->redirect(['admin/users']);
  }
  
  public function actionGetMd5(){
    $key = \yii::$app->request->get("key",false);
    if( !$key ){
      echo "error";
    }
    echo md5($key);
    return;
  }
  
  public function actionUserAdd(){
    $new_user = \app\models\MongoUser::createNew("NewUser", "new_user", "Новый пользователь");
    if( $new_user ){
      return $this->redirect(['admin/users']);
    }
    throw new \yii\web\BadRequestHttpException("Create error");
  }
  
  public function actionUserBasket(){
    $id = \yii::$app->request->get('id',false);
    
    if( !$id ){
      $users = \app\models\MongoUser::find()->all();
      return $this->render("basket_list",['users'=>$users]);
    }
    /* @var $user \app\models\MongoUser */
    $user = \app\models\MongoUser::findOne(['_id' => new \MongoId($id)]);
    if( !$user ){
      throw new \yii\web\BadRequestHttpException("Пользователь не найден");
    }    
    $basket = new \app\models\basket\BasketModel();
    $basket->setList($user->getAttribute('basket'));
    $list = new \app\models\BasketDataProvider([
      'allModels'   => $basket->getRawList(),
        'pagination'  => new \yii\data\Pagination([
          'totalCount'  => count($basket->getList()),
          'pageSize'        => 40,
        ]),
    ]);
    return $this->render('basket',['list'=>$list,'user'=>$user]);    
  }
  
  public function actionPrices(){
    $providers = $this->getFileProviders();
    return $this->render('prices',['providers'=>$providers]);        
  }
  
  public function actionPriceUpload(){
    $clsid = \yii::$app->request->post('clsid',false);
    $file = \yii\web\UploadedFile::getInstanceByName('file');
	
    if( !$clsid || !$file ){
      throw new \yii\web\NotFoundHttpException("Параметры загружаемого файла не указаны");
    }
    $ext = $file->getExtension();
    if( !in_array($ext, ['zip','csv','rar']) ){
      throw new \yii\web\NotFoundHttpException("Неверное расширение файла: $ext");
    }
    $providers = $this->getFileProviders();
    if( !isset($providers[$clsid]) ){
      throw new \yii\web\NotFoundHttpException("Указанный поставщик не найден");      
    }
    /* @var $provider \app\models\search\SearchProviderFile */
    $provider = $providers[$clsid];
    $dir = $provider->getDir();
    $new_name = date("Y_m_d_h_i_s", time());
    if( !$file->saveAs($dir."/".$new_name.".".$ext) ){
      throw new \yii\web\NotFoundHttpException("Ошибка при сохранении файла: ".$file->error);
    }
    return $this->render("upload_success",[
      "upload_file"=>$file->getBaseName().".".$ext,
      "file"=>$dir."/".$new_name.".".$ext]);
  }

  /**
   * Возвращает список поставщиков с данными из файла
   * @return \app\models\search\SearchProviderFile
   */
  protected function getFileProviders(){
    if( !isset(\yii::$app->params['providerUse']) ){
      return [];
    }
    $param = \yii::$app->params['providerUse'];
    $default_data = \yii::$app->params['providers'];
    if( !is_array($param) ){
      return [];
    }
    $answer = [];
    foreach ( $param as $provider ){
      $default = [];
      if(isset($default_data[$provider])){
        $default = $default_data[$provider];
      }      
      $class = \yii::createObject($provider,[$default,[]]);
      if($class instanceof \app\models\search\SearchProviderFile) {
        $answer[$class->getCLSID()] = $class;
      }
    }
    return $answer;
  }
  
  protected function addNotification($message){
    $this->view->params['notify'][] = $message;
  }

}
