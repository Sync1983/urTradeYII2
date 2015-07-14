<?php
/* @var $this \yii\web\View */
/* @var $model \app\models\forms\CatalogCreateForm */
/* @var $form \yii\bootstrap\ActiveForm */

$this->title = "Каталог-создание";
$this->params['breadcrumbs'][] = $this->title;
?>
<h2>Создание нового каталога</h2>
<?php
$form = \yii\bootstrap\ActiveForm::begin([
    'id'              => 'catalog-create-form',
		'method'				  => 'POST',
		'action'				  => \yii\helpers\Url::to(['admin/catalog-create','finish'=>true]),
		'layout'				  => 'horizontal',
]);

echo $form->field($model, "name")->input("text")->label()->error()->hint("Максимальная длина 30 символов");
echo $form->field($model, "visible")->checkbox()->label()->error()->hint("Управляет видимостью каталога для пользователя");
?>
<p class="text-center">Поля каталога</p>
<?php
echo \kartik\grid\GridView::widget([
  'dataProvider'=> $data,
  'responsive'  => true,
  'hover'       => true,
  'columns'     => [
    [
      'class' => '\kartik\grid\SerialColumn'
    ],
    [
      'class' => kartik\grid\DataColumn::className(),
      'attribute' => 'position',
    ],
    [
      'class' => kartik\grid\DataColumn::className(),
      'attribute' => 'attr',
    ],
    [
      'class' => kartik\grid\DataColumn::className(),
      'attribute' => 'name',
    ],
    [
      'class' => kartik\grid\DataColumn::className(),
      'attribute' => 'type',
    ],
    [
      'class' => kartik\grid\DataColumn::className(),
      'attribute' => 'visible',
    ],
    [
      'class' => kartik\grid\DataColumn::className(),
      'attribute' => 'filter',
    ],
  ],
]);

\yii\bootstrap\ActiveForm::end();


