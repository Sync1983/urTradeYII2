<?php
use kartik\checkbox\CheckboxX;
use kartik\editable\Editable;
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
<table id="fields-table" class="fields-table">
  <thead>
    <tr>      
      <th style="width: 3%">Позиция</th>
      <th>Имя аттрибута</th>
      <th>Имя отображения</th>
      <th style="width: 20%">Тип</th>
      <th style="width: 10%">Видимое</th>
      <th style="width: 10%">Фильтр по полю</th>
      <th style="width: 10%"></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($data as $row):?>
    <tr>
      <td><?= $row['position'] ?></td>
      <td><?= $row['attr'] ?></td>
      <td><?= $row['name'] ?></td>
      <td><?= Editable::widget([
                'name'=>'type',
                'asPopover' => true,
                'value' => $row['type'],
                'header' => 'Тип поля',
                'size'=>'md',
                'format' => Editable::FORMAT_BUTTON,
                'inputType' => Editable::INPUT_DROPDOWN_LIST,
                'data'=>['string'=>'Строка'],
              ]);?>
      </td>
      <td><?= CheckboxX::widget([
              'name' => 'visible',
              'value'=> $row['visible'],
              'pluginOptions'=>['threeState'=>false]
            ]); ?>
      </td>
      <td><?= CheckboxX::widget([
              'name' => 'visible',
              'value'=> $row['filter'],
              'pluginOptions'=>['threeState'=>false]
            ]); ?>
      </td>
      <td>Дел</td>
    </tr>
    <?php endforeach;?>
    <tr>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td>Добавить</td>      
    </tr>
  </tbody>
</table>

<?php \yii\bootstrap\ActiveForm::end();


