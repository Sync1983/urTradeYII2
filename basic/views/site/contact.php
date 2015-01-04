<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

$this->title = 'Контакты';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-contact">
    <h1><?= Html::encode($this->title) ?></h1>  
    <h3>Связаться с нами</h3>
    <div class="row">

      <div class="col-lg-4">
        <h4>По телефонам:</h4>
        <p style="margin-left:10px;">
          +7 (8412) 763-533<BR>
          +7 (8412) 518-302
        </p> 
        <h4>По электронной почте:</h4>	
        <p style="margin-left:10px;">
          <a href='mailto:sales@atc58.ru' class="contact-ref">
            <img class="contact-image" src='/img/email.png' name='sales@atc58.ru'>
            sales@atc58.ru
          </a>
        </p>
        <h4>С помощью Skype:</h4>	
        <p style="margin-left:10px;">
          <a class="contact-ref" href='skype:atc_58?chat'>
            <img src='/img/skype.png' class="contact-image" name='sales@atc58.ru'>
              АвтоТехСнаб(atc_58)
          </a>		
        </p>
      </div>    
        <div class="col-lg-5 col-lg-offset-2">
          <table class="rek-table" style="top: -75px;position: relative;">  
            <tr><td colspan="2" style="text-align: center;padding: 0;"><p class = "page-header">Если Вы желаете работать с нами по <strong>безналичному расчёту</strong>, воспользуйтесь нашими реквизитами:</p></td></tr>
            <tr><td class="data">Название</td>     <td class="info_td">ООО "АвтоТехСнаб"</td></tr>
            <tr><td class="data n">ОГРН</td>       <td class="info_td n">1145837001562</td></tr>     
            <tr><td class="data">ИНН</td>          <td class="info_td">5837059930</td></tr> 
            <tr><td class="data n">КПП</td>        <td class="info_td n">583701001</td></tr> 
            <tr><td class="data">Р/С</td>          <td class="info_td">40702810715000002232</td></tr>
            <tr><td class="data n"></td>           <td class="info_td n">в Пензенском РФ ОАО Россельхозбанк</td></tr>  
            <tr><td class="data">Банк</td>         <td class="info_td">440018, г.Пенза, ул.Бекешская, 39</td></tr> 
            <tr><td class="data n">БИК</td>        <td class="info_td n">045655718</td></tr>
            <tr><td class="data">ИНН Банка</td>    <td class="info_td">7725114488</td></tr>
            <tr><td class="data n">КПП Банка</td>  <td class="info_td n">583602001</td></tr>
            <tr><td class="data">ОГРН Банка</td>   <td class="info_td">1027700342890</td></tr>    
            <tr><td class="data n">К/С</td>        <td class="info_td n">30101810600000000718</td></tr> 
            <tr><td class="data"></td>             <td class="info_td">в ГРКЦ ГУ Банка России по Пензенской обл.</td></tr>
        </table>              
      </div>
    </div>

</div>
