<?php
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use yii\helpers\Url;
use app\assets\AppAsset;
use app\components\MainMenuWidget;
use app\components\LoginWidget;
use app\components\SearchWidget;

/* @var $this \yii\web\View */
/* @var $content string */
$this->title = "АвтоТехСнаб - Ваш поставщик запчастей";
$this->registerJs('main.init();');
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
      <nav class="navbar navbar-default navbar-fixed-top nav-atc">
        <div class="navbar-header"><a href="<?= Url::home();?>">&nbsp;</a>&nbsp;</div> 
        <?= LoginWidget::widget() ?>        
      </nav>
      <?php $menu_items = [];
        $menu_items['Корзина'] = [
                'url'   => ['site/basket'],
                'class' => 'basket',
                'describe' => 'Содержит сохраненные детали для формирования заказов'
              ];
        $menu_items['Заказы'] = [
                'url'   => ['site/orders'],
                'class' => 'order',            
                'describe' => 'Список заказанных деталей и состояние работы по ним'
              ];
        $menu_items['Баланс'] = [
                'url'   => ['site/balance'],
                'class' => 'balance',            
                'describe' => 'Содержит историю платежей и расшифровка списаний со счета'
              ];
        $menu_items['Профиль'] = [
                'url'   => ['site/setup'],
                'class' => 'setup',            
                'describe' => 'Данные учетной записи и дополнительная информация'
              ];
        $menu_items['Клиентам'] = [
                'url'   => ['site/consumers'],
                'class' => 'consumer',            
                'describe' => 'Предложения по сотруничеству'
              ];
        $menu_items['Контакты'] = [              
                'url'   => ['site/contact'],
                'class' => 'contact',            
                'describe' => 'Список контактов и способов для связи'
              ];
      ?>
      <?= MainMenuWidget::widget(['items' => $menu_items]); ?>    
      <?= SearchWidget::widget(); ?>
      
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
<div class="preloader">&nbsp;</div>
</body>
</html>
<?php $this->endPage() ?>
