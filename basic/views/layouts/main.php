<?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\LoginWidget;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\models\SiteModel;

/* @var $this \yii\web\View */
/* @var $model \app\models\SiteModel */
/* @var $content string */
$model = SiteModel::_instance();
$this->title = "АвтоТехСнаб - Ваш поставщик запчастей";
AppAsset::register($this);
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
      <div class ="logo-line">
        <?= LoginWidget::widget() ?>
      </div>
      <div class="main-menu">
        <div class="head-logo"><a href="<?= Url::to(['site/index'])?>" onclick="main.menuClick(this);">&nbsp;</a></div>
        <div class="menu-spliter">&nbsp;</div>
        
        <div class="menu-item">
          <div class="basket menu-icon"><span class="badge"></span></div>          
          <a href="<?= Url::to(['site/basket'])?>">Корзина</a>
        </div>
        <div class="menu-spliter">&nbsp;</div>
        
        <div class="menu-item">
          <div class="order menu-icon"><span class="badge"></span></div>
          <a href="<?= Url::to(['site/orders'])?>">Заказы</a>
        </div>
        <div class="menu-spliter">&nbsp;</div>
        
        <div class="menu-item">
          <div class="balance menu-icon"><span class="badge"></span></div>
          <a  href="<?= Url::to(['site/balance'])?>">Баланс</a>
        </div>
        <div class="menu-spliter">&nbsp;</div>
        
        <div class="menu-item">
          <div class="setup menu-icon">&nbsp;</div>
          <a href="<?= Url::to(['site/setup'])?>">Профиль</a>
        </div>
        <div class="menu-spliter">&nbsp;</div>
        
        <div class="menu-item">
          <div class="consumer menu-icon">&nbsp;</div>
          <a  href="<?= Url::to(['site/consumers'])?>">Клиентам</a>
        </div>
        <div class="menu-spliter">&nbsp;</div>
        
        <div class="menu-item">
          <div class="contact menu-icon">&nbsp;</div>
          <a href="<?= Url::to(['site/contact'])?>">Контакты</a>
        </div>        
        
        <div class="menu-search">
          <div class="search-head search">&nbsp;</div>
          <div class="search-line">
            <input class="search-string" type="text" id="search-string" value="<?= $model->search ?>"/>
            <div class="search-dropdown"></div>
          </div>  
          <div class="search-line" style="width: 45%;">
            <div class="btn btn-primary search-button">Искать</div>
            <input type="checkbox" class = "big-check" id="cross" <?= $model->cross?'checked':''?>/>
            <label for="cross">Аналоги</label>
            <select class="over-price" id="over-price" value=3>
              <?= $model->generateOverPrice() ?>
            </select>
            <div class="btn btn-primary search-button" style="float:right;">Каталоги</div>
          </div>
        </div>
      </div>

        <div class="container">
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
            <?= $content ?>
        </div>      
    </div>

    <footer class="footer">
        <div class="container">
            <p class="pull-left">&copy; AвтоТехСнаб <?= date('Y') ?></p>
            <p class="pull-right"><?= Yii::powered() ?></p>
        </div>
    </footer>

<?php $this->endBody() ?>
</body>
</html>
<script type="text/javascript">
  $(document).ready(function(){
    main.initMainMenu();
  });
</script>
<?php $this->endPage() ?>
