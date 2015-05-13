<?php
/* @var $this yii\web\View */
?>

<table class="mail-table" style="width: 100%;border: 1px solid black;position: relative;border-collapse: collapse">
  <thead>
	<tr>	  
	  <th>Номер заказа</th>
	  <th>Покупатель</th>
	  <th>Сток</th>
	  <th>Деталь</th>	  
	  <th>Цена</th>
	  <th>Дост.</th>
	  <th>Ориг</th>
	  <th>Кол-во</th>
	  <th>Ком.</th>
	</tr>
  </thead>
  <tbody>
  <?php	foreach ($items as $item):?>
	<tr style="text-align: center;">
	  <td style="border: 1px solid black;"><?= $item['id']?></td>
	  <?php $user = \yii::$app->user->identity; ?>
	  <td style="border: 1px solid black;"><?=$user->first_name." ".$user->second_name."<br>".$user->name ?></td>
	  <td style="border: 1px solid black;"><?= $item['prov']."<br>".$item['stock']?></td>
	  <td style="border: 1px solid black;"><?= $item['art']."<br>".$item['prov']."<br>".$item['name']?></td>
	  <td style="border: 1px solid black;"><?= $item['price']?></td>
	  <td style="border: 1px solid black;"><?= $item['time']?></td>
	  <td style="border: 1px solid black;"><?= $item['orig']?"Да":"Нет"?></td>
	  <td style="border: 1px solid black;"><?= $item['cnt']."<br>(".$item['lot']."шт.)"?></td>
	  <td style="border: 1px solid black;"><?= $item['comm']?></td>
	</tr>
  <?php endforeach;?>
  </tbody>
</table>