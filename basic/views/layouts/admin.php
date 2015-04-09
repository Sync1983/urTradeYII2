<?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\assets\AdminAsset;
use app\assets\NotifyAsset;
use app\assets\SelectorAsset;
use app\assets\BootboxAsset;


/* @var $this \yii\web\View */
/* @var $content string */

$this->title = "АвтоТехСнаб - Ваш поставщик запчастей";
AdminAsset::register($this);
NotifyAsset::register($this);
SelectorAsset::register($this);
BootboxAsset::overrideSystemConfirm();
SelectorAsset::registerJS();
/* @var $user SiteUser */
$user = Yii::$app->user;
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
    </nav>
    <div class="container">
      <div class="left-menu"> 
        <ul> 
          <li><?= Html::a("Общая информация",   Url::to(['admin/index']),['class'=>'menu-item']);?></li>
          <li><?= Html::a("Пользователи",       Url::to(['admin/users']),['class'=>'menu-item']);?></li>
          <li><?= Html::a("Корзины",            Url::to(['admin/user-basket']),['class'=>'menu-item']);?></li>
          <li><?= Html::a("Заказы",             Url::to(['admin/user-order']),['class'=>'menu-item']);?></li>
          <li><?= Html::a("Прайс-листы",        Url::to(['admin/prices']),['class'=>'menu-item']);?></li>          
        </ul>
      </div>
      <div class="content">
        <?= yii\widgets\Breadcrumbs::widget([
              'links' => $this->params['breadcrumbs'],
              'homeLink' => [
                'label' => "Главная",
                'url' => Url::base()],
            ]); ?>
        <?= $content ?>        
      </div>
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
<?php $this->endPage() ?>
