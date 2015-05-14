<?php

use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use yii\helpers\Url;
use app\assets\AppAsset;
use app\assets\BootboxAsset;
use app\assets\NotifyAsset;
use app\assets\SelectorAsset;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\ButtonDropdown;
use app\models\SearchHistoryRecord;
use kartik\checkbox\CheckboxX;

/* @var $this \yii\web\View */
/* @var $content string */
$this->title = "АвтоТехСнаб - Ваш поставщик запчастей";
$this->registerJs('main.init(' . ((Yii::$app->user->isGuest) * 1) . ');');

AppAsset::register($this);
NotifyAsset::register($this);
SelectorAsset::register($this);
BootboxAsset::overrideSystemConfirm();
SelectorAsset::registerJS();

/* @var $user SiteUser */
$user = Yii::$app->user;
/* @var $form SearchForm */
$form = $this->params['search_model'];
$basket_count = $this->params['basket_count'];
$order_count = $this->params['order_count'];
$balance = $this->params['balance'];
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
    <div class="wrap"> 
      <nav class="navbar-1">
        <a class="navbar-header" href="<?= Url::home(); ?>">&nbsp;</a>
        <div class="head-buttons pull-right" role="group" aria-label="sign-up">          
<?php if ($user->isGuest): ?>            
            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#loginModal">Войти</button>
            <a type="button" class="btn btn-info" href="<?= Url::to(['site/signup']) ?>">Регистрация</a>
<?php else: ?>
            <div style="display:inline-block;">
              <p>Вы вошли как: <b><?= $user->getCaption(); ?></b><br>
			  <?php if ($user->isCompany()): ?>
				  Учетная запись для: <b><?= $user->getCompanyName(); ?></b></p>                
			  <?php endif; ?>
            </div>
            <div  style="display:inline-block;vertical-align: top;">
			  <?= Html::a("Выйти", Url::to(['site/logout']), ['class' => 'btn btn-info', 'data-method' => "post"]) ?>
			  <?php
			  if ($user->isAdmin()) {
				echo Html::a("Админка", Url::toRoute(['admin/index']), ['class' => 'btn btn-info']);
			  }
			  ?>
            </div>
<?php endif; ?>          
        </div>
      </nav>
      <nav class="navbar-2">
        <ul class="nav-line">
          <li>
            <a class="disabled" href="<?= Url::to(['basket/index']); ?>">
              <p class="menu-title basket">Корзина<span class="badge"><?= $basket_count ? $basket_count : "" ?></span></p>
              <p class="menu-describe">Содержит сохраненные детали для формирования заказов</p>
            </a>
          </li>
          <li>
            <a href="<?= Url::to(['order/index']); ?>">
              <p class="menu-title order">Заказы<span class="badge"><?= $order_count ? $order_count : "" ?></span></p>
              <p class="menu-describe">Детали в заказе и состояние по ним</p>
            </a>
          </li>
          <li>
            <a href="<?= Url::to(['balance/index']); ?>">
              <p class="menu-title balance">Баланс<span class="badge"><?= $balance ? $balance : "" ?></span></p>
              <p class="menu-describe">История платежей и расшифровка списаний</p>
            </a>
          </li>
          <li>
            <a href="<?= Url::to(['site/setup']); ?>">
              <p class="menu-title setup">Профиль</p>
              <p class="menu-describe">Данные учетной записи и дополнительная информация</p>
            </a>
          </li>
          <li>
            <a href="<?= Url::to(['site/consumers']); ?>">
              <p class="menu-title consumer">Клиентам</p>
              <p class="menu-describe">Предложения по сотрудничеству</p>
            </a>
          </li>
          <li>
            <a href="<?= Url::to(['site/contact']); ?>">
              <p class="menu-title contact">Контакты</p>
              <p class="menu-describe">Список контактов и способов для связи</p>
            </a>
          </li>
        </ul>
      </nav>
      <nav class="navbar-atc navbar-3 navbar-small">
		<?php
		ActiveForm::begin([
				'id'	 => 'search-form',
				'method' => 'get',
				'action' => ['site/search']
		]);
		?>
		<div class="input-group">
		  <span class="input-group-btn">            
			<?=
			ButtonDropdown::widget([
					'label'		 => 'Каталоги',
					'options'	 => [
							'class' => 'btn btn-info',
					],
					'dropdown'	 => [
							'items' => $form->history,
					]
			]);
			?>
		  </span>        
		  <?=
		  Html::input("text", "search_text", $form->search_text, [
				  'class'			 => 'form-control input-medium',
				  'id'			 => 'search-string',
				  'min-size'		 => '50',
				  'size'			 => '20',
				  'placeholder'	 => "Введите номер запчасти",
				  'autocomplete'	 => 'off'
		  ]);
		  ?>
		  <div id="search-helper" class="search-helper"><?= Html::listBox("", 0, ['a' => 'b']); ?></div>
		  <span class="input-group-btn">            
			<?=
			ButtonDropdown::widget([
					'label'		 => '',
					'options'	 => [
							'class' => 'btn-info',
					],
					'dropdown'	 => [
							'options'	 => [
									'class' => 'dropdown-menu-right'
							],
							'items'		 => SearchHistoryRecord::getHtmlList()
					]
			]);
			?>
<?= Html::submitButton("Искать", ['class' => 'btn btn-info search-button', 'id' => 'search-button']); ?>
			<label for="cross" class="btn btn-info" style="padding: 4px 10px;">
			<?=
			CheckboxX::widget([
					'name'			 => 'cross',
					'options'		 => [
							'id' => 'cross',
					// 'class' => 'btn btn-info'
					],
					'value'			 => $form->cross,
					'pluginOptions'	 => ['threeState' => false]
			]);
			?>          
			  Аналоги          
			</label>

		<?=
		Html::dropDownList('over_price', $form->over_price, $form->over_price_list, [
				'id'		 => 'over-price',
				'class'		 => 'over-price selectpicker',
				'data-width' => "150px",
				//'class'     =>'over-price btn btn-info selectpicker',                
				'onchange'	 => 'main.changeOverPrice();']);
		?>
		  </span>      
		</div>
	<?php ActiveForm::end() ?>
      </nav>      

      <div class="container">
<?=
Breadcrumbs::widget([
		'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
])
?>
		
<?= $content ?>
      </div>      
    </div>

    <footer class="footer">        
      <p class="pull-left">&copy; AвтоТехСнаб <?= date('Y') ?></p>
      <p class="pull-right"><?= Yii::powered() ?></p>
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
