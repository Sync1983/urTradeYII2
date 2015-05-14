<?php
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $key string */
?>

<h3>Подтверждение регистрации на сайте АвтоТехСнаб</h3>

<p>Вы или кто-то иной указал этот адрес почты при регистрации на сайте <a href="www.atc58.ru">АвтоТехСнаб</a>. </p>
<p>Для окончания регистрации Вам необходимо пройти по указанной ниже ссылке: </p>

<p><a href="<?=Url::to(['site/signup-mail-answer','key'=>$key])?>"><?=Url::to(['site/signup-mail-answer','key'=>$key])?></a></p>  
<p>Если Ваш почтовый агент автоматически не переходит по указанной ссылке, её необходимо скопировать в буфер обмена и вставить в адресную строку браузера, после чего перейти по этому адресу.</p>  

<p><b>Внимание!</b> Если Вы не проходили процедуру регистарции, просто игнорируйте это письмо.</p>

<p>С Уважением,<br>
Администрация сайта АвтоТехСнаб</p>