<?php
/* @var $this yii\web\View */
//$this->title = 'My Yii Application';
use app\components\NewsWidget;
?>
<div class="site-index">
    <div class="body-content">
      <h3>Добро пожаловать на сайт АвтоТехСнаб - Вашего поставщика автомобильных запчастей.</h3>      
      <?= NewsWidget::widget() ?>
  
      
      <div class="row">
        <h4>Для наших клиентов мы предлагаем:</h4>
        <div class="col-lg-4">
          <img src="/img/qality.png" class="index-big-icon" />
          <p class="index-icon-text">Поставка оригинальных и не оригинальных запчастей</p>
        </div>
        <div class="col-lg-4">
          <img src="/img/partner.png" class="index-big-icon" />
          <p class="index-icon-text">Мы сотрудничаем с более чем 30−ю поставщиками из России, Европы, ОАЭ, Японии</p>
        </div>
        <div class="col-lg-4">
          <img src="/img/cert.png" class="index-big-icon" />
          <p class="index-icon-text">Сертификаты качества и гарантия на поставляемую продукцию</p>
        </div>
      </div>
      
      <div class="row">
        <div class="col-lg-6">
          <p style ="text-align: center;text-transform: uppercase;font: 14pt sans-serif;color: #0f547c;"><strong>ПЕРСОНАЛЬНОЕ ОБСЛУЖИВАНИЕ</strong></p>
          <ul class="index-list">
            <li><p>Каждый клиент получает персонального менеджера, который занимается всем процессом от получения заявки до выдачи заказа клиенту</p></li>
            <li><p>Максимальная помощь в подборе запчастей, расходных материалов, масел, смазок и спец. жидкостей</p></li>
            <li><p>Быстрый документооборот</p></li>
            <li><p>Бесплатную доставку запчастей по г. Пенза</p></li>
          </ul>
        </div>

        <div class="col-lg-6">
          
        </div>

    </div>
</div>