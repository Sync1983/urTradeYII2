<?xml version="1.0" encoding="UTF-8"?>
<?php
/* @var $model app\models\pays\YaPayOrderModel */
?>
<checkOrderResponse
  performedDatetime = "<?= date(DateTime::RFC3339);?>"
  code = "<?= $code;?>"
  shopId = "<?= $model->shopId;?>"
  invoiceId	= "<?= $model->invoiceId;?>"
  orderSumAmount = "<?= $model->orderSumAmount;?>"
<?php if ( $error ):?>
  message	= "<?= $error ?>"
<?php endif;?>
 />
