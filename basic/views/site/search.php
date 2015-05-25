<?php
use yii\bootstrap\Collapse;
use app\models\search\SearchModel;
use yii\web\View;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\checkbox\CheckboxX;
use app\models\forms\BasketAddForm;
use app\assets\SearchTableAsset;

SearchTableAsset::register($this);

$this->title = 'Поиск';
$this->params['breadcrumbs'][] = $this->title;

/* @var $search_model SearchModel */
/* @var $this View */

$model = new BasketAddForm();
$makers = $this->params['search_model']->generateMakerList();

$items = [];
foreach ($makers as $maker => $json){  
  $items[] = [
    'label' => $maker."",
    'content' => "<div class=\"best-var\">Лучшие варианты деталей производителя $maker</div>"
	  . "<script type=\"text/json\">$json</script>"
	  . "<table class=\"out-data\"></table> "
	  . "<a class=\"show-full\" href=\"#\">Показать полный список...</a>",
  ];  
}

if(count($items)==0){
  echo "По Вашему запросу ничего не найдено!";
} else {
  echo Collapse::widget(['items' => $items]);
}
SearchTableAsset::initCollapse();

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
<div id="count-request" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
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
      
      <div><?= $form->field($model, "price_change")->widget(CheckboxX::classname(), [
                'value' => false,
                'pluginOptions'=>['threeState'=>false]]); ?></div>
	  
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

<div id="full-list" class="modal" >
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Полный список деталей</h4>
      </div>
      <div class="modal-body">
        <table class="out-data"></table>
      </div>
    </div>
  </div>
</div>
