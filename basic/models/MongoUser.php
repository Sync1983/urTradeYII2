<?php
/**
 * Description of MongoUser
 *
 * @author Sync<atc58.ru>
 */
use app\models;
use yii\web\IdentityInterface;
use yii\mongodb\ActiveRecord;

class MongoUser extends yii\mongodb\file\ActiveRecord implements \yii\web\IdentityInterface {
  
  public function 

  // =================== Interface ====================
  public function getAuthKey() {
    echo "get Key";
  }

  public function getId() {
    echo "get ID";
  }

  public function validateAuthKey($authKey) {
    echo "valid AK";
  }

  public static function findIdentity($id) {
    echo "find ID";
  }

  public static function findIdentityByAccessToken($token, $type = null) {
    echo "find id by AT";
  }
}
