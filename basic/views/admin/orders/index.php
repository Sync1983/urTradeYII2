<?php

/* @var $this yii\web\View */
/* @var $filter app\models\admin\orders\TypeFilter */
/* @var $item \app\models\admin\orders\UserTile */

$this->title = "Заказы";
$this->params['breadcrumbs'][] = $this->title;

?>

<ul class="tile">
  <?php foreach($orders as $item):
    $attrs = [
      "pay"   => $item->data->cnt_wait_pay       ,
      "w_plc" => $item->data->cnt_wait_placement ,      
      "plc"   => $item->data->cnt_placement      ,      
      "way"   => $item->data->cnt_in_way         ,      
      "stor"  => $item->data->cnt_in_storage     ,      
      "place" => $item->data->cnt_in_place       ,      
      "rej"   => $item->data->cnt_rejected       ,
      "action"=> yii\helpers\Url::to(['admin/user-order-open',"id"=>$item->user->getId()],true)
    ];
    $attrs_text = "";
    foreach ($attrs as $key=>$value){
      if( $value ){
        $attrs_text .= " $key=\"$value\"";
      }
    }    
    ?>
  <li <?= $attrs_text?> >
    <div class="tile-head"><span class="glyphicon glyphicon-circle-arrow-right pull-right" aria-hidden="true"></span></div>
    <h4>Имя:</h4>
    <p><?= $item->user->getUserName() ?></p>
    <h4>Название:</h4>
    <p><?= $item->user->name ?></p>
    <ul>
      <li>Ожидает оплаты: <?= $item->data->cnt_wait_pay       ?> </li>
      <li>Разместить:     <?= $item->data->cnt_wait_placement ?> </li>
      <li>Размещены:      <?= $item->data->cnt_placement      ?> </li>
      <li>В пути:         <?= $item->data->cnt_in_way         ?> </li>
      <li>На складе:      <?= $item->data->cnt_in_storage     ?> </li>
      <li>Выдано:         <?= $item->data->cnt_in_place       ?> </li>
      <li>Отказ:          <?= $item->data->cnt_rejected       ?> </li>
    </ul>
  </li>
  <?php endforeach;?>
</ul>

<?php
$js_text = <<<JS
 $('.tile').tiles({
   attributes:{
    pay: {
      name: 'Ожидает оплаты',
      button_text: $filter->cnt_wait_pay,
    },
    w_plc: {
      name: 'Разместить',
      button_text: $filter->cnt_wait_placement,
    },
    plc: {
      name: 'Размещено',
      button_text: $filter->cnt_placement,
    },
    way: {
      name: 'В пути',
      button_text: $filter->cnt_in_way,
    },
    stor: {
      name: 'На складе',
      button_text: $filter->cnt_in_storage,
    },
    place: {
      name: 'Выдано',
      button_text: $filter->cnt_in_place,
    },
    rej: {
      name: 'Отказ',
      button_text: $filter->cnt_rejected,
    },
  },
  line_len: 6
 });
JS;
$this->registerJs($js_text);?>


