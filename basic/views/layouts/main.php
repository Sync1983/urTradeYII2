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
$this->registerJs('main.init('.((Yii::$app->user->isGuest)*1).');');

AppAsset::register($this);
NotifyAsset::register($this);
SelectorAsset::register($this);
BootboxAsset::overrideSystemConfirm();
SelectorAsset::registerJS();

/* @var $user SiteUser */
$user = Yii::$app->user;
/* @var $form SearchForm */
$form = $this->params['search_model'];
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
    <div class="wrap"> 
      <nav class="navbar-1">
        <a class="navbar-header" href="<?= Url::home();?>">&nbsp;</a>
        <div class="head-buttons pull-right" role="group" aria-label="sign-up">          
          <?php if($user->isGuest):?>            
          <button type="button" class="btn btn-info" data-toggle="modal" data-target="#loginModal">Войти</button>
          <button type="button" class="btn btn-info" data-toggle="modal" data-target="#signupModal">Регистрация</button>
          <?php else:?>
          <div style="display:inline-block;">
            <p>Вы вошли как: <b><?= $user->getCaption();?></b><br>
              <?php if($user->isCompany()):?>
               Учетная запись для: <b><?= $user->getCompanyName();?></b></p>                
              <?php endif;?>
          </div>
          <div  style="display:inline-block;vertical-align: top;">
            <?= Html::a("Выйти", Url::to(['site/logout']), ['class'=>'btn btn-info','data-method'=>"post"]) ?>
            <?php if($user->isAdmin()){
                    echo Html::a("Админка",Url::toRoute(['admin/index']),['class'=>'btn btn-info']);             
            }?>
          </div>
          <?php endif;?>          
        </div>
      </nav>
      <nav class="navbar-2">
        <ul class="nav-line">
          <li>
            <a class="disabled" href="<?= Url::to(['basket/index']);?>">
              <p class="menu-title basket">Корзина</p>
              <p class="menu-describe">Содержит сохраненные детали для формирования заказов</p>
            </a>
          </li>
          <li>
            <a href="<?= Url::to(['order/index']);?>">
              <p class="menu-title order">Заказы</p>
              <p class="menu-describe">Детали в заказе и состояние по ним</p>
            </a>
          </li>
          <li>
            <a href="<?= Url::to(['site/balance']);?>">
              <p class="menu-title balance">Баланс</p>
              <p class="menu-describe">История платежей и расшифровка списаний</p>
            </a>
          </li>
          <li>
            <a href="<?= Url::to(['site/setup']);?>">
              <p class="menu-title setup">Профиль</p>
              <p class="menu-describe">Данные учетной записи и дополнительная информация</p>
            </a>
          </li>
          <li>
            <a href="<?= Url::to(['site/consumers']);?>">
              <p class="menu-title consumer">Клиентам</p>
              <p class="menu-describe">Предложения по сотрудничеству</p>
            </a>
          </li>
          <li>
            <a href="<?= Url::to(['site/contact']);?>">
              <p class="menu-title contact">Контакты</p>
              <p class="menu-describe">Список контактов и способов для связи</p>
            </a>
          </li>
        </ul>
      </nav>
      <nav class="navbar-atc navbar-3 navbar-small">
      <?php  ActiveForm::begin([
              'id' => 'search-form',
              'method' => 'get',
              'action'=>['site/search']
       ]);?>
      <div class="input-group">
        <span class="input-group-btn">            
          <?= ButtonDropdown::widget([
              'label' => 'Каталоги',                    
              'options' => [
                'class' => 'btn btn-info',
              ],
              'dropdown' => [
                'items' => $form->history,
              ]
            ]);?>
        </span>        
        <?= Html::input("text", "search_text", $form->search_text, [
            'class'=>'form-control input-medium',
            'id'=>'search-string',
            'min-size'=>'50',
            'size'=>'20',
            'placeholder'=>"Введите номер запчасти",
            'autocomplete' => 'off'
          ]);?>
        <div id="search-helper" class="search-helper"><?=  Html::listBox("",0,['a'=>'b']);?></div>
        <span class="input-group-btn">            
          <?= ButtonDropdown::widget([
              'label' => '',
              'options' => [
                'class' => 'btn-info',
              ],
              'dropdown' => [
                'options' => [
                  'class' => 'dropdown-menu-right'                        
                ],
                'items' => SearchHistoryRecord::getHtmlList()
              ]
            ]);?>
          <?= Html::submitButton("Искать", ['class'=>'btn btn-info search-button','id'=>'search-button']);?>
          <label for="cross" class="btn btn-info" style="padding: 4px 10px;">
            <?= CheckboxX::widget([
                'name'=>'cross',
                'options' => [
                  'id'  => 'cross', 
                 // 'class' => 'btn btn-info'
                ],
                'value' => $form->cross,
                'pluginOptions'=>['threeState'=>false]
              ]);?>          
            Аналоги          
          </label>
          
          <?= Html::dropDownList('over_price', $form->over_price, $form->over_price_list,[
                'id'        =>'over-price',
                'class'     =>'over-price selectpicker',                
                'data-width'=>"150px",
                //'class'     =>'over-price btn btn-info selectpicker',                
                'onchange'  =>'main.changeOverPrice();']);?>
        </span>      
      </div>
      <?php ActiveForm::end()?>
      </nav>      
      
      <div class="container">
          <?= Breadcrumbs::widget([
              'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
          ]) ?>
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
      <?php ActiveForm::begin([
              'id' => 'login-form',
              'action'=>['site/login']              
       ]);?>
      <div>
        <?= Html::label("Ваш логин:", 'username',['style'=>'width:100px']); ?>
        <?= Html::input("text", "username","",['id'=>"user-name"]); ?>
      </div>
      <div>
        <?= Html::label("Пароль:", 'userpass',['style'=>'width:100px']); ?>    
        <?= Html::input("password", "userpass","",['id'=>"user-pass"]); ?>
      </div>
      <?= Html::submitButton("Войти",['class'=>'btn btn-info']); ?>
      <?php ActiveForm::end()?>      
      </div>
      <div class="modal-footer">
        <ul class="socnet-list">
          <li class="fb-icon"><?= Html::a("fb", Url::to(["soclogin/login","net"=>"fb"]));?></li>
          <li class="tw-icon hidden"><?= Html::a("tw", Url::to(["soclogin/login","net"=>"tw"]));?></li>
          <li class="od-icon hidden"><?= Html::a("od", Url::to(["soclogin/login","net"=>"od"]));?></li>
          <li class="mm-icon hidden"><?= Html::a("mm", Url::to(["soclogin/login","net"=>"mm"]));?></li>
          <li class="vk-icon">       <?= Html::a("vk", Url::to(["soclogin/login","net"=>"vk"]));?></li>          
        </ul>      
      </div>
    </div>
  </div> 
