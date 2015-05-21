<?php

/**
 * @author Sync
 */

namespace app\models\pays;
use yii\base\Model;

class YaPayType extends Model{
  public $name = "Yandex-Money";
  public $invoiceId;
  
}
