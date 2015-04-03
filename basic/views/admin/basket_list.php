<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
$this->title = "Выбор пользователя для обображения корзины";
$this->params['breadcrumbs'][] = $this->title;

/* @var $user \app\models\MongoUser */
$list = [];
foreach( $users as $user ){
  $name = $user->getUserName()." : ".$user->getAttribute("name");
  $id   = strval($user->getAttribute("_id"));
  $list[$id] = $name;
}
echo '<label class="control-label">Пользователь</label>';
echo kartik\select2\Select2::widget([
  'name' => 'id-selector',
  'data' => $list,
  'options' => [
    'placeholder' => 'Выберите пользователя ...', 
  ],
  'pluginOptions' => [
    'allowClear' => true
  ],
  'size'=>  \kartik\select2\Select2::LARGE,
  'addon' => [
    'append' => [
      'content' => Html::button("Открыть", [
      'class' => 'btn btn-primary',
      'title' => 'Открыть корзину пользователя',
      'data-toggle' => 'tooltip',
      'onClick' => 'openId()'
    ]),
    'asButton' => true,
    ]
  ]
]);

?>

<script>
  function openId(){
    var id = $("select[name='id-selector']").val();
    window.location = window.location + "&id="+id;
    return false;
  }
</script>



  
