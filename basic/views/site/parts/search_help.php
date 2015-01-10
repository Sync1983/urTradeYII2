<?php
use yii\helpers\Html;

echo Html::listBox("search-help", 0, $items,['class'=>'search-help','ondblclick'=>'main.insertSearch(this)']);

