<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
/* @var $this yii\web\View */
/* @var $providers \app\models\search\SearchProviderFile */
$this->title = "Прайс-листы";
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="provider-list">  
<?php
foreach($providers as $provider):
  $name_date = $provider->getLastFileNameDate();
?>
  <div class="provider-record">    
    <span class="provider-name">Провайдер:<h4><?= $provider->getName()?></h4></span>
    <span>Дата изменения:<h5><?= $name_date['time']?></h5></span>
    <span>Файл:<h5><?= $name_date['path']?></h5></span>
    <?php $form = ActiveForm::begin([
        'action' => Url::to(['admin/price-upload']),
        'options'=>['enctype'=>'multipart/form-data'] 
      ]);
      echo Html::hiddenInput('clsid', $provider->getCLSID());
      echo Html::fileInput("file");
      echo Html::submitButton("Загрузить",['class'=>'btn btn-info']);
      ActiveForm::end();
    ?>  
  </div>
<?php endforeach; ?>
</div>
