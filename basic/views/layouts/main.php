<?php

use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use yii\helpers\Url;
use app\assets\AppAsset;
use app\assets\BootboxAsset;
use app\assets\NotifyAsset;
use app\assets\SelectorAsset;
use yii\bootstrap\ActiveForm;

/* @var $this \yii\web\View */
/* @var $content string */
$this->title = "АвтоТехСнаб - Ваш поставщик запчастей";

AppAsset::register($this);
NotifyAsset::register($this);
SelectorAsset::register($this);

BootboxAsset::overrideSystemConfirm();
SelectorAsset::registerJS();

/* @var $user SiteUser */
$user = Yii::$app->user;
$basket_count = $this->params['basket_count'];
$order_count  = $this->params['order_count'];
$balance      = $this->params['balance'];
$this->registerJs("var isGuest=".$user->isGuest);
$this->registerJs("$().main(\"init\")");
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
  <head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name='yandex-verification' content='5d5d11ea4a8128ae' />
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
  </head>
  <body>
<?php $this->beginBody() ?>
    <div class="wrap resizeble">
      <?php app\components\MenuWidget::begin([
        'default_over_price' => \yii::$app->request->get("op",0),
        'menu' => [
          [ 'url'   => ['basket/index'],
            'title' => 'Корзина',
            'descr' => 'Cохраненные детали для заказа',
            'img'   => '/img/basket_icon.png',
          ],
          [ 'url'   => ['order/index'],
            'title' => 'Заказы',
            'descr' => 'Детали в заказе и состояние по ним',
            'img'   => '/img/order_icon.png',
          ],
          [ 'url'   => ['balance/index'],
            'title' => 'Баланс',
            'descr' => 'История платежей и расшифровка списаний',
            'img'   => '/img/balance_icon.png',
          ],
          [ 'url'   => ['site/setup'],
            'title' => 'Профиль',
            'descr' => 'Учетная запись и дополнительная информация',
            'img'   => '/img/setup_icon.png',
          ],
          [ 'url'   => ['site/consumers'],
            'title' => 'Клиентам',
            'descr' => 'Предложения по сотрудничеству',
            'img'   => '/img/consumer_icon.png',
          ],
          [ 'url'   => ['site/contact'],
            'title' => 'Контакты',
            'descr' => 'Список контактов и способов для связи',
            'img'   => '/img/contact_icon.png',
          ],
                
        ]
      ]);
        app\components\MenuWidget::end();
      ?>

      <div class="container">
      <?= Breadcrumbs::widget(['links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],])?>
		
      <?= $content ?>
      </div>      
    </div>

    <footer class="footer resizeble">
      <p class="pull-left">&copy; AвтоТехСнаб <?= date('Y') ?></p>
      <div class="pull-right" style="padding-right: 10px;">
      <ul class="pay-icons">
        <li><?= \yii\helpers\Html::img('/img/pay_icon/yandexmoney.png',   ['alt'=>'Яндекс.Деньги',    'title' => 'Яндекс.Деньги'])    ?></li>
			  <li><?= \yii\helpers\Html::img('/img/pay_icon/cards.png',         ['alt'=>'Банковская карта', 'title' => 'Банковская карта']) ?></li>
			  <li><?= \yii\helpers\Html::img('/img/pay_icon/webmoney-white.png',['alt'=>'WebMoney',         'title' => 'WebMoney'])         ?></li>
			  <li><?= \yii\helpers\Html::img('/img/pay_icon/alfabank-white.png',['alt'=>'Альфа-Клик',       'title' => 'Альфа-Клик'])       ?></li>
			  <li><?= \yii\helpers\Html::img('/img/pay_icon/cash_rub.png',      ['alt'=>'Кассы и терминалы','title'=>'Кассы и терминалы'])  ?></li>
			  <li><?= \yii\helpers\Html::img('/img/pay_icon/masterpass.png',    ['alt'=>'MasterPass',       'title'=>'MasterPass'])         ?></li>
      </ul>
      <p> <?= Yii::powered() ?></p>
      </div>
          
      <div class="locator">
        <ul>
          <li><a href="<?=  Url::home() ?>">Главная</a></li>
          <li>
            <ul>
              <li><a href="<?=  Url::to(['basket/index']); ?>">Корзина</a></li>
              <li><a href="<?=  Url::to(['order/index']) ?>">Заказы</a></li>
            </ul>
          </li>
          <li><a href="<?=  Url::to(['balance/index']) ?>">Баланс</a></li>
          <li>
            <ul>
              <li><a href="<?=  Url::to(['site/consumers']) ?>">Клиентам</a></li>
              <li><a href="<?=  Url::to(['site/contact']) ?>">Контакты</a></li>
            </ul>
          </li>
        </ul>
      </div>
    </footer>

<?php $this->endBody() ?>
    
	<!-- Special Area -->
	<div class="preloader">&nbsp;</div>
	<!-- Login form -->
	<div id="loginModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="Войти" aria-hidden="true">>
	  <div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title" id="myModalLabel">Войти</h4>
		  </div>
		  <div class="modal-body">
<?php
ActiveForm::begin([
		'id'	 => 'login-form',
		'action' => ['site/login']
]);
?>
			<div class = "row">
<?= Html::label("Ваш логин:", 'username', ['class'=>'col-md-offset-2 col-md-3']); ?>
<?= Html::input("text", "username", "", ['id' => "user-name", 'class'=>'col-md-4']); ?>
			</div>
			<div class="row">
<?= Html::label("Пароль:", 'userpass', ['class'=>'col-md-offset-2 col-md-3']); ?>    
<?= Html::input("password", "userpass", "", ['id' => "user-pass", 'class'=>'col-md-4']); ?>
			</div>
			<div class="row">
<?= Html::label("Запомнить:", 'rememberMe', ['class'=>'col-md-offset-2 col-md-3']); ?>
<?= Html::checkbox("rememberMe"); ?>
			</div>
			<div class="row" style="margin-top:10px;">
<?= Html::submitButton("Войти", ['class' => 'btn btn-info col-md-offset-7 col-md-2']); ?>
<?php ActiveForm::end() ?>      
			</div>
		  </div>
		  <div class="modal-footer">
			<ul class="socnet-list col-md-offset-4 col-md-4">			  
			  <li><span class="icon fb-icon"></span>
				<?= Html::a("Войти через Facebook", Url::to(["soclogin/login", "net" => "fb"])); ?></li>
			  <li>
				<span class="icon vk-icon"></span>
				<?= Html::a("Войти через VKontakte", Url::to(["soclogin/login", "net" => "vk"])); ?></li>          
			</ul>      
		  </div>
		</div>
	  </div> 
	</div>

	<div class='notifications bottom-right'></div> 
  </body>
</html>

<?php $this->endPage() ?>
