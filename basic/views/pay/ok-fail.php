<?php
/* @var $this yii\web\View */
/* @var $fail boolean */

if( !$fail ):?>
<h2 class="bg-success" style="padding: 5px 5px;">Ваш платеж успешно проведен</h2>
<?php else:  ?>
<h2 class="bg-danger" style="padding: 5px 5px;">При совершении платежа произошла ошибка!</h2>
<?php endif;?>