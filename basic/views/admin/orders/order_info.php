<?php

/* @var $this yii\web\View */
/* @var $order app\models\orders\OrderRecord */
/* @var $providers \app\models\search\SearchModel */
/* @var $user app\models\MongoUser */
?>
<div class = "admin-order-info">
  
  <div class="admin-order-info-main">
    <h4>Основные</h4>
    <span>ID:             <div class="info"><?= $order->_id ?></div></span>
    <span>Запрос поиска:  <div class="info"><?= $order->search_articul ?></div></span>
    <span>Найденный артикул:  <div class="info"><?= $order->articul ?></div></span>
    <span>Производитель:  <div class="info"><?= $order->producer ?></div></span>
    <span>Название:  <div class="info"><?= $order->name ?></div></span>
    <span>На складе:  <div class="info"><?= $order->count ?> шт.</div></span>
    <span>Оригинал:  <div class="info"><?= $order->is_original?"Да":"Нет"?></div></span>    
  </div>
  <div class="admin-order-info-data">
    <h4>Данные заказа</h4>
    <span>Поставщик:  <div class="info"><?= $providers->getProviderByCLSID($order->provider)->getName()?></div></span>
    <span>Сток:  <div class="info"><?= $order->stock?></div></span>
    <span>Доп. инфо:  <div class="info"><?= $order->info?></div></span>
    <span>ID производителя:  <div class="info"><?= $order->maker_id?></div></span>
    <span>Комментарий:  <div class="info"><?= $order->comment ?></div></span>    
  </div>
  <div class="admin-order-info-price">
    <h4>Цена и количество</h4>
    <span>Наша цена:  <div class="info"><?= $order->price ?></div></span>
    <span>Сумма:  <div class="info"><?= $order->price * $order->sell_count ?></div></span>
    <?php if( $user ): 
      $user_price = $user->getUserPrice($order->price);
      ?>
    <span>Цена пользователя:  <div class="info"><?= $user_price ?></div></span>      
    <span>Сумма пользователя:  <div class="info"><?= $user_price * $order->sell_count ?></div></span>      
    <?php endif;?>
    
    <?php if( $order->price_change ): ?>
    <span>Варианты цен:  <div class="info">
      <?= $order->price-$order->price*0.1 ?>->
      <?= $order->price ?><-
      <?= $order->price+$order->price*0.1 ?></div></span>
    <?php endif;?>
    <?php if( $order->price_change && $user): ?>
    <span>Варианты цен пользователя:  <div class="info">
      <?= $user_price - $user_price*0.1 ?>->
      <?= $user_price ?><-
      <?= $user_price + $user_price*0.1 ?></div></span>
    <?php endif;?>    
    <span>Время доставки:  <div class="info"><?= $order->shiping?> дн.</div></span>
    <span>Упаковка:  <div class="info"><?= $order->lot_quantity?></div></span>
    <span>Количество:  <div class="info"><?= $order->sell_count ?></div></span>
    <span>Оплачено:  <div class="info"><?= $order->pay?"Да":"Нет" ?></div></span>    
    <span>Был запрос оплаты:  <div class="info"><?= $order->pay_request?"Да":"Нет" ?></div></span>    
    <span>Дата оплаты:  <div class="info"><?= date("d-m-Y H:i:s",$order->pay_time) ?></div></span>    
    <span>Cумма оплаты:  <div class="info"><?= $order->pay_value ?></div></span>    
  </div>
  
  <div class="admin-order-info-sum">
    <h4>Общая информация</h4>
    <span>Обновление информации:  <div class="info"><?= date("d-m-Y H:i:s",$order->update_time) ?></div></span>    
    <span>Дата ожидания:  <div class="info"><?= date("d-m-Y H:i:s",$order->wait_time) ?></div></span>    
    <span>Статус:  <div class="info"><?= $order->textStatus() ?></div></span>
    <?php if ( $user ):?>
    <span>Пользователь:  <div class="info"><?= $user->getUserName()." : ".$user->name ?></div></span>    
    <?php endif;?>
  </div>

</div>

