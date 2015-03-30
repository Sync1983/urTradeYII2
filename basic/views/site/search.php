<?php
use yii\bootstrap\Collapse;
use app\models\search\SearchModel;
use yii\web\View;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\checkbox\CheckboxX;
use app\models\forms\BasketAddForm;

$model = new BasketAddForm();

$this->title = 'Поиск';
$this->params['breadcrumbs'][] = $this->title;
/* @var  $search_model SearchModel */
/* @var $this View */
$this->registerCssFile("/css/dataTables.css");
$this->registerJsFile("/js/jquery.dataTables.min.js", ['depends'=>'yii\web\JqueryAsset']);

$maker = $this->params['search_model']->generateMakerList();
ksort($maker,SORT_STRING);

$items = [];

foreach ($maker as $key => $providers) {
  $ids = [];
  foreach ($providers as $id=>$maker_id){
    if(($id=="")||($maker_id=="")){
      continue;
    }
    $ids[] = "$id:'$maker_id'";
  }
  $ids_line = "{".implode(",", $ids)."}";
  if(($key=="")||($ids_line=="{}")){
    continue;
  }
  $html_table_key = md5($key);
  $items[] = [
    'label' => $key."",
    'content' => "Лучшие варианты деталей производителя $key<table id=\"out-data-$html_table_key\" class=\"out-data\"></table> <a href=\"#\">Показать полный список...</a>",
    'options' => [
      'onclick'   => "main.loadPartList(this,$ids_line,'out-data-$html_table_key')",
      'class'     => 'provider-header'
    ]
    ];
}
/*if(count($items)==1){
  $items[0]['contentOptions'] = ['class' => 'in'];
}*/
if(count($items)==0){
  echo "По Вашему запросу \"$model->search\" ничего не найдено!";
} else {
  echo Collapse::widget(['items' => $items]);
}

$this->registerJsFile("/js/search_tables_init.js", ['depends'=>'/js/jquery.dataTables.min.js']);
$this->registerJs("".
 "$('body').on('beforeSubmit', 'form#basket-add-form', function() {
    var form = $(this);
    if (form.find('.has-error').length) {
      return false;
    }

    main.ajax(form.attr('action'),form.serialize(),function(answer){
      $('#count-request').modal('hide');
    });

    return false;
  });");
?>
<div id="count-request" class="modal fade" tabindex="-1" role="dialog"aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Добавить в корзину</h4>
      </div>
      <div class="modal-body">
      <?php /* @var $form ActiveForm */
      $form = ActiveForm::begin([              
              'id'             => 'basket-add-form',
              'action'         => ['basket/add-to'],
              'validationUrl'  => ['basket/add-validate'],
              'enableAjaxValidation'  =>true,
              'enableClientValidation' => true,      
              'validateOnChange'=> true,
              'validateOnSubmit' => true
       ]);?>      
      <div>
        <h4>Вы хотите добавить в корзину:</h4>
        <p id="add-describe" class="basket-add-describe">Описание</p>
      </div>
      <div>
        <h4>Деталь поставляется в количестве кратном:</h4>
        <p id="add-step" class="basket-add-step">шт.</p>
      </div>      
        
      <div><?= $form->field($model, "count")->input("number",['min'=>0,'style'=>'width:200px'])->error();?></div>
      
      <div>        
        <?= $form->field($model, "price_change")->widget(CheckboxX::classname(), [
                'value' => false,
                'pluginOptions'=>['threeState'=>false]]); ?> 
        
      </div>
        <div class="basket-info">
        <h4>Обратите внимание:</h4>
        <p id="add-info" class="basket-add-info text-warning">Информация</p>
      </div>
      <?= Html::activeHiddenInput($model,"id");?>
      <div class="modal-footer">
        <?= Html::submitButton("Добавить",['class'=>'btn btn-info']); ?>
        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
      </div>
      <?php ActiveForm::end()?>      
      </div>      
    </div>
  </div> 
</div>