</div>
<!-- SignUp form -->
<div id="signupModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="Регисрация" aria-hidden="true">>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Регистрация</h4>
      </div>
      <div class="modal-body">
      <?php ActiveForm::begin([
              'id' => 'signup-form',
              'action'=>['site/signup']              
       ]);?>
      <div>
        <?= Html::label("Ваш логин:", 'username',['style'=>'width:100px']); ?>
        <?= Html::input("text", "username","",['id'=>"user-name"]); ?>
      </div>
      <div>
        <?= Html::label("Пароль:", 'userpass',['style'=>'width:100px']); ?>    
        <?= Html::input("password", "userpass","",['id'=>"user-pass"]); ?>
      </div>
      <?= Html::submitButton("Отправить",['class'=>'btn btn-info']); ?>
      <?php ActiveForm::end()?>      
      </div>
      <div class="modal-footer">
        <ul class="socnet-list">          
          <li class="fb-icon"><?= Html::a("fb",        Url::to(["soclogin/register","net"=>"fb"]));?></li>
          <li class="tw-icon hidden"><?= Html::a("tw", Url::to(["soclogin/register","net"=>"tw"]));?></li>
          <li class="od-icon hidden"><?= Html::a("od", Url::to(["soclogin/register","net"=>"od"]));?></li>
          <li class="mm-icon hidden"><?= Html::a("mm", Url::to(["soclogin/register","net"=>"mm"]));?></li>
          <li class="vk-icon">       <?= Html::a("vk", Url::to(["soclogin/register","net"=>"vk"]));?></li>          
        </ul>      
      </div>
    </div>
  </div> 
</div>


<div class='notifications bottom-right'></div> 
</body>
</html>
<?php /*$this->registerJs("    
    
  $('.bottom-right').notify({
  type: 'bangTidy',
    message: {html: false ,text: 'Aw yeah, It works!' }
  }).show();");*/?>
<?php $this->endPage() ?>
