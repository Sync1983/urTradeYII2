<?php
use yii\helpers\Html;
use app\assets\AppAsset;
use yii\helpers\Url;

/* @var $this \yii\web\View */
/* @var $content string */
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
      <nav class="navbar-1">
        <a class="navbar-header" href="<?= Url::home();?>">&nbsp;</a>        
      </nav>
      <div class="container" style="margin-top: 60px;">
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
<?php $this->endPage() ?>
