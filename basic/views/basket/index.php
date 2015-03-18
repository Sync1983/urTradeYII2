<?php

use yii\helpers\Html;
use app\models\PartRecord;
use kartik\grid\GridView;
use kartik\grid\CheckboxColumn;
use kartik\grid\DataColumn;
use kartik\grid\EditableColumn;
use kartik\editable\Editable;
use kartik\grid\ActionColumn;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model PartRecord */
$this->title = 'Корзина';
$this->params['breadcrumbs'][] = $this->title;

 echo $this->render("grid", ['user_basket'=>$user_basket,'guest_basket'=>$guest_basket]);

?>

