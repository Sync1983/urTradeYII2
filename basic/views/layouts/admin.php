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
  <div id="wrapper">    
	<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
	  <div class="navbar-header">
		<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
		  <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
		<a class="navbar-brand" href="<?=Url::home()?>">АвтоТехСнаб</a>
	  </div>
	  <div class="collapse navbar-collapse navbar-ex1-collapse">
		<ul class="nav navbar-nav side-nav">
      <li class="dropdown">
        <a href="javascript:;" data-toggle="collapse" data-target="#summary"><i class="fa fa-angle-double-right"></i> Общая информация</a>
        <ul id="summary" class="collapse">
          <li><?= Html::a("База данных",   Url::to(['admin/database-info']));?></li>
          <li><?= Html::a("Пользователи",   Url::to(['admin/user-info']));?></li>
          <li><a href="#">Dropdown Item</a></li>
          <li><a href="#">Dropdown Item</a></li>
        </ul>                    
      </li>          
      
		  <li><?= Html::a("Пользователи",       Url::to(['admin/users']));?></li>
		  <li><?= Html::a("Корзины",            Url::to(['admin/user-basket']),['class'=>'menu-item']);?></li>
      <li><?= Html::a("Заказы",             Url::to(['admin/user-order']),['class'=>'menu-item']);?></li>
      <li><?= Html::a("Прайс-листы",        Url::to(['admin/prices']),['class'=>'menu-item']);?></li>

      <li class="dropdown">
        <a href="javascript:;" data-toggle="collapse" data-target="#catalogs"><i class="fa fa-angle-double-right"></i> Каталоги</a>
        <ul id="catalogs" class="collapse">
          <li><?= Html::a("Создание каталога",     Url::to(['admin/catalog-create']));?></li>
          <li><?= Html::a("Управление каталогами", Url::to(['admin/catalog-control']));?></li>
          <li><?= Html::a("Наполнение каталогов",  Url::to(['admin/catalog-fill']));?></li>
        </ul>
      </li>
      <li class="dropdown">
        <a href="javascript:;" data-toggle="collapse" data-target="#cars"><i class="fa fa-angle-double-right"></i> Автомобили</a>
        <ul id="cars" class="collapse">
          <li><?= Html::a("Управление каталогом", Url::to(['admin/cars-control']));?></li>
        </ul>
      </li>
		  
    </ul>
      </div>
	</nav>
	
    <div id="page-wrapper">

	  <div class="container-fluid">

		<!-- Page Heading -->
        <div class="row">
		  <div class="col-lg-12">			
			<?= yii\widgets\Breadcrumbs::widget([
              'links' => $this->params['breadcrumbs'],
              'homeLink' => [
                'label' => "Главная",
                'url' => Url::base()],
            ]); ?>
          </div>
        </div>
		<?php $n_list = \yii\helpers\ArrayHelper::getValue($this->params, 'notify', []);		
			  if( !empty($n_list) ):?>
		<div class="row">
		<?php foreach ($n_list as $message):?>
		  <div class="col-lg-12">
			<div class="alert alert-info alert-dismissable">
			  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
              <i class="fa fa-info-circle"></i><?= $message; ?>
            </div>
          </div>		  
		<?php endforeach; ?>
        </div>
		<?php endif;?>
		
        <?= $content ?>        
		
	  </div>
	  
	</div>    
  </div>

<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage() ?>
