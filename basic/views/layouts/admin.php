<?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\assets\AppAsset;
use app\components\LoginWidget;

/* @var $this \yii\web\View */
/* @var $content string */

$this->title = "АвтоТехСнаб - Ваш поставщик запчастей";
AppAsset::register($this);
$this->registerJsFile('/js/admin.js',['depends' => ['yii\web\YiiAsset']]);    
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
    <nav class="navbar navbar-default navbar-fixed-top nav-atc">
      <div class="navbar-header"><a href="<?= Url::home();?>">&nbsp;</a>&nbsp;</div> 
      <?= LoginWidget::widget() ?>        
    </nav>
  
  <?= $content ?>

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
    main.init(window);
  });
</script>
<?php $this->endPage() ?>
