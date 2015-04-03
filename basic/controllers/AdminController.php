<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\news\NewsModel;


class AdminController extends Controller
{
  public $layout = 'admin';
  
  public function behaviors(){
      return [];
  }

  public function actions(){
    return [
      /*'error' => [
        'class' => 'yii\web\ErrorAction',
      ] */           
    ];
  }

  public function actionIndex() {     
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
    $user->setAttributes($model->getAttributes());    
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

}
