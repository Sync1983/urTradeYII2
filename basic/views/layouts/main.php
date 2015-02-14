<?php
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\models\SiteModel;
use app\components\MainMenuWidget;
use yii\helpers\Url;
use app\components\LoginWidget;

/* @var $this \yii\web\View */
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
      <nav class="navbar navbar-default navbar-fixed-top nav-atc">
        <div class="navbar-header"><a style="top: 4px;left:4px;position: relative;" href="<?= Url::home();?>"><img alt="Brand" src="/img/logo_left.png"></a></div> 
        <?= LoginWidget::widget() ?>        
      </nav>
      <?= MainMenuWidget::widget([            
            'items'=>[
              'Корзина'=>[
                'url'   => ['site/basket'],
                'class' => 'basket',
                'describe' => 'Содержит сохраненные детали для формирования заказов'
              ],
              'Заказы'=>[
                'url'   => ['site/orders'],
                'class' => 'order',            
                'describe' => 'Список заказанных деталей и состояние работы по ним'
              ],
              'Баланс'=>[
                'url'   => ['site/balance'],
                'class' => 'balance',            
                'describe' => 'Содержит историю платежей и расшифровка списаний со счета'
              ],
              'Профиль'=>[
                'url'   => ['site/setup'],
                'class' => 'setup',            
                'describe' => 'Данные учетной записи и дополнительная информация'
              ],
              'Клиентам'=>[
                'url'   => ['site/consumers'],
                'class' => 'consumer',            
                'describe' => 'Предложения по сотруничеству'
              ],
              'Контакты'=>[
                'url'   => ['site/contact'],
                'class' => 'contact',            
                'describe' => 'Список контактов и способов для связи'
              ],                
            ]
          ]); ?>    

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
<script type="text/javascript">
  $(document).ready(function(){
    main.initMainMenu();
  });
</script>
<?php $this->endPage() ?>
