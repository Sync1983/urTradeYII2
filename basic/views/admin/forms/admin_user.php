<?php 
use app\models\admin\forms\AdminUserForm;
use yii\bootstrap\ActiveForm;
/* @var $model AdminUserForm */
/* @var $form ActiveForm */
$model = $form;
$form = ActiveForm::begin([
  'id' => 'admin-user-form',
  'layout' => 'horizontal',
  'validateOnChange' => true,  
  'validateOnType' => true,
  'enableAjaxValidation' => true,
  'enableClientValidation' =>true,
  'validationUrl' => ['admin/user-ajax-validate'],
  'action' => ['admin/user-ajax-change'],
  'fieldConfig' => [
        'horizontalCssClasses' => [
            'label' => 'col-sm-4',
            'offset' => 'col-sm-offset-1',
            'wrapper' => 'col-sm-4',            
        ],
    ]
]);
 echo $form->errorSummary($model);
 
 echo $form->field($model, "_id")->textInput(['readonly' => true])->label()->error();
 echo $form->field($model, 'user_name')->label()->error();
 echo $form->field($model, 'user_pass')->label()->error();
 echo \yii\helpers\Html::button("Сгененрировать",[
   'onClick' => 'generatePassword(this)'
 ]);
 echo \yii\helpers\Html::input("text","passwordVision");
 
 echo $form->field($model, 'first_name')->label()->error();
 echo $form->field($model, 'second_name')->label()->error();
 echo $form->field($model, 'photo')->label()->error();
 
 echo $form->field($model, 'name')->label()->error();
 
 echo $form->field($model, 'addres')->label()->error();
 echo $form->field($model, 'phone')->label()->error();
 echo $form->field($model, 'email')->input("email")->label()->error();
 
 echo $form->field($model, 'over_price')->input("number")->label()->error();
 
 echo $form->field($model, 'role')->dropDownList([
          'user' => 'Пользователь',
          'admin' => 'Администратор',
          'manager' => 'Менеджер',
        ])->label()->error();
 
 echo $form->field($model, 'type')->dropDownList([
          'private' => 'Частное лицо',
          'company' => 'Юридическое лицо',        
        ])->label()->error();
 
 echo $form->field($model, 'inn')->label()->error();
 echo $form->field($model, 'kpp')->label()->error();
 
 echo $form->field($model, 'credit')->input("number")->label()->error();
 
 echo \yii\helpers\Html::a('Открыть корзину',  yii\helpers\Url::to(['admin/user-basket',"id"=>  strval($model->_id)]),['class' => 'btn btn-info']);
 echo \yii\helpers\Html::submitButton('Сохранить изменения',['class' => 'btn btn-danger','data-confirm'=>"Изменить данные пользователя?"]); 

ActiveForm::end();

?>


<script>
  function passGen() {
    var chars = "1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
    var length = 10;
    var res="";
    var r;
    var i;
      for (i=1;i<=length;i++)
        {
         r=Math.floor(Math.random()*chars.length);
         res=res+chars.substring(r,r+1);
        }   
    res = res.replace("&","&amp;");
    res = res.replace(">","&gt;");
    res = res.replace("<","&lt;");
    return res;
  }
  
  function generatePassword(){
    var text = passGen();
    $.ajax({
      url: '<?= yii\helpers\Url::to(['admin/get-md5'])?>',
      data: {key:text}
    }).done(function (data){
      $("input[name='passwordVision']").val(text);
      $("input[name='AdminUserForm[user_pass]']").val(data);      
    });
    return false;
  }
</script>


