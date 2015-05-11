<?php
/**
 * @author Sync
 */
namespace app\controllers\actions\site;

use yii\base\Action;
use app\models\forms\SignUpTypeForm;
use app\models\forms\SignUpPrivateForm;
use app\models\forms\SignUpCompanyForm;

class ProfileAction extends Action {
  
  public function run() {	
	if ( $this->id === 'new-user' ){
	  return $this->actionNewUser();
	} elseif ( $this->id === 'new-user-last-step' ){
	  return $this->actionNewUserLastStep();
	} elseif ( $this->id === 'profile-discard' ){
	  return $this->actionProfileDiscard();	
	} elseif ( $this->id === 'profile-save' ){
	  return $this->actionProfileSave();
	} elseif ( $this->id === 'profile-validate' ){
	  return $this->actionVaildate();
	}	
  }
  
  //=============================================================================================
  
  protected function actionNewUser(){
	if( \yii::$app->user->isGuest ) {
	  return $this->controller->goHome();
	}
	
	$user = \yii::$app->user->identity;
	
	$model = new SignUpTypeForm();
	$model->type = $user->getAttribute('type');
	
	return $this->controller->render('profile/new_user',['model'=>$model]);	
  }
  
  protected function actionNewUserLastStep(){
	if( \yii::$app->user->isGuest ) {
	  return $this->controller->goHome();
	}
	
	$model = new SignUpTypeForm();
	if( $model->load(\yii::$app->request->post()) && $model->validate() ) {
	  /* @var $user \app\models\MongoUser */
	  $user = \yii::$app->user->identity;
	  $user->setAttribute('type', $model->type);
	  $user->save();
	  if( $user->type === 'company'){
		
		$form = new SignUpCompanyForm();
		$form->setAttributes($user->getAttributes(),false);
		return $this->controller->render('profile/company',['model'=>$form]);
		
	  }else {
		
		$form = new SignUpPrivateForm();
		$form->setAttributes($user->getAttributes(),false);
		return $this->controller->render('profile/private',['model'=>$form]);
		
	  }
	}
	//If was error	
	return $this->controller->render('profile/new_user',['model'=>$model]);	
  }
  
  protected function actionProfileSave() {
	/* @var $user \app\models\MongoUser */
	$user = \yii::$app->user->identity;
	if( $user->getAttribute('type') === 'private' ){
	  $model = new SignUpPrivateForm();
	} elseif( $user->getAttribute('type') === 'company' ){
	  $model = new SignUpCompanyForm();	  
	}
	
	if( $model->load(\yii::$app->request->post()) && $model->validate() ){
	  $user->setAttributes($model->getAttributes());
	  $user->setAttribute('is_init',true);
	  $user->save();
	  return $this->controller->goHome();	  
	}
	return $this->controller->render('profile/new_user',['model'=>$model]);	
  }
  
  protected function actionProfileDiscard() {
	\yii::$app->session->set('ProfileRequestDiscard',true);
	return $this->controller->goHome();	
  }
  
  protected function actionVaildate() {
	/* @var $user \app\models\MongoUser */
	$user = \yii::$app->user->identity;
	if( $user->getAttribute('type')==='private' ){
	  $model = new SignUpPrivateForm();
	} elseif( $user->getAttribute('type')==='company' ){
	  $model = new SignUpCompanyForm();	  
	}
	
	if (\yii::$app->request->isAjax && $model->load(\yii::$app->request->post())) {
	  \yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
      return \yii\bootstrap\ActiveForm::validate($model);
	}
  }
  
}
