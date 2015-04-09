<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
/* @var $this yii\web\View */
/* @var $providers \app\models\search\SearchProviderFile */
$this->title = "Прайс-листы - успешная загрузка";
$this->params['breadcrumbs'][] = [
  'label' => 'Прайс-листы',
  'url' => ['admin/prices'],
  ];
$this->params['breadcrumbs'][] = $this->title;
?>

<h2>Файл успешно загружен</h2>
<h4>Загружаемый файл: <?= $upload_file ?></h4>
<h4>Размещен: <?= $file ?></h4>
