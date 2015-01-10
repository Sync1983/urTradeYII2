<?php
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\models\SiteModel;
use app\components\MainMenuWidget;

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
      <?= MainMenuWidget::widget([
            'brand' => [
              'url'   => ['site/index'],
              'class' => 'head-logo',
              'img'   => '/img/logo_left.png'
            ],
            'items'=>[
              'Корзина'=>[
                'url'   => ['site/basket'],
                'class' => 'basket',                
              ],
              'Заказы'=>[
                'url'   => ['site/orders'],
                'class' => 'order',            
              ],
              'Баланс'=>[
                'url'   => ['site/balance'],
                'class' => 'balance',            
              ],
              'Профиль'=>[
                'url'   => ['site/setup'],
                'class' => 'setup',            
              ],
              'Клиентам'=>[
                'url'   => ['site/consumers'],
                'class' => 'consumer',            
              ],
              'Контакты'=>[
                'url'   => ['site/contact'],
                'class' => 'contact',            
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
