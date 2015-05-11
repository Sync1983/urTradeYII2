<?php
/**
 * @author Sync
 */
namespace app\controllers\actions\site;
use yii\base\Action;
use app\models\forms\SignUpForm;

class SignUpAction extends Action {
  const STAGE_INITIAL		  = 0;
  const STAGE_WAIT_MAIL		  = 1;
  const STAGE_VALIDATE_FORM	  = 2;
  const STAGE_MAIL_ANSWER	  = 3;
  
  public $stage;
  
  public function run() {
	if( $this->stage === self::STAGE_INITIAL ) {
	  return $this->stageInitial();
	} elseif ( $this->stage === self::STAGE_WAIT_MAIL ){
	  return $this->stageWaitMail();	
	} elseif ( $this->stage === self::STAGE_VALIDATE_FORM ){
	  return $this->stageFormValidate();
	} elseif ( $this->stage === self::STAGE_MAIL_ANSWER ){
	  return $this->stageMailAnswer();
	}
	echo $this->stage;	
  }
  //==================================================
  protected function stageInitial() {
	if (!\yii::$app->user->isGuest) {
	  return $this->controller->goHome();
    }
	
	$model = new SignUpForm();
	$model->key = \app\models\RegRecord::generateKey();
	
	return $this->controller->render('signup/initial',['model' => $model]);
  }
  
  protected function stageFormValidate() {
	$model = new SignUpForm();
	  
	if (\yii::$app->request->isAjax && $model->load(\yii::$app->request->post())) {
	  \yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
      return \yii\bootstrap\ActiveForm::validate($model);
	}
  }
  
  protected function stageWaitMail() {
	$model = new SignUpForm();
	
	if( !$model->load(\yii::$app->request->post()) || !$model->key || !$model->email ){
	  throw new \yii\web\BadRequestHttpException("Ошибка в формате запроса");
	}
	
	if( \app\models\RegRecord::checkKey($model->key) ){
	  throw new \yii\web\BadRequestHttpException("Повторный запрос регистрации");
	}
	
	$reg_record = new \app\models\RegRecord();
	$reg_record->key = $model->key;
	$reg_record->login	= $model->username;
	$reg_record->password = $model->userpass;
	$reg_record->time = time();
	$reg_record->was_send = false;
	$reg_record->mail = $model->email;
	if ( !$reg_record->save() ){
	  throw new \yii\web\BadRequestHttpException("Ошибка добавления пользователя");
	}
		
	return $this->controller->render('signup/wait_mail',['email' => $model->email]);	
  }
  
  protected function stageMailAnswer() {
	$key = \yii::$app->request->get('key',false);	
	if( !$key || !\app\models\RegRecord::checkKey($key) ){
	  throw new \yii\web\BadRequestHttpException("Ошибка идентификации");
	}
	
	$record = \app\models\RegRecord::findOne(['key'=>$key]);
	if( !$record ){
	  throw new \yii\web\BadRequestHttpException("Ошибка идентификации");
	}
	
	$login	= $record->getAttribute('login');
	$pass	= $record->getAttribute('password');
	
	$user = \app\models\MongoUser::createNew($login, $pass, $login);
	if( !$user ){
	  throw new \yii\web\BadRequestHttpException("Ошибка создания пользователя");
	}
	
	$record->delete();
	\yii::$app->user->login($user, 3600*24*30);
	return $this->controller->goHome();
  }
}
